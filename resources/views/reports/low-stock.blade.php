@extends('layouts.app')
@section('title', 'Low Stock Report')
@section('page-title', 'Low Stock Report')

@section('content')
<div class="table-container">
    <button onclick="window.print()" class="btn btn-primary mb-3"><i class="fas fa-print me-1"></i>Print Report</button>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>#</th><th>Medicine</th><th>Category</th><th>Supplier</th><th>Stock</th><th>Threshold</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($medicines as $m)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $m->name }}</td>
                    <td>{{ $m->category->name ?? 'N/A' }}</td>
                    <td>{{ $m->supplier->name ?? 'N/A' }}</td>
                    <td>{{ $m->stock_quantity }}</td>
                    <td>{{ $m->low_stock_threshold }}</td>
                    <td>{{ $m->stock_quantity <= 0 ? 'Out of Stock' : 'Low Stock' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No low stock medicines found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
