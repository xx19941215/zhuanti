<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Klein\App;

class User extends Authenticatable
{
    use Notifiable;
    const VOTE_LIMIT = 20;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'vote_today', 'avt', 'remember_token', 'slogan', 'vote_count'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $timestamps = true;

    public function upVote(Goods $goods)
    {
        return $this->votedGoods()->sync((array) $goods->id, false);
    }

    public function cancelVote(Goods $goods)
    {
        return $this->votedGoods()->detach((array) $goods->id);
    }

    public function hasVoted(Goods $goods)
    {
        return $this->votedGoods()->get()->contains($goods);
    }

    public function votedGoods()
    {
        return $this->belongsToMany(Goods::class, 'vote');
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}
