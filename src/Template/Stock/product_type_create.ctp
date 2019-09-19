<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
	<input type="hidden" name="txtIdTipoProduto" id="txtIdTipoProduto" value="<?php if(isset($tipo_produto)){ echo $tipo_produto->IDPRODUTOTIPO; }?>"/>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Tipo de Produto
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/stock/product_type');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="txtTipoProdutoDescricao">Descri&ccedil;&atilde;o</label>
				<input type="text" id="txtTipoProdutoDescricao" name="txtTipoProdutoDescricao" class="form-control text-uppercase form-control-sm" value="<?php if(isset($tipo_produto)){ echo $tipo_produto->DESCRICAO; }?>" required>
			</div>
			<div class="form-group">
				<label>Atributos</label>
				<div class="card">
					<div class="card-body" style="min-height:200px!important; max-height: 200px; overflow-y: scroll;" id="basket">
						
					</div>
					<div class="card-footer text-right">
						<button type="button" class="btn btn-success btn-sm" id="btnAddAttribute" name="btnAddAttribute" data-toggle="modal" data-target="#modalAtributo" data-backdrop="static" <?php if(!isset($tipo_produto)){ echo "disabled"; } ?>><i class="fas fa-plus-square"></i> Adicionar</button>
					</div>
				</div>
			</div>
			<div class="form-group text-right">
				<button type="submit" id="btnSave" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
			</div>
		</div>
	</div>
</form>

<div class="modal fade" tabindex="-1" role="dialog" id="modalAtributo">
	<form id="frmAttr" name="frmAttr">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Adicionar/Editar Atributo de Tipo de Produto</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" id="txtIdAtributo" name="txtIdAtributo" value="">
				<div class="form-group">
					<label for="txtAttrName">Nome do Atributo</label>
					<input type="text" id="txtAttrName" name="txtAttrName" value="" class="form-control">
				</div>
				<div class="form-group">
					<label for="txtAttrValues">Valores de Atributo</label>
					<textarea name="txtAttrValues" id="txtAttrValues" class="form-control" rows="8" data-toggle="tooltip" data-placement="top" data-html="true"  title="Adicione um por linha da seguinte forma: Texto = Valor <br/>(Ex: Azul = 1)"></textarea>
				</div>
			</div>
			<div class="modal-footer text-right">
				<button type="submit" id="btnSaveAttr" name="btnSaveAttr" class="btn btn-primary btn-sm"><i class="fas fa-download"></i> Salvar e Fechar</button>
			</div>
		</div>
	</div>
	</form>
</div>

<script>
$(document).ready(function(){
	getBasket();
});

$(document).on("submit","#frmRegs",function(evt){
	evt.preventDefault();
	
	var dataForm = {
		IDPRODUTOTIPO : $("#txtIdTipoProduto").val(),
		DESCRICAO     : $("#txtTipoProdutoDescricao").val()
	};
	
	$.ajax({
		headers : {
			'X-CSRF-Token': csrf
		},
		method  : 'post',
		url     : '<?=$this->Url->build("/stock/product_type_save/")?>',
		data    : dataForm,
		dataType: 'json',
		success : function(data){
		   if(data.SALVOU){
				bootbox.alert("Tipo de produto salvo com sucesso!",function(){ 
					if($("#txtIdTipoProduto").val()==""){
						$("#txtIdTipoProduto").val(data.IDPRODUTOTIPO);
						$("#btnAddAttribute").removeAttr("disabled");
					}else{
						document.location.href='<?=$this->Url->build("/stock/product_type")?>'; 
					}
				});
			}else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es do Tipo de produto!");
			}
		}
	});
});

$(document).on("submit","#frmAttr",function(event){
	event.preventDefault();
	
	var dataForm = {
		IDATRIBUTO    : $("#txtIdAtributo").val(),
		IDPRODUTOTIPO : $("#txtIdTipoProduto").val(),
		NOME          : $("#txtAttrName").val(),
		DATA_ATRIBUTO : $("#txtAttrValues").val()
	};
	
	$.ajax({
		headers : {
			'X-CSRF-Token': csrf
		},
		method  : 'post',
		url     : '<?=$this->Url->build("/stock/attribute_save/")?>',
		data    : dataForm,
		dataType: 'json',
		success : function(data){
			if(data){
				bootbox.alert("Atributo salvo com sucesso!",function(){ 
					$('#modalAtributo').modal('hide');
					$("#txtAttrName").val("");
					$("#txtAttrValues").val("");
					$("#txtIdAtributo").val("");
					getBasket();
				});
			}else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es do Atributo!");
			}
		}
	});
	
});

function getBasket(){
	$.ajax({
		headers : {
			'X-CSRF-Token': csrf
		},
		method  : 'post',
		url     : '<?=$this->Url->build("/stock/attribute_get_basket/")?>',
		data    : { IDPRODUTOTIPO : $("#txtIdTipoProduto").val() },
		success : function(data){
			$("#basket").html(data);
		}
	});
}

function editAttribute(idAtributo){
	bootbox.confirm("<strong>Aten&ccedil;&atilde;o</strong>, ao editar o atributo poder&aacute; afetar produtos. Deseja continuar mesmo assim?",function(result){
		if(result){
			$.ajax({
				headers : {
					'X-CSRF-Token': csrf
				},
				method  : 'post',
				url     : '<?=$this->Url->build("/stock/attribute_get/")?>',
				data    : { IDATRIBUTO: idAtributo, IDPRODUTOTIPO: $("#txtIdTipoProduto").val() },
				dataType: 'json',
				success : function(data){
					$("#modalAtributo").modal({
						backdrop:'static'
					});
					$("#txtIdAtributo").val(data.IDATRIBUTO);
					$("#txtAttrName").val(data.NOME);
					$("#txtAttrValues").val(data.VALORES);
					
				}
			});
		}
	});
}

function delAttribute(idAtributo){
	bootbox.confirm("<strong>Aten&ccedil;&atilde;o</strong>, ao excluir o atributo poder&aacute; afetar alguns produtos. Deseja continuar mesmo assim?",function(result){
		if(result){
			$.ajax({
				headers:{
					'X-CSRF-Token': csrf
				},
				method: 'post',
				url:'<?=$this->Url->build("/stock/attribute_delete/")?>',
				data: { IDATRIBUTO: idAtributo },
				dataType: 'json',
				success: function(data){
					if(data){
						bootbox.alert("Atributo removido com sucesso!",function(){
							getBasket();
						});
					}else{
						bootbox.alert("Ocorreu um erro ao tentar remover o Atributo!");
					}
				}
			});
		}
	});
}
</script>