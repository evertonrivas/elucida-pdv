<?php foreach($produtos as $produto): ?>
<tr>
    <td><?php echo $produto->SKU; ?></td>
    <td><?php echo $produto->NOME; ?></td>
    <td><?php echo $this->Number->currency($produto->PRECO_VENDA,"BRL");?></td>
    <td><input type="radio" id="rdProduto[]" name="rdProduto[]" value="<?php echo $produto->IDPRODUTO; ?>"></td>
</tr>
<?php endforeach;