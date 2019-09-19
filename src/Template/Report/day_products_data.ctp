<?php $total = 0; ?>
<table class='table table-striped'>
    <thead>
        <tr>
            <th>Produto</th>
            <th class='text-right'>Quantidade</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data_list as $data): ?>
            <tr>
                <td><?=$data->NOME_PRODUTO;?></td>
                <td class='text-right'><?=$data->TOTAL;?></td>
            </tr>
            <?php $total += $data->TOTAL;
                    
            endforeach; ?>
            <tr class="table-active">
                <td><strong>Total</strong></td>
                <td class='text-right'><strong><?=$total;?></strong></td>
            </tr>
    </tbody>
</table>