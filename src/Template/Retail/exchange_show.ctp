<div class="container">
    <div class="alert alert-success" role="alert" id="alertSuccess"><strong>Sucesso!</strong> Troca estornada com sucesso!</div>
    <div class="alert alert-danger" role="alert" id="alertFail"><strong>Problema!</strong> Ocorreu um erro ao tentar estonar a troca!</div>
    <form name="frmRegs" id="frmRegs">
        <div class="panel panel-default drop-shadow">
            <div class="panel-heading clearfix">
                <h3 class="panel-title pull-left" style="padding-top: 7.5px;"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> Devolu&ccedil;&atilde;o</h3>
                <div class="pull-right">
                    <a href="javascript:window.print()" class="btn btn-default"><span class="glyphicon glyphicon-print"></span> Imprimir</a>
                    <button type="button" id="btnEstorno" name="btnEstorno" class="btn btn-warning<?php if($devolucao->UTILIZADO){ echo " disabled"; }?>"><span class="glyphicon glyphicon-off"></span> Estornar</button>
                </div>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-4">
                                    <label class="control-label">Loja</label>
                                    <p class="form-control-static"><?php echo $loja->NOME; ?></p>
                                </div>
                                <div class="col-xs-4">
                                    <label class="control-label">Data</label>
                                    <p class="form-control-static"><?php echo $devolucao->DATA_DEVOLUCAO->format("d/m/Y H:i:s"); ?></p>
                                </div>
                                <div class="col-xs-4">
                                    <label class="control-label">Total</label>
                                    <p class="form-control-static"><?php echo $this->Number->currency($devolucao->VALOR_TOTAL,"BRL"); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if($devolucao->OBSERVACAO!=""): ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php echo $devolucao->OBSERVACAO; ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($devolucao_itens!=NULL):?>
                                <?php foreach($devolucao_itens as $item):?>
                                <tr>
                                    <td><?php echo $item->IDPRODUTO; ?></td>
                                    <td><?php echo $item->NOME_PRODUTO; ?></td>
                                    <td><?php echo $item->QUANTIDADE; ?></td>
                                    <td><?php echo $this->Number->currency($item->PRECO_UNITARIO,"BRL"); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


<script>    
$(document).on("click", "#btnEstorno", function() {
    bootbox.dialog({message:"Deseja realmente estornar esta troca?", 
    buttons:{
        yes:{
            label:"Sim",
            callback:function(){
                $.ajax({
                    data:{ IDDEVOLUCAO: <?php echo $devolucao->IDDEVOLUCAO; ?> },
                    method:'post',
                    url: '<?=$this->Url->build("/retail/change_reverse/")?>',
                    success: function(data){
                        if(data==true){
                            $("#alertSuccess").show();
                            $("#alertSuccess").fadeTo(2500,500).slideDown(500,function(){
                                $("#alertSuccess").hide();
                                window.parent.document.location.reload();
                                $("#btnCloseModal",window.parent.document).click();
                            });
                        }else{
                             $("#alertFail").show();
                             $("#alertFail").fadeTo(2500,500).slideDown(500,function(){
                                 $("#alertFail").hide();
                             });
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
});
</script>