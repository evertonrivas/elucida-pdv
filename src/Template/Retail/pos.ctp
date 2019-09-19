<?php
    $disabled = "";
    switch($status_caixa){
        case -1: $disabled = " disabled"; break;
        case 1 : $disabled = ""; break;
        case 2 : $disabled = " disabled"; break;
        case 3 : $disabled = " disabled"; break;
        case 4 : $disabled = " disabled"; break;
    }
?>
<script>
var needClient = <?php echo $EXIGE_CLIENTE; ?>;
</script><br/>
<?= $this->Html->script('utf8_encode.js');?>
<?= $this->Html->script('md5.js');?>
<form id="frmRegs" class="form" name="frmRegs" method="post" action="<?php echo $this->Url->build("/retail/payment_process")?>">
<input type="hidden" id="_csrfToken" name="_csrfToken" value=<?= json_encode($this->request->getParam('_csrfToken')) ?>>
<div class="card">
	<div class="card-header">
		<i class="fas fa-angle-right"></i> Ponto de Venda
	</div>
	<div class="card-body" style="padding-bottom: 5px!important;">
		<div class="row" style="margin-bottom: 10px!important;">
			<div class="col">
				<div class="btn-group btn-group-justified" role="group">
					<div class="btn-group" role="group">
		                <button type="button" class="btn btn-info btn-sm" id="btnFidelizar" data-toggle="tooltip" data-placement="top" title="Atalho (F3)"><i class="fas fa-heart"></i> Fidelizar</button>
		            </div>
		            <div class="btn-group" role="group">
		                <button type="button" class="btn btn-warning btn-sm" id="btnValeTroca"<?php echo $disabled; ?> data-toggle="tooltip" data-placement="top" title="Atalho (F4)"><i class="fas fa-tags"></i> Cupom</button>
		            </div>
		            <div class="btn-group" role="group">
		                <button type="button" class="btn btn-danger btn-sm" id="btnCancel" disabled data-toggle="tooltip" data-placement="top" title="Atalho (Esc)"><i class="fas fa-eraser"></i> Cancelar</button>
		            </div>
		            <div class="btn-group" role="group">
		                <button type="button" class="btn btn-success btn-sm" id="btnFinish" disabled data-toggle="tooltip" data-placement="top" title="Atalho (F12)"><i class="fas fa-credit-card"></i> Finalizar</button>
		            </div>
		        </div>
			</div>
			<div class="col">
				<input type="text" class="form-control text-right form-control-lg" id="txtCodigoBarra" autocomplete="off"<?php echo $disabled;?> placeholder="C&Oacute;DIGO DE BARRAS"/>
			</div>
			<div class="col">
				<input type="text" class="form-control form-control-lg" id="txtNomeProduto" readonly="">
				<input type="hidden" name="txtPrecoUnitario" id="txtPrecoUnitario"/>
			</div>
			<div class="col-2">
				<div class="input-group input-group-lg">
        			<input type="number" class="form-control text-right input-group-sm" id="txtQuantidade" value="1" min="0" autocomplete="off"<?php echo $disabled;?>/>
        			<span class="input-group-append">
        				<button type="button" class="btn btn-primary" id="btnAddProduto" disabled="<?php echo $disabled;?>"><i class="fas fa-plus"></i></button>
        			</span>
        		</div>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<div class="card">
	    			<div style="min-height: 290px!important; max-height: 290px!important;overflow-y: scroll;" id="pnlItExpress"></div>
	    			<div class="card-footer text-right"><a href="javascript:modalExpress();" class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i> Adicionar Item</a></div>
				</div>
			</div>
			<div class="col">
			
				<div class="card" style="min-height: 180px; max-height: 180px; overflow-y: scroll;margin-bottom: 5px!important;">
                    <div id="divBasket"></div>
                </div>
                
    			<div class="card" style="padding:5px!important;margin-bottom: 5px!important;">
                    <div id="divBasketTotal">
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-group input-group-sm">
                        <span class="input-group-prepend">
                            <button class="btn btn-outline-secondary" type="button" id="btnSearchSell" data-toggle="modal" data-target="#modalCustomerBuy" title="Compras do Cliente" <?php if(!isset($NOME_CLIENTE)){ echo " disabled"; }?>><span class="fa fa-shopping-cart"></span></button>
                        </span>
                        <input type="text" id="txtNomeCliente" name="txtNomeCliente" class="form-control input-sm" placeholder="Busque o cliente desejado" readonly="true" value="<?php if(isset($NOME_CLIENTE)){ echo $NOME_CLIENTE; }?>"/>
                        <span class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="btnSearchCustomer" title="Cliente"<?php echo $disabled;?>><span class="fa fa-users"></span></button>
                        </span>
                    </div>
                    <div class="custom-control custom-switch">
					  <input type="checkbox" class="custom-control-input" id="chCpfNota" name="chCpfNota" value="1" <?php if(!isset($NOME_CLIENTE)){ echo " disabled"; }?>>
					  <label class="custom-control-label" for="chCpfNota"> CPF na nota</label>
					</div>
                </div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" id="txtIdCliente" name="txtIdCliente" value="<?php echo $IDCLIENTE;?>"/>
