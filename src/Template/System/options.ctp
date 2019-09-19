<br/>
<form id="frmRegs" name="frmRegs">
	<div class="card">
		<div class="card-header">
			<i class="fas fa-angle-right"></i> <?=$title?>
		</div>
		<div class="card-body" id="divResult">    
			
		</div>
		<div class="card-footer text-right">
			<button class="btn btn-primary btn-sm" id="btnSend" name="btnSend"><i class="fas fa-hdd"></i> Salvar</button>
		</div>
	</div>
</form>

<script>
$(document).ready(function(){
	url_data = '<?=$this->Url->build($url_data); ?>';
    getData();
});

$(document).on("submit","#frmRegs",function(e){
    
    e.preventDefault();
    
    $.ajax({
		headers: {
			'X-CSRF-Token' : csrf
		},
        method: 'POST',
        url: '<?=$this->Url->build($url_data_save);?>',
        data: $("#frmRegs").serialize(),
        success: function(data){
            if(data==true){
                bootbox.alert("Op&ccedil;&otilde;es salvas com sucesso!",function(){ getData(); });
            }else{
                bootbox.alert("Ocorreu um erro ao tentar salvar as op&ccedil;&otilde;es!");
            }
        }
    });
});
</script>