<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Number;
use Cake\I18n\Time;

class ReportController extends AppController{
	
	public function initialize() {
        parent::initialize();
        $this->loadComponent("Paginator");
        $this->loadComponent("Filter");
        ob_start("ob_gzhandler");
    }
    
    /**
	* Metodo que exibe o dashboard
	* 
	* @return null
	*/
    public function dashboard(){
		$this->viewBuilder()->setLayout("dashboard");
		
		$user = $this->Auth->user();
		
		$this->set('categorias_pai',TableRegistry::get('SysCategoria')->find()->where(['CATEGORIA_PAI IS' => NULL]));
        $this->set('categorias',TableRegistry::get('SysCategoria')->find()->where(['CATEGORIA_PAI IS NOT' => NULL]));
		
		$lojas = TableRegistry::get('SysLoja')->find()->select(['IDLOJA','NOME']);
		if($this->Auth->user()['role']=="admin"){
			$lojas->where(function($exp){
                return $exp->notEq('IDLOJA',TableRegistry::get('SysOpcao')->get("DEFAULT_STORE")->OPCAO_VALOR);
            });
		}else{
			$lojas->where(['IDLOJA' => $user['storeid']]);
		}
		
		$this->set('lojas',$lojas);
        $this->set('fornecedores',  TableRegistry::get('SysFornecedor')->find()->order(['FANTASIA' => 'ASC']));
		
	}
	
