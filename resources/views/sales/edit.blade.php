@extends('layouts.app')
@section('title', 'Edit Sale')
@section('page-title', 'Edit Sale')

@section('content')
<div class="row g-4">
    <div class="col-md-8">
        <div class="table-container">
            <h5 class="fw-bold mb-3"><i class="fas fa-cart-plus me-2"></i>Edit Items</h5>
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <select id="medicineSelect" class="form-select">
                        <option value="">Select Medicine</option>
                        @foreach($medicines as $m)
                        <option value="{{ $m->id }}" data-name="{{ $m->name }}" data-price="{{ $m->selling_price }}" data-stock="{{ $m->stock_quantity }}">{{ $m->name }} - Rs. {{ number_format($m->selling_price, 2) }} (Stock: {{ $m->stock_quantity }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" id="quantityInput" class="form-control" value="1" min="1" placeholder="Qty">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" onclick="addItem()"><i class="fas fa-plus me-1"></i>Add</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="cartTable">
                    <thead><tr><th>Medicine</th><th>Price</th><th>Qty</th><th>Total</th><th>Action</th></tr></thead>
                    <tbody id="cartItems"></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-container">
            <h5 class="fw-bold mb-3"><i class="fas fa-receipt me-2"></i>Update Sale</h5>
            <form action="{{ route('sales.update', $sale) }}" method="POST" id="saleForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="items" id="itemsInput">

                <div class="mb-2">
                    <label class="form-label fw-bold">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $sale->customer_name) }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Customer Phone <span class="text-danger">*</span></label>
                    <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone', $sale->customer_phone) }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', $sale->customer_email) }}">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Payment Method</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="cash" {{ old('payment_method', $sale->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="card" {{ old('payment_method', $sale->payment_method) == 'card' ? 'selected' : '' }}>Card</option>
                        <option value="online" {{ old('payment_method', $sale->payment_method) == 'online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Sale Type</label>
                    <select name="sale_type" id="saleTypeSelect" class="form-select" required onchange="toggleCreditFields()">
                        <option value="cash" {{ old('sale_type', $sale->sale_type) == 'cash' ? 'selected' : '' }}>Cash Sale</option>
                        <option value="credit" {{ old('sale_type', $sale->sale_type) == 'credit' ? 'selected' : '' }}>Credit Sale</option>
                    </select>
                </div>
                <div id="creditFields" style="display: {{ old('sale_type', $sale->sale_type) == 'credit' ? 'block' : 'none' }};">
                    <div class="mb-2">
                        <label class="form-label fw-bold">Due Date</label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date', optional($sale->due_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Due Amount:</span>
                        <span id="displayDue" class="fw-bold text-danger">Rs. 0.00</span>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-1"><span>Subtotal:</span><span id="displaySubtotal" class="fw-bold">Rs. 0.00</span></div>
                <div class="row g-2 mb-2">
                    <div class="col-8">
                        <label>Discount</label>
                        <input type="number" step="0.01" name="discount" id="discountInput" class="form-control" value="{{ old('discount', $sale->discount) }}" min="0" oninput="updateSummary()">
                    </div>
                    <div class="col-4">
                        <label>Type</label>
                        <select id="discountTypeSelect" class="form-select" onchange="updateSummary()">
                            <option value="rs">Rs.</option>
                            <option value="percent">%</option>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="discount_percent" id="discountPercentInput" value="0">
                <div class="mb-2">
                    <label>Tax (Rs.)</label>
                    <input type="number" step="0.01" name="tax" id="taxInput" class="form-control" value="{{ old('tax', $sale->tax) }}" min="0" oninput="updateSummary()">
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-1"><span>Total:</span><span id="displayTotal" class="fw-bold h5">Rs. 0.00</span></div>
                <div class="mb-2">
                    <label>Amount Paid</label>
                    <input type="number" step="0.01" name="amount_paid" id="amountPaidInput" class="form-control" value="{{ old('amount_paid', $sale->amount_paid) }}" min="0" oninput="updateSummary()">
                </div>
                <div class="d-flex justify-content-between mb-3"><span>Change:</span><span id="displayChange" class="fw-bold text-success">Rs. 0.00</span></div>
                <div class="mb-2">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $sale->notes) }}</textarea>
                </div>
                <button type="submit" class="btn btn-success w-100" onclick="return submitSale()"><i class="fas fa-check me-1"></i>Update Sale</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let cart = @json($saleItems);

