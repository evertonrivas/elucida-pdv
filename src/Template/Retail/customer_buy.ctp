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
<table class='table table-hover table-bordered'>
    <thead>
        <tr>
            <th>Data da Venda</th>
            <th>Subtotal</th>
            <th>Desconto</th>
            <th>Valor Pago</th>
        </tr>
    </thead>
    <tbody>
    <?php if($data_buy->count()>0):?>
    <?php foreach($data_buy as $data): ?>
        <tr>
            <td><?= $data->DATA_VENDA->format("d/m/Y"); ?></td>
            <td><?= $this->Number->currency($data->SUBTOTAL,"BRL"); ?></td>
            <td><?= $this->Number->currency($data->DESCONTO,"BRL"); ?></td>
            <td><?= $this->Number->currency($data->VALOR_PAGO,"BRL"); ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Produto</th>
                            <th>Pre&ccedil;o UN.</th>
                            <th>Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($data_item as $item):?>
                    <?php if($item->IDVENDA==$data->IDVENDA):?>
                    <tr>
                        <td><?=$item->SKU_PRODUTO; ?></td>            
                        <td><?=$item->NOME_PRODUTO; ?></td>
                        <td><?=$this->Number->currency($item->PRECO_UNITARIO,"BRL"); ?></td>
                        <td><?=$item->QUANTIDADE; ?></td>
                    </tr>
                    <?php endif;?>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </td>
        </tr>
    <?php endforeach;?>
    <?php else:?>
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