	/**
	* Metodo que realiza a busca e formatacao dos KPi
	* 
	* @return json
	*/
	public function dashboardKpi(){
		$retorno = new \stdClass();
		$retorno->FAT = $this->numberShorten('0.00'); //faturamento
		$retorno->MAR = '0.00%'; //margem de contribuicao
		$retorno->NVE = '0';   //numero de vendas
		$retorno->TIC = $this->numberShorten('0.00'); //ticket medio
		$retorno->FME = $this->numberShorten('0.00'); //faturamento medio mensal
		$retorno->MVE = '0.00';   //media de vendas dia
		
		//se o usuario for administrador usuarah os filtros
		//senao usarah apenas a loja do usuario
		if($this->Auth->user()['role']=="admin"){
			$lojas = $this->request->getData("LOJAS");
		}else{
			$lojas[] = $this->Auth->user()['storeid'];
		}
		$cats  = $this->request->getData("CATEGORIAS");
		$forns = $this->request->getData("FORNECEDORES");
		$dtIni = $this->request->getData("DATA_INICIAL");
		$dtFim = $this->request->getData("DATA_FINAL");
		
		//verifica se foram passados os parametros de data
		if($dtIni!="" && $dtFim!=""){
			$dtIni = $this->dateToDatabase($dtIni);
			$dtFim = $this->dateToDatabase($dtFim);
		}else{
			//monta a data padrao sendo deste o primeiro dia do ano atual ateh a data de hoje
			$dtIni = date("Y-01-01");
			$dtFim = date("Y-m-d");
		}
		
		//monta a subquery de data que sempre serah utilizada
		$qryDate = TableRegistry::get('LojVenda')->find()->select(['IDVENDA'])
			->where(function($exp) use($dtIni,$dtFim){ 
				return $exp->between('DATE(DATA_VENDA)',$dtIni,$dtFim); 
			});
		
		//verifica se o parametro de categorias foi passado
		if(is_array($cats)){
			$qryCat = TableRegistry::get('SysCategoriaProduto')->find()->select(['IDPRODUTO'])
				->where(function($exp) use ($cats){ return $exp->in('IDCATEGORIA',$cats); });
		}
		
		//verifica se o parametro de fornecedores foi passado
		if(is_array($forns)){
			$qryForn = TableRegistry::get('SysProduto')->find()->select(['IDPRODUTO'])
				->where(function($exp) use ($forns){ return $exp->in('IDFORNECEDOR',$forns); });
		}
		
		/*************************INICIO FATURAMENTO ********************************/
		$qryFat = TableRegistry::get('LojVenda')->find()
			->select(['FAT' => 'SUM(LVP.VALOR-(TROCO/(SELECT COUNT(IDVENDA) FROM loj_venda_pagamento WHERE IDVENDA=LVP.IDVENDA)))'])
			->join([
				'alias' => 'LVP',
				'table' => 'loj_venda_pagamento',
				'type'  => 'inner',
				'conditions' => 'LVP.IDVENDA=LojVenda.IDVENDA'
			])->where(function($exp){ return $exp->notIn('LVP.IDCONDICAOPAGAMENTO',['-1','12']); })
			->where(function($exp) use($qryDate){ return $exp->in('LojVenda.IDVENDA',$qryDate); });
			
		if(is_array($lojas)){
			$qryFat->where(function($exp) use ($lojas){ return $exp->in('IDLOJA',$lojas); });
		}
		
		if(is_array($cats)){ $qryFat->where(['IDPRODUTO IN',$qryCat]); }
		
		if(is_array($forns)){ $qryFat->where(['IDPRODUTO IN',$qryForn]); }
		
		if($qryFat && $qryFat->count()>0){ $retorno->FAT = $this->numberShorten($qryFat->first()->FAT); }
		/**************************** FIM FATURAMENTO *********************************/
		
		
		
		/********************* INICIO MARGEM DE CONTRIBUICAO (VERIFICAR NECESSIDADE) **************************/
		$qryMar = TableRegistry::get('LojMargemContribuicao')->find()
			->select(['MAR' => '(SUM(PERCENT_MARGEM)/COUNT(*))*100'])
			->where(function($exp) use($qryDate){ return $exp->in('IDVENDA',$qryDate); });
			
		if(is_array($lojas)){
			$qryMar->where(function($exp) use ($lojas){ return $exp->in('IDLOJA',$lojas); });
		}
		
		if(is_array($cats)){ $qryMar->where(['IDPRODUTO IN',$qryCat]); }
		
		if(is_array($forns)){ $qryMar->where(['IDPRODUTO IN',$qryForn]); }
		
		if($qryMar && $qryMar->count()>0){ $retorno->MAR = number_format($qryMar->MAR,2).'%'; }
		/************************ FIM MARGEM DE CONTRIBUICAO **************************/
		
		
		
		/********************* INICIO DO NUMERO DE VENDAS **************************/
		$qryNve = TableRegistry::get('LojVenda')->find()
			->select(['NVE' => 'COUNT(DISTINCT LojVenda.IDVENDA)'])
			->join([
				'alias' => 'LVP',
				'table' => 'loj_venda_pagamento',
				'type'  => 'inner',
				'conditions' => 'LVP.IDVENDA=LojVenda.IDVENDA'
			])->where(function($exp){ return $exp->notIn('LVP.IDCONDICAOPAGAMENTO',['-1','12']); })
			->where(function($exp) use($qryDate){ return $exp->in('LojVenda.IDVENDA',$qryDate); });
			
		if(is_array($lojas)){
			$qryNve->where(function($exp) use ($lojas){ return $exp->in('IDLOJA',$lojas); });
		}
		
		if(is_array($cats)){ $qryNve->where(['IDPRODUTO IN',$qryCat]); }
		
		if(is_array($forns)){ $qryNve->where(['IDPRODUTO IN',$qryForn]); }
		
		if($qryNve && $qryNve->count()>0){ $retorno->NVE = $qryNve->first()->NVE; }
		/********************* FIM DO NUMERO DE VENDAS **************************/
		
		/********************* INICIO DO TICKET MEDIO **************************/
		$qryTic = TableRegistry::get('LojVenda')->find()
			->select(['TIC' => 'SUM(LVP.VALOR-(TROCO/(SELECT COUNT(IDVENDA) FROM loj_venda_pagamento WHERE IDVENDA=LojVenda.IDVENDA)))/COUNT(DISTINCT LojVenda.IDVENDA)'])
			->join([
				'alias' => 'LVP',
				'table' => 'loj_venda_pagamento',
				'type'  => 'inner',
				'conditions' => 'LVP.IDVENDA=LojVenda.IDVENDA'
			])->where(function($exp){ return $exp->notIn('LVP.IDCONDICAOPAGAMENTO',['-1','12']); })
			->where(function($exp) use($qryDate){ return $exp->in('LojVenda.IDVENDA',$qryDate); });
			
		if(is_array($lojas)){
			$qryTic->where(function($exp) use ($lojas){ return $exp->in('IDLOJA',$lojas); });
		}
		
		if(is_array($cats)){ $qryTic->where(['IDPRODUTO IN',$qryCat]); }
		
		if(is_array($forns)){ $qryTic->where(['IDPRODUTO IN',$qryForn]); }
		
		if($qryTic && $qryTic->count()>0){ $retorno->TIC = $this->numberShorten($qryTic->first()->TIC); }
		/*********************** FIM DO TICKET MEDIO ***************************/
		
		
		/*********************** INICIO FAT MENSAL ***************************/
		$pStart = substr($dtIni,0,strlen($dtIni)-3);
		$pStart = str_replace("-", "", $pStart);
		
		$pEnd   = substr($dtFim,0,strlen($dtFim)-3);
		$pEnd   = str_replace("-", "", $pEnd);
		
		$qryFme = TableRegistry::get('LojVenda')->find()
			->select(['FME' => "SUM(LVP.VALOR-(TROCO/(SELECT COUNT(IDVENDA) FROM loj_venda_pagamento WHERE IDVENDA=LojVenda.IDVENDA)))/(PERIOD_DIFF('$pEnd','$pStart')+1)"])
			->join([
				'alias' => 'LVP',
				'table' => 'loj_venda_pagamento',
				'type'  => 'inner',
				'conditions' => 'LVP.IDVENDA=LojVenda.IDVENDA'
			])->where(function($exp){ return $exp->notIn('LVP.IDCONDICAOPAGAMENTO',['-1','12']); })
			->where(function($exp) use($qryDate){ return $exp->in('LojVenda.IDVENDA',$qryDate); });
		
		if(is_array($lojas)){
			$qryFme->where(function($exp) use ($lojas){ return $exp->in('IDLOJA',$lojas); });
		}
		
		if(is_array($cats)){ $qryFme->where(['IDPRODUTO IN',$qryCat]); }
		
		if(is_array($forns)){ $qryFme->where(['IDPRODUTO IN',$qryForn]); }
		
		if($qryFme && $qryFme->count()>0){ $retorno->FME = $this->numberShorten($qryFme->first()->FME); }
		/*********************** FIM DO FAT MENSAL ***************************/
		
		/*********************** INICIO MEDIA VENDAS DIA ***************************/
		$qryMve = TableRegistry::get('LojVenda')->find()
			->select(['MVE' => "(COUNT(LVP.IDVENDA)/DATEDIFF('$dtFim','$dtIni'))"])
			->join([
				'alias' => 'LVP',
				'table' => 'loj_venda_pagamento',
				'type'  => 'inner',
				'conditions' => 'LVP.IDVENDA=LojVenda.IDVENDA'
			])->where(function($exp){ return $exp->notIn('LVP.IDCONDICAOPAGAMENTO',['-1','12']); })
			->where(function($exp) use($qryDate){ return $exp->in('LojVenda.IDVENDA',$qryDate); });
			
		if(is_array($lojas)){
			$qryMve->where(function($exp) use ($lojas){ return $exp->in('IDLOJA',$lojas); });
		}
		
		if(is_array($cats)){ $qryMve->where(['IDPRODUTO IN',$qryCat]); }
		
		if(is_array($forns)){ $qryMve->where(['IDPRODUTO IN',$qryForn]); }
		
		if($qryMve && $qryMve->count()>0){ $retorno->MVE = number_format($qryMve->first()->MVE,2); }
		/*********************** FIM DA MEDIA VENDAS DIA ***************************/
		
		return $this->response->withStringBody ( json_encode($retorno) );
	}
	
