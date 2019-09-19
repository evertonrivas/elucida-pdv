<br/>    
<form id="frmRegs" name="frmRegs">
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> <?=$title?>
				</div>
				<div lang="col-sm text-right">
					<a href='javascript:trash("<?php echo $this->Url->build('/financial/outflow_delete');?>",<?= json_encode($this->request->getParam("_csrfToken")) ?>)' class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Excluir</a>
					<a href="<?php echo $this->Url->build('/financial/outflow_create');?>" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Nova</a>
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


$(document).on("show.bs.collapse",function(evt){
	//console.log(evt);
	evt.currentTarget.activeElement.firstChild.classList.remove("fa-plus-circle");
	evt.currentTarget.activeElement.firstChild.classList.remove("text-success");
	evt.currentTarget.activeElement.firstChild.classList.add("fa-minus-circle");
	evt.currentTarget.activeElement.firstChild.classList.add("text-danger");
});

$(document).on("hidden.bs.collapse",function(evt){
	evt.currentTarget.activeElement.firstChild.classList.remove("fa-minus-circle");
	evt.currentTarget.activeElement.firstChild.classList.remove("text-danger");
	evt.currentTarget.activeElement.firstChild.classList.add("fa-plus-circle");
	evt.currentTarget.activeElement.firstChild.classList.add("text-success");
});
</script>