<input type="hidden" id="txtNewCustomer" name="txtNewCustomer" value="<?php echo $NEW_CUSTOMER;?>"/>
<input type="hidden" id="txtDescontoIndicacao" name="txtDescontoIndicacao" value="<?php echo $INDICATION_DISCOUNT; ?>"/>
<input type="hidden" id="txtIdFuncionario" name="txtIdFuncionario" value="<?php echo $IDFUNCIONARIO;?>"/>
<input type="hidden" id="txtIdCondicaoPromo" name="txtIdCondicaoPromo" value="<?php echo $CONDICAO_PROMOCAO;?>">
<input type="hidden" id="txtOtherTaxvat" name="txtOtherTaxvat" value="<?php echo $OUTRO_CPF;?>">
<input type="hidden" id="txtPrecoPromo" name="txtPrecoPromo" value="0">
</form><br/>
<!-- MODAL DE APLICACAO DO DESCONTO -->
<div class="modal fade" id="modalDesconto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <form id="frmDiscount" class="form">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h4 class="modal-title" id="myModalLabel">Fideliza&ccedil;&atilde;o de Venda</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">Tipo de Fideliza&ccedil;&atilde;o</label><br/>
                    <label class="radio-inline">
                        <input type="radio" name="radioTipoFideliza" id="optFidelizaVenda" value="1" checked onclick="enableProdutoDesconto()"> Sobre a Venda
                    </label>&nbsp;&nbsp;
                    <label class="radio-inline">
                        <input type="radio" name="radioTipoFideliza" id="optFidelizaProduto" value="2" onclick="disableProdutoDesconto()"> Sobre um Produto
                    </label>
                </div>
                <div class="form-group">
                    <label class="control-label">Formato do Desconto</label><br/>
                    <label class="radio-inline">
                        <input type="radio" name="radioFormaDesc" id="optFidelizaPerc" value="1" checked onclick="setMaskPercent()">
                        Percentual (%)
                    </label>&nbsp;&nbsp;
                    <label class="radio-inline">
                        <input type="radio" name="radioFormaDesc" id="optFidelizaValor" value="2" onclick="setMaskMoney()">
                        Valor (R$)
                    </label>
                </div>
                <div class="form-group">
                    <label class="control-label">Valor do Desconto</label>
                    <input type="text" id="txtValorDesconto" class="form-control form-control-sm">
                </div>
                <div class="form-group">
                    <label class="control-label">Produto</label>
                    <select id="cbProdutoDesconto" class="form-control form-control-sm" disabled="true"></select>
                </div>
                <div class="form-group">
                    <label class="control-label">Autoriza&ccedil;&atilde;o de Desconto</label>
                    <input type="password" class="form-control form-control-sm" disabled="true" id="txtSenhaDesconto" name="txtSenhaDesconto"/>
                    <p class="text-danger" id="msgInvalidPass">Senha inv&aacute;lida, por favor tente novamente!</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success btn-sm" id="btnAplicarDesconto" name="btnAplicarDesconto">Aplicar</button>
            </div>
        </div>
    </div>
    </form>
</div>

<!-- MODAL DE APLICACAO DE DESCONTO PROMOCIONAL -->
<div class="modal fade" id="modalPromocao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <form id="frmAddPromo" class="form">
        <input type="hidden" id="txtIdProdutoPromo" name="txtIdProdutoPromo">
        <input type="hidden" id="txtBarcodeProdutoPromo" name="txtBarcodeProdutoPromo">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Busca de Produtos</h4>
            </div>
            <div class="modal-body">
                <p><strong>Aten&ccedil;&atilde;o!</strong> Este produto est&aacute; em promo&ccedil;&atilde;o. <br/>Informe a condi&ccedil;&atilde;o de pagamento para que o desconto possa ser aplicado!</p>
                <div class="form-group">
                    <label class="control-label">Selecione a condi&ccedil&atilde;o de pagamento</label>
                    <select id="cbCondPagamentoPromo" name="cbCondPagamentoPromo" class="form-control">
                        <option value="">&laquo; Selecione &raquo;</option>
                        <?php foreach($condition_list as $condition):?>
                        <option value="<?php echo $condition->IDCONDICAOPAGAMENTO; ?>"><?php echo $condition->NOME;?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Aplicar promo&ccedil;&atilde;o</button>
            </div>
        </div>
    </div>
    </form>
</div>

