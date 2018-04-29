<?php

namespace coreseek\Shop;

/**
 * Class ShopAbstract
 * @package coreseek\Shop
 */
abstract class ShopAbstract
{
    /**
     * coreseek链接,采用think自带mysql连接器
     *
     * @var \think\db\Connection
     */
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
        'default'      => [
            'mr'         => 'shop_id desc',
        ]
    ];

    /**
     * ShopAbstract constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $param
     * @return array
     * @throws \Exception
     * @throws \think\exception\PDOException
     */
    public function getList($param)
    {
        $tableName = $this->getTableName($param);
        $param = $this->filter($param);
        $parsedQuery = $this->parseQuery($param);
        $sql = "
            SELECT
              shop_id,
              shop_name,
              is_gold,
              market_id,
              floor_id,
              market_name,
              dangkou_no,
              service_num,
              daifa_num,
              goods_num
            FROM ${tableName}
            WHERE 1=1
            AND QUERY='
                ${parsedQuery}
        ";

        $productList = $this->coreseekDB->query($sql);
        $totalPage = $this->coreseekDB->query('SHOW STATUS LIKE \'sphinx_total_found\';');

        return [
            'productList' => $productList,
            'total'       => isset($totalPage[0]) && isset($totalPage[0]['Value']) ? $totalPage[0]['Value'] : ''
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
        $zdid       = $param['zdid'] ?? 42;
        $sid        = !empty($param['sid']) ? "filter=shop_id,${param['sid']}" : '';
        $mid        = !empty($param['mid']) ? "filter=market_id,${param['mid']};" : '';
        $fid        = !empty($param['fid']) ? "filter=floor_id,${param['fid']}" : '';
        $flagship   = !empty($param['fs']) ?  "filter=is_flagship,${param['fs']}" : '';
        $shopName   = !empty($param['name']) ? '(@shop_name ' . $this->formatKeyword($param['name']) . ')' : '';
        $keyword    = !empty($param['so']) ? '(@search_key ' . $this->formatKeyword($param['so']) . ')' : '';
        $ord        = $this->getSortRule($param['sortType'] ?? '', $param['ord'] ?? 'mr');
        $pageSize   = $param['pageSize'] ?? 92;
        $offset     = !empty($param['page']) ? ($param['page'] - 1) * $pageSize : 0;

        return "
            query=${keyword}${shopName};
            filter=zdid,${zdid};
            ${sid}
            ${mid}
            ${fid}
            ${flagship}
            mode=extended2;
            sort=extended:${ord};
            offset=${offset};
            limit=${pageSize};
            maxmatches=9500;';
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
            $sortType = 'default';
        }
        if ($sortBy == '' || !isset($this->sortType[$sortType][$sortBy])) {
            $sortBy = array_keys($this->sortType[$sortType])[0];
        }
        return $this->sortType[$sortType][$sortBy];
    }

    /**
     * 返回查询coreseek的表名
     * @param $siteID
     * @return string
     */
    protected function getTableName($params)
    {
        return 'search_Shop_Info';
    }

    /**
     * @param $keyword
     * @return mixed
     */
    protected function formatKeyword($keyword)
    {
        $filterKey = [
            '$',
            '-',
            '!',
            "'",
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
        return $param;
    }
}
