<br/><form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
    <input type="hidden" id="txtIdCliente" name="txtIdCliente" value="<?php if(isset($cliente)){ echo $cliente->IDCLIENTE; }?>"/>
    <input type="hidden" id="txtCadClienteDataCadastro" name="txtCadClienteDataCadastro" value="<?php if(isset($cliente)){ echo $cliente->DATA_CADASTRO; }else{ date("Y-m-d"); }?>"/>
    <div class="card">
        <div class="card-header">	
        	<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Categoria
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/retail/customer');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
        </div>
        <div class="card-body">
            <?php if(!$is_mobile):?>
            <div class="form-group">
                  <label>Nome:</label>
                  <input type="text" id="txtCadClienteNome" name="txtCadClienteNome" class="form-control text-uppercase form-control-sm" autocomplete="off" value="<?php if(isset($cliente)){ echo $cliente->NOME; }?>" required/>
              </div>
              <div class="form-group">
                  <label>E-mail:</label>
                  <input type="text" id="txtCadClienteEmail" name="txtCadClienteEmail" class="form-control text-lowercase form-control-sm" autocomplete="off" value="<?php if(isset($cliente)){ echo $cliente->EMAIL; }?>" required/>
              </div>
              <div class="row">
                  <div class="form-group col-sm">
                    <label>Nascimento:</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control date" data-provide='datepicker' data-date-format='dd/mm/yyyy' data-date-language='pt-BR' data-date-autoclose='true' data-date-today-highlight='true' id="txtCadClienteNasc" name="txtCadClienteNasc" value="<?php if(isset($cliente)){ echo $cliente->NASCIMENTO; }?>" required/>
                        <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                    </div>
                  </div>
                  <div class="form-group col-sm">
                    <label>G&ecirc;nero:</label>
                    <select id="cbCadClienteGenero" name="cbCadClienteGenero" class="form-control form-control-sm" required>
                        <option value=""></option>
                        <option value="1"<?php if(isset($cliente)){ if($cliente->GENERO=="1"){ echo " selected"; } }?>>Masculino</option>
                        <option value="2"<?php if(isset($cliente)){ if($cliente->GENERO=="2"){ echo " selected"; } }?>>Feminino</option>
                        <option value="3"<?php if(isset($cliente)){ if($cliente->GENERO=="3"){ echo " selected"; } }?>>Transexual</option>
                    </select>
                  </div>
                  <div class="form-group col-sm">
                  	<label>CPF:</label>
                  <input type="text" id="txtCadClienteCPF" name="txtCadClienteCPF" class="form-control form-control-sm" placeholder="___.___.___-__" value="<?php if(isset($cliente)){ echo $cliente->CPF; }?>" required/>
                  </div>
              </div>
              <div class="row">
                  <div class="form-group col-sm">
                    <label>Telefone</label>
                    <input type="text" id="txtCadClienteFone" name="txtCadClienteFone" class="form-control form-control-sm" pattern="\([0-9]{2}\)[\s][0-9]{4,5}-[0-9]{4,5}" value="<?php if(isset($cliente)){ echo $cliente->TELEFONE; }?>" required/>
                  </div>
                  <div class="form-group col-sm">
                    <label>Telefone 2</label>
                    <input type="text" id="txtCadClienteFone2" name="txtCadClienteFone2" class="form-control form-control-sm" value="<?php if(isset($cliente)){ echo $cliente->TELEFONE2; }?>"/>
                  </div>
                  <div class="form-group col-sm">
                    <label>CEP</label>
                    <input type="text" id="txtCadClienteCEP" name="txtCadClienteCEP" class="form-control" value="<?php if(isset($cliente)){ echo $cliente->CEP; }?>" required/>
                  </div>
              </div>
            <?php else: ?>
            <div class="form-group">
                <label>Nome:</label>
                <input type="text" id="txtCadClienteNome" name="txtCadClienteNome" class="form-control text-uppercase" autocomplete="off" value="<?php if(isset($cliente)){ echo $cliente->NOME; }?>"/>
            </div>
            <div class="form-group">
                <label>E-mail:</label>
                <input type="email" id="txtCadClienteEmail" name="txtCadClienteEmail" class="form-control text-lowercase" autocomplete="off" value="<?php if(isset($cliente)){ echo $cliente->EMAIL; }?>"/>
            </div>
            <div class="form-group">
                <label>Nascimento:</label>
                <div class="input-group date">
                    <input type="text" class="form-control input-sm" id="txtCadClienteNasc" name="txtCadClienteNasc" value="<?php if(isset($cliente)){ echo $time->parseDate($cliente->NASCIMENTO)->i18nFormat("dd/MM/yyyy"); }?>"/><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                </div>
            </div>
            <div class="form-group">
                <label>G&ecirc;nero:</label>
                <select id="cbCadClienteGenero" name="cbCadClienteGenero" class="form-control">
                    <option value=""></option>
                    <option value="1"<?php if(isset($cliente)){ if($cliente->GENERO=="1"){ echo " selected"; } }?>>Masculino</option>
                    <option value="2"<?php if(isset($cliente)){ if($cliente->GENERO=="2"){ echo " selected"; } }?>>Feminino</option>
                </select>
            </div>
            <div class="form-group">
                <label>CPF:</label>
                <input type="text" id="txtCadClienteCPF" name="txtCadClienteCPF" class="form-control" placeholder="___.___.___-__" value="<?php if(isset($cliente)){ echo $cliente->CPF; }?>"/>
            </div>
            <div class="form-group">
				<label>Telefone</label>
				<input type="tel" id="txtCadClienteFone" name="txtCadClienteFone" class="form-control" value="<?php if(isset($cliente)){ echo $cliente->TELEFONE; }?>"/>
            </div>
            <div class="form-group">
                <label>Telefone 2</label>
                <input type="tel" id="txtCadClienteFone2" name="txtCadClienteFone2" class="form-control" value="<?php if(isset($cliente)){ echo $cliente->TELEFONE2; }?>"/>
            </div>
            <div class="form-group">
                <label>CEP</label>
                <input type="text" id="txtCadClienteCEP" name="txtCadClienteCEP" class="form-control" value="<?php if(isset($cliente)){ echo $cliente->CEP; }?>"/>
            </div>
            <?php endif; ?>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-primary btn-sm" id="btnSend" name="btnSend"><i class="fas fa-hdd"></i> Salvar</button>
            </div>
        </div>
    </div>
