<br/><form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
        <input type="hidden" id="txtIdParceiro" name="txtIdParceiro" value="<?php if(isset($parceiro)){ echo $parceiro->IDPARCEIRO; }?>"/>
        <div class="card">
            <div class="card-header">
                <div class="row">
					<div class="col-sm">
						<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Parceiro
					</div>
					<div class="col-sm text-right">
						<a href="<?php echo $this->Url->build('/retail/partner');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
					</div>
				</div>
            </div>
            <div class="card-body">
                <?php if(!$is_mobile):?>
                <div class="form-group">
                      <label for="txtCadParceiroNome">Nome:</label>
                      <input type="text" id="txtCadParceiroNome" name="txtCadParceiroNome" class="form-control form-control-sm text-uppercase" autocomplete="off" value="<?php if(isset($parceiro)){ echo $parceiro->NOME; }?>" required/>
                  </div>
                  <div class="form-group">
                      <label for="txtCadParceiroCupom">Cupom:</label>
                      <input type="text" id="txtCadParceiroCupom" name="txtCadParceiroCupom" class="form-control form-control-sm text-uppercase" autocomplete="off" value="<?php if(isset($parceiro)){ echo $parceiro->CODIGO_CUPOM; }?>" required/>
                  </div>
                  <div class="form-group">
                      <label for="txtCadParceiroPercDesc">% de Desconto:</label>
                      <input type="text" id="txtCadParceiroPercDesc" name="txtCadParceiroPercDesc" class="form-control form-control-sm" value="<?php if(isset($parceiro)){ echo $this->Number->precision($parceiro->PERC_DESCONTO,2); }?>" required/>
                  </div>
                  <div class="row">
                      <div class="form-group col-sm">
                        <label for="txtCadParceiroInicio">Inicio Parceria</label>
                        <div class="input-group mb-3 input-group-sm">
                            <input type="text" class="form-control date" id="txtCadParceiroInicio" name="txtCadParceiroInicio" value="<?php if(isset($parceiro)){ echo $parceiro->DATA_INICIO->format("d/m/Y"); }?>" required/>
                            <div class="input-group-append">
                            	<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                        </div>
                      </div>
                      <div class="form-group col-sm">
                        <label for="txtCadParceiroFim">Fim Parceria</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control date" id="txtCadParceiroFim" name="txtCadParceiroFim" class="form-control" value="<?php if(isset($parceiro)){ if($parceiro->DATA_FIM){ echo $parceiro->DATA_FIM->format("d/m/Y"); } }?>"/>
                            <div class="input-group-append">
	                        	<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        	</div>
                        </div>
                      </div>
                  </div>
                <?php else: ?>
                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" id="txtCadParceiroNome" name="txtCadParceiroNome" class="form-control form-control-sm text-uppercase" autocomplete="off" value="<?php if(isset($cliente)){ echo $cliente->NOME; }?>"/>
                </div>
                <div class="form-group">
                      <label>Cupom:</label>
                      <input type="text" id="txtCadParceiroCupom" name="txtCadParceiroCupom" class="form-control form-control-sm text-uppercase" autocomplete="off" value="<?php if(isset($parceiro)){ echo $parceiro->CODIGO_CUPOM; }?>"/>
                </div>
                <div class="form-group">
                    <label>% de Desconto:</label>
                    <input type="text" id="txtCadParceiroPercDesc" name="txtCadParceiroPercDesc" class="form-control" value="<?php if(isset($parceiro)){ echo $this->Number->precision($parceiro->PERC_DESCONTO,2); }?>"/>
                </div>
                <div class="form-group">
                    <label>Inicio Parceria</label>
                    <div class="input-group date">
                        <input type="text" class="form-control form-control-sm" id="txtCadParceiroInicio" name="txtCadParceiroInicio" value="<?php if(isset($parceiro)){ echo $parceiro->DATA_INICIO->format("d/m/Y"); }?>"/><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Fim Parceria</label>
                    <div class="input-group date">
                        <input type="text" class="form-control form-control-sm" id="txtCadParceiroFim" name="txtCadParceiroFim" class="form-control" value="<?php if(isset($parceiro)){ if($parceiro->DATA_FIM){ echo $parceiro->DATA_FIM->format("d/m/Y"); } }?>"/><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSend" name="btnSend"><i class="fas fa-hdd"></i> Salvar</button>
                </div>
            </div>
        </div>
</form>

<script>
$(document).ready(function(){
    $('#txtCadParceiroPercDesc').mask('00.00');
    $("#txtCadParceiroInicio").mask("00/00/0000");
    $("#txtCadParceiroFim").mask("00/00/0000");
});

$(document).on("submit","#frmRegs",function(event){
	e.preventDefault();
    
    var dataForm = {
        IDPARCEIRO   : $("#txtIdParceiro").val(),
        NOME         : $("#txtCadParceiroNome").val(),
        CODIGO_CUPOM : $("#txtCadParceiroCupom").val(),
        PERC_DESCONTO: $("#txtCadParceiroPercDesc").val(),
        DATA_INICIO  : $("#txtCadParceiroInicio").val(),
        DATA_FIM     : $("#txtCadParceiroFim").val()
    };
    
    $.ajax({
       method: 'post',
       url: '<?=$this->Url->build("/retail/partner_save/")?>',
       data: dataForm,
       dataType: 'json',
       success: function(data){
           if(data){
                bootbox.alert("Parceiro salvo com sucesso!",function(){ document.location.href='<?=$this->Url->build("/retail/partner")?>'; });
            }else{
            	bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es do Parceiro!");
            }
       }
    });
});
	
function clearFields()
{
    $("#txtIdParceiro").val("");
    $("#txtCadParceiroNome").val("");
    $("#txtCadParceiroCupom").val("");
    $("#txtCadParceiroPercDesc").val("");
    $("#txtCadParceiroInicio").val("");
    $("#txtCadParceiroFim").val("");
    $("#txtCadParceiroNome").focus();
}
</script>