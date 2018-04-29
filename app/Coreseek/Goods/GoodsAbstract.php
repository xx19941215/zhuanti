<?php

namespace App\Coreseek\Goods;

abstract class GoodsAbstract
{
    protected $coreseekDB;

    /**
     * @var
     */
    protected $tableName;
    /**
     * 页面排序规则
     * @var array
     */
    protected $sortType = [
        'daily-new'             => [
            'mr'         => 'daily_new_refreshed desc, random_sort_index desc',
            'newOn_desc' => 'item_created desc, random_sort_index desc',
            'priOn_asc'  => 'item_price_pifa asc, random_sort_index desc',
            'priOn_desc' => 'item_price_pifa desc, random_sort_index desc',
        ],
        'product-list'          => [
            'mr'         => 'hot_level desc, syn_created desc, random_sort_index desc',
            'newOn_desc' => 'hot_level desc, syn_created desc, random_sort_index desc',
            'priOn_asc'  => 'hot_level desc, item_price_pifa asc, random_sort_index desc',
            'priOn_desc' => 'hot_level desc, item_price_pifa desc, random_sort_index desc',
        ],
        'flagship-product-list' => [
            'mr'         => 'hot_level desc, syn_created desc, random_sort_index desc',
            'newOn_desc' => 'hot_level desc, syn_created desc, random_sort_index desc',
            'priOn_asc'  => 'hot_level desc, item_price_pifa asc, random_sort_index desc',
            'priOn_desc' => 'hot_level desc, item_price_pifa desc, random_sort_index desc',
        ],
        'tong'                  => [
            'mr'         => 'hot_level desc, random_sort_index desc, syn_created desc',
            'newOn_desc' => 'hot_level desc, syn_created desc, random_sort_index desc',
            'priOn_asc'  => 'hot_level desc, item_price_pifa asc, random_sort_index desc',
            'priOn_desc' => 'hot_level desc, item_price_pifa desc, random_sort_index desc',
        ],
        'model-shot'            => [
            'mr'         => 'mtsp_hot_level desc, random_sort_index desc, syn_created desc',
            'newOn_desc' => 'item_created desc, random_sort_index desc',
            'priOn_asc'  => 'item_price_pifa asc, random_sort_index desc',
            'priOn_desc' => 'item_price_pifa desc, random_sort_index desc',
        ],
        'shop-detail'           => [
            'mr'         => 'item_created desc',
            'newOn_asc'  => 'item_created asc',
            'newOn_desc' => 'item_created desc',
            'offOn_asc'  => 'syn_modified asc',
            'offOn_desc' => 'syn_modified desc',
            'priOn_asc'  => 'item_price_pifa asc',
            'priOn_desc' => 'item_price_pifa desc',
        ]
    ];

    public function __construct()
    {
    }

    /**
     * 提供每日新款数据
     * @param mixed $param
     * @return array
     * @author marlon marlon@tfan.net
     */
    public function getList($param)
    {
        $tableName = $this->getTableName($param);
        $param = $this->filter($param);
        $parsedQuery = $this->parseQuery($param);
        $sql = "
            SELECT
                id,
                zdid,
                shop_market_id,
                shop_floor_id,
                item_cid,
                item_title,
                item_price_pifa,
                item_price,
                sid as shop_id,
                item_pic_url,
                shop_name,
                shop_market_name,
                shop_floor_name,
                item_no,
                item_id,
                shop_deposit,
                item_created
            FROM ${tableName}
            WHERE 1=1
            AND QUERY='
                ${parsedQuery}
        ";

        $productList = $this->coreseekDB->select($sql);
        $totalPage   = $this->coreseekDB->select('SHOW STATUS LIKE \'sphinx_total_found\';');
        $total = isset($totalPage[0]) && !empty($totalPage[0]->Value) ? $totalPage[0]->Value : 0;


        return [
            'status' => 'success',
            'productList' => $productList,
            'total'       =>  (int)$total,
            'page' => (int)$param['page'] ?? 1,
            'totalPage'   => ceil($total/($param['pageSize'] ?? 20)),
            'pageSize'   => (int)$param['pageSize'] ?? 20,
        ];
    }

