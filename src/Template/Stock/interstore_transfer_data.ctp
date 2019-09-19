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
?><form id="frmListTransfer">
    <table class='table table-hover' id='tblResult'>
        <thead>
            <tr>
                <th class="first-column"><input type="checkbox" id="chCheckAll" name="chCheckAll"></th>
                <th>Identifica&ccedil;&atilde;o</th>
                <th>Origem</th>
                <th>Destino</th>
                <th>Data De Cria&ccedil;&atilde;o</th>
                <th>Data De Validade</th>
                <th>Status</th>
                <th class="text-center last-column" style="width: 100px;">A&ccedil;&atilde;o</th>
            </tr>
        </thead>
        <tbody>
            <?php if($transfer_list->count()>0):?>
            <?php foreach ($transfer_list as $transfer) : ?>
            <tr>
                <td class="first-column"><input type="checkbox" id="check_list[]" name="check_list[]" value="<?php echo $transfer->IDTRANSFERENCIA; ?>"<?php if($transfer->STATUS == "C" || $transfer->STATUS == "F"){ echo " disabled='disabled'"; }?>></td>
                <td><?php echo $transfer->NOME; ?></td>
                <td><?php echo $transfer->ORIGEM; ?></td>
                <td><?php echo $transfer->DESTINO; ?></td>
                <td><?php echo ($transfer->DATA_CRIACAO!=null)?$transfer->DATA_CRIACAO->format("d/m/Y"):""; ?></td>
                <td><?php echo ($transfer->DATA_VALIDADE!=null)?$transfer->DATA_VALIDADE->format("d/m/Y"):""; ?></td>
                <td><?php switch($transfer->STATUS){
                    case 'C': echo "Cancelada"; break;
                    case 'F': echo "Finalizada"; break;
                    case 'P': echo "Pendente"; break;
                }
                ?></td>
                <td class="last-column">
                    <?php if($transfer->STATUS=="C" || $transfer->STATUS=="F"): ?>
                    <a href="javascript:showTransfer(<?php echo $transfer->IDTRANSFERENCIA; ?>)" class="btn btn-default btn-sm" id="btnFilter"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Exibir</a>
                    <?php else:?>
                    <a href="<?php echo $this->Url->build('/stock/stock_transfer_create/'.$transfer->IDTRANSFERENCIA);?>" class="btn btn-light btn-sm" id="btnFilter"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editar</a>
                    <?php endif;?>
                </td>
            </tr>
            <?php endforeach; ?>
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
</form>