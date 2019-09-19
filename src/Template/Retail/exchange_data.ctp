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
            <th>#</th>
            <th>Data</th>
            <th>Status</th>
            <th>Valor Total</th>
            <th class='text-center last-column'>A&ccedil;&atilde;o</th>
        </tr>
    </thead>
    <tbody>
    <?php if($data_list->count()>0): ?>
    <?php foreach($data_list as $data): ?>
        <tr>
            <?php if($IS_ADMIN):?>
                <td><?php echo $data->LOJA; ?></td>
            <?php endif; ?>
            <td><?php echo $data->IDDEVOLUCAO; ?></td>
            <td><?php echo $data->DATA_DEVOLUCAO->format("d/m/Y H:i:s"); ?></td>
            <td><?php echo ($data->UTILIZADO==0)?"N&atilde;o Utilizada":"Utilizada"; ?></td>
            <td><?php echo $this->Number->currency($data->VALOR_TOTAL,"BRL"); ?></td>
            <td class="last-column"><a href="javascript:openChange(<?php echo $data->IDDEVOLUCAO;?>)" class="btn btn-default btn-sm" id="btnShow"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Exibir</a></td>
        </tr>
    <?php endforeach;?>
    <?php else: ?>
        <tr><td colspan="<?php echo ($IS_ADMIN)?"7":"6"; ?>" class="text-center">Nenhum registro encontrado!</td></tr>
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