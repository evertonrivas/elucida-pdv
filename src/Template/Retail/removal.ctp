<br/>
<div class="card">
    <div class="card-header">
    	<i class="fas fa-angle-right"></i> Retirada de Caixa
    </div>
    <div class="card-body">
      <form id="frmRegs" name="frmRegs" method="post" class="needs-validation" action="<?php $this->Url->build("/caixa/removal")?>" novalidate>
      	<div class="form-row">
          <div class="form-group col-2">
			<label class="control-label">Usu&aacute;rio</label>
		  </div>
		  <div class="form-group col">
            <input type="text" class="form-control-plaintext" value="<?php echo $user['username']; ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-2">
              <label>Tipo de Despesa</label>
          </div>
          <div class="form-group col">
              <select class="form-control form-control-sm" name="cbTipoDespesa" id="cbTipoDespesa" required>
                  <option value="">&laquo; Selecione &raquo;</option>
                  <?php foreach($tipodespesalist as $tipodespesa):?>
                  <option value="<?php echo $tipodespesa->IDTIPODESPESA; ?>"><?php echo $tipodespesa->NOME; ?></option>
                  <?php endforeach; ?>
              </select>
          </div>
        </div>
          <div class="form-row">
          	<div class="form-group col-2">
              <label>Valor</label>
            </div>
			<div class="form-group col">
			  <div class="input-group input-group-sm">
			      <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
			      <input type="text" class="form-control" id="valorRetirada" placeholder="0,00" data-thousands="." data-decimal="," required>
			  </div>
			</div>
          </div>
          <div class="form-row">
          	<div class="form-group col-2">
            	<label>Observa&ccedil;&atilde;o</label>
            </div>
            <div class="form-group col">
            	<textarea class="form-control text-uppercase" rows="3" id="txtObservacao" name="txtObservacao" required></textarea>
          	</div>
          </div>
          <div class="form-row">
              <div class="form-group col text-right">
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
              </div>
          </div>
      </form>
    </div>
</div>
<script> 
$(document).ready(function(){
	$('#valorRetirada').mask("#,##0.00", {reverse: true});
});

$(document).on("submit","#frmRegs",function(event){
        event.preventDefault();
        
        var dataForm = {
            IDTIPODESPESA : $("#cbTipoDespesa").val(),
            VALOR         : $("#valorRetirada").val(),
            OBSERVACAO    : $("#txtObservacao").val()
        };
        
        $.ajax({
        	headers:{
				'X-CSRF-Token':csrf
			},
            method: 'post',
            data: dataForm,
            url: '<?=$this->Url->build("/retail/removal_save")?>',
            success: function(data){
                if(data==true){
                    bootbox.alert("Retirada de caixa registrada com sucesso!");
                }else{
                    bootbox.alert("Ocorreu um erro ao tentar registrar a retirada de caixa!");
                }
                clearScreen();
           }
        });
        
    }); 

function clearScreen(){
	$("#frmRegs").removeClass("was-validated");
	
    $("#cbTipoDespesa").val("");
    $("#valorRetirada").val("");
    $("#txtObservacao").val("");
}
</script>