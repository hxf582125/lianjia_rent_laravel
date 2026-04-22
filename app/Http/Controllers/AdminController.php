<?php

namespace App\Http\Controllers;

use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 管理后台控制器
 * 
 * 提供房源列表和数据看板功能
 */
class AdminController extends Controller
{
    /**
     * 房源列表页面
     * 
     * 功能说明：
     * - 展示房源列表，按ID倒序排列
     * - 支持多条件筛选
     * - 支持分页显示
     * 
     * @param Request $request HTTP请求对象
     * @return \Illuminate\View\View
     */
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

    /**
     * 数据看板页面
     * 
     * 功能说明：
     * - 展示房源统计数据
     * - 支持小区、区域商圈、成交时间、年份、月份筛选
     * - 统计指标：房源总数、总价总额、平均总价
     * - 区域商圈 TOP 10 排名
     * - 月度成交趋势
     * - 户型分布 TOP 10
     * 
     * @param Request $request HTTP请求对象
     * @return \Illuminate\View\View
     */
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

        $availableYears = $this->getAvailableYears();
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

    /**
     * 获取可用年份列表（静态生成，避免数据库 GROUP BY 慢查询）
     * 
     * 生成当前年份及过去10年的年份列表
     * 这样可以避免对大表执行 GROUP BY 查询导致的性能问题
     * 
     * @return array 年份数组，格式：[2026, 2025, 2024, ...]
     */
    private function getAvailableYears()
    {
        $currentYear = (int) date('Y');
        $years = [];
        
        for ($i = 0; $i < 15; $i++) {
            $years[] = $currentYear - $i;
        }
        
        return $years;
    }

    /**
     * 计算总价总额
     * 
     * 使用 MySQL 直接计算，避免 PHP 遍历大量数据
     * 由于 total_price 是 varchar 类型且可能包含千分位逗号，
     * 使用 REPLACE 移除逗号后转换为 DECIMAL 类型进行求和
     * 
     * 性能优化：
     * - 让数据库完成计算，比 PHP 遍历效率高得多
     * - 避免将大量数据加载到内存
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query 查询构建器
     * @return float 总价总额（单位：万元）
     */
    private function calculateTotalPriceSum($query)
    {
        $result = $query->selectRaw(
            'SUM(CAST(REPLACE(total_price, ",", "") AS DECIMAL(12,2))) as total_sum'
        )->first();
        
        return (float) ($result->total_sum ?? 0);
    }

    /**
     * 获取区域商圈统计 TOP 10
     * 
     * 按房源数量统计，返回前10名的区域商圈
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query 查询构建器
     * @return \Illuminate\Database\Eloquent\Collection 统计结果集合
     *         每个元素包含：district（区域商圈名称）、count（房源数量）
     */
    private function getDistrictStats($query)
    {
        return $query->select('district', DB::raw('count(*) as count'))
            ->where('district', '!=', '')
            ->groupBy('district')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * 获取月度成交统计
     * 
     * 按年月统计房源数量，返回最近12个月的统计数据
     * 
     * 注意：deal_date 字段是 varchar 类型，格式为 'YYYY-MM-DD'，
     * 因此使用字符串截取函数而非日期函数来提取年月信息
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query 查询构建器
     * @return \Illuminate\Support\Collection 统计结果集合
     *         每个元素包含：year（年份）、month（月份）、count（房源数量）、year_month（年月格式：YYYY-MM）
     */
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
                return is_numeric($item->year) && is_numeric($item->month) 
                    && $item->month >= 1 && $item->month <= 12;
            })
            ->map(function ($item) {
                $item->year_month = $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                return $item;
            });
    }

    /**
     * 获取户型分布统计 TOP 10
     * 
     * 按房源数量统计，返回前10名的户型
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query 查询构建器
     * @return \Illuminate\Database\Eloquent\Collection 统计结果集合
     *         每个元素包含：layout（户型名称）、count（房源数量）
     */
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
