<?php

namespace App\Repositories;

use App\Coupon;
use App\Exceptions\HasVotedException;
use App\Goods;
use App\Stats;
use App\User;
use App\Exceptions\ExceedVoteLimitException;

class GoodsRepositories
{
    use BaseRepository;

    protected $model;
    protected $coreseekModel;

    public function __construct(Goods $goods, \App\Coreseek\Goods\Goods $coreseekGoods)
    {
        $this->model = $goods;
        $this->coreseekModel = $coreseekGoods;
    }

    public function toggleVote(int $id)
    {
        $user = auth()->user();
//        $user = User::first();

        $goods = $this->getById($id);

        $hasVoted = $user->hasVoted($goods);

        $stats = Stats::firstOrCreate([
            'date' => date("Y-m-d"),
        ]);

        if ($hasVoted) {
            // 不允许取消投票
            throw new HasVotedException();

        } else {
            if ($user->vote_today == User::VOTE_LIMIT) {
                throw new ExceedVoteLimitException;
            }

            $user->upVote($goods);
            $goods->increment('vote_count', 1);
            $user->increment('vote_today', 1);
            $user->increment('vote_count', 1);
        }

        return true;
    }


    public function getListByCoreseek($param)
    {
        $args = [
            'so' => $param['so'] ?? '',
            'page' => $param['page'] ?? 1,
            'zdid' => $param['zdid'] ?? '42',
            'sortType' => $param['sortType'] ?? 'product-list',
            'sid' => $param['shop_id'] ?? '',
            'pageSize' => $param['pageSize'] ?? 20,
            'child_ids' => $param['child_ids'] ?? 0,
            "ord_rule" => $param['ord_rule'] ?? "item_created desc",
        ];

        return $this->coreseekModel->getList($args);
    }
}