	/**
	* Metodo que retorna os dados para montagem dos graficos do dashboard
	* 
	* @return json
	*/
	public function dashboardChart(){
		$retorno = new \stdClass();
		$retorno->BAR['label'] = NULL;
		$retorno->BAR['value'] = NULL;
		$retorno->PIE['label'] = NULL;
		$retorno->PIE['value'] = NULL;
		
		//se for administrador usarah a filtragem de tela
		//senao usarah apenas a loja do usuario
		if($this->Auth->user()['role']=="admin"){
			$lojas = $this->request->getData("LOJAS");
		}else{
			$lojas[] = $this->Auth->user()['storeid'];
		}
		$cats  = $this->request->getData("CATEGORIAS");
		$forns = $this->request->getData("FORNECEDORES");
		$dtIni = $this->request->getData("DATA_INICIAL");
		$dtFim = $this->request->getData("DATA_FINAL");
		
		//verifica se foram passados os parametros de data
		if($dtIni!="" && $dtFim!=""){
			$dtIni = $this->dateToDatabase($dtIni);
			$dtFim = $this->dateToDatabase($dtFim);
		}else{
			//monta a data padrao sendo deste o primeiro dia do ano atual ateh a data de hoje
			$dtIni = date("Y-01-01");
			$dtFim = date("Y-m-d");
		}
		
		//monta a subquery de data que sempre serah utilizada
		$qryDate = TableRegistry::get('LojVenda')->find()->select(['IDVENDA'])
			->where(function($exp) use($dtIni,$dtFim){ 
				return $exp->between('DATE(DATA_VENDA)',$dtIni,$dtFim); 
			});
		
		//verifica se o parametro de categorias foi passado
		if(is_array($cats)){
			$qryCat = TableRegistry::get('SysCategoriaProduto')->find()->select(['IDPRODUTO'])
				->where(function($exp) use ($cats){ return $exp->in('IDCATEGORIA',$cats); });
		}
		
		//verifica se o parametro de fornecedores foi passado
		if(is_array($forns)){
			$qryForn = TableRegistry::get('SysProduto')->find()->select(['IDPRODUTO'])
				->where(function($exp) use ($forns){ return $exp->in('IDFORNECEDOR',$forns); });
		}
		
		/*********************** INICIO MEIOS DE PAGAMENTO ***************************/
		$qryPie = TableRegistry::get('LojVenda')->find()
			->select(['COND_PAGAMENTO' => 'SCP.NOME','TOTAL' => 'SUM(LVP.VALOR-TROCO)'])
			->join([
				'LVP' => [
					'table' => 'loj_venda_pagamento',
					'type'  => 'inner',
					'conditions' => 'LVP.IDVENDA=LojVenda.IDVENDA'
				],
				'SCP' => [
					'table' => 'sys_condicao_pagamento',
					'type'  => 'inner',
					'conditions' => 'SCP.IDCONDICAOPAGAMENTO=LVP.IDCONDICAOPAGAMENTO'
				]
			])->where(function($exp){ return $exp->notIn('LVP.IDCONDICAOPAGAMENTO',['-1','12']); })
			->where(function($exp) use($qryDate){ return $exp->in('LojVenda.IDVENDA',$qryDate); });
			
		if(is_array($lojas)){
			$qryPie->where(function($exp) use ($lojas){ return $exp->in('IDLOJA',$lojas); });
		}
		
		if(is_array($cats)){ $qryPie->where(['IDPRODUTO IN',$qryCat]); }
		
		if(is_array($forns)){ $qryPie->where(['IDPRODUTO IN',$qryForn]); }
		
		$qryPie->group('SCP.IDCONDICAOPAGAMENTO');
		$qryPie->order(['SUM(LVP.VALOR-TROCO)' => 'DESC']);
		
		//print($qryPie);
		
		if($qryPie && $qryPie->count()>0){ 
			foreach($qryPie as $cond){				
				$retorno->PIE['label'][] =  $cond->COND_PAGAMENTO;
				$retorno->PIE['value'][] =  $cond->TOTAL;
			}
		}
		
		/*********************** FIM DOS MEIOS DE PAGAMENTO ***************************/
		
		/*********************** INICIO VENDAS ANO ***************************/
		$qryBar = TableRegistry::get('LojVenda')->find()
			->select([
				'MES'   => 'MONTH(DATA_VENDA)',
				'TOTAL' => 'SUM(LVP.VALOR-(TROCO/(SELECT COUNT(IDVENDA) FROM loj_venda_pagamento WHERE IDVENDA=LVP.IDVENDA)))'
			])
			->join([
				'alias' => 'LVP',
				'table' => 'loj_venda_pagamento',
				'type'  => 'inner',
				'conditions' => 'LVP.IDVENDA=LojVenda.IDVENDA'
			])->where(function($exp){ return $exp->notIn('LVP.IDCONDICAOPAGAMENTO',['-1','12']); })
			->where(function($exp) use($qryDate){ return $exp->in('LojVenda.IDVENDA',$qryDate); });
			
		if(is_array($lojas)){
			$qryBar->where(function($exp) use ($lojas){ return $exp->in('IDLOJA',$lojas); });
		}
		
		if(is_array($cats)){ $qryBar->where(['IDPRODUTO IN',$qryCat]); }
		
		if(is_array($forns)){ $qryBar->where(['IDPRODUTO IN',$qryForn]); }
		
		$qryBar->group('MONTH(DATA_VENDA)');
		
		
		if($qryBar && $qryBar->count()>0){
			foreach($qryBar as $venda){				
				$retorno->BAR['label'][] = $venda->MES;
				$retorno->BAR['value'][] = $venda->TOTAL;
			}
		}
		/*********************** FIM DAS VENDAS ANO ***************************/
		
		return $this->response->withStringBody( json_encode($retorno) );
	}
	
