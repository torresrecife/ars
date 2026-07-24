<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'en_CA' ? 'en-ca' : 'pt-br' }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('ARS Control') }}</title>
    <link href="{{ url('css/images/favicon.ico') }}" rel="shortcut icon" type="image/vnd.microsoft.icon" />
    <link rel="stylesheet" href="{{ url('css/ars-modern.css') }}" type="text/css" />
</head>
<body class="auth-page">
    <div class="auth-layout">
        <section class="auth-layout__hero">
            <div class="auth-layout__hero-inner">
                <div class="auth-layout__brand">
                    <span class="auth-layout__brand-mark">
                        <img src="{{ asset('css/images/ars-logo-icon.png') }}" alt="{{ __('ARS - NEO Legal') }}" />
                    </span>
                    <div class="auth-layout__brand-text">
                        <strong>ARS</strong>
                        <span>{{ __('ARS - NEO Legal') }}</span>
                    </div>
                </div>
                <div class="auth-layout__hero-copy">
                    <h1>{{ __('Access the ARS Panel') }}</h1>
                    <p>{{ __('Use a valid username and password to access the Administration Panel.') }}</p>
                </div>
            </div>
        </section>
        <section class="auth-layout__panel">
            <div class="auth-card">
                <div class="auth-card__header">
                    <h2>{{ __('Sign in') }}</h2>
                    <p>{{ __('Administrative workspace') }}</p>
                </div>
                <div id="system-message-container"></div>
                <form action="{{ url('login') }}" method="post" id="form-login" class="auth-form">
                    @csrf
                    <div class="auth-form__field">
                        <label id="mod-login-username-lbl" for="mod-login-username">{{ __('Username') }}</label>
                        <input type="text" name="username" id="mod-login-username" class="auth-form__input" size="15" />
                    </div>
                    <div class="auth-form__field">
                        <label id="mod-login-password-lbl" for="mod-login-password">{{ __('Password') }}</label>
                        <input type="password" name="passwd" id="mod-login-password" class="auth-form__input" size="15" />
                    </div>
                    <div id="alerta" class="login-alert {{ $alerta === 1 ? '' : 'is-hidden' }}">{{ __('Invalid username or password!') }}</div>
                    <button type="submit" class="auth-form__submit">{{ __('Sign in') }}</button>
                    <input type="submit" class="hidebtn" value="{{ __('Sign in') }}" />
                </form>
                <noscript class="auth-card__noscript">
                    {{ __('Warning! JavaScript must be enabled for the administration backend to work properly.') }}
                </noscript>
            </div>
        </section>
    </div>
</body>
</html>
