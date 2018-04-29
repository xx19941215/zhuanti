<?php

namespace App\Http\Controllers\Api;

use App\Goods;
use App\Repositories\GoodsRepositories;
use App\Transformers\GoodsTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class GoodsController extends ApiController
{
    protected $goods;

    public function __construct(GoodsRepositories $goodsRepositories)
    {
        parent::__construct();
        $this->goods = $goodsRepositories;
    }

    public function search(Request $request)
    {
        return $this->goods->getListByCoreseek($request->all());
    }

    public function hot(Request $request)
    {
        $limit = $request->query->all()['limit'] ?? 5;
        return $this->response->collection(Goods::hotGoods($limit), new GoodsTransformer());
    }

    public function new(Request $request)
    {
        $limit = $request->query->all()['limit'] ?? 5;
        return $this->response->collection(Goods::newGoods($limit), new GoodsTransformer());
    }

    public function all(Request $request)
    {
        return $this->response->collection($this->goods->page(), new GoodsTransformer());
    }

    public function baokuanSearch(Request $request)
    {
        $this->validate($request, [
            'so' => 'required'
        ]);

        $goods = Goods::where('is_competition', '=', 1)->where('title', 'like', '%' . Input::get('so') . '%')->paginate();

        return $this->response->collection($goods, new GoodsTransformer());
    }

}