<?php
$enabled = true;
$ver_required = 0;
if($browser->isBrowser('Chrome','<',$min_version->chrome)):
	$enabled = false;
	$ver_required = $min_version->chrome;
elseif($browser->isBrowser('Edge','<',$min_version->edge)):
	$enabled = false;
	$ver_required = $min_version->edge;
elseif($browser->isBrowser('Internet Explorer','<',$min_version->iexplore)):
	$enabled = false;
	$ver_required = $min_version->iexplore;
elseif($browser->isBrowser('Safari','<',$min_version->safari)):
	$enabled = false;
	$ver_required = $min_version->safari;
elseif($browser->isBrowser('Firefox','<',$min_version->firefox+1)):
	$enabled = false;
	$ver_required = $min_version->firefox;
endif;
?>
    <form class="form-signin needs-validation text-center" name="frmAcesso" id="frmAcesso" novalidate>
        <?= $this->Html->image("logo.png",['alt' => 'Elucida','class' => 'mb-4']) ?>
        <!--<h1 class="h3 mb-3 font-weight-normal">Acessar sistema</h1>-->
        <label for="inputEmail" class="sr-only">E-mail</label>
        <input type="email" id="txtUsuario" name="txtUsuario" class="form-control" placeholder="Endereço de e-mail" required autofocus value="<?=$username;?>">
        <label for="inputPassword" class="sr-only">Senha</label>
        <input type="password" id="txtSenha" name="txtSenha" class="form-control" placeholder="Senha" required>
        <div class="custom-control custom-switch text-left">
		  <input type="checkbox" class="custom-control-input" id="chkRemember">
		  <label class="custom-control-label" for="chkRemember">Lembrar login</label>
		</div><br/>
        <button class="btn bnt-lg btn-success btn-block" type="submit"<?php echo (!$enabled)?" disabled":""; ?>>Acessar</button><br/>
		<a href="/users/forgot_password/">Esqueci minha senha</a>
    </form>
<script>
$(document).ready(function(){
	<?php if($username==""):?>
    $("#txtUsuario").focus();
    <?php else: ?>
    $("#txtSenha").focus();
    <?php endif; ?>
    
    <?php if(!$enabled): ?>
    bootbox.alert("Por favor atualize seu navegador para utilizar o sistema.<br> A vers&atilde;o atual &eacute;: <strong><?=$browser->toString()?></strong><br>A vers&atilde;o necess&aacute;ria &eacute: <?=$ver_required;?>");
    <?php endif;?>
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
            username : $("#txtUsuario").val(),
            password : $("#txtSenha").val(),
            remember : ($("#chkRemember")[0].checked)?1:0
        };
    
    $.ajax({
		headers: {
			'X-CSRF-Token': <?= json_encode($this->request->getParam('_csrfToken')) ?>
		},
        type: 'POST',
        url: '<?=$this->Url->build("/users/authenticate/")?>',
        data: frmData,
        dataType: 'json',
        success: function(data){
            if(data==1){
                document.location.href='<?=(($this->request->getQuery("redirect")!="")?$this->Url->build($this->request->getQuery("redirect")):$this->Url->build('/system/'))?>';
            }
            if(data==0){
				bootbox.alert("Usu&aacute;rio ou senha inv&aacute;lidos!");
			}
			if(data==2){
				bootbox.alert("Usu&aacuterio n&atilde;o encontrado!");
			}
        }
    });
});
</script>