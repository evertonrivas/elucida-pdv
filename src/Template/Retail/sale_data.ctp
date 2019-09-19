<?php
// Change a template
$this->Paginator->meta(['prev' => false, 'next' => false]);
$this->Paginator->setTemplates([
	'number' 		=> '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
	'nextActive' 	=> '<li class="page-item"><a class="page-link" href="{{url}}" title="Pr&oacute;ximo">{{text}}</a></li>',
	'prevActive' 	=> '<li class="page-item"><a class="page-link" href="{{url}}" title="Anterior">{{text}}</a></li>',
	'nextDisabled' 	=> '<li class="page-item disabled"><a class="page-link" href="{{url}}">{{text}}</a></li>',
	'prevDisabled' 	=> '<li class="page-item disabled"><a class="page-link" href="{{url}}">{{text}}</a></li>',
	'current' 		=> '<li class="page-item active"><a class="page-link" href="">{{text}}</a></li>'
]);
?>
<table class='table table-hover' id='tblResult'>
    <thead>
        <tr>
            <?php if($IS_ADMIN):?>
            <th>Loja</th>
            <?php endif; ?>
            <th>Data</th>
            <th>Subtotal</th>
            <th>Desconto</th>
            <th>Valor Pago</th>
            <th>Troco</th>
            <th class='text-center last-column'>A&ccedil;&atilde;o</th>
        </tr>
    </thead>
    <tbody>
    <?php if($data_list->count()>0):?>
    <?php foreach($data_list as $data): ?>
        <tr>
            <?php if($IS_ADMIN):?>
                <td><?php echo $data['L']['NOME']; ?></td>
            <?php endif; ?>
            <td><?php echo $data->DATA_VENDA->format("d/m/Y"); ?></td>
            <td><?php echo $this->Number->currency($data->SUBTOTAL,"BRL"); ?></td>
            <td><?php echo $this->Number->currency($data->DESCONTO,"BRL"); ?></td>
            <td><?php echo $this->Number->currency($data->VALOR_PAGO,"BRL"); ?></td>
            <td><?php echo $this->Number->currency($data->TROCO,"BRL"); ?></td>
            <td class="last-column"><a href="javascript:openSale(<?php echo $data->IDVENDA; ?>)" class="btn btn-light btn-sm" id="btnFilter"><i class="fas fa-eye"></i> Exibir</a></td>
        </tr>
    <?php endforeach;?>
    <?php else:?>
        <tr><td colspan="<?php echo ($IS_ADMIN)?"7":"6"?>" class="text-center">Nenhum registro encontrado!</td></tr>
    <?php endif;?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="<?php echo ($IS_ADMIN)?"7":"6"?>">
            	<div class="row">
					<div class="col-sm">
						<?php
							echo $this->Paginator->counter(array(
							'format' => "P&aacute;gina {{page}} de {{pages}}, exibindo <strong>{{current}}</strong> registros do total de {{count}}"
							));
						?>
					</div>
					<div class="col-sm">
						<ul class="pagination justify-content-end">
							<?php echo $this->Paginator->prev('<<'); ?><?php echo $this->Paginator->numbers(); ?><?php echo $this->Paginator->next('>>'); ?>
						</ul>
					</div>
				</div>
            </td>
        </tr>
    </tfoot>
</table>
