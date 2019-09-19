    <form class="form-signin needs-validation text-center" name="frmRegs" id="frmRegs" novalidate oninput="txtSenha.setCustomValidity(txtSenha.value != txtSenha1.value ? 'Confirmação de senha inválida' : '')">
		<?= $this->Html->image("logo.png",['alt' => 'Elucida','class' => 'mb-4']) ?>
        <h1 class="h3 mb-3 font-weight-normal">Mudan&ccedil;a de senha</h1>
		<label>Realize a troca da sua senha <?=$usuario->username;?></label>
        <input type="password" id="txtSenha1" name="txtSenha1" class="form-control" placeholder="Senha" required autofocus>
        <input type="password" id="txtSenha" name="txtSenha" class="form-control" placeholder="Confirmar Senha">
        <button class="btn bnt-lg btn-success btn-block" type="submit">Mudar senha e Acessar</button>
    </form>
<script>
$(document).ready(function(){
    $("#txtSenha").focus();
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