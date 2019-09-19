<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
    <input type="hidden" id="txtIdProduct" name="txtIdProduct" value="<?php if(isset($produto)){ echo $produto->IDPRODUTO; }?>"/>
    <input type="hidden" id="txtProductDateCreate" name="txtProductDateCreate" value="<?php if(isset($produto)){ echo $produto->DATA_CADASTRO->format("Y-m-d"); }else{ echo date("Y-m-d"); }?>"/>
    <input type="hidden" id="txtProductImageName" name="txtProductImageName" value="<?php if(isset($produto)){ echo $produto->IMAGEM; }?>"/>
        <div class="card">
            <div class="card-header">
            	<div class="row">
					<div class="col-sm">
						<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Produto Composto
					</div>
					<div class="col-sm text-right">
						<a href="<?php echo $this->Url->build('/stock/composite_product');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
					</div>
				</div>
            </div>
            <div class="card-body">
            	<div class="row">
            		<div class="col-sm">
            			<div class="form-group">
	                        <div>
	                        	<a href="#" data-toggle="modal" data-target="#modalGallery" data-backdrop="static">
	                        	<img id="imgThumb" style="width:80px; height:80px;border:1px solid #ddd!important;border-radius: 4px;" <?php if(isset($produto)){ if($produto->IMAGEM!=""){ echo "src='".$produto->IMAGEM."'"; } }?>/>
	                        	</a>
	                        </div>
	                    </div>
	                    <div class="form-group">
	                        <label for="txtKitNome">Nome do Kit</label>
	                        <input type="text" class="form-control text-uppercase form-control-sm" id="txtKitNome" name="txtKitNome" value="<?php if(isset($produto)){ echo $produto->NOME; }?>" autocomplete="off" required />
	                    </div>
	                    <div class="form-group">
	                        <label class="control-label">Nome Etiqueta</label>
	                        <input type="text" class="form-control text-uppercase form-control-sm" id="txtKitNomeTag" name="txtKitNomeTag" maxlength="30" value="<?php if(isset($produto)){ echo $produto->NOME_TAG; }?>" autocomplete="off"/>
	                    </div>
	                    <div class="form-group">
	                        <label for="txtProductBarcode">C&oacute;digo de Barras</label>
                            <div class="input-group mb-3 input-group-sm">
                                <input type="text" id="txtProductBarcode" maxlength="15" name="txtProductBarcode" class="form-control" value="<?php if(isset($produto)){ echo $produto->CODIGO_BARRA; }?>" autocomplete="off" required />
                                <span class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="btnGenBarcode" name="btnGenBarcode" data-toggle="tooltip" data-placement="top" title="Gerar c&oacute;digo de barras"><i class="fas fa-barcode"></i></button>
                                </span>
                            </div>
	                    </div>
	                    <div class="form-group">
	                        <label for="txtKitSKU">SKU</label>
	                        <input type="text" class="form-control text-uppercase form-control-sm" id="txtKitSKU" name="txtKitSKU" value="<?php if(isset($produto)){ echo $produto->SKU; }?>" autocomplete="off" required />
	                    </div>
	                    <div class="form-group">
	                        <label for="cbCategory">Categoria(s)</label>
                            <select name="cbCategory" id="cbCategory" size="5" multiple="" class="form-control form-control-sm" required >
                                <?php for($i=0;$i<count($category_list);$i++): ?>
                                <option value="<?php echo $category_list[$i]['IDCATEGORIA']?>"<?php
                                    if(isset($produto_cat)){
                                        foreach($produto_cat as $CATEGORIA)
                                        {
                                            if($CATEGORIA->IDCATEGORIA==$category_list[$i]['IDCATEGORIA']){
                                                echo " selected";
                                            }
                                        }
                                    }
                                ?>><?php echo $category_list[$i]['NOME'];?></option>
                                <?php endfor; ?>
                            </select>
	                    </div>
            		</div>
            		<div class="col-1">&nbsp;</div>
            		<div class="col-7">
            			<div class="form-group">
                            <label>Item(s) do Produto</label>
                            <div class="card">
                            	<div class="card-body" style="min-height: 250px;max-height:250px; overflow-y: scroll;" id="pnlItens">
                            		
                            	</div>
                            	<div class="card-footer text-right">
									<button type="button" class="btn btn-success btn-sm" id="btnAddAttribute" name="btnAddAttribute" data-toggle="modal" data-target="#modalProductMultiple" data-backdrop="static"><i class="fas fa-plus-square"></i> Adicionar</button>
								</div>
                            </div>
	                    </div>
	                    <div class="form-group">
	                        <label for="cbProvider">Fornecedor</label>
                            <select name="cbProvider" id="cbProvider" class="form-control form-control-sm" required >
                                <option value="">&laquo; Selecione &raquo;</option>
                                <?php foreach($provider_list as $provider): ?>
                                <option value="<?php echo $provider->IDFORNECEDOR?>"<?php if(isset($produto)){ if($produto->IDFORNECEDOR==$provider->IDFORNECEDOR){ echo " selected"; } }?>><?php echo $provider->FANTASIA; ?></option>
                                <?php endforeach; ?>
                            </select>
	                    </div>
	                    <div class="form-group">
	                        <label for="txtProductPrice">Pre&ccedil;o de Venda</label>
                            <div class="input-group mb-3 input-group-sm">
                                <input type="text" class="form-control text-right" id="txtProductPrice" name="txtProductPrice" value="<?php if(isset($produto)){ echo $this->Number->precision($produto->PRECO_VENDA,2); }?>" autocomplete="off" required />
                                <span class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btnGetPrice" name="btnGetPrice"data-toggle="tooltip" data-placement="top" title="Montar pre&ccedil;o de venda"><i class="fas fa-search-dollar"></i></button>
                                </span>
                            </div>
	                    </div>
            		</div>
            	</div>
				<div class="row">
	            	<div class="col">
	            		<div class="form-group text-right">
			                <button type="submit" id="btnSave" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
			            </div>
	            	</div>
	            </div>
            </div>
        </div>
