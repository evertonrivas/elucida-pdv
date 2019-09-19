<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
	<input type="hidden" id="txtIdMethod" name="txtIdMethod" value="<?php if(isset($meio)){ echo $meio->IDMEIOPAGAMENTO; }?>"/>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Meio de Pagamento
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/financial/payment_option');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label class="control-label">Nome do Meio de Pagamento</label>
				<input type="text" class="form-control text-uppercase form-control-sm" id="txtMethodName" name="txtMethodName" value="<?php if(isset($meio)){ echo $meio->NOME; }?>" required />
			</div>
			<div class="form-group">
				<label class="control-label">C&oacute;digo NF-e</label>
				<input type="text" class="form-control text-uppercase form-control-sm" id="txtMethodCode" name="txtMethodCode" value="<?php if(isset($meio)){ echo $meio->CODIGO_NFE; }?>" required />
			</div>
			<div class="form-group">
				<label class="control-label">Condi&ccedil;&otilde;es de pagamento</label>
				<?php foreach($condicoes as $condicao):?>
					<div class="form-check form-control-sm">
						<label>
							<input type="checkbox" class="form-check-input" id="chCondicao[]" name="chCondicao[]" value="<?php echo $condicao->IDCONDICAOPAGAMENTO;?>"
								<?php if(isset($meio_condicoes)){ 
									foreach($meio_condicoes as $cnd){
										if($cnd->IDCONDICAOPAGAMENTO==$condicao->IDCONDICAOPAGAMENTO){ echo " checked"; }
									}
								}
								?>><label class="form-check-label"> <?php echo $condicao->NOME; ?></label>
						</label>
					</div>
				<?php endforeach; ?>
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
		IDMEIOPAGAMENTO : $("#txtIdMethod").val(),
		NOME            : $("#txtMethodName").val(),
		CODIGO_NFE      : $("#txtMethodCode").val(),
		CONDICOES       : $("input[name*=chCondicao]").serialize()
	};

	$.ajax({
		headers : {
			'X-CSRF-Token': csrf
		},
		type: 'POST',
		url: '<?=$this->Url->build("/financial/payment_method_data_save")?>',
		data: frmData,
		dataType: 'json',
		success: function(data){
			if(data==true){
				bootbox.alert("Meio de Pagamento salvo com sucesso!",function(){ document.location.href='<?=$this->Url->build("/financial/payment_option")?>'; });
			}else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es da Condi&ccedil;&atilde;o de Pagamento!");
				clearFields();
			}
		}
	});
});
	
function clearFields(){
	$("#frmRegs").removeClass("was-validated");
	
    $("#txtIdMethod").val("");
    $("#txtMethodName").val("");
    $("#txtMethodCode").val("");
    $("input[name*=chCondicao]").each(function(){
        $(this)[0].checked = false;
    });
    $("#txtMethodName").focus();
}
</script>