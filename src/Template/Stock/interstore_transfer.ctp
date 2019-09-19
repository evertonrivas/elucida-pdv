<br/>    
<form id="frmRegs" name="frmRegs">
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm"><i class="fas fa-angle-right"></i> <?=$title?></div>
				<div class="col-sm">
					<div class="text-right">
						<a href="javascript:disableChecked()" class="btn btn-danger btn-sm"><i class="fas fa-ban"></i> Cancelar</a>
		                <a href="javascript:procChecked()" class="btn btn-warning btn-sm"><i class="fas fa-random"></i> Processar</a>
		                <?php if($user['role']=="admin"): ?>
		                <a href="javascript:rescue()" class="btn btn-secondary btn-sm"><i class="fas fa-first-aid"></i> Resgatar</a>
		                <?php endif; ?>
		                <a href="<?php echo $this->Url->build('/stock/interstore_transfer_create');?>" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Nova</a>
		                <button type="button" class="btn btn-dark btn-sm" data-toggle="modal" data-target="#modalFilter"><i class="fas fa-filter"></i> Filtrar</button>
					</div>
				</div>
			</div>
		</div>
		<div class="card-body" id="divResult">    
			
		</div>
	</div>
</form>

<!-- MODAL DE SELECAO DE FORNECEDOR/LOJA DE DESTINO -->
<div class="modal fade" id="modalRescue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <form id="frmNfe" class="form">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                	<h4 class="modal-title" id="myModalLabel">Resgate de Transfer&ecirc;ncia</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">Fornecedor &agrave; ser utilizado</label>
                        <select id="cbFornece" name="cbFornece" class="form-control">

                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Loja de Destino</label>
                        <select id="cbLojaDestino" name="cbLojaDestino" class="form-control">
                            <option value=""></option>
                            <?php foreach($store_list as $store):?>
                            <option value="<?php echo $store->IDLOJA; ?>"><?php echo $store->NOME; ?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Utilizar</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!--MODAL DE EXIBICAO DA TRANSFERENCIA-->
<div class="modal fade" id="modalViewTransfer" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" id="modalLabel">Exibi&ccedil;&atilde;o de Transfer&ecirc;ncia</h4>
      </div>
      <div class="modal-body">
      	<input type="hidden" name="txtIdToOpen" id="txtIdToOpen"/>
            <iframe id="frmViewTransfer" name="frmViewTransfer" frameborder="0" style="min-height:500px; max-height:550px; overflow-y:scroll;width:100%"></iframe>
      </div>
      <div class="modal-footer">
      	<button type="button" class="btn btn-default" data-dismiss="modal" id="btnCloseModal">Fechar</button>
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

$(document).on("submit","#frmNfe",function(event){
    event.preventDefault();
    $.ajax({
        url: '<?=$this->Url->build("/stock/interstore_transfer_mount_by_provider")?>',
        method: 'post',
        data:{ IDFORNECEDOR: $("#cbFornece").val(),LOJA_DESTINO: $("#cbLojaDestino").val() },
        success:function(data){
            if(data==true){
                document.location.reload();
            }else{
                bootbox.alert('Ocorreu um erro ao tentar gerar a transfer&ecirc;ncia &agrave; partir do fornecedor!');
            }
        }
    });
});

function disableChecked(){
    var totalChecked = 0;
    $("input[name='check_list[]']").each(function(){
        if( $(this)[0].checked ==true ){
            totalChecked = totalChecked + 1;
        }
    });
    
    if(totalChecked>0){
        bootbox.dialog({message:"Deseja realmente cancelar a(s) transfer&ecirc;ncia(s) selecionada(s)?", 
            buttons:{
                yes:{
                    label:"Sim",
                    callback:function(){
                        $.ajax({
                            method:'post',
                            url: '<?=$this->Url->build("/stock/interstore_transfer_cancel/")?>',
                            data: $("#frmListTransfer").serialize(),
                            success: function(data){
                                if(data){
                                	bootbox.alert("Transfer&ecirc;ncia(s) cancelada(s) com sucesso!",function(){ document.location.reload(); });
                                }else{
                                	bootbox.alert("Ocorreu um erro ao tentar cancelar a(s) Transfer&ecirc;ncia(s)!");
                                }
                            }
                        });
                    }
                },
                no:{
                    className: "btn-success",
                    label:"N\u00e3o"			
                }
            }
        });
    }else{
        bootbox.alert("Selecione ao menos uma transfer&ecirc;ncia para cancelar.");
    }
    return false;
}

function procChecked(){
    var totalChecked = 0;
    $("input[name='check_list[]']").each(function(){
        if( $(this)[0].checked ==true ){
            totalChecked = totalChecked + 1;
        }
    });
    
    if(totalChecked>0){
        bootbox.dialog({message:"Deseja realmente processar a(s) transfer&ecirc;ncia(s) selecionada(s)?", 
        buttons:{
            yes:{
                label:"Sim",
                callback:function(){
                    $.ajax({
                        method:'post',
                        url: '<?=$this->Url->build("/stock/stock_transfer_execute/")?>',
                        data: $("#frmListTransfer").serialize(),
                        success: function(data){
                            if(data){
                            	bootbox.alert("Transfer&ecirc;ncia(s) processada(s) com sucesso!",function(){ document.location.reload(); });
                            }else{
                            	bootbox.alert("Ocorreu um erro ao tentar processar a(s) Transfer&ecirc;ncia(s)!");
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
    }else{
        bootbox.alert("Selecione ao menos uma transfer&ecirc;ncia para processar.");
    }    
    return false;
}

function rescue(){
    $("#modalRescue").modal({
        backdrop:'static'
    });
}

$(document).on('shown.bs.modal',"#modalRescue",function(){
    
    $('#cbFornece')
    .find('option')
    .remove()
    .end();
    
    $.ajax({
        url:'<?=$this->Url->build("/stock/interstore_transfer_rescue_provider")?>',
        dataType:'json',
        success:function(data){
            $("#cbFornece").append("<option value=''>&laquo; Selecione &raquo;</option>");
            for(var i=0;i<data.length;i++){
                $("#cbFornece").append("<option value='"+data[i].IDFORNECEDOR+"'>"+data[i].FANTASIA+"</option>");
            }
        }
    });
});

$(document).on('show.bs.modal',"#modalViewTransfer",function(e){
    var url = '<?=$this->Url->build("/stock/interstore_transfer_show/")?>'+$("#txtIdToOpen").val();
    $("#frmViewTransfer").attr("src",url);
});

function showTransfer(idTransferencia){
    $("#txtIdToOpen").val(idTransferencia);

    $("#modalViewTransfer").modal({
        backdrop: 'static'
    });
}
</script>