<table class="table table-striped">
	<thead>
		<tr>
			<th>#</th>
			<th>Tipo de despesa</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($itens as $it): ?>
		<tr>
			<th><?=$it->IDTIPODESPESA?></th>
			<td><?=$it->NOME;?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>