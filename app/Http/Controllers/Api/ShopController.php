<?php

namespace App\Http\Controllers\Api;

use App\Goods;
use App\Repositories\GoodsRepositories;
use App\Repositories\ShopRepositories;
use App\Shop;
use Illuminate\Http\Request;

class ShopController extends ApiController
{
    protected $shop;
    protected $goods;

    public function __construct(ShopRepositories $shopRepositories, GoodsRepositories $goodsRepositories)
    {
        $this->shop = $shopRepositories;
        $this->goods = $goodsRepositories;

        parent::__construct();
    }

    public function listGoods(Request $request)
    {
        $params = [
            "so" => $request->all()['so'] ?? '' ,
            "page" => $request->all()['page'] ?? 1,
            "zdid" => $request->get('zdid', '42'),
            "sortType" => "shop-detail",
            "sid" => $request->shop_id,
            "cateid" => null,
            "child_ids" => 0,
            "ord_rule" => "item_created desc",
            "pageSize" => $request->get('pageSize', '20'),
            "offset" => 0
        ];

        return $this->goods->getListByCoreseek($params);
    }

    public function participate(Request $request)
    {
        $this->validate($request, [
            'goods' => 'required',
            'slogan'=> 'required'
        ]);

        $shopId = $request->shop_id;

        $shop = Shop::where([
            'id' => $shopId
        ])->first();

        $user = auth()->user();

        $user->slogan = $request->slogan;

        $user->save();

        if (!$shop) {
            throw new \Exception('系统暂时没有改档口信息，请联系客服');
        }

        $shop->is_competition = 1;
        $shop->save();

        $goods = json_decode($request->get('goods'), true);

        array_map(function($item){
            app(Goods::class)->fill($item)->save();
        }, $goods);


        return $this->response->withNoContent();
    }
}