function addItem() {
    const select = document.getElementById('medicineSelect');
    const qty = parseInt(document.getElementById('quantityInput').value) || 1;
    if (!select.value) { alert('Please select a medicine'); return; }

    const option = select.options[select.selectedIndex];
    const id = parseInt(select.value);
    const name = option.dataset.name;
    const price = parseFloat(option.dataset.price);
    const stock = parseInt(option.dataset.stock);

    const existing = cart.find(i => i.medicine_id === id);
    const totalQty = existing ? existing.quantity + qty : qty;
    if (totalQty > stock) { alert('Insufficient stock! Available: ' + stock); return; }

    if (existing) {
        existing.quantity = totalQty;
    } else {
        cart.push({ medicine_id: id, name, unit_price: price, quantity: qty });
    }

    renderCart();
    select.value = '';
    document.getElementById('quantityInput').value = 1;
}

function removeItem(index) {
    cart.splice(index, 1);
    renderCart();
}

function renderCart() {
    const tbody = document.getElementById('cartItems');
    tbody.innerHTML = '';
    let subtotal = 0;

    cart.forEach((item, i) => {
        const total = item.unit_price * item.quantity;
        subtotal += total;
        tbody.innerHTML += `<tr>
            <td>${item.name}</td>
            <td>Rs. ${item.unit_price.toFixed(2)}</td>
            <td><input type="number" class="form-control form-control-sm" min="1" value="${item.quantity}" onchange="updateItemQty(${i}, this.value)"></td>
            <td>Rs. ${total.toFixed(2)}</td>
            <td><button class="btn btn-sm btn-danger" onclick="removeItem(${i})"><i class="fas fa-trash"></i></button></td>
        </tr>`;
    });

    if (cart.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No items added</td></tr>';
    }

    document.getElementById('itemsInput').value = JSON.stringify(cart.map(i => ({ medicine_id: i.medicine_id, quantity: i.quantity })));
    window.cartSubtotal = subtotal;
    updateSummary();
}

function updateItemQty(index, qty) {
    const newQty = parseInt(qty) || 1;
    if (newQty <= 0) {
        cart[index].quantity = 1;
    } else {
        cart[index].quantity = newQty;
    }
    renderCart();
}

function toggleCreditFields() {
    const saleType = document.getElementById('saleTypeSelect').value;
    document.getElementById('creditFields').style.display = saleType === 'credit' ? 'block' : 'none';
    updateSummary();
}

function updateSummary() {
    const subtotal = window.cartSubtotal || 0;
    const type = document.getElementById('discountTypeSelect').value;
    const rawDiscount = parseFloat(document.getElementById('discountInput').value) || 0;
    const percentageDiscount = type === 'percent' ? Math.min(rawDiscount, 100) : 0;
    const discountAmount = type === 'percent' ? Math.min((subtotal * percentageDiscount) / 100, subtotal) : rawDiscount;
    const tax = parseFloat(document.getElementById('taxInput').value) || 0;
    const total = subtotal - discountAmount + tax;
    const paid = parseFloat(document.getElementById('amountPaidInput').value) || 0;
    const change = Math.max(0, paid - total);
    const due = Math.max(0, total - paid);

    document.getElementById('discountPercentInput').value = type === 'percent' ? percentageDiscount : 0;
    document.getElementById('discountInput').value = type === 'percent' ? percentageDiscount.toFixed(2) : rawDiscount.toFixed(2);
    document.getElementById('displaySubtotal').textContent = 'Rs. ' + subtotal.toFixed(2);
    document.getElementById('displayTotal').textContent = 'Rs. ' + total.toFixed(2);
    document.getElementById('displayChange').textContent = 'Rs. ' + change.toFixed(2);
    document.getElementById('displayDue').textContent = 'Rs. ' + due.toFixed(2);
}

function submitSale() {
    if (cart.length === 0) { alert('Please add at least one item'); return false; }

    const type = document.getElementById('discountTypeSelect').value;
    if (type === 'percent') {
        const subtotal = window.cartSubtotal || 0;
        const rawDiscount = parseFloat(document.getElementById('discountInput').value) || 0;
        const percentageDiscount = Math.min(rawDiscount, 100);
        const convertedValue = Math.min((subtotal * percentageDiscount) / 100, subtotal);
        document.getElementById('discountInput').value = convertedValue.toFixed(2);
        document.getElementById('discountPercentInput').value = percentageDiscount.toFixed(2);
    }

    return true;
}

$(document).ready(function() {
    $('#medicineSelect').select2({ theme: 'bootstrap-5', width: '100%', placeholder: 'Search and select a medicine...', allowClear: true });
    renderCart();
    updateSummary();
    toggleCreditFields();
});
</script>
@endpush
