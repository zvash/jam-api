<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Item;
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
}
