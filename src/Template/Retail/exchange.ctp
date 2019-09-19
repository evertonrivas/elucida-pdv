<br/>    
<form id="frmRegs" name="frmRegs">
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> <?=$title?>
				</div>
				<div lang="col-sm text-right">
					<a href="<?php echo $this->Url->build('/retail/exchange_make');?>" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Nova</a>
					<button type="button" class="btn btn-dark btn-sm" data-toggle="modal" data-target="#modalFilter"><i class="fas fa-filter"></i> Filtrar</button>
				</div>
			</div>
		</div>
		<div class="card-body" id="divResult"></div>
	</div>
</form>
<br/>

<script>
$(document).ready(function(){
    url_data = '<?=$this->Url->build($url_data); ?>';
    getData();
   	url_filter = '<?=$this->Url->build($url_filter); ?>';
});

$(document).on('show.bs.modal',function(e){
    var url = '<?=$this->Url->build("/retail/exchange_show/"?>'+$("#txtIdToOpen").val();
    $("#frmTroca").attr("src",url);
});

function openChange(idTroca){
    $("#txtIdToOpen").val(idTroca);

    $("#modalTroca").modal({
        backdrop: 'static'
    });
}
</script>