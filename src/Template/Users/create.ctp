<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
    <input type="hidden" id="txtIdUser" name="txtIdUser" value="<?php if(isset($usuario)){ echo $usuario->id; }?>"/>
    <div class="card">
        <div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Usu&aacute;rio
				</div>
				<div class="col-sm text-right">
					<?php if(!$is_modal):?>
					<a href="<?php echo $this->Url->build('/users');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
					<?php endif; ?>
				</div>
			</div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="control-label">Regra de Acesso</label>
                <select class="form-control form-control-sm" id="cbUserRole" name="cbUserRole" required<?php if($user['role']!="admin"){ echo " disabled="; }?>>
                    <option value="">&laquo; Selecione &raquo;</option>
                    <option value="admin"<?php if(isset($usuario)){ if($usuario->role   =="admin")  { echo " selected"; } } ?>>Administrador</option>
                    <option value="manager"<?php if(isset($usuario)){ if($usuario->role =="manager"){ echo " selected"; } } ?>>Gerente</option>
                    <option value="seller"<?php if(isset($usuario)){ if($usuario->role  =="seller") { echo " selected"; } }else{ if($user['role']!="admin"){ echo " selected"; } } ?>>Vendedor</option>
                </select>
            </div>
            <div class="form-group">
            	<label for="cbUserStore">Loja</label>
            	<select class="form-control form-control-sm" id="cbUserStore" name="cbUserStore"<?php if($user['role']!="admin"){ echo " disabled"; }?>>
            		<option value="">&laquo; Selecione &raquo</option>
            		<?php foreach($storelist as $store):?>
            		<option value="<?=$store->IDLOJA;?>"<?php if(isset($usuario)){ if($store->IDLOJA==$usuario->storeid){ echo " selected"; } }else{ if($user['role']!="admin"){ if($store->IDLOJA==$user['storeid']){ echo " selected"; } } }?>><?=$store->NOME;?></option>
            		<?php endforeach; ?>
            	</select>
            </div>
            <div class="form-group">
                <label class="control-label">Nome do Usu&aacute;rio</label>
                <input type="text" class="form-control form-control-sm" id="txtUserNome" name="txtUserNome" value="<?php if(isset($usuario)){ echo $usuario->name; }?>" autocomplete="off" required />
            </div>
            <div class="form-group">
                <label class="control-label">Login (e-mail)</label>
                <input type="email" class="form-control form-control-sm text-lowercase" id="txtUserLogin" name="txtUserLogin" value="<?php if(isset($usuario)){ echo $usuario->username; }?>" autocomplete="off" required />
            </div>
            <div class="form-group">
                <label class="control-label">Senha</label>
                <input type="password" class="form-control form-control-sm" id="txtUserPassword" name="txtUserPassword" value="" autocomplete="off" required />
            </div>
            <div class="form-group">
                <label class="control-label">Confirmar Senha</label>
                <input type="password" class="form-control form-control-sm" id="txtUserPasswordConfirm" name="txtUserPasswordConfirm" value="" autocomplete="off" required />
            </div>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><i class="fas fa-hdd"></i> Salvar<?php if(!isset($usuario)):?> e notificar usu&aacute;rio<?php endif;?></button>
            </div>
        </div>
    </div>
</form><br/>

<script>

$(document).on("submit","#frmRegs",function(event){
	
	event.preventDefault();
	
	var frmData = {
		IDUSUARIO : $("#txtIdUser").val(),
		NOME      : $("#txtUserNome").val(),
		LOGIN     : $("#txtUserLogin").val(),
		REGRA     : $("#cbUserRole").val(),
		IDLOJA    : $("#cbUserStore").val(),
		SENHA     : $("#txtUserPassword").val()
	};

	$.ajax({
		headers: {
			'X-CSRF-Token': <?= json_encode($this->request->getParam('_csrfToken')) ?>
		},
		type: 'POST',
		url: '<?=$this->Url->build("/users/data_save")?>',
		data: frmData,
		dataType: 'json',
		success: function(data){
			if(data==true){
				bootbox.alert("Usu&aacute;rio salvo com sucesso!",function(){ <?php if(!$is_modal):?>document.location.href='<?=$this->Url->build("/users/")?>';<?php else: ?>window.parent.closeUserModal();<?php endif; ?> });
			}else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es do usu&aacute;rio!",function(){ clearFields(); });
			}
		}
	});
});

function clearFields(){
	$("#frmRegs").removeClass("was-validated");
	
    $("#txtIdUser").val("");
    $("#cbUserRole").val("");
    $("#txtUserNome").val("");
    $("#txtUserLogin").val("");
    $("#txtUserPassword").val("");
    $("#txtUserPasswordConfirm").val("");
	$("#cbUserRole").focus();
}
</script>