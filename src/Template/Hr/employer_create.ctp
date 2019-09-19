<br/>
<form id="frmRegs" name="frmRegs" class="tabs-validation" novalidate>
<div class="card">
    <div class="card-header">
    	<div class="row">
			<div class="col-sm">
				<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Funcion&aacute;rio
			</div>
			<div class="col-sm text-right">
				<a href="<?php echo $this->Url->build('hr/employer');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
			</div>
		</div>
    </div>
    <div class="card-body">
        <input type="hidden" id="txtIdEmployer" name="txtIdEmployer" value="<?php if(isset($employer)){ echo $employer->IDFUNCIONARIO; }?>"/>
        <input type="hidden" id="txtIdCity" name="txtIdCity" value="<?php if(isset($employer)){ echo $employer->IDCIDADE; }?>"/>
        <input type="hidden" id="txtIdBank" name="txtIdBank" value="<?php if(isset($employer)){ echo $employer->IDBANCO; }?>"/>
        <ul class='nav nav-tabs' id='tabProduto' role="tablist">
            <li class='nav-item'><a class="nav-link active" href='#cadastro' data-toggle='tab' role="tab" id="tabCadastro">Inf. Cadastrais</a></li>
            <li class='nav-item'><a class="nav-link" href='#contato' data-toggle='tab' role="tab" id="tabContato">Contato</a></li>
            <li class='nav-item'><a class="nav-link" href='#contrato_trab' data-toggle='tab' role="tab" id="tabContrato">Contrato de Trabalho</a></li>
            <li class="nav-item"><a class="nav-link" href="#acesso" data-toggle='tab' role="tab" id="tabAcesso">Acesso ao sistema</a></li>
        </ul>
        <div class='tab-content'>

            <!--cadastro-->
            <div role='tabpanel' class='tab-pane fade active show' id='cadastro'><br/>
                <div class="row">
                    <div class="form-group col-8">
                        <label class="control-label">Nome do Funcion&aacute;rio</label>
                        <input type="text" class="form-control text-uppercase form-control-sm" id="txtEmployerNome" name="txtEmployerNome" value="<?php if(isset($employer)){ echo $employer->NOME; }?>" autocomplete="off" required/>
                    </div>
                    <div class="form-group col-4">
                        <label class="control-label">Apelido do Funcion&aacute;rio</label>
                        <input type="text" class="form-control text-uppercase form-control-sm" id="txtEmployerNick" name="txtEmployerNick" value="<?php if(isset($employer)){ echo $employer->APELIDO; }?>" autocomplete="off" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col">
                        <label class="control-label">E-mail do Funcion&aacute;rio</label>
                        <input type="text" class="form-control text-lowercase form-control-sm" id="txtEmployerEmail" name="txtEmployerEmail" value="<?php if(isset($employer)){ echo $employer->EMAIL; }?>" autocomplete="off" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-3">
                        <label class="control-label">Data de Nascimento</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control date" id="txtEmployerNascimento" name="txtEmployerNascimento" value="<?php if(isset($employer)){ echo $employer->NASCIMENTO->format("d/m/Y"); }?>" required/>
                            <div class="input-group-append">
                            	<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-3">
                        <label class="control-label">RG</label>
                        <input type="text" class="form-control form-control-sm" id="txtEmployerRG" name="txtEmployerRG" value="<?php if(isset($employer)){ echo $employer->RG; }?>" autocomplete="off" required/>
                    </div>
                    <div class="form-group col-3">
                        <label class="control-label">CPF</label>
                        <input type="text" class="form-control form-control-sm" id="txtEmployerCPF" name="txtEmployerCPF" value="<?php if(isset($employer)){ echo $employer->CPF; }?>" autocomplete="off" required/>
                    </div>
                    <div class="form-group col-3">
                        <label class="control-label">Data de Cadastro</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control date" id="txtEmployerCadastro" name="txtEmployerCadastro" value="<?php if(isset($employer)){ echo $employer->DATA_CADASTRO->format("d/m/Y"); }else{ echo date("d/m/Y"); }?>" required/>
                            <div class="input-group-append">
                            	<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- contato -->
            <div role='tabpanel' class='tab-pane fade' id='contato'><br/>
                <div class="row">
                    <div class="form-group col-8">
                        <label class="control-label">Endere&ccedil;o</label>
                        <input type="text" class="form-control text-uppercase form-control-sm" id="txtEmployerEndereco" name="txtEmployerEndereco" value="<?php if(isset($employer)){ echo $employer->ENDERECO; }?>" autocomplete="off" required/>
                    </div>
                    <div class="form-group col-4">
                        <label class="control-label">CEP</label>
                        <input type="text" id="txtEmployerCEP" name="txtEmployerCEP" class="form-control text-uppercase form-control-sm" value="<?php if(isset($employer)){ echo $employer->CEP; }?>" autocomplete="off" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-4">
                        <label class="control-label">Bairro</label>
                        <input type="text" id="txtEmployerBairro" name="txtEmployerBairro" class="form-control text-uppercase form-control-sm" value="<?php if(isset($employer)){ echo $employer->BAIRRO; }?>" autocomplete="off" required/>
                    </div>
                    <div class="form-group col-5">
                        <label class="control-label">Cidade</label><br/>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control text-uppercase" id="txtEmployerCity" name="txtEmployerCity" value="" autocomplete="off" readonly="true" required/>
                            <span class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalSearchCity">...</button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-3">
                        <label class="control-label">Estado</label>
                        <select class="form-control text-uppercase form-control-sm" name="cbEstado" id="cbEstado" required>
                            <option value=""></option>
                            <option value="AC">AC</option>
                            <option value="AL">AL</option>
                            <option value="AM">AM</option>
                            <option value="AP">AP</option>
                            <option value="BA">BA</option>
                            <option value="CE">CE</option>
                            <option value="DF">DF</option>
                            <option value="ES">ES</option>
                            <option value="GO">GO</option>
                            <option value="MA">MA</option>
                            <option value="MG">MG</option>
                            <option value="MS">MS</option>
                            <option value="MT">MT</option>
                            <option value="PA">PA</option>
                            <option value="PB">PB</option>
                            <option value="PE">PE</option>
                            <option value="PI">PI</option>
                            <option value="PR">PR</option>
                            <option value="RJ">RJ</option>
                            <option value="RN">RN</option>
                            <option value="RO">RO</option>
                            <option value="RR">RR</option>
                            <option value="RS">RS</option>
                            <option value="SC">SC</option>
                            <option value="SE">SE</option>
                            <option value="SP">SP</option>
                            <option value="TO">TO</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-4">
                        <label class="control-label">Telefone</label>
                        <input type="text" class="form-control form-control-sm" id="txtEmployerTelefone" name="txtEmployerTelefone" value="<?php if(isset($employer)){ echo $employer->TELEFONE; }?>" autocomplete="off" required/>
                    </div>
                    <div class="form-group col-4">
                        <label class="control-label">Telefone 2</label>
                        <input type="text" class="form-control form-control-sm" id="txtEmployerTelefone2" name="txtEmployerTelefone2" value="<?php if(isset($employer)){ echo $employer->TELEFONE2; }?>" autocomplete="off"/>
                    </div>
                    <div class="form-group col-4">
                    	<label class="control-label">Recados com</label>
                        <input type="text" class="form-control text-uppercase form-control-sm" id="txtEmployerRecado" name="txtEmployerRecado" value="<?php if(isset($employer)){ echo $employer->RECADOS; }?>" autocomplete="off" required/>
                    </div>
                </div>
            </div>

            <!-- contrato_trab -->
            <div role='tabpanel' class='tab-pane fade' id='contrato_trab'><br/>
                <div class="row">
                    <div class="form-group col-4">
                        <label class="control-label">Cargo</label>
                        <select class="form-control form-control-sm" id="cbEmployerCargo" name="cbEmployerCargo" required>
                            <option value="">&laquo; Selecione &raquo;</option>
                            <?php foreach($offices as $office):?>
                            <option value="<?php echo $office->IDCARGO;?>"<?php if(isset($employer)){ if($employer->IDCARGO==$office->IDCARGO){ echo " selected"; } }?>><?php echo $office->NOME; ?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
					<div class="form-group col-4">
                        <label class="control-label">Situa&ccedil;&atilde;o Cadastral</label>
                        <select class="form-control form-control-sm" id="cbEmployerStatus" name="cbEmployerStatus" required>
                            <option value="">&laquo; Selecione &raquo;</option>
                            <option value="E"<?php if(isset($employer)){ if($employer->STATUS=="E"){ echo " selected"; } }?>>Exercendo a fun&ccedil;&atilde;o</option>
                            <option value="D"<?php if(isset($employer)){ if($employer->STATUS=="D"){ echo " selected"; } }?>>Demitido/Desligado</option>
                        </select>
                    </div>
                    <div class="form-group col-4">
                        <label class="control-label">Data de Demiss&atilde;o</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control date" id="txtEmployerDemissao" name="txtEmployerDemissao" value="<?php if(isset($employer)){ if($employer->DATA_DEMISSAO!=""){ echo formatDate($employer->DATA_DEMISSAO); } }?>"/>
                            <div class="input-group-append">
                            	<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div role='tabpanel' class='tab-pane fade' id='acesso'><br/>
            	<div class="row">
            		<div class="form-group col">
            			<label for="cbSystemUser">Usu&aacute;rio para acessar o sistema</label>
            			<div class="input-group input-group-sm">
	            			<select class="form-control" id="cbSystemUser" name="cbSystemUser" required>
	            				<option value="">&laquo; Selecione &raquo;</option>
	            			</select>
	            			<div class="input-group-append">
	            				<button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalNewUser"><i class="fas fa-user-plus"></i></button>
	            			</div>
            			</div>
            		</div>
            	</div>
            </div>
            <div class="text-right"><button type="submit" class="btn btn-primary btn-sm" id="btnSave"><i class="fas fa-hdd"></i> Salvar</button></div>
        </div>
    </div>
