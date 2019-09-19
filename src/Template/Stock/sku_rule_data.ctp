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
            <th>Tipo de Produto</th>
            <th>Formato</th>
            <th class='text-center last-column'>A&ccedil;&atilde;o</th>
        </tr>
    </thead>
    <tbody>
    <?php if($sku_rule_list->count()>0):?>
    <?php foreach($sku_rule_list as $sku_rule): ?>        
        <tr>
            <td><input type='checkbox' id='check_list[]' name='check_list[]' value="<?= $sku_rule->IDREGRASKU; ?>"></td>
            <td><?= $sku_rule->DESCRICAO_REGRA; ?></td>
            <td><?= $sku_rule->FORMATO_REGRA; ?></td>
            <td><a href="<?php echo $this->Url->build('stock/sku_rule_create/'.$sku_rule->IDREGRASKU.'/'.$sku_rule->IDPRODUTOTIPO);?>" class="btn btn-light btn-sm" id="btnEdit"><i class="fas fa-edit"></i> Editar</a></td>
        </tr>
    <?php endforeach;?>
    <?php else: ?>
        <tr><td colspan="4" class="text-center">Nenhum registro encontrado!</td></tr>
    <?php endif;?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">
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