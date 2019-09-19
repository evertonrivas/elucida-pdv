<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<a class="navbar-brand" href="/"><?=$this->Html->image("logo-icon.png",['alt' => 'Elucida']) ?></a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item">
				<a class="nav-link" href="/hr/employer"><i class="fas fa-users"></i> Colaboradores</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/retail/customer"><i class="fas fa-user-friends"></i> Clientes</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/stock/interstore_transfer"><i class="fas fa-exchange-alt"></i> Transfer&ecirc;ncias</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/stock/consult/"><i class="fas fa-store-alt"></i> Consultar Estoque</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/financial/calendar"><i class="fas fa-calendar-alt"></i> Contas &agrave; Pagar</a>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarHr" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="far fa-chart-bar"></i> Decis&otilde;es
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarHr'">
					<h6 class="dropdown-header">Gr&aacute;ficos</h6>
					<a class="dropdown-item" href="/report/dashboard">Painel (Dashboard)</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Relat&oacute;rios</h6>
					<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCashflow">Fluxo de Caixa</a>
					<a class="dropdown-item" href="/report/day_receipts">Recebimentos/Dia</a>
					<a class="dropdown-item" href="/report/day_products">Produtos/Dia</a>
					<a class="dropdown-item" href="/report/day_payment_options">Meios de Pagamento/Dia</a>
					<h6 class="dropdown-header">Estoque</h6>
					<a class="dropdown-item" href="/report/stock_by_category">Estoque por Categoria</a>
					<a class="dropdown-item" href="/report/stock_by_type">Estoque por Tipo de Produto</a>
				</div>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/users/logout"><i class="fas fa-sign-out-alt"></i> Sair</a>
			</li>
		</ul>
		<form class="form-inline my-2 my-lg-0" action="/system/search_result_manager" method="post">
			<input type="hidden" name="_csrfToken" id="_csrfToken" value=<?= json_encode($this->request->getParam('_csrfToken')) ?>/>
			<input class="form-control mr-sm-2" type="search" name="search_keyword" placeholder="Procurar no sistema..." aria-label="Search">
			<button class="btn btn-outline-secondary my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
		</form>
  </div>
</nav>