</div>
</form>

<div class="modal fade" tabindex="-1" role="dialog" id="modalNewUser">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cadastro de usu&aacute;rio</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <iframe name="frmNewUser" id="frmNewUser" width="100%" style="border:0;min-height:450px;max-height:450px;"></iframe>
      </div>
    </div>
  </div>
</div>

<?php $this->Dialog->city(); ?>

<?php $this->Dialog->bank(); ?>

<script>
window.closeUserModal = function(){
	$("#modalNewUser").modal('hide');

	getUsers();
}

$(document).ready(function(){
    var MaskBehavior = function (val) {
      return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
    fnOptions = {
      onKeyPress: function(val, e, field, options) {
          field.mask(MaskBehavior.apply({}, arguments), options);
        }
    };

    $("#txtEmployerTelefone").mask(MaskBehavior,fnOptions);
    $("#txtEmployerTelefone2").mask(MaskBehavior,fnOptions);
    $('#txtEmployerCEP').mask('00000-000');
    $("#txtEmployerCPF").mask('000.000.000-00');
    $("#txtEmployerNascimento").mask("00/00/0000");
    $("#txtEmployerCadastro").mask("00/00/0000");

    <?php if(isset($employer)): ?>
        $.ajax({
            url:'<?=$this->Url->build("/system/city_get_by_id/")?><?php echo $employer->IDCIDADE; ?>',
            dataType: 'json',
            success: function(data){
                $("#txtEmployerCity").val(data.NOME);
                $("#cbEstado").val(data.UF);
            }
        });
    <?php endif;?>
});

/**
* Evento que trata o envio do formulario
*/
$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();

    var dataForm = {
        IDFUNCIONARIO  : $("#txtIdEmployer").val(),
        IDCIDADE       : $("#txtIdCity").val(),
        IDBANCO        : $("#txtIdBank").val(),
        NOME           : $("#txtEmployerNome").val(),
        APELIDO        : $("#txtEmployerNick").val(),
        EMAIL          : $("#txtEmployerEmail").val(),
        NASCIMENTO     : $("#txtEmployerNascimento").val(),
        RG             : $("#txtEmployerRG").val(),
        CPF            : $("#txtEmployerCPF").val(),
        DATA_CADASTRO  : $("#txtEmployerCadastro").val(),

        ENDERECO  : $("#txtEmployerEndereco").val(),
        CEP       : $("#txtEmployerCEP").val(),
        BAIRRO    : $("#txtEmployerBairro").val(),
        TELEFONE  : $("#txtEmployerTelefone").val(),
        TELEFONE2 : $("#txtEmployerTelefone2").val(),
        RECADOS   : $("#txtEmployerRecado").val(),

        IDCARGO           : $("#cbEmployerCargo").val(),
        STATUS            : $("#cbEmployerStatus").val(),
        DATA_DEMISSAO     : $("#txtEmployerDemissao").val(),

        IDUSUARIO         : $("#cbSystemUser").val()
    };

    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method  : 'POST',
        data    : dataForm,
        url     : '<?=$this->Url->build("/hr/employer_save")?>',
        success : function(data){
            if(data){
                bootbox.alert('Funcion&aacute;rio salvo com sucesso!',function(){ document.location.href='<?=$this->Url->build("/hr/employer")?>'; })
            }else{
                bootbox.alert('Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es do funcion&aacute;rio!');
            }
        }
    });
});

