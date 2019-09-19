<?php /*$disable_indication = "";
 $text_problem_indication = "";
if($NEW_CUSTOMER!="1"){
	$disable_indication = " disabled=''";
	$text_problem_indication = "<span class='text-danger'>Apenas novos clientes podem gerar indica&ccedil;&atilde;o</span>";
}elseif($totais->SUBTOTAL<=$VALUE_MIN_INDICATION){
	$disable_indication = " disabled=''";
	$text_problem_indication = "<span class='text-warning'>Apenas compras acima de ".$this->Number->currency($VALUE_MIN_INDICATION,"BRL")." geram indica&ccedil;&atilde;o</span>";
}*/
?><br/>
<form id="frmRegs" name="frmRegs">
	<input type="hidden" id="_csrfToken" name="_csrfToken" value=<?= json_encode($this->request->getParam('_csrfToken')) ?>>
    <input type="hidden" name="txtIdCliente" id="txtIdCliente" value="<?php echo $IDCLIENTE; ?>"/>
    <!--<input type="hidden" name="txtNewCustomer" id="txtNewCustomer" value="<?php /*echo $NEW_CUSTOMER;*/ ?>"/>-->
    <!--<input type="hidden" name="txtIndicationDiscount" id="txtIndicationDiscount" value="<?php /*echo "$INDICATION_DISCOUNT";*/ ?>"/>-->
    <input type="hidden" name="txtIdFuncionario" id="txtIdFuncionario" value="<?php echo $IDFUNCIONARIO; ?>"/>
    <input type="hidden" name="txtCpfNaNota" id="txtCpfNaNota" value="<?php echo $CPF_NA_NOTA; ?>"/>
    <input type="hidden" name="txtCpfAlternativo" id="txtCpfAlternativo" value="<?php echo $CPF_ALTERNATIVO; ?>"/>
    <input type="hidden" name="txtValorAPagar" id="txtValorAPagar" value="<?php echo $totais->SUBTOTAL-$totais->DESCONTO; ?>"/>
    <input type="hidden" id="txtValorPago"/>
    <input type="hidden" id="txtCondicaoPromocao" value="<?php echo $CONDICAO_PROMOCAO; ?>"/>
    <input type="hidden" id="chkContraVale" value="0"/>

    <div class="card">
    	<div class="card-header">
        	<i class="fas fa-angle-right"></i> Fechamento de Venda
        </div>
        <div class="card-body">
        	<div class="row">
        		<div class="col-8">
        			<!--<div class="card">
	            		<div class="card-body">
	            			<div class="form-group">
	            				<label>Indicado por:</label>
	            				<div class="input-group input-group-sm">
	            					<input type="text" id="txtIndicacao" name="txtIndicacao" class="form-control input-sm"<?php /*$disable_indication*/ ?>/>
	            					<span class="input-group-append"><button type="button" id="btnSearchCustomer" name="btnSearchCustomer" class="btn btn-outline-secondary" title="Buscar cliente" <?php /*$disable_indication*/ ?>><i class="fas fa-user"></i> Buscar Cliente</button></span>
	            				</div>
	            				<span id="pNomeIndicacao"><?php /*$text_problem_indication*/ ?></span>
	            			</div>
	            		</div>
	            	</div>
	            	<br/>-->

	            	<div class="card">
	            		<div class="card-body">
	            			<div class="btn-group-vertical d-flex" role="group">
	            				<div class="btn-group d-flex" role="group">
	            			<?php $i = 1; foreach($condicoes_pagamento as $condicao): ?>
	                            <?php if($condicao->NOME!="DINHEIRO"): ?>
	                            <button type="button" class="btn btn-outline-secondary <?php if($CONDICAO_PROMOCAO!=0 && $CONDICAO_PROMOCAO!=$condicao->IDCONDICAOPAGAMENTO){ echo " disabled"; }?>" name="payments[]" onclick="paymentAdd(<?php echo $condicao->IDCONDICAOPAGAMENTO; ?>,<?php echo ($totais->SUBTOTAL-$totais->DESCONTO); ?>)" accesskey="<?php echo ord($condicao->ATALHO); ?>"><?php echo $condicao->NOME; ?> ( <?php echo $condicao->ATALHO; ?> )</button>
	                            <?php else: ?>
	                            <button type="button" class="btn btn-outline-secondary <?php if($CONDICAO_PROMOCAO!=0 && $CONDICAO_PROMOCAO!=$condicao->IDCONDICAOPAGAMENTO){ echo " disabled"; }?>" name="payments[]" onclick="paymentAdd(<?php echo $condicao->IDCONDICAOPAGAMENTO; ?>,0)" accesskey="<?php echo ord($condicao->ATALHO); ?>"><?php echo $condicao->NOME; ?> ( <?php echo $condicao->ATALHO; ?> )</button>
	                            <?php endif; ?>

	                            <?php if($i%3==0):?>
	                            </div><div class="btn-group d-flex" role="group">
	                            <?php elseif($i==$condicoes_pagamento->count()):?>
	                            </div>
	                            <?php endif; $i++;?>

	                        <?php endforeach; ?>
	                        </div>
	            		</div>
	            	</div>

	            	<div id="tblPay">

                    </div>
        		</div>
        		<div class="col-4">
        			<div class="card">
        				<ul class="list-group list-group-flush">
        					<li class="list-group-item text-right">
        						<label class="text-success font-weight-bold">Total em Produtos</label>
	                        	<span class="text-success"><?php echo $this->Number->currency($totais->SUBTOTAL,"BRL"); ?></span>
        					</li>
        					<li class="list-group-item text-right">
        						<label class="text-danger font-weight-bold">Desconto</label>
	                        	<span class="text-danger"><?php echo $this->Number->currency($totais->DESCONTO,"BRL"); ?></span>
        					</li>
        					<li class="list-group-item text-right">
        						<label class="text-warning font-weight-bold">Cupom</label>
	                        	<span class="text-warning"><?php echo $this->Number->currency($totais->VALOR_CUPOM,"BRL"); ?></span>
        					</li>
        					<li class="list-group-item text-right">
        						<label class="font-weight-bold text-info">&Agrave; pagar</label>
	                        	<span class="text-info"><?php echo $this->Number->currency($totais->SUBTOTAL-$totais->DESCONTO-$totais->VALOR_CUPOM,"BRL")?></span>
        					</li>
        					<li class="list-group-item text-right">
        						<label class="font-weight-bold text-secondary">Troco/Falta</label>
	                        	<span id="troco_falta" class="text-secondary"></span>
        					</li>
        				</ul>
        			</div>
        			<?php if($TIPO_EMISSAO_NOTA=="2"):?><br/>
        			<div class="custom-control custom-switch">
					  <input type="checkbox" class="custom-control-input" id="chkEmiteNota" name="chkEmiteNota" value="1">
					  <label class="custom-control-label" for="chkEmiteNota"> Emitir NFC-e para esta venda</label>
					</div>
        			<?php endif; ?>
					<!--<div class="custom-control custom-switch">
					  <input type="checkbox" class="custom-control-input" id="chkContraVale" name="chkContraVale" value="1">
					  <label class="custom-control-label" for="chkContraVale">Gerar Contra-vale com o troco</label>
					</div>-->
        		</div>
        	</div>
        </div>
        <div class="card-footer text-right">
        	<?php //antigo url com fidelidade do cliente que foi removido "?IDCLIENTE=$IDCLIENTE&CONDICAO_PROMOCAO=$CONDICAO_PROMOCAO&IDFUNCIONARIO=$IDFUNCIONARIO&OUTRO_CPF=$CPF_ALTERNATIVO&NEW_CUSTOMER=$NEW_CUSTOMER&INDICATION_DISCOUNT=$INDICATION_DISCOUNT"?>
        	<a href="<?php echo $this->Url->build('retail/pos')."?IDCLIENTE=$IDCLIENTE&CONDICAO_PROMOCAO=$CONDICAO_PROMOCAO&IDFUNCIONARIO=$IDFUNCIONARIO&OUTRO_CPF=$CPF_ALTERNATIVO"; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
            <button type="submit" class="btn btn-success btn-sm" disabled="" id="btnEfetivar" name="btnEfetivar"><i class="fas fa-check-circle"></i> Efetivar (F12)</button>
        </div>
    </div>
