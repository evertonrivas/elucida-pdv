<br/>
<div class="card">
	<div class="card-header">
		<i class="fas fa-angle-right"></i> Resultados da Busca para <strong><?=$search_keyword?></strong>
	</div>
	<div class="card-body">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Local da Busca</th>
					<th>Resultado</th>
					<th>A&ccedil;&atilde;o</th>
				</tr>
			</thead>
			<tbody>
				<!-- resultado da busca de produtos -->
				<?php if($product_list->count()>0): ?>
				<tr>
					<td class="align-middle">Produto</td>
					<td>
						<?php foreach($product_list as $product): ?>
							<strong>Nome: </strong> <?=$product->NOME; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($product_list as $product): ?>
							<?php if($product->ESTRUTURA=="S"): ?>
							<a href="<?=$this->Url->build("/stock/single_product_create/")?><?=$product->IDPRODUTO;?>">ver registro</a><br/>
							<?php else: ?>
							<a href="<?=$this->Url->build("/stock/composite_product_create/")?><?=$product->IDPRODUTO;?>">ver registro</a><br/>
							<?php endif; ?>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif;?>
				
				<!-- resultado da busca de contas a pagar -->
				<?php if($contas_pagar_list->count()>0):?>
				<tr>
					<td>Contas &agrave; Pagar</td>
					<td>
						<?php foreach($contas_pagar_list as $contas_pagar): ?>
						<strong>Observa&ccedil;&atilde;o: </strong> <?=$contas_pagar->OBSERVACAO; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($contas_pagar_list as $contas_pagar): ?>
						<a href="#">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif; ?>
				
				<!-- resultado da busca de clientes -->
				<?php if($cliente_list->count()>0): ?>
				<tr>
					<td>Clientes</td>
					<td>
						<?php foreach($cliente_list as $cliente): ?>
						<strong>Nome: </strong><?=$cliente->NOME; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($cliente_list as $cliente): ?>
						<a href="<?=$this->Url->build("/retail/customer_create/")?><?=$cliente->IDCLIENTE?>">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif;?>
				
				<!-- resultado da busca de avaliacao de vendas -->
				<?php if($avaliacao_venda_list->count()>0): ?>
				<tr>
					<td>Avalia&ccedil;&atilde;o de Vendas</td>
					<td>
						<?php foreach($avaliacao_venda_list as $aval): ?>
						<strong>Sugest&atilde;o: </strong><?=$aval->SUGESTAO; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($avaliacao_venda_list as $aval): ?>
						<a href="#">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif; ?>
				
				<!-- resultado da busca de funcionarios -->
				<?php if($funcionario_list->count()>0): ?>
				<tr>
					<td>Funcion&aacute;rios</td>
					<td>
						<?php foreach($funcionario_list as $func): ?>
						<strong>Nome: </strong><?=$func->NOME; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($funcionario_list as $func): ?>
						<a href="<?=$this->Url->build("/hr/employer_create/")?><?=$func->IDFUNCIONARIO?>">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif; ?>
				
				<!-- resultado da busca de extrato bancario -->
				<?php if($extrato_list->count()>0): ?>
				<tr>
					<td>Funcion&aacute;rios</td>
					<td>
						<?php foreach($extrato_list as $func): ?>
						<strong>Hist&oacute;rico: </strong><?=$func->HISTORICO; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($extrato_list as $func): ?>
						<a href="<?=$this->Url->build("/financial/bankStatement/")?>">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif; ?>
				
				<!-- resultado da busca de fornecedores -->
				<?php if($fornecedor_list->count()>0): ?>
				<tr>
					<td>Fornecedores</td>
					<td>
						<?php foreach($fornecedor_list as $func): ?>
						<strong>Raz&atilde;o Social: </strong><?=$func->RAZAO_SOCIAL; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($fornecedor_list as $func): ?>
						<a href="<?=$this->Url->build("/system/provider_create/")?><?=$func->IDFORNECEDOR;?>">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif; ?>
				
				<!-- resultado da busca de cidades -->
				<?php if($cidade_list->count()>0): ?>
				<tr>
					<td>Cidades</td>
					<td>
						<?php foreach($cidade_list as $func): ?>
						<strong>Nome: </strong><?=$func->NOME; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($cidade_list as $func): ?>
						<a href="<?=$this->Url->build("/system/city_create/")?><?=$func->IDCIDADE;?>">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif; ?>
				
				<!-- resultado da busca de bancos -->
				<?php if($banco_list->count()>0): ?>
				<tr>
					<td>Bancos</td>
					<td>
						<?php foreach($banco_list as $func): ?>
						<strong>Nome: </strong><?=$func->NOME; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($banco_list as $func): ?>
						<a href="<?=$this->Url->build("/system/bank/")?>">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif; ?>
				
				<!-- resultado da busca de lojas -->
				<?php if($loja_list->count()>0): ?>
				<tr>
					<td>Lojas</td>
					<td>
						<?php foreach($loja_list as $func): ?>
						<strong>Nome: </strong><?=$func->NOME; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($loja_list as $func): ?>
						<a href="<?=$this->Uril->build("/system/store_create/")?><?=$func->IDLOJA;?>">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif; ?>
				
				<!-- resultado da busca de templates -->
				<?php if($template_list->count()>0): ?>
				<tr>
					<td>Modelos de E-mail</td>
					<td>
						<?php foreach($template_list as $func): ?>
						<strong>Nome: </strong><?=$func->NOME; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($template_list as $func): ?>
						<a href="<?=$this->Url->build("/system/template_create/")?><?=$func->IDTEMPLATE;?>">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif; ?>
				
				<!-- resultado da busca de categorias -->
				<?php if($categoria_list->count()>0): ?>
				<tr>
					<td>Categorias</td>
					<td>
						<?php foreach($categoria_list as $func): ?>
						<strong>Nome: </strong><?=$func->NOME; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($categoria_list as $func): ?>
						<a href="<?=$this->Url->build("/stock/category_create/")?><?=$func->IDCATEGORIA;?>">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif; ?>
				
				<!-- resultado da busca de regras de SKU -->
				<?php if($regra_list->count()>0): ?>
				<tr>
					<td>Regras de SKU</td>
					<td>
						<?php foreach($regra_list as $func): ?>
						<strong>Regra: </strong><?=$func->REGRA; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($regra_list as $func): ?>
						<a href="<?=$this->Url->build("/stock/sku_rule_create/")?><?=$func->IDREGRASKU?>/<?=$func->IDPRODUTOTIPO?>">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif; ?>
				
				<!-- resultado da busca de albuns -->
				<?php if($album_list->count()>0): ?>
				<tr>
					<td>&Aacute;lbuns</td>
					<td>
						<?php foreach($album_list as $func): ?>
						<strong>Nome: </strong><?=$func->NOME; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($album_list as $func): ?>
						<a href="<?=$this->Url->build("/gallery/index/false")?>">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif; ?>
				
				<!-- resultado da busca de imagens -->
				<?php if($imagem_list->count()>0): ?>
				<tr>
					<td>Imagens</td>
					<td>
						<?php foreach($imagem_list as $func): ?>
						<strong>Nome: </strong><?=$func->NOME; ?><br/>
						<?php endforeach; ?>
					</td>
					<td>
						<?php foreach($imagem_list as $func): ?>
						<a href="<?=$this->Url->build("/gallery/album/")?><?=$func->IDALBUM?>">ver registro</a><br/>
						<?php endforeach; ?>
					</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>