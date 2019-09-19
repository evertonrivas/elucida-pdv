<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<br/><form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
    <input type="hidden" id="IDLOJA" name="IDLOJA" value="<?php if(isset($loja)){ echo $loja->IDLOJA; }?>"/>
    <input type="hidden" id="IDCIDADE" name="IDCIDADE" value="<?php if(isset($cidade)){ echo $cidade->IDCIDADE; }?>"/>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Loja
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/system/store');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			<fieldset>
				<legend>Dados da Loja</legend>
				<div class="form-group">
					<label for="NOME">Nome/Apelido</label>
					<input type="text" class="form-control text-uppercase form-control-sm" id="NOME" name="NOME" value="<?php if(isset($loja)){ echo $loja->NOME; }?>" autocomplete="off" required />
				</div>
				<div class="form-row">
					<div class="form-group col-6">
						<label for="ENDERECO">Endere&ccedil;o</label>
						<input type="text" class="form-control text-uppercase form-control-sm" id="ENDERECO" name="ENDERECO" value="<?php if(isset($loja)){ echo $loja->ENDERECO; }?>" autocomplete="off" required />
					</div>
					<div class="form-group col-2">
						<label for="NUM_ENDERECO">N&uacute;mero</label>
						<input type="number" class="form-control form-control-sm" id="NUM_ENDERECO" name="NUM_ENDERECO" value="<?php if(isset($loja)){ echo $loja->ENDERECO_NUM; }?>" autocomplete="off" required />
					</div>
					<div class='form-group col-4'>
						<label for="COMPLEMENTO_ENDERECO">Complemento</label>
						<input type="text" class="form-control text-uppercase form-control-sm" id="COMPLEMENTO_ENDERECO" name="COMPLEMENTO_ENDERECO" value="<?php if(isset($loja)){ echo $loja->ENDERECO_COMPLEMENTO; }?>" autocomplete="off" />
					</div>
				</div>
				<div class="form-row">
					<div class='form-group col-6'>
						<div class="form-group">
							<label for="">Cidade</label>
							<div class="input-group mb-3 input-group-sm">
								<input type="text" class="form-control text-uppercase" id="txtLojaCidadeNome" name="txtLojaCidadeNome" value="<?php if(isset($cidade)){ echo $cidade->NOME; }?>" autocomplete="off" readonly="true"/>
								<span class="input-group-append">
									<button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalSearchCity">...</button>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group col-4">
						<label for="BAIRRO">Estado</label>
						<input type="text" class="form-control text-uppercase form-control-sm" id="BAIRRO" name="BAIRRO" value="<?php if(isset($loja)){ echo $loja->BAIRRO; }?>" autocomplete="off" required />
					</div>
					<div class='form-group col-2'>
						<label for="CEP">CEP</label>
						<input type="text" class="form-control form-control-sm" id="CEP" name="CEP" value="<?php if(isset($loja)){ echo $loja->CEP; }?>" autocomplete="off" required />
					</div>
				</div>
				<div class="form-row">
					<div class='form-group col-4'>
						<label for="TELEFONE">Telefone da Loja</label>
						<input type="text" class="form-control form-control-sm" id="TELEFONE" name="TELEFONE" value="<?php if(isset($loja)){ echo $loja->TELEFONE; }?>" autocomplete="off" required />
					</div>
					<div class='form-group col-4'>
						<label for="RESPONSAVEL">Respons&aacute;vel</label>
						<input type="text" class="form-control text-uppercase form-control-sm" id="RESPONSAVEL" name="RESPONSAVEL" value="<?php if(isset($loja)){ echo $loja->RESPONSAVEL; }?>" autocomplete="off" required />
					</div>
					<div class="form-group col-4">
						<label for="TEL_RESPONSAVEL">Telefone do Respons&aacute;vel</label>
						<input type="text" class="form-control form-control-sm" id="TEL_RESPONSAVEL" name="TEL_RESPONSAVEL" value="<?php if(isset($loja)){ echo $loja->TEL_RESPONSAVEL; }?>" autocomplete="off"/>
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>Informa&ccedil;&otilde;es Fiscais</legend>
				<div class="form-row">
					<div class="form-group col-6">
						<label for="RAZAO_SOCIAL">Raz&atilde;o Social</label>
						<input type="text" id="RAZAO_SOCIAL" name="RAZAO_SOCIAL" class="form-control-sm form-control text-uppercase" value="<?php if(isset($loja)){ echo $loja->RAZAO_SOCIAL; }?>" />
					</div>                                
					<div class="form-group col-6">
						<label for="NOME_FANTASIA">Nome Fantasia</label>
						<input type="text" id="NOME_FANTASIA" name="NOME_FANTASIA" class="form-control form-control-sm text-uppercase" value="<?php if(isset($loja)){ echo $loja->NOME_FANTASIA; }?>" />
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-3">
						<label for="CNPJ">CNPJ</label>
						<input type="text" id="CNPJ" name="CNPJ" class="form-control form-control-sm" value="<?php if(isset($loja)){ echo $loja->CNPJ; }?>"/>
					</div>
					<div class="form-group col-3">
						<label for="CNAE">CNAE</label>
						<input type="text" id="CNAE" name="CNAE" class="form-control form-control-sm" value="<?php if(isset($loja)){ echo $loja->CNAE; }?>"/>
					</div>
					<div class="form-group col-3">
						<label for="INSCRICAO_ESTADUAL">Inscri&ccedil;&atilde;o Estadual</label>
						<input type="text" id="INSCRICAO_ESTADUAL" name="INSCRICAO_ESTADUAL" class="form-control form-control-sm" placeholder="9062328808" value="<?php if(isset($loja)){ echo $loja->INSCRICAO_ESTADUAL; }?>"/>
					</div>
					<div class="form-group col-3">
						<label for="INSCRICAO_MUNICIPAL">Inscri&ccedil;&atilde;o Municipal</label>
						<input type="text" id="INSCRICAO_MUNICIPAL" name="INSCRICAO_MUNICIPAL" class="form-control form-control-sm" placeholder="06618401" value="<?php if(isset($loja)){ echo $loja->INSCRICAO_MUNICIPAL; }?>"/>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-6">
						<label for="DESCONTO_MAXIMO_SEM_SENHA">Desconto M&aacute;ximo sem senha (%)</label>
						<input type="text" id="DESCONTO_MAXIMO_SEM_SENHA" name="DESCONTO_MAXIMO_SEM_SENHA" class="form-control form-control-sm" placeholder="0.05" value="<?php if(isset($loja)){ echo $loja->DESCONTO_MAXIMO_SEM_SENHA; }?>" required />
					</div>
					<div class="form-group col-6">
						<label for="DESCONTO_SENHA">Senha do desconto</label>
						<input type="password" id="DESCONTO_SENHA" name="DESCONTO_SENHA" class="form-control form-control-sm" value="" />
					</div>
				</div>
				<div class="form-group">
					<div class="custom-control custom-switch">
						<input type="checkbox" class="custom-control-input" id="VENDE_ESTOQUE_ZERADO" name="VENDE_ESTOQUE_ZERADO" value="1"<?php if(isset($loja)){ if($loja->VENDE_ESTOQUE_ZERADO=="1"){ echo " checked"; } }?>>
						<label class="custom-control-label" for="VENDE_ESTOQUE_ZERADO">Realizar venda com estoque zerado</label>
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>NF-e/NFC-e</legend>
				<div class="form-group">
					<div class="custom-control custom-switch">
						<input type="checkbox" id="NFE_EMITE" class="custom-control-input" name="NFE_EMITE" value="1"<?php if(isset($loja)){ if($loja->NFE_EMITE=="1"){ echo " checked"; } }?>/>
						<label class="custom-control-label" for="NFE_EMITE"> Realiza emiss&atilde;o de NF-e/NFC-e</label>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-3">
						<label for="NFE_AMBIENTE">Ambiente</label>
						<select class="form-control form-control-sm" id="NFE_AMBIENTE" name="NFE_AMBIENTE">
							<option value="1"<?php if(isset($loja)){ if($loja->NFE_AMBIENTE=="1"){ echo " selected"; } }?>>Produ&ccedil;&atilde;o</option>
							<option value="2"<?php if(isset($loja)){ if($loja->NFE_AMBIENTE=="2"){ echo " selected"; } }?>>Homologa&ccedil;&atilde;o</option>
						</select>
					</div>
					<div class="form-group col-3">
						<label for="NFE_TIPO_EMISSAO">Tipo de Emiss&atilde;o</label>
						<select class="form-control form-control-sm" id="NFE_TIPO_EMISSAO" name="NFE_TIPO_EMISSAO">
							<option value="1"<?php if(isset($loja)){ if($loja->NFE_TIPO_EMISSAO=="1"){ echo " selected"; } }?>>Emiss&atilde;o Normal</option>
							<option value="2"<?php if(isset($loja)){ if($loja->NFE_TIPO_EMISSAO=="2"){ echo " selected"; } }?>>Conting&ecirc;ncia FS-IA</option>
							<option value="3"<?php if(isset($loja)){ if($loja->NFE_TIPO_EMISSAO=="3"){ echo " selected"; } }?>>Conting&ecirc;ncia SCAN</option>
							<option value="4"<?php if(isset($loja)){ if($loja->NFE_TIPO_EMISSAO=="4"){ echo " selected"; } }?>>Conting&ecirc;ncia DPEC</option>
							<option value="5"<?php if(isset($loja)){ if($loja->NFE_TIPO_EMISSAO=="5"){ echo " selected"; } }?>>Conting&ecirc;ncia FS-DA</option>
							<option value="6"<?php if(isset($loja)){ if($loja->NFE_TIPO_EMISSAO=="6"){ echo " selected"; } }?>>Conting&ecirc;ncia SVC-AN</option>
							<option value="7"<?php if(isset($loja)){ if($loja->NFE_TIPO_EMISSAO=="7"){ echo " selected"; } }?>>Conting&ecirc;ncia SVC-RS</option>
							<option value="9"<?php if(isset($loja)){ if($loja->NFE_TIPO_EMISSAO=="9"){ echo " selected"; } }?>>Conting&ecirc;ncia off-line da NFC-e</option>
						</select>
					</div>
					<div class="form-group col-3">
						<label for="NFE_TRIBUTACAO">Percentual de Imposto da NFC-e</label>
						<input type="text" class="form-control form-control-sm" id="NFE_TRIBUTACAO" name="NFE_TRIBUTACAO" placeholder="0.0275" value="<?php if(isset($loja)){ echo $loja->NFE_TRIBUTACAO; }?>"/>
					</div>
					<div class="form-group col-3">
						<label for="NFE_UF_DEST">UF de Destino</label>
						<input type="text" class="form-control form-control-sm text-uppercase" id="NFE_UF_DEST" name="NFE_UF_DEST" placeholder="PR" value="<?php if(isset($loja)){ echo $loja->NFE_UF_DEST; }?>"/>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-4">
						<label for="NFE_CSC">CSC</label>
						<input type="text" class="form-control form-control-sm text-uppercase" id="NFE_CSC" name="NFE_CSC" value="<?php if(isset($loja)){ echo $loja->NFE_CSC; }?>"/>
					</div>
					<div class="form-group col-2">
						<label for="NFE_CSC_TOKEN">Token CSC</label>
						<input type="text" class="form-control form-control-sm text-uppercase" id="NFE_CSC_TOKEN" name="NFE_CSC_TOKEN" placeholder="000001" value="<?php if(isset($loja)){ echo $loja->NFE_CSC_TOKEN; }?>"/>
					</div>
					<div class="form-group col-4">
						<label for="NFE_CERT_DIGITAL">Certificado Digital</label>
						<div class="custom-file form-control-sm">
						  <input type="file" class="custom-file-input" id="NFE_CERT_DIGITAL" name="NFE_CERT_DIGITAL" accept=".pfx">
						  <label class="custom-file-label" for="NFE_CERT_DIGITAL" data-browse="Procurar">Selecione o arquivo</label>
						</div>
					</div>
					<div class="form-group col-2">
						<label for="NFE_CERT_PASSWORD">Senha do Certificado</label>
						<input type="password" class="form-control form-control-sm text-uppercase" id="NFE_CERT_PASSWORD" name="NFE_CERT_PASSWORD" placeholder="******" value="<?php if(isset($loja)){ echo $loja->NFE_CERT_PASSWORD; }?>"/>
					</div>
				</div>
			</fieldset>
			<div class="form-group text-right">
				<button type="submit" id="btnSave" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
			</div>
		</div>
	</div>
