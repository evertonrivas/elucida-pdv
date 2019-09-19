<?php if(!isset($produto)):?>
<br/>
<?php 
else:
	//validar a diferenca de estoque
	if($digitado>$estoque->QUANTIDADE){
		$tipo_ajuste = "+";
		$dif_quantidade = $digitado-$estoque->QUANTIDADE;
	}else{
		$tipo_ajuste = "-";
		$dif_quantidade = $estoque->QUANTIDADE-$digitado;
	}

endif;
?>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
   <input type="hidden" id="txtIdProduto" name="txtIdProduto" value="<?php if(isset($produto)){ echo $produto->IDPRODUTO; }?>"/>
        
    <div class="card">
        <div class="card-header">
            <i class="fas fa-angle-right"></i> <?=$title?>
        </div>
        <div class="card-body">
        	<?php if(!$is_mobile):?>
        	
            <div class="row">
                <div class="form-group col-sm">
                    <label class="control-label">Loja do Estoque</label>
                    <select name="cbStore" id="cbStore" class="form-control form-control-sm" required<?php if(isset($loja)){ echo " disabled"; }?>>
                        <option value="">&laquo; Selecione &raquo;</option>
                        <?php foreach($store_list as $store): ?>
                        <option value="<?php echo $store->IDLOJA; ?>"<?php if(isset($loja)){ if($loja==$store->IDLOJA){ echo " selected"; } }?>><?php echo $store->NOME; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-sm">
                    <label class="control-label">Produto (C&oacute;digo de Barras)</label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="txtBarCode" name="txtBarCode" class="form-control text-right" value="<?php if(isset($produto)){ echo $produto->CODIGO_BARRA; }?>" autocomplete="off" required <?php if(isset($produto)){ echo " disabled"; }?>/>
                        <span class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="btnFindProduct" name="btnFindProduct"<?php if(isset($produto)){ echo " disabled"; }?>><i class="fas fa-cubes"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-2">
                    <img class="img-thumbnail" id="imgThumb" style="width:80px; height:80px" <?php if(isset($produto)){ echo " src='".$produto->IMAGEM."'"; }?>/>
                </div>
                <div class="form-group col-10">
                    <label class="control-label">Nome do Produto</label>
                    <input type="text" class="form-control text-uppercase form-control-sm" id="txtNomeProduto" name="txtNomeProduto" disabled=""<?php if(isset($produto)){ echo "value='".$produto->NOME."'"; }?>/>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm">
                    <label class="control-label">Disponibilidade</label>
                    <input type="number" class="form-control form-control-sm" id="txtQtdeDisp" name="txtQtdeDisp" value="<?php if(isset($estoque)){ echo $estoque->QUANTIDADE; }?>" disabled="">
                </div>
                <div class="form-group col-sm">
                    <label class="control-label">Opera&ccedil;&atilde;o</label>
                    <select name="cbOperacao" id="cbOperacao" class="form-control form-control-sm" required>
                        <option value="">&laquo; Selecione &raquo;</option>
                        <option value="A+"<?php if(isset($produto)){ if($tipo_ajuste=="+"){ echo " selected"; } }?>>Ajuste (+)</option>
                        <option value="A-"<?php if(isset($produto)){ if($tipo_ajuste=="-"){ echo " selected"; } }?>>Ajuste (-)</option>
                        <option value="C">Compra</option>
                        <option value="V">Venda</option>
                    </select>
                </div>
                <div class="form-group col-sm">
                    <label class="control-label">Quantidade</label>
                    <input type="number" class="form-control form-control-sm" id="txtQtde" name="txtQtde" value="<?php if(isset($produto)){ echo $dif_quantidade; }?>" min="0" required>
                </div>
            </div>
            
            
			<?php else: ?>
			
			
			<div class="form-group">
                <label class="control-label">Loja do Estoque</label>
                <select name="cbStore" id="cbStore" class="form-control form-control-sm">
                    <option value="">&laquo; Selecione &raquo;</option>
                    <?php foreach($store_list as $store): ?>
                    <option value="<?php echo $store->IDLOJA; ?>"><?php echo $store->NOME; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">Produto (C&oacute;digo de Barras)</label>
                <div class="input-group">
                    <input type="text" id="txtBarCode" name="txtBarCode" class="form-control text-right" value="" autocomplete="off"/>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" id="btnFindProduct" name="btnFindProduct">...</button>
                    </span>
                </div>
            </div>
            <div class="form-group">
            	<img class="img-thumbnail" id="imgThumb" style="width:80px; height:80px"/>
            </div>
            <div class="form-group">
                <label class="control-label">Nome do Produto</label>
                <input type="text" class="form-control text-uppercase" id="txtNomeProduto" name="txtNomeProduto" disabled=""/>
            </div>
            <div class="form-group">
                <label class="control-label">Disponibilidade</label>
                <input type="number" class="form-control" id="txtQtdeDisp" name="txtQtdeDisp" value="" disabled="">
            </div>
            <div class="form-group">
                <label class="control-label">Opera&ccedil;&atilde;o</label>
                <select name="cbOperacao" id="cbOperacao" class="form-control">
                    <option value="">&laquo; Selecione &raquo;</option>
                    <option value="A+">Ajuste (+)</option>
                    <option value="A-">Ajuste (-)</option>
                    <option value="C">Compra</option>
                    <option value="V">Venda</option>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">Quantidade</label>
                <input type="number" class="form-control" id="txtQtde" name="txtQtde" value="" min="0">
            </div>
			<?php endif; ?>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><i class="fas fa-adjust"></i> Realizar ajuste</button>
            </div>
        </div>
    </div>
