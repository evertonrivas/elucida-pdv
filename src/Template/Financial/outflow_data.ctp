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
            <th>Exibir</th>
            <th>Opera&ccedil;&atilde;o Financeira</th>
        </tr>
    </thead>
    <tbody>
    <?php if($data_list->count()>0):?>
    <?php foreach($data_list as $data): ?>
        <tr>
            <td class="first-column"><input type="checkbox" id="check_list[]" name="check_list[]" value="<?php echo $data->IDOPERACAOFINANCEIRA; ?>"></td>
            <td><a href="#row_<?=$data->IDOPERACAOFINANCEIRA?>" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="row_<?=$data->IDOPERACAOFINANCEIRA?>"><i class="fas fa-plus-circle text-success"></i></a></td>
            <td><?php echo $data->OPERACAO_FINANCEIRA; ?></td>
        </tr>
        <?php foreach($expenses as $expense):?>
        <tr class="collapse multi-collapse" id="row_<?=$data->IDOPERACAOFINANCEIRA?>">
        	<td colspan="3">
        		<table class="table table-striped">
        			<thead>
        				<tr>
        					<th>Tipo de Despesa</th>
        				</tr>
        			</thead>
        			<tbody>
        				<?php foreach($expenses as $expense): ?>
        				<?php if($expense->IDOPERACAOFINANCEIRA==$data->IDOPERACAOFINANCEIRA):?>
        				<tr>
        					<td><?=$expense->TIPO_DESPESA;?></td>
        				</tr>
        				<?php endif;?>
        				<?php endforeach; ?>
        			</tbody>
        		</table>
        	</td>
        </tr>
        <?php endforeach;?>
    <?php endforeach;?>
    <?php else: ?>
        <tr><td colspan="3" class="text-center">Nenhum registro encontrado!</td></tr>
    <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">
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