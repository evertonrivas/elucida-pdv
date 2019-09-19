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
            <th class="first-column">#</th>
            <th>Loja</th>
            <th>Nome</th>
            <th>In&iacute;cio</th>
            <th>Fim</th>
            <th class="text-center last-column">A&ccedil;&atilde;o</th>
        </tr>
    </thead>
    <tbody>
    <?php if($data_list->count()>0):?>
    <?php foreach($data_list as $promo): ?>
        <?php
            //calcula se a promocao eh valida
            $data_fim_promo = new \DateTime($promo->DATA_FINAL->format("Y-m-d"));
            $data_fim_promo->setTime(0, 0, 0);

            $data_hoje = new \DateTime(date("Y-m-d"));
            $interval = $data_fim_promo->diff($data_hoje);
            $totalDiff = $interval->format("%R%a");

            if($totalDiff>0){
                $abre = "<del>"; $fecha = "</del>";
            }else{
                $abre = ""; $fecha = "";
            }
        ?>
        <tr>
            <th role="row"><?php echo $promo->IDPROMOCAO; ?></th>
            <td><?php echo $abre.$promo['L']['NOME'].$fecha; ?></td>
            <td><?php echo $abre.$promo->NOME.$fecha; ?></td>
            <td><?php echo $abre.$promo->DATA_INICIAL->format("d/m/Y").$fecha; ?></td>
            <td><?php echo $abre.$promo->DATA_FINAL->format("d/m/Y").$fecha; ?></td>
            <td class="last-column"><a href="<?php echo $this->Url->build('retail/promotion_create/'.$promo->IDPROMOCAO);?>" class="btn btn-light btn-sm" id="btnEdit"><i class="fas fa-edit"></i> Editar</a></td>
        </tr>
    <?php endforeach;?>
    <?php else:?>
        <tr><td colspan="6" class="text-center">Nenhum registro encontrado!</td></tr>
    <?php endif;?>
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