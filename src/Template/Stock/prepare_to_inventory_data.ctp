<form id="frmInventory" action="/stock/validate_inventory" method="post">
<input type="hidden" name="_csrfToken" id="_csrfToken" value=<?= json_encode($this->request->getParam('_csrfToken')) ?>/>
<input type="hidden" id="IDLOJA" name="IDLOJA" value="<?=$IDLOJA?>"/>
<input type="hidden" id="IDFORNECEDOR" name="IDFORNECEDOR" value="<?=$IDFORNECEDOR;?>"/>
<input type="hidden" id="IDPRODUTOTIPO" name="IDPRODUTOTIPO" value="<?=$IDPRODUTOTIPO?>"/>
<table class="table table-striped">
    <thead>
        <tr>
        	<th>SKU</th>
            <th>Produto</th>
            <th>Quantidade</th>
        </tr>
    </thead>
    <tbody>
    <?php if($data_list->count()>0): ?>
    <?php foreach($data_list as $inventario): ?>
    	<input type="hidden" id="txtIdProduto[]" name="txtIdProduto[]" value="<?=$inventario->IDPRODUTO?>">
        <tr>
        	<td><?=$inventario->SKU?></td>
            <td><?=$inventario->NOME?></td>
            <td><input type="number" id="txtQuantity[]" name="txtQuantity[]"></td>
        </tr>
        <?php 
        endforeach; ?>
        <?php else:?>
        <tr>
        	<td colspan="3" class="text-center">Nenhum registro encontrado!</td>
        </tr>
        <?php endif;?>
    </tbody>
    <tfoot>
    	<tr>
    		<td colspan="3" class="text-right"><button type="submit" id="btnSend" name="btnSend" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Validar</button></td>
    	</tr>
    </tfoot>
</table>
</form>