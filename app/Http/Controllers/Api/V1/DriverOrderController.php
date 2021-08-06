<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Exceptions\ContentWasNotFountException;
use App\Exceptions\OperationNotPossibleException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\DriverAcceptOrder;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Traits\Responses\ResponseMaker;
use Illuminate\Http\Request;

class DriverOrderController extends Controller
{
    use ResponseMaker;

    /**
     * @param Request $request
     * @param OrderRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\ContentWasNotFountException
     */
    public function suggestions(Request $request, OrderRepository $repository)
    {
        $user = $request->user();
        return $this->success($repository->getOrderSuggestionsForDriver($user));
    }

    /**
     * @param OrderRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function acceptOrder(OrderRequest $request)
    {
        $user = $request->user();
        $inputs = $request->all();
        $order = Order::find($inputs['order_id']);
        if ($order->driver_id) {
            if ($order->driver_id == $user->id) {
                return $this->success(['message' => __('messages.success.order_accepted_by_you')]);
            } else {
                return $this->success(['message' => __('messages.success.order_was_taken')]);
            }
        }
        $inputs['driver_id'] = $user->id;
        DriverAcceptOrder::query()
            ->firstOrCreate($inputs);
        return $this->success(['message' => __('messages.success.driver_order_registered')]);
    }

    /**
     * @param OrderRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     * @throws OperationNotPossibleException
     */
    public function announceMoving(OrderRequest $request)
    {
        return $this->moveOrderStatusForward($request, OrderStatus::DRIVER_HEADING_TO_LOCATION);
    }

    /**
     * @param OrderRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     * @throws OperationNotPossibleException
     */
    public function announcePickingUp(OrderRequest $request)
    {
        return $this->moveOrderStatusForward($request, OrderStatus::PICKED_UP);
    }

    /**
     * @param OrderRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     * @throws OperationNotPossibleException
     */
    public function announceDelivered(OrderRequest $request)
    {
        return $this->moveOrderStatusForward($request, OrderStatus::DELIVERED);
    }

    /**
     * @param OrderRequest $request
     * @param string $status
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     * @throws OperationNotPossibleException
     */
    private function moveOrderStatusForward(OrderRequest $request, string $status)
    {
        $user = $request->user();
        $inputs = $request->all();
        $order = Order::find($inputs['order_id']);
        if ($order->driver_id == $user->id) {
            if ($order->stateTransitionIsPermitted($status)) {
                $order = $order->updateOrderStatus($status);
                return $this->success($order);
            }
            throw new OperationNotPossibleException(__('messages.error.not_possible_operation'));
        }
        throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
    }
}
