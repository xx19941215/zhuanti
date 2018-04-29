<?php

namespace App\Repositories;


use App\Shop;

class ShopRepositories
{
    use BaseRepository;

    protected $model;

    public function __construct(Shop $shop)
    {
        $this->model = $shop;
    }
}
