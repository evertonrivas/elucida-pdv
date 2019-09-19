<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<a class="navbar-brand" href="/system/"><?=$this->Html->image("logo-icon.png",['alt' => 'Elucida']) ?></a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarSystem" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fas fa-cog"></i> Sistema
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarSystem">
					<h6 class="dropdown-header">Localidade</h6>
					<a class="dropdown-item" href="/system/bank">Bancos</a>
					<a class="dropdown-item" href="/system/city">Cidades</a>
					<a class="dropdown-item" href="/system/store">Lojas</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Monet&aacute;rio</h6>
					<a class="dropdown-item" href="/financial/card_flag">Bandeiras de Cart&otilde;es</a>
					<a class="dropdown-item" href="/financial/payment_condition">Condi&ccedil;&otilde;es de Pagamento</a>
					<a class="dropdown-item" href="/financial/payment_option">Meios de Pagamento</a>
					<a class="dropdown-item" href="/financial/expense_type">Tipos de Despesas</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="/system/provider">Fornecedores</a>
					<a class="dropdown-item" href="/system/template">Modelos de e-mail</a>
					<a class="dropdown-item" href="/stock/product_type">Tipos de Produtos</a>
					<a class="dropdown-item" href="/users">Usu&aacute;rios</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="/system/options">Op&ccedil;&otilde;es do Sistema</a>
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarHr" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-user"></i> Pessoas
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarHr'">
					<h6 class="dropdown-header">Configura&ccedil;&otilde;es</h6>
					<a class="dropdown-item" href="/hr/job_title">Cargos</a>
					<a class="dropdown-item" href="/hr/job_title_type">Hierarquia de Cargos</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Registro</h6>
					<a class="dropdown-item" href="/hr/employer">Colaboradores</a>
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarHr" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-tags"></i> Suprimentos
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarHr'">
					<h6 class="dropdown-header">Classifica&ccedil;&atilde;o</h6>
					<a class="dropdown-item" href="/stock/category">Categorias</a>
					<a class="dropdown-item" href="/stock/sku_rule">Regras de SKU</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Dados dos Produtos</h6>
					<a class="dropdown-item" href="/stock/single_product">Produtos Simples</a>
					<a class="dropdown-item" href="/stock/composite_product">Produtos Compostos</a>
					<a class="dropdown-item" href="/gallery/index/false">Galeria de Imagens</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Estoque</h6>
					<a class="dropdown-item" href="/stock/single_product_alocation">Aloca&ccedil;&atilde;o de Produto</a>
					<a class="dropdown-item" href="/stock/adjustment">Ajuste</a>
					<a class="dropdown-item" href="/stock/prepare_to_inventory">Inventariar</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Expedi&ccedil;&atilde;o</h6>
					<a class="dropdown-item" href="/stock/interstore_transfer">Transfer&ecirc;ncia entre lojas</a>
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarHr" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-file-invoice-dollar"></i> Tribut&aacute;rio
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarHr'">
					<h6 class="dropdown-header">Notas Fiscais Recebidas (NFe)</h6>
					<a class="dropdown-item" href="/tributary/invoice_received_import">Importar</a>
					<a class="dropdown-item" href="/tributary/invoice_received">Listar</a>
					<a class="dropdown-item" href="/tributary/invoice_received/true">Processar</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Devolu&ccedil;&otilde;es (NFe)</h6>
					<a class="dropdown-item" href="/tributary/list_return">Listar</a>
					<a class="dropdown-item" href="/tributary/issue_return">Emitir</a>
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarHr" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-hand-holding-usd"></i> Comercial
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarHr'">
					<h6 class="dropdown-header">Operac&otilde;es Comerciais</h6>
					<a class="dropdown-item" href="/retail/sale">Listar Vendas</a>
					<a class="dropdown-item" href="/retail/exchange">Listar Trocas</a>
					<a class="dropdown-item" href="/retail/cupon">Listar Cupons</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Descontos</h6>
					<a class="dropdown-item" href="/retail/partner">Parcerias</a>
					<a class="dropdown-item" href="/retail/promotion">Promo&ccedil;&otilde;es</a>
					<div class="dropdown-divider"></div>
					<div class="dropdown-header">Caixa</div>
					<a class="dropdown-item" href="/retail/box_close_view">Fechamento de Caixa</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Clientes</h6>
					<a class="dropdown-item" href="/retail/customer">Listar</a>
					<a class="dropdown-item" href="/retail/wish">Desejos - Revisar</a>
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarHr" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-coins"></i> Financeiro
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarHr'">
					<h6 class="dropdown-header">Contas &agrave; Pagar</h6>
					<a class="dropdown-item" href="/financial/calendar">Calend&aacute;rio</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Planejamento</h6>
					<a class="dropdown-item" href="/financial/budget">Or&ccedil;amento</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Estrutura do Fluxo de Caixa</h6>
					<a class="dropdown-item" href="/financial/financial_operation">Opera&ccedil;&otilde;es Financeiras</a>
					<a class="dropdown-item" href="/financial/inflow">Entradas</a>
					<a class="dropdown-item" href="/financial/outflow">Sa&iacute;das</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Complemento do Fluxo de Caixa</h6>
					<a class="dropdown-item" href="/financial/bank_statement_import">Importar Extratos Banc&aacute;rios</a>
					<a class="dropdown-item" href="/financial/bankStatement">Extratos Importados</a>
					
				</div>
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
					<a class="dropdown-item" href="/report/day_receipts">Recebimentos/Dia</a>
					<a class="dropdown-item" href="/report/day_products">Produtos/Dia</a>
					<a class="dropdown-item" href="/report/day_payment_options">Meios de Pagamento/Dia</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Fluxo de Caixa</h6>
					<a class="dropdown-item" href="javascript:updateCashFlow(true);">For&ccedil;ar Reconstru&ccedil;&atilde;o</a>
					<a class="dropdown-item" href="javascript:updateCashFlow(false);">For&ccedil;ar Atualiza&ccedil;&atilde;o</a>
					<a class="dropdown-item" href="javascript:openCashFlow();">Exibi&ccedil;&atilde;o</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">Estoque</h6>
					<a class="dropdown-item" href="/report/stock_by_category">Estoque por Categoria</a>
					<a class="dropdown-item" href="/report/stock_by_provider">Estoque por Fornecedor</a>
					<a class="dropdown-item" href="/report/stock_by_store">Estoque por Loja</a>
					<a class="dropdown-item" href="/report/stock_by_type">Estoque por Tipo de Produto</a>
				</div>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/users/logout"><i class="fas fa-sign-out-alt"></i> Sair</a>
			</li>
		</ul>
		<form class="form-inline my-2 my-lg-0" action="/system/search_result_admin" method="post">
			<input type="hidden" name="_csrfToken" id="_csrfToken" value=<?= json_encode($this->request->getParam('_csrfToken')) ?>/>
			<input class="form-control mr-sm-2" type="search" name="search_keyword" placeholder="Procurar no sistema..." aria-label="Search">
			<button class="btn btn-outline-secondary my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
		</form>
  </div>
</nav>

<!-- MODAL DO FLUXO DE CAIXA -->
<div class="modal fade" id="modalCashFlow" tabindex="-1" role="dialog" aria-hidden="true">
  <input type="hidden" id="txtPathToCashFlow" name="txtPathToCashFlow">
  <input type="hidden" id="dateToUpdate" name="dateToUpdate"/>
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title" id="modalCashFlowTitle">Fluxo de Caixa</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <div class="modal-body" id="filters">
	  	<iframe name="frmCashflow" id="frmCashflow" style="border:0px;overflow-y:scroll;min-height:450px;max-height:450px;width:100%;"></iframe>
	  </div>
	</div>
  </div>
</div>