<br/>
<div class="card">
    <div class="card-header">
    	<div class="row">
			<div class="col-sm">
				<i class="fas fa-angle-right"></i> <?=$title?>
			</div>
			<div class="col-sm text-right">
				<a href="<?php echo $this->Url->build('/tributary/invoice_received/true');?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>
			</div>
		</div>
	</div>
    <div class="card-body">
        <table class="table  table-striped">
            <thead>
                <tr>
                    <th>N&uacute;mero da Nota</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($process_list as $process): ?>
                <tr>
                    <td><?php if(isset($process->NUMERO_NOTA)){ echo $process->NUMERO_NOTA; } ?></td>
                    <td><?php if(isset($process->STATUS)){ if($process->STATUS=="1"){ echo "Importada"; }elseif($process->STATUS=="2"){ echo "N&atilde;o Importada"; }else{ echo "Apenas atualizou o XML"; }}?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>