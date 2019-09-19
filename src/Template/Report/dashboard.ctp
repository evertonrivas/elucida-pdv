<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/i18n/defaults-pt_BR.min.js"></script>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
		<a class="navbar-brand" href="/system/"><?=$this->Html->image("logo-icon.png",['alt' => 'Elucida']) ?></a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
				<select class="selectpicker" multiple title="Selecionar Loja(s)" name="cbStore" id="cbStore">
					<?php foreach($lojas as $loja):?>
					<option value="<?=$loja->IDLOJA;?>"><?=$loja->NOME;?></option>
					<?php endforeach;?>
				</select>&nbsp;
				<select class="selectpicker" multiple data-live-search="true" title="Selecionar Categoria(s)" name="cbCategory" id="cbCategory">
					<?php foreach($categorias_pai as $categoria_pai): ?>
                  <optgroup label="<?php echo $categoria_pai->NOME; ?>">
                      <?php foreach($categorias as $categoria):?>
                      <?php if($categoria->CATEGORIA_PAI==$categoria_pai->IDCATEGORIA): ?>
                      <option value="<?php echo $categoria->IDCATEGORIA; ?>"><?php echo $categoria->NOME; ?></option>
                      <?php endif; ?>
                      <?php endforeach;?>
                  </optgroup>
                  <?php endforeach; ?>
				</select>&nbsp;
				<select class="selectpicker" multiple data-live-search="true" title="Selecionar Fornecedor(es)" id="cbProvider" name="cbProvider">
					<?php foreach($fornecedores as $fornecedor):?>
	                  <option value="<?php echo $fornecedor->IDFORNECEDOR; ?>"><?php echo $fornecedor->FANTASIA; ?></option>
	                <?php endforeach; ?>
				</select>&nbsp;
				<div class="input-group" style="max-width: 150px!important;">
					<input type="text" class="form-control date" data-provide='datepicker' data-date-format='dd/mm/yyyy' data-date-language='pt-BR' data-date-autoclose='true' data-date-today-highlight='true' placeholder="Data Inicial" id="txtDateStart" name="txtDateStart">
					<div class="input-group-append">
						<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
					</div>
				</div>&nbsp;
				<div class="input-group" style="max-width: 150px!important;">
					<input type="text" class="form-control date" data-provide='datepicker' data-date-format='dd/mm/yyyy' data-date-language='pt-BR' data-date-autoclose='true' data-date-today-highlight='true' placeholder="Data Final" id="txtDateEnd" name="txtDateEnd">
					<div class="input-group-append">
						<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
					</div>
				</div>
				<!--<a href="<?php echo $this->Url->build('/');?>" class="btn btn-secondary"><i class="fas fa-arrow-alt-circle-left"></i> Voltar</a>-->
			</ul>
			<button type="submit" class="btn btn-primary" id="btnUpdate" name="btnUpdate"><i class="fas fa-wrench"></i> Atualizar</button>
	  </div>
	</nav>

	<div class="container"><br/>
		<div class="row">
			<div class="col-sm">
				<div class="card border-success">
					<div class="card-body text-center">
						<div class="row">
							<div class="col">
								<h6 class="text-muted card-title text-uppercase mb-2">Faturamento</h6>
								<span class="h2 mb-0 text-success" id="spanFAT">0.00</span>
							</div>
							<div class="col-auto">
								<i class="h2 fas fa-dollar-sign text-muted mb-0"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm">
				<div class="card border-info">
					<div class="card-body text-center">
						<div class="row">
							<div class="col">
								<h6 class="text-muted card-title text-uppercase mb-2">Margem de Contribui&ccedil;&atilde;o</h6>
								<span class="h2 mb-0 text-info" id="spanMAR">0.00%</span>
							</div>
							<div class="col-auto">
								<span class="h2 fas fa-hand-holding-usd text-muted mb-0"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm">
				<div class="card border-purple">
					<div class="card-body text-center">
						<div class="row">
							<div class="col">
								<h6 class="text-muted card-title text-uppercase mb-2">N&deg; de Vendas</h6>
								<span class="h2 mb-0 text-purple" id="spanNVE">0</span>
							</div>
							<div class="col-auto">
								<span class="h2 fas fa-wallet text-muted mb-0"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><br/>
		<!-- INICIO DA SEGUNDA LINHA -->
		<div class="row">
			<div class="col-sm">
				<div class="card border-teal">
					<div class="card-body text-center">
						<div class="row">
							<div class="col">
								<h6 class="text-muted card-title text-uppercase mb-2">Ticket M&eacute;dio</h6>
								<span class="h2 mb-0 text-teal" id="spanTIC">0.00</span>
							</div>
							<div class="col-auto">
								<span class="h2 fas fa-receipt text-muted mb-0"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm">
				<div class="card border-cian">
					<div class="card-body text-center">
						<div class="row">
							<div class="col">
								<h6 class="text-muted card-title text-uppercase mb-2">Fat. M&eacute;dio Mensal</h6>
								<span class="h2 mb-0 text-cian" id="spanFME">0.00</span>
							</div>
							<div class="col-auto">
								<span class="h2 fas fa-money-check-alt text-muted mb-0"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm">
				<div class="card border-pink">
					<div class="card-body text-center">
						<div class="row">
							<div class="col">
								<h6 class="text-muted card-title text-uppercase mb-2">M&eacute;d. Vendas Dia</h6>
								<span class="h2 mb-0 text-pink" id="spanMVE">0.00</span>
							</div>
							<div class="col-auto">
								<span class="h2 fas fa-cash-register text-muted mb-0"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- INICIO DA TERCEIRA LINHA -->
		<br/>
		<div class="row">
			<div class="col">
				<div class="card">
					<div class="card-body">
						<canvas id="chartBar"></canvas>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card">
					<div class="card-body">
						<canvas id="chartPie"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>

