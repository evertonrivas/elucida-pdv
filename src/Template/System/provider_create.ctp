<br/><form name="frmRegs" id="frmRegs" class="tabs-validation" novalidate>
    <input type="hidden" id="txtIdProvider" name="txtIdProvider" value="<?php if(isset($fornecedor)){ echo $fornecedor->IDFORNECEDOR; }?>"/>
    <input type="hidden" id="txtIdCity" name="txtIdCity" value="<?php if(isset($fornecedor)){ echo $fornecedor->IDCIDADE; }?>"/>
    <input type="hidden" id="txtIdBank" name="txtIdBank" value="<?php if(isset($fornecedor)){ echo $fornecedor->IDBANCO; }?>"/>
	<div class="card drop-shadow">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Fornecedor
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/system/provider');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			<nav>
				<div class="nav nav-tabs" id="nav-tab" role="tablist">
					<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-cadastro" role="tab" aria-controls="nav-home" aria-selected="true">Cadastro</a>
					<a class="nav-item nav-link" id="nav-bank-tab" data-toggle="tab" href="#nav-bank" role="tab" aria-controls="nav-profile" aria-selected="false">Dados Banc&aacute;rios</a>
				</div>
			</nav>

			<div class="tab-content">
				<div class="tab-pane fade show active" id="nav-cadastro">
					<div class="form-row">
						<div class="form-group col-md-8">
							<label for="">Raz&atilde;o Social</label>
							<input type="text" name="txtProviderRazaoSocial" id="txtProviderRazaoSocial" class="form-control text-uppercase form-control-sm" value="<?php if(isset($fornecedor)){ echo $fornecedor->RAZAO_SOCIAL; }?>" autocomplete="off" required />
						</div>
						<div class="form-group col-md-4">
							<label for="">Apelido</label>
							<input type="text" name="txtProviderApelido" id="txtProviderApelido" class="form-control text-uppercase form-control-sm" value="<?php if(isset($fornecedor)){ echo $fornecedor->FANTASIA; }?>" autocomplete="off" required />
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-4">
							<label for="">Representante</label>
							<input type="text" id="txtProviderRep" maxlength="30" name="txtProviderRep" class="form-control text-uppercase form-control-sm" value="<?php if(isset($fornecedor)){ echo $fornecedor->REPRESENTANTE; }?>" autocomplete="off" required />
						</div>

						<div class="form-group col-md-4">
							<label for="">Telefone</label>
							<input type="text" id="txtProviderFone" maxlength="30" name="txtProviderFone" class="form-control form-control-sm" value="<?php if(isset($fornecedor)){ echo $fornecedor->TELEFONE; }?>" autocomplete="off" required />
						</div>
						<div class="form-group col-md-4">
							<label for="">Telefone 2</label>
							<input type="text" id="txtProviderFone2" maxlength="30" name="txtProviderFone2" class="form-control form-control-sm" value="<?php if(isset($fornecedor)){ echo $fornecedor->TELEFONE2; }?>" autocomplete="off"/>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-8">
							<label for="">Endere&ccedil;o</label>
							<input type="text" id="txtProviderAddress" name="txtProviderAddress" class="form-control text-uppercase form-control-sm" value="<?php if(isset($fornecedor)){ echo $fornecedor->ENDERECO; }?>" autocomplete="off" required />
						</div>
						<div class="form-group col-md-2">
							<label for="">N&uacute;mero</label>
							<input type="text" id="txtProviderNumber" name="txtProviderNumber" class="form-control form-control-sm" value="<?php if(isset($fornecedor)){ echo $fornecedor->NUMERO_ENDERECO; }?>" autocomplete="off" required />
						</div>
						<div class="form-group col-md-2">
							<label for="">CEP</label>
							<input type="text" id="txtProviderPostal" name="txtProviderPostal" class="form-control text-uppercase form-control-sm" value="<?php if(isset($fornecedor)){ echo $fornecedor->CEP; }?>" autocomplete="off" required />
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-4">
							<label for="">Tempo Resposta</label>
							<input type="number" id="txtProviderTime" name="txtProviderTime" min="0" class="form-control form-control-sm" value="<?php if(isset($fornecedor)){ echo $fornecedor->PRAZO_ENTREGA; }?>" autocomplete="off" data-toggle="tooltip" data-placement="top" title="Cada 5 dias &uacute;teis = 1 per&iacute;odo" required />
						</div>
						<div class="form-group col-md-6">
							<label for="">Cidade</label><br/>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control text-uppercase" id="txtProviderCity" name="txtProviderCity" value="<?php if(isset($cidade)){ echo $cidade->NOME; }?>" autocomplete="off" readonly="true" required />
								<span class="input-group-append">
									<button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalSearchCity">...</button>
								</span>
							</div>
						</div>
						<div class="form-group col-md-2">
							<label for="cbEstado">Estado</label>
							<select class="form-control text-uppercase form-control-sm" name="cbEstado" id="cbEstado" required>
								<option value="">&laquo; Selecione &raquo;</option>
								<option value="AC"<?php if(isset($cidade)){ if($cidade->UF=="AC"){ echo " selected"; } }?>>AC</option>
								<option value="AL"<?php if(isset($cidade)){ if($cidade->UF=="AL"){ echo " selected"; } }?>>AL</option>
								<option value="AM"<?php if(isset($cidade)){ if($cidade->UF=="AM"){ echo " selected"; } }?>>AM</option>
								<option value="AP"<?php if(isset($cidade)){ if($cidade->UF=="AP"){ echo " selected"; } }?>>AP</option>
								<option value="BA"<?php if(isset($cidade)){ if($cidade->UF=="BA"){ echo " selected"; } }?>>BA</option>
								<option value="CE"<?php if(isset($cidade)){ if($cidade->UF=="CE"){ echo " selected"; } }?>>CE</option>
								<option value="DF"<?php if(isset($cidade)){ if($cidade->UF=="DF"){ echo " selected"; } }?>>DF</option>
								<option value="ES"<?php if(isset($cidade)){ if($cidade->UF=="ES"){ echo " selected"; } }?>>ES</option>
								<option value="GO"<?php if(isset($cidade)){ if($cidade->UF=="GO"){ echo " selected"; } }?>>GO</option>
								<option value="MA"<?php if(isset($cidade)){ if($cidade->UF=="MA"){ echo " selected"; } }?>>MA</option>
								<option value="MG"<?php if(isset($cidade)){ if($cidade->UF=="MG"){ echo " selected"; } }?>>MG</option>
								<option value="MS"<?php if(isset($cidade)){ if($cidade->UF=="MS"){ echo " selected"; } }?>>MS</option>
								<option value="MT"<?php if(isset($cidade)){ if($cidade->UF=="MT"){ echo " selected"; } }?>>MT</option>
								<option value="PA"<?php if(isset($cidade)){ if($cidade->UF=="PA"){ echo " selected"; } }?>>PA</option>
								<option value="PB"<?php if(isset($cidade)){ if($cidade->UF=="PB"){ echo " selected"; } }?>>PB</option>
								<option value="PE"<?php if(isset($cidade)){ if($cidade->UF=="PE"){ echo " selected"; } }?>>PE</option>
								<option value="PI"<?php if(isset($cidade)){ if($cidade->UF=="PI"){ echo " selected"; } }?>>PI</option>
								<option value="PR"<?php if(isset($cidade)){ if($cidade->UF=="PR"){ echo " selected"; } }?>>PR</option>
								<option value="RJ"<?php if(isset($cidade)){ if($cidade->UF=="RJ"){ echo " selected"; } }?>>RJ</option>
								<option value="RN"<?php if(isset($cidade)){ if($cidade->UF=="RN"){ echo " selected"; } }?>>RN</option>
								<option value="RO"<?php if(isset($cidade)){ if($cidade->UF=="RO"){ echo " selected"; } }?>>RO</option>
								<option value="RR"<?php if(isset($cidade)){ if($cidade->UF=="RR"){ echo " selected"; } }?>>RR</option>
								<option value="RS"<?php if(isset($cidade)){ if($cidade->UF=="RS"){ echo " selected"; } }?>>RS</option>
								<option value="SC"<?php if(isset($cidade)){ if($cidade->UF=="SC"){ echo " selected"; } }?>>SC</option>
								<option value="SE"<?php if(isset($cidade)){ if($cidade->UF=="SE"){ echo " selected"; } }?>>SE</option>
								<option value="SP"<?php if(isset($cidade)){ if($cidade->UF=="SP"){ echo " selected"; } }?>>SP</option>
								<option value="TO"<?php if(isset($cidade)){ if($cidade->UF=="TO"){ echo " selected"; } }?>>TO</option>
							</select>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="">CNPJ(s)</label>
							<textarea class="form-control form-control-sm" id="txtCNPJS" name="txtCNPJS" data-toggle="tooltip" data-placement="top" title="Adicionar um por linha" required><?php if(isset($fornecedor)){
									foreach($cnpjs as $CNPJ){
										if(trim($CNPJ->CNPJ)!=""){
											echo trim($CNPJ->CNPJ)."\n";
										}
									}
								}?></textarea>
						</div>
						<div class="form-group col-md-6">
							<label for="">Observa&ccedil;&otilde;es</label>
							<textarea class="form-control form-control-sm text-uppercase" id="txtObs" name="txtObs"><?php if(isset($fornecedor)){ echo $fornecedor->OBSERVACAO; }?></textarea>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="nav-bank">
					<div class="form-group">
						<div class="text-center"><br/>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-default<?php if(isset($fornecedor)){ if($fornecedor->TIPO_CONTA=="C"){ echo " active"; } } ?>">
									<input type="radio" name="rdProviderAccountType" id="rdProviderCC" value="0"<?php if(isset($fornecedor)){ if($fornecedor->TIPO_CONTA=="0"){ echo " checked"; } } ?>> Conta Corrente
								</label>
								<label class="btn btn-default<?php if(isset($fornecedor)){ if($fornecedor->TIPO_CONTA=="1"){ echo " active"; } } ?>">
									<input type="radio" name="rdProviderAccountType" id="rdProviderPP" value="1"<?php if(isset($fornecedor)){ if($fornecedor->TIPO_CONTA=="1"){ echo " checked"; } } ?>> Conta Poupan&ccedil;a
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="label-group">Banco</label>
						<div class="input-group input-group-sm">
							<input type="text" id="txtProviderBank" name="txtProviderBank" value="<?php if(isset($banco)){ echo $banco->NOME; }?>" class="form-control" autocomplete="off" readonly=""/>
							<span class="input-group-append">
								<button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalSearchBank">...</button>
							</span>
						</div>

					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="">Ag&ecirc;ncia</label>
							<input type="text" id="txtProviderAgency" name="txtProviderAgency" value="<?php if(isset($fornecedor)){ echo $fornecedor->AGENCIA; }?>" class="form-control form-control-sm"/>
						</div>
						<div class="form-group col-md-6">
							<label for="">N&uacute;mero da Conta</label>
							<input type="text" id="txtProviderAccountNumber" name="txtProviderAccountNumber" value="<?php if(isset($fornecedor)){ echo $fornecedor->NUM_CONTA; }?>" class="form-control form-control-sm">
						</div>
					</div>
					<div class="form-group">
						<label class="label-group">Favorecido</label>
						<input type="text" id="txtProviderAccountName" name="txtProviderAccountName" class="form-control form-control-sm" value="<?php if(isset($fornecedor)){ echo $fornecedor->NOME_CONTA; }?>" autocomplete="off"/>
					</div>
				</div>
			</div>
		</div>
        <div class="card-footer text-right">
            <button type="submit" id="btnSave" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
        </div>
	</div>
