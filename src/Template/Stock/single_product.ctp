<br/>
<form id="frmRegs" name="frmRegs">
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-sm">
					<i class="fas fa-angle-right"></i> <?=$title?>
				</div>
				<div lang="col-sm text-right">
					<a href="javascript:change_status('D')" class="btn btn-danger btn-sm"><i class="fas fa-eye-slash"></i> Desativar</a>
                    <a href="javascript:change_status('A')" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Ativar</a>
                    <a href="javascript:changePrice();" role="button" class="btn btn-warning btn-sm"><i class="fas fa-money-bill-wave"></i> Alterar Pre&ccedil;o</a>
					<a href="<?php echo $this->Url->build('/stock/single_product_create');?>" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Novo</a>
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

function change_status(status){
    //frmListProduct
    var totalChecked = 0;
    $("input[name='check_list[]']").each(function(){
        if( $(this)[0].checked ==true ){
            totalChecked = totalChecked + 1;
        }
    });

    if(totalChecked>0){
        bootbox.dialog({
            message:"Deseja realmente alterar o status do(s) produto(s) selecionado(s)?",
            buttons:{
                yes:{
                    label:"Sim",
                    callback:function(){
                        lockPost();
                        $.ajax({
                            headers:{
                                'X-CSRF-Token':csrf
                            },
                            method:'post',
                            url: '<?=$this->Url->build("/stock/product_change_status/")?>'+status,
                            data: $("#frmRegs").serialize(),
                            success: function(data){
                                unlockPost();
                                if(data==true){
									bootbox.alert("Produto(s) "+((status=="D")?"desativado(s)":"ativado(s)")+" com sucesso!",function(){ getData('<?=$this->Url->build($url_data); ?>'); });
                                }else{
                                    bootbox.alert("Ocorreu um erro ao tentar "+((status=="D")?"Desativar":"Ativar")+" o(s) Produto(s)!");
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
    }
    else{
        bootbox.alert("Selecione ao menos um produto para <strong>"+((status=="D")?"Desativar":"Ativar")+"</strong>.");
    }
}

function changePrice(){
    //frmListProduct
    var totalChecked = 0;
    $("input[name='check_list[]']").each(function(){
        if( $(this)[0].checked ==true ){
            totalChecked = totalChecked + 1;
        }
    });


    if(totalChecked>0){
        bootbox.dialog({
            message:"Deseja realmente alterar o pre&ccedil;o do(s) produto(s) selecionado(s)?",
            buttons:{
                yes:{
                    className: "btn-success",
                    label:"Sim",
                    callback:function(){
                        var dlg = bootbox.dialog({
                            title: "Por favor informe o novo pre&ccedil;o do(s) produto(s):",
                            message: "<input type='text' id='txtInputNovoPreco' class='form-control form-control-sm text-right' placeholder='0.00'>",
                            buttons:{
                                cancel:{
                                    label: 'Cancelar',
                                    className: 'btn-danger'
                                },
                                ok:{
                                    label: 'Alterar',
                                    className: 'btn-success',
                                    callback: function(){
                                        $.ajax({
                                            headers:{
                                                'X-CSRF-Token':csrf
                                            },
                                            type: "post",
                                            url: '<?=$this->Url->build("/stock/product_change_price/")?>'+$("#txtInputNovoPreco").val(),
                                            data: $("#frmRegs").serialize(),
                                            success: function (response) {
                                                if(response){
                                                    bootbox.alert('Pre&ccedil;o do(s) produto(s) alterado(s) com sucesso!',function(){ getData(); });
                                                }else{
                                                    bootbox.alert('Ocorreu um erro ao tentar alterar o(s) pre&ccedil;o(s) do(s) produto(s). Por favor contacte o suporte!');
                                                }
                                            }
                                        });
                                    }
                                }
                            }
                        });

                        dlg.init(function(){
                            $("#txtInputNovoPreco").mask("#,##0.00", {reverse: true});
                        });
                    }
                },
                no:{
                    className: "btn-danger",
                    label:"N\u00e3o"
                }
            }
        });
    }else{
        bootbox.alert("Selecione ao menos um produto para alterar o pre&ccedil;o.");
    }
}
</script>
