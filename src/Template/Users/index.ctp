<br/>    
<form id="frmRegs" name="frmRegs">
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Usu&aacute;rios
				</div>
				<div lang="col-sm text-right">
					<a href='javascript:trash("<?php echo $this->Url->build('/users/trash');?>",<?= json_encode($this->request->getParam("_csrfToken")) ?>)' class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Excluir</a>
					<a href="<?php echo $this->Url->build('/users/create');?>" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Novo</a>
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
</script>