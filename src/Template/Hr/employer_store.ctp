<br/>    
<form id="frmRegs" name="frmRegs">
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> <?=$title?>
				</div>
				<div lang="col-sm text-right">
					<a href="<?php echo $this->Url->build('/hr/employer_store_create');?>" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Nova associa&ccedil;&atilde;o</a>
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

function delEmployer(idFuncionario,idLoja){
     bootbox.dialog({message:"Deseja realmente esta associa&ccedil;&atilde;o?", 
        buttons:{
            yes:{
                label:"Sim",
                callback:function(){
                    $.ajax({
                        url: '<?=$this->Url->build("/hr/employer_store_remove/")?>'+idFuncionario+'/'+idLoja,
                        success: function(data){
                            if(data==true){
                                document.location.reload();
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
    return false;
}
</script>