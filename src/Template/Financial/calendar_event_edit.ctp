<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
	<input type="hidden" id="txtIdPayment" name="txtIdPayment" value="<?php echo $conta->IDCONTASPAGAR; ?>"/>
    <div class="card">
        <div class="card-header">
        	<div class="row">
        		<div class="col-sm">
        			<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Conta &agrave; Pagar
        		</div>
        		<div class="col-sm text-right">
        			<a href="<?php echo $this->Url->build('financial/calendar_event_show/'.$conta->IDCONTASPAGAR);?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
        		</div>
        	</div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="control-label">Tipo de Despesa</label>
                <select class="form-control" id="cbTipoDespesa" name="cbTipoDespesa" required>
                    <option value="">&laquo; Selecione &raquo;</option>
                    <?php foreach($despesalist as $despesa):?>
                    <option value="<?php echo $despesa->IDTIPODESPESA; ?>"<?php if($conta->IDTIPODESPESA==$despesa->IDTIPODESPESA){ echo " selected"; }?>><?php echo $despesa->NOME; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">N&uacute;mero do Documento</label>
                <input type="text" class="form-control" id="txtNumDocumento" name="txtNumDocumento" autocomplete="off" value="<?php echo $conta->NUM_DOCUMENTO; ?>"/>
            </div>
            <div class="form-group">
                <label class="control-label">Data de Vencimento</label>
                <div class="input-group">
                    <input type="text" class="form-control date" id="txtDataVencimento" name="txtDataVencimento" autocomplete="off" value="<?php echo $conta->DATA_VENCIMENTO->format("d/m/Y"); ?>" required/>
                	<div class="input-group-append">
                		<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                	</div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">Hora do Vencimento</label>
                <input type="text" class="form-control" id="txtHoraVencimento" name="txtHoraVencimento" autocomplete="off" value="<?php echo $conta->DATA_VENCIMENTO->format("H:i:s"); ?>" required/>
            </div>
            <div class="form-group"> 
                <label class="control-label">Valor &agrave; pagar</label>
                <input type="text" class="form-control" id="txtValOriginal" name="txtValOriginal" autocomplete="off" value="<?php echo $this->Number->precision($conta->VALOR_ORIGINAL,2); ?>" required/>
            </div>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-primary" id="btnSend" name="btnSend">Salvar</button>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $("#txtDataVencimento").mask("00/00/0000");
    $("#txtValOriginal").mask("#,##0.00", {reverse: true});    
});

$(document).on("submit","#frmRegs",function(event){
    event.preventDefault();
    
    var dataForm = {
        IDCONTASPAGAR : $("#txtIdPayment").val(),
        IDTIPODESPESA : $("#cbTipoDespesa").val(),
        NUM_DOCUMENTO : $("#txtNumDocumento").val(),
        DATA_VENCIMENTO : $("#txtDataVencimento").val(),
        HORA_VENCIMENTO : $("#txtHoraVencimento").val(),
        VALOR_ORIGINAL  : $("#txtValOriginal").val()
    };
    
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf	
		},
        method: 'post',
        data: dataForm,
        url: '<?=$this->Url->build("/financial/calendar_event_save")?>',
        success: function(data){
            if(data==true){
            	bootbox.alert("Evento editado com sucesso!");
            }else{
            	bootbox.alert("Ocorreu um erro ao tentar editar o evento!");
            }
            clearFields();
        }
    });
});
	
function clearFields(){
	$("#frmRegs").removeClass("was-validated");
    $("#txtIdPayment").val("");
    $("#cbTipoDespesa").val("");
    $("#txtNumDocumento").val("");
    $("#txtDataVencimento").val("");
    $("#txtHoraVencimento").val("");
    $("#txtValOriginal").val("");
}
</script>