</form><br/>

<?php $this->Dialog->city(); ?>
<script>
$(document).ready(function(){
    var MaskBehavior = function (val) {
      return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
    fnOptions = {
      onKeyPress: function(val, e, field, options) {
          field.mask(MaskBehavior.apply({}, arguments), options);
        }
    };

    $("#txtLojaCEP").mask('00000-000');
    $("#txtLojaTelefone").mask(MaskBehavior,fnOptions);
    $("#txtLojaTelResponsavel").mask(MaskBehavior,fnOptions);
    $("#txtLojaCNPJ").mask("00.000.000/0000-00");
    $("#txtLojaNfePercImp").mask("0.0000",{ reverse:true });
    $("#txtLojaPercDescMax").mask("0.00",{ reverse:true });
});

$(document).on("submit","#frmRegs",function(evt){
	evt.preventDefault();

	$.ajax({
		headers: {
			'X-CSRF-Token': csrf
		},
		type: 'POST',
		url: '<?=$this->Url->build("/system/store_save")?>',
		data: new FormData(this),
		cache: false,
		contentType: false,
		processData: false,
		success: function(data){
			if(data==true){
				bootbox.alert("Loja salva com sucesso!",function(){ document.location.href='<?=$this->Url->build("/system/store/")?>'; });
			}else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es da loja!");
				clearScreen();
			}
		}
	});
});

