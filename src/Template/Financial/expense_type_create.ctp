<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
    <input type="hidden" id="txtIdExpenseType" name="txtIdExpenseType" value="<?php if(isset($tipodespesa)){ echo $tipodespesa->IDTIPODESPESA; }?>"/>
    <div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Tipo de Despesa
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/financial/expense_type');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="txtExpenseTypeName">Nome do Tipo de Despesa</label>
				<input type="text" class="form-control text-uppercase form-control-sm" id="txtExpenseTypeName" name="txtExpenseTypeName" value="<?php if(isset($tipodespesa)){ echo $tipodespesa->NOME; }?>" autocomplete="off" required />
			</div>
			<div class="form-group">
				<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" id="chExibeLoja"<?php if(isset($tipodespesa)){ if($tipodespesa->EXIBE_LOJA==1){ echo " checked"; } }?>>
					<label class="custom-control-label" for="chExibeLoja">Exibir para lojas</label>
				</div>
			</div>
			<div class="form-group">
				<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" id="chSalvaCalendario" name="chSalvaCalendario"<?php if(isset($tipodespesa)){ if($tipodespesa->SALVA_CALENDARIO==1){ echo " checked"; } }?>>
					<label class="custom-control-label" for="chSalvaCalendario">Exibir no calend&aacute;rio</label>
				</div>
			</div>
			<div class="form-group text-right">
				<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
			</div>
		</div>
	</div>
</form>
<script>
$(document).on("submit","#frmRegs",function(event){
	
	event.preventDefault();

	var frmData = {
		IDTIPODESPESA    : $("#txtIdExpenseType").val(),
		NOME             : $("#txtExpenseTypeName").val(),
		EXIBE_LOJA       : ($("#chExibeLoja")[0].checked)?1:0,
		SALVA_CALENDARIO : ($("#chSalvaCalendario")[0].checked)?1:0
	};

	$.ajax({
		headers : {
			'X-CSRF-Token': csrf
		},
		type: 'POST',
		url: '<?=$this->Url->build("/financial/expense_type_data_save")?>',
		data: frmData,
		dataType: 'json',
		success: function(data){
			if(data==true){
				bootbox.alert("Tipo de Despesa salva com sucesso!",function(){ document.location.href='<?=$this->Url->build("/financial/expense_type")?>'; });
			}else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es do Tipo de Despesa!");
				clearFields();
			}
		}
	});
});
	
function clearFields(){
	$("#frmRegs").removeClass("was-validated");
	
    $("#txtIdExpenseType").val("");
    $("#txtExpenseTypeName").val("");
    $("#txtExpenseTypeName").focus();
}
</script>