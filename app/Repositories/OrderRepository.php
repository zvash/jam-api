<?php

namespace App\Repositories;


use App\Enums\DriverOrderStatus;
use App\Enums\OrderStatus;
use App\Events\NewOrderSuggestionsForDriversCreated;
use App\Events\OrderWasCreated;
use App\Exceptions\ContentWasNotFountException;
use App\Exceptions\OrderCreationError;
use App\Exceptions\OrderIsNotAcceptableException;
use App\Exceptions\OrderIsNotCancelableException;
use App\Exceptions\UserReachedMaxLocationsException;
use App\Http\Requests\StoreOrderRequest;
use App\Models\County;
use App\Models\DriverCounty;
use App\Models\DriverOrder;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderStatusLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository
{

    /**
     * @param StoreOrderRequest $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     * @throws OrderCreationError
     */
    public function create(StoreOrderRequest $request)
    {
        $user = $request->user();
        $inputs = $request->validated();
        DB::beginTransaction();
        try {
            $inputs['user_id'] = $user->id;
            $inputs['status'] = OrderStatus::PENDING;

            $creationAttributes = $this->getOrderCreationFields();
            $creationInputs = array_filter($inputs, function ($key) use ($creationAttributes) {
                return in_array($key, $creationAttributes);
            }, ARRAY_FILTER_USE_KEY);

            $order = Order::query()->create($creationInputs);

            if (! $user->isSeller()) {
                $sellerRole = Role::getByName('seller');
                $user->roles()->attach($sellerRole->id);
            }

            if ($request->hasFile('images')) {
                $files = $request->file('images');
                foreach ($files as $file) {
                    $path = $this->saveFile($file, 'orders');
                    OrderImage::query()
                        ->create([
                            'order_id' => $order->id,
                            'image' => $path
                        ]);
                }
            }

            if (array_key_exists('items', $inputs)) {
                $inputItems = $inputs['items'];
                $toAttach = [];
                foreach ($inputItems as $inputItem) {
                    $toAttach[$inputItem['id']] = [
                        'weight' => $inputItem['weight']
                    ];
                }
                if ($toAttach) {
                    $order->items()->attach($toAttach);
                }
            }

            DB::commit();
            event(new OrderWasCreated($order));
            return Order::query()
                ->with('images')
                ->where('id', $order->id)
                ->first();
        } catch (\Exception $exception) {
            DB::rollBack();
            //throw new OrderCreationError(__('messages.error.order_creation_error'));
            throw new OrderCreationError($exception->getMessage());
        }
    }

    /**
     * @param User $user
     * @param Order $order
     * @return Order
     * @throws ContentWasNotFountException
     * @throws OrderIsNotCancelableException
     */
    public function cancelOrder(User $user, Order $order)
    {
        if ($order->user_id != $user->id) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }
        if ($order->stateTransitionIsPermitted(OrderStatus::CANCELED_BY_CUSTOMER)) {
            return $order->updateOrderStatus(OrderStatus::CANCELED_BY_CUSTOMER);
        }
        throw new OrderIsNotCancelableException(__('messages.error.order_not_cancelable'));
    }

    /**
     * @param User $user
     * @param Order $order
     * @return Order
     * @throws ContentWasNotFountException
     * @throws OrderIsNotAcceptableException
     */
    public function acceptOrder(User $user, Order $order)
    {
        if ($order->user_id != $user->id) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }
        $nextState = OrderStatus::ACCEPTED_DRIVER_NOT_NEEDED;
        if ($order->requires_driver) {
            $nextState = OrderStatus::ACCEPTED_WAITING_FOR_DRIVER;
        }
        if ($order->stateTransitionIsPermitted($nextState)) {
            return $order->updateOrderStatus($nextState);
        }
        throw new OrderIsNotAcceptableException(__('messages.error.order_not_acceptable'));
    }

    /**
     * @param Order $order
     * @return array|null
     */
    public function suggestOrderToDrivers(Order $order)
    {
        if ($order->driver) {
            return null;
        }

        $county = $order->location->county;

        $allDriverIds = DriverCounty::query()
            ->where('county_id', $county->id)
            ->pluck('driver_id')
            ->toArray();

        $driverOrderCounts = Order::query()
            ->selectRaw('driver_id, count(*) as order_count')
            ->whereIn('driver_id', $allDriverIds)
            ->whereIn('status', [
                OrderStatus::ACCEPTED_BY_DRIVER,
                OrderStatus::PICKED_UP,
            ])
            //->whereRaw('order_count >= 2')
            ->groupBy('driver_id')
            ->pluck('order_count', 'driver_id')
            ->toArray();

        $availableDriverIds = [];
        foreach ($allDriverIds as $driverId) {
            if (! isset($driverOrderCounts[$driverId]) || $driverOrderCounts[$driverId] < 2) {
                $availableDriverIds[] = $driverId;
            }
        }
        $previousOrderToken = $order->driver_token;

        try {
            DB::beginTransaction();
            $order = $order->setDriverToken();
            $orderToken = $order->driver_token;
            if ($previousOrderToken) {
                DriverOrder::query()
                    ->where('order_token', $previousOrderToken)
                    ->where('order_id', $order->id)
                    ->update(['status' => DriverOrderStatus::EXPIRED]);
            }
            $driverOrders = [];
            foreach ($availableDriverIds as $driverId) {
                $driverOrders[] = DriverOrder::query()
                    ->create([
                        'order_id' => $order->id,
                        'driver_id' => $driverId,
                        'status' => DriverOrderStatus::SUGGESTED,
                        'order_token' => $orderToken,
                    ]);
            }
            event(new NewOrderSuggestionsForDriversCreated($order));

            DB::commit();
            return $driverOrders;
        } catch (\Exception $exception) {
            DB::rollBack();
            return null;
        }
    }

    /**
     * @param User $user
     */
    public function getActiveOrdersCountForDriver(User $user)
    {
        Order::query()
            ->where('driver_id', $user->id)
            ->whereIn('status', [
                OrderStatus::ACCEPTED_BY_DRIVER,
                OrderStatus::PICKED_UP,
            ])
            ->count();
    }

    /**
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws ContentWasNotFountException
     */
    public function getOrderSuggestionsForDriver(User $user)
    {
        if ($this->getActiveOrdersCountForDriver($user) >= 2) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }
        $suggestedOrderIds = DriverOrder::query()
            ->where('driver_id', $user->id)
            ->where('status', DriverOrderStatus::SUGGESTED)
            ->pluck('order_id')
            ->all();

        if (!$suggestedOrderIds) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }

        return Order::query()
            ->whereIn('id', $suggestedOrderIds)
            ->where('status', OrderStatus::ACCEPTED_WAITING_FOR_DRIVER)
            ->whereNull('driver_id')
            ->orderBy('created_at', 'desc')
            ->with(['location', 'images'])
            ->get();
    }

    /**
     * @param Request $request
     * @param Order $order
     * @return Order
     */
    public function saveWaybillImage(Request $request, Order $order)
    {
        $path = $this->saveFileFromRequest($request, 'image', 'orders');
        $order->waybill_image = $path;
        $order->save();
        return $order;
    }

    /**
     * @param Request $request
     * @param Order $order
     * @return Order
     */
    public function saveEvacuationPermitImage(Request $request, Order $order)
    {
        $path = $this->saveFileFromRequest($request, 'image', 'orders');
        $order->evacuation_permit_image = $path;
        $order->save();
        return $order;
    }

    /**
     * @param User $user
     * @param string $state
     * @param int $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws ContentWasNotFountException
     */
    public function getOrdersByLogicalStatus(User $user, string $state, int $paginate = 10)
    {
        $logicalStates = [
            'waiting' => [
                OrderStatus::PENDING,
            ],
            'active' => [
                OrderStatus::ACCEPTED_WAITING_CUSTOMER_APPROVAL,
                OrderStatus::ACCEPTED_WAITING_FOR_DRIVER,
                OrderStatus::ACCEPTED_BY_DRIVER,
                OrderStatus::ACCEPTED_DRIVER_NOT_NEEDED,
                OrderStatus::DRIVER_HEADING_TO_LOCATION,
                OrderStatus::PICKED_UP,
            ],
            'finished' => [
                OrderStatus::DELIVERED,
                OrderStatus::FINISHED,
            ],
            'canceled' => [
                OrderStatus::REJECTED,
                OrderStatus::CANCELED_BY_CUSTOMER,
            ],
        ];

        if (! in_array($state, array_keys($logicalStates))) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }

        $query = Order::query()->where('user_id', $user->id);
        if (count($logicalStates[$state]) == 1) {
            $query = $query->where('status', $logicalStates[$state][0]);
        } else {
            $query = $query->whereIn('status', $logicalStates[$state]);
        }
        $query = $query->orderBy('created_at', 'desc');
        if ($paginate) {
            return $query->paginate($paginate);
        }
        return $query->get();
    }

    /**
     * @return array
     */
    private function getOrderCreationFields()
    {
        return [
            'user_id',
            'description',
            'status',
            'location_id',
            'requires_driver',
            'final_price_needed',
            'approximate_weight',
            'pickup_date',
        ];
    }
}