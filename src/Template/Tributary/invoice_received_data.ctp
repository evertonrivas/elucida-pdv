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
            <th class='first-column'><input type='checkbox' id='chCheckAll' name='chCheckAll'></th>
            <th>N&uacute;mero</th>
            <th>Emitente</th>
            <th>Data de Emiss&atilde;o</th>
            <th>Valor da Nota</th>
            <th class='text-center last-column'>A&ccedil;&atilde;o</th>
        </tr>
    </thead>
    <tbody>
    <?php if($invoice_list->count()>0):?>
    <?php foreach($invoice_list as $invoice): ?>
        <tr>
            <td><input type='checkbox' id='check_list[]' name='check_list[]' value="<?= $invoice->IDNFERECEBIDA; ?>"></td>
            <td><?= $invoice->NUMERO; ?></td>
            <td><?= $invoice->FANTASIA_EMITENTE; ?></td>
            <td><?= $invoice->DATA_EMISSAO->format("d/m/Y H:i:s"); ?></td>
            <td><?= $this->Number->currency($invoice->VALOR_NOTA,"BRL"); ?></td>
            <td><?php if(!$to_process):?>
                <a href="javascript:showDanfe('<?php echo $invoice->IDNFERECEBIDA?>')" class="btn btn-light btn-sm" id="btnView"><i class="fas fa-eye"></i> Exibir</a>
                <?php else: ?>
                <a href="<?php echo $this->Url->build("/tributary/invoice_received_prepare_to_process/".$invoice->IDNFERECEBIDA); ?>" class="btn btn-warning btn-sm" id="btnFilter"><i class="fas fa-random"></i> Proessar</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach;?>
    <?php else: ?>
        <tr><td colspan="6" class="text-center">Nenhum registro encontrado!</td></tr>
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