<div class="btn-group btn-group-justified" role="group">
<?php $i=1; foreach($simple_products as $simple_product):?>
	<div class="btn-group" role="group">
	<button type="button" class="btn btn-default" onclick="javascript:productBasketAdd('<?=$simple_product->CODIGO_BARRA;?>','1');" style="white-space: normal;min-height: 175px!important;">
		<img src='/img/<?=(($simple_product->IMAGEM!="")?$simple_product->IMAGEM:"spacer.gif");?>' style="max-height: 80px!important;max-width: 80px!important;"><br/>
		<small><strong><?=$simple_product->NOME;?></strong><br/>Dispon&iacute;vel: <?=$simple_product->QUANTIDADE;?></small>
	</button>
	</div>
	<?php if($i%3==0){ echo "</div><div class='btn-group btn-group-justified' role='group'>"; }?>
<?php $i++; endforeach; ?>

<?php foreach($complex_products as $complex_product):?>
	<div class="btn-group" role="group">
	<button type="button" class="btn btn-default" onclick="javascript:productBasketAdd('<?=$complex_product->CODIGO_BARRA;?>','1');" style="white-space: normal;min-height: 175px!important;">
		<img src='/img/<?=(($complex_product->IMAGEM!="")?$complex_product->IMAGEM:"spacer.gif");?>' style="max-height: 80px!important;max-width: 80px!important;"><br/>
		<small><strong><?=$complex_product->NOME;?></strong></small>
	</button>
	</div>
	<?php if($i%3==0){ echo "</div><div class='btn-group btn-group-justified' role='group'>"; }?>
<?php $i++; endforeach; ?>
</div>

<!--<table class='table table-hover'>
    <tbody>
        <?php foreach($simple_products as $simple_product):?>
        <tr>
            <td><a href=''><img src='/img/produto/<?=(($simple_product->IMAGEM_NOME!="")?$simple_product->IMAGEM_NOME:"spacer.gif");?>' style="width:80px; height:80px"></a></td>
            <td><small><a href='javascript:productBasketAdd("<?=$simple_product->CODIGO_BARRA;?>",1)' style='color:#000!important;text-decoration:none!important;'><strong><?=$simple_product->NOME;?></strong><br/>Dispon&iacute;vel: <?=$simple_product['E']['QUANTIDADE'];?></small></a></td>
        </tr>
        <?php endforeach; ?>
        
        <?php foreach($complex_products as $complex_product):?>
        <tr>
            <td><a href='javascript:addProduct("<?=$complex_product->CODIGO_BARRA;?>","1","");'><img src='/img/produto/<?=(($complex_product->IMAGEM_NOME!="")?$complex_product->IMAGEM_NOME:"spacer.gif");?>' style="width:80px; height:80px"></a></td>
            <td><small><a href='javascript:productBasketAdd("<?=$complex_product->CODIGO_BARRA;?>",1)' style='color:#000!important;text-decoration:none!important;'><strong><?=$complex_product->NOME;?></strong><br/>Dispon&iacute;vel: 0</small></a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
-->