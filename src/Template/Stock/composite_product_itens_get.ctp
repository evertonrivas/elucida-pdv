<table class='table table-striped'>
	<thead>
	<tr>
	    <th>SKU</th>
	    <th>Produto</th>
	    <th>Pre&ccedil;o Venda</th>
	    <th>A&ccedil;&atilde;o</th>
	</tr>
	</thead>
	<tbody>
	    <?php foreach($itens as $item):?>
	    <tr>
	        <td><?php echo $item->SKU_PRODUTO;?></td>
	        <td><?php echo $this->Text->truncate($item->NOME_PRODUTO,20,['ellipsis' => '...','exact' => false]);?></td>
	        <td><?php echo $this->Number->currency($item->PRECO_VENDA,'BRL');?></td>
	        <td><a href="javascript:removeItem(<?php echo $item->IDPRODUTO?>);" class="btn btn-danger btn-sm"><i class="fas fa-times"></i></a></td>
	    </tr>
	    <?php endforeach; ?>
	</tbody>
</table>