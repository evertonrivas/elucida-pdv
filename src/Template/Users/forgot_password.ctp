    <form class="form-signin needs-validation" name="frmRegs" id="frmRegs" novalidate>
		<div class="form-group">
        <?= $this->Html->image("logo.png",['alt' => 'Elucida','class' => 'mb-4']) ?>
        <h1 class="h3 mb-3 font-weight-normal">Esqueci minha senha</h1>
        <label for="inputEmail" class="sr-only">E-mail</label>
        <input type="email" id="txtEmail" name="txtEmail" class="form-control" placeholder="Endereço de e-mail" required autofocus /><br/>
        <button class="btn bnt-lg btn-success btn-block" type="submit">Recuperar Senha</button>
		</div>
    </form>
<script>
$(document).ready(function(){
    $("#txtEmail").focus();
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

$(document).on("submit","#frmRegs",function(event){
	
    event.preventDefault();
    
    var frmData = {
            username : $("#txtEmail").val()
        };
    
    $.ajax({
		headers: {
			'X-CSRF-Token': <?= json_encode($this->request->getParam('_csrfToken')) ?>
		},
        type: 'POST',
        url: '<?=$this->Url->build("/users/recover_password/")?>',
        data: frmData,
        dataType: 'json',
        success: function(data){
            if(data==1){
                bootbox.alert("Foi enviado um link para o seu e-mail, acesse para trocar a senha da sua conta!",function(){ document.location.href='<?=$this->Url->build("/system/")?>'; });
            }
            if(data==0){
				bootbox.alert("Ocorreu um erro ao tentar enviar um link para o seu e-mail, por favor contacte o administrador do sistema!",function(){ document.location.href='<?=$this->Url->build("/system/")?>'; });
			}
			if(data==2){
				bootbox.alert("Usu&aacute;rio n&atilde;o encontrado, por favor entre em contato com o administrador do sistema!",function(){ document.location.href='<?=$this->Url->build("/system/")?>'; });
			}
        }
    });
});
</script>