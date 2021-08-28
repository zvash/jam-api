<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemPriceHistory;
use App\Traits\Responses\ResponseMaker;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    use ResponseMaker;

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getItems(Request $request)
    {
        return $this->success(Item::query()->get(['id', 'name', 'image']));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function prices(Request $request)
    {
        $items = Item::query()->get();
        $prices = [];
        foreach ($items as $item) {
            $prices[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'user_price' => $item['user_price'],
                'price_fluctuation_amount' => $item['price_fluctuation_amount'],
                'price_fluctuation_percent' => $item['price_fluctuation_percent'],
            ];
        }
        return $this->success($prices);
    }
}
