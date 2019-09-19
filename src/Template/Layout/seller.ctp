<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<a class="navbar-brand" href="/"><?=$this->Html->image("logo-icon.png",['alt' => 'Elucida']) ?></a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarSystem" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fas fa-box"></i> Caixa
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarSystem">
					<a class="dropdown-item" href="/retail/box_open">Abrir</a>
					<a class="dropdown-item" href="/retail/removal">Registrar Retirada</a>
					<a class="dropdown-item" href="/retail/box_close">Fechar</a>
				</div>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/retail/pos"><i class="fas fa-cash-register"></i> PDV</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/retail/exchange_make"><i class="fas fa-sync-alt"></i> Trocas</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/retail/wish"><i class="fas fa-inbox"></i> Desejos</a>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarHr" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fas fa-list-alt"></i> Relat&oacute;rios
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarHr'">
					<h6 class="dropdown-header">Tabelas</h6>
					<a class="dropdown-item" href="/report/day_receipts">Recebimentos do Dia</a>
					<a class="dropdown-item" href="/report/day_products">Produtos do Dia</a>
					<a class="dropdown-item" href="/retail/customer">Cadastro de Clientes</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Opera&ccedil;&otilde;es</h6>
					<a class="dropdown-item" href="/retail/sale">Vendas</a>
					<a class="dropdown-item" href="/retail/exchange">Trocas</a>
				</div>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/stock/consult/"><i class="fas fa-store-alt"></i> Consultar Estoque</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/users/logout"><i class="fas fa-sign-out-alt"></i> Sair</a>
			</li>
		</ul>
  </div>
</nav>