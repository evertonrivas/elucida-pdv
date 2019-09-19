<br/>
<form id="frmRegs" name="frmRegs">
	<input type="hidden" id="txtIdProduto" name="txtIdProduto" value=""/>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col">
					<i class="fas fa-angle-right"></i> Consulta de Estoque
				</div>
				<div class="col text-right">
					<button type="button" id="btnResetConsult" name="btnResetConsult" disabled="" class="btn btn-sm btn-primary"><i class="fas fa-backspace"></i> Limpar busca</button>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col form-group">
					<label for="txtBarCode">Produto (C&oacute;digo de Barras)</label>
					<div class="input-group input-group-sm">
	                    <input type="text" id="txtBarCode" name="txtBarCode" class="form-control text-right" value="" autocomplete="off" required/>
	                    <span class="input-group-append">
	                        <button class="btn btn-outline-secondary" type="button" id="btnFindProduct" name="btnFindProduct" data-toggle="modal" data-target="#modalProductSingle" data-backdrop="static"><i class="fas fa-cubes"></i></button>
	                    </span>
	                </div>
				</div>
				<div class="col form-gropu">
					<label for="txtNomeProduto">Nome</label>
					<input type="text" class="form-control form-control-sm" disabled="" id="txtNomeProduto" name="txtNomeProduto">
				</div>
			</div>
			<div id="tblResult">
				
			</div>
		</div>
	</div>
</form>

<?php $this->Dialog->product_single(); ?>

<script>
function useProduct(obj){
	$("#txtBarCode").val(obj.CODIGO_BARRA);
	$("#txtNomeProduto").val(obj.NOME);
	
	$.ajax({
		headers:{
			'X-CSRF-Token':csrf
		},
		type   : 'post',
		data   : { IDPRODUTO: obj.IDPRODUTO },
		url    :'<?=$this->Url->build("/stock/consult_data")?>',
		success: function(data){
			$("#tblResult").html(data);
			
			$("#btnResetConsult").removeAttr("disabled");
		}
	});
}

$(document).on("click","#btnResetConsult",function(){
	$("#txtBarCode").val("");
	$("#txtNomeProduto").val("");
	$(this).attr("disabled","disabled");
	$("#tblResult").html("");
});
</script>