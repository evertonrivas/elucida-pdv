<?php $time = new Cake\I18n\Time();?>
<div class="container">
<form name="frmCadRequest" id="frmCadRequest">
    <input type="hidden" id="txtIdRequest" name="txtIdRequest" value="<?php if(isset($solicitacao)){ echo $solicitacao->IDSOLICITACAO; }?>"/>
    <input type="hidden" id="txtIdLoja" name="txtIdLoja" value="<?php if(isset($solicitacao)){ echo $solicitacao->IDLOJA; }?>"/>
        <div class="panel panel-default drop-shadow">
            <div class="panel-heading clearfix">
                <h3 class="panel-title pull-left" style="padding-top: 7.5px;"><span class="glyphicon glyphicon-chevron-right"></span> Edi&ccedil;&atilde;o de Solicita&ccedil;&atilde;o </h3>
                <div class="pull-right">
                    <a href="<?php echo $this->Url->build('/retail/request_list');?>" class="btn btn-default"><span class="glyphicon glyphicon-circle-arrow-left"></span> Voltar</a>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label">Cliente</label><br/>
                    <input type="text" class="form-control text-uppercase" id="txtNomeCliente" name="txtNomeCliente" autocomplete="off" value="<?php if(isset($cliente)){ echo $cliente->NOME; } ?>" disabled=""/>
                    <input type="hidden" id="txtIdCliente" name="txtIdCliente" value="<?php if(isset($solicitacao)){ echo $solicitacao->IDCLIENTE; }?>"/>
                </div>
                <div class="form-group">
                    <label class="control-label">Data da Solicita&ccedil;&atilde;o</label>
                    <div class="input-group date">
                        <input type="text" class="form-control" id="txtDataSolicita" name='txtDataSolicita' autocomplete="off" value="<?php if(isset($solicitacao)){ echo $time->parseDate($solicitacao->DATA_SOLICITACAO)->i18nFormat("dd/MM/yyyy"); } ?>"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Forma de contato</label>
                    <select id="cbFormaContato" name="cbFormaContato" class="form-control">
                        <option value="">&laquo; Selecione &raquo;</option>
                        <option value="E"<?php if(isset($solicitacao)){ if($solicitacao->FORMA_CONTATO=='E'){ echo " selected"; } }?>>E-mail</option>
                        <option value="L"<?php if(isset($solicitacao)){ if($solicitacao->FORMA_CONTATO=='L'){ echo " selected"; } }?>>Liga&ccedil;&atilde;o</option>
                        <option value="M"<?php if(isset($solicitacao)){ if($solicitacao->FORMA_CONTATO=='M'){ echo " selected"; } }?>>SMS/WhatsApp</option>
                        <option value="T"<?php if(isset($solicitacao)){ if($solicitacao->FORMA_CONTATO=='T'){ echo " selected"; } }?>>Todas</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Desejo</label>
                    <textarea class="form-control text-uppercase" id="txtDesejo" name="txtDesejo" rows="4"><?php if(isset($solicitacao)){ echo $solicitacao->DESEJO; } ?></textarea>
                </div>
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary" id="btnSend" name="btnSend">Salvar</button>
                </div>
            </div>
        </div>
</form>
</div>

<script>
$(document).ready(function(){
    
    $("#txtDataSolicita").mask("00/00/0000");

    $("#frmCadRequest").formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields:{
            txtDataSolicita:{
                validators:{
                    notEmpty:{
                        message: "Por favor informe a data da solicita&ccedil;&atilde;o"
                    }
                }
            },
            txtDesejo:{
                validators:{
                    notEmpty:{
                        message: "Por favor informe o desejo do cliente"
                    }
                }
            },
            cbFormaContato:{
                validators:{
                    notEmpty:{
                        message: "Por favor selecione a forma de contato"
                    }
                }
            }
        }
    })
    .on('success.form.fv',function(event){

        event.preventDefault();
        
        var dataForm = {
            IDSOLICITACAO    : $("#txtIdRequest").val(),
            IDLOJA           : $("#txtIdLoja").val(),
            IDCLIENTE        : $("#txtIdCliente").val(),
            DATA_SOLICITACAO : $("#txtDataSolicita").val(),
            FORMA_CONTATO    : $("#cbFormaContato").val(),
            DESEJO           : $("#txtDesejo").val()
        };

        $.ajax({
                type: 'POST',
                url: '<?=$this->Url->build("/retail/request_data_save")?>',
                data: dataForm,
                success: function(data){
                    if(data==true){
                        $("#alertSuccess").show();
                        $("#alertSuccess").fadeTo(2500,500).slideDown(500,function(){
                            $("#alertSuccess").hide();
                        });
                    }else{
                         $("#alertFail").show();
                         $("#alertFail").fadeTo(2500,500).slideDown(500,function(){
                             $("#alertFail").hide();
                         });
                    }
                    clearFields();
                }
            });
    });
});
	
function clearFields()
{
    $("#txtIdLoja").val("");
    $("#txtIdCliente").val("");
    $("#txtNomeCliente").val("");
    $("#txtDataSolicita").val("");
    $("#txtDesejo").val("");
    $("#frmCadRequest").formValidation('resetForm',true);
}
</script>