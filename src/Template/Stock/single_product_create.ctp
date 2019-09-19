<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
    <input type="hidden" id="txtIdProduct" name="txtIdProduct" value="<?php if(isset($produto)){ echo $produto->IDPRODUTO; }?>"/>
    <input type="hidden" id="txtProductDataCreated" name="txtProductDataCreated" value="<?php if(isset($produto)){ echo $produto->DATA_CADASTRO->format("Y-m-d"); }else{ echo date("Y-m-d"); }?>"/>
    <input type="hidden" id="txtProductImageName" name="txtProductImageName" value="<?php if(isset($produto)){ echo $produto->IMAGEM; }?>"/>
    <div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Produto
				</div>
				<div class="col-sm text-right">
					<a href="<?php if( !$adjust ): echo $this->Url->build('/stock/single_product'); else: echo $this->Url->build('tributary/invoice_received_process_result'); endif; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div>
				<ul class="nav nav-tabs" id="tabProduto">
					<li class="nav-item"><a class="nav-link active" href="#cadastro" data-toggle="tab">Cadastro</a></li>
					<li class="nav-item"><a class="nav-link" href="#estoque" data-toggle="tab">Estoque</a></li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade active show" id="cadastro">
						<div class="form-row">
							<div class="form-group col-sm-6">
									<label for="cbProductType">Tipo de Produto</label>
									<select name="cbProductType" id="cbProductType" class="form-control form-control-sm" required>
										<option value="">&laquo; Selecione &raquo;</option>
										<?php foreach($producttype_list as $producttype):?>
										<option value="<?php echo $producttype->IDPRODUTOTIPO?>"<?php if(isset($produto)){ if($produto->IDPRODUTOTIPO==$producttype->IDPRODUTOTIPO){ echo " selected"; } }else{ if($producttype->IDPRODUTOTIPO==$default_product_type){ echo " selected"; } }?>><?php echo $producttype->DESCRICAO; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="form-group col-sm-6">
									<label for="cbProvider">Fornecedor</label>
									<select name="cbProvider" id="cbProvider" class="form-control form-control-sm" required>
										<option value="">&laquo; Selecione &raquo;</option>
										<?php foreach($provider_list as $provider):?>
										<option value="<?php echo $provider->IDFORNECEDOR?>"<?php if(isset($produto)){ if($produto->IDFORNECEDOR==$provider->IDFORNECEDOR){ echo " selected"; } }?>><?php echo $provider->FANTASIA; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
						</div>
						<div class="form-row">
							<div class="form-group col-sm-1">
								<div style="width:100%!important; border:1px solid #ddd;border-radius: 4px;">
									<a href="#" data-toggle="modal" data-target="#modalGallery" data-backdrop="static">
									<img id="imgThumb" style="width:100%!important;min-height: 80px!important;max-height: 80px!important;border-radius: 4px!important;border:0!important;"<?php if(isset($produto)){ if($produto->IMAGEM!=""){ echo "src='".$produto->IMAGEM."'"; } }?>/></a>
								</div>
							</div>
							<div class="form-group col-sm-11">
								<label for="txtProductNome">Nome</label>
								<input type="text" name="txtProductNome" id="txtProductNome" class="form-control text-uppercase form-control-sm" value="<?php if(isset($produto)){ echo $produto->NOME; }?>" autocomplete="off" required/>
							</div>

						</div>
						<div class="form-row">
							<div class="form-group col-sm-6">
								<label for="txtProductNomeTAG">Nome Etiqueta</label>
								<input type="text" id="txtProductNomeTAG" maxlength="30" name="txtProductNomeTAG" class="form-control form-control-sm text-uppercase" value="<?php if(isset($produto)){ echo $produto->NOME_TAG; }?>" autocomplete="off"/>
							</div>

							<div class="form-group col-sm-6">
								<label for="txtProductBarcode">C&oacute;digo de Barras</label>
								<div class="input-group input-group-sm">
									<input type="text" id="txtProductBarcode" maxlength="15" name="txtProductBarcode" class="form-control" value="<?php if(isset($produto)){ echo $produto->CODIGO_BARRA; }?>" autocomplete="off" required/>
									<span class="input-group-append">
										<button class="btn btn-outline-secondary" type="button" id="btnGenBarcode" name="btnGenBarcode" data-toggle="tooltip" data-placement="top" title="Gerar c&oacute;digo de barras"><i class="fas fa-barcode"></i></button>
									</span>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-sm-4">
								<label for="txtProductPriceBuy">Pre&ccedil;o de Compra</label>
								<input type="text" id="txtProductPriceBuy" name="txtProductPriceBuy" class="form-control form-control-sm" value="<?php if(isset($produto)){ echo $this->Number->precision($produto->PRECO_COMPRA,2); }?>" autocomplete="off" required/>
							</div>
							<div class="form-group col-sm-4">
								<label for="txtProductMarkup">Markup</label>
								<input type="text" id="txtProductMarkup" name="txtProductMarkup" class="form-control form-control-sm" value="<?php if(isset($produto)){ echo $this->Number->precision($produto->MARKUP,5); }?>" autocomplete="off" required/>
							</div>
							<div class="form-group col-sm-4">
								<label for="txtProductPriceSell">Pre&ccedil;o de Venda</label>
								<input type="text" id="txtProductPriceSell" name="txtProductPriceSell" class="form-control form-control-sm" value="<?php if(isset($produto)){ echo $this->Number->precision($produto->PRECO_VENDA,2); }?>" autocomplete="off" disabled="disabled"/>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-sm-3">
								<label for="txtProductCodFornec">C&oacute;digo do Fornecedor</label>
								<input type="text" id="txtProductCodFornec" name="txtProductCodFornec" class="form-control text-uppercase form-control-sm" value="<?php if(isset($produto)){ echo $produto->COD_FORNECE; }elseif(isset($cod_fornece)){ echo $cod_fornece; }?>" autocomplete="off" required/>
							</div>
							<div class="form-group col-sm-3">
								<label for="txtProductNCM">NCM</label>
								<div class="input-group input-group-sm">
								<input type="text" id="txtProductNCM" name="txtProductNCM" class="form-control" value="<?php if(isset($produto)){ echo $produto->NCM; }?>" autocomplete="off"/>
								<span class="input-group-append">
									<button class="btn btn-outline-secondary" type="button" id="btnGetNCM" name="btnGetNCM" data-toggle="tooltip" data-placement="top" title="Pesquisar NCM"><i class="fas fa-globe-americas"></i></button>
								</span>
								</div>
							</div>
							<div class="form-group col-sm-2">
								<label for="txtProductCSOSN">CSOSN</label>
								<input type="text" id="txtProductCSOSN" name="txtProductCSOSN" class="form-control form-control-sm" value="<?php if(isset($produto)){ echo $produto->CSOSN; }?>" autocomplete="off"/>
							</div>
							<div class="form-group col-sm-2">
								<label for="txtProductSKU">SKU</label>
								<input type="text" id="txtProductSKU" name="txtProductSKU" class="form-control text-uppercase form-control-sm" value="<?php if(isset($produto)){ echo $produto->SKU; }?>" autocomplete="off" disabled="disabled"/>
							</div>
							<div class="form-group col-sm-2">
								<label for="cbProductUnit">Medida</label>
								<select id="cbProductUnit" name="cbProductUnit" class="form-control form-control-sm" required>
									<option value="UN"<?php if(isset($produto)){ if($produto->UNIDADE_MEDIDA=="UN"){ echo " selected"; } }?>>UN</option>
									<option value="KG"<?php if(isset($produto)){ if($produto->UNIDADE_MEDIDA=="KG"){ echo " selected"; } }?>>KG</option>
								</select>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-sm-4">
								<label for="cbCategory">Categoria</label>
								<select name="cbCategory" id="cbCategory" size="5" multiple="" class="form-control form-control-sm" required>
									<?php for($i=0;$i<count($category_list);$i++): ?>
										<option value="<?php echo $category_list[$i]['IDCATEGORIA']?>"<?php
											if(isset($produto_cat)){
												foreach($produto_cat as $CATEGORIA){
													if($CATEGORIA->IDCATEGORIA==$category_list[$i]['IDCATEGORIA']){
														echo " selected";
													}
												}
											}
										?>><?php echo $category_list[$i]['NOME'];?></option>
										<?php endfor; ?>
								</select>
							</div>
							<div class="form-group col-sm-8">
								<label class="control-label">Informa&ccedil;&otilde;es Adicionais</label>
								<div id="AdditionalInf"></div>
							</div>
						</div>
						<div class="form-group text-right">
							<button type="submit" id="btnSave" name="btnSave" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
							<button type="button" id="btnSaveAs" name="btnSaveAs" class="btn btn-primary btn-sm" <?php if (!isset($produto)){ echo " disabled"; } ?>><i class="far fa-hdd"></i> Salvar Como</button>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane fade" id="estoque">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<!-- MODAL DE BUSCA DE NCM -->
