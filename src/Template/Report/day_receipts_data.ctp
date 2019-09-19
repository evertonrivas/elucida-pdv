<?php $total_vendido = 0; ?>
<table class='table table-striped'>
    <thead>
        <tr>
            <th>Condi&ccedil;&atilde;o de Pagamento</th>
            <th class='text-right'>Valor</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data_list as $venda): ?>
            <tr>
                <td><?=$venda->CONDICAO_PAGAMENTO;?></td>
                <td class='text-right'><?=$this->Number->currency($venda->TOTAL,"BRL");?></td>
            </tr>
            <?php $total_vendido += $venda->TOTAL;
                    
            endforeach; ?>
            <tr class="table-active">
                <td><strong>Total</strong></td>
                <td class='text-right'><strong><?=$this->Number->currency($total_vendido,"BRL");?></strong></td>
            </tr>
    </tbody>
</table>