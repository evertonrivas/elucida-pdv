<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
	<input type="hidden" id="txtIdCategory" name="txtIdCategory" value="<?php if(isset($categoria)){ echo $categoria->IDCATEGORIA; }?>"/>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Categoria
				</div>
				<div class="col-sm text-right">
					<a href="<?php echo $this->Url->build('/stock/category');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="txtCategoryNome">Nome da Categoria</label>
				<input type="text" class="form-control form-control-sm" id="txtCategoryNome" name="txtCategoryNome" value="<?php if(isset($categoria)){ echo $categoria->NOME; }?>" autocomplete="off"/>
			</div>
			<div class="form-group">
				<label for="cbCategoryParent">Categoria Pai</label>
				<select class="form-control text-uppercase form-control-sm" id="cbCategoryParent" name="cbCategoryParent">
					<option value="">&laquo; Selecione &raquo;</option>
					<?php foreach($categoria_pai as $cat):?>
						<option value="<?php echo $cat->IDCATEGORIA?>"<?php if(isset($categoria)){ if($categoria->CATEGORIA_PAI==$cat->IDCATEGORIA){ echo " selected"; } } ?>><?php echo $cat->NOME; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group text-right">
				<button type="submit" class="btn btn-primary btn-sm" id="btnSave"><i class="fas fa-hdd"></i> Salvar</button>
			</div>
		</div>
	</div>
</form>

<script>
$(document).on("submit","#frmRegs",function(event){
    
    event.preventDefault();

	var frmData = {
		IDCATEGORIA   : $("#txtIdCategory").val(),
		NOME          : $("#txtCategoryNome").val(),
		CATEGORIA_PAI : $("#cbCategoryParent").val()
	};

	$.ajax({
		headers : {
			'X-CSRF-Token': csrf
		},
		type: 'POST',
		url: '<?=$this->Url->build("/stock/category_save")?>',
		data: frmData,
		dataType: 'json',
		success: function(data){
			if(data==true){
				bootbox.alert("Categoria salva com sucesso!",function(){ document.location.href='<?=$this->Url->build("/stock/category")?>'; });
			}
			else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es da Categoria!");
				clearFields();
			}
		}
	});
});


function clearFields(){
	$("#frmRegs").removeClass("was-validated");
	
    $("#txtIdCategory").val("");
    $("#txtCategoryNome").val("");
    $("#cbCategoryParent").val("");
    $("#txtCategoryNome").focus();
};
</script>