<div class="modal fade" id="modalFindNCM" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <form id="frmFindNCM" class="form">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Busca de NCM</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table class="table" id="tblFindNCM">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>C&oacute;digo NCM</th>
                            <th>Nome</th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input type="text" class="form-control text-uppercase input-sm" id="txtCodigoNCM" name="txtCodigoNCM" autocomplete="off"></td>
                            <td><input type="text" class="form-control text-uppercase input-sm" id="txtNameNCM" name="txtNameNCM" autocomplete="off"></td>
                            <td><button type="submit" class="btn btn-primary" id="btnFilterCli" name="btnFilterCli">Filtrar</button> </td>
                        </tr>
                    </thead>
                    <tbody>
                    	
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" id="btnUseNCM">Usar Selecionado</button>
            </div>
        </div>
    </div>
    </form>
</div>

<script>
$(document).ready(function(){ 
    $("#txtProductPriceBuy").mask("#,##0.00", {reverse: true});
    $("#txtProductPriceSell").mask("#,##0.00", {reverse: true});
    $("#txtProductMarkup").mask("0.00000", {reverse: true});
<?php 
    if(isset($produto) && $produto->IDPRODUTO!=""){        
        echo "$.ajax({
			headers : {
				'X-CSRF-Token': csrf
			},
            url: '".$this->Url->build("/stock/single_product_get_stock")."/".$produto->IDPRODUTO."',
            success:function(data){
                $('#estoque').html(data);
            }
        });\n";
    }
