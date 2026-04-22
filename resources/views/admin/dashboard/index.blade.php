@extends('layouts.admin')

@section('title', '数据看板')

@section('content')
    <div class="filter-card">
        <form action="{{ route('dashboard') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">小区</label>
                <input type="text" class="form-control" name="community" 
                       placeholder="请输入小区名称" 
                       value="{{ request('community') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">区域商圈</label>
                <input type="text" class="form-control" name="district" 
                       placeholder="请输入区域商圈" 
                       value="{{ request('district') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">成交时间开始</label>
                <input type="date" class="form-control" name="deal_date_start" 
                       value="{{ request('deal_date_start') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">成交时间结束</label>
                <input type="date" class="form-control" name="deal_date_end" 
                       value="{{ request('deal_date_end') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">年份</label>
                <select class="form-select" name="year">
                    <option value="">全部年份</option>
                    @foreach ($availableYears as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}年
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">月份</label>
                <select class="form-select" name="month">
                    <option value="">全部月份</option>
                    @foreach ($availableMonths as $month)
                        <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                            {{ $month }}月
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search me-1"></i>查询
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>重置
                </a>
            </div>
        </form>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card blue">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number">{{ $totalCount }}</div>
                        <div class="stat-label">房源总数</div>
                    </div>
                    <i class="bi bi-house-door fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number">{{ number_format($totalPriceSum, 2) }} 万</div>
                        <div class="stat-label">总价总额</div>
                    </div>
                    <i class="bi bi-currency-yen fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card orange">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number">{{ number_format($avgPrice, 2) }} 万</div>
                        <div class="stat-label">平均总价</div>
                    </div>
                    <i class="bi bi-graph-up fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-geo-alt me-2"></i>区域商圈 TOP 10
                </div>
                <div class="card-body">
                    @if ($districtStats->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th width="60">#</th>
                                    <th>区域商圈</th>
                                    <th width="120" class="text-end">房源数量</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($districtStats as $index => $stat)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $stat->district }}</td>
                                        <td class="text-end">{{ $stat->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted text-center py-4">暂无数据</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-calendar me-2"></i>月度成交趋势
                </div>
                <div class="card-body">
                    @if ($monthlyStats->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th width="60">#</th>
                                    <th>年月</th>
                                    <th width="120" class="text-end">房源数量</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($monthlyStats as $index => $stat)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $stat->year_month }}</td>
                                        <td class="text-end">{{ $stat->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted text-center py-4">暂无数据</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-door-open me-2"></i>户型分布 TOP 10
                </div>
                <div class="card-body">
                    @if ($layoutStats->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th width="60">#</th>
                                    <th>户型</th>
                                    <th width="120" class="text-end">房源数量</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($layoutStats as $index => $stat)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $stat->layout }}</td>
                                        <td class="text-end">{{ $stat->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted text-center py-4">暂无数据</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>统计说明
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>房源总数：当前筛选条件下的房源数量</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>总价总额：所有房源总价的累加值（单位：万元）</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>平均总价：总价总额除以房源总数</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>区域商圈 TOP 10：按房源数量排名前10的区域</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>月度成交趋势：最近12个月的成交统计</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>户型分布 TOP 10：按房源数量排名前10的户型</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
