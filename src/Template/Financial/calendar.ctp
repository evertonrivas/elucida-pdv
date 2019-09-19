<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.1.0/main.min.css" crossorigin="anonymous"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.1.0/main.min.css" crossorigin="anonymous"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@4.1.0/main.min.css" crossorigin="anonymous"/>

<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.1.0/locales/pt-br.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.1.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.1.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@4.1.0/main.min.js"></script>
<br/>
<div class="card">
    <div class="card-header">
    	<div class="row">
			<div class="col-sm">
				<i class="fas fa-angle-right"></i> <?=$title?>
			</div>
			<div lang="col-sm text-right">
				<button type="button" class="btn btn-primary btn-sm" data-backdrop="static" data-toggle="modal" data-target="#modalNewEvent"><i class="fas fa-calendar-day"></i> Novo Evento</button>
			</div>
		</div>
    </div>
    <div class="card-body">
        <div id="calendar"></div>
    </div>
    <div class="card-footer">
        <div class="row" id="totais" style="height: 22px!important;"></div>
    </div>
</div><br/>


<!--INICIO DO MODAL DE NOVO EVENTO-->
<div class="modal fade" id="modalNewEvent" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	<h4 class="modal-title" id="modalLabel">Novo Evento</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
            <div class="form-group">
                <label class="control-label">Linha Digit&aacute;vel/C&oacute;digo de Barras</label>
                <input type="text" class="form-control form-control-sm" id="txtLinhaDigitavel" name="txtLinhaDigitavel" autocomplete="off"/>
            </div>
            <div class="row">
	            <div class="form-group col-sm">
	                <label class="control-label">Tipo de Despesa</label>
	                <select class="form-control form-control-sm" id="cbTipoDespesa" name="cbTipoDespesa" required>
	                    <option value="">&laquo; Selecione &raquo;</option>
	                    <?php foreach($despesalist as $despesa):?>
	                    <option value="<?php echo $despesa->IDTIPODESPESA; ?>"><?php echo $despesa->NOME; ?></option>
	                    <?php endforeach; ?>
	                </select>
	            </div>
	            <div class="form-group col-sm">
	                <label class="control-label">N&uacute;mero do Documento</label>
	                <input type="text" class="form-control form-control-sm" id="txtNumDocumento" name="txtNumDocumento" autocomplete="off"/>
	            </div>
            </div>
            <div class="row">
	            <div class="form-group col-sm">
	                <label class="control-label">Data de Vencimento</label>
	                <div class="input-group">
	                    <input type="text" class="form-control date form-control-sm" data-provide='datepicker' data-date-format='dd/mm/yyyy' data-date-language='pt-BR' data-date-autoclose='true' data-date-today-highlight='true' id="txtDataVencimento" name="txtDataVencimento" autocomplete="off" value="<?php echo date("d/m/Y"); ?>" required>
	                    <div class="input-group-append">
	                    	<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
	                    </div>
	                </div>
	            </div>
	            <div class="form-group col-sm"> 
	                <label class="control-label">Valor &agrave; pagar</label>
	                <input type="text" class="form-control form-control-sm" id="txtValOriginal" name="txtValOriginal" autocomplete="off" required/>
	            </div>
            </div>
            <div class="row">
            	<div class="form-group col-sm">
		            <label class="control-label">Nome do Evento</label>
                	<input type="text" class="form-control text-uppercase form-control-sm" id="txtObservaEvento" name="txtObservaEvento" autocomplete="off" required maxlength="20"/>
				</div>
	            <div class="form-group col-sm">
	                <label class="control-label">Agendamento</label>
	                <select id="cbRepeat" name="cbRepeat" class="form-control form-control-sm">
	                    <option value="">&laquo; Selecione &raquo;</option>
	                    <option value="5DU">Todo 5&deg; dia &uacute;til</option>
	                    <option value="D01">Todo dia 1</option>
	                    <option value="D05">Todo dia 5</option>
	                    <option value="D10">Todo dia 10</option>
	                    <option value="D15">Todo dia 15</option>
	                    <option value="D20">Todo dia 20</option>
	                    <option value="D25">Todo dia 25</option>
	                    <option value="UDU">&Uacute;ltimo dia &uacute;til de cada m&ecirc;s</option>
	                </select>
	            </div>
            </div>
            <div class="custom-control custom-switch">
			  <input type="checkbox" class="custom-control-input" id="chkBaixado">
			  <label class="custom-control-label" for="chkBaixado"> Evento j&aacute; finalizado</label>
			</div>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-success btn-sm" id="btnSend" name="btnSend"><i class="fas fa-hdd"></i> Salvar</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!--INICIO DO MODAL DE EXIBICAO DE EVENTO-->
