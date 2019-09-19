<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
	<input type="hidden" id="txtIdFlag" name="txtIdFlag" value="<?php if(isset($bandeira)){ echo $bandeira->IDBANDEIRA; }?>"/>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Bandeiras de Cart&otilde;es
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/financial/card_flag');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="txtFlagName">Nome da Bandeira de Cart&atilde;o</label>
				<input type="text" class="form-control text-uppercase form-control-sm" id="txtFlagName" name="txtFlagName" value="<?php if(isset($bandeira)){ echo $bandeira->NOME; }?>" required />
			</div>
			<div class="form-group">
				<label for="txtFlagIcon">&Iacute;cone da Bandeira</label>
				<input type="text" placeholder="/cards/[nome_do_icone]" class="form-control text-lowercase form-control-sm" id="txtFlagIcon" name="txtFlagIcon" value="<?php if(isset($bandeira)){ echo $bandeira->ICONE; }?>" required />
			</div>
			<div class="form-group">
				<label for="cbMeio">Meio de pagamento</label>
				<select class="form-control text-uppercase form-control-sm" id="cbMeio" name="cbMeio" required>
					<option value="">&laquo; Selecione &raquo;</option>
					<?php foreach($meios as $meio):?>
						<option value="<?php echo $meio->IDMEIOPAGAMENTO?>"<?php if(isset($bandeira)){ if($bandeira->IDMEIOPAGAMENTO==$meio->IDMEIOPAGAMENTO){ echo " selected"; } } ?>><?php echo $meio->NOME; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group text-right">
				<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
			</div>
		</div>
	</div>
</form>
</div>
<script>
$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();

	var frmData = {
		IDBANDEIRA      : $("#txtIdFlag").val(),
		IDMEIOPAGAMENTO : $("#cbMeio").val(),
		NOME            : $("#txtFlagName").val(),
		ICONE           : $("#txtFlagIcon").val()
	};

	$.ajax({
		headers : {
			'X-CSRF-Token': csrf
		},
		type: 'POST',
		url: '<?=$this->Url->build("/financial/card_flag_save")?>',
		data: frmData,
		dataType: 'json',
		success: function(data){
			if(data==true){
				bootbox.alert("Bandeira de cart&atilde;o salva com sucesso!",function(){ document.location.href='<?=$this->Url->build("/financial/card_flag")?>'; });
			}else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es da Bandeira de cart&&atilde;o!");
				clearFields();
			}
		}
	});
});
	
function clearFields(){
	$("#frmRegs").removeClass("was-validated");
    $("#txtIdFlag").val("");
    $("#cbMeio").val("");
    $("#txtFlagName").val("");
    $("#txtFlagIcon").val("");
    $("#txtFlagName").focus();
}
</script>