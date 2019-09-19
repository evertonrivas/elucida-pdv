<div class="text-right"><small>Subtotal: <strong><?=$this->Number->currency($totais->SUBTOTAL,"BRL");?></strong></small></div>
<?php if($totais->DESCONTO > 0):?>
    <div class="text-right"><small><span class="text-danger">Desconto: <strong><?=$this->Number->currency($totais->DESCONTO,"BRL");?></strong></span></small></div>
<?php endif;?>
<?php if($cupom):
    if($cupom->TIPO_VALOR=="$"):?>
        <div class="text-right"><small><span class="text-warning">Cupom: <strong><?=$this->Number->currency( $cupom->VALOR,"BRL" );?></strong></span></small></div>
        <div class="text-right"><small>Total: <strong><?=$this->Number->currency( round($totais->SUBTOTAL-$totais->DESCONTO+$cupom->VALOR,1),"BRL");?></strong></small></div>
    <?php else: ?>
        <div class="text-right"><small><span class="text-warning">Cupom: <strong><?=$cupom->VALOR?> %</strong></span></small></div>
        <div class="text-right"><small>Total: <strong><?=$this->Number->currency( round($totais->SUBTOTAL-($totais->SUBTOTAL*($cupom->VALOR/100)),1),"BRL");?></strong></small></div>
    <?php endif; ?>
<?php else: ?>
    <div class="text-right"><small><span class="text-warning">Cupom: <strong><?=$this->Number->currency( 0,"BRL");?></strong></span></div><div class="text-right">Total: <strong><?=$this->Number->currency($totais->SUBTOTAL-$totais->DESCONTO+0,"BRL");?></strong></small></div>
<?php endif; ?>

    <?php 
    if($totais->DESCONTO==0): ?>
        <div class="text-right">&nbsp;</div>
    <?php endif;?>
    <input type="hidden" id="txtDesconto" value="<?=$totais->DESCONTO;?>"/>
    <input type="hidden" id="txtSubtotal" value="<?=$totais->SUBTOTAL;?>"/>
    
    <!--este campo ajuda a verificar se a compra possui algum cupom nao remover-->
    <input type="hidden" id="txtTroca" value="<?=(($cupom!=null)?$cupom->IDCUPOM:"")?>"/>