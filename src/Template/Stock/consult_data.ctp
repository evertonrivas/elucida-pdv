<table class="table table-striped">
	<thead>
		<tr>
			<th>Loja</th>
			<th>Quantidade</th>
		</tr>
	</thead>
	<tbody>
		<?php if(isset($data_list)):?>
		<?php foreach($data_list as $data): ?>
		<tr>
			<td><?=$data->LOJA;?></td>
			<td><?=$data->QUANTIDADE;?></td>
		</tr>
		<?php endforeach; ?>
		<?php else: ?>
		<tr>
			<td colspan="2" class="text-center">Nenhum registro encontrado!</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>