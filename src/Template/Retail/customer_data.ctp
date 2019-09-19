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
            <th>#</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th class='text-center last-column'>A&ccedil;&atilde;o</th>
        </tr>
    </thead>
    <tbody>
    <?php if($data_list->count()>0):?>
    <?php foreach($data_list as $data): ?>
        <tr>
            <td><input type='checkbox' id='check_list[]' name='check_list[]' value="<?= $data->IDCLIENTE; ?>"></td>
            <td><?= $data->IDCLIENTE;?></td>
            <td><?= $data->NOME; ?></td>
            <td><?= $this->Mask->apply($data->CPF,"###.###.###-##"); ?></td>
            <td><a href="mailto:<?php echo $data->EMAIL;?>"><?php echo $data->EMAIL; ?></a></td>
            <td><?php echo $data->TELEFONE; ?></td>
            <td class="text-center">
                <a href="<?php echo $this->Url->build('/retail/customer_create/'.$data->IDCLIENTE);?>" class="btn btn-outline-secondary btn-sm" id="btnFilter" title="Editar"><i class="fas fa-edit"></i></a>
                <?php if($user['role']=="admin"):?>
                <a href="javascript:importOrigin(<?=$data->IDCLIENTE; ?>)" class="btn btn-outline-secondary btn-sm<?php if($data->CODIBGE!="" || $data->CEP==""){ echo " disabled"; }?>" id="lnkOrigin<?=$data->IDCLIENTE;?>" title="Buscar Origem"><i class="fas fa-map-marker-alt"></i></a>
                <?php endif;?>
            </td>
        </tr>
    <?php endforeach;?>
    <?php else:?>
        <tr><td colspan="7" class="text-center">Nenhum registro encontrado!</td></tr>
    <?php endif;?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">
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