<?php

namespace App\Coreseek\Goods;

use Illuminate\Support\Facades\DB;

class Goods extends GoodsAbstract
{

    protected $coreseekDB;

    public function __construct()
    {
        parent::__construct();
        $this->coreseekDB = DB::connection('coreseek');
    }
}
