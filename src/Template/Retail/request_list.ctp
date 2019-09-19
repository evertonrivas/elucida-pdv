<div class="container">
    <!-- FILTROS DA LISTAGEM -->
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default drop-shadow">
            <div class="panel-heading" role="tab" id="headingOne">
              <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  Filtros de Consulta
                </a>
              </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                <form id="frmFilter">
                    <div class="panel-body" id="filters">

                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- FIM DOS FILTROS -->
    
    <div class="alert alert-success" role="alert" id="alertSuccess"><strong>Sucesso!</strong> Mensagem enviada com sucesso!</div>
    <div class="alert alert-danger" role="alert" id="alertFail"><strong>Problema!</strong> Ocorreu um erro ao enviar a mensagem!</div>
    <div class="panel panel-default drop-shadow">
        <div class="panel-heading clearfix">
            <h3 class="panel-title pull-left" style="padding-top: 7.5px;"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> Solicita&ccedil;&otilde;es de Clientes</h3>
            <div class="pull-right">
                <button type="button" class="btn btn-success" data-toggle="modal" data-backdrop="static" data-target="#modalNewRequest"><span class="glyphicon glyphicon-ok-circle"></span> Nova</button>
                <button class="btn btn-danger" id="btnCancel" name="btnCancel" type="button"><span class="glyphicon glyphicon-remove-circle"></span> Cancelar</button>
                <button class="btn btn-info" id="btnAtend" name="btnAtend" type="button"><span class="glyphicon glyphicon-ok"></span> Atendida</button>
            </div>
        </div>
        <div class="panel-body" id="divResult">    
            
        </div>
    </div>
</div>

<!--INICIO DO MODAL DE NOVA SOLICITACAO-->
<div class="modal fade" id="modalNewRequest" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalLabel">Nova Solicita&ccedil;&atilde;o</h4>
      </div>
      <div class="modal-body">
        <form name="frmNewRequest" id="frmNewRequest">
            <div class="form-group">
                <label class="control-label">Cliente</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="txtNomeCliente" name="txtNomeCliente" readonly="" placeholder="Busque o cliente desejado"/>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" id="btnSearchCustomer" data-toggle="modal" data-target="#modalFindCliente" title="Buscar Cliente"><span class="fa fa-users"></span></button>
                    </span>
                </div>
                <input type="hidden" id="txtIdCliente" name="txtIdCliente"/>
            </div>
            <div class="form-group">
                <label class="control-label">Data da Solicita&ccedil;&atilde;o</label>
                <div class="input-group date">
                    <input type="text" class="form-control" id="txtDataSolicita" name='txtDataSolicita' autocomplete="off" value="<?php echo date("d/m/Y")?>"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
              </div>
            </div>
            <div class="form-group">
                <label class="control-label">Forma de contato</label>
                <select id="cbFormaContato" name="cbFormaContato" class="form-control">
                    <option value="">&laquo; Selecione &raquo;</option>
                    <option value="E">E-mail</option>
                    <option value="L">Liga&ccedil;&atilde;o</option>
                    <option value="M">SMS/WhatsApp</option>
                    <option value="T">Todas</option>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">Desejo</label>
                <textarea class="form-control text-uppercase" id="txtDesejo" name="txtDesejo" rows="4"></textarea>
            </div>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-primary" id="btnSend" name="btnSend">Salvar</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!--INICIO DE ENVIO DE E-MAIL DE SOLICITACAO-->
<div class="modal fade" id="modalNewRequestMail" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalLabel">Envio de E-mail</h4>
      </div>
      <div class="modal-body">
          <form name="frmSendMail" id="frmSendMail" method="post">
            <div class="form-group">
                <label class="control-label">Cliente</label>
                <p class="form-control-static" id="txtEmailNomeCliente"></p>
                <input type="hidden" class="form-control" id="txtEmailIdCliente" name="txtEmailIdCliente"/>
            </div>
            <div class="form-group">
                <label class="control-label">E-mail do Cliente</label>
                <p class="form-control-static" id="txtEmailCliente"></p>
            </div>
            <div class="form-group">
                <label class="control-label">Mensagem</label>
                <p class="form-control-static" id="txtEmailAssunto">O produto que voc&ecirc; deseja j&aacute; encontra-se na Hestilo</p>
            </div>
            <div class="form-group">
                <label class="control-label">Desejo</label>
                <textarea class="form-control" id="txtEmailDesejo" name="txtEmailDesejo" rows="4"></textarea>
            </div>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-primary" id="btnSend" name="btnSend">Enviar</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php $this->Dialog->customer_find(); ?>
