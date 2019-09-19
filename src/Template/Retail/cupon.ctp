<br/>    
<form id="frmRegs" name="frmRegs">
	<input type="hidden" id="_csrfToken" name="_csrfToken" value=<?= json_encode($this->request->getParam('_csrfToken')) ?>>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> <?=$title?>
				</div>
				<div lang="col-sm text-right">
					<a href="javascript:gen_tags();" class="btn btn-success btn-sm"><i class="fas fa-tag"></i> Exportar Etiqueta</a>
					<button type="button" data-toggle="modal" data-target="#modalGenerator" data-backdrop="static" class="btn btn-warning btn-sm"><i class="fas fa-user-cog"></i> Gerar</button>
					<button type="button" class="btn btn-dark btn-sm" data-toggle="modal" data-target="#modalFilter"><i class="fas fa-filter"></i> Filtrar</button>
				</div>
			</div>
		</div>
		<div class="card-body" id="divResult"></div>
	</div>
</form>
<br/>

<!--INICIO DO MODAL DE GERACAO DE CUPOM-->
<div class="modal fade" id="modalGenerator" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form name="frmGenerator" id="frmGenerator" class="needs-validation" novalidate>
      <div class="modal-header">
      	<h5 class="modal-title" id="modalLabel">Gera&ccedil;&atilde;o de Cupom</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">      	
        <div class="row">
            <div class="form-group col-sm">
                <label class="control-label">Descri&ccedil;&atilde;o</label>
                <input type="text" id="txtValeName" name="txtValeName" class="form-control form-control-sm text-uppercase" autocomplete="off" required/>
            </div>
            <div class="form-group col-sm">
                <label class="control-label">Tipo de Cupom</label>
                <select class="form-control form-control-sm" id="cbValeTipo" name="cbValeTipo" required>
                    <option value="">&laquo; Selecione &raquo;</option>
                    <option value="D">Desconto</option>
                    <option value="A">Pedido de Compra</option>
                    <option value="P">Vale Presente</option>
                </select>
            </div>
        </div>
        <div class="row">
              <div class="form-group col-sm">
                  <label class="control-label">Valor</label>
                  <input type="text" id="txtValeValor" name="txtValeValor" class="form-control-sm form-control" required/>
              </div>
              <div class="form-group col-sm">
                  <label class="control-label">Tipo de Valor</label>
                  <select class="form-control form-control-sm" id="cbValeTipoValor" name="cbValeTipoValor" required>
                      <option value="">&laquo; Selecione &raquo;</option>
                      <option value="$">$ - Monet&aacute;rio</option>
                      <option value="%">% - Percentual</option>
                  </select>
              </div>
        </div>
        <div class="row">
            <div class="form-group col-sm">
                <label class="control-label">Quantidade</label>
                <input type="number" id="txtValeQtde" name="txtValeQtde" class="form-control form-control-sm" min="1" value="1" required/>
            </div>
            <div class="form-group col-sm">
                <label class="control-label">Data de validade (Opcional)</label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control date " id="txtValeValidade" name="txtValeValidade" data-provide='datepicker' data-date-format='dd/mm/yyyy' data-date-language='pt-BR' data-date-autoclose='true' data-date-today-highlight='true'/>
                    <div class="input-group-append">
                    	<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="custom-control custom-switch">
		  <input type="checkbox" class="custom-control-input" id="chkNuncaExpira">
		  <label class="custom-control-label" for="chkNuncaExpira">Cupom n&atilde;o Expir&aacute;vel</label>
		</div>
        <div class="form-group">
            <label class="control-label">Observa&ccedil;&atilde;o</label>
            <textarea class="form-control text-uppercase" rows="3" id="txtValeObservacao" name="txtValeObservacao"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnClose" name="btnClose" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> Fechar</button>
        <button type="submit" id="btnGenModal" name="btnGenModal" class="btn btn-primary btn-sm"><i class="fas fa-cog"></i> Gerar</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!--INICIO DO MODAL DE EXIBICAO DE CUPOM-->
