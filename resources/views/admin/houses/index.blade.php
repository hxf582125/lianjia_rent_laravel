@extends('layouts.admin')

@section('title', '房源列表')

@section('content')
    <div class="filter-card">
        <form action="{{ route('houses.index') }}" method="GET" class="row g-3">
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
                <label class="form-label">面积最小值(㎡)</label>
                <input type="number" class="form-control" name="area_min" 
                       placeholder="最小值" step="0.01"
                       value="{{ request('area_min') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">面积最大值(㎡)</label>
                <input type="number" class="form-control" name="area_max" 
                       placeholder="最大值" step="0.01"
                       value="{{ request('area_max') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">价格最小值(万)</label>
                <input type="number" class="form-control" name="price_min" 
                       placeholder="最小值" step="0.01"
                       value="{{ request('price_min') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">价格最大值(万)</label>
                <input type="number" class="form-control" name="price_max" 
                       placeholder="最大值" step="0.01"
                       value="{{ request('price_max') }}">
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
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search me-1"></i>搜索
                </button>
                <a href="{{ route('houses.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>重置
                </a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-ul me-2"></i>房源列表</span>
            <span class="text-muted">共 {{ $houses->total() }} 条记录</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>小区</th>
                            <th>区域商圈</th>
                            <th>户型</th>
                            <th>面积(㎡)</th>
                            <th>总价(万)</th>
                            <th>成交时间</th>
                            <th>楼栋号</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($houses as $house)
                            <tr>
                                <td>{{ $house->id }}</td>
                                <td>{{ $house->community }}</td>
                                <td>{{ $house->district }}</td>
                                <td>{{ $house->layout }}</td>
                                <td>{{ $house->area }}</td>
                                <td>{{ $house->total_price }}</td>
                                <td>{{ $house->deal_date }}</td>
                                <td>{{ $house->building }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                                    暂无数据
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($houses->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    显示 {{ $houses->firstItem() }} - {{ $houses->lastItem() }} 条，共 {{ $houses->total() }} 条
                </div>
                <div>
                    {{ $houses->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <style>
        .pagination {
            margin-bottom: 0;
        }
        .page-item.active .page-link {
            background-color: #2563eb;
            border-color: #2563eb;
        }
    </style>
@endsection
