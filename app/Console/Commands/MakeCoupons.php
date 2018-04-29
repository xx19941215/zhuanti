<?php

namespace App\Console\Commands;

use App\Coupon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MakeCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:coupons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->makeCoupons();

    }

    public function makeCoupons()
    {
        $existedCouponsCount = Coupon::where('date', date('Y-m-d'))->count();

        if ($existedCouponsCount == 53) {
            echo '错误！！今日红包之前已经生成' . PHP_EOL;
            Log::error('今日红包之前已经生成');

            return false;
        }


        $amount = 40000;
        $min = 500;
        $max = 1000;

        $coupons = [];

        for ($i = 0; $i < 53; $i++) {
            $c = mt_rand($min, $max);
            $amount -= $c;
            $coupons[] = $c;
        }
        //钱发超了
        while ($amount < 0) {
            for ($i = 0; $i < 53; $i++) {
                if ($amount < 0 && $coupons[$i] > $min) {
                    $coupons[$i]--;
                    $amount++;
                }
            }
        }

        //钱发少了
        while ($amount > 0) {
            for ($i = 0; $i < 53; $i++) {
                if ($amount > 0 && $coupons[$i] < $max) {
                    $coupons[$i]++;
                    $amount--;
                }
            }
        }
        echo '今日红包生成完毕' . PHP_EOL;
        foreach ($coupons as $key => $val) {
            $val = $val/100;
            echo "第{$key}个红包，金额{$val}元" . PHP_EOL;
            Log::info("第{$key}个红包，金额{$val}元");
        }

        Log::info("红包生成完毕");

        $this->saveToDB($coupons);
    }

    protected function saveToDB(array $coupons)
    {
        foreach ($coupons as $item) {
            app(Coupon::class)->fill(
                [
                    'amount' => $item / 100,
                    'date' => date('Y-m-d')
                ]
            )->save();
        }

        echo '今日红包保存完毕' . PHP_EOL;
        Log::info("红包保存完毕");

    }
}
