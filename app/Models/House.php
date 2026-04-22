<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $table = 'houses';

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
            $query->whereRaw('CAST(total_price AS DECIMAL(10,2)) >= ?', [$filters['price_min']]);
        }

        if (isset($filters['price_max']) && $filters['price_max'] !== '') {
            $query->whereRaw('CAST(total_price AS DECIMAL(10,2)) <= ?', [$filters['price_max']]);
        }

        if (isset($filters['deal_date_start']) && $filters['deal_date_start'] !== '') {
            $query->where('deal_date', '>=', $filters['deal_date_start']);
        }

        if (isset($filters['deal_date_end']) && $filters['deal_date_end'] !== '') {
            $query->where('deal_date', '<=', $filters['deal_date_end']);
        }

        if (isset($filters['year']) && $filters['year'] !== '') {
            $query->whereYear('deal_date', $filters['year']);
        }

        if (isset($filters['month']) && $filters['month'] !== '') {
            $query->whereMonth('deal_date', $filters['month']);
        }

        return $query;
    }

    public function getNumericTotalPriceAttribute()
    {
        return (float) str_replace(',', '', $this->total_price);
    }

    public function getNumericAreaAttribute()
    {
        return (float) str_replace(',', '', $this->area);
    }
}
