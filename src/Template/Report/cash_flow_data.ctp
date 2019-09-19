<table id="tblCashFlow" class="table table-hover">
	<thead>
		<tr>
			<th></th>
			<th><small>Opera&ccedil;&atilde;o Financeira</small></th>
			<th class="align-middle"><small>Jan</small></th>
			<th class="align-middle"><small>Fev</small></th>
			<th class="align-middle"><small>Mar</small></th>
			<th class="align-middle"><small>Abr</small></th>
			<th class="align-middle"><small>Mai</small></th>
			<th class="align-middle"><small>Jun</small></th>
			<th class="align-middle"><small>Jul</small></th>
			<th class="align-middle"><small>Ago</small></th>
			<th class="align-middle"><small>Set</small></th>
			<th class="align-middle"><small>Out</small></th>
			<th class="align-middle"><small>Nov</small></th>
			<th class="align-middle"><small>Dez</small></th>
		</tr>
	</thead>
	<tbody>
		<?php if(isset($data_list)):?>
		<?php foreach($data_list as $data): ?>
		<tr>
			<td><a href="#row_<?=$data['IDOPERACAOFINANCEIRA']?>" data-toggle="collapse" role="button" aria-expanded="false"><i class="fas fa-plus-circle text-success"></i></a></td>
			<td class="<?php if($data['TIPO_OPERACAO']=="I"){ echo "text-primary"; }elseif($data['TIPO_OPERACAO']=="E"){ echo "text-success"; }else{ echo "text-danger"; }?>"><small><?=$data['NOME'];?></small></td>
			<td class="<?php if($data['TIPO_OPERACAO']=="I"){ echo "text-primary"; }elseif($data['TIPO_OPERACAO']=="E"){ echo "text-success"; }else{ echo "text-danger"; }?> text-right"><small><?=$data['JAN']?></small></td>
			<td class="<?php if($data['TIPO_OPERACAO']=="I"){ echo "text-primary"; }elseif($data['TIPO_OPERACAO']=="E"){ echo "text-success"; }else{ echo "text-danger"; }?> text-right"><small><?=$data['FEV']?></small></td>
			<td class="<?php if($data['TIPO_OPERACAO']=="I"){ echo "text-primary"; }elseif($data['TIPO_OPERACAO']=="E"){ echo "text-success"; }else{ echo "text-danger"; }?> text-right"><small><?=$data['MAR']?></small></td>
			<td class="<?php if($data['TIPO_OPERACAO']=="I"){ echo "text-primary"; }elseif($data['TIPO_OPERACAO']=="E"){ echo "text-success"; }else{ echo "text-danger"; }?> text-right"><small><?=$data['ABR']?></small></td>
			<td class="<?php if($data['TIPO_OPERACAO']=="I"){ echo "text-primary"; }elseif($data['TIPO_OPERACAO']=="E"){ echo "text-success"; }else{ echo "text-danger"; }?> text-right"><small><?=$data['MAI']?></small></td>
			<td class="<?php if($data['TIPO_OPERACAO']=="I"){ echo "text-primary"; }elseif($data['TIPO_OPERACAO']=="E"){ echo "text-success"; }else{ echo "text-danger"; }?> text-right"><small><?=$data['JUN']?></small></td>
			<td class="<?php if($data['TIPO_OPERACAO']=="I"){ echo "text-primary"; }elseif($data['TIPO_OPERACAO']=="E"){ echo "text-success"; }else{ echo "text-danger"; }?> text-right"><small><?=$data['JUL']?></small></td>
			<td class="<?php if($data['TIPO_OPERACAO']=="I"){ echo "text-primary"; }elseif($data['TIPO_OPERACAO']=="E"){ echo "text-success"; }else{ echo "text-danger"; }?> text-right"><small><?=$data['AGO']?></small></td>
			<td class="<?php if($data['TIPO_OPERACAO']=="I"){ echo "text-primary"; }elseif($data['TIPO_OPERACAO']=="E"){ echo "text-success"; }else{ echo "text-danger"; }?> text-right"><small><?=$data['SET']?></small></td>
			<td class="<?php if($data['TIPO_OPERACAO']=="I"){ echo "text-primary"; }elseif($data['TIPO_OPERACAO']=="E"){ echo "text-success"; }else{ echo "text-danger"; }?> text-right"><small><?=$data['OUT']?></small></td>
			<td class="<?php if($data['TIPO_OPERACAO']=="I"){ echo "text-primary"; }elseif($data['TIPO_OPERACAO']=="E"){ echo "text-success"; }else{ echo "text-danger"; }?> text-right"><small><?=$data['NOV']?></small></td>
			<td class="<?php if($data['TIPO_OPERACAO']=="I"){ echo "text-primary"; }elseif($data['TIPO_OPERACAO']=="E"){ echo "text-success"; }else{ echo "text-danger"; }?> text-right"><small><?=$data['DEZ']?></small></td>
		</tr>
		<?php if(isset($flux_data)):?>
		<tr class="collapse" id="row_<?=$data['IDOPERACAOFINANCEIRA']?>">
			<td colspan="14">
				<table class="table-striped" style="width: 100%">
					<thead>
						<tr>
							<th>Data do Movimento</th>
							<th>Valor</th>
							<th>Hist&oacute;rico</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($flux_data as $fdata): ?>
						<?php if($fdata->IDOPERACAOFINANCEIRA==$data['IDOPERACAOFINANCEIRA']):?>
						<tr>
							<td><?=$fdata->DATA_MOVIMENTO->format("d/m/Y");?></td>
							<td><?=$fdata->VALOR;?></td>
							<td><?=$fdata->HISTORICO;?></td>
						<?php endif;?>
						<?php endforeach; ?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php endif; ?>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>