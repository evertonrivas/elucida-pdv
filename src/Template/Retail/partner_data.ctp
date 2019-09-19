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
            <th>#</th>
            <th>Nome</th>
            <th>Cupom</th>
            <th>Desconto</th>
            <th class='text-center last-column'>A&ccedil;&atilde;o</th>
        </tr>
    </thead>
    <tbody>
    <?php if($data_list->count()>0): ?>
    <?php foreach($data_list as $data): ?>
        <tr>
            <td><?php echo $data->IDPARCEIRO; ?></td>
            <td><?php echo $data->NOME; ?></td>
            <td><?php echo $data->CODIGO_CUPOM; ?></td>
            <td><?php echo $this->Number->toPercentage($data->PERC_DESCONTO,2); ?></td>
            <td class="last-column"><a href="<?php echo $this->Url->build('retail/partner_create/'.$data->IDPARCEIRO);?>" class="btn btn-light btn-sm" id="btnFilter"><span class="glyphicon glyphicon-pencil" aria-hidden="true" title="Editar"></span> Editar</a></td>
        </tr>
    <?php endforeach;?>
    <?php else: ?>
        <tr><td colspan="5" class="text-center">Nenhum registro encontrado!</td></tr>
    <?php endif;?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5">
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