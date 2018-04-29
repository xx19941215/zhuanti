<?php
namespace App\Transformers;

use App\Shop;
use League\Fractal\TransformerAbstract;

class ShopTransformer extends TransformerAbstract
{
    public function transform(Shop $shop)
    {
        return [
            "id" => $shop->id,
            "name" => $shop->name,
        ];
    }
}