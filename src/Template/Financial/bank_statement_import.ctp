<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<br/><form name="frmRegs" id="frmRegs" enctype="multipart/form-data" method="post" action="<?php echo $this->Url->build('financial/bank_statement_consume');?>">
	<input type="hidden" id="_csrfToken" name="_csrfToken" value=<?= json_encode($this->request->getParam('_csrfToken')) ?>>
    <div class="card">
        <div class="card-header">
        	<i class="fas fa-angle-right"></i> <?=$title?>
        </div>
        <div class="card-body">
        	<div class="form-group">
	        	<div class="custom-file">
				  <input type="file" class="custom-file-input" id="TXT_FILE_UPLOAD" name="TXT_FILE_UPLOAD">
				  <label class="custom-file-label" for="TXT_FILE_UPLOAD" data-browse="Procurar">Selecionar arquivo</label>
				</div>
			</div>
            <div class="form-group">
                <input type="hidden" id="txtFileContent" name="txtFileContent"/>
                <div class="form-group text-right">
                    <button type="submit" id="btnSend" name="btnSend" class="btn btn-primary btn-sm" disabled=""><i class="fas fa-file-upload"></i> Importar</button>
                </div>
            </div>
            
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	bsCustomFileInput.init();	
});

$("#TXT_FILE_UPLOAD").on("change",function(){
	$("#btnSend").removeAttr("disabled");
});
</script>