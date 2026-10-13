@extends('layouts.app')

@section('content')
<div class="auth-layout auth-layout-compact">
    <section class="auth-brand-panel">
        <a class="auth-brand" href="{{ url('/') }}"><span>DBFM</span><small>OPERATIONAL SYSTEM</small></a>
        <div class="auth-brand-copy"><span class="auth-eyebrow">Digitalized Bookkeeping and Finance Management</span><h1>Get back to work securely.</h1><p>We’ll send a password reset link to your registered email address.</p></div>
        <div class="auth-brand-mark" aria-hidden="true"><svg viewBox="0 0 96 96" fill="none"><rect x="20" y="30" width="56" height="42" rx="8" stroke="currentColor" stroke-width="3"/><path d="m22 37 26 20 26-20M35 30v-6a13 13 0 0 1 26 0v6" stroke="currentColor" stroke-width="3"/></svg></div>
    </section>
    <section class="auth-card">
        <div class="auth-card-heading"><span class="auth-kicker">Account recovery</span><h2>Forgot password?</h2><p>Enter your registered email and we’ll send you a secure reset link.</p></div>
        <a class="auth-back-link" href="{{ route('login') }}">← {{ __('Back to Login') }}</a>
        @if (session('status'))<div class="alert alert-success auth-alert" role="alert">{{ session('status') }}</div>@endif
        <form method="POST" action="{{ route('password.email') }}" class="auth-form">
            @csrf
            <div class="auth-field"><label for="email">{{ __('Email Address') }}</label><input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="you@company.com">@error('email')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror</div>
            <button type="submit" class="btn btn-primary auth-submit">{{ __('Send Password Reset Link') }}<span aria-hidden="true">→</span></button>
        </form>
    </section>
</div>
@endsection