<!-- MODAL COM AS INFORMACOES DE COMPRA DO CLIENTE -->
<div class="modal fade" id="modalCustomerBuy" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h4 class="modal-title" id="myModalLabel">Compras do cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <iframe name="ifrmCustomerBuy" id="ifrmCustomerBuy" frameborder="0" style="min-height:350px; max-height:350px; overflow-y:scroll;width:100%"></iframe>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE SELECAO DO VENDEDOR -->
<div class="modal fade" id="modalEmployer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Sele&ccedil;&atilde;o de Vendedor</h4>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <label>Por favor informe o respons&aacute;vel pela venda:</label><br/>
                    <div class="btn-group" role="group">
                        <?php if($employer_list->count()>0):?>
                        <?php  foreach($employer_list as $employer):?>
                        <button type="button" class="btn btn-primary" onclick="setEmployer(<?php echo $employer->IDFUNCIONARIO; ?>)"><i class="fas fa-user"></i><br><?php echo $employer->APELIDO; ?></button>
                        <?php endforeach; ?>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE CPF ALTERNATIVO -->
<div class="modal fade" id="modalTaxvat" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Informe o CPF alternativo</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>CPF alternativo:</label><br/>
                    <input type="text" id="txtModalTaxvat" name="txtModalTaxvat" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" data-dismiss="modal">Usar CPF</button>
            </div>
        </div>
    </div>
</div>


<?php $this->Dialog->product_single($IDLOJA); ?>
<?php $this->Dialog->product_multiple($IDLOJA); ?>
<?php $this->Dialog->customer_find(); ?>
<?php $this->Dialog->customer_new(); ?>
<script>
$(document).ready(function(){
<?php if($status_caixa==-1): ?>
	bootbox.alert("<strong>Aten&ccedil;&atilde;o</strong><br/> Ainda n&atilde;o existem informa&ccedil;&otile;es de abertura de caixa, por favor realize a primeira antes de prosseguir.",function(){ document.location.href='<?=$this->Url->build("/retail/box_open/")?>'; });
<?php elseif($status_caixa==2): ?>
	bootbox.alert("<strong>Aten&ccedil;&atilde;o</strong><br/>  Caixa j&aacute; fechado hoje, por favor realize uma nova abertura para continuar",function(){ document.location.href='<?=$this->Url->build("/retail/box_open")?>'; });
<?php elseif($status_caixa==3): ?>
	bootbox.alert("<strong>Aten&ccedil;&atilde;o</strong><br/>  Por favor realize o fechamento do caixa anterior e abra novamente para prosseguir",function(){ document.location.href='<?=$this->Url->build("/retail/box_close/")?>'; });
<?php elseif($status_caixa==4): ?>
	bootbox.alert("<strong>Aten&ccedil;&atilde;o</strong><br/>  Por favor realiza abertura do caixa para prosseguir!",function(){ document.location.href='<?=$this->Url->build("/retail/box_open")?>'; });
<?php endif; ?>
    $('#txtCodigoBarra').focus();
    
    //Tecla Esc para cancelar a venda
    $(document).keydown(function(evt){
        if (evt.keyCode==27){
            evt.preventDefault();
            $('#btnCancel').click();
            return false;
        }
    });

	
    //Tecla F3 para o botao fidelizar venda
    $(document).keydown(function(evt){
        if(evt.keyCode==114){
            evt.preventDefault();
            $('#btnFidelizar').click();
            return false;
        }	
    });
	
    //Tecla F4 para o botao vale troca/presente
    $(document).keydown(function(evt){
        if (evt.keyCode==115){
            evt.preventDefault();
            $('#btnValeTroca').click();
            return false;
        }
    });
	
    //Tecla F12 para o botao finalizar
    $(document).keydown(function(evt){
        if (evt.keyCode==123){
            evt.preventDefault();
            $('#btnFinish').click();
            return false;
        }
    });
  
  	//busca as informacoes dos itens adquiridos
    getBasket();
    
    //realiza busca dos itens expressos
   	loadExpressItens();
   	
    <?php if($status_caixa==-1):?>
    	bootbox.alert("<strong>Sem Registro de Caixa!</strong><br/><br/>N&atilde;o foi encontrado nenhum registro de caixa para esta loja, por favor realize a primeira abertura.",function(){ document.location.href='<?=$this->Url->build("/retail/box_open")?>'; });
    <?php endif;  
    if($status_caixa==3): ?>
    	bootbox.alert("<strong>Caixa Anterior Ainda Aberto!</strong><br/>O caixa do per&iacute;odo anterior ainda encontra-se aberto, realize o fechamento do mesmo, e abertura do atual para realizar novas vendas.",function(){ document.location.href='<?=$this->Url->build("/retail/box_close")?>'; });
    <?php endif; ?>
    <?php if($employer_list->count()==0):?>
    	bootbox.alert("<strong>Aten&ccedil;&atilde;o</strong><br/><br/>N&atilde;o &eachte; poss&iacute;vel realizar vendas sem ter cadastrado ao menos um colaborador para isso!",function(){ document.location.href='<?=$this->Url->build("/users/logoff")?>'; });
    <?php endif;?>
});

