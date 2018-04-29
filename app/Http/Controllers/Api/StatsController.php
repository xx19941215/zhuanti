<?php
namespace App\Http\Controllers\Api;

use App\Goods;
use App\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsController extends ApiController
{
    public function index()
    {
        $baokuanCount = Cache::remember('competition_baokuan_count', 30, function(){
            return Goods::where('is_competition', '=', 1)
                ->get()
                ->count();
        });

        $goodsCount = Cache::remember('competition_goods_count', 30, function(){
            return Goods::count();
        });


        $voteUserCount = Cache::remember('competition_vote_user_count', 30, function(){
            return User::where('vote_count', '>', 0)->get()->count();
        });

        $voteCount = Cache::remember('competition_vote_count', 30, function(){
            return DB::table('vote')->get()->count();
        });

        return $this->response->json([
            'status' => 'success',
            'goodsCount' => $goodsCount,
            'baokuanCount' => $baokuanCount,
            'voteUserCount' => $voteUserCount,
            'voteCount' => $voteCount,
        ]);
    }
}