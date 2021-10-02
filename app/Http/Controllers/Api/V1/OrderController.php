<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ContentWasNotFountException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Traits\Responses\ResponseMaker;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ResponseMaker;

    /**
     * @param StoreOrderRequest $request
     * @param OrderRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\OrderCreationError
     */
    public function store(StoreOrderRequest $request, OrderRepository $repository)
    {
        $order = $repository->create($request);
        return $this->success($order);
    }

    /**
     * @param Request $request
     * @param Order $order
     * @param OrderRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\ContentWasNotFountException
     * @throws \App\Exceptions\OrderIsNotCancelableException
     */
    public function cancel(Request $request, Order $order, OrderRepository $repository)
    {
        $user = $request->user();
        return $this->success($repository->cancelOrder($user, $order));
    }

    /**
     * @param Request $request
     * @param Order $order
     * @param OrderRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     * @throws \App\Exceptions\OrderIsNotAcceptableException
     */
    public function accept(Request $request, Order $order, OrderRepository $repository)
    {
        $user = $request->user();
        return $this->success($repository->acceptOrder($user, $order));
    }

    /**
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     */
    public function get(Request $request, Order $order)
    {
        $user = $request->user();
        if ($user->id = $order->user_id) {
            return $this->success($order->load('images', 'driver'));
        }
        throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
    }

    /**
     * @param Request $request
     * @param string $state
     * @param OrderRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     */
    public function getOrdersByLogicalState(Request $request, string $state, OrderRepository $repository)
    {
        $user = $request->user();
        return $this->success($repository->getOrdersByLogicalStatus($user, $state));
    }
}
