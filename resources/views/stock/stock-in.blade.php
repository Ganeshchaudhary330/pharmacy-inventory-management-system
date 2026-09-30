@extends('layouts.app')
@section('title', 'Stock In')
@section('page-title', 'Add Stock In')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="table-container">
            <form action="{{ route('stock.store-in') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Medicine <span class="text-danger">*</span></label>
                    <select name="medicine_id" class="form-select @error('medicine_id') is-invalid @enderror" required>
                        <option value="">Select Medicine</option>
                        @foreach($medicines as $m)
                        <option value="{{ $m->id }}" {{ old('medicine_id')==$m->id ? 'selected' : '' }}>{{ $m->name }} (Stock: {{ $m->stock_quantity }})</option>
                        @endforeach
                    </select>
                    @error('medicine_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1" required>
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Unit Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', 0) }}" min="0" required>
                        @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i>Add Stock</button>
                    <a href="{{ route('stock.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