/**
* Evento que trata o que acontece no codigo de barras
*/
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
    
    //se pressionar o Enter
    if(event.keyCode == 13){
      event.preventDefault();

	  //se tiver preenchido entao tenta buscar as informacoes do produto
      if($("#txtCodigoBarra").val()!=''){
          getProductInfo($("#txtCodigoBarra").val(),'BAR');
      }
      else{
      	//exibe o modal para procurar um produto
        $("#modalProductSingle").modal({
            backdrop: 'static'
        });
      }
      return false;
    }
});

/**
* Evento que trata o que acontece na quantidade quando pressionada uma tecla
*/
$(document).on("keydown","#txtQuantidade",function(event){
	//se tiver pressionado enter
    if(event.keyCode == 13){
        event.preventDefault();
        //forca o clique no botao
        $("#btnAddProduto").click();
        return false;
    }
});

/**
* Evento que trata o clique do botao cancelar
*/
$(document).on("click", "#btnCancel", function() {
	//verifica se realmente quer cancelar a venda
    bootbox.dialog({message:"Deseja realmente cancelar esta venda?", 
        buttons:{
            yes:{
                label:"Sim",
                callback:function(){
                	//remove todas as informacoes de produtos
                    $.ajax({
                    	headers:{
							'X-CSRF-Token':csrf
						},
                        url: '<?=$this->Url->build("/retail/pos_basket_clear")?>',
                        success: function(data){
                        	//limpa os campos para retornar a tela ao estado original
                            $("#txtIdCondicaoPromo").val('');
                            $("#txtNomeCliente").val('');
                            $("#chCpfNota")[0].checked = false;
                            $("#chCpfNota").attr("disabled","disabled");
                            $("#txtIdCliente").val('');
                            $("#txtIdFuncionario").val('');
                            clearAndLockScreen();
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
});

/**
* Evento que trata o que acontece quando clica no botao de fidelizar
*/
$(document).on("click","#btnFidelizar",function(){
	//verifica se ja foi aplicado algum cupom
    if($("#txtTroca").val()!=""){
        bootbox.alert("N&atilde;o &eacute; poss&iacute;vel aplicar desconto em vendas que possuem algum cupom!");
    }
    else{
    	//exibe a tela para definir qual tipo de desconto serah aplicado
        $("#modalDesconto").modal({
            backdrop: 'static'
        });
        $("#msgInvalidPass").hide();
	setMaskPercent();
    }
});

/**
* Evento que trata o que acontece quando o modal de desconto for fechado
*/
$(document).on('hidden.bs.modal',"#modalDesconto",function(){
	//volta o form de desconto ao estado original
    $("#frmDiscount")[0].reset();
});

/**
* Evento que trata um clique no botao de troca
*/
$(document).on("click","#btnValeTroca",function(){
	//pede o numero do cupom que pode ser um cupom de troca
	//presente, desconto ou antecipacao de venda
    bootbox.prompt({
        title: "Por favor informe o c&oacute;digo do cupom (Troca, Presente, Desconto ou Antecipa&ccedil;&atilde;o)!",
        value:"",
        callback: function(result){
            if(result!==null){
            	//aplica o cupom
                $.ajax({
                	headers:{
						'X-CSRF-Token':csrf
					},
                    method:'post',
                    data: { CUPOM : result},
                    url: '<?=$this->Url->build("/retail/pos_cupom_apply/")?>',
                    success: function(data){
                        if(data==true){
                        	bootbox.alert("Cupom aplicado com sucesso!", function(){ getTotals(); });
                        }
                        if(data==-1){
                        	bootbox.alert("Cupom removido do sistema!",function(){ getTotals(); });
                        }
                        if(data==false){
                        	bootbox.alert("Cupom j&aacute; utilizado ou inexistente!");
                        }
                        $("#txtCodigoBarra").focus();
                    }
                });
            }
        },
        buttons:{
            cancel:{
                label: 'Cancelar',
                className: 'btn-secondary'
            },
            confirm:{
                label:'OK',
                className: 'btn-primary'
                
            }
        }
    });
});

/**
* Evento que trata o que acontece ao clicar no botao de adicionar produto
*/
$(document).on("click","#btnAddProduto",function(){
	//realiza uma verificao do tamanho do texto no campo quantidade
	//isso evita um problema que as vezes acontece que eh:
	//o cursor pode estar sobre o campo na hora da leitura do codigo
	//de barras
    if($("#txtQuantidade").val().length <= 3 ){
    	//adiciona o produto ao codigo ao carrinho de compras
        productBasketAdd($('#txtCodigoBarra').val(),$('#txtQuantidade').val());
    }else{
        bootbox.alert("Por favor verifique a quantidade informada do item!");
        $("#txtQuantidade").val("1");
        $("#txtQuantidade").focus();
    }
});

/**
* Evento que verifica o clique no botao fianlizar
*/
$(document).on("click","#btnFinish",function(){
	//verifica se ha necessidade do cliente e se tem cliente associado a venda
    if($("#txtIdCliente").val()=="" && needClient==1){
        bootbox.alert("Por favor informe ou cadastre o cliente da compra!");
        $("#btnSearchCustomer").focus();
        return false;
    }
    else{
        //exibe aqui o modal de selecao de vendedor
        if($("#txtIdFuncionario").val()==""){
            $("#modalEmployer").modal({
                backdrop: 'static',
                keyboard: false
            });
        }else{
            $("#frmRegs").submit();
        }
        
        /*
        //com o cadastro do cliente nao obrigatorio verifica se quer cpf na nota
        if($("#chCpfNota")[0].checked){
            //se quer precisa informar ou cadastrar o cliente
            if($("#txtIdCliente").val()==""){
                bootbox.alert("Por favor informe ou cadastre o cliente da compra!");
                $("#btnSearchCustomer").focus();
            }else{
                $("#frmcaixa").submit();
            }
        }else{
            //nao desejando cpf na nota apenas transmite as informacoes da venda
            $("#frmcaixa").submit();
        }*/
    }
});

/**
* Evento que trata o que acontece durante o envio do form de desconto
*/
$(document).on("submit","#frmDiscount",function(event){
    event.preventDefault();
    
    //obtem o conteudo do valor de desconto
    var valDesconto = $("#txtValorDesconto").val();
    
    //obtem a senha do campo jah transformando em hash se houver necessidade
    var senhaDesconto = ($("#txtSenhaDesconto").val()!="")?md5($("#txtSenhaDesconto").val()):"";
    
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method: 'post',
        data: { VALOR: valDesconto.replace("%","pct"), SENHA: senhaDesconto },
        url: '<?=$this->Url->build("/retail/pos_discount_check_size/")?>',
        success: function(data){
        	//se o retorno informar que precisa de senha
        	//irah desbloquear o campo senha
            if(data=="needpass"){
                $("#txtSenhaDesconto").removeAttr("disabled");
                $("#txtSenhaDesconto").focus();
            }
            //exibe a menhsagem de senha invalida
            if(data=="wrongpass"){
                $("#msgInvalidPass").show();
            }
            //se retornou que pode aplicar o desconto
            if(data=="canapply"){
            	//oculta a mensagem de senha invalida
                $("#msgInvalidPass").hide();
                
                //chama o metodo para aplicar o desconto
                $.ajax({
                	headers:{
						'X-CSRF-Token':csrf
					},
                    method:'post',
                    data: { VALOR: valDesconto.replace("%","pct"), PRODUTO: $("#cbProdutoDesconto").val() },
                    url: '<?=$this->Url->build("/retail/pos_discount_apply/")?>',
                    success: function(data){
                    	//limpa as informacoes do formulario
                        $("#txtSenhaDesconto").val("");
                        $("#txtValorDesconto").val("");
                        $("#txtSenhaDesconto").val("");
                        $("#txtSenhaDesconto").attr("disabled","true");
                        $("#cbProdutoDesconto").find('option')
                        .remove()
                        .end();
                        
                        //oculta a tela
                        $("#modalDesconto").modal('hide');
                        //busca os dados da venda
                        getBasket();
                        if(data>0){
                        	//exibe a mensagem de desconto aplicado
                            bootbox.alert('Desconto de R$ '+data+' aplicado!');
                        }
                        else{
                        	//ou a mensagem de desconto removido
                            bootbox.alert('Desconto removido!');
                        }
                    }
                });
            }
        }
    });    
});

/**
* Evento que trata o que acontece no envio do form de adicionar promocao
*/
$(document).on("submit","#frmAddPromo",function(evt){
    evt.preventDefault();
    
    //colocado para passar o valor ateh a tela de pagamento
    var condicao = $("#cbCondPagamentoPromo").val();
    $("#txtIdCondicaoPromo").val(condicao);
    
    //adiciona o produto ao carrinho de compras
    productBasketAdd($("#txtCodigoBarra").val(),$("#txtQuantidade").val());
    
    //fecha o modal de promocao
    $("#modalPromocao").modal('hide');
});

/**
* Evento que trata o que acontece quando o modal de promocao eh fechado
*/
$(document).on('hide.bs.modal',"#modalPromocao",function(){
	//limpa a tela
    $("#txtIdProdutoPromo").val('');
    $("#txtBarcodeProdutoPromo").val('');
    $("#cbCondPagamentoPromo").val('');
});

/**
* Evento que trata o que acontece quando o modal de compras do cliente eh exibido
*/
$(document).on('show.bs.modal',"#modalCustomerBuy",function(){
	//chama a URL passando o codigo do lciente
    var url = '<?=$this->Url->build("/retail/customer_buy/")?>'+$("#txtIdCliente").val();
    $("#ifrmCustomerBuy").attr("src",url);
});

/**
* Evento que trata o que acontece quando o modal de multiplos produtos eh fechado
*/
$(document).on("hide.bs.modal","#modalProductMultiple",function(){
	//carrega os itens expressos
    loadExpressItens();
});

/**
* Evento que trata o clique no botao de busca do cliente
*/
$(document).on("click","#btnSearchCustomer",function(){
	//verifica se jah ha um cliente
	if($("#txtIdCliente").val()!=""){
		//apenas busca as informacoes do mesmo se jah estiver id
		$.ajax({
			headers:{
				'X-CSRF-Token':csrf
			},
	        method:'post',
	        data : {IDCLIENTE : $("#txtIdCliente").val() },
	        url: '<?=$this->Url->build("/retail/customer_get_info")?>',
	        dataType:'json',
	        success:function(data){
	        	
	            $("#btnSearchSell").removeAttr("disabled");
	            $("#modalNewCustomer").modal({
	                backdrop:'static'
	            });
	            //definie os dados da tela do modal de cadastro do cliente
	            setCustomerRegistry(data);
	        }
	    });
	}else{
		//exibe o modal para buscar o cliente
		$("#modalFindCliente").modal({
			backdrop:'static'
		});
	}
});

/**
* Evento que trata o cliente no switch de cpf na nota
*/
$(document).on("click","#chCpfNota",function(){
	//verifica se esta em ON
	if($("#chCpfNota")[0].checked){
		//pergunta se deseja utilizar outro CPF ou o atual para o cliente
		bootbox.confirm({
			message: 'Deseja usar outro CPF ao inv&eacute;s do CPF do cliente?',
			buttons:{
				confirm:{
					label: 'Sim',
				},
				cancel:{
					label: 'N&atilde;o'
				}
			},
			callback:function(result){
				if(result){
					//se clicou sim entao eh exibida a nova tela para entrar com as informacoes
					$("#modalTaxvat").modal({ backdrop:'static' });
					$("#txtModalTaxvat").mask('000.000.000-00');
				}
			}
		});	
	}
});

/**
* Evento que trata o evento de fechar o modal de CPF
*/
$(document).on("hide.bs.modal","#modalTaxvat",function(){
	//se tiver preenchido o campo na tela
	if($("#txtModalTaxvat").val()!=""){
		//joga no campo outro CPF o valor da tela
		$("#txtOtherTaxvat").val($("#txtModalTaxvat").val());
	}
});

/**
* Funcao que busca os totais dos itens que estao sendo comprados
* 
* @return null
*/
function getTotals(){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/retail/pos_basket_totals/")?>',
        success: function(data){
            $("#divBasketTotal").html(data);
        }
    });
}

/**
* Funcao que desbloqueia os botoes da tela
* 
* @return null
*/
function unlockScreen(){
    $("#btnFidelizar").removeAttr("disabled");
    $("#btnCancel").removeAttr("disabled");
    $("#btnFinish").removeAttr("disabled");
}

/**
* Funcao que bloqueia os botoes da tela
* 
* @return null
*/
function lockScreen(){
    $("#btnFidelizar").attr("disabled","true");
    //$("#btnValeTroca").attr("disabled","true");
    $("#btnCancel").attr("disabled","true");
    $("#btnFinish").attr("disabled","true");
}

/**
* Funcao que limpa os dados da tela
* 
* @return null
*/
function clearFields(){
    $("#txtNomeProduto").val("");
    $("#txtCodigoBarra").val("");
    $("#txtQuantidade").val("1");
    $("#txtPrecoUnitario").val("0.00");
    $("#btnAddProduto").attr("disabled","true");
    //$("#txtDisponibilidade").val("0");
    $("#txtPrecoPromo").val("0");
    $("#txtCodigoBarra").focus();
}

/**
* Funcao que alem de limpar a tela bloqueia
* 
* @return null
*/
function clearAndLockScreen(){
	//chama a funcao para limpar a tela
    clearFields();
    //chama a funcao para bloquear a tela
    lockScreen();
    //busca a cesta de compras
    getBasket();
}

/**
* Desabilita o combo com os produtos se o desconto for
* sobre a venda
* 
* @return
*/
function enableProdutoDesconto(){
    $("#cbProdutoDesconto").attr("disabled","true");
    $("#cbProdutoDesconto").val("");
}

/**
* Funcao que habilita o campo dos produtos da venda
* caso o desconto seja aplicado apenas sobre um produto
* 
* @return
*/
function disableProdutoDesconto(){
    $("#cbProdutoDesconto").removeAttr("disabled");
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/retail/pos_basket_itens")?>',
        dataType: 'json',
        success: function(data){
            for(var i=0;i<data.length;i++){
                $("#cbProdutoDesconto").append(new Option(data[i].NOME_PRODUTO, data[i].IDPRODUTO));
            }
        }
    });
}

/**
* Funcao que coloca mascara de desconto no campo do desconto 
* se o desconto for monetario
* 
* @return
*/
function setMaskMoney(){
    $("#txtValorDesconto").mask('#,##0.00', {reverse: true});
}

/**
* funcao que coloca mascara de desconto no campo do desconto
* se o desconto for percentual
* 
* @return
*/
function setMaskPercent(){
    $("#txtValorDesconto").mask('##0.00%', {reverse: true});
}

/**
* Funcao que busca os itens da compra
* 
* @return null
*/
function getBasket(){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/retail/pos_basket_get")?>',
        success: function(data){
            getTotals();
            if(data){
                $("#divBasket").html(data);
                //limpa os campos da tela
                clearFields();
            }
        }
    });
}