<div class="modal fade" id="modalEditEvent" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	<h4 class="modal-title" id="modalLabel">Edi&ccedil;&atilde;o/Exibi&ccedil;&atilde;o de Evento</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="txtIdToOpen" id="txtIdToOpen"/>
        <iframe id="frmConta" name="frmConta" frameborder="0" style="min-height:500px; max-height:550px; overflow-y:scroll;width:100%"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
var calendar = null;
$(document).ready(function(){
    $("#txtDataVencimento").mask("00/00/0000");
    
    $("#txtValOriginal").mask("##,##0.00", {reverse: true});
    
    getCalendar();
});

$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();

    var dataForm = {
        DATA_VENCIMENTO : $("#txtDataVencimento").val(),
        IDTIPODESPESA   : $("#cbTipoDespesa").val(),
        NUM_DOCUMENTO   : $("#txtNumDocumento").val(),
        VALOR_ORIGINAL  : $("#txtValOriginal").val(),
        OBSERVACAO      : $("#txtObservaEvento").val(),
        JA_BAIXADO      : (($("#chkBaixado")[0].checked) ? 1 : 0),            
        REPETIR         : $("#cbRepeat").val()
    };

    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method: 'POST',
        url: '<?=$this->Url->build("/financial/calendar_event_new")?>',
        data: dataForm,
        dataType: 'json',
        success: function(data){
            if(data==true){
                $("#modalNewEvent").modal('hide');
                reloadCalendar();
            }
        }
    });
});

$('#modalNewEvent').on('hidden.bs.modal', function() {
	//remove a informacao que o form jah foi validado,
	//assim forca uma nova validacao
	$("#frmAdjustEstoque").removeClass("was-validated");
	
    $("#txtLinhaDigitavel").val("");
    $("#txtNumDocumento").val('');
    $("#cbTipoDespesa").val('');
    $("#txtDataVencimento").val("");
    $("#txtValOriginal").val("");
    $("#txtObservaEvento").val("");
    $("#cbRepeat").val("");
    $("#chkBaixado")[0].checked = false;
});

$("#modalEditEvent").on('show.bs.modal',function(){
    var url = '<?=$this->Url->build("/financial/calendar_event_show/")?>'+$("#txtIdToOpen").val();
    $("#frmConta").attr("src",url);
});

$("#modalEditEvent").on('hidden.bs.modal',function(){
    reloadCalendar();
});

$("#txtLinhaDigitavel").keydown(function(event){		
	if(event.keyCode == 13){
            event.preventDefault();
            $.ajax({
            	headers:{
					'X-CRSF-Token':csrf
				},
                url: '<?=$this->Url->build("/financial/get_boleto_info/")?>'+$(this).val(),
                dataType: 'json',
                success: function(data){
                    $("#txtDataVencimento").val(data.VENCIMENTO);
                    $("#txtValOriginal").val(data.VALOR);
                    $("#cbTipoDespesa").focus();
                }
            });
	}
});

function getCalendar(){
	
	var calendarEl = document.getElementById('calendar');

	calendar = new FullCalendar.Calendar(calendarEl, {
		plugins: [ 'dayGrid','timeGrid' ],
		header: {
	        left: 'prev,next today',
	        center: 'title',
	        right: 'dayGridMonth,timeGridWeek,timeGridDay'
	    },
		eventLimit: true,
		locale:'pt-br',
		eventSources: [{
			events: function(info, successCallback, failureCallback){
				$.ajax({
			    	headers:{
						'X-CSRF-Token':csrf
					},
			        method:'post',
			        url: '<?=$this->Url->build("/financial/calendar_get_events/")?>?start='+info.startStr+"&end="+info.endStr,
			        dataType:'json',
			        success: function(data){
			        	if(data){
							successCallback(data);
						}
			        }
			    });
			    
			    getTotals(info.start.getFullYear(),(info.end.getMonth()));
			}
		}],
		eventClick: function(info){
			$("#txtIdToOpen").val(info.event.id);
            $("#modalEditEvent").modal({
                backdrop: 'static'
            });
		}
	});

	calendar.render();
}

function getTotals(ano,mes){
    
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method:'post',
        data:{
            mes : mes,
            ano : ano
        },
        url: '<?=$this->Url->build("/financial/calendar_get_totals")?>',
        success: function(data){
            $("#totais").html(data);
        }
    });
}

function reloadCalendar(){
    calendar.refetchEvents();
}
</script>