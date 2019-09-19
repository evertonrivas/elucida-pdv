<br/>
<form id="frmRegs" name="frmRegs">
<input type="hidden" id="IDLOJA" name="IDLOJA" value="<?=$IDLOJA?>"/>
<input type="hidden" id="IDFORNECEDOR" name="IDFORNECEDOR" value="<?=$IDFORNECEDOR;?>"/>
<input type="hidden" id="IDPRODUTOTIPO" name="IDPRODUTOTIPO" value="<?=$IDPRODUTOTIPO?>"/>
<div class="card">
	<div class="card-header">
		<i class="fas fa-angle-right"></i> <?=$title?>
	</div>
	<div class="card-body">
		<table class="table table-striped">
		    <thead>
		        <tr>
		        	<th>SKU</th>
		            <th>Produto</th>
		            <th>Qtde. Existente</th>
		            <th>Qtde. Digitada</th>
		            <th>A&ccedil;&atilde;o</th>
		        </tr>
		    </thead>
		    <tbody>
		    <?php if(isset($data_list)): ?>
		    <?php foreach($data_list as $inventario): ?>
		    	<input type="hidden" id="txtIdProduto[]" name="txtIdProduto[]" value="<?=$inventario->IDPRODUTO?>">
		        <tr>
		        	<td><?=$inventario->SKU?></td>
		            <td><?=$inventario->NOME?></td>
		            <td><?=$inventario->EXISTENTE?></td>
		            <td><?=$inventario->DIGITADO?></td>
		            <td><?php if($inventario->EXISTENTE!=$inventario->DIGITADO):?><a href="javascript:makeAdjust(<?=$IDLOJA;?>,<?=$inventario->IDPRODUTO;?>,<?=$inventario->DIGITADO?>)" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Ajustar Estoque"><i class="fas fa-adjust"></i></a><?php endif; ?></td>
		        </tr>
		        <?php 
		        endforeach; ?>
		        <?php else:?>
		        <tr>
		        	<td colspan="5" class="text-center">Nenhum registro encontrado!</td>
		        </tr>
		        <?php endif;?>
		    </tbody>
		</table>
	</div>
</div>
</form>

<!-- INICIO DO MODAL DE IMPRESSAO -->
<div class="modal" tabindex="-1" role="dialog" id="modalAdjust">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajuste de Estoque</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="min-height: 400px!important;">
        <iframe id="frmAjuste" name="frmAjuste" style="height:400;width:100%;border:0;overflow-y:scroll" width="100%" height="400"></iframe>
      </div>
    </div>
  </div>
</div>
<!-- FIM DO MODAL DE IMPRESSAO -->

<script>
window.closeModalAdjust = function(){
    $('#modalAdjust').modal('hide');
    document.location.reload();
};

/*
$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();
});*/

function makeAdjust(idLoja,idProduto,digitado){
	$("#frmAjuste").attr("src",'<?=$this->Url->build("/stock/adjustment/")?>'+idLoja+"/"+idProduto+"/"+digitado);
	$("#modalAdjust").modal({
		backdrop: 'static'
	});
}
</script>