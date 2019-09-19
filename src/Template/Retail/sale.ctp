<br/>
<form id="frmRegs" name="frmRegs">
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> <?=$title?>
				</div>
				<div lang="col-sm text-right">
					<button type="button" class="btn btn-dark btn-sm" data-toggle="modal" data-target="#modalFilter"><i class="fas fa-filter"></i> Filtrar</button>
				</div>
			</div>
		</div>
		<div class="card-body" id="divResult"></div>
	</div>
</form>
<br/>

<!--MODAL DE EXIBICAO DA VENDA-->
<div class="modal fade" id="modalVenda" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modalLabel">Exibi&ccedil;&atilde;o de Venda</h4>
      </div>
      <div class="modal-body">
      	<input type="hidden" name="txtIdToOpen" id="txtIdToOpen"/>
            <iframe id="frmVenda" name="frmVenda" frameborder="0" style="min-height:400px; max-height:400px; overflow-y:scroll;width:100%"></iframe>
      </div>
      <div class="modal-footer">
      	<button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal" id="btnCloseModal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){

	url_data = '<?=$this->Url->build($url_data); ?>';
    getData();
    <?php if($url_filter!=""):?>
   	url_filter = '<?=$this->Url->build($url_filter); ?>';
	<?php endif; ?>
});

$(document).on('show.bs.modal',function(e){
    var url = '<?=$this->Url->build("/retail/sale_show/")?>'+$("#txtIdToOpen").val();
    $("#frmVenda").attr("src",url);
});

function openSale(idVenda){
    $("#txtIdToOpen").val(idVenda);

    $("#modalVenda").modal({
        backdrop: 'static'
    });
}
</script>
