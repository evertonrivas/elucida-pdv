<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
	<input type="hidden" id="txtIdBudget" name="txtIdBudget" value="<?php if(isset($orcamento)){ echo "1"; }?>"/>
    <div class="card">
        <div class="card-header">
        	<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Or&ccedil;amento
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/financial/budget');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
					<a href="#" data-toggle="modal" data-target="#modalExpense" data-backdrop="static" class="btn btn-success btn-sm"><i class="fas fa-search-dollar"></i> Opera&ccedil;&otilde;es Financeiras</a>
				</div>
			</div>
        </div>
        <div class="card-body">
        	<div class="form-group">
            	<label>Loja</label>
            	<select class="form-control form-control-sm" id="cbStore" name="cbStore" required>
            		<option value="">&laquo; Selecione &raquo;</option>
            		<?php foreach($store_list as $store):?>
            		<option value="<?=$store->IDLOJA?>"<?php if(isset($orcamento)){ if($orcamento->IDLOJA==$store->IDLOJA){ echo " Selected"; } }?>><?=$store->NOME;?></option>
            		<?php endforeach; ?>
            	</select>
            </div>
            <div class="form-group">
                <label class="control-label">Nome do Or&ccedil;amento</label>
                <input type="text" class="form-control text-uppercase form-control-sm" id="txtBudgetName" name="txtBudgetName" value="<?php if(isset($orcamento)){ echo $orcamento->NOME; }?>" required/>
            </div>
            <div class="form-group">
                <label class="control-label">Ano de execu&ccedil;&atilde;o</label>
                <input type="number" class="form-control text-uppercase form-control-sm" id="txtBudgetYear" name="txtBudgetYear" value="<?php if(isset($orcamento)){ echo $orcamento->ANO; }else{ echo date("Y"); }?>" required/>
            </div>
            <div class="form-group" id="lstAccounts">
            </div>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
            </div>
        </div>
    </div>
</form>

<!--MODAL DE BUSCA DOS TIPOS DE DESPESA-->
<div class="modal fade" id="modalExpense" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	<h4 class="modal-title" id="modalLabel">Sele&ccedil;&atilde;o de tipo de despesa</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <table class="table">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>Opera&ccedil;&atilde;o Financeira</th>
                    <th>Tipo de Opera&ccedil;&atilde;o</th>
                </tr>
            </thead>
        </table>
        <form id="frmExpense">
            <div style="min-height: 300px; max-height:300px; overflow-y: scroll;" id="dvAccounts">
                
            </div>
        </form>
      </div>
      <div class="modal-footer">
      	<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> Fechar</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
    
    getBasket();    
});

$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();

    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        type: 'POST',
        url: '<?=$this->Url->build("/financial/budget_save")?>',
        data: $(this).serialize(),
        success: function(data){
            if(data==true){
                bootbox.alert("Or&ccedil;amento salvo com sucesso!",function(){ document.location.href='/financial/budget'; });
            }else{
                 bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es do Or&ccedil;amento!</p>",function(){ clearFields(); });
            }
        }
    });
});

$(document).on("hide.bs.modal","#modalExpense",function(){
    getBasket();
});

$(document).on("show.bs.modal","#modalExpense",function(){
    getAccounts();
});

$(document).ajaxComplete(function(){
    $("input[name='txtValJan[]']").each(function(){
        $(this).mask("##,##0.00",{reverse:true});
    });
    
    $("input[name='txtValFev[]']").each(function(){
        $(this).mask("##,##0.00",{reverse:true});
    });
    
    $("input[name='txtValMar[]']").each(function(){
        $(this).mask("##,##0.00",{reverse:true});
    });
    
    $("input[name='txtValAbr[]']").each(function(){
        $(this).mask("##,##0.00",{reverse:true});
    });
    
    $("input[name='txtValMai[]']").each(function(){
        $(this).mask("##,##0.00",{reverse:true});
    });
    
    $("input[name='txtValJun[]']").each(function(){
        $(this).mask("##,##0.00",{reverse:true});
    });
    
    $("input[name='txtValJul[]']").each(function(){
        $(this).mask("##,##0.00",{reverse:true});
    });
    
    $("input[name='txtValAgo[]']").each(function(){
        $(this).mask("##,##0.00",{reverse:true});
    });
    
    $("input[name='txtValSet[]']").each(function(){
        $(this).mask("##,##0.00",{reverse:true});
    });
    
    $("input[name='txtValOut[]']").each(function(){
        $(this).mask("##,##0.00",{reverse:true});
    });
    
    $("input[name='txtValNov[]']").each(function(){
        $(this).mask("##,##0.00",{reverse:true});
    });
    
    $("input[name='txtValDez[]']").each(function(){
        $(this).mask("##,##0.00",{reverse:true});
    });
});

function addAccount(idOperacaoFinanceira){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},//bugdetAccountAdd
        url: '<?=$this->Url->build("/financial/bugdet_account_add/")?>'+idOperacaoFinanceira,
        success:function(data){
            if(data==true){
                getAccounts();
            }
        }
    });
}

function delAccount(idOperacaoFinanceira){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/financial/budget_account_del/")?>'+idOperacaoFinanceira,
        success:function(data){
            if(data==true){
                getAccounts();
            }
        }
    });
}

function getBasket(){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/financial/budget_accounts_basket/")?>'+$("#cbStore").val()+'/'+$("#txtBudgetYear").val(),
        success:function(data){
            $("#lstAccounts").html(data);
        }
    });
}

function getAccounts(){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/financial/budget_accounts_get")?>',
        success: function(data){
            $("#dvAccounts").html(data);
        }
    });
}
	
function clearFields(){
	//remove a informacao que o form jah foi validado,
	//assim forca uma nova validacao
	$("#frmRegs").removeClass("was-validated");
	
    $("#txtIdOrcamento").val("");
    $("#txtOrcaName").val("");
    $("#cbOrcaName").focus();
}
</script>