</form>

<!-- MODAL DE CONDICAO DE PAGAMENTO -->
<div class="modal fade" id="modalPagamento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <form id="frmPagamento" class="form">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h4 class="modal-title" id="myModalLabel">Informa&ccedil;&atilde;o de pagamento</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="txtValorPag">Valor &agrave; pagar</label>
                    <input type="text" id="txtValorPag" name="txtValorPag" class="form-control">
                    <input type="hidden" id="txtIdCondicao" name="txtIdCondicao"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> Cancelar</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check-square"></i> Confirmar</button>
            </div>
        </div>
    </div>
    </form>
</div>

<!--INICIO DO MODAL DE EXIBIÇÃO DA DANFE-->
<div class="modal fade" id="modalDanfe" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	<h4 class="modal-title" id="modalLabel">Exibi&ccedil;&atilde;o de DANFE</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
      	<input type="hidden" name="txtIdToOpen" id="txtIdToOpen"/>
        <iframe id="frmDanfe" name="frmDanfe" frameborder="0" style="min-height:600px; max-height:600px; overflow-y:scroll;width:100%"></iframe>
      </div>
    </div>
  </div>
</div>

<!-- MODAL DE FINALIZACAO DE VENDA-->
<div class="modal fade" id="modalEndSell" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modalLoading">Finalizando venda...</h4>
      </div>
      <div class="modal-body">
          <input type="hidden" id="txtShowPdf" value="0"/>
          <p id="txtModalEndSell">Salvando informa&ccedil;&otilde;es da venda.</p>
      </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnCloseModal" disabled="disabled">Fechar</button>
        </div>
    </div>
  </div>
