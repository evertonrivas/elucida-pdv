<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
    <input type="hidden" id="txtIdPromotion" name="txtIdPromotion" value="<?php if(isset($promo)){ echo $promo->IDPROMOCAO; }?>"/>
    <div class="card">
        <div class="card-header">
        	<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Promo&ccedil;&atilde;o
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/retail/promotion');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="control-label">Loja</label>
                <select name="cbPromoStore" id="cbPromoStore" class="form-control form-control-sm">
                    <option value="">&laquo; Selecione &raquo;</option>
                    <?php foreach($storelist as $store):?>
                    <option value="<?php echo $store->IDLOJA; ?>"<?php if(isset($promo)){ if($promo->IDLOJA==$store->IDLOJA){ echo " selected"; } } ?>><?php echo $store->NOME; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="custom-control custom-switch">
			  <input type="checkbox" class="custom-control-input" id="chPromoAllStore">
			  <label class="custom-control-label" for="chPromoAllStore"> Promocionar em todas as lojas</label>
			</div>
            <div class="form-group">
                <label class="control-label">Nome da Promo&ccedil;&atilde;o</label>
                <input type="text" class="form-control text-uppercase form-control-sm" id="txtPromoName" name="txtPromoName" value="<?php if(isset($promo)){ echo $promo->NOME; }?>" required/>
            </div>
            <div class="row">
            	<div class="form-group col-sm">
            		<label class="control-label">Data Inicial</label>
	                <div class="input-group input-group-sm">
	                    <input type="text" class="form-control date" data-provide='datepicker' data-date-format='dd/mm/yyyy' data-date-language='pt-BR' data-date-autoclose='true' data-date-today-highlight='true' id="txtPromoInicio" name="txtPromoInicio" value="<?php if(isset($promo)){ echo $promo->DATA_INICIAL->format("d/m/Y"); }?>" required/>
	                    <div class="input-group-append">
	                    	<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
	                    </div>
	                </div>
            	</div>
            	<div class="form-group col-sm">
            		<label class="control-label">Data Final</label>
            		<div class="input-group input-group-sm">
	                    <input type="text" class="form-control date" data-provide='datepicker' data-date-format='dd/mm/yyyy' data-date-language='pt-BR' data-date-autoclose='true' data-date-today-highlight='true' id="txtPromoFim" name="txtPromoFim" value="<?php if(isset($promo)){ echo $promo->DATA_INICIAL->format("d/m/Y"); }?>" required/>
	                    <div class="input-group-append">
	                    	<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
	                    </div>
	                </div>
            	</div>
            	<div class="col-sm text-right">
            		<br/>
            		<button type="button" data-toggle="modal" data-target="#modalAddProduct" data-backdrop="static" class="btn btn-success btn-sm"><i class="fas fa-search"></i> Produto(s)</button>
            	</div>
            </div>
            <div class="form-group" id="lstProducts">
            </div>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
            </div>
        </div>
    </div>
</form><br/>

<!--MODAL DE ADICAO DE PRODUTO-->
<form id="frmPromotional">
<div class="modal fade" id="modalAddProduct" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	<h4 class="modal-title" id="modalLabel">Sele&ccedil;&atilde;o de produtos</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
    	<div class="form-group">
            <label for="txtPromoProductId">Produto</label>
            <input type="hidden" id="txtPromoProductId" name="txtPromoProductId"/>
            <div class="input-group input-group-sm">
                <input type="text" id="txtPromoProductName" name="txtPromoProductName" class="form-control" autocomplete="off" readonly=""/>
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="btnFindProduct" name="btnFindProduct" data-toggle="modal" data-target="#modalProductSingle" data-backdrop="static" ><i class="fas fa-cubes"></i></button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm">
                <label class="control-label">Pre&ccedil;o Normal</label>
                <input type="text" class="form-control-plaintext form-control-sm" id="txtPromoProductNormalPrice" name="txtPromoProductNormalPrice"/>
            </div>
            <div class="form-group col-sm">
                <label class="control-label">Pre&ccedil;o Promocional</label>
                <input type="text" id="txtPromoProductPrice" name="txtPromoProductPrice" class="form-control text-right form-control-sm" autocomplete="off"/>
            </div>
        </div>
        <label class="control-label">Condi&ccedil;&otilde;es de Pagamento</label>
        <div class="custom-control custom-switch">
		  <input type="checkbox" class="custom-control-input" id="chkCondicaoPagamentoAny">
		  <label class="custom-control-label" for="chkCondicaoPagamentoAny">QUALQUER</label>
		</div>
        <?php $row = 0; foreach($conditionlist as $condition):?>
        	<?php if($row==0): ?>
        		<div class="row">
        	<?php endif; ?>
        	
        	<div class="col-sm">
	        	<div class="custom-control custom-switch">
				  <input type="checkbox" class="custom-control-input" id="chkCondicaoPagamento_<?php echo $condition->IDCONDICAOPAGAMENTO; ?>" value="<?php echo $condition->IDCONDICAOPAGAMENTO; ?>" name="chkCondicaoPagamento[]">
				  <label class="custom-control-label" for="chkCondicaoPagamento_<?php echo $condition->IDCONDICAOPAGAMENTO; ?>"><?php echo $condition->NOME; ?></label>
				</div>
			</div>
			
			<?php if($row==4): $row=0; ?>
				</div>
			<?php else: $row++; endif; ?>
			
        <?php endforeach;?>
      </div>
      <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> Fechar</button>
            <button type="button" class="btn btn-success btn-sm" id="btnPromoSaveClose"><i class="fas fa-download"></i> Salvar e Fechar</button>
      </div>
    </div>
  </div>
