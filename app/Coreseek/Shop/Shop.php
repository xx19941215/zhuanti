<?php

namespace coreseek\Shop;

use think\Db;
use think\Env;

/**
 * Class Shop
 * @package Coreseek\Shop
 */
class Shop extends ShopAbstract
{
    /**
     * @var \think\db\Connection
     */
    protected $coreseekDB;

    /**
     * Goods constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $coreseekDbConfig = [
            'type'     => 'mysql',
            'hostname' => Env::get('database.Coreseek.hostname'),
            'database' => Env::get('database.Coreseek.database'),
            'username' => Env::get('database.Coreseek.username'),
            'password' => Env::get('database.Coreseek.password'),
            'hostport' => Env::get('database.Coreseek.hostport'),
        ];

        $this->coreseekDB = Db::connect($coreseekDbConfig);
    }
}
