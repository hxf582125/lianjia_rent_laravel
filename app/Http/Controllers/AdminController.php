<?php

namespace App\Http\Controllers;

use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'community',
            'district',
            'area_min',
            'area_max',
            'price_min',
            'price_max',
            'deal_date_start',
            'deal_date_end',
        ]);

        $perPage = $request->input('per_page', 20);

        $houses = House::filter($filters)
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($filters);

        return view('admin.houses.index', compact('houses', 'filters'));
    }

    public function dashboard(Request $request)
    {
        $filters = $request->only([
            'community',
            'district',
            'deal_date_start',
            'deal_date_end',
            'year',
            'month',
        ]);

        $query = House::filter($filters);

        $totalCount = (clone $query)->count();
        $totalPriceSum = $this->calculateTotalPriceSum(clone $query);
        $avgPrice = $totalCount > 0 ? $totalPriceSum / $totalCount : 0;

        $districtStats = $this->getDistrictStats(clone $query);
        $monthlyStats = $this->getMonthlyStats(clone $query);
        $layoutStats = $this->getLayoutStats(clone $query);

        $availableYears = House::selectRaw('LEFT(deal_date, 4) as year')
            ->whereNotNull('deal_date')
            ->where('deal_date', '!=', '')
            ->whereRaw('LENGTH(deal_date) >= 4')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter(function ($year) {
                return is_numeric($year) && $year > 0;
            })
            ->values()
            ->toArray();

        $availableMonths = range(1, 12);

        return view('admin.dashboard.index', compact(
            'totalCount',
            'totalPriceSum',
            'avgPrice',
            'districtStats',
            'monthlyStats',
            'layoutStats',
            'filters',
            'availableYears',
            'availableMonths'
        ));
    }

    private function calculateTotalPriceSum($query)
    {
        $houses = $query->select('total_price')->get();
        $sum = 0;
        foreach ($houses as $house) {
            $price = (float) str_replace(',', '', $house->total_price);
            $sum += $price;
        }
        return $sum;
    }

    private function getDistrictStats($query)
    {
        return $query->select('district', DB::raw('count(*) as count'))
            ->where('district', '!=', '')
            ->groupBy('district')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getMonthlyStats($query)
    {
        return $query->select(
            DB::raw('LEFT(deal_date, 4) as year'),
            DB::raw('SUBSTRING(deal_date, 6, 2) as month'),
            DB::raw('count(*) as count')
        )
            ->whereNotNull('deal_date')
            ->where('deal_date', '!=', '')
            ->whereRaw('LENGTH(deal_date) >= 7')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->filter(function ($item) {
                return is_numeric($item->year) && is_numeric($item->month) && $item->month >= 1 && $item->month <= 12;
            })
            ->map(function ($item) {
                $item->year_month = $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                return $item;
            });
    }

    private function getLayoutStats($query)
    {
        return $query->select('layout', DB::raw('count(*) as count'))
            ->where('layout', '!=', '')
            ->groupBy('layout')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }
}
