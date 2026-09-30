<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $sale->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>body{font-family:'Segoe UI',sans-serif;padding:20px}.invoice-box{max-width:800px;margin:auto;padding:30px;border:1px solid #eee;box-shadow:0 0 10px rgba(0,0,0,.15)}.invoice-title{font-size:28px;font-weight:700;color:#1a1a2e}.table th{border-top:none;background:#f8f9fc;font-size:.85rem}.footer{text-align:center;margin-top:30px;color:#858796;font-size:.85rem}@media print{.no-print{display:none}}</style>
</head>
<body>
<div class="invoice-box">
    <div class="no-print text-end mb-3"><button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print me-1"></i>Print</button></div>
    <div class="row mb-4">
        <div class="col-6">
            <h2 class="invoice-title"><i class="fas fa-pills me-2"></i>{{ config('app.name', 'Pharmacy') }}</h2>
            <p class="mb-0"> Itahari-17,Bishanpur, Itahari</p>
            <p>Phone: 9800000001 | Email:princepharmacy@gmail.com</p>
        </div>
        <div class="col-6 text-end">
            <h3 class="text-muted">Prince Pharmacy</h3>
            <h4 class="fw-bold">{{ $sale->invoice_number }}</h4>
            <p>Date: {{ $sale->created_at->format('d M Y h:i A') }}</p>
            <p class="mb-0"><strong>Pharmacist:</strong> {{ Auth::user()->name ?? $sale->user->name ?? 'N/A' }}</p>
        </div>
    </div>
    <hr>
    <div class="row mb-4">
        <div class="col-6">
            <strong>Customer:</strong>
            <p class="mb-0">{{ $sale->customer_name ?? 'Walk-in Customer' }}</p>
            @if($sale->customer_phone)<p>Phone: {{ $sale->customer_phone }}</p>@endif
        </div>
        <div class="col-6 text-end">
            <strong>Payment Method:</strong>
            <p class="text-capitalize">{{ $sale->payment_method }}</p>
        </div>
    </div>
    <table class="table">
        <thead><tr><th>#</th><th>Medicine</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead>
        <tbody>
            @foreach($sale->saleItems as $i=>$item)
            <tr><td>{{ $i+1 }}</td><td>{{ $item->medicine->name }}</td><td>Rs. {{ number_format($item->unit_price, 2) }}</td><td>{{ $item->quantity }}</td><td>Rs. {{ number_format($item->total_price, 2) }}</td></tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="col-6 offset-6">
            <table class="table table-sm">
                <tr><td>Subtotal:</td><td class="text-end">Rs. {{ number_format($sale->subtotal, 2) }}</td></tr>
                @if($sale->discount>0)<tr><td>Discount:</td><td class="text-end text-danger">-Rs. {{ number_format($sale->discount, 2) }}</td></tr>@endif
                @if($sale->tax>0)<tr><td>Tax:</td><td class="text-end">Rs. {{ number_format($sale->tax, 2) }}</td></tr>@endif
                <tr class="fw-bold"><td>Total:</td><td class="text-end h5">Rs. {{ number_format($sale->total, 2) }}</td></tr>
                <tr><td>Paid:</td><td class="text-end">Rs. {{ number_format($sale->amount_paid, 2) }}</td></tr>
                <tr><td>Change:</td><td class="text-end text-success">Rs. {{ number_format($sale->change_amount, 2) }}</td></tr>
            </table>
        </div>
    </div>
    <hr>
    <div class="footer">
        <p>Thank you for your business!</p>
        <p><small>This is a computer-generated invoice.</small></p>
    </div>
</div>
</body>
</html>