</form>

<?php $this->Dialog->product_single(); ?>

<script>
$(document).ready(function(){
    
    $("#cbStore").focus();
});

$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();

    var frmData = {
        IDPRODUTO  : $("#txtIdProduto").val(),
        IDLOJA     : $("#cbStore").val(),
        QUANTIDADE : $("#txtQtde").val(),
        OPERACAO   : $("#cbOperacao").val()
    };

    $.ajax({
    	headers:{
			'X-CSRF-Token' : csrf
		},
        type: 'POST',
        url: '<?=$this->Url->build("/stock/adjustment_save")?>',
        data: frmData,
        success: function(data){
            if(data==true){
            	<?php if(!isset($produto)): ?>
                bootbox.alert("Estoque do produto ajustado com sucesso!",function(){ clearFields(); });
                <?php else: ?>
                bootbox.alert("Estoque do produto ajustado com sucesso!",function(){ window.parent.closeModalAdjust(); })
                <?php endif; ?>
            }
            else{
                bootbox.alert("Ocorreu um erro ao tentar ajustar o estoque do produto!");
            }
        }
    });
});

$(document).keydown(function(evt){
    if (evt.keyCode==27){
        evt.preventDefault();

        bootbox.dialog({message:"Deseja realmente cancelar esta opera&ccedil;&atilde;o?", 
            buttons:{
                yes:{
                    label:"Sim",
                    callback:function(){
                        clearFields();
                    }
                },
                no:{
                    label:"N\u00e3o"			
                }
            }
        });
        return false;
    }
});
    
$(document).on("change","#cbStore",function(){
    if($("#cbStore").val()!=""){
        $("#txtBarCode").focus();
    }
});

$(document).on("keydown","#txtBarCode",function(event){
    if(event.keyCode==13){        
        if($("#txtBarCode").val()!=''){
            getProductInfo(true);
        }
    }
});

$(document).on("click","#btnFindProduct",function(){
	//verifica seh possui uma loja selecionada
    if($("#cbStore").val()!=""){
    	//define a loja do modal antes de exibr
    	$("#TXT_PRODUCTS_DIALOG_STORE").val($("#cbStore").val());
        $("#modalProductSingle").modal({
            backdrop: 'static'
        });
    }else{
        bootbox.alert("Por favor selecione a loja onde o estoque ser&aacute; criado!");
    }
});

$(document).on('hidden.bs.modal',"#modalProduto",function(){
    $("#tblFindProduct tbody").html("");
    $("#txtSKUBuscaProduto").val("");
    $("#txtNomeBuscaProduto").val("");
    $("#txtQtdeBuscaProduto").val("");
});


function useProduct(obj){
	$("#txtBarCode").val(obj.CODIGO_BARRA);
	$("#txtNomeProduto").val(obj.NOME);
	$("#txtIdProduto").val(obj.IDPRODUTO);
	$("#txtQtdeDisp").val(obj.QUANTIDADE_ESTOQUE);
	
	if(obj.IMAGEM!=""){
        $("#imgThumb").attr("src",obj.IMAGEM);
    }else{
        $("#imgThumb").attr("src",'<?=$this->Url->build("/img/spacer.gif")?>');
    }
	
	$("#cbOperacao").focus();
}


function clearFields(){
	//remove a informacao que o form jah foi validado,
	//assim forca uma nova validacao
	$("#frmRegs").removeClass("was-validated");
	
    $("#txtIdProduto").val("");
    $("#txtBarCode").val("");
    $("#txtNomeProduto").val("");
    $("#txtQtdeDisp").val("");
    $("#txtQtde").val("");
    $("#cbStore").val("");
    $("#cbOperacao").val("");
    $("#imgThumb").removeAttr("src");
    $("#cbStore").focus();
};
</script>