</div>

<script>
(function (global) {

    if(typeof (global) === "undefined") {
        throw new Error("window is undefined");
    }

    var _hash = "#!";
    var noBackPlease = function () {
        global.location.href += "#";

        // making sure we have the fruit available for juice (^__^)
        global.setTimeout(function () {
            global.location.href += "!";
        }, 50);
    };

    global.onhashchange = function () {
        if (global.location.hash !== _hash) {
            global.location.hash = _hash;
        }
    };

    global.onload = function () {
        noBackPlease();

        // disables backspace on page except on input fields and textarea..
        document.body.onkeydown = function (e) {
            var elm = e.target.nodeName.toLowerCase();
            if (e.which === 8 && (elm !== 'input' && elm  !== 'textarea')) {
                e.preventDefault();
            }
            //tratamento das teclas de atalho
            $("button[name='payments[]']").each(function(){
                if($(this).attr("accesskey")==e.keyCode){
                    $(this).click();
                }

                if(e.keyCode==123){
                    $("#btnEfetivar").click();
                }
            });
            // stopping event bubbling up the DOM tree..
            e.stopPropagation();
        };
    };

})(window);

$(document).ready(function(){
    getPayments();
    calcTroco();
});

/*
$(document).on("click","#btnSearchCustomer",function(){
	if($("#txtIndicacao").val()!=$("#txtIdCliente").val()){
		$.ajax({
			headers:{
				'X-CSRF-Token':csrf
			},
	        method:'post',
	        data : {IDCLIENTE : $("#txtIndicacao").val()},
	        url:'<?=$this->Url->build("/retail/customer_get_info")?>',
	        dataType:'json',
	        success:function(data){
	        	$("#txtIndicacao").val(data.IDCLIENTE);
	            $("#pNomeIndicacao").html(data.NOME);
	        }
	    });
	}else{
		bootbox.alert('O Cliente da indica&ccedil;&atilde;o deve ser diferente do Cliente da compra!');
	}
});*/

