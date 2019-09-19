<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
    <input type="hidden" id="txtIdFinancTrans" name="txtIdFinancTrans" value="<?php if(isset($operacao_financeira)){ echo $operacao_financeira->IDOPERACAOFINANCEIRA; }?>"/>
    <div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Opera&ccedil;&atilde;o Financeira
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/financial/financial_operation');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="txtOperationName">Nome da Opera&ccedil;&atilde;o Financeira</label>
				<input type="text" class="form-control text-uppercase form-control-sm" id="txtOperationName" name="txtOperationName" value="<?php if(isset($operacao_financeira)){ echo $operacao_financeira->NOME; }?>" required />
			</div>
			<div class="form-group">
				<label for="txtOperationType">Tipo de Opera&ccedil;&atilde;o</label>
				<select id="txtOperationType" name="txtOperationType" class="form-control form-control-sm" required >
					<option value="">&laquo; Selecione &raquo;</option>
					<option value="I"<?php if(isset($operacao_financeira)){ if($operacao_financeira->TIPO_OPERACAO=="I"){ echo " selected"; }} ?>>Saldo Inicial</option>
					<option value="E"<?php if(isset($operacao_financeira)){ if($operacao_financeira->TIPO_OPERACAO=="E"){ echo " selected"; }} ?>>Entradas</option>
					<option value="S"<?php if(isset($operacao_financeira)){ if($operacao_financeira->TIPO_OPERACAO=="S"){ echo " selected"; }} ?>>Sa&iacute;das</option>
				</select>
			</div>
			<div class="form-group">
				<label for="txtOperationOrder">Ordem</label>
				<input type="number" class="form-control form-control-sm" name="txtOperationOrder" id="txtOperationOrder" value="<?php if(isset($operacao_financeira)){ echo $operacao_financeira->ORDEM; }?>" required/>
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
		IDOPERACAOFINANCEIRA : $("#txtIdFinancTrans").val(),
		NOME                 : $("#txtOperationName").val(),
		TIPO_OPERACAO        : $("#txtOperationType").val(),
		ORDEM                : $("#txtOperationOrder").val(),
	};

	$.ajax({
		headers : {
			'X-CSRF-Token': csrf
		},
		type: 'POST',
		url: '<?=$this->Url->build("/financial/financial_operation_save")?>',
		data: frmData,
		success: function(data){
			if(data==true){
				bootbox.alert("Condi&ccedil;&atilde;o de Pagamento salva com sucesso!",function(){ document.location.href='<?=$this->Url->build("/financial/financial_operation")?>'; });
			}else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es da Condi&ccedil;&atilde;o de Pagamento!");
				clearFields();
			}
		}
	});
});
	
function clearFields(){
	$("#frmRegs").removeClass("was-validated");
	
    $("#txtIdFinancTrans").val("");
    $("#txtOperationName").val("");
    $("#txtOperationType").val("");
    $("#txtOperationName").focus();
}
</script>