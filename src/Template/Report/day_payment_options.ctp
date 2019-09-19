<br>
<div class="card">
	<div class="card-header">
		<div class="row">
			<div class="col">
				<i class="fas fa-angle-right"></i> <?=$title?>
			</div>
			<div class="col">
				<div class="row justify-content-end">
					<div class="input-group input-group-sm" style="max-width: 150px!important;">
	                    <input type="text" class="form-control date" data-provide='datepicker' data-date-format='dd/mm/yyyy' data-date-language='pt-BR' data-date-autoclose='true' data-date-today-highlight='true' id="txtDate" name="txtDate"/>
	                    <div class="input-group-append">
	                    	<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
	                    </div>
	                </div>
                </div>
			</div>
		</div>
	</div>
	<div class="card-body" id="tblResult">
		
	</div>
</div>
<script>
$(document).ready(function(){
	getData();
});

$(document).on("change","#txtDate",function(){
    getData();
});

function getData(){
	$.ajax({
		headers:{
			'X-CSRF-Token':csrf
		},
        method: 'post',
        data: { DATA: $("#txtDate").val() },
        url: '<?=$this->Url->build("/report/day_payment_options_data/")?>',
        success: function(data){
            $("#tblResult").html(data);
        }
    });
}
</script>