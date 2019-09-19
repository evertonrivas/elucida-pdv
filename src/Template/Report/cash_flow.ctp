<br/>
<div class="card">
	<div class="card-header">
		<div class="row">
			<div class="col">
				<i class="fas fa-angle-right"></i> Fluxo de Caixa
			</div>
			<div class="col">
				<div class="row justify-content-end">
				Ano:&nbsp;<select id="filterYear" name="filterYear" class="form-control form-control-sm" style="max-width: 150px!important;">
					<?php foreach($years as $year): ?>
					<option value="<?=$year->ANO?>"><?=$year->ANO;?></option>
					<?php endforeach; ?>
				</select>&nbsp;
				<a href="<?php echo $this->Url->build('/system/');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
		</div>
	</div>
	<div class="card-body" id="divResult" style="overflow-x: scroll;">
		
	</div>
</div>
<script>
$(document).ready(function(){
	url_data = '<?=$this->Url->build($url_data); ?>';
    getData();
});

$(document).on("change","#filterYear",function(){
	url_data = '<?=$this->Url->build($url_data); ?>/'+$(this).val();
	getData();
});
</script>