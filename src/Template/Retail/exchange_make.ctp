<br/><form id="frmRegs" name="frmRegs" method="post">
<div class="card">
  <div class="card-header">
		<i class="fas fa-angle-right"></i> Troca de Produto
  </div>
  <div class="card-body">
    <div class="row">
    	<div class="col-4">
    		<div class="card">
    			<div class="card-body">
	        		<div class="form-group">
	                    <label class="control-label">Produto (C&oacute;digo de Barras)</label>
	                    <input type="text" class="form-control text-right form-control-sm" id="txtCodigoBarra" autocomplete="off"/>
	                </div>
	                <div class="form-group">
	                    <label class="control-label">Quantidade</label>
	                    <input type="number" class="form-control form-control-sm" id="txtQuantidade" value="1" autocomplete="off"/>
	                </div>
	                <div class="form-group">
	                    <label class="control-label">Pre&ccedil;o Unit&aacute;rio</label>
	                    <input type="text" class="form-control form-control-sm" id="txtPrecoUnitario" value="0,00" autocomplete="off" disabled="true"/>
	                </div>
	                <div class="form-group">
	                    <label class="conrol-label">Subtotal</label>
	                    <input type="text" class="form-control form-control-sm" id="txtSubtotalProduto" value="0,00" autocomplete="off" disabled="true"/>
	                </div>
	                <div class="form-group text-right">
	                    <button type="button" class="btn btn-primary btn-sm" id="btnAddProduto" disabled="true"><i class="fas fa-cubes"></i> Adicionar Produto</button>
	                </div>
	            </div>
            </div>
    	</div>
    	<div class="col-8">
    		<div class="row">
    			<div class="col-sm">
	        		<div class="card">
	        			<div class="card-body" id="divBasket" style="min-height: 320px; max-height: 320px; overflow-y: scroll;">
	        				
	        			</div>
	        		</div>
        		</div>
    		</div><br/>
    		<div class="row">
    			<div class="col-sm">
    				<div class="card">
    					<div class="card-body text-right" id="divBasketTotal">
    						
    					</div>
    				</div>
    			</div>
    		</div>
    	</div>
    </div>    
  </div>
</div>
</form>

<!--MODAL-->
<div class="modal fade" id="modalTroca" tabindex="-1" role="dialog" aria-labelledby="modalTrocaLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalTrocaLabel">Aten&ccedil;&atilde;o</h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {  
    //Tecla F12 para o botao finalizar
    $(document).keydown(function(evt){
        if (evt.keyCode==123){
            evt.preventDefault();
            $('#frmChange').submit();
        }
    });

    getBasket();
    
    $('#txtCodigoBarra').focus();
});

$(document).on("keydown","#txtCodigoBarra",function(event){
    // Permitidos: backspace, delete, tab, escape
    if ($.inArray(event.keyCode, [46, 8, 9, 27]) !== -1 ||
         // Permitido: Ctrl+A, Command+A
        (event.keyCode == 65 && ( event.ctrlKey === true || event.metaKey === true ) ) || 
         // Permitidos: home, end, left, right, down, up
        (event.keyCode >= 35 && event.keyCode <= 40)) {
             // let it happen, don't do anything
             return;
    }
    
    //apenas previne que nao numeros sejam digitados
    if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) && (event.keyCode < 96 || event.keyCode > 105)) {
        event.preventDefault();
    }
    
    if(event.keyCode == 13) {
        event.preventDefault();
      
        if($("#txtCodigoBarra").val()!=''){
            $.ajax({
            	headers:{
					'X-CSRF-Token':csrf
				},
                url: '<?=$this->Url->build("/stock/product_get_info/")?>'+$('#txtCodigoBarra').val()+'/BAR/<?=$user['storeid'];?>',
                dataType: 'json',
                success: function(result){
                    $("#txtPrecoUnitario").val(result.PRECO_VENDA);
                    $("#txtSubtotalProduto").val(result.PRECO_VENDA);
                    $("#txtQuantidade").attr("max",result.QUANTIDADE);
                    $("#txtDisponibilidade").val(result.QUANTIDADE_ESTOQUE);
                    $("#btnAddProduto").removeAttr("disabled");
                    $("#btnCancel").removeAttr("disabled");
                    $("#btnFidelizar").removeAttr("disabled");
                }
            });

            $('#txtQuantidade').focus();
        }else{
            alert("Informe o c\u00f3digo de barras do produto!");
            $("#txtCodigoBarra").focus();
        }
        return false;
    }
});

