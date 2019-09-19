<table class='table'>
    <thead>
        <tr>
            <th>#</th>
            <th>Produto</th>
            <th>Pode Enviar</th>
            <th>Deseja enviar</th>
            <th>A&ccedil;&atilde;o</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($transfer_itens as $item): ?>
        <?php $value_transfer = ($item->QUANTIDADE=="")?0:$item->QUANTIDADE; ?>
        <tr>
            <td><?php echo $item->SKU_PRODUTO;?></td>
            <td><?php echo $item->NOME_PRODUTO;?></td>
            <td style='max-width:80px!important'><?php echo $item->DISPONIVEL_TRANSFER; ?></td>
            <td><input type='number' id='txtQuantidade[]' name='txtQuantidade[]' class='form-control' value='<?php echo $value_transfer;?>' max='<?php (($item->MAX_DESTINO>0)?$item->MAX_DESTINO:$item->DISPONIVEL_TRANSFER);?>' min='0'><input type='hidden' id='txtIdProduto[]' name='txtIdProduto[]' value='<?php echo $item->IDPRODUTO; ?>'></td>
            <td><a href="javascript:removeItem(<?php echo $item->IDPRODUTO?>);" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-remove"></span></a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>