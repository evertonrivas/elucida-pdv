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

<!--INICIO DO MODAL DE EXIBIÇÃO DA DANFE-->
<div class="modal fade" id="modalDanfe" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	<h4 class="modal-title" id="modalLabel">Exibi&ccedil;&atilde;o de DANFE</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
      	<input type="hidden" name="txtIdToOpen" id="txtIdToOpen"/>
        <iframe id="frmDanfe" name="frmDanfe" frameborder="0" style="min-height:600px; max-height:600px; overflow-y:scroll;width:100%"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
    url_data = '<?=$this->Url->build($url_data); ?>';
    getData();
   	url_filter = '<?=$this->Url->build($url_filter); ?>';
});

function showDanfe(idNFE){
    $("#txtIdToOpen").val(idNFE);

    $("#modalDanfe").modal({
        backdrop: 'static'
    });
}

$(document).on('show.bs.modal',"#modalDanfe",function(e){
    var url = '<?=$this->Url->build("/tributary/invoice_return_show/")?>'+$("#txtIdToOpen").val();
    $("#frmDanfe").attr("src",url);
});

$(document).on('hide.bs.modal',"#modalDanfe",function(e){
    $("#frmDanfe").removeAttr("src");
});
</script>