<script>
window.barChart = null;
window.pieChart = null;

$(document).ready(function(){
	getKpis();
	getCharts();
});

$(document).on("click","#btnUpdate",function(){
	getKpis();
	getCharts();
});

function getKpis(){

	var dataFilter = {
		LOJAS        : $("#cbStore").val(),
		CATEGORIAS   : $("#cbCategory").val(),
		FORNECEDORES : $("#cbProvider").val(),
		DATA_INICIAL : $("#txtDateStart").val(),
		DATA_FINAL   : $("#txtDateEnd").val()
	};

	$.ajax({
		headers : {
			'X-CSRF-Token':csrf
		},
		type    : 'POST',
		data    : dataFilter,
		url     : '<?=$this->Url->build("/report/dashboard_kpi/")?>',
		dataType: 'json',
		success : function(data){
			$("#spanFAT").html(data.FAT);
			$("#spanMAR").html(data.MAR);
			$("#spanNVE").html(data.NVE);
			$("#spanTIC").html(data.TIC);
			$("#spanFME").html(data.FME);
			$("#spanMVE").html(data.MVE);
		}
	});
}

function getCharts(){
	var dataFilter = {
		LOJAS        : $("#cbStore").val(),
		CATEGORIAS   : $("#cbCategory").val(),
		FORNECEDORES : $("#cbProvider").val(),
		DATA_INICIAL : $("#txtDateStart").val(),
		DATA_FINAL   : $("#txtDateEnd").val()
	};

	$.ajax({
		headers : {
			'X-CSRF-Token':csrf
		},
		type    : 'POST',
		data    : dataFilter,
		url     : '<?=$this->Url->build("/report/dashboard_chart/")?>',
		dataType: 'json',
		success : function(data){
			mountBar(data.BAR);
			mountPie(data.PIE);
		}
	});
}

function mountBar(data){
	if(window.barChart != undefined ){
		window.barChart.destroy();
	}
	window.barChart = new Chart($("#chartBar"), {
		type: 'bar',
		data: {
			labels: data.label,
			datasets:[{
				label: 'Receita no Ano',
				data : data.value,
				fill : false,
				backgroundColor : [
					barsColors.pink,
					barsColors.red,
					barsColors.orange,
					barsColors.yellow,
					barsColors.green,
					barsColors.teal,
					barsColors.blue,
					barsColors.lightBlue,
					barsColors.purple,
					barsColors.grey,
					barsColors.brown,
					barsColors.black],
				borderColor     : [
					barsColors.pink,
					chartColors.red,
					chartColors.orange,
					chartColors.yellow,
					chartColors.green,
					chartColors.teal,
					chartColors.blue,
					chartColors.lightBlue,
					chartColors.purple,
					chartColors.grey,
					chartColors.brown,
					chartColors.black],
				borderWidth     : 1
			}]
		},
		options : {
			scales : {
				yAxes :[{
					ticks : {
						beginAtZero : true
					}
				}]
			}
		}
	});
}

function mountPie(data){

	if(window.pieChart != undefined){
		window.pieChart.destroy();
	}

	window.pieChart = new Chart($("#chartPie"),{
		type:'doughnut',
		data:{
			datasets: [{
				data: data.value,
				backgroundColor: [
					barsColors.pink,
					chartColors.red,
					chartColors.orange,
					chartColors.yellow,
					chartColors.green,
					chartColors.teal,
					chartColors.blue,
					chartColors.lightBlue,
					chartColors.purple,
					chartColors.grey,
					chartColors.brown,
					chartColors.black]
			}],
			labels: data.label
		},
		options:{
			responsive : true,
			animation : {
				animateScale : true,
				animateRotate : true
			},
			legend:{
				position: 'left'
			}
		}
	});
}
</script>
