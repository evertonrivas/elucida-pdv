<style>
html,body{
	height: 100%;
}
</style>
<br/>
<form id="frmRegs" name="frmRegs">
	<input type="hidden" id="txtIdProduto" name="txtIdProduto"/>
	<div class="card">
		<div class="card-header">
			<i class="fas fa-angle-right"></i> <?=$title?>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="form-group col-sm">
					<label for="txtNomeProduto">Produto (C&oacute;digo de Barras)</label>
					<div class="input-group input-group-sm">
						<input type="text" name="txtBarcode" class="form-control text-right" id="txtBarcode" autocomplete="off"/>
						<div class="input-group-append">
							<button id="btnSearchProduct" name="btnSearchProduct" type="button" class="btn btn-outline-secondary" data-toggle="tooltip" data-placement="top" title="Pesquisar produto"><i class="fas fa-cubes"></i></button>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-sm">
					<input type="text" id="txtNomeProduto" class="form-control" name="txtNomeProduto" readonly="readonly"/>
				</div>
			</div>
			<div class="row">
				<div class="col-5">
					<label for="cbStoreNo">Estoque sem o produto</label>
					<div class="card">
						<div class="card-body" style="overflow-y:scroll; min-height:230px!important;max-height:230px!important;" id="divNoEstoque" name="divNoEstoque">
							
						</div>
					</div>				
				</div>
				<div class="col-2 my-auto text-center" style="min-height: 266px!important;"><br/><br/><br/>
					<div class="btn-group-vertical" role="group">
						<button type="button" name="btnAddAll" id="btnAddAll" class="btn btn-outline-success" data-toggle="tooltip" data-placement="top" title="Adicionar Todos"><i class="fas fa-angle-double-right"></i></button>
						<!--<button type="button" name="btnAdd" id="btnAdd" class="btn btn-outline-success" data-toggle="tooltip" data-placement="left" title="Adicionar selecionado"><i class="fas fa-angle-right"></i></button>
						<button type="button" name="btnDel" id="btnDel" class="btn btn-outline-danger" data-toggle="tooltip" data-placement="right" title="Remover Selecionado"><i class="fas fa-angle-left"></i></button>-->
						<button type="button" name="btnDelAll" id="btnDelAll" class="btn btn-outline-danger" data-toggle="tooltip" data-placement="bottom" title="Remover Todos"><i class="fas fa-angle-double-left"></i></button>
					</div>
				</div>
				<div class="col-5">
					<label for="cbStoreYes">Estoque com o produto</label>
					<div class="card">
						<div class="card-body" style="overflow-y:scroll; min-height:230px!important;max-height:230px!important;" id="divYesEstoque" name="divYesEstoque">
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<?php $this->Dialog->product_single(); ?>

<!-- MODAL DE CONFIGURACAO DO ESTOQUE -->
<div class="modal" tabindex="-1" role="dialog" id="modalConfig">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Configura&ccedil;&atilde;o de Estoque</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <iframe id="frmConfigStock" name="frmConfigStock" frameborder="0" style="min-height:350px!important; max-height:350px!important;width:100%!important;"></iframe>
      </div>
    </div>
  </div>
</div>
<!-- FIM DO MODAL DE CONFIGURACAO DO ESTOQUE -->

<script>
window.closeModalConfig = function(){
    $('#modalConfig').modal('hide');
};

$(document).on("click","#btnSearchProduct",function(){
	$("#modalProductSingle").modal({
		backdrop: 'static'
	});
});

$(document).on("keydown","#txtBarCode",function(event){
    if(event.keyCode==13){
        if($("#txtBarCode").val()!=''){
            getProductInfo(true);
        }
    }
});

function useProduct(obj){
	$("#txtBarcode").val(obj.CODIGO_BARRA);
	$("#txtNomeProduto").val(obj.NOME);
	$("#txtIdProduto").val(obj.IDPRODUTO);
	
	getStockPlace(obj.IDPRODUTO);
}

function getStockPlace(idProduto){
	$.ajax({
		headers:{
			'X-CSRF-Token': csrf
		},
		method:'post',
		url:'<?=$this->Url->build("/stock/single_product_stock/")?>'+idProduto,
		dataType:'json',
		success:function(data){
			$("#divNoEstoque").html(data.NOTEXIST);
			$("#divYesEstoque").html(data.EXIST);
		}
	});
}

$(document).on("click","#btnAddAll",function(){
	
	var dataForm = {
		IDPRODUTO : $("#txtIdProduto").val(),
		STORE     : "",
		ACTION    : "ADD"
	};
	
	ajaxSend(dataForm);
});

$(document).on("click","#btnDelAll",function(){

	var dataForm = {
		IDPRODUTO : $("#txtIdProduto").val(),
		STORE     : "",
		ACTION    : "DEL"
	};

	ajaxSend(dataForm);
});

function ajaxSend(dataForm){
	
	$.ajax({
		headers : {
			'X-CSRF-Token' : csrf
		},
		method  : 'post',
		data    : dataForm,
		url     : '<?=$this->Url->build("/stock/")?>'+((dataForm.ACTION=="DEL")?'/single_product_del_stock':'/single_product_add_stock'),
		success : function(data){
			if(data){
				if(dataForm.ACTION=="ADD"){
					if(dataForm.STORE==""){
						bootbox.alert("Produto adicionado com sucesso &agrave; todos os estoques!",function(){ getStockPlace($("#txtIdProduto").val()); });
					}else{
						bootbox.alert("Produto adicionado com sucesso ao estoque selecionado!",function(){ getStockPlace($("#txtIdProduto").val()); });
					}
				}else{
					if(dataForm.STORE==""){
						bootbox.alert("Produto removido com sucesso de todos os estoques!",function(){ getStockPlace($("#txtIdProduto").val()); });
					}else{
						bootbox.alert("Produto removido com sucesso do estoque selecionado!",function(){ getStockPlace($("#txtIdProduto").val()); });
					}
				}
			}else{
				if(dataForm.ACTION=="ADD"){
					if(dataForm.STORE==""){
						bootbox.alert("Ocorreu um erro ao tentar adicionar o produto &agrave; todos os estoques!");
					}else{
						bootbox.alert("Ocorreu um erro ao tentar adicionar o produto ao estoque selecionado!");
					}
				}else{
					if(dataForm.STORE==""){
						bootbox.alert("Ocorreu um erro ao tentar remover o produto de todos os estoques!")
					}else{
						bootbox.alert("Ocorreu um erro ao tentar remover o produto do estoque selecionado!")
					}
				}
			}
		}
	});
}

function removeStock(idLoja,idProduto){
	var dataForm = {
		IDPRODUTO : idProduto,
		STORE     : idLoja,
		ACTION    : "DEL"
	};
	
	ajaxSend(dataForm);
}

function addStock(idLoja,idProduto){
	var dataForm = {
		IDPRODUTO : idProduto,
		STORE     : idLoja,
		ACTION    : "ADD"
	};
	
	ajaxSend(dataForm);
}

function configStock(idLoja,idProduto){
	$("#frmConfigStock").attr("src",'<?=$this->Url->build("/stock/single_product_config_stock/")?>'+idProduto+"/"+idLoja);
	$("#modalConfig").modal({
		backdrop: 'static'
	});
}
</script>