<?=$this->Html->css("../js/codemirror-5.46.0/lib/codemirror.css");?>
<?=$this->Html->css("../js/codemirror-5.46.0/addon/hint/show-hint.css");?>
<?=$this->Html->script("codemirror-5.46.0/lib/codemirror.js");?>
<?=$this->Html->script("codemirror-5.46.0/mode/sql/sql.js");?>
<?=$this->Html->script("codemirror-5.46.0/addon/hint/show-hint.js");?>
<?=$this->Html->script("codemirror-5.46.0/addon/hint/sql-hint.js");?>
<br/>
    <form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
        <input type="hidden" id="txtIdRule" name="txtIdRule" value="<?php if(isset($regra_sku)){ echo $regra_sku->IDREGRASKU; }?>"/>
        <div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-sm">
						<i class="fas fa-angle-right"></i> Cadastro/Edi&ccedil;&atilde;o de Regra de SKU
					</div>
					<div class="col-sm text-right">
						<a href="<?php echo $this->Url->build('/stock/sku_rule');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="form-group">
					<label for="cbRuleProductType">Tipo de Produto</label>
					<select class="form-control text-uppercase form-control-sm" id="cbRuleProductType" name="cbRuleProductType" required>
						<option value="">&laquo; Selecione &raquo;</option>
						<?php foreach($produto_tipo as $prodt){?>
							<option value="<?php echo $prodt->IDPRODUTOTIPO?>"<?php if(isset($regra_sku)){ if($regra_sku->IDPRODUTOTIPO==$prodt->IDPRODUTOTIPO){ echo " selected"; } } ?>><?php echo $prodt->DESCRICAO; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label for="txtRule">Regra <a href="javascript:help('SKU_RULE');"><strong>?</strong></a></label>
					<textarea id="txtRule" name="txtRule" class="form-control" rows="10"><?php if(isset($regra_sku)){ echo $regra_sku->REGRA; }?></textarea>
				</div>
				<div class="form-group">
					<label for="txtRuleFormat">Formato da Regra</label>
					<div class="input-group mb-3 input-group-sm">
						<input type="text" class="form-control text-uppercase" id="txtRuleFormat" name="txtRuleFormat" value="<?php if(isset($regra_sku)){ echo $regra_sku->FORMATO_REGRA; }?>" autocomplete="off" required />
						<div class="input-group-append">
							<button class="btn btn-outline-secondary" type="button" id="button-addon2" onclick="javascript:help('SKU_RULE_FORMAT')">?</button>
						</div>
					</div>
				</div>
				<div class="form-group text-right">
					<button type="submit" class="btn btn-primary btn-sm" id="btnSave"><i class="fas fa-hdd"></i> Salvar</button>
				</div>
			</div>
		</div>
    </form>
<script>
$(document).ready(function(){
    
    editor = CodeMirror.fromTextArea(document.getElementById("txtRule"),{
        mode:'text/x-mysql',
        indentWithTabs: true,
        smartIndent: true,
        lineNumbers: true,
        matchBrackets : true,
        autofocus: true,
        extraKeys: {"Ctrl-Space": "autocomplete"},
        hintOptions: {tables: {
          users: {name: null, score: null, birthDate: null},
          countries: {name: null, population: null, size: null}
        }}
    });
    
    editor.setSize('100%',200);
});

$(document).on("submit","#frmRegs",function(event){
	
	event.preventDefault();

	var frmData = {
		IDREGRASKU    : $("#txtIdRule").val(),
		IDPRODUTOTIPO : $("#cbRuleProductType").val(),
		REGRA         : $("#txtRule").val(),
		FORMATO_REGRA : $("#txtRuleFormat").val()
	};

	$.ajax({
		headers : {
			'X-CSRF-Token': csrf
		},
		type: 'POST',
		url: '<?=$this->Url->build("/stock/sku_rule_save")?>',
		data: frmData,
		dataType: 'json',
		success: function(data){
			if(data==true){
				bootbox.alert("Regra de SKU salva com sucesso!",function(){ document.location.href='<?=$this->Url->build("/stock/sku_rule")?>'; });
		   }else{
				bootbox.alert("Ocorreu um erro ao tentar salvar as informa&ccedil;&otilde;es da Regra de SKU!");
				clearFields();
		   }
		}
	});
});


function clearFields(){
	$("#frmRegs").removeClass("was-validated");
	
    $("#txtIdRule").val("");
    $("#cbRuleProductType").val("");
    $("#txtRule").val("");
    $("#txtRuleFormat").val("");
    $("#cbRuleProductType").focus();
}
</script>