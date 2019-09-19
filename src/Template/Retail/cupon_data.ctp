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
            <th>Nome</th>
            <th>Data de Cria&ccedil;&atilde;o</th>
            <th>Status</th>
            <th>Tipo</th>
            <th>C&oacute;digo</th>
            <th>Valor</th>
            <th class='text-center last-column'>A&ccedil;&atilde;o</th>
        </tr>
    </thead>
    <tbody>
    <?php if($data_list->count()>0):?>
    <?php foreach($data_list as $data): ?>
        <tr>
            <td class="first-column"><input type="checkbox" id="check_list[]" name="check_list[]" value="<?php echo $data->IDCUPOM; ?>"></td>
            <td><?php echo $data->DESCRICAO; ?></td>
            <td><?php echo ($data->DATA_CRIACAO!="")?$data->DATA_CRIACAO->format("d/m/Y"):""; ?></td>
            <td><?php echo (($data->UTILIZADO=="S")?"Utilizado":(($data->UTILIZADO=="N")?"N&atilde;o Utilizado":(($data->UTILIZADO=="E")?"Expirado":"Indeterminado"))); ?></td>
            <td><?php if($data->TIPO_CUPOM=="A"){ echo "Pedido de Compra"; }elseif($data->TIPO_CUPOM=="D"){ echo "Desconto"; }else{ echo "Vale Presente"; }; ?></td>
            <td><?php echo $data->CODIGO; ?></td>
            <td><?php if($data->TIPO_VALOR=="$"){ echo $this->Number->currency($data->VALOR,"BRL"); }else{ echo $data->VALOR."%"; } ?></td>
            <td class="last-column"><a href="javascript:showCupom(<?php echo $data->IDCUPOM;?>)" class="btn btn-light btn-sm" id="btnView"><i class="fas fa-eye"></i> Exibir</a></td>
        </tr>
    <?php endforeach;?>
    <?php else: ?>
        <tr><td colspan="8" class="text-center">Nenhum registro encontrado!</td></tr>
    <?php endif;?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8">
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