@extends('layouts.app')

@section('content')
<div class="auth-layout auth-layout-compact">
    <section class="auth-brand-panel">
        <a class="auth-brand" href="{{ url('/') }}"><span>DBFM</span><small>OPERATIONAL SYSTEM</small></a>
        <div class="auth-brand-copy"><span class="auth-eyebrow">Digitalized Bookkeeping and Finance Management</span><h1>Set a fresh password.</h1><p>Choose a strong password to protect your DBFM workspace.</p></div>
        <div class="auth-brand-mark" aria-hidden="true"><svg viewBox="0 0 96 96" fill="none"><rect x="25" y="42" width="46" height="34" rx="7" stroke="currentColor" stroke-width="3"/><path d="M33 42V29a15 15 0 0 1 30 0v13M48 56v9" stroke="currentColor" stroke-width="3"/></svg></div>
    </section>
    <section class="auth-card">
        <div class="auth-card-heading"><span class="auth-kicker">New credentials</span><h2>Reset password</h2><p>Enter your email and choose a new password.</p></div>
        <a class="auth-back-link" href="{{ route('login') }}">← {{ __('Back to Login') }}</a>
        <form method="POST" action="{{ route('password.update') }}" class="auth-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="auth-field"><label for="email">{{ __('Email Address') }}</label><input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>@error('email')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror</div>
            <div class="auth-field"><label for="password">{{ __('Password') }}</label><input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Create a new password">@error('password')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror</div>
            <div class="auth-field"><label for="password-confirm">{{ __('Confirm Password') }}</label><input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your new password"></div>
            <button type="submit" class="btn btn-primary auth-submit">{{ __('Reset Password') }}<span aria-hidden="true">→</span></button>
        </form>
    </section>
</div>
@endsection
