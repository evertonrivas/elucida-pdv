<?php if($produtos->count()>0): ?>
<?php foreach($produtos as $produto): ?>
<tr>
    <td><?php echo $produto->SKU; ?></td>
    <td><?php echo $produto->NOME; ?></td>
    <td><a href="javascript:addProduct('<?=$produto->IDPRODUTO;?>')" class="btn btn-success" id="lnk<?=$produto->IDPRODUTO;?>"><i class="fas fa-plus"></i></a></td>
</tr>
<?php endforeach;?>
<?php else: ?>
	<tr>
		<td colspan="4" class="text-center">Nenhum registro encontrado!</td>
	</tr>
<?php endif;?>