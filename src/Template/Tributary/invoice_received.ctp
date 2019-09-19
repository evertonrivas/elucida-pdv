<br/>    
<form id="frmRegs" name="frmRegs">
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> <?=$title?>
				</div>
				<div lang="col-sm text-right">
					<?php if($is_to_process): ?>
					<button class="btn btn-info btn-sm" id="btnProc" name="btnProc" type="button"><i class="fas fa-share-square"></i> Dispensar Processamento</button>
					<?php endif; ?>
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

$(document).on("click","#btnProc",function(){
    var totalChecked = 0;
    $("input[name='check_list[]']").each(function(){
        if( $(this)[0].checked ==true ){
            totalChecked = totalChecked + 1;
        }
    });
    
    if( totalChecked > 0){
        bootbox.dialog({message:"Deseja realmente dispensar o processamento da(s) nota(s) selecionada(s)?", 
        buttons:{
            yes:{
                label:"Sim",
                callback:function(){
                    $.ajax({
                    	headers:{
							'X-CSRF-Token':csrf
						},
                        method: 'post',
                        url: '<?=$this->Url->build("/tributary/invoice_received_dismiss_process/")?>',
                        data: $("input[name='check_list[]']").serialize(),
                        success: function(data){
                            if(data){
                            	bootbox.alert("NF-e(s) dispensada(s) do processamento com sucesso!",function(){ getData(); });
                            }
                            else{
                                bootbox.alert("Ocorreu um erro ao tentar dispensar a(s) NF-e(s) do processamento!");
                            }
                        }
                    });
                }
            },
            no:{
                label:"N\u00e3o"			
            }
        }
        });
        /**/
    }else{
        bootbox.alert("Selecione ao menos uma Nota Fiscal para processar!");
    }
});


function showDanfe(idNFE){
    $("#txtIdToOpen").val(idNFE);

    $("#modalDanfe").modal({
        backdrop: 'static'
    });
}

$(document).on('show.bs.modal',function(e){
    var url = '<?=$this->Url->build("/tributary/invoice_received_show/")?>'+$("#txtIdToOpen").val();
    $("#frmDanfe").attr("src",url);
});

$(document).on('hide.bs.modal',function(e){
    $("#frmDanfe").removeAttr("src");
});
</script>