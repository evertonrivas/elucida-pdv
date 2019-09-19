<br/>
<form id="frmRegs" name="frmRegs" class="need-validation" novalidate>
<div class="card">
	<div class="card-header">
		<div class="row">
			<div class="col">
				<i class="fas fa-angle-right"></i> <?=$title?>
			</div>
			<div class="col">
				<div class="row justify-content-end">
					<select class="form-control form-control-sm" style="max-width: 250px!important;" id="cbTipoProduto" name="cbTipoProduto" required>
						<option value="" selected="">Selecione um tipo de produto</option>
						<?php if(isset($types)):?>
						<?php foreach($types as $type):?>
						<option value="<?=$type->IDPRODUTOTIPO?>"><?=$type->DESCRICAO;?></option>
						<?php endforeach;?>
						<?php endif;?>
					</select>&nbsp;
					<div class="input-group input-group-sm" style="max-width: 170px!important;">
	                    <input type="text" class="form-control date" data-provide='datepicker' data-date-format='mm-yyyy' data-date-language='pt-BR' data-date-autoclose='true' data-date-today-highlight='true' id="txtDate" name="txtDate" placeholder="Per&iacute;odo (m&ecirc;s-ano)" required/>
	                    <div class="input-group-append">
	                    	<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
	                    </div>
	                </div>&nbsp;
	                <button type="submit" id="btnSend" name="btnSend" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Consultar</button>
                </div>
			</div>
		</div>
	</div>
	<div class="card-body" id="tblResult">
		
	</div>
</div>
</form>
<script>
$(document).ready(function(){
	getData();
});

$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();
	
	getData();
});

function getData(){
	$.ajax({
		headers:{
			'X-CSRF-Token':csrf
		},
        method: 'post',
        data: { PERIODO: $("#txtDate").val(),TIPO_PRODUTO: $("#cbTipoProduto").val() },
        url: '<?=$this->Url->build("/report/stock_by_type_data/")?>',
        success: function(data){
            $("#tblResult").html(data);
        }
    });
}
</script>