$(document).on("click","#btnUseCity",function(){
    $("input[name='rdCidade[]']").each(function(){
        if($(this)[0].checked){
            var idCidade = $(this).val();
            
            $("#txtIdCidadeLoja").val(idCidade);
            $("#txtLojaCidadeNome").val($("#txtCityResultNome_"+idCidade).val());
            $("#modalSearchCity").modal('hide');
        }
    });
});

function clearFields(){
    $("#txtIdLoja").val("");
    $("#txtIdCidadeLoja").val("");
    $("#txtLojaNome").val("");
    $("#txtLojaCidadeNome").val("");
    $("#txtLojaEndereco").val("");
    $("#txtLojaEndNumero").val("");
    $("#txtLojaBairro").val("");
    $("#txtLojaCEP").val("");
    $("#txtLojaTelefone").val("");
    $("#txtLojaResponsavel").val("");
    $("#txtLojaTelResponsavel").val("");
    $("#txtLojaComplEnd").val("");
    
    $("#txtLojaCNAE").val("");
    $("#txtLojaCNPJ").val("");
    $("#txtLojaIE").val("");
    $("#txtLojaIM").val("");
    $("#txtLojaFantasia").val("");
    $("#txtLojaRazao").val("");
    $("#txtLojaPercDescMax").val("");
    $("#txtLojaSenhaDesc").val("");
    $("#cbLojaNfeAmbiente").val("");
    $("#txtLojaSMTPPass").val("");
    $("#txtLojaSMTPPort").val("");
    $("#txtLojaSMTPServer").val("");
    $("#txtLojaSMTPUser").val("");
    $("#cbLojaNfeTipoEmissao").val("");
    $("#txtLojaNfePercImp").val("");
    $("#txtLojaNfeUfDest").val("");
    $("#txtLojaWSURL").val("");
    $("#txtLojaWSPass").val("");
    $("#txtLojaWSUser").val("");
    
    $("#txtLojaNome").focus();
}
</script>