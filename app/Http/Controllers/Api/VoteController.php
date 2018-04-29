<?php
namespace App\Http\Controllers\Api;

use App\Coupon;
use App\Repositories\GoodsRepositories;
use Illuminate\Http\Request;

class VoteController extends ApiController
{
    protected $goods;

    public function __construct(GoodsRepositories $goodsRepositories)
    {
        parent::__construct();

        $this->goods = $goodsRepositories;
    }

    public function postVoteGoods(Request $request)
    {

        $hasVoted = $this->goods->toggleVote($request->id);

        if ($hasVoted) {
            if (Coupon::isPrize()) {
                $coupon = app(Coupon::class)->updateCoupon($request->id);
                return $this->response->json(['status' => 'success', 'prize' => true, 'coupon' => $coupon]);
            }

            return $this->response->json([
                'status' => 'success',
                'prize' => false
            ]);
        }

    }
}