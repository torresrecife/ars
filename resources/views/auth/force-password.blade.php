<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nova senha</title>
    <script type="text/javascript" src="{{ url('js/jquery-1.8.0.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('js/jquery-ui-1.8.23.custom.min.js') }}"></script>
    <link rel="stylesheet" href="{{ url('css/template.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ url('css/custom-theme/jquery-ui-1.8.23.custom.css') }}">
    <style type="text/css">
    #dialog-new-pass table { width: 100%; }
    #dialog-new-pass td { padding: 4px 6px; vertical-align: middle; }
    #dialog-new-pass .cls_usu { width: 180px; }
    </style>
</head>
<body>
<script>
$(function(){
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    if(csrfToken){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
    }
});

function new_pass(){
    $("#dialog-new-pass").dialog({
        title: "Nova senha",
        modal: true,
        autoOpen: true,
        height: 240,
        width: 360,
        close: function(){
            location.href = "{{ url('login') }}";
        },
        buttons: {
            Salvar: function() {
                if($("#senha_usu1").val() != $("#senha_usu2").val()){
                    alert("As senhas nao conferem!");
                }else if($("#senha_usu1").val() == ""){
                    alert("Preencha o campo senha!");
                }else{
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url : "{{ url('ajax/newpass') }}",
                        data: "flag=U&id_usu={{ $userId }}&senha_usu1=" + $("#senha_usu1").val(),
                        success: function(response){
                            if(!response || response.ok!==true){
                                alert((response && response.message) ? response.message : "Nao foi possivel alterar a senha.");
                                return;
                            }
                            $("<div></div>").html("<br><table align='center'><tr><td>Senha alterada com sucesso!</td></tr></table>").dialog({
                                modal: true,
                                autoOpen: true,
                                buttons: {
                                    "Fechar": function() {
                                        $(this).dialog("close");
                                        location.href = "{{ url('index') }}";
                                    }
                                },
                                title: "Alerta"
                            });
                        },
                        error: function(xhr){
                            if(xhr && xhr.responseJSON && xhr.responseJSON.message){
                                alert(xhr.responseJSON.message);
                                return;
                            }
                            alert("Nao foi possivel alterar a senha.");
                        }
                    });
                }
            },
            Sair: function() {
                $(this).dialog("close");
            }
        }
    });
}
$(function(){ new_pass(); });
</script>

<div id="dialog-new-pass" title="Nova senha" style="display:none; text-align:left;">
    <p class="validateTips">Alteracao de senha obrigatoria!</p>
    <fieldset>
        <table>
            <tr>
                <td><label>Nova senha</label></td>
                <td><input type="password" class="cls_usu" name="senha_usu1" id="senha_usu1" value="" /></td>
            </tr>
            <tr>
                <td><label>Repete a senha</label></td>
                <td><input type="password" class="cls_usu" name="senha_usu2" id="senha_usu2" value="" /></td>
            </tr>
        </table>
    </fieldset>
</div>
</body>
</html>