</form><br/>

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
    
    $("#txtCadClienteFone").mask(MaskBehavior,fnOptions);
    $("#txtCadClienteFone2").mask(MaskBehavior,fnOptions);
    $('#txtCadClienteCPF').mask('000.000.000-00');
    $('#txtCadClienteCEP').mask('00000-000');
});

$(document).on("submit","#frmRegs",function(event){
	// Prevent form submission
    event.preventDefault();
    
    var dataForm = {
        IDCLIENTE  : $("#txtIdCliente").val(),
        NASCIMENTO : $("#txtCadClienteNasc").val(),
        GENERO     : $("#cbCadClienteGenero").val(),
        CPF        : $("#txtCadClienteCPF").val(),
        TELEFONE   : $("#txtCadClienteFone").val(),
        TELEFONE2  : $("#txtCadClienteFone2").val(),
        NOME       : $("#txtCadClienteNome").val(),
        EMAIL      : $("#txtCadClienteEmail").val(),
        CEP        : $("#txtCadClienteCEP").val(),
        DATA_CADASTRO: $("txtCadClienteDataCadastro").val()
    };
    
    $.ajax({
		headers:{
			'X-CSRF-Token':csrf
		},
		method: 'post',
		url: '<?=$this->Url->build("/retail/customer_save/")?>',
		data: dataForm,
		dataType: 'json',
		success: function(data){
		   if(data){
		        bootbox.alert("Cliente salvo com sucesso!",function(){ document.location.href='/retail/customer'; });
		    }else{
		        bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es do Cliente!");
		    }
		}
    });
});
	
function clearFields(){
	
	$("#frmRegs").removeClass("was-validated");
	
    $("#txtIdCliente").val("");
    $("#txtCadClienteNome").val("");
    $("#txtCadClienteDataCadastro").val("");
    $("#txtCadClienteEmail").val("");
    $("#txtCadClienteNasc").val("");
    $("#txtCadClienteCPF").val("");
    $("#txtCadClienteCEP").val("");
    $("#cbCadClienteGenero").val("");
    $("#txtCadClienteFone").val("");
    $("#txtCadClienteFone2").val("");
    $("#txtCadClienteNome").focus();
}
</script>