	/**
	* Metodo que exibe a pagina inicial do fluxo de caixa
	* 
	* @return null
	*/
	public function cashFlow(){
		$this->viewBuilder()->setLayout("gallery");
		//busca os anos que existem no fluxo de caixa
		$fcaixa = TableRegistry::get('LojFluxoCaixa')->find()
			->select(['ANO' => 'YEAR(DATA_MOVIMENTO)'])
			->group(['ANO'])
			->order(['ANO' => 'ASC']);
			
		//se o usuario nao for adminstrador entao
		//usarah a sua loja
		if($this->Auth->user()['role']!="admin"){
			$fcaixa->where(['IDLOJA' => $this->Auth->user()['storeid']]);
		}
		
		$this->set('years',$fcaixa);
		
		//define a url de busca dos dados
		$this->set('url_data','/report/cash_flow_data');
	}
	
	/**
	* Metodo que realiza busca da informacao do fluxo de caixa
	* 
	* @return null
	*/
	public function cashFlowData($_year=""){
		
		if($_year==""){
			$_year = TableRegistry::get('LojFluxoCaixa')->find()->select(['ANO' => 'MIN(YEAR(DATA_MOVIMENTO))'])->first()->ANO;
		}
		
		$results = null;
		
		$sql = "SELECT CASE OPF.TIPO_OPERACAO WHEN 'I' THEN 1 WHEN 'E' THEN 2 ELSE 3 END AS ORD_TYPE,ORDEM,OPF.IDOPERACAOFINANCEIRA AS IDOPERACAOFINANCEIRA,NOME,TIPO_OPERACAO,"
                . "COALESCE(SUM(CASE WHEN MONTH(DATA_ENTRADA)=1 THEN VALOR END),0) AS `JAN`,"
                . "COALESCE(SUM(CASE WHEN MONTH(DATA_ENTRADA)=2 THEN VALOR END),0) AS `FEV`,"
                . "COALESCE(SUM(CASE WHEN MONTH(DATA_ENTRADA)=3 THEN VALOR END),0) AS `MAR`,"
                . "COALESCE(SUM(CASE WHEN MONTH(DATA_ENTRADA)=4 THEN VALOR END),0) AS `ABR`,"
                . "COALESCE(SUM(CASE WHEN MONTH(DATA_ENTRADA)=5 THEN VALOR END),0) AS `MAI`,"
                . "COALESCE(SUM(CASE WHEN MONTH(DATA_ENTRADA)=6 THEN VALOR END),0) AS `JUN`,"
                . "COALESCE(SUM(CASE WHEN MONTH(DATA_ENTRADA)=7 THEN VALOR END),0) AS `JUL`,"
                . "COALESCE(SUM(CASE WHEN MONTH(DATA_ENTRADA)=8 THEN VALOR END),0) AS `AGO`,"
                . "COALESCE(SUM(CASE WHEN MONTH(DATA_ENTRADA)=9 THEN VALOR END),0) AS `SET`,"
                . "COALESCE(SUM(CASE WHEN MONTH(DATA_ENTRADA)=10 THEN VALOR END),0) AS `OUT`,"
                . "COALESCE(SUM(CASE WHEN MONTH(DATA_ENTRADA)=11 THEN VALOR END),0) AS `NOV`,"
                . "COALESCE(SUM(CASE WHEN MONTH(DATA_ENTRADA)=12 THEN VALOR END),0) AS `DEZ` "
                . "FROM `loj_fluxo_caixa` OV "
                . "INNER JOIN `sys_operacao_financeira` OPF on OPF.IDOPERACAOFINANCEIRA=OV.IDOPERACAOFINANCEIRA "
                . "WHERE YEAR(DATA_MOVIMENTO)=$_year ".(($this->Auth->user()['role']!="admin")?" AND IDLOJA=".$this->Auth->user()['storeid']." ":"")
                . "GROUP BY IDOPERACAOFINANCEIRA ORDER BY 1,2";
        $connection = ConnectionManager::get('default');
        $results = $connection->execute($sql)->fetchAll('assoc');
        
        $this->set('data_list',$results);
        
        
        $this->set('flux_data',TableRegistry::get('LojFluxoCaixa')->find()->select(['IDOPERACAOFINANCEIRA','DATA_MOVIMENTO','VALOR','HISTORICO'])->where(['YEAR(DATA_MOVIMENTO)' => $_year]));
	}
	