</div>
</form>

<?php $this->Dialog->product_single();?>

<script>
$(document).ready(function(){
    
    $("#txtPromoInicio").mask("00/00/0000");
    $("#txtPromoFim").mask("00/00/0000");
    
    getBasket();    
});

$(document).on("click","#chPromoAllStore",function(){
	if( $(this)[0].checked ){
		//alert('Teste');
		$("#cbPromoStore").attr("disabled","disabled");
	}else{
		$("#cbPromoStore").removeAttr("disabled");
	}
});

$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();

    if(parseInt($("#txtTotalItens").val())>0){
        $.ajax({
            type: 'POST',
            url: '<?=$this->Url->build("/retail/promotion_save")?>',
            data: $("#frmCadPromo").serialize(),
            success: function(data){
                if(data){
	                bootbox.alert("Promo&ccedil;&atilde;o salva com sucesso!",function(){ document.location.href='<?=$this->Url->build("/retail/partner")?>'; });
	            }else{
	            	bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es da Promo&ccedil;&atilde;o!");
	            }
            }
        });
    }else{
        bootbox.alert("N&atilde;o houve sele&ccedil;&atilde;o de produtos para criar uma promo&ccedil;&atilde;o, por favor inclua produtos para salvar!");
    }
});

$(document).on("show.bs.modal","#modalAddProduct",function(){
    $("#txtPromoProductPrice").mask("#,##0.00", {reverse: true});
});

$(document).on("hidden.bs.modal",function(){
    $("body").css('padding-right','');
});

$(document).on("hide.bs.modal","#modalAddProduct",function(){
    $("#txtPromoProductName").val('');
    $("#txtPromoProductId").val('');
    $("#txtPromoProductPrice").val('');
    $("#txtPromoProductNormalPrice").html('');
    
    $("input[name='chkCondicaoPagamento[]']").each(function(){
        $(this)[0].checked = false;
    });
    
    getBasket();
});

$(document).on("show.bs.modal","#modalAddProduct",function(){
    $("#txtPromoProductNormalPrice").mask("#,##0.00",{reverse:true});
});

$(document).on("change","#chkCondicaoPagamentoAny",function(){
    if($(this)[0].checked){
        $("input[name='chkCondicaoPagamento[]']").each(function(){
            $(this).attr("disabled","");
        });
    }else{
        $("input[name='chkCondicaoPagamento[]']").each(function(){
            $(this).removeAttr("disabled");
        });
    }
});

$(document).on("click","#btnPromoSaveClose",function(){
    
    var totalChecked = 0;
    if(!$("#chkCondicaoPagamentoAny")[0].checked){
        $("input[name='chkCondicaoPagamento[]']").each(function(){
            if( $(this)[0].checked ==true ){
                totalChecked = totalChecked + 1;
            }
        });
    }else{
        totalChecked = 1;
    }
    
    if(totalChecked > 0){
        
        if($("#txtPromoProductPrice").val()!=""){
            if($("#txtPromoProductId").val()!=""){
                $.ajax({
                    method:'post',
                    data: $("#frmPromotional").serialize(),
                    url: '<?=$this->Url->build("/retail/promotion_item_add/")?>',
                    success:function(data){
                        if(data){
                            bootbox.alert('Produto promocional adicionado com sucesso!');
                            $("#modalAddProduct").modal("hide");
                        }
                    }
                });
            }else{
                bootbox.alert("Por favor selecione um produto para promocionar!");
            }
        }else{
            bootbox.alert('Por favor informe o pre&ccedil;o promocional do produto!');
        }
    }else{
        bootbox.alert('Selecione ao menos uma condi&ccedil;&atilde;o de pagamento!');
    }
});

function useProduct(obj){
	$("#txtPromoProductName").val(obj.NOME);
	$("#txtPromoProductNormalPrice").val(obj.PRECO_VENDA.toFixed(2));
	$("#txtPromoProductId").val(obj.IDPRODUTO);
	$("#modalProduct").modal('hide');
}

function removeProduct(idProduto,idCondPagamento){

    bootbox.dialog({message:"Deseja realmente excluir este item?", 
        buttons:{
            yes:{
                label:"Sim",
                callback:function(){
                    $.ajax({
                        url: '<?=$this->Url->build("/retail/promotion_item_del/")?>'+idProduto+'/'+idCondPagamento,
                        success:function(data){
                            if(data==true){
                                bootbox.alert('Item removido com sucesso!',function(){ getBasket(); });
                            }
                        }
                    });
                }
            },
            no:{
                label:"N\u00e3o"			
            }
        }
    });
}

function getBasket(){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/retail/promotion_itens/")?>'+$("#txtIdPromotion").val(),
        success:function(data){
            $("#lstProducts").html(data);
        }
    });
}
	
function clearFields(){
	$("#frmRegs").removeClass("was-validated");
	
    $("#txtIdPromotion").val("");
    $("#txtPromoName").val("");
    $("#txtPromoInicio").val("");
    $("#txtPromoFim").val("");
    $("#lstProducts").html("");
    $("#txtPromoName").focus();
}
</script>