$(document).on("keydown","#txtQuantidade",function(event){
    if(event.keyCode == 13){
        event.preventDefault();
        $("#btnAddProduto").click();
        return false;
    }
});

$(document).on("click","#btnAddProduto",function(){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method: 'post',
        data: { PRODUTO: $("#txtCodigoBarra").val(),QUANTIDADE:$('#txtQuantidade').val() },
        url: '<?=$this->Url->build("/retail/exchange_item_add/")?>',
        success: function(data){
            if(data){
                getBasket();
                clearFields();
            }
            else{
                bootbox.alert('Ocorreu um problema ao tentar adicionar o produto!');
            }
        }
    });
});

$(document).on("focus","#txtQuantidade",function(){
    if($("#txtCodigoBarra").val()==""){
        alert("Informe o c\u00f3digo de barras do produto!");
        $("#txtCodigoBarra").focus();
    }
});

$(document).on("change","#txtQuantidade",function(){
    var subtotal = $("#txtPrecoUnitario").val()*$("#txtQuantidade").val();
    $("#txtSubtotalProduto").val(subtotal.toFixed(2));
});

$(document).on("submit","#frmRegs",function(e){
    e.preventDefault();
    
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method:'post',
        data: { VALOR_TOTAL: $("#txtSubtotal").val() },
        url: '<?=$this->Url->build("/retail/exchange_execute")?>',
        success: function(data){
            if(data==false){
                $("#modalTroca").find('.modal-title').text('Aten\u00e7\u00e3o!!!');
                $("#modalTroca").find('.modal-body').html('Ocorreu um erro ao tentar efetivar a troca, por favor verifique!');
                $("#modalTroca").modal({
                    backdrop: 'static'
                });
            }
            else{
                $("#modalTroca").find('.modal-title').text('Anote!!!');
                $("#modalTroca").find('.modal-body').html('Troca/devolu&ccedil;&atilde;o efetivada com sucesso, o n&uacute;mero da troca &eacute; <strong>'+data+'</strong>. Anote este n&uacute;mero, pois ser&aacute; pedido na venda!');
                $("#modalTroca").modal({
                    backdrop: 'static'
                });
                getBasket();
            }		
        }
    });
});

function getBasket(){
    $.ajax({
    	headers:{
			'X-CSRF-Token': csrf
		},
        url: '<?=$this->Url->build("/retail/exchange_itens")?>',
        success: function(data){
            getTotals();
            $("#divBasket").html(data);
        }
    });
}

function getTotals(){
    $.ajax({
        url: '<?=$this->Url->build("/retail/exchange_totals")?>',
        success: function(data){
            $("#divBasketTotal").html(data);
            if($("#txtSubtotal").val()=="0"){
                lockScreen();
            }else{
                unlockScreen();
            }
        }
    });
}

function unlockScreen(){
    $("#btnEfetivar").removeAttr("disabled");
}

function lockScreen(){
    $("#btnEfetivar").attr("disabled","true");
}

function clearFields(){
	$("#frmRegs").removeClass("was-validated");
	
    $("#txtNomeProduto").html("&nbsp;");
    $("#txtCodigoBarra").val("");
    $("#txtQuantidade").val("1");
    $("#txtPrecoUnitario").val("0.00");
    $("#txtSubtotalProduto").val("0.00");
    $("#btnAddProduto").attr("disabled","true");
    $("#imgThumb").removeAttr("src");
    $("#txtCodigoBarra").focus();
}

function clearAndLockScreen(){
    lockScreen();
    clearFields();
    $("#divBasket").html('<table class="table table-striped" style="font-size:12px!important"><thead><tr><th>Produto</th><th>Pre&ccedil;o UN.</th><th>Qtde</th><th>#</th></tr></thead>');
}

function dropItem(idProduto,nomeProduto){
    bootbox.dialog({message:"Deseja realmente excluir o produto '"+nomeProduto+"'?", 
    buttons:{
        yes:{
            label:"Sim",
            callback:function(){
                $.ajax({
                	headers:{
						'X-CSRF-Token':csrf
					},
                    method:'post',
                    data: { IDPRODUTO: idProduto },
                    url: '<?=$this->Url->build("/retail/exchange_item_del/")?>',
                    success: function(data){
                        if(data){
                            getBasket();
                        }else{
                            bootbox.alert('Ocorreu um problema ao tentar excluir o produto');
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