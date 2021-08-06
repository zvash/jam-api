<?php

namespace App\Repositories;


use App\Enums\DriverOrderStatus;
use App\Enums\OrderStatus;
use App\Events\NewOrderSuggestionsForDriversCreated;
use App\Events\OrderWasCreated;
use App\Exceptions\ContentWasNotFountException;
use App\Exceptions\OrderCreationError;
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
use App\Models\User;
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
            throw new OrderCreationError(__('messages.error.order_creation_error'));
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
            ->with(['location', 'images'])
            ->get();
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