/**
* Funcao que busca as informacoes do produto
* 
* @return null
*/
function getProductInfo(id,tipo_codigo){
    
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method : 'post',
        url: '<?=$this->Url->build("/stock/product_get_info/")?>'+id+'/'+tipo_codigo+'/<?=$IDLOJA?>',
        success: function(result){
        	
            var obj = $.parseJSON( result );
            
            //verifica se encontrou um produto
            if(obj.NOME!=null){
                if(obj.QUANTIDADE_ESTOQUE==0 && obj.ESTRUTURA=='S'){
                    alert('N\u00e3o h\u00e1 disponibilidade deste produto em estoque!');
                    $("#txtCodigoBarra").val("");
                    $("#txtCodigoBarra").focus();
                }
                else{
                    if(obj.QUANTIDADE_ESTOQUE==-1 && obj.ESTRUTURA=='C'){
                        alert('N\u00e3o h\u00e1 produtos suficientes para montar este kit!');
                        $("#txtCodigoBarra").val("");
                        $("#txtCodigoBarra").focus();
                    }else{
                        $("#txtCodigoBarra").val(obj.CODIGO_BARRA);
                        $("#txtNomeProduto").val(obj.NOME);
                        $("#txtPrecoUnitario").val(obj.PRECO_VENDA.toFixed(2));
                        $("#txtQuantidade").attr("max",obj.QUANTIDADE_ESTOQUE);
                        //$("#txtDisponibilidade").val(obj.QUANTIDADE_ESTOQUE);
                        $("#btnAddProduto").removeAttr("disabled");
                        
                        //verifica se o produto possui alguma promocao, 
                        //se houver mostra a tela que solicita o tipo do pagamento
                        $.ajax({
                        	headers:{
								'X-CSRF-Token':csrf
							},
                            method:'post',
                            data: { IDPRODUTO: obj.IDPRODUTO }, 
                            url : '<?=$this->Url->build("/retail/promo_iten_in/")?>',
                            dataType:'json',
                            success:function(data){
                            	//mostra a tela e solicita o tipo de pagamento
                                if(data.IDCONDICAOPAGAMENTO > 0){
                                    $("#txtPrecoPromo").val(data.PRECO_PROMO);
                                    $("#txtPrecoUnitario").val(data.PRECO_PROMO);
                                    $("#modalPromocao").modal({
                                        backdrop:'static'
                                    });
                                }else{
                                    if(data.IDCONDICAOPAGAMENTO == 0){
                                        $("#txtPrecoPromo").val(data.PRECO_PROMO);
                                        $("#txtPrecoUnitario").val(data.PRECO_PROMO);
                                    }
                                }
                            }
                        })
                    }
                }
            }
            else{
            	//exibe a mensagem que na encontrou o produto no sistema
                bootbox.alert("Produto inexistente no sistema, por favor verifique o c&oacute;digo de barras!");
                clearFields();
                $("#txtCodigoBarra").focus();
            }
        }
    });
    $('#txtQuantidade').focus();
}

