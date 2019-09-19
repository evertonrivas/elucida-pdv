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
            <td>&nbsp;</td>
        </tr>
        <?php 
        endforeach; ?>
        <?php else:?>
        <tr>
        	<td colspan="3" class="text-center">Nenhum registro encontrado!</td>
        </tr>
        <?php endif;?>
    </tbody>
</table>