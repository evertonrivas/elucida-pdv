<br/><form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
    <input type="hidden" id="txtIdTemplate" name="txtIdTemplate" value="<?php if(isset($template)){ echo $template->IDTEMPLATEEMAIL; }?>"/>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Modelo de E-mail
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/system/template');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="txtTemplateNome">Nome do Modelo</label>
				<input type="text" class="form-control text-uppercase form-control-sm" id="txtTemplateNome" name="txtTemplateNome" value="<?php if(isset($template)){ echo $template->NOME; }?>" autocomplete="off" required />
			</div>
			<div class="form-group">
				<label for="txtTemplateAssunto">Assunto do E-mail</label>
				<input type="text" class="form-control form-control-sm" id="txtTemplateAssunto" name="txtTemplateAssunto" value="<?php if(isset($template)){ echo $template->ASSUNTO; }?>" autocomplete="off"/>
			</div>
			<div class="form-group">
				<textarea name="txtHtmlContent" id="txtHtmlContent" rows="10" cols="80" required><?php if(isset($template)){ echo $template->HTML; } ?></textarea>
				<script>
					// instance, using default configuration.
					CKEDITOR.replace( 'txtHtmlContent' );
				</script>
			</div>
			<div class="form-group text-right">
				<button type="submit" class="btn btn-primary btn-sm" id="btnSave"><i class="fas fa-hdd"></i> Salvar</button>
			</div>
		</div>
	</div>
</form>
<script>  
$(document).on("submit","#frmRegs",function(evt){
	evt.preventDefault();

	var frmData = {
		IDTEMPLATEEMAIL : $("#txtIdTemplate").val(),
		NOME            : $("#txtTemplateNome").val(),
		ASSUNTO         : $("#txtTemplateAssunto").val(),
		HTML            : CKEDITOR.instances.txtHtmlContent.getData()
	};

	$.ajax({
		headers: {
			'X-CSRF-Token': csrf
		},
		type: 'POST',
		url: '<?=$this->Url->build("/system/template_save")?>',
		data: frmData,
		dataType: 'json',
		success: function(data){
			if(data==true){
				bootbox.alert("Modelo de e-mail salvo com sucesso!",function(){ document.location.href='<?=$this->Url->build("/system/template/")?>'; });
			}else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es do modelo de e-mail!");
				clearScreen();
			}
		}
	});
});

function clearScreen()
{
    $("#txtIdTemplate").val("");
    $("#txtTemplateNome").val("");
    $("#txtHtmlContent").val("");
    $("#txtTemplateNome").focus();
    tinyMCE.activeEditor.setContent('');
    $("#frmCadTemplate").formValidation('resetForm',true);
}
</script>