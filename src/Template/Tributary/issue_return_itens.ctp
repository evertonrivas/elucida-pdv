<table class="table table-striped">
	<thead>
		<tr>
			<th>#</th>
			<th>C&oacute;d.</th>
			<th>Produto</th>
			<th>Qtde p/ Devolu&ccedil;&atilde;o</th>
			<th>Valor Unit&aacute;rio</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($item_list as $item):?>
		<tr>
			<td><input type="checkbox" id="chkItemDevolve_<?=$item->IDITEM;?>" name="chkItemDevolve[]" value="<?=$item->IDITEM?>" onclick="toggleItem(<?=$item->IDITEM;?>)"/></td>
			<td><?=$item->COD_PRODUTO?></td>
			<td><?=$item->NOME_PRODUTO?></td>
			<td><input type="number" id="txtQtdeDevolve_<?=$item->IDITEM;?>" name="txtQtdeDevolve[]" value="<?=$item->QUANTIDADE_COMERCIAL?>" class="form-control form-control-sm text-right" max="<?=$item->QUANTIDADE_COMERCIAL?>" min="1" disabled=""/></td>
			<td><?=$this->Number->currency($item->VALOR_UNITARIO,"BRL");?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>