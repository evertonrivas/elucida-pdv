<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<br/><form class="form" enctype="multipart/form-data" action="<?php echo $this->Url->build('/tributary/invoice_received_consume')?>" method="post">
<input type="hidden" id="_csrfToken" name="_csrfToken" value=<?= json_encode($this->request->getParam('_csrfToken')) ?>>
<div class="card">
    <div class="card-header">
    	<i class="fas fa-angle-right"></i> <?=$title?>
    </div>
    <div class="card-body">
        <div class="form-group">
        	<div class="custom-file form-control-sm">
			  <input type="file" class="custom-file-input" id="TXT_FILE_UPLOAD" name="TXT_FILE_UPLOAD[]" multiple="multiple" accept=".xml">
			  <label class="custom-file-label" for="TXT_FILE_UPLOAD" data-browse="Procurar">Selecionar arquivo</label>
			</div>
		</div>
		<div class="custom-control custom-switch">
		  <input type="checkbox" class="custom-control-input" id="chSaveCalendar" name="chSaveCalendar" value="1">
		  <label class="custom-control-label" for="chSaveCalendar">Salvar informa&ccedil;&otilde;es de duplicada no calend&aacute;rio</label>
		</div>
        <div class="form-group text-right">
            <button type="submit" id="btnSend" name="btnSend" class="btn btn-primary btn-sm" disabled=""><i class="fas fa-file-upload"></i> Importar</button>
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