<br/>    
<form id="frmRegs" name="frmRegs">
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> <?=$title?>
				</div>
				<div lang="col-sm text-right">
					<?php if($user['role']=="admin"):?>
					<a href="<?php echo $this->Url->build('/retail/customer_export_mail');?>" class="btn btn-warning btn-sm" id="btnExport"><i class="fas fa-file-export"></i> Exportar</a>
					<?php endif;?>
                	<a href="<?php echo $this->Url->build('/retail/customer_create');?>" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Novo</a>
					<button type="button" class="btn btn-dark btn-sm" data-toggle="modal" data-target="#modalFilter"><i class="fas fa-filter"></i> Filtrar</button>
				</div>
			</div>
		</div>
		<div class="card-body" id="divResult"></div>
	</div>
</form>
<br/>

<script>
$(document).ready(function(){
    url_data = '<?=$this->Url->build($url_data); ?>';
    getData();
   	url_filter = '<?=$this->Url->build($url_filter); ?>';
});

function importOrigin(idCliente){
    $.ajax({
        method: 'post',
        data:{ IDCLIENTE: idCliente },
        url: '<?=$this->Url->build("/retail/customer_import_origin")?>',
        success: function(data){
            if(data=="1"){
                $("#lnkOrigin"+idCliente).addClass("disabled");
            }else{
            	if(data=="-1"){
					bootbox.alert('CEP do cliente inv&aacute;lido!');
				}else{
					bootbox.alert('Ocorreu um erro ao tentar importa os dados da origem do cliente!');
				}
                
            }
        }
    });
}
</script>