?>        
});

$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();
	
	//verifica se eh um produto novo
	if($("#txtIdProduct").val()==""){
		
		//verifica se existe o codigo de barras
		$.ajax({
			headers : {
				'X-CSRF-Token': csrf
			},
			url: '<?=$this->Url->build("/stock/product_barcode_exist/")?>'+$("#txtProductBarcode").val(),
			success: function(data){
				if(data){
					bootbox.alert("J&aacute; existe um produto com o mesmo c&oacute;digo de barras, por favor verifique!");
				}else{
					//verifica se o SKU ja existe
					var dataPost = {
						IDPRODUTOTIPO : $("#cbProductType").val(),
						COD_FORNECE   : $("#txtProductCodFornec").val(),
						ATRIBUTOS     : $("select[name*=lst_atributo]").serialize()
					};

					$.ajax({
						headers : {
							'X-CSRF-Token': csrf
						},
						method:'post',
						data: dataPost,
						url: '<?=$this->Url->build("/stock/product_sku_exist/")?>',
						success:function(data){
							if(data){
								bootbox.alert("J&aacute; existe um produto com o mesmo c&oacute;digo <strong>SKU</strong>, por favor verifique!");
							}else{
								saveProductData(false);
							}
						}
					});
				}
			}
		});
	}else{
		saveProductData(false);
	}
});

