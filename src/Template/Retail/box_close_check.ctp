<?PHP $diferenca_total = 0;?>
<br/><form class="form" id="frmRegs" name="frmRegs">
    <input type="hidden" id="VALOR_FECHAMENTO" name="VALOR_FECHAMENTO" value="<?php echo $valor_fechamento; ?>"/>
    <div class="card">
        <div class="card-header">
        	<div class="row">
        		<div class="col">
        			<h6 class="card-title"><i class="fas fa-angle-right"></i> Verifica&ccedil;&atilde;o de Fechamento de Caixa</h6>
        		</div>
        		<div class="col text-right">
        			<a href="#" onclick="telaAnterior()" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
                <button type="submit" id="btnClose" name="btnClose" class="btn btn-primary btn-sm"<?php if((number_format($diferenca_total,2)+number_format($sobra_ou_falta,2) )<0){ echo " disabled"; }?>><i class="fas fa-door-closed"></i> Fechar Caixa</button>
        		</div>
        	</div>
        </div>
        <div class="card-body">
            <fieldset>
                <legend>Apura&ccedil;&atilde;o Dinheiro</legend>
                <div class="row">
	                <div class="col-3">
	                    <label>Sobra/Falta</label>
	                    <p><?php echo $this->Number->currency($sobra_ou_falta,"BRL") ?> = (&nbsp;&nbsp;( <label>Abertura de Caixa</label></p>
	                </div>
	                <div class="col-3">
	                    <label>Vendas em Dinheiro</label>
	                    <p>+&nbsp;&nbsp;<?php echo $this->Number->currency($venda_total_dinheiro,"BRL"); ?> ) </p>
	                </div>
	                <div class="col-3">
	                    <label>Retiradas</label>
	                    <p class="text-danger">- &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->Number->currency($total_retirada,"BRL"); ?> ) </p>
	                </div>
	                <div class="col-3">
	                    <label>Total do Caixa</label>
	                    <p><?php echo $this->Number->currency($valor_fechamento,"BRL");?></p>
	                </div>
                </div>
            </fieldset>
            <fieldset>
                <legend>Retiradas</legend>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo de Despesa</th>
                            <th>Observa&ccedil;&atilde;o</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($retiradalist)): ?>
                        <?php foreach($retiradalist as $retirada): ?>
                        <tr>
                            <th role="row"><?php echo $retirada->IDCAIXARETIRADA; ?></th>
                            <td><?php echo $retirada->NOME_TIPO_DESPESA; ?></td>
                            <td><?php echo $retirada->OBSERVACAO; ?></td>
                            <td><?php echo $this->Number->currency($retirada->VALOR,"BRL"); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </fieldset>
            <fieldset>
                <legend>Meios de Pagamento</legend>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Meio de Pagamento</th>
                            <th class="text-right">Valor Apurado</th>
                            <th class="text-right">Valor Informado</th>
                            <th class="text-right">Diferen&ccedil;a</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(isset($pagamentolist)):
                        foreach($pagamentolist as $pagamento): ?>
                        <tr>
                            <th role="row"><?php echo $pagamento->IDMEIOPAGAMENTO; ?><input type="hidden" id="IDMEIOPAGAMENTO[]" name="IDMEIOPAGAMENTO[]" value="<?php echo $pagamento->IDMEIOPAGAMENTO; ?>"></th>
                            <td><?php echo $pagamento->NOME; ?><input type="hidden" id="MEIO_PAGAMENTO[]" name="MEIO_PAGAMENTO[]" value="<?php echo $pagamento->IDMEIOPAGAMENTO; ?>"></td>
                            <td class="text-right"><?php echo $this->Number->currency($pagamento->VALOR_APURADO,"BRL"); ?><input type="hidden" id="VALOR_APURADO[]" name="VALOR_APURADO[]" value="<?php echo $pagamento->VALOR_APURADO; ?>"></td>
                            <td class="text-right"><?php echo $this->Number->currency($pagamento->VALOR_DIGITADO); ?><input type="hidden" id="VALOR_DIGITADO[]" name="VALOR_DIGITADO[]" value="<?php echo $pagamento->VALOR_DIGITADO; ?>"></td>
                            <td class="text-right"><?php echo $this->Number->currency(number_format($pagamento->VALOR_APURADO,2)-number_format($pagamento->VALOR_DIGITADO,2),"BRL"); ?></td>
                        </tr>
                        <?php 
                        $diferenca_total += number_format($pagamento->VALOR_DIGITADO,2)-number_format($pagamento->VALOR_APURADO,2);
                        endforeach;
                        endif; ?>
                        <tr>
                            <td colspan="4" class="text-right"><strong>Diferen&ccedil;a Total:</strong></td>
                            <td class="text-right"><?php echo $this->Number->currency($diferenca_total,"BRL");?></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
            <fieldset>
                <legend>Sangria (Apenas Dinheiro)</legend>
                <div class="form-group">
                    <label class="control-label">Valor</label>
                    <input type="text" name="VALOR_SANGRIA" id="VALOR_SANGRIA" class="form-control" placeholder="0.00"/>
                </div>
            </fieldset>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    
    $("#VALOR_SANGRIA").mask("#,##0.00", {reverse: true});
    
    $("#frmCloseCaixa").submit(function(event){
        event.preventDefault();
        $.ajax({
        	headers:{
				'X-CSRF-Token':csrf
			},
            method: 'post',
            url: '<?=$this->Url->build("/retail/box_close_execute")?>',
            data: $("#frmCloseCaixa").serialize(),
            success: function(data){
                if(data==true){
                	bootbox.alert("Fechamento de caixa realizado com sucesso!",function(){ goClose(); });
                }else{
                    bootbox.alert("Ocorreu um erro ao tentar realizar o fechamento de caixa, verifique!");
                }
            }
        });
    });
});
function telaAnterior(){
    bootbox.dialog({
        message:"Ao retornar &agrave; tela anterior os valores n&atilde;o ser&atilde;o mantidos, deseja realmente continuar?", 
        buttons:{
            yes:{
                label:"Sim",
                callback:function(){
                    document.location.href='<?php echo $this->Url->build('/retail/box_close?valorCaixa='.$this->request->data("valorCaixa")); ?>';
                }
            },
            no:{
                label:"N\u00e3o"			
            }
        }
    });
    return false;
}

function clearScreen(){
    $("input[name='IDGRUPOPAGAMENTO[]']").each(function(){
        $(this).val("");
    });
    
    $("input[name='GRUPO_PAGAMENTO[]']").each(function(){
        $(this).val("");
    });
    
    $("input[name='VALOR_APURADO[]']").each(function(){
        $(this).val("");
    });
    
    $("input[name='VALOR_DIGITADO[]']").each(function(){
        $(this).val("");
    });
    
    $("#VALOR_FECHAMENTO").val("");
    
    //desabilita o botao para evitar uma nova tentativa de fechamento
    $("#btnClose").attr("disabled");
}

function goClose(){
    document.location.href='<?=$this->Url->build("/system")?>';
}
</script>