<?php $this->Dialog->customer_new(); ?>

<script>
$(document).ready(function(){
    getData(BASE_URL_JS+'<?= $url_data; ?>');
    getFilter('<?= $url_filter; ?>');
    
    $("#txtDataSolicita").mask('00/00/0000');
    $('#txtSolicitaData').mask('00/00/0000');
    
    $("#frmNewRequest").formValidation({
        framework: 'bootstrap',
        excluded: ':disabled',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields:{
            txtIdCliente:{
                validators:{
                    notEmpty:{
                        message: "Por favor informe o cliente"
                    }
                }
            },
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
            IDCLIENTE  : $("#txtIdCliente").val(),
            DATA_SOLICITACAO : $("#txtDataSolicita").val(),
            FORMA_CONTATO  :$("#cbFormaContato").val(),
            DESEJO : $("#txtDesejo").val()
        };
        
        $.ajax({
            method:'post',
            url: BASE_URL_JS+'/retail/request_data_save',
            data: dataForm,
            success: function(data){
                if(data){
                    $("#modalNewRequest").modal('hide');
                    $("#alertSuccess").html('<strong>Sucesso!</strong> Solicita&ccedil;&atilde;o salva com sucesso!');
                    $("#alertSuccess").show();
                    $("#alertSuccess").fadeTo(2500,500).slideDown(500,function(){
                        $("#alertSuccess").hide();
                        document.location.reload();
                    });
                }
                else{
                    $("#modalNewRequest").modal('hide');
                    $("#alertFail").html('<strong>Problema!</strong> Ocorreu um erro ao salvar a solicita&ccedil;&atilde;o!');
                    $("#alertFail").show();
                    $("#alertFail").fadeTo(2500,500).slideDown(500,function(){
                        $("#alertFail").hide();
                    });
                }
            }
        });
    });
});

$(document).on("submit","#frmFilter",function(e){
    e.preventDefault();
    getData(BASE_URL_JS+'<?= $url_data; ?>');
    $('.collapse').collapse('toggle');
});

$(document).on("submit","#frmSendMail",function(e){
    $.ajax({
        method:'post',
        data:{
            CLIENTE  : $("#txtEmailIdCliente").val(),
            TEMPLATE : <?=$template?>,
            ASSUNTO  : $("#txtEmailAssunto").html(),
            MENSAGEM : $("#txtEmailDesejo").val()
        },
        url: BASE_URL_JS+'/system/send_templated_email/TRUE',
        success: function(data){
            if(data){
                $("#modalNewMail").modal('hide');
                $("#alertSuccess").html('<strong>Sucesso!</strong> Mensagem enviada com sucesso!');
                $("#alertSuccess").show();
                $("#alertSuccess").fadeTo(2500,500).slideDown(500,function(){
                    $("#alertSuccess").hide();
                });
            }
            else{
                $("#modalNewMail").modal('hide');
                $("#alertFail").html('<strong>Problema!</strong> Ocorreu um erro ao enviar a mensagem!');
                $("#alertFail").show();
                $("#alertFail").fadeTo(2500,500).slideDown(500,function(){
                    $("#alertFail").hide();
                });
            }
        }
    });

    e.preventDefault();
});

