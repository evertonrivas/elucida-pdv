<form id="frmRegs" name="frmRegs" class="needs-validation" novalidate>
<div class="card">
	<div class="card-header">
		<i class="fas fa-angle-right"></i> Configura&ccedil;&atilde;o de Estoque
	</div>
	<div class="card-body">
		<div class="row">
			<div class="form-group col-sm">
				<label for="txtNomeLoja">Loja do estoque</label>
				<input type="text" id="txtNomeLoja" name="txtNomeLoja" class="form-control-plaintext font-weight-bold" value="<?=$store->NOME;?>"/>
			</div>
			<div class="form-group col-sm">
				<label for="txtNomeProduto">Produto</label>
				<input type="text" id="txtNomeProduto" name="txtNomeProduto" class="form-control-plaintext font-weight-bold" value="<?=$product->NOME;?>"/>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm">
				<label for="">Quantidade M&iacute;nima</label>
				<input type="number" class="form-control form-control-sm" id="txtQtdeMin" name="txtQtdeMin" value="<?=$stock->QTDE_MIN;?>" required>
			</div>
			<div class="form-group col-sm">
				<label for="">Ponto de Pedido</label>
				<input type="number" class="form-control form-control-sm" id="txtQtdeBuy" name="txtQtdeBuy" value="<?=$stock->QTDE_BUY;?>" required>
			</div>
			<div class="form-group col-sm">
				<label for="">Quantidade M&aacute;xima</label>
				<input type="number" class="form-control form-control-sm" id="txtQtdeMax" name="txtQtdeMax" value="<?=$stock->QTDE_MAX;?>" required>
			</div>
			<div class="form-group col-sm">
				<label for="">Calcular Reposi&ccedil;&atilde;o</label>
				<select class="form-control form-control-sm" id="cbControlRepo" name="cbControlRepo" required>
					<option value="0"<?php if($stock->CALCULA_REPOSICAO==0){ echo " Selected"; }?>>N&atilde;o</option>
					<option value="1"<?php if($stock->CALCULA_REPOSICAO==1){ echo " Selected"; }?>>Sim</option>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm text-right">
				<button type="submit" id="btnSend" name="btnSend" class="btn btn-primary btn-sm">Salvar e Fechar</button>
			</div>
		</div>
	</div>
</div>
</form>

<script>
$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();
	
	var dataForm = {
        IDPRODUTO : <?=$product->IDPRODUTO; ?>,
        IDLOJA    : <?=$store->IDLOJA; ?>,
        QTDE_MIN  : $("#txtQtdeMin").val(), 
        QTDE_BUY  : $("#txtQtdeBuy").val(),
        QTDE_MAX  : $("#txtQtdeMax").val(),
        CALCULA_REPOSICAO : $("#cbControlRepo").val()
    };

	$.ajax({
		headers : {
			'X-CSRF-Token' : csrf
		},
		method  : 'post',
		data    : dataForm,
		url     : '<?=$this->Url->build("/stock/single_product_stock_save")?>',
		success : function(data){
			if(data){
				bootbox.alert("Estoque do produto configurado com sucesso!",function(){ window.parent.closeModalConfig(); });
			}else{
				bootbox.alert(" Ocorreu um erro ao tentar configurar o estoque do produto!");
			}
		}
	});
});
</script>