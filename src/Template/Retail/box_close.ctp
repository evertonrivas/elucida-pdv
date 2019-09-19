<div class="card">
	<div class="card-header">
	    <h6 class="card-title"><i class="fas fa-angle-right"></i> Fechamento de Caixa</h6>
	</div>
	<div class="card-body">
	  <form id="frmRegs" name="frmRegs" method="post" action="<?php echo $this->Url->build("/retail/box_close_check")?>" class="form-horizontal needs-validation" novalidate>
	  	<input type="hidden" id="_csrfToken" name="_csrfToken" value=<?= json_encode($this->request->getParam('_csrfToken')) ?>>
	      <div class="row">
	          <div class="form-group col-3">
	            <label class="control-label">Dinheiro Total</label>
	          </div>
	          <div class="form-group col-9">
	              <div class="input-group">
	                  <div class="input-group-prepend">
	                  	<span class="input-group-text">R$</span>
	                  </div>
	                  <input type="text" class="form-control text-right" id="valorCaixa" name="valorCaixa" placeholder="0.00"<?php if(isset($caixa_status)){ if($caixa_status==2){ echo " disabled"; } }?> value="<?php echo ($valorCaixa!="")?$this->Number->precision($valorCaixa,2):""; ?>" required>
	                  <div class="input-group-append">
	                  	<button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalCalculator" data-backdrop="static"<?php if(isset($caixa_status)){ if($caixa_status==2){ echo " disabled"; } }?>><span class="fa fa-calculator"></span></button>
	                  </div>
	              </div>
	          </div>
	      </div>
	      <div class="row">
	        <table class="table table-striped">
	        	<thead>
	                <tr>
	                    <th>&nbsp;</th>
	                    <th>Bandeira</th>
	                    <th>Cielo</th>
	                    <th>Rede</th>
	                </tr>
	            </thead>
	            <tbody>
	            <?php foreach($bandeiras as $bandeira): ?>
	            <tr>
	                <td><img  src="/img/<?php echo $bandeira->ICONE; ?>" border="0"/><input type="hidden" id="txtIdBandeira[]" name="txtIdBandeira[]" value="<?php echo $bandeira->IDBANDEIRA; ?>"></td>
	                <td><?php echo $bandeira->NOME; ?></td>
	                <td>
	                    <input type="text" class="form-control text-right" id="valorMaquina1[]" name="valorMaquina1[]" placeholder="0.00"<?php if(isset($caixa_status)){ if($caixa_status==2){ echo " disabled"; } }?> required/>
	                </td>
	                <td>
	                    <input type="text" class="form-control text-right" id="valorMaquina2[]" name="valorMaquina2[]" placeholder="0.00"<?php if(isset($caixa_status)){ if($caixa_status==2){ echo " disabled"; } }?> required/>
	                </td>
	            </tr>
	            <?php endforeach; ?>
	            </tbody>
	        </table>
	      </div>
	      <div class="form-group text-right">
	            <button type="submit" class="btn btn-primary"<?php if(isset($caixa_status)){ if($caixa_status==2){ echo " disabled"; } }?>><i class="fas fa-door-closed"></i> Fechar caixa</button>
	      </div>
	  </form>
	</div>
</div>


<!-- MODAL CALCULADORA -->
<div class="modal fade" id="modalCalculator" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <form id="frmFindProduct" class="form">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Calculadora</h4>
            </div>
            <div class="modal-body">
                <table class="table">
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
                            <td><input type="number" class="form-control text-right" id="txtNota100" name="txtNota100" autocomplete="off"></td>
                            <td>&nbsp;</td>
                            <td>R$ 50,00</td>
                            <td><input type="number" class="form-control text-right" id="txtNota50" name="txtNota50" autocomplete="off"></td>
                        </tr>
                        <tr>
                            <td>R$ 20,00</td>
                            <td><input type="number" class="form-control text-right" id="txtNota20" name="txtNota20" autocomplete="off"></td>
                            <td>&nbsp;</td>
                            <td>R$ 10,00</td>
                            <td><input type="number" class="form-control text-right" id="txtNota10" name="txtNota10" autocomplete="off"></td>
                        </tr>
                        <tr>
                            <td>R$ 5,00</td>
                            <td><input type="number" class="form-control text-right" id="txtNota5" name="txtNota5" autocomplete="off"></td>
                            <td>&nbsp;</td>
                            <td>R$ 2,00</td>
                            <td><input type="number" class="form-control text-right" id="txtNota2" name="txtNota2" autocomplete="off"></td>
                        </tr>
                        <tr>
                            <td>R$ 1,00</td>
                            <td><input type="number" class="form-control text-right" id="txtNota1" name="txtNota1" autocomplete="off"></td>
                            <td>&nbsp;</td>
                            <td>R$ 0,50</td>
                            <td><input type="number" class="form-control text-right" id="txtNota050" name="txtNota050" autocomplete="off"></td>
                        </tr>
                        <tr>
                            <td>R$ 0,25</td>
                            <td><input type="number" class="form-control text-right" id="txtNota025" name="txtNota025" autocomplete="off"></td>
                            <td>&nbsp;</td>
                            <td>R$ 0,10</td>
                            <td><input type="number" class="form-control text-right" id="txtNota010" name="txtNota010" autocomplete="off"></td>
                        </tr>
                        <tr>
                            <td>R$ 0,05</td>
                            <td colspan="4"><input type="number" class="form-control text-right" id="txtNota005" name="txtNota005" autocomplete="off"></td>                            
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnCalcIt">Calcular</button>
            </div>
        </div>
    </div>
    </form>
</div>

<script>
$(document).ready(function() {
	
	<?php if(isset($caixa_status)): ?>
	    <?php if($caixa_status==2): ?>
	    	bootbox.alert("<p><strong class='text-warning'>O caixa j&aacute; encontra-se fechado!</strong><br/><br/>Primeiro abra o caixa para realizar um novo fechamento.</p>");
	    <?php elseif($caixa_status==3): ?>
	    	bootbox.alert("<p><strong class='text-danger'>Caixa Anterior Ainda Aberto!</strong><br/><br/>O caixa do <strong>per&iacute;odo anterior</strong> ainda encontra-se aberto, Por favor <u>realize o fechamento</u> do mesmo, e abertura do atual para realizar novas vendas.</p>");
	    <?php endif;?>
	<?php endif; ?>
	
	
    $('#valorCaixa').mask("#,##0.00", {reverse: true});
    $("input[name='valorMaquina1[]']").each(function(){
    	$(this).mask("#,##0.00", {reverse: true});
    });    
    $("input[name='valorMaquina2[]']").each(function(){
    	$(this).mask("#,##0.00", {reverse: true});
    });
});

$("#btnCalcIt").on("click",function(){
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
    $("#valorCaixa").val( total.toFixed(2) );
});
</script>