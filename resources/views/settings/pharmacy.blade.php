@extends('layouts.app')
@section('title', 'Pharmacy Settings')
@section('page-title', 'Pharmacy Settings')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="table-container">
            <h5 classhello@example.com="fw-bold mb-3"><i class="fas fa-building me-2"></i>Pharmacy Information</h5>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('pharmacy.settings.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Pharmacy Name</label>
                    <input type="text" name="app_name" class="form-control" value="{{ old('app_name', $appName) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mail From Name</label>
                    <input type="text" name="mail_from_name" class="form-control" value="{{ old('mail_from_name', $mailFromName) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mail From Address</label>
                    <input type="email" name="mail_from_address" class="form-control" value="{{ old('mail_from_address', $mailFromAddress) }}" required>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
