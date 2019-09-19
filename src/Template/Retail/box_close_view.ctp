<br/><div class="card">
    <div class="card-header">
    	<div class="row">
    		<div class="col">
    			<i class="fas fa-angle-right"></i> Fechamento de Caixa
    		</div>
    		<div class="col">
    			<form class="form-inline justify-content-end" id="frmRegs">
    			<select id="cbStore" name="cbStore" class="form-control form-control-sm mb-2 mr-sm-2">
                    <option value="">&laquo; Selecione &raquo;</option>
                    <?php foreach($storelist as $store):?>
                    <option value="<?php echo $store->IDLOJA?>"><?php echo $store->NOME; ?></option>
                    <?php endforeach;?>
                </select>
                <div class="input-group input-group-sm  mb-2 mr-sm-2">
                    <input type="text" id="txtDateClose" name="txtDateClose" class="form-control date" data-provide='datepicker' data-date-format='dd/mm/yyyy' data-date-language='pt-BR' data-date-autoclose='true' data-date-today-highlight='true' placeholder="Data de Abertura">
                    <div class="input-group-append">
                    	<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                </div>
            	<button type="submit" class="btn btn-dark btn-sm mb-2" id="btnFilterBox"><i class="fas fa-filter"></i> Filtrar</button>
            	</form>
    		</div>
    	</div>
    </div>
    <div class="card-body" id="tblResult">
        
    </div>
</div>

<script>
$(document).ready(function(){
    getData();
    
    $("#txtDateClose").mask("00/00/0000");
    
    $('.input-group.date').datepicker({
        format: "dd/mm/yyyy",
        todayBtn: true,
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        forceParse: false
    });
});

$(document).on("submit","#frmRegs",function(evt){
    evt.preventDefault();
    getData();
});
    
function getData(){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method: 'post',
        data: { DATA: $("#txtDateClose").val(),LOJA: $("#cbStore").val() },
        url: '<?=$this->Url->build("/retail/box_close_view_data/")?>',
        success: function(data){
            $("#tblResult").html(data);
        }
    });
}
</script>