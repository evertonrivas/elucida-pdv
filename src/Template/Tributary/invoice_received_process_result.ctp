<br/>
<?php if($total_divergencias>0):?>
<div class="card">
    <div class="card-heading">
    	<div class="row">
			<div class="col-sm">
				<i class="fas fa-angle-right"></i> <?=$title?>
			</div>
			<div class="col-sm text-right">
				<a href="<?php echo $this->Url->build('/tributary/invoice_received');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
			</div>
		</div>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Produto</th>
                    <th>Pre&ccedil;o Sistema</th>
                    <th>Pre&ccedil;o NF-e</th>
                    <th>A&ccedil;&atilde;o</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($divergencias as $divergencia): ?>
                <tr>
                    <td><?php echo $divergencia->SKU_PRODUTO; ?></td>
                    <td><?php echo $divergencia->NOME_PRODUTO; ?></td>
                    <td><?php echo $this->Number->currency($divergencia->PRECO_PRODUTO,"BRL"); ?></td>
                    <td><?php echo $this->Number->currency($divergencia->PRECO_NFE,"BRL"); ?></td>
                    <td>
                    <a class="btn btn-danger btn-xs" href="<?php echo $this->Url->build('/tributary/invoice_received_ignore_item_price/').$divergencia->IDPRODUTO; ?>">Ignorar</a> 
                    <a class="btn btn-success btn-xs" href="<?php echo $this->Url->build('/stock/product_create/').$divergencia->IDPRODUTO."/true"; ?>">Ajustar</a></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<script>
$(document).ready(function(){
	bootbox.alert("Processamento realizado com sucesso!",function(){ document.location.href='/tributary/invoice_received/'; });
});
</script>
<?php endif; ?>