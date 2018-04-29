<?php

namespace App;

use App\Scopes\CompetitionScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Goods extends Model
{
    protected $table = 'goods';
    protected $fillable = ['id', 'img', 'title', 'desc', 'video', 'url', 'shop_id', 'is_competition'];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompetitionScope());
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public static function hotGoods($limit = 5)
    {
        $data = Cache::remember('competition_hot_goods', 10, function() use ($limit){
            return self::orderBy('vote_count', 'DESC')
                ->limit($limit)
                ->get();
        });
        return $data;
    }

    public static function newGoods($limit = 5)
    {
        $data = Cache::remember('competition_new_goods', 10, function() use ($limit){
            return self::orderBy('created_at', 'DESC')
                ->limit($limit)
                ->get();
        });
        return $data;
    }
}