</form><br/>

<?php $this->Dialog->product_multiple(); ?>

<script>
$(document).ready(function(){
    $("#txtProductPrice").mask("#,##0.00", {reverse: true});
    getBasket();
    
});

$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();
    
        var frmData = {
            IDPRODUTO     : $("#txtIdProduct").val(),

            IDFORNECEDOR  : $("#cbProvider").val(),
            NOME          : $("#txtKitNome").val(),
            NOME_TAG      : $("#txtKitNomeTag").val(),
            CODIGO_BARRA  : $("#txtProductBarcode").val(),
            PRECO_VENDA   : $("#txtProductPrice").val(),
            SKU           : $("#txtKitSKU").val(),
            DATA_CADASTRO : $("#txtProductDateCreate").val(),

            //categorias
            CATEGORIAS    : $("#cbCategory").serialize(),

            //imagem
            IMG_NAME      : $("#txtProductImageName").val()
        };

        lockPost();

        $.ajax({
            type: 'POST',
            url: '<?=$this->Url->build("/stock/composite_product_save")?>',
            data: frmData,
            success: function(data){
                unlockPost();
                if(data==true){
                	bootbox.alert("Produto criado com sucesso",function(){ document.location.href='<?=$this->Url->build("/stock/composite_product/")?>'; });
                }else{
                    bootbox.alert("Ocorreu um erro ao tentar salvar os dados do produto composto!");
                    clearFields();
                }
            }
        });
});

$(document).on("click","#btnGenBarcode",function(){
    if($("#txtProductBarcode").val()==""){
            $.ajax({
                url: '<?=$this->Url->build("/stock/product_barcode_generate")?>',
                success: function(data){
                    $("#txtProductBarcode").val(data);
                    $("#frmRegs").removeClass("was-validated");
                }	
            });
        }
        else
            $("#txtProductPriceBuy").focus();
});


$(document).on("click","#btnGetPrice",function(){
    $.ajax({
            url: '<?=$this->Url->build("/stock/composite_product_mount_price")?>',
            success: function(data){
                $("#txtProductPrice").val(data);
            }	
        });
});

$(document).on("hidden.bs.modal","#modalProductMultiple",function(){
    getBasket();
});

function addProduct(idProduto){
    $.ajax({
        url: '<?=$this->Url->build("/stock/composite_product_item_add/")?>'+idProduto,
        success: function(data){
            if(data==false){
                bootbox.alert("Ocorreu um erro ao tentar adicionar o produto");
            }else{
                $("#lnk"+idProduto).addClass("disabled");
            }
        }
    });
}

function removeItem(idProduto){
    $.ajax({
        url: '<?=$this->Url->build("/stock/composite_product_item_remove/")?>'+idProduto,
        success: function(data){
            if(data==true){
                getBasket();
            }
        }
    });
}

function getBasket(){
    $.ajax({
        url: '<?=$this->Url->build("/stock/composite_product_itens_get/")?>'+$("#txtIdProduct").val(),
        success: function(data){
            $("#pnlItens").html(data);
        }
      });
}

function clearFields(){
	$("#frmRegs").removeClass("was-validated");
	
    $("#txtIdProduct").val("");
    $("#txtProductImageName").val("");
    $("#txtProductSiteImageName").val("");
    $("#txtKitNome").val("");
    $("#txtKitNomeTag").val("");
    $("#txtProductBarcode").val("");
    $("#txtKitSKU").val("");
    $("#cbCategory").val("");
    $("#cbProvider").val("");
    $("#txtProductPrice").val("");
    
    $("#txtKitNome").focus();
    getBasket();
}

//ao fechar o modal da galeria, define a imagem no campo e o id da imagem tambem
$("#modalGallery").on("hidden.bs.modal",function(){
	$("#txtProductImageName").val(selectedImagePath);
    $("#imgThumb").attr("src",selectedImagePath);
});
</script>