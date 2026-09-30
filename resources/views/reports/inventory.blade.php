@extends('layouts.app')
@section('title', 'Inventory Report')
@section('page-title', 'Inventory Report')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card stat-card p-3 text-center"><h3 class="fw-bold text-primary">{{ $medicines->count() }}</h3><small class="text-muted">Total Items</small></div></div>
    <div class="col-md-3"><div class="card stat-card p-3 text-center"><h3 class="fw-bold text-success">Rs. {{ number_format($totalValue, 2) }}</h3><small class="text-muted">Total Purchase Value</small></div></div>
    <div class="col-md-3"><div class="card stat-card p-3 text-center"><h3 class="fw-bold text-info">Rs. {{ number_format($totalSellingValue, 2) }}</h3><small class="text-muted">Total Selling Value</small></div></div>
    <div class="col-md-3"><div class="card stat-card p-3 text-center"><h3 class="fw-bold text-success">Rs. {{ number_format($totalSellingValue - $totalValue, 2) }}</h3><small class="text-muted">Potential Profit</small></div></div>
</div>

<div class="table-container">
    <form method="GET" class="d-flex gap-2 mb-3">
        <select name="category_id" class="form-select" style="width:200px" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="stock_status" class="form-select" style="width:200px" onchange="this.form.submit()">
            <option value="">All Stock</option>
            <option value="active" {{ request('stock_status')=='active' ? 'selected' : '' }}>In Stock</option>
            <option value="low" {{ request('stock_status')=='low' ? 'selected' : '' }}>Low Stock</option>
            <option value="out" {{ request('stock_status')=='out' ? 'selected' : '' }}>Out of Stock</option>
        </select>
        <a href="{{ route('reports.inventory') }}" class="btn btn-secondary">Reset</a>
        <button onclick="window.print()" class="btn btn-primary ms-auto"><i class="fas fa-print me-1"></i>Print Report</button>
    </form>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>#</th><th>Name</th><th>Category</th><th>Stock</th><th>Purchase Price</th><th>Selling Price</th><th>Expiry</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($medicines as $m)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $m->name }}</td>
                    <td>{{ $m->category->name ?? 'N/A' }}</td>
                    <td>{{ $m->stock_quantity }}</td>
                    <td>Rs. {{ number_format($m->purchase_price, 2) }}</td>
                    <td>Rs. {{ number_format($m->selling_price, 2) }}</td>
                    <td>{{ $m->expiry_date ? $m->expiry_date->format('d M Y') : 'N/A' }}</td>
                    <td>{{ $m->isExpired() ? 'Expired' : ($m->stock_quantity <= 0 ? 'Out' : 'Active') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No medicines found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
