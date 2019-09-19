<br/><form class="form">
    <div class="card">
        <div class="card-header">
        	<div class="row">
        		<div class="col-sm">
        			<i class="fas fa-angle-right"></i> Visualiza&ccedil;&atilde;o de Conta
        		</div>
        		<div class="col-sm text-right">
        			<a href="javascript:window.print()" class="btn btn-outline-secondary btn-sm"><i class="fas fa-print"></i> Imprimir</a>
	                <?php if($conta->DATA_PAGAMENTO==NULL): ?>
	                <button type="button" id="btnDel" name="btnDel" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash-alt"></i> Excluir</button>
	                <a href="<?php echo $this->Url->build('financial/calendar_event_edit/'.$conta->IDCONTASPAGAR);?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit"></i> Editar</a>
	                <button type="button" id="btnPay" name="btnPay" class="btn btn-outline-success btn-sm"><i class="fas fa-hourglass-end"></i> Finalizar</button>
	                <?php endif; ?>
        		</div>
        	</div>
        </div>
        <div class="card-body">
            <fieldset>
                <legend>Dados da Conta</legend>
                <div class="row">
                    <div class="col-sm">
                        <label class="control-label">Conta</label>
                        <p><?php echo $conta->IDCONTASPAGAR; ?></p>
                    </div>
                    <div class="col-sm">
                        <label class="control-label">Data de Vencimento</label>
                        <p><?php echo $conta->DATA_VENCIMENTO->format("d/m/Y"); ?></p>
                    </div>
                    <div class="col-sm">
                        <label class="control-label">Valor Original</label>
                        <p><?php echo $this->Number->currency($conta->VALOR_ORIGINAL,"BRL"); ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm">
                        <label class="control-label">N&uacute;m. do Documento</label>
                        <p"><?php echo $conta->NUM_DOCUMENTO; ?></p>
                    </div>
                    <div class="col-sm">
                        <label class="control-label">Tipo de Despesa</label>
                        <p><?php echo $despesa_conta->NOME; ?></p>
                    </div>
                    <div class="col-sm">
                        <label class="control-label">Observa&ccedil;&atilde;o</label>
                        <p><?php echo $conta->OBSERVACAO; ?></p>
                    </div>
                </div>
            </fieldset><br/>
            <fieldset>
                <legend>Dados do Pagamento</legend>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Valor Pago</th>
                            <th>Diferen&ccedil;a</th>
                            <th>Despesa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($conta->DATA_PAGAMENTO!=NULL): ?>
                        <tr>
                            <td><?php echo ($conta->DATA_PAGAMENTO!=NULL)?$conta->DATA_PAGAMENTO->format("d/m/Y"):""; ?></td>
                            <td><?php echo $this->Number->currency($conta->VALOR_PAGO,"BRL"); ?></td>
                            <td><?php echo $this->Number->currency($conta->DIFERENCA_PAGAMENTO,"BRL");?></td>
                            <td><?php if(isset($despesa_pagamento)){ echo $despesa_pagamento->NOME; }?></td>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <td style="max-width:350px!important">
                                <div class="input-group date">
                                    <input type="text" class="form-control" id="txtDataPagamento" name="txtDataPagamento" autocomplete="off" value="<?php echo date("d/m/Y"); ?>"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                            </td>
                            <td><input type='text' class='form-control' id='txtValorPago' name="txtValorPago"></td>
                            <td><input type='text' class='form-control' id='txtDiferenca' name="txtDiferenca" readonly=""></td>
                            <td><select class='form-control' id='cbTipoDespesaPagamento' name='cbTipoDespesaPagamento' disabled>
                                    <option value=''></option>
                                    <?php foreach($despesalist as $despesa):?>
                                    <option value='<?php echo $despesa->IDTIPODESPESA; ?>'><?php echo $despesa->NOME; ?></option>
                                    <?php endforeach; ?>
                               </select></td>
                        </tr>
                        <?php endif;?>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){  
    $("#txtDataPagamento").mask("00/00/0000");
    
    <?php if($conta->VALOR_PAGO==0): ?>
    $("#txtValorPago").mask("#,##0.00", 
    {
        reverse: true,
        onKeyPress: function(valor, event, currentField, options){
            var original = <?php echo $conta->VALOR_ORIGINAL; ?>;
            var diferenca = valor.replace(",","")-original;
            if(diferenca > 0){
                $("#cbTipoDespesaPagamento").removeAttr("disabled");
            }else{
                $("#cbTipoDespesaPagamento").attr("disabled","");
            }
            $("#txtDiferenca").val(diferenca.toFixed(2));
        }
    });
    <?php endif; ?>
});

$(document).on("click", "#btnDel", function() {
    bootbox.dialog({message:"Deseja realmente excluir esta conta?", 
    buttons:{
        yes:{
            label:"Sim",
            callback:function(){
                $.ajax({
                    url: '<?=$this->Url->build("/financial/calendar_event_remove/")?><?php echo $conta->IDCONTASPAGAR; ?>',
                    success: function(data){
                        if(data==true){
                            $("#alertSuccess").html("<strong>Sucesso!</strong> Conta exclu&iacute;da com sucesso!");
                            $("#alertSuccess").show();
                            $("#alertSuccess").fadeTo(2500,500).slideDown(500,function(){
                                $("#alertSuccess").hide();
                                $("#btnCloseModal",window.parent.document).click();
                            });
                        }else{
                            $("#alertFail").html("Problema!</strong> Ocorreu um erro ao tentar excluir a conta!");
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

$(document).on("click", "#btnPay", function(){
    var diferenca = parseFloat( $("#txtDiferenca").val().replace(",","") );

    if( (diferenca > 0 && $("#cbTipoDespesaPagamento").val()!="") || diferenca == 0){
        bootbox.dialog({message:"Deseja realmente marcar esta conta como paga?", 
            buttons:{
                yes:{
                    label:"Sim",
                    callback:function(){
                        $.ajax({
                            method: 'post',
                            data: {
                                IDCONTASPAGAR   : "<?php echo $conta->IDCONTASPAGAR; ?>",
                                DATA_PAGAMENTO  : $("#txtDataPagamento").val(),
                                VALOR_PAGAMENTO : $("#txtValorPago").val(),
                                TIPO_DESPESA_PAGAMENTO    : $("#cbTipoDespesaPagamento").val()
                            },
                            url: '<?=$this->Url->build("/financial/calendar_event_data_save/")?>',
                            success: function(data){
                                if(data==true){
                                    $("#alertSuccess").show();
                                    $("#alertSuccess").fadeTo(2500,500).slideDown(500,function(){
                                        $("#alertSuccess").hide();
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
    }else{
        bootbox.alert("Por favor selecione o tipo de despesa da diferen&ccedil;a!");
        $("#txtDiferenca").focus();
    }
    return false;
});
</script>