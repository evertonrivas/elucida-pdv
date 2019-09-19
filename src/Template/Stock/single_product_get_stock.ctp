<table class='table table-hover'>
    <thead>
        <tr>
            <th>Loja</th>
            <th>Data de Cadastro</th>
            <th>Dispon&iacute;vel</th>
            <th>&Uacute;ltima Compra</th>
            <th>&Uacute;ltima Venda</th>
        </tr>
    </thead>
    <tbody>
    	<?php if($estoque->count()>0):?>
        <?php foreach($estoques as $estoque): ?>
        <tr>
            <td><?=$estoque->LOJA; ?></td>
            <td><?=$estoque->DATA_ENTRADA->format("d/m/Y")?></td>
            <td><?=$estoque->QUANTIDADE;?></td>
            <td><?=($estoque->DATA_ULTIMA_COMPRA!=null)?$estoque->DATA_ULTIMA_COMPRA->format("d/m/Y"):"";?></td>
            <td><?=($estoque->DATA_ULTIMA_VENDA!=null)?$estoque->DATA_ULTIMA_VENDA->format("d/m/Y"):"";?></td>
        </tr>
        <?php endforeach; ?>
        <?php else:?>
        <tr>
        	<td colspan="5" class="text-center">Nenhum registro encontrado!</td>
        </tr>
        <?php endif; ?>
</tbody>
</table>