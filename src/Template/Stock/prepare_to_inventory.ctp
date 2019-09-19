<br/>
    <div class="card">
        <div class="card-header">
        	<form id="frmRegs" name="frmRegs" class="needs-validation" novalidate>
            <div class="row">
            	<div class="col-3"><i class="fas fa-angle-right"></i> <?=$title?></div>
            	<div class="col-9">
            		<div class="row justify-content-end">
		                    <select id="cbStore" name="cbStore" class="form-control form-control-sm" style="max-width: 150px;" required>
		                        <option value="">Loja(s)</option>
		                        <?php foreach($store_list as $store):?>
		                        <option value="<?php echo $store->IDLOJA?>"><?php echo $store->NOME; ?></option>
		                        <?php endforeach;?>
		                    </select>&nbsp;
		                    <select id="cbProductType" name="cbProductType" class="form-control form-control-sm" style="max-width: 180px;" required>
		                    	<option value="">Tipo(s) de Produto</option>
		                    	<?php foreach($product_type_list as $product_type):?>
		                    	<option value="<?php echo $product_type->IDPRODUTOTIPO?>"><?php echo $product_type->DESCRICAO; ?></option>
		                    	<?php endforeach;?>
		                    </select>&nbsp;
		                    <select id="cbProvider" name="cbProvider" class="form-control form-control-sm" style="max-width: 180px;">
		                    	<option value="">Fornecedor(es)</option>
		                    </select>&nbsp;
		                    <button type="submit" class="btn btn-dark btn-sm" id="btnFilter"><i class="fas fa-filter"></i> Filtrar</button>&nbsp;
		                    <button type="button" class="btn btn-secondary btn-sm" id="btnPrint" disabled=""><i class="fas fa-print"></i> Imprimir</button>
            		</div>
            	</div>
            </div>
            </form>
        </div>
        <div class="card-body" id="tblResult">
            
        </div>
    </div>

<!-- INICIO DO MODAL DE IMPRESSAO -->
<div class="modal" tabindex="-1" role="dialog" id="modalPrint">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Impress&atilde;o</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="min-height: 400px!important;">
        <iframe id="frmPrint" name="frmPrint" style="height:400;width:100%;border:0;overflow-y:scroll" width="100%" height="400"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" id="btnPrintModal" name="btnPrintModal"><i class="fas fa-print"></i> Imprimir</button>
      </div>
    </div>
  </div>
</div>
<!-- FIM DO MODAL DE IMPRESSAO -->

<script>
$(document).on("change","#cbProductType",function(){
	if($(this).val()!=""){
		$.ajax({
			headers:{
				'X-CSRF-Token':csrf
			},
			method:'post',
			url:'<?=$this->Url->build("/system/provider_json/")?>'+$(this).val(),
			success:function(data){
				var options = [];
				options.push('<option value="">Fornecedor(es)</option>');
				$.each(JSON.parse(data), function(i, ele) {
				    options.push('<option value="'+ele.IDFORNECEDOR+'">'+ele.FANTASIA+'</option>');
				});
				$("#cbProvider").html(options.join(''));
			}
		});
	}
});
$(document).on('submit',"#frmRegs",function(evt){
    evt.preventDefault();
    
    $.ajax({
    	headers:{
			'X-CSRF-Token': csrf
		},
        method: 'post',
        data: { IDLOJA: $("#cbStore").val(), IDPRODUTOTIPO: $("#cbProductType").val(),IDFORNECEDOR:$("#cbProvider").find(":selected").val() },
        url: '<?=$this->Url->build("/stock/prepare_to_inventory_data/")?>',
        success: function(data){
            $("#tblResult").html(data);
            $("#btnPrint").removeAttr("disabled");
        }
    });
});

$(document).on('click',"#btnPrint",function(){
	$("#frmPrint").attr("src",'<?=$this->Url->build("/stock/prepare_to_inventory_print/")?>'+$("#cbStore").val()+"/"+$("#cbProductType").val()+"/"+$("#cbProvider").val());
	
	$("#modalPrint").modal({
		backdrop: 'static'
	});
});

$(document).on('click',"#btnPrintModal",function(){
	window.frames["frmPrint"].focus();
	window.frames["frmPrint"].print();
});
</script>