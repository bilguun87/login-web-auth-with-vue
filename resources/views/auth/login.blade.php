@extends('layouts.maba')

@section('content')
<div class="container maba-center">
    <div class="row maba-login">
        <div class="col-md-7 maba-login-left">
            <img src="/img/statebank-logo.png" alt="Төрийн банк" style="margin-bottom: 20px;"><br><h2>Мэдээллийн Аюулгүй Байдлын Алба</h2>
            <p>
                <!--h3>Malware</h3>
                <ul>
                    <li>Be suspicious of files in emails, websites and other places</li>
                    <li>Don’t install unauthorized software</li>
                    <li>Keep antivirus running and up to date</li>
                    <li>Contact IT/security team if you may have a malware infection</li>
                </ul-->
                <h3>Password security</h3>
                <ul>
                    <li>Always use a unique password for each online account</li>
                    <li>Passwords should be randomly generated</li>
                    <li>Passwords should contain a mix of letters, numbers and symbols</li>
                    <li>Use a password manager to generate and store strong passwords for each account</li>
                    <li>Use multi-factor authentication (MFA) when available to reduce the impact of a compromised password</li>
                </ul>
                <h3>Safe internet habits</h3>
                <ul>
                    <li>The ability to recognize suspicious and spoofed domains (like yahooo.com instead of yahoo.com)</li>
                    <li>The differences between HTTP and HTTPS and how to identify an insecure connection</li>
                    <li>The dangers of downloading untrusted or suspicious software off the internet</li>
                    <li>The risks of entering credentials or login information into untrusted or risks websites (including spoofed and phishing pages)</li>
                    <li>Watering hole attacks, drive-by downloads and other threats of browsing suspicious sites</li>
                </ul>
            </p>
        </div>
        <div class="col-md-5 maba-login-right">            
            <form method="POST" action="{{ route('login') }}" class="maba-login-form">
                @csrf
                <div class="form-group-row maba-login-right-image"><img src="/img/cyber-security-team.jpg" alt=""></div>
                <div class="form-group row" style="padding-right: 15px;">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Domain Name') }}</label>

                    <div class="col-md-8">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row" style="padding-right: 15px;">
                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                    <div class="col-md-8">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-8 offset-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-8 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Login') }}
                        </button>

                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection