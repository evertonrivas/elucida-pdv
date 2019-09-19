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
            <th>Data</th>
            <th>Cliente</th>
            <th>Telefone</th>
            <th>Desejo</th>
            <th>Modo Contato</th>
            <th class='text-center last-column'>A&ccedil;&atilde;o</th>
        </tr>
    </thead>
    <tbody>
    <?php if($data_list->count()>0):?>
    <?php foreach($data_list as $data): ?>
        <?php if($data->ATENDIDO==1){ $start_atendido="<span class='text-warning'>"; $end_atendido = "</span>"; }elseif($data->ATENDIDO==2){ $start_atendido="<span class='text-danger'>"; $end_atendido = "</span>"; }else{ $start_atendido=""; $end_atendido=""; }?>
        <?php switch($data->FORMA_CONTATO){ case 'T': $forma_contato = 'Todas'; break; case 'E': $forma_contato = 'E-mail'; break; case 'M': $forma_contato = 'SMS/WhatsApp'; break; case 'L': $forma_contato = 'Liga&ccedil;&atilde;o'; break; }?>
        <tr>
            <td><input type='checkbox' id='check_list[]' name='check_list[]' value="<?= $data->IDSOLICITACAO; ?>"></td>
            <td><?= $start_atendido.($data->DATA_SOLICITACAO!=NULL)?$data->DATA_SOLICITACAO->format("d/m/Y"):"".$end_atendido; ?></td>
            <td>
                <?php if($data['C']['EMAIL']!=""): ?>
                <a href="javascript:sendRequest(<?php echo $data->IDSOLICITACAO;?>)">
                <?php endif; ?>
                    <?php echo $start_atendido.$data['C']['NOME'].$end_atendido; ?>
                <?php if($data->EMAIL!=""): ?>
                </a>
                <?php endif; ?>
            </td>
            <td><?= $start_atendido.$data['C']['TELEFONE'].$end_atendido; ?></td>
            <td><?= $start_atendido.$data->DESEJO.$end_atendido; ?></td>
            <td><?= $start_atendido.$forma_contato.$end_atendido; ?></td>
            <td><a href="<?php echo $this->Url->build('/retail/request_create/'.$data->IDSOLICITACAO);?>" class="btn btn-default btn-sm" id="btnFilter"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a></td>
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