</form><br/>

<?php $this->Dialog->city(); ?>

<?php $this->Dialog->bank(); ?>

<script type="text/javascript">
function checkAba(){
    if($("#nav-home-tab").hasClass("active")){
        if($("#txtProviderRazaoSocial").val()!=""){
            if($("#txtProviderApelido").val()!=""){
                if($("#txtProviderRep").val()!=""){
                    if($("#txtProviderFone").val()!=""){
                        if($("#txtProviderAddress").val()!=""){
                            if($("#txtProviderNumber").val()!=""){
                                if($("#txtProviderPostal").val()!=""){
                                    if($("#txtProviderTime").val()!=""){
                                        if($("#txtProviderCity").val()!=""){
                                            if($("#cbEstado").val()!=""){
                                                if($("#txtCNPJS").val()!=""){
                                                    $("#nav-bank-tab").trigger("click");
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
                    }
                }
            }
        }
    }

    return true;
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

    $("#txtProviderFone").mask(MaskBehavior,fnOptions);
    $("#txtProviderFone2").mask(MaskBehavior,fnOptions);
    $('#txtProviderPostal').mask('00000-000');

});

$(document).on("click","#btnNext",function(){
	$('#nav-tab a[href="#nav-bank"]').tab('show')
	//$('.nav-tabs .active').parent().next('li').find('a').trigger('click');
});

$(document).on("submit","#frmRegs",function(evt){
	event.preventDefault();

	 var tipoConta = null;
	if($("#rdProviderCC")[0].checked)
			tipoConta = "C";
	if($("#rdProviderPP")[0].checked)
			tipoConta = "P";

	var $dataForm = {
		IDFORNECEDOR   : $("#txtIdProvider").val(),
		RAZAO_SOCIAL   : $("#txtProviderRazaoSocial").val(),
		FANTASIA       : $("#txtProviderApelido").val(),
		CEP            : $("#txtProviderPostal").val(),
		NUMERO_ENDERECO: $("#txtProviderNumber").val(),
		PRAZO_ENTREGA  : $("#txtProviderTime").val(),
		TELEFONE       : $("#txtProviderFone").val(),
		TELEFONE2      : $("#txtProviderFone2").val(),
		ENDERECO       : $("#txtProviderAddress").val(),
		IDCIDADE       : $("#txtIdCity").val(),
		REPRESENTANTE  : $("#txtProviderRep").val(),
		IDBANCO        : $("#txtIdBank").val(),
		NUM_CONTA      : $("#txtProviderAccountNumber").val(),
		AGENCIA        : $("#txtProviderAgency").val(),
		TIPO_CONTA     : tipoConta,
		NOME_CONTA     : $("#txtProviderAccountName").val(),
		OBSERVACAO     : $("#txtObs").val(),
		CNPJS          : $("#txtCNPJS").val()
	};

	$.ajax({
		headers: {
			'X-CSRF-Token': csrf
		},
		method: 'POST',
		url: '<?=$this->Url->build("/system/provider_save")?>',
		data: $dataForm,
		dataType : 'json',
		success: function(data){
			if(data==true){
				bootbox.alert("Fornecedor salvo com sucesso!",function(){ document.location.href='<?=$this->Url->build("/system/provider")?>'; });
			}else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es do fornecedor!");
				clearScreen();
			}
		}
	});

});

$(document).on("click","#btnUseCity",function(){
    $("input[name='rdCidade[]']").each(function(){
        if($(this)[0].checked){
            var idCidade = $(this).val();

            $("#txtIdCity").val(idCidade);
            $("#txtProviderCity").val($("#txtCityResultNome_"+idCidade).val());
			$("#cbEstado").val($("#txtCityResultUF_"+idCidade).val());
            $("#modalSearchCity").modal('hide');
        }
    });
});

$(document).on("click","#btnUseBank",function(){
    $("input[name='rdBanco[]']").each(function(){
        if($(this)[0].checked){
            var idBanco = $(this).val();

            $("#txtIdBank").val(idBanco);
            $("#txtProviderBank").val($("#txtBankResultNome_"+idBanco).val());
            $("#modalSearchBank").modal('hide');
        }
    });
});

function clearScreen(){
	$("#frmRegs").removeClass("was-validated");

    $("#txtIdProvider").val("");
    $("#txtProviderRazaoSocial").val("");
    $("#txtProviderApelido").val("");
    $("#txtProviderPostal").val("");
    $("#txtProviderNumber").val("");
    $("#txtProviderFone").val("");
    $("#txtProviderFone2").val("");
    $("#txtProviderAddress").val("");
    $("#txtIdCity").val("");
    $("#txtProviderRep").val("");
    $("#txtIdBank").val("");
    $("#txtProviderAccountNumber").val("");
    $("#txtProviderAgency").val("");
    $("#rdProviderAccountType").val("");
    $("#txtProviderAccountName").val("");
    $("#txtObs").val("");
    $("#txtCNPJS").val("");

    $("#txtProviderBank").val("");
    $("#txtProviderCity").val("");

    $("#txtProviderRazaoSocial").focus();
}
</script>