$(document).on("click","#btnSaveAs",function(event){
	event.preventDefault();
	
	//verifica se existe o codigo de barras
	$.ajax({
		headers : {
			'X-CSRF-Token': csrf
		},
		url: '<?=$this->Url->build("/stock/product_barcode_exist/")?>'+$("#txtProductBarcode").val(),
		success: function(data){
			if(data){
				bootbox.alert("J&aacute; existe um produto com o mesmo c&oacute;digo de barras, por favor verifique!");
			}else{
				//verifica se existe o SKU
				var dataPost = {
					IDPRODUTOTIPO : $("#cbProductType").val(),
					COD_FORNECE   : $("#txtProductCodFornec").val(),
					ATRIBUTOS     : $("select[name*=lst_atributo]").serialize()
				};

				$.ajax({
					headers : {
						'X-CSRF-Token': csrf
					},
					method:'post',
					data: dataPost,
					url: '<?=$this->Url->build("/stock/product_sku_exist/")?>',
					success:function(data){
						if(data){
							bootbox.alert("J&aacute; existe um produto com o mesmo c&oacute;digo <strong>SKU</strong>, por favor verifique!");
						}else{
							saveProductData(true);
						}
					}
				});
			}
		}
	});
});

//para o envio do formulario se for precionada a tecla Enter
$(document).on("keypress","#txtProductBarcode",function(event){
    if (event.keyCode === 10 || event.keyCode === 13) 
        event.preventDefault();
});

$(document).on("change","#cbProductType",function(){
    $.ajax({
		headers : {
			'X-CSRF-Token': csrf
		},
        url: '<?=$this->Url->build("/stock/product_type_get_attributes/")?>'+$(this).val(),
        success: function(data){
            $("#AdditionalInf").html(data);
        }
    });
});

$(document).on("keyup","#txtProductMarkup",function(){
    var preco_compra = parseFloat($("#txtProductPriceBuy").val());
    var markup = parseFloat($("#txtProductMarkup").val());
    var preco_venda = preco_compra*markup;

    $("#txtProductPriceSell").val(preco_venda.toFixed(1));
});

$(document).on("click","#btnGenBarcode",function(){
    if($("#txtProductBarcode").val()===""){
        $.ajax({
			headers : {
				'X-CSRF-Token': csrf
			},
            url: '<?=$this->Url->build("/stock/product_barcode_generate")?>',
            success: function(data){
                $("#txtProductBarcode").val(data);
                $("#txtProductPriceBuy").focus();
            }	
        });
    }
    else
        $("#txtProductPriceBuy").focus();
});

$(document).on("click","#btnGetNCM",function(){
    $("#modalFindNCM").modal({
        backdrop:'static'
    });
});

$(document).on("show.bs.modal","#modalFindNCM",function(){
    $("#txtNomeNCM").focus();
});

$(document).on("hidden.bs.modal","#modalFindNCM",function(){
    $("#tblFindNCM tbody").html("");
    $("#txtCodigoNCM").val("");
    $("#txtNameNCM").val("");
});

$(document).on("submit","#frmFindNCM",function(evt){
    evt.preventDefault();
    
    if($("#txtCodigoNCM").val()!="" || $("#txtNameNCM").val()!=""){
        var dataForm = {
            CODIGO_NCM : $("#txtCodigoNCM").val(),
            NOME_NCM   : $("#txtNameNCM").val()
        };

        $.ajax({
			headers : {
				'X-CSRF-Token': csrf
			},
            method: 'post',
            url: '<?=$this->Url->build("/stock/dialog_ncm_find")?>',
            data: dataForm,
            success: function(data){
                $("#tblFindNCM tbody").html(data);
            }
        });
    }
    else{
        $("#tblFindNCM tbody").html("");
    }
});

