<table class='table'>
    <thead>
        <tr>
            <th>Cond. de Pagamento</th>
            <th>Valor</th>
            <th>Parcela</th>
            <th>#</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($payments as $payment): ?>
        <tr>
            <td><?=$payment->CONDICAO_PAGAMENTO;?></td>
            <td><?=$this->Number->currency($payment->VALOR_PAGO,"BRL");?></td>
            <td><?=$this->Number->currency($payment->VALOR_PARCELA,"BRL");?></td>
            <td><a href="javascript:paymentDel('<?=$payment->IDCONDICAOPAGAMENTO;?>')"><i class="fas fa-ban text-danger"></i></a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>