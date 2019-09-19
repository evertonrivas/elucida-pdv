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
<table class='table table-striped' id='tblResult'>
    <thead>
        <tr>
            <th>Data do Movimento</th>
            <th>N&uacute;m. do Documento</th>
            <th>Hist&oacute;rico</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
    <?php if($data_list->count()>0):?>
    <?php foreach($data_list as $data): ?>
        <tr>
            <td><?php echo $data->DATA_MOVIMENTO->format("d/m/Y"); ?></td>
            <td><?php echo $data->NUM_DOCUMENTO; ?></td>
            <td><?php echo $data->HISTORICO; ?></td>
            <td class="text-right"><?php echo $this->Number->currency($data->VALOR,"BRL"); ?></td>
        </tr>
    <?php endforeach;?>
    <?php else: ?>
        <tr><td colspan="4" class="text-center">Nenhum registro encontrado!</td></tr>
    <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6">
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