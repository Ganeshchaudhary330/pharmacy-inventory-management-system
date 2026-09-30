@extends('layouts.app')
@section('title', 'Sale ' . $sale->invoice_number)
@section('page-title', 'Sale Details')

@section('content')
<div class="row g-4">
    <div class="col-md-5">
        <div class="table-container">
            <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Sale Information</h5>
            <table class="table table-sm">
                <tr><th>Invoice #</th><td class="fw-bold">{{ $sale->invoice_number }}</td></tr>
                <tr><th>Date</th><td>{{ $sale->created_at->format('d M Y h:i A') }}</td></tr>
                <tr><th>Customer</th><td>{{ $sale->customer_name ?? 'Walk-in Customer' }}</td></tr>
                <tr><th>Phone</th><td>{{ $sale->customer_phone ?? 'N/A' }}</td></tr>
                <tr><th>Payment Method</th><td>{{ $sale->payment_method }}</td></tr>
                <tr><th>Sale Type</th><td>{{ $sale->sale_type == 'credit' ? 'Credit' : 'Cash' }}</td></tr>
                @if($sale->sale_type == 'credit')
                <tr><th>Due Amount</th><td class="text-danger fw-bold">Rs. {{ number_format($sale->due_amount, 2) }}</td></tr>
                <tr><th>Due Date</th><td>{{ $sale->due_date ? $sale->due_date->format('d M Y') : 'N/A' }}</td></tr>
                @endif
                <tr><th>Status</th><td>{{ $sale->status=='completed' ? 'Completed' : 'Cancelled' }}</td></tr>
                <tr><th>Processed By</th><td>{{ $sale->user->name }}</td></tr>
            </table>
        </div>
    </div>
    <div class="col-md-7">
        <div class="table-container">
            <h5 class="fw-bold mb-3"><i class="fas fa-receipt me-2"></i>Items</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Medicine</th><th>Category</th><th>Price</th><th>Qty</th><th>Total</th><th>Actions</th></tr></thead>
                    <tbody>
                        @foreach($sale->saleItems as $item)
                        <tr>
                            <td>{{ $item->medicine->name }}</td>
                            <td>{{ $item->medicine->category->name ?? 'N/A' }}</td>
                            <td>Rs. {{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="fw-bold">Rs. {{ number_format($item->total_price, 2) }}</td>
                            <td>
                                <a href="{{ route('medicines.show', $item->medicine) }}" class="btn btn-sm btn-info text-white" title="View medicine"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('sales.invoice', $sale) }}" class="btn btn-sm btn-secondary" target="_blank" title="Print invoice"><i class="fas fa-print"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td colspan="4" class="text-end">Subtotal:</td><td>Rs. {{ number_format($sale->subtotal, 2) }}</td></tr>
                        <tr><td colspan="4" class="text-end">Discount:</td><td>-Rs. {{ number_format($sale->discount, 2) }}</td></tr>
                        <tr><td colspan="4" class="text-end">Tax:</td><td>Rs. {{ number_format($sale->tax, 2) }}</td></tr>
                        <tr class="fw-bold"><td colspan="4" class="text-end">Total:</td><td>Rs. {{ number_format($sale->total, 2) }}</td></tr>
                        <tr><td colspan="4" class="text-end">Amount Paid:</td><td>Rs. {{ number_format($sale->amount_paid, 2) }}</td></tr>
                        <tr><td colspan="4" class="text-end">Change:</td><td class="text-success">Rs. {{ number_format($sale->change_amount, 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('sales.invoice', $sale) }}" class="btn btn-secondary" target="_blank"><i class="fas fa-print me-1"></i>Print Invoice</a>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-primary">Back to Sales</a>
            </div>
        </div>
    </div>
</div>
@endsection
