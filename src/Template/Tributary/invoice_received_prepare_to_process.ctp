<br/>
<form name="frmRegs" id="frmRegs" method="post" action="<?php echo $this->Url->build('/tributary/invoice_received_process')?>" class="form">
<input type="hidden" id="txtIdNfeRecebida" name="txtIdNfeRecebida" value="<?php if(isset($nfe_recebida) ){ echo $nfe_recebida->IDNFERECEBIDA; } ?>"/>
<input type="hidden" id="_csrfToken" name="_csrfToken" value=<?= json_encode($this->request->getParam('_csrfToken')) ?>>
    <div class="card">
        <div class="card-header">
        	<i class="fas fa-angle-right"></i> <?=$title?>
        </div>
        <div class="card-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-2">
                            <label for="stcNumero">N&uacute;mero</label>
                            <p id="stcNumero"><?php if(isset($nfe_recebida) ){ echo $nfe_recebida->NUMERO; } ?></p>
                        </div>
                        <div class="col-2">
                            <label for="stcEmissao">Data de Emiss&atilde;o</label>
                            <p id="stcEmissao"><?php if(isset($nfe_recebida) ){ echo $nfe_recebida->DATA_EMISSAO->format("d/m/Y H:i:s"); } ?></p>
                        </div>
                        <div class="col-2">
                            <label for="stcVolumes">Volumes</label>
                            <p id="stcVolumes"><?php if(isset($nfe_recebida) ){ echo $nfe_recebida->NUM_VOLUME; } ?></p>
                        </div>
                        <div class="col-2">
                            <label for="stcValorNota">Valor da Nota</label>
                            <p id="stcValorNota"><?php if(isset($nfe_recebida) ){ echo $this->Number->currency($nfe_recebida->VALOR_NOTA,"BRL"); } ?></p>
                        </div>
                        <div class="col-2">
                            <label for="stcValorProdutos">Valor dos Produtos</label>
                            <p id="stcValorProdutos"><?php if(isset($nfe_recebida) ){ echo $this->Number->currency($nfe_recebida->VALOR_PRODUTOS."BRL"); } ?></p>
                        </div>
                        <div class="col-2">
                            <label for="stcValorFrete">Valor do Frete</label>
                            <p id="stcValorFrete"><?php if(isset($nfe_recebida) ){ echo $this->Number->currency($nfe_recebida->VALOR_FRETE,"BRL"); } ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label for="stcCNPJ">CNPJ</label>
                            <p class="form-control-static" id="stcCNPJ"><?php if(isset($nfe_recebida) ){ echo $nfe_recebida->CPFCNPJ_EMITENTE; } ?></p>
                        </div>
                        <div class="col-4">
                            <label for="stcRazao">Raz&atilde;o Social</label>
                            <p class="form-control-static" id="stcCNPJ"><?php if(isset($nfe_recebida) ){ echo $nfe_recebida->NOME_EMITENTE; } ?></p>
                        </div>
                        <div class="col-4">
                            <label for="stcRazao">Nome Fantasia</label>
                            <p class="form-control-static" id="stcCNPJ"><?php if(isset($nfe_recebida) ){ echo $nfe_recebida->FANTASIA_EMITENTE; } ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body" id="pnlItens">

                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            Adicionar para etiquetas <input type="checkbox" id="chkEtiqueta" name="chkEtiqueta" value="1">&nbsp;&nbsp;
            <button type="submit" id="btnProcess" name="btnProcess" class="btn btn-warning btn-sm"><i class="fas fa-random"></i> Processar</button>	
        </div>
    </div>
</form>



<?php $this->Dialog->product_multiple(); ?>

<?php $this->Dialog->product_single();?>

<script>
var cod_produto = null;
var nom_produto = null;
$(document).ready(function(){
    getBasket();
});

$(document).on("hidden.bs.modal","#modalProductMultiple",function(){
    getBasket();
});

function getBasket(){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/tributary/invoice_received_itens_get/")?>'+<?php echo $nfe_recebida->IDNFERECEBIDA;?>,
        success:function(data){
            $("#pnlItens").html(data);
        }
    });
}

function addTmpLink(cod_prod,nom_prod){
    cod_produto = cod_prod;
    nom_produto = nom_prod;
    
    $("#modalProductMultiple").modal({
        backdrop:'static'
    });
}

function addProduct(idProduto){

    var qtde = prompt("Que quantidade deste produto gostaria de adicionar?");
    
    var dataPost = {
        COD_PRODUTO : cod_produto,
        NOM_PRODUTO : nom_produto,
        ID_PRODUTO  : idProduto,
        TIP_VINCULO : 'T',
        QUANTIDADE  : qtde
    };

    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method:"post",
        data: dataPost,
        url: '<?=$this->Url->build("/tributary/invoice_received_link_add/")?>',
        success: function(data){
            if(data==false){
                bootbox.alert("Ocorreu um erro ao tentar adicionar o produto");
            }else{
                $("#lnk"+idProduto).addClass("disabled");
            }
        }
    });
}

function removeItem(idProduto){
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        url: '<?=$this->Url->build("/tributary/invoice_received_link_del/")?>'+idProduto,
        success: function(data){
            if(data==true){
                getBasket();
            }
        }
    });
}

function addPermLink(cod_prod,nom_prod){
    cod_produto = cod_prod;
    nom_produto = nom_prod;
    
    $("#modalProductSingle").modal({
        backdrop:'static'
    });
}

function useProduct(produto){
    var qtde = prompt("Qual a quantidade de destino?","1");
    
    var dataPost = {
        COD_PRODUTO : cod_produto,
        NOM_PRODUTO : nom_produto,
        ID_PRODUTO  : produto.IDPRODUTO,
        TIP_VINCULO : 'P',
        QUANTIDADE  : qtde
    };
    
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method:'post',
        data: dataPost,
        url:'<?=$this->Url->build("/tributary/invoice_received_link_add/")?>',
        success: function(data){
            if(data){
                getBasket();
            }
        }
    }); 
}
</script>