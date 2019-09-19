<br/><div class="container container-width">
    <?php if(isset($caixa_status)): ?>
        <?php if($caixa_status==1): ?>
            <div class="alert alert-warning"><strong>O caixa j&aacute; encontra-se aberto!</strong><br/>Primeiro feche o caixa aberto para realizar nova abertura.</div>
        <?php elseif($caixa_status==4):; ?>
            <div class="alert alert-info"><strong>Caixa Fechado!</strong><br/>Por favor abra o caixa antes de realizar qualquer venda no sistema.</div>
        <?php endif;?>
    <?php endif; ?>
    <div class="card">
        <div class="card-header">
        	<i class="fas fa-angle-right"></i> Abertura de Caixa
        </div>
        <div class="card-body">
	    <form id="frmRegs" name="frmRegs" method="post" class="form form-horizontal">
	    	<div class="form-group">
                <label>Usu&aacute;rio</label>
                <p><?php echo $user['username']; ?></p>
            </div>
            <div class="form-group">
                <label>Per&iacute;odo</label>                           
                <select class="form-control" id="cbPeriodo" name="cbPeriodo"<?php if(isset($caixa_status)){ if($caixa_status==1 || $caixa_status==-2){ echo " disabled"; } }?>>
                    <option value="1">Manh&atilde;</option>
                    <option value="2">Tarde</option>
                </select>
            </div>
            <div class="form-group">
                <label>Fundo</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                    	<span class="input-group-text">R$</span>
                    </div>
                    <input type="text" class="form-control" id="valorFundoCaixa" placeholder="0.00"<?php if(isset($caixa_status)){ if($caixa_status==1 || $caixa_status==-2){ echo " disabled"; } }?>>
                    <span class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalCalculator" data-backdrop="static"<?php if(isset($caixa_status)){ if($caixa_status==1 || $caixa_status==-2){ echo " disabled"; } }?>><span class="fa fa-calculator"></span></button>
                  </span>
                </div>
            </div>
            <div class="form-group text-right">
            	<button type="submit" class="btn btn-primary"<?php if(isset($caixa_status)){ if($caixa_status==1 || $caixa_status==-2){ echo " disabled"; } }?>>Abrir Caixa</button>
            </div>
	    </form>
        </div>
    </div>
</div>

<!-- MODAL CALCULADORA -->
<div class="modal fade" id="modalCalculator" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <form id="frmCalc" class="form">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h4 class="modal-title" id="myModalLabel">Calculadora</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Nota</th>
                            <th>Quantidade</th>
                            <th>&nbsp;</th>
                            <th>Nota</th>
                            <th>Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>R$ 100,00</td>
                            <td><input type="number" class="form-control text-right form-control-sm" id="txtNota100" name="txtNota100" autocomplete="off"></td>
                            <td>&nbsp;</td>
                            <td>R$ 50,00</td>
                            <td><input type="number" class="form-control text-right form-control-sm" id="txtNota50" name="txtNota50" autocomplete="off"></td>
                        </tr>
                        <tr>
                            <td>R$ 20,00</td>
                            <td><input type="number" class="form-control text-right form-control-sm" id="txtNota20" name="txtNota20" autocomplete="off"></td>
                            <td>&nbsp;</td>
                            <td>R$ 10,00</td>
                            <td><input type="number" class="form-control text-right form-control-sm" id="txtNota10" name="txtNota10" autocomplete="off"></td>
                        </tr>
                        <tr>
                            <td>R$ 5,00</td>
                            <td><input type="number" class="form-control text-right form-control-sm" id="txtNota5" name="txtNota5" autocomplete="off"></td>
                            <td>&nbsp;</td>
                            <td>R$ 2,00</td>
                            <td><input type="number" class="form-control text-right form-control-sm" id="txtNota2" name="txtNota2" autocomplete="off"></td>
                        </tr>
                        <tr>
                            <td>R$ 1,00</td>
                            <td><input type="number" class="form-control text-right form-control-sm" id="txtNota1" name="txtNota1" autocomplete="off"></td>
                            <td>&nbsp;</td>
                            <td>R$ 0,50</td>
                            <td><input type="number" class="form-control text-right form-control-sm" id="txtNota050" name="txtNota050" autocomplete="off"></td>
                        </tr>
                        <tr>
                            <td>R$ 0,25</td>
                            <td><input type="number" class="form-control text-right form-control-sm" id="txtNota025" name="txtNota025" autocomplete="off"></td>
                            <td>&nbsp;</td>
                            <td>R$ 0,10</td>
                            <td><input type="number" class="form-control text-right form-control-sm" id="txtNota010" name="txtNota010" autocomplete="off"></td>
                        </tr>
                        <tr>
                            <td>R$ 0,05</td>
                            <td colspan="4"><input type="number" class="form-control text-right form-control-sm" id="txtNota005" name="txtNota005" autocomplete="off"></td>                            
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm" id="btnCalcIt"><i class="fas fa-calculator"></i> Calcular</button>
            </div>
        </div>
    </div>
    </form>
</div>

<script>
$(document).ready(function(){
    $('#valorFundoCaixa').mask("#,##0.00", {reverse: true});    
});

$(document).on("submit","#frmRegs",function(event){
    event.preventDefault();
    
    if($("#valorFundoCaixa").val()!=""){
        var dataForm = {
            PERIODO_ABERTURA : $("#cbPeriodo").val(),
            VALOR_ABERTURA   : $("#valorFundoCaixa").val()
        };
        
        $.ajax({
        	headers:{
				'X-CSRF-Token':csrf
			},
            method:'post',
            data: dataForm,
            url: '<?=$this->Url->build("/retail/box_open_execute")?>',
            dataType:'json',
            success:function(data){
                if(data==true){
                	bootbox.alert("Abertura de caixa realizada com sucesso.",function(){ document.location.href='/retail/pos'; });
                }
                if(data==false){
                	bootbox.alert("Ocorreu um problema ao tentar abrir o caixa, por favor verifique.");
                }
                if(data==-1){
                	bootbox.alert("Por favor revise a contagem do valor do caixa, foram encontras diverg&ecirc;ncias.");
                }
            }
        });
    }else{
        bootbox.alert('Por favor informe o valor do Fundo de Caixa');
        $("#valorFundoCaixa").focus();
    }
});

$(document).on("submit","#frmCalc",function(event){
	event.preventDefault();
	
    $("#modalCalculator").modal("hide");
    
    var total = ($("#txtNota100").val()*100)+
            ($("#txtNota50").val()*50)+
            ($("#txtNota20").val()*20)+
            ($("#txtNota10").val()*10)+
            ($("#txtNota5").val()*5)+
            ($("#txtNota2").val()*2)+
            ($("#txtNota1").val()*1)+
            ($("#txtNota050").val()*0.50)+
            ($("#txtNota025").val()*0.25)+
            ($("#txtNota010").val()*0.10)+
            ($("#txtNota005").val()*0.05);
    $("#valorFundoCaixa").val( total.toFixed(2) );
});
</script>