/**
* Funcao que carrega os itens expressos, ou seja, 
* aqueles itens de venda com um clique
* 
* @return
*/
function loadExpressItens(){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/retail/pos_express_itens_get")?>',
        success: function(data){
            $("#pnlItExpress").html(data);
        }
    });
}

/**
* Funcao que adiciona um produto aos itens de compra
* @param idProduto Codigo do Produto
* @param quantidade Quantidade do Produto
* 
* @return
*/
function productBasketAdd(idProduto,quantidade){
    
    var precoPromo = 0;
    //verifica se o produto tem preco promocional
    if($("#txtPrecoPromo").val()!=0){
        precoPromo = $("#txtPrecoPromo").val();
    }
    
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/retail/pos_basket_add/")?>'+idProduto+'/'+quantidade+'/'+precoPromo,
        success: function(data){
            if(data){
                getBasket();
                unlockScreen();
                clearFields();
            }else{
                bootbox.alert('N&atilde;o h&aacute; quantidades suficientes desse item para adi&ccedil;&atilde;o!')
                clearFields();
            }
        }
    });
}

/**
* Funcao que exclui um produto da cesta de produtos
* 
* @return null
*/
function productDel(idProduto,nomeProduto){
	//verifica se realmente deseja excluir
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
                        data:{ IDPRODUTO: idProduto },
                        url: '<?=$this->Url->build("/retail/pos_basket_del/")?>',
                        success: function(data){
                            if(data){
                                getBasket();
                                clearFields();
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

/**
* Funcao que exibe o modal para adicionar produtos aos 
* itens expressos
* 
* @return null
*/
function modalExpress(){
    $("#modalProductMultiple").modal({
        backdrop:'static'
    });
    $("#isExpress").val("1");
}

//funcao utilizada pelo dialog simple
function useSingleProduct(idProduto){
    getProductInfo(idProduto,'ID');
}

//funcao utilizada pelo dialog multiple
function addProduct(idProduto){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method:'post',
        data: { IDPRODUTO: idProduto },
        url: '<?=$this->Url->build("/retail/pos_express_item_add/")?>',
        success: function(data){
            if(data){
                $("#lnk"+idProduto).addClass("disabled");
                //loadExpressItens();
            }
        }
    });
}

//busca as informacoes do cliente apos selecionado no dialog
function useCustomer(idCliente){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method:'post',
        data : {IDCLIENTE : idCliente},
        url  : '<?=$this->Url->build("/retail/customer_get_info")?>',
        dataType:'json',
        success:function(data){
            $("#txtIdCliente").val(data.IDCLIENTE);
            $("#txtNomeCliente").val(data.NOME);
            $("#btnSearchSell").removeAttr("disabled");
            $("#chCpfNota").removeAttr("disabled");
            
            if(data.STATUS_CADASTRO==0){
                $("#modalNewCustomer").modal({
                    backdrop:'static'
                });
                setCustomerRegistry(data);
            }
            getCustomerDiscount(data.IDCLIENTE);
        }
    });
}

