<?php $this->layout = false; ?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        NeuGen - Solu&ccedil;&otilde;es Diagn&oacute;sticas
    </title>
    <?= $this->Html->meta('icon') ?>
    <?= $this->Html->css('signin.css') ?>
    <?= $this->Html->css('system.css') ?>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <?= $this->Html->script("bootbox.min.js");?>
</head>
<body class="text-center">
    <form class="form-signin needs-validation" name="frmAcesso" id="frmAcesso" novalidate oninput="txtSenha.setCustomValidity(txtSenha.value != txtSenha1.value ? 'Confirmação de senha inválida' : '')">
        <?= $this->Html->image("logo.png",['alt' => 'Elucida','class' => 'mb-4']) ?>
        <h1 class="h3 mb-3 font-weight-normal">Mudan&ccedil;a de senha</h1>
		<label>Realize a troca da sua senha <?=$usuario->username;?></label>
        <input type="password" id="txtSenha1" name="txtSenha1" class="form-control" placeholder="Senha" required autofocus>
        <input type="password" id="txtSenha" name="txtSenha" class="form-control" placeholder="Confirmar Senha">
        <button class="btn bnt-lg btn-success btn-block" type="submit">Mudar senha e Acessar</button>
    </form>
</body>
<script>
$(document).ready(function(){
    $("#txtSenha1").focus();
});

(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();

$(document).on("submit","#frmAcesso",function(event){
    event.preventDefault();
    
    var frmData = {
            IDUSUARIO : <?=$usuario->id?>,
            PASSWORD  : $("#txtSenha").val()
        };
    
    
    $.ajax({
		headers: {
			'X-CSRF-Token': <?= json_encode($this->request->getParam('_csrfToken')) ?>
		},
        type: 'POST',
        url: '<?=$this->Url->build("/users/change_password/")?>',
        data: frmData,
        dataType: 'json',
        success: function(data){
            if(data){
				bootbox.alert("Senha alterada com sucesso!",function(){ document.location.href='<?=$this->Url->build("/system/")?>'; });
            }else{
                bootbox.alert("Ocorreu um erro ao tentar alterar a senha!");
			}
        }
    });
});
</script>
</html>