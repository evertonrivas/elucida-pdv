<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
	<input type="hidden" id="txtIdCondition" name="txtIdCondition" value="<?php if(isset($condicao)){ echo $condicao->IDCONDICAOPAGAMENTO; }?>"/>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Condi&ccedil;&atilde;o de Pagamento
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/financial/payment_condition');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="txtConditionName">Nome da Condi&ccedil;&atilde;o de Pagamento</label>
				<input type="text" class="form-control text-uppercase form-control-sm" id="txtConditionName" name="txtConditionName" value="<?php if(isset($condicao)){ echo $condicao->NOME; }?>" required />
			</div>
			<div class="form-group">
				<label class="control-label">Parcelas</label>
				<input type="number" class="form-control form-control-sm" id="txtConditionParts" name="txtConditionParts" value="<?php if(isset($condicao)){ echo $condicao->PARCELAS; }?>" required />
			</div>
			<div class="form-group">
				<label class="control-label">Dias para Recebimento</label>
				<input type="number" class="form-control form-control-sm" id="txtConditionDays" name="txtConditionDays" value="<?php if(isset($condicao)){ echo $condicao->DIAS_RECEBIMENTO; }?>"/>
			</div>
			<div class="form-group">
				<label class="control-label">Tecla de Atalho</label>
				<input type="text" class="form-control form-control-sm" id="txtConditionShortcut" name="txtConditionShortcut" value="<?php if(isset($condicao)){ echo $condicao->ATALHO; }?>"/>
			</div>
			<div class="form-group">
				<label class="control-label">Taxa de Administra&ccedil;&atilde;o (cart&atilde;o)</label>
				<input type="text" class="form-control form-control-sm" id="txtConditionTax" name="txtConditionTax" value="<?php if(isset($condicao)){ echo $this->Number->precision($condicao->TAXA_ADM,2); }?>" required />
			</div>
			<div class="form-group">
				<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" id="chShowPDV" name="chShowPDV"<?php if(isset($condicao)){ if($condicao->EXIBIR_PDV=="1"){ echo " checked"; } }?>>
					<label class="custom-control-label" for="chShowPDV">Exibir op&ccedil;&atilde;o no fechamento da venda</label>
				</div>
			</div>
			<div class="form-group text-right">
				<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
			</div>
		</div>
	</div>
</form>
<script>
$(document).ready(function(){
    $("#txtConditionTax").mask("#,##0.00", {reverse: true});
});

$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();

	var frmData = {
		IDCONDICAOPAGAMENTO  : $("#txtIdCondition").val(),
		NOME             : $("#txtConditionName").val(),
		PARCELAS         : $("#txtConditionParts").val(),
		DIAS_RECEBIMENTO : $("#txtConditionDays").val(),
		ATALHO           : $("#txtConditionShortcut").val(),
		EXIBIR_PDV       : ($("#chShowPDV")[0].checked)?'1':0,
		TAXA_ADM         : $("#txtConditionTax").val()
	};

	$.ajax({
		headers : {
			'X-CSRF-Token': csrf
		},
		type: 'POST',
		url: '<?=$this->Url->build("/financial/payment_condition_data_save")?>',
		data: frmData,
		dataType: 'json',
		success: function(data){
			if(data==true){
				bootbox.alert("Condi&ccedil;&atilde;o de Pagamento salva com sucesso!",function(){ document.location.href='<?=$this->Url->build("/financial/payment_condition")?>'; });
			}else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es da Condi&ccedil;&atilde;o de Pagamento!");
				clearFields();
			}
		}
	});
});
	
function clearFields(){
	$("#frmRegs").removeClass("was-validated");
	
    $("#txtIdCondition").val("");
    $("#txtConditionName").val("");
    $("#txtConditionParts").val("");
    $("#txtConditionDays").val("");
    $("#txtConditionShortcut").val("");
    $("#txtConditionColor").val("");
    $("#txtConditionTax").val("");
    $("#chShowPDV")[0].checked = false;
    $("#txtConditionName").focus();
}
</script>