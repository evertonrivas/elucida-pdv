<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
    <div class="card">
        <div class="card-header">
        	<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro de Sa&iacute;da do Fluxo de Caixa
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/financial/outflow');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
					<a href="javascript:showOptions()" class="btn btn-success btn-sm"><i class="fas fa-search-dollar"></i> Tipos de Despesa</a>
				</div>
			</div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="cbOperacaoFinanceira">Opera&ccedil;&atilde;o Financeira</label>
                <select name="cbOperacaoFinanceira" id="cbOperacaoFinanceira" class="form-control form-control-sm" required>
                    <option value="">&laquo; Selecione &raquo;</option>
                    <?php foreach($operacaolist as $operacao):?>
                    <option value="<?php echo $operacao->IDOPERACAOFINANCEIRA; ?>"><?php echo $operacao->NOME; ?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group" id="dvDespesas"></div>
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
      	<h5 class="modal-title" id="modalLabel">Sele&ccedil;&atilde;o de Tipos de Despesa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <table class="table">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>Tipo de Despesa</th>
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

$(document).on("hide.bs.modal","#modalExpense",function(){
    getBasket();
});

$(document).on("show.bs.modal","#modalExpense",function(){
    getOptions();
});

$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();

        var frmData = {
            IDOPERACAOFINANCEIRA : $("#cbOperacaoFinanceira").val()
        };

        $.ajax({
        	headers:{
				'X-CSRF-Token':csrf
			},
            type: 'POST',
            url: '<?=$this->Url->build("/financial/outflow_save")?>',
            data: frmData,
            dataType: 'json',
            success: function(data){
                if(data){
					bootbox.alert("Sa&iacute;da de fluxo de caixa salva com sucesso!",function(){ document.location.href='/financial/outflow'; });
				}
				else{
					bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es da Sa&iacute;da do fluxo de caixa!",function(){ clearFields(); });
				}
            }
        });
});
	
function getBasket(){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
		data:{ IDOPERACAOFINANCEIRA: $("#cbOperacaoFinanceira").val() },
        url: '<?=$this->Url->build("/financial/outflow_basket/")?>',
        success:function(data){
            $("#dvDespesas").html(data);
        }
    });
}

function showOptions(){
	if($("#cbOperacaoFinanceira").val()==""){
		bootbox.alert('Por favor informe a Opera&ccedil;&atilde;o Financeira!');
	}else{
		$("#modalExpense").modal({
			backdrop: 'static'
		});
	}
}

function getOptions(){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/financial/outflow_options_get")?>',
        success: function(data){
            $("#dvAccounts").html(data);
        }
    });
}

function addOption(id){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},//bugdetAccountAdd
		type:'POST',
		data:{ IDTIPODESPESA: id, IDOPERACAOFINANCEIRA: $("#cbOperacaoFinanceira").val()  },
        url: '<?=$this->Url->build("/financial/outflow_option_add/")?>',
        success:function(data){
            if(data){
                getOptions();
            }
        }
    });
}

function delOption(id){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
		type:'POST',
		data:{ IDTIPODESPESA: id },
        url: '<?=$this->Url->build("/financial/outflow_option_del/")?>',
        success:function(data){
            if(data){
                getOptions();
            }
        }
    });
}
</script>