	/**
	* Metodo que exibe a pagina inicial do relatorio
	* de recebimentos por dia
	* 
	* @return null
	*/
	public function dayReceipts(){
		$this->set('title','Recebimentos por dia');
		
	}
	
	/**
	* Metodo que realiza a busca das informacoes do relatorio
	* de recebimentos por dia
	* 
	* @return null
	*/
	public function dayReceiptsData(){
		$user = $this->Auth->user();
        
        //formata a data para busca
        if($this->request->getData("DATA")==""){
            $_date = date("Y-m-d");
        }else{
            $_date = $this->dateToDatabase($this->request->getData("DATA"),true);
        }
        
        //se o usuario nao for administrador buscarah a loja dele
        $idloja = ($user['storeid']==0)?"":$user['storeid'];
        
        $query = TableRegistry::get('LojVenda')->find();
        $query->select(['CONDICAO_PAGAMENTO' => 'SCP.NOME','TOTAL' => $query->func()->sum('LVP.VALOR-TROCO')])
        	->join([
        		'LVP' =>[
        			'table' => 'loj_venda_pagamento',
        			'type'  => 'inner',
        			'conditions' => 'LVP.IDVENDA=LojVenda.IDVENDA'
        		],
        		'SCP' =>[
        			'table' => 'sys_condicao_pagamento',
        			'type'  => 'inner',
        			'conditions' => 'SCP.IDCONDICAOPAGAMENTO=LVP.IDCONDICAOPAGAMENTO'
        		]
        	])
        	->where(['DATE(DATA_VENDA)' => $_date])
        	->where(function($exp){
        		return $exp->notIn('LVP.IDCONDICAOPAGAMENTO',['-1','12']);
        	});
        if($idloja!=""){
        	$query->where(['LojVenda.IDLOJA' => $idloja]);
        }
        $query->group('SCP.IDCONDICAOPAGAMENTO');
        $query->order(['TOTAL' => 'ASC']);

        $this->set('data_list',$query);
	}
	