/**
* Evento que trata o clique no botao de usar a cidade
*/
$(document).on("click","#btnUseCity",function(){
    $("input[name='rdCidade[]']").each(function(){
        if($(this)[0].checked){
            var idCidade = $(this).val();

            $("#txtIdCity").val(idCidade);
            $("#txtEmployerCity").val($("#txtCityResultNome_"+idCidade).val());
            $("#cbEstado").val($("#txtCityResultUF_"+idCidade).val());
            $("#modalSearchCity").modal('hide');
        }
    });
});

/**
* Evento que trata o clique no botao de usar o banco
*/
$(document).on("click","#btnUseBank",function(){
    $("input[name='rdBanco[]']").each(function(){
        if($(this)[0].checked){
            var idBanco = $(this).val();

            $("#txtIdBank").val(idBanco);
            $("#txtEmployerBank").val($("#txtBankResultNome_"+idBanco).val());
            $("#modalSearchBank").modal('hide');
        }
    });
});

/**
* Funcao que realiza a validacao das abas
*
* @return
*/
function checkAba(){
	//valida a primeira aba que eh a de cadastro
	if($("#tabCadastro").hasClass("active")){
		if($("#txtEmployerNome").val()!=""){
			if($("#txtEmployerNick").val()!=""){
				if($("#txtEmployerEmail").val()!=""){
					if($("#txtEmployerNascimento").val()!=""){
						if($("#txtEmployerRG").val()!=""){
							if($("#txtEmployerCPF").val()!=""){
								$('#tabContato').trigger("click");
                                return false;
							}
						}
					}
				}
			}
		}
	}
	//valida a segunda aba que eh a de contato
	if($("#tabContato").hasClass("active")){
		if($("#txtEmployerEndereco").val()!=""){
			if($("#txtEmployerCEP").val()!=""){
				if($("#txtEmployerBairro").val()!=""){
                    if($("#txtEmployerCity").val()!=""){
                        if($("#txtEmployerTelefone").val()!=""){
                            if($("#txtEmployerRecado").val()!=""){
                                $("#tabContrato").trigger("click");
                                return false;
                            }
                        }
                    }else{
                        bootbox.alert("Por favor informe a cidade!");
                        return false;
                    }
				}
			}
		}
	}
	//valida a terceira aba que eh a de contrato
	if($("#tabContrato").hasClass("active")){
		if($("#cbEmployerCargo").val()!=""){
			if($("#cbEmployerStatus").val()!=""){
				$("#tabAcesso").trigger('click');
                return false;
			}
		}
	}

    return true;
}

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  if(e.target.id=="tabAcesso"){
  	getUsers();
  }
});

$(document).on("show.bs.modal","#modalNewUser",function(){
	$("#frmNewUser").attr("src",'<?=$this->Url->build("/users/create/0/1")?>');
});


function getUsers(){
	//limpa o campo antes de adicionar novos registros
	$("#cbSystemUser")
	.find('option')
	.remove()
	.end();

	$("#cbSystemUser").append(new Option("\u00ab Selecione \u00bb",""))

	$.ajax({
  		headers : {
			'X-CSRF-Token': csrf
		},
		type    : 'POST',
		url     : '<?=$this->Url->build("/users/json/")?>',
		dataType: 'json',
		success : function(data){
			$.each(data,function(index,value){
				$("#cbSystemUser").append( new Option(value.name+' - '+value.username,value.id));
			});
			<?php if(isset($employer)): ?>
			$("#cbSystemUser").val("<?=$employer->IDUSUARIO;?>");
			<?php endif; ?>
		}
  	})
}
</script>
