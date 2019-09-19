<br/>
<form name="frmRegs" id="frmRegs">
	<input type="hidden" name="txtIdCidade" id="txtIdCidade" value="<?php if(isset($cidade)){ echo $cidade->IDCIDADE; }?>"/>
<div class="card">
	<div class="card-header">
		<div class="row">
			<div class="col-sm">
				<i class="fas fa-angle-right"></i> Edi&ccedil;&atilde;o de Cidade
			</div>
			<div class="col-sm text-right">
				<a href="<?php echo $this->Url->build('/system/city');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="">Nome da Cidade</label>
			<input type="text" id="txtCidadeNome" name="txtCidadeNome" class="form-control form-control-sm" value="<?php if(isset($cidade)){ echo $cidade->NOME; }?>" required>
		</div>
		<div class="form-group">
			<label for="">UF</label>
			<select class="form-control text-uppercase form-control-sm" name="cbEstado" id="cbEstado" required>
				<option value="">&laquo; Selecione &raquo;</option>
				<option value="AC"<?php if(isset($cidade)){ if($cidade->UF=="AC"){ echo " selected"; } }?>>AC</option>
				<option value="AL"<?php if(isset($cidade)){ if($cidade->UF=="AL"){ echo " selected"; } }?>>AL</option>
				<option value="AM"<?php if(isset($cidade)){ if($cidade->UF=="AM"){ echo " selected"; } }?>>AM</option>
				<option value="AP"<?php if(isset($cidade)){ if($cidade->UF=="AP"){ echo " selected"; } }?>>AP</option>
				<option value="BA"<?php if(isset($cidade)){ if($cidade->UF=="BA"){ echo " selected"; } }?>>BA</option>
				<option value="CE"<?php if(isset($cidade)){ if($cidade->UF=="CE"){ echo " selected"; } }?>>CE</option>
				<option value="DF"<?php if(isset($cidade)){ if($cidade->UF=="DF"){ echo " selected"; } }?>>DF</option>
				<option value="ES"<?php if(isset($cidade)){ if($cidade->UF=="ES"){ echo " selected"; } }?>>ES</option>
				<option value="GO"<?php if(isset($cidade)){ if($cidade->UF=="GO"){ echo " selected"; } }?>>GO</option>
				<option value="MA"<?php if(isset($cidade)){ if($cidade->UF=="MA"){ echo " selected"; } }?>>MA</option>
				<option value="MG"<?php if(isset($cidade)){ if($cidade->UF=="MG"){ echo " selected"; } }?>>MG</option>
				<option value="MS"<?php if(isset($cidade)){ if($cidade->UF=="MS"){ echo " selected"; } }?>>MS</option>
				<option value="MT"<?php if(isset($cidade)){ if($cidade->UF=="MT"){ echo " selected"; } }?>>MT</option>
				<option value="PA"<?php if(isset($cidade)){ if($cidade->UF=="PA"){ echo " selected"; } }?>>PA</option>
				<option value="PB"<?php if(isset($cidade)){ if($cidade->UF=="PB"){ echo " selected"; } }?>>PB</option>
				<option value="PE"<?php if(isset($cidade)){ if($cidade->UF=="PE"){ echo " selected"; } }?>>PE</option>
				<option value="PI"<?php if(isset($cidade)){ if($cidade->UF=="PI"){ echo " selected"; } }?>>PI</option>
				<option value="PR"<?php if(isset($cidade)){ if($cidade->UF=="PR"){ echo " selected"; } }?>>PR</option>
				<option value="RJ"<?php if(isset($cidade)){ if($cidade->UF=="RJ"){ echo " selected"; } }?>>RJ</option>
				<option value="RN"<?php if(isset($cidade)){ if($cidade->UF=="RN"){ echo " selected"; } }?>>RN</option>
				<option value="RO"<?php if(isset($cidade)){ if($cidade->UF=="RO"){ echo " selected"; } }?>>RO</option>
				<option value="RR"<?php if(isset($cidade)){ if($cidade->UF=="RR"){ echo " selected"; } }?>>RR</option>
				<option value="RS"<?php if(isset($cidade)){ if($cidade->UF=="RS"){ echo " selected"; } }?>>RS</option>
				<option value="SC"<?php if(isset($cidade)){ if($cidade->UF=="SC"){ echo " selected"; } }?>>SC</option>
				<option value="SE"<?php if(isset($cidade)){ if($cidade->UF=="SE"){ echo " selected"; } }?>>SE</option>
				<option value="SP"<?php if(isset($cidade)){ if($cidade->UF=="SP"){ echo " selected"; } }?>>SP</option>
				<option value="TO"<?php if(isset($cidade)){ if($cidade->UF=="TO"){ echo " selected"; } }?>>TO</option>
			</select>
		</div>
		<div class="form-group">
			<label for="">C&oacute;digo do IBGE</label>
			<input type="text" id="txtCidadeCodIBGE" name="txtCidadeCodIBGE" class="form-control form-control-sm" value="<?php if(isset($cidade)){ echo $cidade->COD_IBGE; }?>">
		</div>
		<div class="form-group text-right">
			<button type="submit" id="btnSave" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
		</div>
	</div>
</div>
</form>
<script>
$(document).on("submit","#frmRegs",function(evt){
	evt.preventDefault();
	
	var dataForm = {
		IDCIDADE : $("#txtIdCidade").val(),
		NOME     : $("#txtCidadeNome").val(),
		UF       : $("#cbEstado").val(),
		COD_IBGE : $("#txtCidadeCodIBGE").val()
	};
	
	$.ajax({
		headers: {
			'X-CSRF-Token': csrf
		},
		method: 'post',
		url: '<?=$this->Url->build("/system/city_save/")?>',
		data: dataForm,
		dataType: 'json',
		success: function(data){
		   if(data){
				bootbox.alert("Cidade salva com sucesso!",function(){ document.location.href='<?=$this->Url->build("/system/city/")?>'; });
			}else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es da cidade!");
			}
		}
	});
	
});

</script>