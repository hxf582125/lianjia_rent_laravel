<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 房源模型类
 * 
 * 对应数据库表 houses，用于房源数据的操作和查询
 */
class House extends Model
{
    /**
     * 表名
     * 
     * @var string
     */
    protected $table = 'houses';

    /**
     * 可批量赋值的字段
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'biz_key',
        'community',
        'district',
        'layout',
        'total_price',
        'agent_unit_price',
        'market_unit_price',
        'agent_company',
        'deal_date',
        'area',
        'floor',
        'listing_price',
        'transaction_cycle',
        'orientation',
        'floor_detail',
        'building',
        'decoration',
        'year_built',
        'has_elevator',
        'monthly_rent',
        'rent_yield',
        'price_ref_percent',
        'price_change',
        'rank_info',
        'bargain_amount',
        'bargain_percent',
        'project_source',
        'payload_json',
    ];

    /**
     * 筛选条件作用域
     * 
     * 应用各种筛选条件到查询中
     * 
     * 注意：
     * - deal_date 字段是 varchar 类型，格式为 'YYYY-MM-DD'
     * - 因此使用字符串操作（LIKE、SUBSTRING）而非日期函数
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query 查询构建器
     * @param array $filters 筛选条件数组
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $filters)
    {
        if (isset($filters['community']) && $filters['community'] !== '') {
            $query->where('community', 'like', '%' . $filters['community'] . '%');
        }

        if (isset($filters['district']) && $filters['district'] !== '') {
            $query->where('district', 'like', '%' . $filters['district'] . '%');
        }

        if (isset($filters['area_min']) && $filters['area_min'] !== '') {
            $query->where('area', '>=', $filters['area_min']);
        }

        if (isset($filters['area_max']) && $filters['area_max'] !== '') {
            $query->where('area', '<=', $filters['area_max']);
        }

        if (isset($filters['price_min']) && $filters['price_min'] !== '') {
            $query->whereRaw('CAST(REPLACE(total_price, ",", "") AS DECIMAL(12,2)) >= ?', [$filters['price_min']]);
        }

        if (isset($filters['price_max']) && $filters['price_max'] !== '') {
            $query->whereRaw('CAST(REPLACE(total_price, ",", "") AS DECIMAL(12,2)) <= ?', [$filters['price_max']]);
        }

        if (isset($filters['deal_date_start']) && $filters['deal_date_start'] !== '') {
            $query->where('deal_date', '>=', $filters['deal_date_start']);
        }

        if (isset($filters['deal_date_end']) && $filters['deal_date_end'] !== '') {
            $query->where('deal_date', '<=', $filters['deal_date_end']);
        }

        if (isset($filters['year']) && $filters['year'] !== '') {
            $query->where('deal_date', 'like', $filters['year'] . '%');
        }

        if (isset($filters['month']) && $filters['month'] !== '') {
            $monthStr = str_pad($filters['month'], 2, '0', STR_PAD_LEFT);
            $query->whereRaw("SUBSTRING(deal_date, 6, 2) = ?", [$monthStr]);
        }

        return $query;
    }

    /**
     * 获取数值类型的总价
     * 
     * 移除千分位逗号后转换为浮点数
     * 
     * @return float
     */
    public function getNumericTotalPriceAttribute()
    {
        return (float) str_replace(',', '', $this->total_price);
    }

    /**
     * 获取数值类型的面积
     * 
     * 移除千分位逗号后转换为浮点数
     * 
     * @return float
     */
    public function getNumericAreaAttribute()
    {
        return (float) str_replace(',', '', $this->area);
    }
}
