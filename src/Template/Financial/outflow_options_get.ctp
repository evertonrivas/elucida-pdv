<table class="table table-striped">
    <tbody>
        <?php foreach($operacoes as $operacao): ?>
        <tr>
        <?php if(!$operacao->UTILIZADA): ?>       
            <td><a href='javascript:addOption("<?php echo $operacao->IDTIPODESPESA;?>")' class='btn btn-success btn-sm'><i class="fas fa-plus-square"></i></a></td>
        <?php else: ?>
            <td><a href='javascript:delOption("<?php echo $operacao->IDTIPODESPESA;?>")' class="btn btn-danger btn-sm"><i class="fas fa-minus-square"></i></a></td>
        <?php endif; ?>
            <td><?php echo $operacao->NOME; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>