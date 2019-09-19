<br/>
<form id="frmRegs" name="frmRegs">
	<div class="card">
		<div class="card-header"><i class="fas fa-angle-right"></i> <?=$title?>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="cbNfeRecebida">Selecione a Nota Fiscal de Origem</label>
				<select id="cbNfeRecebida" name="cbNfeRecebida" class="form-control form-control-sm">
					<option value="">&laquo; Selecione &raquo;</option>
					<?php foreach($nfe_recebida_list as $nfe_recebida):?>
					<option value="<?=$nfe_recebida->IDNFERECEBIDA;?>"><?=$nfe_recebida->NUMERO." - ".$nfe_recebida->NOME_EMITENTE?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group">
				<label>Item(ns) para devolu&ccedil;&atilde;o</label>
				<div id="dvItens"></div>
			</div>
			<div class="form-group text-right">
				<button type="submit" id="btnSend" name="btnSend" class="btn btn-primary btn-sm"><i class="fas fa-undo"></i> Emitir Devolu&ccedil;&atilde;o</button>
			</div>
		</div>
	</div>
</form>
<script>
$(document).on("change","#cbNfeRecebida",function(){
	$.ajax({
		headers:{
			'X-CSRF-Token':csrf
		},
		type:'POST',
		url:'<?=$this->Url->build("/tributary/issue_return_itens")?>',
		data: { IDNFERECEBIDA : $(this).val() },
		success: function(data){
			$("#dvItens").html(data);
		}
	});
});

$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();

	var totalChecked = 0;
    $("input[name='chkItemDevolve[]']").each(function(){
        if( $(this)[0].checked ==true ){
            totalChecked = totalChecked + 1;
        }
    });

    if(totalChecked>0){
		$.ajax({
			headers:{
				'X-CSRF-Token':csrf
			},
			data: $(this).serialize(),
			url:'<?=$this->Url->build("/tributary/make_return")?>',
			type:'POST',
			success:function(data){
                if(data){
                    bootbox.alert('Devolu&ccedil;&atilde;o realizada com sucesso!',function(){ document.location.href="<?=$this->Url->build('/tributary/list_return/')?>"; });
                }else{
                    bootbox.alert("Ocorreu um erro ao tentar emitir a nota de devolu&ccedil;&atilde;o! Por favor contacte o suporte.");
                }
			}
		});
	}
    else{
		bootbox.alert('Por favor selecione o(s) item/ns que deseja devolver e informe a(s) quantidade(s)!');
	}
});

function toggleItem(item){
	if($("#chkItemDevolve_"+item)[0].checked){
		$("#txtQtdeDevolve_"+item).removeAttr("disabled");
	}else{
		$("#txtQtdeDevolve_"+item).attr("disabled","");
	}
}
</script>
