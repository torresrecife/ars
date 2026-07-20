<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Controle da ARS</title>
    <link href="{{ url('css/images/favicon.ico') }}" rel="shortcut icon" type="image/vnd.microsoft.icon" />
    <link rel="stylesheet" href="{{ url('css/system.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ url('css/template.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ url('css/ars-modern.css') }}" type="text/css" />
</head>
<body>
    <div id="border-top" class="h_blue">
        <span class="logo"><img src="{{ $legacyBaseUrl }}/css/images/logo.png" alt="Sistema de Peticao" /></span>
        <span class="title"><a href="#">ARS - NEO JURIDICO</a></span>
    </div>
    <div id="content-box">
        <div id="element-box" class="login">
            <div class="m wbg">
                <h1>Acessar o Painel da ARS</h1>
                <div id="system-message-container"></div>
                <div id="section-box">
                    <div class="m">
                        <form action="{{ url('login') }}" method="post" id="form-login">
                            @csrf
                            <fieldset class="loginform">
                                <label id="mod-login-username-lbl" for="mod-login-username">Nome de Usuario</label>
                                <input type="text" name="username" id="mod-login-username" class="inputbox" size="15" />
                                <label id="mod-login-password-lbl" for="mod-login-password">Senha</label>
                                <input type="password" name="passwd" id="mod-login-password" class="inputbox" size="15" />
                                <div class="button-holder">
                                    <div class="button1">
                                        <div class="next">
                                            <a href="#" onclick="document.getElementById('form-login').submit(); return false;">Acessar</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="clr"></div>
                                <input type="submit" class="hidebtn" value="Acessar" />
                            </fieldset>
                        </form>
                        <div id="alerta" class="login-alert {{ $alerta === 1 ? '' : 'is-hidden' }}">Usuario ou senha invalidos!</div>
                        <div class="clr"></div>
                    </div>
                </div>
                <p>Use um nome de usuario e senha validos para acessar o Painel de Administracao.</p>
                <div id="lock"></div>
            </div>
        </div>
        <noscript>
            Atencao! JavaScript deve estar habilitado para o bom funcionamento do backend do administrador.
        </noscript>
    </div>
    <div id="footer">
        <p class="copyright"><a href="#">BVAA</a> - Desenvolvido por: @TTorres.</p>
    </div>
</body>
</html>
