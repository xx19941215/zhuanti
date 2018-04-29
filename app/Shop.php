<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = 'shops';
    protected $fillable = ['id', 'name', 'shop_mobile_url', 'shop_pc_url', 'user_id'];

    public function goods()
    {
        return $this->hasMany(Goods::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
