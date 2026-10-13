@extends('layouts.app')

@section('content')
<div class="auth-layout">
    <section class="auth-brand-panel">
        <a class="auth-brand" href="{{ url('/') }}"><span>DBFM</span><small>OPERATIONAL SYSTEM</small></a>
        <div class="auth-brand-copy">
            <span class="auth-eyebrow">Digitalized Bookkeeping and Finance Management</span>
            <h1>One clear view of your operations.</h1>
            <p>Integrated operations, inventory, production and financial management for confident daily decisions.</p>
        </div>
        <div class="auth-brand-mark" aria-hidden="true"><svg viewBox="0 0 96 96" fill="none"><path d="M48 10 78 27v34L48 78 18 61V27L48 10Z" stroke="currentColor" stroke-width="3"/><path d="m18 27 30 17 30-17M48 44v34" stroke="currentColor" stroke-width="3"/></svg></div>
    </section>
    <section class="auth-card">
        <div class="auth-card-heading"><span class="auth-kicker">Secure workspace</span><h2>Welcome back</h2><p>Sign in to continue to your DBFM dashboard.</p></div>
        @if(session('status'))<div class="alert alert-success auth-alert" role="alert">{{ session('status') }}</div>@endif
        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf
            <div class="auth-field"><label for="email">{{ __('Email Address') }}</label><input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="you@company.com">@error('email')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror</div>
            <div class="auth-field"><div class="d-flex justify-content-between align-items-center"><label for="password">{{ __('Password') }}</label>@if (Route::has('password.request'))<a class="auth-inline-link" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>@endif</div><input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter your password">@error('password')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror</div>
            <div class="auth-options"><div class="form-check"><input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}><label class="form-check-label" for="remember">{{ __('Remember Me') }}</label></div></div>
            <button type="submit" class="btn btn-primary auth-submit">{{ __('Login') }}<span aria-hidden="true">→</span></button>
        </form>
        <p class="auth-footer-note">Authorized DBFM users only.</p>
    </section>
</div>
@endsection
