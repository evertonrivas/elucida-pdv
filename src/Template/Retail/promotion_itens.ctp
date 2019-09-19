<input type="hidden" id="txtTotalItens" name="txtTotalItens" value='<?=$itens->count();?>'/>
<table class='table'>
    <thead>
        <tr>
            <th>#</th>
            <th>Produto</th>
            <th>Pre&ccedil;o Venda</th>
            <th>Pre&ccedil;o Promocional</th>
            <th>Cond. Pagamento</th>
            <th>A&ccedil;&atilde;o</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($itens as $item):?>
        <tr>
            <th role='row'><?=$item->SKU;?><input type='hidden' name='txtIdProduto[]' id='txtIdProduto[]' value='<?=$item->IDPRODUTO;?>'></th>
            <td><?=$item->NOME;?></td>
            <td><?=$this->Number->currency($item->PRECO_VENDA,"BRL");?></td>
            <td style='max-width:100px!important'><?=$this->Number->currency($item->PRECO_PROMO,"BRL");?>
            <input type='hidden' class='form-control text-right' name='txtPrecoPromo[]' id='txtPrecoPromo[]' value='<?=(($item->PRECO_PROMO!="")?$item->PRECO_PROMO:"");?>'>
            </td>
            <td><input type='hidden' id='txtCondPagamento[]' name='txtCondPagamento[]' value='<?=$item->IDCONDICAOPAGAMENTO;?>'><?=$item->CONDICAO_PAGAMENTO?></td>
            <td><a href='javascript:removeProduct("<?=$item->IDPRODUTO;?>","<?=$item->IDCONDICAOPAGAMENTO;?>")' class='btn btn-danger' id='lnk<?=$item->IDPRODUTO;?>'><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>
            <tr>
    <?php endforeach; ?>
    </tbody>
</table>