$(document).on("click","#btnUseNCM",function(){
    $("input[name='rdNCM[]']").each(function(){
        if($(this)[0].checked){
            $("#txtProductNCM").val($(this).val());
            $("#modalFindNCM").modal('hide');
        }
    });
});

<?php if(isset($produto) && $produto->IDPRODUTO!=""):?>

$(document).ajaxComplete(function(event, xhr, settings){
    if(settings.url.indexOf("product_type_get_attributes")!=-1){
    <?php 
    if(isset($produto_attr)){
        foreach($produto_attr as $ATRIBUTO){
            echo "\$(\"#lst_atributo\\\[".$ATRIBUTO->IDATRIBUTO."\\\]\").val('".$ATRIBUTO->VALOR."');\n";
        }
    }
    ?>
    }
});

<?php endif; ?>

function clearFields(){
	$("#frmRegs").removeClass("was-validated");
	
	
    $("#txtIdProduct").val("");
    $("#cbProductType").val("");
    $("#cbProvider").val("");
    $("#txtProductNome").val("");
    $("#txtProductNomeTAG").val("");
    $("#txtProductBarcode").val("");
    $("#txtProductPriceBuy").val("");
    $("#txtProductMarkup").val("");
    $("#txtProductPriceSell").val("");
    $("#txtProductCodFornec").val("");
    $("#txtProductNCM").val("");
    $("#txtProductSKU").val("");
    $("#cbCategory").val("");

    $("#txtProductImageName").val("");
    $("#txtProductSiteImageName").val("");

    $("cbProductType").focus();
}

function saveProductData(isSaveAS){
    var $dataForm = {
        IDPRODUTO     : ((!isSaveAS)?$("#txtIdProduct").val():""),
        IDFORNECEDOR  : $("#cbProvider").val(),
        NOME          : $("#txtProductNome").val(),
        NOME_TAG      : $("#txtProductNomeTAG").val(),
        CODIGO_BARRA  : $("#txtProductBarcode").val(),
        PRECO_COMPRA  : $("#txtProductPriceBuy").val(),
        MARKUP        : $("#txtProductMarkup").val(),
        PRECO_VENDA   : $("#txtProductPriceSell").val(),
        COD_FORNECE   : $("#txtProductCodFornec").val(),
        NCM           : $("#txtProductNCM").val(),
        CSOSN         : $("#txtProductCSOSN").val(),
        SKU           : ((!isSaveAS)?$("#txtProductSKU").val():""),
        UNIDADE_MEDIDA: $("#cbProductUnit").val(),
        DATA_CADASTRO : $("#txtProductDataCreated").val(),

        //categorias
        CATEGORIAS    : $("#cbCategory").serialize(),

        //atributos dos produtos
        ATRIBUTOS       : $("select[name*=lst_atributo]").serialize(),
        IDPRODUTOTIPO   : $("#cbProductType").val(),

        //imagem
        IMG_NAME      : $("#txtProductImageName").val(),
        ADJUST        : <?php echo (($adjust)?"1":"0"); ?>
    };

    $.ajax({
    	headers : {
			'X-CSRF-Token': csrf
		},
        method: 'POST',
        url: '<?=$this->Url->build("/stock/single_product_save")?>',
        data: $dataForm,
        success: function(data){
            if(data){
				bootbox.alert("Produto salvo com sucesso!",function(){ document.location.href='<?=$this->Url->build("/stock/single_product")?>' });
            }else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es do Produto!");
				clearFields();
            }
        }
    });
}

//ao fechar o modal da galeria, define a imagem no campo e o id da imagem tambem
$("#modalGallery").on("hidden.bs.modal",function(){
	$("#txtProductImageName").val(selectedImagePath);
    $("#imgThumb").attr("src",selectedImagePath);
});
</script>