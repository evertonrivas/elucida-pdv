<?php $time = new Cake\I18n\Time();?>
<div class="container">
<form class="form">
    <div class="panel panel-default drop-shadow">
        <div class="panel-heading clearfix">
            <h3 class="panel-title pull-left" style="padding-top: 7.5px;"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> Visualiza&ccedil;&atilde;o de Transfer&ecirc;ncia</h3>
            <div class="pull-right">
                <a href="javascript:window.print()" class="btn btn-default"><span class="glyphicon glyphicon-print"></span> Imprimir</a>
            </div>
        </div>
        <div class="panel-body">
            <fieldset>
                <legend>Dados da Transfer&ecirc;ncia</legend>
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">Identifica&ccedil;&atilde;o</label>
                            <p class="form-control-static"><?php echo $transfer->NOME; ?></p>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-6">
                            <label class="control-label">Estoque de Origem</label>
                            <p class="form-control-static"><?php echo $origem->NOME; ?></p>
                        </div>
                        <div class="col-xs-6">
                            <label class="control-label">Estoque de Destino</label>
                            <p class="form-control-static"><?php echo $destino->NOME; ?></p>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-6">
                            <label class="control-label">Data de Cria&ccedil;&atilde;o</label>
                            <p class="form-control-static"><?php echo $time->parseDate($transfer->DATA_CRIACAO)->i18nFormat("dd/MM/yyyy"); ?></p>
                        </div>
                        <div class="col-xs-6">
                            <label class="control-label">Data de Recep&ccedil;&atilde;o</label>
                            <p class="form-control-static"><?php if($transfer->DATA_EXECUCAO!=NULL){ echo $time->parseDate($transfer->DATA_CRIACAO)->i18nFormat("dd/MM/yyyy"); }else{ echo "Em andamento"; } ?></p>
                        </div>
                    </div>
                </div>
            </fieldset><br/>
            <fieldset>
                <legend>Produto(s)</legend>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Produto</th>
                            <th>Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($transfer_itens as $ITEM): ?>
                            <tr>
                                <td><?php echo $ITEM->SKU_PRODUTO; ?></td>
                                <td><?php echo $ITEM->NOME_PRODUTO; ?></td>
                                <td><?php echo $ITEM->QUANTIDADE; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
</form>
</div>