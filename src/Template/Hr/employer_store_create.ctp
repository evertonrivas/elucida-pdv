<br/>
<form name="frmRegs" id="frmRegs" class="needs-validation" novalidate>
<div class="card">
    <div class="card-header">
    	<div class="row">
			<div class="col-sm">
				<i class="fas fa-angle-right"></i> Associa&ccedil;&atilde;o de Funcion&aacute;rio X Loja
			</div>
			<div class="col-sm text-right">
				<a href="<?php echo $this->Url->build('hr/employer_store');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
			</div>
		</div>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="txtEmployerName">Colaborador</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" id="txtEmployerName" name="txtEmployerName" readonly="" placeholder="Busque o funcion&aacute;rio desejado"/>
                <span class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="btnSearchEmployer" data-toggle="modal" data-target="#modalFindEmployer" title="Buscar Funcion&aacute;rio"><span class="fa fa-users"></span></button>
                </span>
            </div>
            <input type="hidden" id="txtIdEmployer" name="txtIdEmployer"/>
        </div>
        <div class="form-group">
            <label class="control-label">Loja(s)</label>
            <select name="cbEmployerStore" id="cbEmployerStore" class="form-control">
                <?php foreach($stores as $store){ ?>
                <option value="<?php echo $store->IDLOJA; ?>"><?php echo $store->NOME; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><i class="fas fa-hdd"></i> Salvar</button>
        </div>
    </div>
</div>
</form>  

<?=$this->Dialog->employer_find(); ?>

<script>
$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();

    var frmData = {
        IDFUNCIONARIO : $("#txtIdEmployer").val(),
        LOJA          : $("#cbEmployerStore").val()
    };

    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        type: 'POST',
        url: '<?=$this->Url->build("/hr/employer_store_save")?>',
        data: frmData,
        success: function(data){
            if(data==true){
                bootbox.alert('Conec&ccedil;&atilde;o realizada com sucesso!',function(){ document.location.href='hr/employer_store'; })
            }else{
                bootbox.alert('Ocorreu um erro ao tentar realizar a conec&ccedil;&atilde;o!');
            }
        }
    });
});

function useEmployer(idFuncionario){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/hr/employer_get_info/")?>'+idFuncionario,
        dataType:'json',
        success: function(result){
            $("#txtEmployerName").val(result.NOME);
            $("#txtIdEmployer").val(result.IDFUNCIONARIO);
            //realiza a revalidacao do campo
            $("#frmCadEmployer").formValidation('revalidateField', "txtEmployerName");
        }
    });
}
</script>