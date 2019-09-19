<?php $time = new Cake\I18n\Time();?>
<form class="form">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-sm"><i class="fas fa-angle-right"></i> Visualiza&ccedil;&atilde;o de Venda</div>
                <div class="col-sm text-right">
                    <a href="javascript:window.print()" class="btn btn-outline-secondary btn-sm"><i class="fas fa-print"></i> Imprimir</a>
                    <!--<button type="button" id="btnEstorno" name="btnEstorno" class="btn btn-warning<?php /*if($diferenca_data!=0 || $tem_nota_fiscal){ echo " disabled"; }*/ ?>"><span class="glyphicon glyphicon-off"></span> Estornar</button>-->
                    <?php if(!$tem_nota_fiscal):?>
                    <button type="button" id="btnNfce" name="btnNfce" class="btn btn-success btn-sm"><i class="fab fa-google-wallet"></i> NFC-e</button>
                    <?php else: ?>
                    <a href='<?php echo $this->Url->build('/tributary/invoice_sended_show/'.$nfce->IDNFCE.'/'.$venda->IDLOJA);?>' class="btn btn-primary btn-sm"><i class="fas fa-file-alt"></i> Danfe</a>
                    <a href='javascript:sendByMail(<?=$venda->IDVENDA?>,<?=$nfce->IDNFCE?>)' class="btn btn-warning btn-sm"><i class="far fa-envelope"></i> Por E-mail</a>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <div class="card-body">
            <fieldset>
                <legend>Dados da Venda</legend>
                <div class="row">
                    <div class="col-sm-2">
                        <label class="control-label">Venda</label>
                        <p><?php echo $venda->IDVENDA; ?></p>
                    </div>
                    <div class="col-sm-3">
                        <label class="control-label">Data da Venda</label>
                        <p><?php echo $venda->DATA_VENDA->format("d/m/Y H:i:s"); ?></p>
                    </div>
                    <div class="col-sm-4">
                        <label class="control-label">Cliente</label>
                        <p><?php if(isset($cliente)) { echo $cliente->NOME; } ?></p>
                    </div>
                    <div class="col-sm-3">
                        <label class="control-label">Vendedor</label>
                        <p><?php if(isset($employer)) { echo $employer->APELIDO; } ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <label class="control-label">Valor Bruto</label>
                        <p><?php echo $this->Number->currency($venda->SUBTOTAL,"BRL"); ?></p>
                    </div>
                    <div class="col-sm-3">
                        <label class="control-label">Troco</label>
                        <p><?php echo $this->Number->currency($venda->TROCO,"BRL"); ?></p>
                    </div>
                    <div class="col-sm-3">
                        <label class="control-label">Desconto</label>
                        <p><?php echo $this->Number->currency($venda->DESCONTO,"BRL"); ?></p>
                    </div>
                    <div class="col-sm-3">
                        <label class="control-label">Valor Pago</label>
                        <p><?php echo $this->Number->currency($venda->VALOR_PAGO,"BRL"); ?></p>
                    </div>
                </div>
            </fieldset><br/>
            <fieldset>
                <legend>Meio(s) de Pagamento</legend>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Meio de Pagamento</th>
                                <th>Valor Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($PAGAMENTOS as $PAGAMENTO): ?>
                            <tr>
                                <td><?php echo $PAGAMENTO['CP']['IDCONDICAOPAGAMENTO']; ?></td>
                                <td><?php echo $PAGAMENTO['CP']['NOME']; ?></td>
                                <td><?php echo $this->Number->currency($PAGAMENTO->VALOR,"BRL");?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </fieldset><br/>
            <fieldset>
                <legend>Produto(s)</legend>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Pre&ccedil;o Unit&aacute;rio</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ITENS_VENDA as $ITEM): ?>
                                <tr>
                                    <td><?php echo $ITEM->IDPRODUTO; ?></td>
                                    <td><?php echo $ITEM->NOME_PRODUTO; ?></td>
                                    <td><?php echo $ITEM->QUANTIDADE; ?></td>
                                    <td><?php echo $this->Number->currency($ITEM->PRECO_UNITARIO,"BRL"); ?></td>
                                    <td><?php echo $this->Number->currency($ITEM->SUBTOTAL,"BRL"); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>
    </div>
</form>

<script>

$(document).on("click", "#btnEstorno", function() {
    bootbox.dialog({message:"Deseja realmente estornar esta venda?",
    buttons:{
        yes:{
            label:"Sim",
            callback:function(){
                $.ajax({
                    url: '<?=$this->Url->build("/retail/sale_reverse/")?><?php echo $venda->IDVENDA; ?>',
                    success: function(data){
                        if(data==true){
                            $("#alertSuccess").show();
                            $("#alertSuccess").fadeTo(2500,500).slideDown(500,function(){
                                $("#alertSuccess").hide();
                                $("#btnCloseModal",window.parent.document).click();
                                document.location.reload();
                            });
                        }else{
                             $("#alertFail").show();
                             $("#alertFail").fadeTo(2500,500).slideDown(500,function(){
                                 $("#alertFail").hide();
                             });
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
    return false;
});

$(document).on("click","#btnNfce",function(){
    bootbox.dialog({
        message:"Deseja realmente emitir uma NFC-e para esta venda?",
        buttons:{
            yes:{
                label:"Sim",
                className:"btn-primary",
                callback:function(){
                    $.ajax({
                        url: '<?=$this->Url->build("/tributary/invoice_resend/").$venda->IDVENDA?>',
                        dataType:'json',
                        success: function(data){
                            if(data.IDNFCE>0){
                                bootbox.alert("NFC-e <strong>"+data.IDNFCE+"</strong> emitida com sucesso!",function(){ document.location.href='<?=$this->Url->build("/tributary/invoice_sended_show/")?>'+data.IDNFCE+'/<?php echo $venda->IDLOJA; ?>'; });
                            }else{
                                bootbox.alert("Ocorreu o seguinte problema ao tentar emitir a NFC-e da venda!<br/>"+data.STATUS);
                            }
                        }
                    });
                }
            },
            cpf:{
                label:"Com CPF",
                className:"btn-warning",
                callback:function(){
                    $.ajax({
                        url: '<?=$this->Url->build("/tributary/invoice_resend/").$venda->IDVENDA?>/1',
                        dataType:'json',
                        success: function(data){
                            if(data.IDNFCE>0){
                                bootbox.alert("NFC-e <strong>"+data.IDNFCE+"</strong> emitida com sucesso usando CPF na nota!",function(){ document.location.href='<?=$this->Url->build("/tributary/invoice_sended_show/")?>'+data.IDNFCE+'/<?php echo $venda->IDLOJA; ?>'; })
                            }else{
                                bootbox.alert("Ocorreu o seguinte problema ao tentar emitir a NFC-e com CPF!<br/>"+data.STATUS);
                            }
                        }
                    });
                }
            }
            ,
            no:{
                label:"N\u00e3o"
            }
        }
    });
    return false;
});
/*
function sendByMail(idVenda,idNFCe){
    $.ajax({
        url:'<?=$this->Url->build("tributary/invoice_sended_mail/")?>'+idVenda+'/'+idNFCe+'/true',
        success:function(data){
            if(data==true){
            	bootbox.alert("Nota fiscal enviada por e-mail!");
            }else{
            	bootbox.alert("Ocorreu um problema ao tentar enviar a nota por e-mail!");
            }
        }
    });
}*/
</script>
