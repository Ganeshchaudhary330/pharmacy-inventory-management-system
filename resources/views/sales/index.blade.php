@extends('layouts.app')
@section('title', 'Sales')
@section('page-title', 'Sales Management')

@section('content')
<div class="mb-3">
    <a href="{{ route('sales.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Sale</a>
</div>
<div class="table-container">
    <form method="GET" class="d-flex flex-wrap gap-2 mb-3">
        <div class="input-group" style="width:250px">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" name="search" class="form-control" placeholder="Search invoice/customer..." value="{{ request('search') }}">
        </div>
        <select name="status" class="form-select" style="width:150px" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <input type="date" name="date_from" class="form-control" style="width:160px" value="{{ request('date_from') }}">
        <input type="date" name="date_to" class="form-control" style="width:160px" value="{{ request('date_to') }}">
        <button class="btn btn-primary" type="submit"><i class="fas fa-filter me-1"></i>Filter</button>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Reset</a>
    </form>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Invoice #</th><th>Customer</th><th>Items</th><th>Subtotal</th><th>Discount</th><th>Total</th><th>Paid</th><th>Method</th><th>Due</th><th>Type</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($sales as $s)
                <tr>
                    <td class="fw-bold">{{ $s->invoice_number }}</td>
                    <td>{{ $s->customer_name ?? 'Walk-in' }}</td>
                    <td>{{ $s->saleItems->count() }}</td>
                    <td>Rs. {{ number_format($s->subtotal, 2) }}</td>
                    <td>Rs. {{ number_format($s->discount, 2) }}</td>
                    <td class="fw-bold">Rs. {{ number_format($s->total, 2) }}</td>
                    <td>Rs. {{ number_format($s->amount_paid, 2) }}</td>
                    <td>{{ $s->payment_method }}</td>
                    <td>{{ $s->sale_type == 'credit' && $s->due_amount > 0 ? 'Rs. '.number_format($s->due_amount, 2) : '-' }}</td>
                    <td>{{ $s->sale_type == 'credit' ? 'Credit' : 'Cash' }}</td>
                    <td>{{ $s->status=='completed' ? 'Completed' : 'Cancelled' }}</td>
                    <td>{{ $s->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('sales.show', $s) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('sales.edit', $s) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <a href="{{ route('sales.invoice', $s) }}" class="btn btn-sm btn-secondary" target="_blank"><i class="fas fa-print"></i></a>
                        <form action="{{ route('sales.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this sale and restore stock?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="13" class="text-center py-4 text-muted">No sales found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end">{{ $sales->links() }}</div>
</div>
@endsection
