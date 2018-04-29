<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stats extends Model
{
    protected $table = 'stats';
    protected $fillable = ['today_vote_count', 'history_vote_count', 'user_vote_count', 'date'];
}
