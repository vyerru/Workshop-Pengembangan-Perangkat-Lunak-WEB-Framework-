@extends('layouts.auth')

@section('content')
<div class="row w-100 mx-0">
    <div class="col-lg-5 mx-auto">
        <div class="auth-form-light text-left py-5 px-4 px-sm-5 shadow-sm">
            <div class="text-center mb-4">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="logo" class="login-form-logo">
            </div>
            <h4 class="fw-light mb-20">Masuk untuk melanjutkan ke dashboard</h4>
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form class="pt-3" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email Address">

                    @error('email')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">

                    @error('password')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-check form-check-flat form-check-primary mb-4">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        {{ __('Remember Me') }}
                        <i class="input-helper"></i>
                    </label>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-gradient-primary btn-lg auth-form-btn">
                        {{ __('Login') }}
                    </button>
                    <a href="{{ route('google.redirect') }}" class="btn btn-google btn-lg auth-form-btn">
                        <i class="mdi mdi-google"></i> {{ __('Login with Google') }}
                    </a>
                </div>

                @if (Route::has('password.request'))
                <div class="text-center mt-3">
                    <a class="auth-link text-black" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection

@push('style-page')
<style>
    .auth .auth-form-light {
        border-radius: 0.5rem;
    }

    .login-form-logo {
        width: 100%;
        max-width: 170px;
        height: auto;
    }
</style>
@endpush