	/**
	* Metodo que exibe a pagina inicial do relatorio
	* de produtos por dia
	* 
	* @return null
	*/
	public function dayProducts(){
		$this->set('title','Produto(s) por Dia');
	}
	
	/**
	* Metodo que realiza a busca das informacoes do relatorio
	* de produtos por dia
	* 
	* @return null
	*/
	public function dayProductsData(){
		$user = $this->Auth->user();
        
        //se o usuario nao for administrador buscarah a loja dele
        $idloja = ($user['storeid']==0)?"":$user['storeid'];
        
        //formata a data para busca
        if($this->request->getData("DATA")==""){
            $_date = date("Y-m-d");
        }else{
            $_date = $this->dateToDatabase($this->request->getData("DATA"),true);
        }
        
        $query = TableRegistry::get('LojVendaProduto')->find();
        $query->select(['IDVENDA' => 'V.IDLOJA','NOME_PRODUTO','QUANTIDADE'])
        	->join([
        		'alias' => 'V',
        		'table' => 'loj_venda',
        		'type'  => 'inner',
        		'conditions' => 'V.IDVENDA=LojVendaProduto.IDVENDA'
        	])
        	->where(['DATE(V.DATA_VENDA)' => $_date])
        	->where(function($exp){
        		return $exp->notIn('V.IDVENDA',['(SELECT IDVENDA FROM loj_venda_pagamento WHERE IDCONDICAOPAGAMENTO=-1) ORDER BY IDVENDA ASC']);
        	});
        
        //filtra a loja
        if($idloja!=""){
			$query->where(['V.IDLOJA' => $idloja]);
		}
        
        $this->set('data_list',$query);
	}
	
