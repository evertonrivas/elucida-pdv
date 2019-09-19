<br/>
<form id="frmRegs" name="frmRegs">
<div class="card">
	<div class="card-header">
		<div class="row">
			<div class="col-sm">
				<i class="fas fa-angle-right"></i> Ordena&ccedil;&atilde;o de Opera&ccedil;&otilde;es Financeiras
			</div>
			<div class="col-sm text-right">
				<a href="<?php echo $this->Url->build('/financial/financial_operation');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="">Tipo de Opera&ccedil;&atilde;o</label>
			<select class="form-control form-control-sm" id="cbOperationType" name="cbOperationType">
				<option value="">&laquo; Selecione &raquo;</option>
				<option value="E">Entradas</option>
				<option value="S">Sa&iacute;das</option>
			</select>
		</div>
		<div id="dvOperations" style="min-height: 300px; max-height:300px; overflow-y: scroll; margin-bottom: 10px;">
			
		</div>
		<div class="form-group text-right">
			<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
		</div>
	</div>
</div>
</form>
<script>
$("#cbOperationType").on("change",function(){
	$.ajax({
		headers:{
			'X-CSRF-Token':csrf
		},
		type: 'POST',
		data:{ TIPO_OPERACAO: $(this).val() },
		url:'<?=$this->Url->build("/financial/financial_operation_list")?>',
		success:function(data){
			$("#dvOperations").html(data);
		}
	});
});

$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();
	
	$.ajax({
		headers:{
			'X-CSRF-Token':csrf
		},
		type:'POST',
		data: $("#frmRegs").serialize(),
		url: '<?=$this->Url->build("/financial/financial_operation_order_save")?>',
		success: function(data){
			if(data){
				bootbox.alert("Ordena&ccedil;&atilde;o de Opera&ccedil;&otilde;es Financeiras realizada com sucesso!",function(){ document.location.href='/financial/financial_operation'; });
			}else{
				bootbox.alert("Ocorreu um problema ao tentar salvar as informa&ccedil;&otilde;es de ordena&ccedil;&atilde;o das Opera&ccedil;&otilde;es Financeiras!/");
			}
		}
	});
});
</script>