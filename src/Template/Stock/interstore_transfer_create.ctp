<br/>
<form id="frmRegs" name="frmRegs">
    <input type="hidden" id="txtIdTransfer" name="txtIdTransfer" value="<?php if(isset($transfer)){ echo $transfer->IDTRANSFERENCIA; }?>"/>
    <div class="card">
        <div class="card-header">
        	<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Transfer&ecirc;ncia de Produto
				</div>
				<div class="col-sm text-right">
					<a href="javascript:showModal()" class="btn btn-success btn-sm"><i class="fas fa-cubes"></i> Produtos</a>
					<a href="<?php echo $this->Url->build('/stock/interstore_transfer');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a> 
				</div>
			</div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <p><strong>Importante sobre transfer&ecirc;ncia de produtos:</strong></p>
                <ol>
                    <li>N&atilde;o ser&aacute; executada a transfer&ecirc;ncia de uma loja para ela mesma;</li>
                    <li>N&atilde;o ser&aacute; transferido mais que a quantidade m&aacute;xima do estoque de destino;</li>
                    <li>S&oacute; ser&aacute; transferida a quantidade que for excedente a quantidade m&iacute;nima da origem + 1;</li>
                    <li>Se o produto n&atilde;o existir no estoque de destino, o mesmo ser&aacute; inclu&iacute;do;</li>
                    <li>A transfer&ecirc;ncia s&oacute; ser&aacute; conclu&iacute;da quando o destino confirmar o recebimento;</li>
                    <li>Toda transfer&ecirc;ncia tem um <u>prazo de validade</u> de <strong><?php echo $validade;?> dias corridos</strong>. Ap&oacute;s o prazo a mesma &eacute; cancelada automaticamente.</li>
                </ol>
            </div>
            <div class="form-group">
                <label class="control-label">Identifica&ccedil;&atilde;o da Transfer&ecirc;ncia</label>
                <input type="text" class="form-control text-uppercase form-control-sm" id="txtTransferNome" name="txtTransferNome" value="<?php if(isset($transfer)){ echo $transfer->NOME; }?>" maxlength="50"/>
            </div>
            <div class="row">
                <div class="form-group col-sm">
                    <label class="control-label">Estoque de Origem</label>
                    <select name="cbOrigem" id="cbOrigem" class="form-control form-control-sm" <?php if($user['role']!="admin"){ echo " disabled"; }?>>
                        <option value="">&laquo; Selecione &raquo;</option>
                        <?php foreach($store_list as $store): ?>
                        <option value="<?php echo $store->IDLOJA?>"<?php if(isset($transfer)){ if($transfer->IDLOJA_ORIGEM==$store->IDLOJA){ echo " selected"; } }else{ if($user['role']!="admin"){ if($user['storeid']==$store->IDLOJA){ echo " selected"; } } }?>><?php echo $store->NOME; ?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="form-group col-sm">
                    <label class="control-label">Estoque de Destino</label>
                    <select name="cbDestino" id="cbDestino" class="form-control form-control-sm">
                        <option value="">&laquo; Selecione &raquo;</option>
                        <?php foreach($store_list as $store): ?>
                        <?php if($user['role']!="admin"): //se o usuario nao for administrador ?>
                        <?php if($user['storeid']!=$store->IDLOJA): //remove do destino a loja que ele pertence ?>
                        <option value="<?php echo $store->IDLOJA?>"<?php if(isset($transfer)){ if($transfer->IDLOJA_DESTINO==$store->IDLOJA){ echo " selected"; } }?>><?php echo $store->NOME; ?></option>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="form-group" id="tblResult">

            </div>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
            </div>
        </div>
    </div>
</form>

<?php $this->Dialog->product_multiple();?>
<script>
$(document).ready(function(){
    $("#alertSuccess").hide();
    $("#alertFail").hide();
    
    getBasket();
    
    $('#modalProduct').on('hidden.bs.modal', function (e) {
      getBasket();
    });
    
    $("#fromCadTransfer").formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields:{
            txtTransferNome:{
                validators:{
                    notEmpty:{
                        message: "Por favor informe a identifica&ccedil;&atilde;o da transfer&ecirc;ncia"
                    }
                }
            }
        }
    })
    .on('success.form.fv',function(event){
        
        
    });
    
});

$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();
        
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method:'post',
        url: '<?=$this->Url->build("/stock/interstore_transfer_save")?>',
        data: $("#fromCadTransfer").serialize(),
        success: function(data){
            if(data==true){
            	bootbox.alert("Transfer&ecirc;ncia criada com sucesso!");
            }else{
            	bootbox.alert("Ocorreu um problema ao tentar criar a transfer&ecirc;ncia!");
            }
        }
    });
});

$(document).on("hidden.bs.modal","#modalProductMultiple",function(){
    getBasket();
});

function addProduct(idProduto){
    $.ajax({
    	header:{
			'X-CSRF-Token':csrf
		},
        url:'<?=$this->Url->build("/stock/interstore_transfer_item_add/")?>'+idProduto+'/'+$("#cbOrigem").val(),
        success: function(data){
            if(data==false){
                bootbox.alert("Ocorreu um erro ao tentar adicionar o produto");
            }
            else{
                $("#lnk"+idProduto).addClass("disabled");
            }
        }
    });
}

function removeItem(idProduto){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/stock/interstore_transfer_item_del/")?>'+idProduto,
        success: function(data){
            if(data==true){
                getBasket();
            }
        }	
    });
}

function getBasket(){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/stock/iterstore_transfer_itens/")?>'+$("#txtIdTransfer").val(),
        success: function(data){
            $("#tblResult").html(data);
        }
    });
}

function showModal(){
    if($("#cbOrigem").val()!="" && $("#cbDestino").val()!=""){
        if($("#cbOrigem").val()!=$("#cbDestino").val()){
            $("#modalProductMultiple").modal({
                backdrop : 'static'
            });
        }else{
            bootbox.alert("O estoque de destino deve ser diferente do estoque de origem!");
        }
    }else{
        bootbox.alert("Por favor selecione a origem e o destino antes de selecionar o(s) produto(s)!");
    }
}
</script>