	/**
	* Metodo que exibe a pagina inicial do relatorio
	* de meios de pagamento por dia
	* 
	* @return null
	*/
	public function dayPaymentOptions(){
		$this->set('title','Meios de Pagamento por dia');
	}
	
	/**
	* Metodo que realiza a busca das informacoes do relatorio
	* de meios de pagamento por dia
	* 
	* @return null
	*/
	public function dayPaymentOptionsData(){
		$user = $this->Auth->user();
        
        //formata a data para busca
        if($this->request->getData("DATA")==""){
            $_date = date("Y-m-d");
        }else{
            $_date = $this->dateToDatabase($this->request->getData("DATA"),true);
        }
        
        //se o usuario nao for administrador buscarah a loja dele
        $idloja = ($user['storeid']==0)?"":$user['storeid'];
        
        $query = TableRegistry::get('LojVenda')->find();
        $query->select(['MEIO_PAGAMENTO' => 'SMP.NOME','TOTAL' => $query->func()->sum('LVP.VALOR-TROCO')])
        	->join([
        		'LVP' =>[
        			'table' => 'loj_venda_pagamento',
        			'type'  => 'inner',
        			'conditions' => 'LVP.IDVENDA=LojVenda.IDVENDA'
        		],
        		'SCP' =>[
        			'table' => 'sys_condicao_pagamento',
        			'type'  => 'inner',
        			'conditions' => 'SCP.IDCONDICAOPAGAMENTO=LVP.IDCONDICAOPAGAMENTO'
        		],
        		'SMC' => [
        			'table' => 'sys_meio_condicao',
        			'type'  => 'inner',
        			'conditions' => 'SMC.IDCONDICAOPAGAMENTO=SCP.IDCONDICAOPAGAMENTO'
        		],
        		'SMP' => [
        			'table' => 'sys_meio_pagamento',
        			'type'  => 'inner',
        			'conditions' => 'SMP.IDMEIOPAGAMENTO=SMC.IDMEIOPAGAMENTO'
        		]
        	])
        	->where(['DATE(DATA_VENDA)' => $_date])
        	->where(function($exp){
        		return $exp->notIn('LVP.IDCONDICAOPAGAMENTO',['-1','12']);
        	});
        
        //filtra a loja
        if($idloja!=""){
        	$query->where(['LojVenda.IDLOJA' => $idloja]);
        }
        $query->group('SMP.IDMEIOPAGAMENTO');
        $query->order(['TOTAL' => 'ASC']);

        $this->set('data_list',$query);
	}
	
	/**
	* Metodo que exibe a pagina inicial do relatorio de estoque
	* por loja
	* 
	* @return null
	*/
	public function stockByStore(){
		$this->set('stores',
			TableRegistry::get('SysLoja')->find()
			->select(['IDLOJA','NOME'])
			->where(function($exp){ 
				return $exp->notIn('IDLOJA',TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR); 
			})
		);
		
		$this->set('title','Estoque por loja');
	}
	
	/**
	* Metodo que realiza a busca das informacoes do saldo de estoque
	* por loja
	* 
	* @return null
	*/
	public function stockByStoreData(){
		
		if($this->request->getData("PERIODO")!=""){
			$ano = explode("-",$this->request->getData("PERIODO"))[1];
			$mes = explode("-",$this->request->getData("PERIODO"))[0];
		}else{
			$ano = date("Y"); $mes = date("m");
		}
		
		$query = TableRegistry::get('LojEstoqueSaldo')->find()
		->select(['NOME_PRODUTO','QUANTIDADE' => 'VALOR_ESTOQUE_FINAL'])
		->where(['ANO' => $ano,'MES' => $mes]);
		
		//realiza o filtro da loja
		$query->where(['IDLOJA' => $this->request->getData("LOJA")]);
		
		$this->set('data_list',$query);
	}
	
	/**
	* Metodo que exibe a pagina inicial do relatorio de estoque
	* por tipo de produto
	* 
	* @return null
	*/
	public function stockByType(){
		$this->set('types',
			TableRegistry::get('SysProdutoTipo')->find()
			->select(['IDPRODUTOTIPO','DESCRICAO'])
		);
		
		$this->set('title','Estoque por tipo de produto');
	}
	