    /**
     * 根据查询参数格式化sql语句
     * @param array $param
     * @return string
     * @author marlon marlon@tfan.net
     */
    public function parseQuery($param)
    {
        $pageSize = $param['pageSize'] ?? 20;
        if(!empty($param['sid']) && is_array($param['sid'])){
            $param['sid'] = implode(',', $param['sid']);
        }
        $diffOtherQuery = $this->getDiffBySortType($param['sortType'] ?? '', $param);
        $diffOtherFilter= $this->getDiffFilterBySortType($param['sortType'] ?? '', $param);
        $keyword        = !empty($param['so']) ? '(@search_key=' . $param['so'] . ' )' : '';
        $keyword        = $this->formatKeyword($keyword);
        $color          = !empty($param['color']) ? '(@item_tag_colors ' . str_replace('码', '', $param['color']) . ')' : '';
        $size           = !empty($param['size']) ? '(@item_tag_sizes ' . str_replace('码', '', $param['size']) . ')' : '';
        $mid            = !empty($param['mid']) ? 'filter=shop_market_id,' . $param['mid'] . ';' : '';
        $fid            = !empty($param['fid']) ? 'filter=shop_floor_id,' . $param['fid'] . ';' : '';
        $sid            = !empty($param['sid']) ? "filter=sid,${param['sid']};" : '';
        $cateID         = isset($param['child_ids']) && !!$param['child_ids'] ? 'filter=item_cid,' . $param['child_ids'] . ';' : '';
        $startPrice     = !empty($param['pstart']) ? $param['pstart'] : 0;
        $endPrice       = !empty($param['pend']) ? $param['pend'] : 100000000;
        $offset         = !empty($param['offset']) ? $param['offset'] : (!empty($param['page']) ? ($param['page'] - 1) * $pageSize : 0);
        $rq             = !empty($param['rq']) ? 'filter=item_created_diff,' . $param['rq'] . ';' : '';
        $rqs            = !empty($param['rqs']) ? 'range=item_created_diff,1,' . $param['rqs'] . ';' : '';
        $ord            = $this->getSortRule($param['sortType'] ?? '', $param['ord'] ?? 'mr');
        $isShow         = isset($param['rack']) ? $param['rack'] : 1;
        $itemStatus     = isset($param['rack']) && $param['rack'] == 0 ? '!filter=item_status,-99;' : '';
        $flagship       = !empty($param['fs']) ? "filter=is_flagship_item,${param['fs']};" : '';
        $shopService    = !empty($param['sv']) ? '(@shop_service_num ' . $param['sv'] . ')' : '';
        $ml             = !empty($param['ml']) ? 'groupby=attr:merge_similar;groupsort=item_created desc;' : '';
        $notGid         = !empty($param['not_gid']) ? '!filter=gid,' . $param['not_gid'] . ';' : '';

        /** 档口详情页面有毒,需要单独调整默认的排序 */
        $param['ord'] = $param['ord'] ?? 'mr';
        if (!empty($param['sortType']) && $param['sortType'] == 'shop-detail' && $param['ord'] == 'mr') {
            $ord = $param['ord_rule'] ?? '';
        }

        return "
            query=${diffOtherQuery}${keyword}${color}${size}${shopService};
            ${notGid}
            ${mid}
            ${fid}
            ${cateID}
            ${rq}
            ${sid}
            ${flagship}
            ${rqs}
            {$ml}
            ${diffOtherFilter}
            floatrange=item_price_pifa,${startPrice},${endPrice};
            filter=zdid,${param['zdid']};
            filter=is_show,${isShow};
            ${itemStatus}
            mode=extended2;
            sort=extended:${ord};
            offset=${offset};limit=${pageSize};maxmatches=95000;comment=这是搜款式页面的查询;';
        ";
    }

    /**
     * 根据不同的页面规则返回不同的排序
     * @param $sortType
     * @param $sortBy
     * @return string
     */
    protected function getSortRule($sortType, $sortBy)
    {
        if ($sortType == '' || !isset($this->sortType[$sortType])) {
            $sortType = 'product-list';
        }
        if ($sortBy == '' || !isset($this->sortType[$sortType][$sortBy])) {
            $sortBy = array_keys($this->sortType[$sortType])[0];
        }

        return $this->sortType[$sortType][$sortBy];
    }

    public function getDiffFilterBySortType($sortType, $params)
    {
        if ($sortType == 'diff-model-shot') {
            return '!filter=mtsp_hot_level,0;';
        }
        if ($sortType == 'show-by-daifa') {
            $sids = $params['sids'] ?? 0;
            return "filter=sid,${sids};";
        }
        if ($sortType == 'tools-ads') {
            return '!filter=mtsp_hot_level,0;!filter=hot_level,1,2,3,4,5,6,7,8;';
        }


    }

    /**
     * 区别开不同的页面搜索所需的不同条件类型
     * @param $sortType
     * @param array $param
     * @return string
     */
    protected function getDiffBySortType($sortType, $param = [])
    {
        if ($sortType == 'model-shot') {
            return '(@shop_service_num 2)';
        }

        if ($sortType == 'tong') {
            return "(@item_title ${param['title']})";
        }

        if ($sortType == 'shop-detail' && isset($param['cateid']) && $param['cateid'] != '') {
            return "(@item_seller_cids {$param['cateid']})";
        }

        if ($sortType == 'search-in-shop') {
            return '';
        }
        return '';
    }

    /**
     * 获取查询coreseek的表名,根据站点划分
     * @param $siteID
     * @return string
     */
    protected function getTableName($params)
    {
        // NOTICE: 这里更改了coreseek获取的表名,之后不知是否会有变动
        $siteID = $params['zdid'] ?? 0;
        $isShow = $params['rack'] ?? 1; // 上下架商品

        if($isShow === '0'){
            return 'search_Goods_Info_Under';
        }

        return 'search_Goods_Info';
    }

    protected function formatKeyword($keyword)
    {
        $keyword = urldecode($keyword);

        $filterKey = [
            '$',
            '-',
            '!',
            '|',
            '/',
        ];
        return str_replace($filterKey, ' ', $keyword);
    }


    /**
     * 查询参数处理
     * @param $param
     * @return mixed
     */
    protected function filter($param)
    {
        $param['so'] = isset($param['so']) ? addslashes($param['so']) : '';
        $param['mid'] = isset($param['mid']) ? (int)$param['mid'] : '';
        $param['fid'] = isset($param['fid']) ? (int)$param['fid'] : '';
        $param['color'] = isset($param['color']) ? $param['color'] : '';
        $param['size'] = isset($param['size']) ? $param['size'] : '';

        return $param;
    }
}