$(document).on("click","#btnAtend",function(){
    var totalChecked = 0;
    $("input[name='check_list[]']").each(function(){
        if( $(this)[0].checked ==true ){
            totalChecked = totalChecked + 1;
        }
    });
    
    if( totalChecked > 0){
        var msg = (totalChecked==1)?"Deseja realmente marcar esta solicita&ccedil;&atilde;o como atendida?":"Deseja realmente marcar estas solicita&ccedil;&otilde;es como atendidas?";
        
        bootbox.dialog({message: msg, 
        buttons:{
            yes:{
                label:"Sim",
                callback:function(){
                    $.ajax({
                        method: 'post',
                        url: BASE_URL_JS+'/retail/request_set_finished/1',
                        data: $("input[name='check_list[]']").serialize(),
                        success: function(data){
                            if(data){
                                if(totalChecked==1){
                                    $("#alertSuccess").html('<strong>Sucesso!</strong> Solicita&ccedil;&atilde;o marcada como atendida com sucesso!');
                                }
                                else{
                                    $("#alertSuccess").html('<strong>Sucesso!</strong> Solicita&ccedil;&otilde;es marcadas como atendidas com sucesso!');
                                }
                                $("#alertSuccess").show();
                                $("#alertSuccess").fadeTo(2500,500).slideDown(500,function(){
                                    $("#alertSuccess").hide();
                                    document.location.reload();
                                });
                            }
                            else{
                                $("#modalNewMail").modal('hide');
                                $("#alertFail").html('<strong>Problema!</strong> Ocorreu um erro ao tentar marcar a(s) solicita&ccedil;&atilde;o(&otilde;es)!');
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
    }
});

$(document).on("click","#btnCancel",function(){
    var totalChecked = 0;
    $("input[name='check_list[]']").each(function(){
        if( $(this)[0].checked ==true ){
            totalChecked = totalChecked + 1;
        }
    });
    
    if( totalChecked > 0){
        var msg = (totalChecked==1)?"Deseja realmente marcar esta solicita&ccedil;&atilde;o como cancelada?":"Deseja realmente marcar estas solicita&ccedil;&otilde;es como canceladas?";
        
        bootbox.dialog({message: msg, 
        buttons:{
            yes:{
                label:"Sim",
                callback:function(){
                    $.ajax({
                        method: 'post',
                        url: BASE_URL_JS+'/retail/request_set_finished/2',
                        data: $("input[name='check_list[]']").serialize(),
                        success: function(data){
                            if(data){
                                if(totalChecked==1){
                                    $("#alertSuccess").html('<strong>Sucesso!</strong> Solicita&ccedil;&atilde;o marcada como cancelada com sucesso!');
                                }
                                else{
                                    $("#alertSuccess").html('<strong>Sucesso!</strong> Solicita&ccedil;&otilde;es marcadas como canceladas com sucesso!');
                                }
                                $("#alertSuccess").show();
                                $("#alertSuccess").fadeTo(2500,500).slideDown(500,function(){
                                    $("#alertSuccess").hide();
                                    document.location.reload();
                                });
                            }
                            else{
                                $("#modalNewMail").modal('hide');
                                $("#alertFail").html('<strong>Problema!</strong> Ocorreu um erro ao tentar marcar a(s) solicita&ccedil;&atilde;o(&otilde;es)!');
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
    }
});

function sendRequest(_idSolicitacao){
    
    $.ajax({
        method:'post',
        data: { IDSOLICITACAO : _idSolicitacao },
        url:BASE_URL_JS+'/retail/request_get_mail_info/',
        dataType:'json',
        success:function(data){
            $("#txtEmailNomeCliente").html(data.CLIENTE);
            $("#txtEmailIdCliente").val(data.IDCLIENTE);
            $("#txtEmailIdSolicitacao").val(_idSolicitacao);
            $("#txtEmailCliente").html(data.EMAIL_CLIENTE);
            $("#txtEmailDesejo").val(data.DESEJO);
        }
    });
    
    $("#modalNewRequestMail").modal({
        backdrop: 'static'
    });
}

//busca as informacoes do cliente apos selecionado no dialog
function useCustomer(idCliente){
    $.ajax({
        method:'post',
        data : {IDCLIENTE : idCliente},
        url:BASE_URL_JS+'/retail/customer_get_info',
        dataType:'json',
        success:function(data){
            $("#txtIdCliente").val(data.IDCLIENTE);
            $("#txtNomeCliente").val(data.NOME);
            if(data.STATUS_CADASTRO==0){
                $("#modalNewCustomer").modal({
                    backdrop:'static'
                });
                setCustomerRegistry(data);
            }
        }
    });
}

//define a informacao do cliente apos o salvamento das informacoes no BD
function setCustomerData(data){
    $("#txtIdCliente").val(data.IDCLIENTE);
    $("#txtNomeCliente").val(data.NOME);
}
</script>