	/**
	* Metodo que realiza a busca das informacoes do saldo de estoque
	* por tipo de produto
	* 
	* @return null
	*/
	public function stockByTypeData(){
		
		if($this->request->getData("PERIODO")!=""){
			$ano = explode("-",$this->request->getData("PERIODO"))[1];
			$mes = explode("-",$this->request->getData("PERIODO"))[0];
		}else{
			$ano = date("Y"); $mes = date("m");
		}
		
		$query = TableRegistry::get('LojEstoqueSaldo')->find()
		->select(['NOME_PRODUTO','QUANTIDADE' => 'VALOR_ESTOQUE_FINAL'])
		->where(['ANO' => $ano,'MES' => $mes]);
		
		//realiza o filtro do tipo de produto
		$query->where(function($exp){
			return $exp->in('IDPRODUTO',
				TableRegistry::get('SysProduto')->find()->select(['IDPRODUTO'])->where(['IDPRODUTOTIPO' => $this->request->getData("TIPO_PRODUTO")])
			);
		});
		
		//busca apenas a loja do usuario quando nao for administrador
		if($this->Auth->user()['role']!="admin"){
			$query->where(['IDLOJA' => $this->Auth->user()['storeid']]);
		}
		
		$this->set('data_list',$query);
	}
	
	/**
	* Metodo que exibe a pagina inicial do relatorio de estoque
	* por categoria
	* 
	* @return null
	*/
	public function stockByCategory(){
		$this->set('categories',
			TableRegistry::get('SysCategoria')->find()
			->select(['IDCATEGORIA','NOME'])
			->where(['CATEGORIA_PAI',null])
		);
		
		$this->set('title','Estoque por categoria de produto');
	}
	
	/**
	* Metodo que realiza a busca das informacoes do saldo de estoque
	* por categoria
	* 
	* @return null
	*/
	public function stockByCategoryData(){
		
		if($this->request->getData("PERIODO")!=""){
			$ano = explode("-",$this->request->getData("PERIODO"))[1];
			$mes = explode("-",$this->request->getData("PERIODO"))[0];
		}else{
			$ano = date("Y"); $mes = date("m");
		}
		
		$query = TableRegistry::get('LojEstoqueSaldo')->find()
		->select(['NOME_PRODUTO','QUANTIDADE' => 'VALOR_ESTOQUE_FINAL'])
		->where(['ANO' => $ano,'MES' => $mes]);
		
		//realiza o filtro da categoria
		$query->where(function($exp){
			return $exp->in('IDPRODUTO',
				TableRegistry::get('SysCategoriaProduto')->find()->select(['IDPRODUTO'])->where(['IDCATEGORIA' => $this->request->getData("CATEGORIA")])
			);
		});
		
		//busca apenas a loja do usuario quando nao for administrador
		if($this->Auth->user()['role']!="admin"){
			$query->where(['IDLOJA' => $this->Auth->user()['storeid']]);
		}
		
		$this->set('data_list',$query);
	}
	
	/**
	* Metodo que exibe a pagina inicial do relatorio de estoque
	* por fornecedor
	* 
	* @return null
	*/
	public function stockByProvider(){
		$this->set('providers',
			TableRegistry::get('SysFornecedor')->find()
			->select(['IDFORNECEDOR','FANTASIA'])
		);
		
		$this->set('title','Estoque por fornecedor');
	}
	
	/**
	* Metodo que realiza a busca das informacoes do saldo de estoque
	* por fornecedor
	* 
	* @return null
	*/
	public function stockByProviderData(){
		
		if($this->request->getData("PERIODO")!=""){
			$ano = explode("-",$this->request->getData("PERIODO"))[1];
			$mes = explode("-",$this->request->getData("PERIODO"))[0];
		}else{
			$ano = date("Y"); $mes = date("m");
		}
		
		$query = TableRegistry::get('LojEstoqueSaldo')->find()
		->select(['NOME_PRODUTO','QUANTIDADE' => 'VALOR_ESTOQUE_FINAL'])
		->where(['ANO' => $ano,'MES' => $mes]);
		
		//realiza o filtro do fornecedor
		$query->where(function($exp){
			return $exp->in('IDPRODUTO',
				TableRegistry::get('SysProduto')->find()->select(['IDPRODUTO'])->where(['IDFORNECEDOR' => $this->request->getData("FORNECEDOR")])
			);
		});
		
		$this->set('data_list',$query);
	}
}