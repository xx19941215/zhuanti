<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    const COUPON_PRO = [
        ['id' => 1, 'isPrize' => true, 'val' => 1],
        ['id' => 2, 'isPrize' => false, 'val' => 99]
    ];

    protected $table = 'coupons';
    protected $fillable = ['user_id', 'goods_id', 'date', 'amount'];

    public static function isPrize()
    {
        $validCouponCount = Coupon::where('date', date('Y-m-d'))->whereNull('code')->count();

        if ($validCouponCount == 0) {
            //今日红包已经发完
            return false;
        }

        $result = array();

        foreach (Coupon::COUPON_PRO as $key => $val) {
            $arr[$key] = $val['val'];
        }

        $proSum = array_sum($arr);

        foreach ($arr as $k => $v) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $v) {
                $result = Coupon::COUPON_PRO[$k];
                break;
            } else {
                $proSum -= $v;
            }
        }

        if ($result['isPrize']) {
            return true;
        }

        return false;
    }

    public function updateCoupon(string $goodsId)
    {
        $coupon = Coupon::where('date', date('Y-m-d'))->whereNull('code')->first();

//        $coupon->user_id = auth()->user()->id;
        $coupon->user_id = User::first()->id;
        $coupon->goods_id = $goodsId;
        $coupon->code = uniqid();
        $coupon->save();

        return $coupon;
    }

}