<div class="modal fade" id="modalShow" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form name="frmShowCupom" id="frmShowCupom">
      <div class="modal-header">
      	<h5 class="modal-title" id="modalLabel">Gera&ccedil;&atilde;o de Cupom</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">      	
        <input type="hidden" name="txtIdToOpen" id="txtIdToOpen"/>
        <iframe id="frmCupom" name="frmCupom" frameborder="0" style="min-height:300px; max-height:550px; overflow-y:scroll;width:100%"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> Fechar</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
    url_data = '<?=$this->Url->build($url_data); ?>';
    getData();
   	url_filter = '<?=$this->Url->build($url_filter); ?>';
});

$(document).on("submit","#frmGenerator",function(event){
	event.preventDefault();

    var dataForm = {
        DESCRICAO     : $("#txtValeName").val(),
        TIPO_CUPOM    : $("#cbValeTipo").val(),
        VALOR         : $("#txtValeValor").val(),
        TIPO_VALOR    : $("#cbValeTipoValor").val(),
        DATA_VALIDADE : $("#txtValeValidade").val(),
        OBSERVACAO    : $("#txtValeObservacao").val(),
        QUANTIDADE    : $("#txtValeQtde").val(),
        UTILIZADO     : ($("#chkNuncaExpira")[0].checked)?"I":"N"
    };

    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method: 'POST',
        url:'<?=$this->Url->build("/retail/cupon_gen")?>',
        data: dataForm,
        success: function(data){
            if(data==true){
            	bootbox.alert("Cupons gerados com sucesso!",function(){ getData(); });
            }
            if(data==false){
            	bootbox.alert("Ocorreu um erro ao tentar gerar os cupons!");
            }
        }
    });

    $("#modalGenerator").modal('hide');
});

$(document).on('show.bs.modal',"#modalShow",function(e){
    var url = '<?=$this->Url->build("/retail/cupon_show/")?>'+$("#txtIdToOpen").val();
    $("#frmCupom").attr("src",url);
});

$(document).on("shown.bs.modal","#modalGenerator",function(){
    $("#txtValeValor").mask("#,##0.00", {reverse: true});
    $("#txtValeValidade").mask('00/00/0000');
});

$(document).on("hidden.bs.modal","#modalGenerator",function(){
	//remove a informacao que o form jah foi validado,
	//assim forca uma nova validacao
	$("#frmAdjustEstoque").removeClass("was-validated");	
	
    $("#txtValeName").val("");
    $("#cbValeTipo").val("");
    $("#txtValeValor").val("");
    $("#cbValeTipoValor").val("");
    $("#txtValeQtde").val("1");
    $("#txtValeValidade").val("");
    $("#txtValeObservacao").val("");
});

function gen_tags(){
    var totalChecked = 0;
    $("input[name='check_list[]']").each(function(){
        if( $(this)[0].checked ==true ){
            totalChecked = totalChecked + 1;
        }
    });
    
    if(totalChecked>0){
        bootbox.dialog({message:"Deseja realmente gerar a etiqueta do(s) cupom(ns) selecionado(s)?", 
            buttons:{
                yes:{
                    label:"Sim",
                    callback:function(){
                        $("#frmRegs").prop("action",'<?=$this->Url->build("/retail/cupon_tag_export/")?>');
                        $("#frmRegs").prop("method","post");
                        $("#frmRegs").submit();
                    }
                },
                no:{
                    label:"N\u00e3o"			
                }
            }
        });
    }
    else{
        bootbox.alert("Selecione ao menos um cupom para exportar etiqueta.");
    }
}

$(document).on('show.bs.modal',"#modalShow",function(e){
    var url = '<?=$this->Url->build("/retail/cupon_show/")?>'+$("#txtIdToOpen").val();
    $("#frmCupom").attr("src",url);
});

function showCupom(idCupom){
    $("#txtIdToOpen").val(idCupom);

    $("#modalShow").modal({
        backdrop: 'static'
    });
}
</script>