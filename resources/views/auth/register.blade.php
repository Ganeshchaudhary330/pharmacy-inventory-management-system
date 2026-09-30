<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Pharmacy Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);height:100vh;display:flex;align-items:center;justify-content:center;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}.login-card{background:#fff;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.3);padding:2.5rem;width:100%;max-width:420px}.login-card .brand{text-align:center;margin-bottom:2rem}.login-card .brand h3{font-weight:700;color:#1a1a2e}.login-card .brand p{color:#858796}.login-card .brand i{font-size:3rem;color:#4e73df}.form-control{border-radius:10px;padding:0.75rem 1rem;border:2px solid #e3e6f0}.form-control:focus{border-color:#4e73df;box-shadow:none}.btn-login{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border:none;border-radius:10px;padding:0.75rem;font-weight:600;color:#fff;width:100%;transition:transform 0.3s}.btn-login:hover{transform:translateY(-2px);color:#fff}.input-group-text{border:2px solid #e3e6f0;border-radius:10px 0 0 10px;background:#f8f9fc}</style>
</head>
<body>
<div class="login-card">
    <div class="brand">
        <i class="fas fa-user-plus mb-3"></i>
        <h3>Create Account</h3>
        <p>Pharmacy Management System</p>
    </div>
    @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
    <form method="POST" action="{{ route('register.submit') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-bold">Full Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Enter your full name">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="Enter your email">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control" required placeholder="Enter your password" minlength="8">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password_confirmation" class="form-control" required placeholder="Confirm your password">
            </div>
        </div>
        <button type="submit" class="btn btn-login"><i class="fas fa-user-plus me-2"></i>Sign Up</button>
    </form>
    <div class="text-center mt-3">
        <small>Already have an account? <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Sign In</a></small>
    </div>
</div>
</body>
</html>
