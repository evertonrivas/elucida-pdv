<table class="table table-striped">
	<thead>
		<tr>
			<th>#</th>
			<th>Meio de Pagamento</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($itens as $it): ?>
		<tr>
			<th><?=$it->IDMEIOPAGAMENTO?></th>
			<td><?=$it->NOME;?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>