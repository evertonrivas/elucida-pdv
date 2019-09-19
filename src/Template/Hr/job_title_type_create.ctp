<br/>
<form id="frmRegs" name="frmRegs" class="needs-validation" novalidate>
<input type="hidden" id="txtIdJobTitleType" name="txtIdJobTitleType" value="<?php if(isset($job_title_type)){ echo $job_title_type->IDCARGOTIPO; }?>"/>
<div class="card">
    <div class="card-header">
    	<div class="row">
			<div class="col-sm">
				<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Tipo de Cargo
			</div>
			<div class="col-sm text-right">
				<a href="<?php echo $this->Url->build('hr/job_title_type');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
			</div>
		</div>
    </div>
    <div class="card-body">
    	<div class="form-group">
    		<label for="txtJobTitleName">Nome</label>
    		<input type="text" id="txtJobTitleTypeName" name="txtJobTitleTypeName" class="form-control form-control-sm text-uppercase" value="<?php if(isset($job_title_type)){ echo $job_title_type->NOME; }?>" required/>
    	</div>
        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><i class="fas fa-hdd"></i> Salvar</button>
        </div>
    </div>
</div>
</form>
<script>
/**
* Evento que trata o envio do formulario
*/
$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();
        
    var dataForm = {
        IDCARGOTIPO    : $("#txtIdJobTitleType").val(),
        NOME           : $("#txtJobTitleTypeName").val()
    };

    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method  : 'POST',
        data    : dataForm,
        url     : '<?=$this->Url->build("/hr/job_title_type_save")?>',
        success : function(data){
            if(data){
                bootbox.alert('Tipo de Cargo salvo com sucesso!',function(){ document.location.href='<?=$this->Url->build("/hr/job_title_type")?>'; })
            }else{
                bootbox.alert('Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es do Tipo de Cargo!');
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
					if($("#txtEmployerTelefone").val()!=""){
						if($("#txtEmployerRecado").val()!=""){
							$("#tabContrato").trigger("click");
						}
					}
				}
			}
		}
	}
	//valida a terceira aba que eh a de contrato
	if($("#tabContrato").hasClass("active")){
		if($("#cbEmployerCargo").val()!=""){
			if($("#cbEmployerJourney").val()!=""){
				if($("#cbEmployerStatus").val()!=""){
					$("#tabBanco").trigger("click");
				}
			}
		}
	}
}


</script>