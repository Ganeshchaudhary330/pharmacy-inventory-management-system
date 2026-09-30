@extends('layouts.app')
@section('title', 'Medicines')
@section('page-title', 'Medicines')

@section('content')
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" class="d-flex flex-wrap gap-2">
            <div class="input-group" style="width:250px">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search medicines..." value="{{ request('search') }}">
            </div>
            <select name="category_id" class="form-select" style="width:150px" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="stock_filter" class="form-select" style="width:150px" onchange="this.form.submit()">
                <option value="">All Stock</option>
                <option value="low" {{ request('stock_filter')=='low' ? 'selected' : '' }}>Low Stock</option>
                <option value="out" {{ request('stock_filter')=='out' ? 'selected' : '' }}>Out of Stock</option>
                <option value="expiring" {{ request('stock_filter')=='expiring' ? 'selected' : '' }}>Expiring Soon</option>
            </select>
            @if(request()->anyFilled(['search','category_id','stock_filter']))
                <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </form>
        <a href="{{ route('medicines.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Medicine</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Batch No</th>
                    <th>Stock</th>
                    <th>Purchase</th>
                    <th>Selling</th>
                    <th>Expiry</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicines as $m)
                <tr>
                    <td>{{ $m->id }}</td>
                    <td class="fw-bold"><a href="{{ route('medicines.show', $m) }}" class="text-decoration-none">{{ $m->name }}</a></td>
                    <td>{{ $m->category->name ?? 'N/A' }}</td>
                    <td>{{ $m->batch_number ?? 'N/A' }}</td>
                    <td>{{ $m->stock_quantity }}</td>
                    <td>Rs. {{ number_format($m->purchase_price, 2) }}</td>
                    <td>Rs. {{ number_format($m->selling_price, 2) }}</td>
                    <td class="{{ $m->isExpired() ? 'text-danger fw-bold' : '' }}">{{ $m->expiry_date ? $m->expiry_date->format('d M Y') : 'N/A' }}</td>
                    <td>
                        @if($m->isExpired())
                            Expired
                        @elseif($m->stock_quantity <= 0)
                            Out
                        @elseif($m->isLowStock())
                            Low
                        @else
                            Active
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('medicines.edit', $m) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('medicines.destroy', $m) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this medicine?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">No medicines found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
