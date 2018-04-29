<?php
namespace App\Transformers;

use App\Goods;
use Illuminate\Support\Facades\DB;
use League\Fractal\TransformerAbstract;

class GoodsTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'shop'
    ];

    public function transform(Goods $goods)
    {
        return [
            "id" => $goods->id,
            "shop_id" => $goods->shop_id,
            "img" => $goods->img,
            "is_competition" => $goods->is_competition,
            "vote_count" => $goods->vote_count,
            "vote_user" => DB::table('vote')->where('goods_id', '=', $goods->id)->get()->pluck('user_id')->toArray(),
            "title" => $goods->title,
            "video" => $goods->video,
            "desc" => $goods->desc,
            "created_at" => $goods->created_at,
        ];
    }

    public function includeShop(Goods $goods)
    {
        if ($shop = $goods->shop) {
            return $this->item($shop, new ShopTransformer());
        }
    }

}