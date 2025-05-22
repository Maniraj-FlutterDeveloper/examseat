@extends('layouts.mobile')

@section('title', 'Login')

@section('custom-css')
.login-container {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
}

.login-logo {
    text-align: center;
    margin-bottom: 30px;
}

.login-logo img {
    max-width: 150px;
    height: auto;
}

.login-title {
    text-align: center;
    color: var(--primary-color);
    font-weight: bold;
    margin-bottom: 30px;
}

.login-form {
    background-color: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.form-floating {
    margin-bottom: 20px;
}

.btn-login {
    width: 100%;
    padding: 12px;
    font-weight: bold;
    margin-top: 10px;
}

.login-footer {
    text-align: center;
    margin-top: 20px;
    font-size: 0.9rem;
    color: #6c757d;
}
@endsection

@section('content')
<div class="login-container">
    <div class="login-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
    </div>
    
    <h2 class="login-title">Student Portal</h2>
    
    <div class="login-form">
        <form method="POST" action="{{ route('mobile.login.submit') }}">
            @csrf
            
            <div class="form-floating">
                <input type="text" class="form-control @error('roll_number') is-invalid @enderror" id="roll_number" name="roll_number" placeholder="Roll Number" value="{{ old('roll_number') }}" required autofocus>
                <label for="roll_number">Roll Number</label>
                @error('roll_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="form-floating">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                <label for="password">Password</label>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    Remember Me
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-login">
                <i class="fas fa-sign-in-alt me-2"></i> Login
            </button>
        </form>
    </div>
    
    <div class="login-footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Exam Seat Management System') }}. All rights reserved.</p>
    </div>
</div>
@endsection

