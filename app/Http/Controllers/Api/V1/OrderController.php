<?php

namespace App\Http\Controllers\Api\V1;

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
}
