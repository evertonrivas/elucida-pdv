<table class="table table-striped">
    <tbody>
        <?php foreach($operacoes as $operacao): ?>
        <tr>
        <?php if(!$operacao->UTILIZADA): ?>       
            <td><a href='javascript:addAccount("<?php echo $operacao->IDOPERACAOFINANCEIRA;?>")' class='btn btn-success btn-sm'><i class="fas fa-plus-square"></i></a></td>
        <?php else: ?>
            <td><a href='javascript:delAccount("<?php echo $operacao->IDOPERACAOFINANCEIRA;?>")' class="btn btn-danger btn-sm"><i class="fas fa-minus-square"></i></a></td>
        <?php endif; ?>
            <td><?php echo $operacao->NOME; ?></td>
            <td><?php if($operacao->TIPO_OPERACAO=='E'){ echo "Entrada"; }else{ echo "Sa&iacute;da"; }?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>