/**
* Funcao que busca o desconto do cliente
* 
* @return
*/
function getCustomerDiscount(idCliente){
	$.ajax({
		headers:{
			'X-CSRF-Token':csrf
		},
		method:'post',
		data: { IDCLIENTE: idCliente },
		url: '<?=$this->Url->build("/retail/customer_get_discount")?>',
		success: function(pData){
			if(pData>0){
				var valor = parseFloat(pData);
				bootbox.confirm({
					message: 'O cliente possui um desconto total de <strong>R$ '+valor.toFixed(2)+'</strong>. Deseja utiliz&aacute;-lo agora?',
					buttons:{
						confirm:{
							label: 'Sim'
						},
						cancel:{
							label: 'N&atilde;o'
						}
					},
					callback:function(result){
						if(result){
							$.ajax({
								headers:{
									'X-CSRF-Token':csrf
								},
			                    method:'post',
			                    data: { VALOR: pData, PRODUTO: '' },
			                    url: '<?=$this->Url->build("/retail/pos_discount_apply/")?>',
			                    success: function(data){
			                        $("#txtSenhaDesconto").val("");
			                        $("#txtValorDesconto").val("");
			                        $("#txtSenhaDesconto").val("");
			                        $("#txtSenhaDesconto").attr("disabled","true");
			                        $("#cbProdutoDesconto").find('option')
			                        .remove()
			                        .end();
			                        
			                        getBasket();
			                        if(data>0){
			                        	$("#txtDescontoIndicacao").val(data);
			                            bootbox.alert('Desconto de R$ '+data+' aplicado!');
			                        }
			                    }
			                });
						}
					}
				});
			}
			//bootbox.alert("Valor total do desconto: "+data);
		}
	});
}

/**
* Funcao que aplica os dados do cliente
* 
* @return null
*/
function setCustomerData(data){
    $("#txtIdCliente").val(data.IDCLIENTE);
    $("#txtNomeCliente").val(data.NOME);
    $("#txtNewCustomer").val(1);
}

/**
* Funcao que aplica os dados do vendedor
* 
* @return
*/
function setEmployer(idFuncionario){
    $("#txtIdFuncionario").val(idFuncionario);
    $("#modalEmployer").modal('hide');
    $("#frmRegs").submit();
}
</script>