$(document).on("submit","#frmRegs",function(event){
    event.preventDefault();

    $("#modalEndSell").modal({
        backdrop: 'static'
    });

    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method: 'post',
        url: '<?=$this->Url->build("/retail/sale_save/")?>',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(data){
            if(data.SUCCESS==true){
                if(data.IDDEVOLUCAO > 0){
                    $("#txtModalEndSell").html("Venda finalizada com sucesso! <br/>O n&uacute;mero do contra-vale da venda &eacute;: <strong>"+data.IDDEVOLUCAO+"</strong>");
                    $("#btnCloseModal").removeAttr("disabled");
                    $("#btnCloseModal").focus();
                }else{
                    if(data.EMITE_NFCE){
                        $("#txtModalEndSell").html("Transmitindo Nota Fiscal ao Consumidor Eletr&ocirc;nica!");

                        /*$.ajax({
                        	data:{
								IDVENDA    : data.IDVENDA,
								CPFNANOTA  : $("#txtCpfNaNota").val(),
								CPFALTERNA : $("#txtCpfAlternativo").val()
							},
                            url: '<?=$this->Url->build("/tributary/invoice_sale_send/")?>',
                            dataType:'json',
                            success:function(invData){
                                if(invData.IDNFCE>0){
                                    $("#txtShowPdf").val("1");
                                    $("#modalEndSell").modal('hide');
                                    showPdf('invoice',invData.IDNFCE);
                                }
                                else{
                                    $("#txtModalEndSell").html("Venda finalizada com sucesso, mas ocorreu o seguinte erro ao tentar transmitir a NFC-e:<br/><span class='text-danger'><pre>"+invData.STATUS+"</pre></span>.");
                                    $("#btnCloseModal").removeAttr("disabled");
                                    $("#btnCloseModal").focus();
                                }
                            }
                        });*/
                    }
                    else{
                        //aqui oculta o modal de fim de venda e exibe o modal danfe para exibir o cupom nao fiscal
                        $("#txtShowPdf").val("1");
                        $("#modalEndSell").modal("hide");
                        showPdf('ticket',data.IDVENDA);
                        /*$("#txtModalEndSell").html("Venda finalizada com sucesso!");
                        $("#btnCloseModal").removeAttr("disabled");
                        $("#btnCloseModal").focus();*/
                    }
                }
            }else{
                $("#txtModalEndSell").html("Ocorreu um problema ao tentar finalizar a venda, por favor verifique!");
                $("#btnCloseModal").removeAttr("disabled");
                $("#btnCloseModal").focus();
            }
        }
    });
});

$(document).on("submit","#frmPagamento",function(event){
    event.preventDefault();

    //$_idcondicaopagamento, $_valor, $print=true, $_operadora_cartao='',$_autentica=''
    var urlPayment = '<?=$this->Url->build("/retail/payment_add/")?>'+$("#txtIdCondicao").val()+'/'+$("#txtValorPag").val();

    $.ajax({
        url: urlPayment,
        success: function(data){
            if(data==true){
                calcTroco();
                getPayments();
            }
        }
    });

    $("#modalPagamento").modal('hide');
});

$(document).on("hide.bs.modal","#modalDanfe",function(){
    $("#frmDanfe").removeAttr("src");
    document.location.href = '<?=$this->Url->build("/retail/pos")?>';
});

$(document).on("hide.bs.modal","#modalEndSell",function(){
    if($("#txtShowPdf").val()==0){
	    document.location.href = '<?=$this->Url->build("/retail/pos")?>';
    }
});

$(document).on("shown.bs.modal","#modalPagamento",function(){
    $("#txtValorPag").focus().select();
});

function getPayments(){
    $.ajax({
        url: '<?=$this->Url->build("/retail/payments_get")?>',
        success: function(data){
            $("#tblPay").html(data);
        }
    });
}

function paymentAdd(idCondicaoPagamento,valor){

    $("#txtIdCondicao").val(idCondicaoPagamento);

    $("#modalPagamento").modal({
        backdrop: 'static'
    });

    $("#txtValorPag").mask("#,##0.00",{reverse: true});
    $("#txtValorPag").val((valor-$("#txtValorPago").val()).toFixed(2));
}

function paymentDel(idCondicaoPagamento){
    $.ajax({
        url: '<?=$this->Url->build("/retail/payment_del/")?>'+idCondicaoPagamento,
        success: function(data){
            if(data){
                getPayments();
                calcTroco();
            }
        }
    });
}

function calcTroco(){
    $.ajax({
        url: '<?=$this->Url->build("/retail/payment_get_total")?>',
        success: function(data){
            $("#txtValorPago").val(data);
            $("#troco_falta").html(  (data-$("#txtValorAPagar").val()).toFixed(2) );

            var diferenca = parseFloat(data-$("#txtValorAPagar").val());

            if(diferenca>=0){
                $("#btnEfetivar").removeAttr("disabled");
            }
            else{
                $("#btnEfetivar").attr("disabled","disabled");
            }
        }
    });
}

function showPdf(type,idNfceOrVenda){
    $("#modalDanfe").modal({
        backdrop: 'static'
    });

    var url = '';
    if(type=="ticket"){
        url = '<?=$this->Url->build("/retail/ticket/")?>'+idNfceOrVenda
    }else{
        url = '<?=$this->Url->build("/tributary/invoice_sended_show/")?>'+idNfceOrVenda;
    }
    $("#frmDanfe").attr("src",url);
}
</script>
