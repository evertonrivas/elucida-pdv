<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class FinancialController extends AppController{
    
    public function initialize() {
        parent::initialize();
        $this->loadComponent("Paginator");
        $this->loadComponent("Filter");
        $this->loadComponent("Ofxparser");
        ob_start("ob_gzhandler");
    }
    
    /**
	* Metodo que exibe o calendario de contas a pagar
	* 
	* @return null
	*/
    public function calendar(){
    	$user = $this->Auth->user();
    	
    	$this->set('title',"Calend&aacute;rio de Contas &agrave; Pagar".(($user['role']!="admin")?" da Loja":"") );
    	
        
        $tblDesp = TableRegistry::get('SysTipoDespesa');
        
        $despesas = $tblDesp->find();
        if($user['role']!="admin"){
            $despesas->where(['EXIBE_LOJA' => 1]);
        }
        $despesas->order(['NOME' => 'ASC']);
        $this->set('despesalist',$despesas);
    }
    
    /**
	* Metodo que verifica se o dia desejado eh um dia util
	* @param int $mes numero do mes
	* @param int $ano numero do ano
	* @param string $dia_util_desejado numero do dia (usar UDU para ultimo dia utili )
	* 
	* @return int $dia retorna o numero do dia do mes
	*/
    private function diaUtil($mes,$ano,$dia_util_desejado){
        $diaUtil = 1;
        
        //verifica se eh o ultimo dia util
        if($dia_util_desejado!="UDU"){
            for($i=0;$i<cal_days_in_month(CAL_GREGORIAN, $mes, $ano);$i++){
                $dia = $i+1;
                $diaSemana = date('w', mktime(0,0,0,$mes,$dia, $ano));
                //verifica se o dia nao eh segunda nem domingo
                if ($diaSemana != 0 && $diaSemana != 6) {
                    if ($diaUtil < $dia_util_desejado) {
                        $diaUtil++;
                    } else {
                        break;
                    }
                }
            }
        }else{
            $dias = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
            $ultimo = mktime(0, 0, 0, $mes, $dias, $ano); 
            $dia = date("j", $ultimo);
            $dia_semana = date("w", $ultimo);

            // domingo = 0;
            // sábado = 6;
            // verifica sábado e domingo
            //se for domingo joga a conta para sexta
            if($dia_semana == 0){
              $dia--;$dia--;
            }
            //se for sabado joga a conta para sexta
            if($dia_semana == 6){
              $dia--;
            }
        }
        return $dia;	
    }
    
    /**
	* Metodo que busca as datas para salvar os eventos futuros
	* @param string $data_vencimento dia do vencimento da conta
	* @param string $repeticaok qual serah a repeticao seguindo 
	* os seguintes codigos: 
	* 5DU = quinto dia util
	* D01 = dia primeiro do mes
	* D05 = dia 5 do mes
	* D10 = dia 10 do mes
	* D15 = dia 15 do mes
	* D20 = dia 20 do mes
	* D25 = dia 25 do mes
	* UDU = ultimo dia util
	* 
	* @return
	*/
    private function _get_datas($data_vencimento,$repeticao){

        $datas = NULL;
        if($repeticao==""){
            $datas[] = $data_vencimento;
        }
        else{
            
            $mes = substr($data_vencimento, 5,2);
            
            switch($repeticao){
                case '5DU' : {
                    //faz o loop do mes atual ate o ultimo mes do ano
                    for($i=(int)$mes;$i<13;$i++){
                        $datas[] = date("Y")."-".str_pad($i, 2, '0', STR_PAD_LEFT)."-".$this->diaUtil($i,date("Y"), 5);
                    }
                }break;
                case 'D01' : {
                    for($i=(int)$mes;$i<13;$i++){
                        $datas[] = date("Y")."-".str_pad($i,2,'0',STR_PAD_LEFT)."-01";
                    }
                }
                case 'D05' : {
                    for($i=(int)$mes;$i<13;$i++){
                        $datas[] = date("Y")."-".str_pad($i,2,'0',STR_PAD_LEFT)."-05";
                    }
                }break;
                case 'D10' : {
                    for($i=(int)$mes;$i<13;$i++){
                        $datas[] = date("Y")."-".str_pad($i,2,'0',STR_PAD_LEFT)."-10";
                    }
                }
                case 'D15' : {
                    for($i=(int)$mes;$i<13;$i++){
                        $datas[] = date("Y")."-".str_pad($i,2,'0',STR_PAD_LEFT)."-15";
                    }
                }break;
                case 'D20' : {
                    for($i=(int)$mes;$i<13;$i++){
                        $datas[] = date("Y")."-".str_pad($i,2,'0',STR_PAD_LEFT)."-20";
                    }
                }break;
                case 'D25' : {
                    for($i=(int)$mes;$i<13;$i++){
                        $datas[] = date("Y")."-".str_pad($i,2,'0',STR_PAD_LEFT)."-25";
                    }
                }break;
                case 'UDU' : {
                    //faz o loop do mes atual ate o ultimo mes do ano
                    for($i=(int)$mes;$i<13;$i++){
                        $datas[] = date("Y")."-".str_pad($i, 2, '0', STR_PAD_LEFT)."-".$this->diaUtil($i,date("Y"), $repeticao);
                    }
                }break;
            }
        }
        return $datas;
    }
    
    /**
	* Metodo que busca quais sao os eventos que irao aparecer no mes desejado
	* 
	* @return json um array dos eventos do mes para exibicao no calendario 
	*/
    public function calendarGetEvents(){
        $tblCap = TableRegistry::get('LojContasPagar');
        $eventos = NULL;
        
        $contas = $tblCap->find()->where(function($exp,$q){
            return $exp->between('DATA_VENCIMENTO',$this->request->query("start"),$this->request->query("end"));
        });
        
        if($this->Auth->user()['role']!="admin"){
			$contas->where(['IDLOJA' => $this->Auth->user()['storeid']]);
		}
        
        foreach($contas as $conta){        	
        	
        	//Monta a cor do evento conforme a sua situacao
        	//por padrao todos os eventos em aberto serah amarelos
        	$color = "yellow";
        	$texto = "black";
	        if($conta->DATA_VENCIMENTO->format("d")<=date("d")){
	        	$texto = "white";
				$color = ($conta->DATA_PAGAMENTO==NULL)?"red":"green";
			}elseif($conta->DATA_PAGAMENTO!=NULL){
				$texto = "white";
				$color = "green";
			}
        	
            $event = new \stdClass();
            $event->id    = $conta->IDCONTASPAGAR;
            $event->title = $conta->OBSERVACAO;
            $event->start = $conta->DATA_VENCIMENTO->format("Y-m-d H:i:s");
            $event->end   =  date('Y-m-d H:i:s', strtotime('+1 hours', strtotime( $conta->DATA_VENCIMENTO->format("Y-m-d H:i:s") )));
            $event->backgroundColor = $color; //se o evento estiver aberto eh vermelho
            $event->borderColor     = $color; //se o evento estiver aberto eh vermelho
            $event->textColor       = $texto; //pinta o texto de branco
            $event->classNames      = "eventMouse"; //coloca o cursor hand no mouse
            $eventos[] = $event;
        }
        
        return $this->response->withStringBody( json_encode($eventos) );
    }
    
    /**
	* Metodo que busca os totais do calendario para exibicao no rodape
	* 
	* @return null
	*/
    public function calendarGetTotals(){
        $tblCap = TableRegistry::get('LojContasPagar')->find();
        
        //total Aberto no mes
        $this->set('total_month',$tblCap->select(['TOTAL' => $tblCap->func()->sum('VALOR_ORIGINAL')])
            ->where(function($exp,$q){
                return $exp->eq('YEAR(DATA_VENCIMENTO)',$this->request->getData("ano"));
            })
            ->where(function($exp,$q){
                return $exp->eq('MONTH(DATA_VENCIMENTO)',$this->request->getData("mes"));
            })
            ->where(['DATA_PAGAMENTO IS' => NULL])
            ->first()
        );
        
        $tblCap = TableRegistry::get('LojContasPagar')->find();
        
        //total aberto no dia
        $this->set('total_today',$tblCap->select(['TOTAL' => $tblCap->func()->sum("VALOR_ORIGINAL")])
            ->where(function($exp,$q){
                return $exp->eq('DATE(DATA_VENCIMENTO)',date("Y-m-d"));
            })
            ->where(['DATA_PAGAMENTO IS' => NULL])
            ->first()
        );
        
        $tblCap = TableRegistry::get('LojContasPagar')->find();
        
        //total aberto na semana
        $this->set('total_week',$tblCap->select(['TOTAL' => $tblCap->func()->sum('VALOR_ORIGINAL')])
            ->where(function($exp,$q){
                $day = date('w');
                $week_start = date('Y-m-d', strtotime('-'.$day.' days'));
                $week_end = date('Y-m-d', strtotime('+'.(6-$day).' days'));
                return $exp->between('DATE(DATA_VENCIMENTO)',$week_start,$week_end);
            })
            ->where(['DATA_PAGAMENTO IS' => NULL])
            ->first()
        );
    }

	/**
	* Metodo que cria um novo evento no calendario
	* 
	* @return boolean
	*/
    public function calendarEventNew(){
        $user    = $this->Auth->user();
        $tblCap  = TableRegistry::get('LojContasPagar');
        $retorno = false;
        
        $ideventopai = 0;
        
        //formata a data de vencimento
        $DATA_VENCIMENTO = $this->dateToDatabase($this->request->getData("DATA_VENCIMENTO"));
        //$DATA_VENCIMENTO = substr($this->request->getData("DATA_VENCIMENTO"), 6,4)."-".substr($this->request->getData("DATA_VENCIMENTO"),3,2)."-".substr($this->request->getData("DATA_VENCIMENTO"),0,2);
        
        //recebe quantas datas o evento terah (em caso de repeticao)
        $datas = $this->_get_datas($DATA_VENCIMENTO,$this->request->getData("REPETIR"));
        
        //conta o total de eventos que deverao ser registrados
        $total_eventos = count($datas);

		//varre o toal de eventos 
        for($i=0;$i<$total_eventos;$i++){

            //recebe a proxima hora disponivel do dia do pagamento
            $data_teste = $datas[$i];
            $hora_base = $tblCap->find()->select(['DATA_VENCIMENTO'])
                    ->where(function($exp,$q) use ($data_teste){ return $exp->eq('DATE(DATA_VENCIMENTO)',$data_teste); })
                    ->order(["DATA_VENCIMENTO" => "DESC"]);
            //se nao houver um primeiro evento no dia o primeiro comecarah no horario defindo nas opcoes do sistema
            if($hora_base->count()>0){
                $hora = str_pad($hora_base->first()->DATA_VENCIMENTO->format("H")+1, 2, '0', STR_PAD_LEFT);
            }else{
                $hora = TableRegistry::get('SysOpcao')->get("FIRST_TIME_EVENT")->OPCAO_VALOR;
            }

            $contas_pagar = $tblCap->newEntity();
            $contas_pagar->IDLOJA          = $user['storeid'];
            $contas_pagar->DATA_VENCIMENTO = "$datas[$i] $hora:00:00";
            $contas_pagar->IDTIPODESPESA   = $this->request->getData("IDTIPODESPESA");
            $contas_pagar->NUM_DOCUMENTO   = $this->request->getData("NUM_DOCUMENTO");
            $contas_pagar->VALOR_ORIGINAL  = str_replace(",","",$this->request->getData("VALOR_ORIGINAL"));
            $contas_pagar->OBSERVACAO      = mb_strtoupper($this->request->getData("OBSERVACAO"));
            $contas_pagar->GOOGLE_CALENDAR = $this->request->getData("GOOGLE_CALENDAR");
            $contas_pagar->TEM_REPETICAO   = ($this->request->getData("REPETIR")!="")?1:0;
            
            //se for uma conta jah finalizada, entao adiciona os dados da baixa
            if($this->request->getData("JA_BAIXADO")=="1"){
                $contas_pagar->VALOR_PAGO          = str_replace(",","",$this->request->getData("VALOR_ORIGINAL"));
                $contas_pagar->DATA_PAGAMENTO      = "$datas[$i] $hora:00:00";
                $contas_pagar->DIFERENCA_PAGAMENTO = 0;
            }

            //a partir do segundo evento adiciona o id do evento pai
            //para conseguir ter rastreabilidade
            if($i>0){
                $contas_pagar->IDEVENTOPAI = $ideventopai;
            }

			//salva o evento
            $retorno = $tblCap->save($contas_pagar)?true:false;
            
            //recebe o id do evento atual para o caso de haver repeticao
            if($i==0){
                $ideventopai = $contas_pagar->IDCONTASPAGAR;
            }
        }
        
        return $this->response->withStringBody( $retorno );
    }

	/**
	* Metodo que remove um ou mais eventos do calendario
	* Se o evento tiver eventos filhos eles serao removidos tambem
	* @param int $_idContasPagar
	* 
	* @return boolean
	*/
    public function calendarEventRemove($_idContasPagar){
        $retorno = false;
        $tblCap = TableRegistry::get('LojContasPagar');
        
        foreach($tblCap->find()->select(['IDCONTASPAGAR'])->where(['IDCONTASPAGAR' => $_idContasPagar])->orWhere(['IDEVENTOPAI' => $_idContasPagar]) as $cap){
            $del_cap = $tblCap->get($cap->IDCONTASPAGAR);
            $retorno = $tblCap->delete($del_cap)?true:false;
        }
   
        $this->response->withStringBody( $retorno );
    }

	/**
	* Metodo que busca as informacoes do codigo de barras de um boleto bancario
	* @param string $_linha_digitavel
	* 
	* @return objeto com as informacoes do boleto
	*/
    public function getBoletoInfo($_linha_digitavel = ""){

		//verifica se tem 44 caracteres na linha
        if(strlen($_linha_digitavel)==44){
            $valor = substr($_linha_digitavel,9,10);
            $valor_boleto = $valor/100;

            $dias = substr($_linha_digitavel,5,4);
        }//verifica se tem 47 caracteres na linha
        elseif(strlen($_linha_digitavel)==47){
            $valor = substr($_linha_digitavel,37,10);
            $valor_boleto = $valor/100;

            $dias = substr($_linha_digitavel,33,4);			
        }//outro tamanho nao serah possivel
        else{
            $valor_boleto = 0;
            $dias = 0;
        }

        $data = date_create("1997-10-07");
        date_add($data, date_interval_create_from_date_string("$dias days"));
        $data_vencimento = $data;

		//retorna o valor e a data de vencimento do boleto
        $retorno['VALOR']      = $valor_boleto;
        $retorno['VENCIMENTO'] = date_format($data_vencimento,'d/m/Y');

        echo json_encode($retorno);
    }
    
    /**
	* Metodo que busca as informacoes de um evento para exibicao
	* @param int $_idContasPagar codigo do evento no banco de dados
	* 
	* @return null
	*/
    public function calendarEventShow($_idContasPagar){
        $this->viewBuilder()->layout('gallery');
        
        //busca as informacoes da despesa
        $tblDespesa = TableRegistry::get('SysTipoDespesa');
        $conta = TableRegistry::get('LojContasPagar')->get($_idContasPagar);
        
        $this->set('conta',$conta);
        //busca as informacoes da despesa
        $this->set('despesa_conta',$tblDespesa->get($conta->IDTIPODESPESA));
        
        //busca as informacoes de pagamento da conta
        if($conta->IDTIPODESPESA_PAGAMENTO!=NULL){
            $this->set('despesa_pagamento',$tblDespesa->get($conta->IDTIPODESPESA_PAGAMENTO));
        }
        //busca a lista de tipos de despesa
        $this->set('despesalist',$tblDespesa->find()->order(['NOME' => 'ASC']));
    }
    
    /**
	* Metodo que monta a tela de edicao de um calendario
	* @param int $_idContasPagar codigo da conta no banco de dados
	* 
	* @return null
	*/
    public function calendarEventEdit($_idContasPagar){
        $this->viewBuilder()->layout('gallery');
        
        $tblDespesa = TableRegistry::get('SysTipoDespesa');
        $conta = TableRegistry::get('LojContasPagar')->get($_idContasPagar);
        
        $this->set('conta',$conta);
        $this->set('despesa_conta',$tblDespesa->get($conta->IDTIPODESPESA));
        if($conta->IDTIPODESPESA_PAGAMENTO!=NULL){
            $this->set('despesa_pagamento',$tblDespesa->get($conta->IDTIPODESPESA_PAGAMENTO));
        }
        $this->set('despesalist',$tblDespesa->find()->order(['NOME' => 'ASC']));
    }
    
    /**
	* Metodo que salva as informacoes de um evento que estah sendo editado
	* 
	* @return boolean
	*/
    public function calendarEventSave(){
        $tblCap = TableRegistry::get('LojContasPagar');
        
        $contas_pagar = $tblCap->get($this->request->getData("IDCONTASPAGAR"));
        if($this->request->getData("IDTIPODESPESA")!=""){
            $contas_pagar->IDTIPODESPESA = $this->request->getData("IDTIPODESPESA");
        }
        if($this->request->getData("NUM_DOCUMENTO")!=""){
            $contas_pagar->NUM_DOCUMENTO = $this->request->getData("NUM_DOCUMENTO");
        }
        if($this->request->getData("DATA_VENCIMENTO")!=""){
            $contas_pagar->DATA_VENCIMENTO = (substr($this->request->getData("DATA_VENCIMENTO"), 6,4)."-".substr($this->request->getData("DATA_VENCIMENTO"),3,2)."-".substr($this->request->getData("DATA_VENCIMENTO"),0,2))." ".$this->request->getData("HORA_VENCIMENTO");
        }
        if($this->request->getData("VALOR_ORIGINAL")!=""){
            $contas_pagar->VALOR_ORIGINAL  = str_replace(",","",$this->request->getData("VALOR_ORIGINAL"));
        }
        if($this->request->getData("DATA_PAGAMENTO")!=""){
            $contas_pagar->DATA_PAGAMENTO = substr($this->request->getData("DATA_PAGAMENTO"), 6,4)."-".substr($this->request->getData("DATA_PAGAMENTO"),3,2)."-".substr($this->request->getData("DATA_PAGAMENTO"),0,2);
        }
        if($this->request->getData("VALOR_PAGAMENTO")!=""){
            $contas_pagar->VALOR_PAGO = str_replace(",","",$this->request->getData("VALOR_PAGAMENTO"));
        }
        if($this->request->getData("TIPO_DESPESA_PAGAMENTO")!=""){
            $contas_pagar->DIFERENCA_PAGAMENTO = $contas_pagar->VALOR_PAGO - $contas_pagar->VALOR_ORIGINAL;
            $contas_pagar->IDTIPODESPESA_PAGAMENTO = $this->request->getData("TIPO_DESPESA_PAGAMENTO");
        }

        return $this->response->withStringBody( $tblCap->save($contas_pagar)?true:false );
    }

    /*****************************MEIO DE PAGAMENTO********************************/
    /**
	* Metodo que exibe os filtros do meio de pagamento
	* 
	* @return string
	*/
    public function paymentOptionFilter(){
    	
        $this->Filter->addFilter("Nome","TXT_PAYMENT_METHOD_SEARCH_NAME","text");
        
        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }
    
    /**
	* Metodo que exibe a tela principal da opcao de pagamento
	* 
	* @return null
	*/
    public function paymentOption(){
        $this->set('url_filter','/financial/payment_option_filter');
        $this->set('url_data','/financial/payment_option_data');
    }
    
    /**
	* Metodo que monta os dados para exibicao
	* 
	* @return null
	*/
    public function paymentOptionData(){
        $tblStatement = TableRegistry::get('SysMeioPagamento');
        
        $query = $tblStatement->find();
        $query->select(['IDMEIOPAGAMENTO','NOME','CODIGO_NFE']);
        
        if($this->request->getData("TXT_PAYMENT_METHOD_SEARCH_NAME")!=""){
            $query->where(function ($exp){
                return $exp->like('NOME','%'.$this->request->getData('TXT_PAYMENT_METHOD_SEARCH_NAME').'%');
            });
        }        
        
        $this->set('data_list',$this->paginate($query,['limit' => 10]));
    }

	/**
	* Metodo que exibe a tela de meio pagamento para criacao ou edicao
	* @param int $_idMeioPagamento codigo do meio de pagamento
	* 
	* @return null
	*/
    public function paymentOptionCreate($_idMeioPagamento=""){
        if($_idMeioPagamento!=""){
            $this->set('meio',TableRegistry::get('SysMeioPagamento')->get($_idMeioPagamento));
            $this->set('meio_condicoes',TableRegistry::get('SysMeioCondicao')->find()->where(['IDMEIOPAGAMENTO' => $_idMeioPagamento]));
        }
        $this->set('condicoes',TableRegistry::get('SysCondicaoPagamento')->find());
    }

	/**
	* Metodo que salva o meio de pagmento
	* 
	* @return boolean
	*/
    public function paymentOptionSave(){
        $retorno      = false;
        $tblMethod    = TableRegistry::get('SysMeioPagamento');
        $tblMethodCnd = TableRegistry::get('SysMeioCondicao');
        
        if($this->request->getData("IDMEIOPAGAMENTO")!=""){
            $meio = $tblMethod->get($this->request->getData("IDMEIOPAGAMENTO"));
            $tblMethodCnd->deleteAll(['IDMEIOPAGAMENTO' => $this->request->getData("IDMEIOPAGAMENTO")]);
        }else{
            $meio = $tblMethod->newEntity();
        }
        
        $meio->NOME       = mb_strtoupper($this->request->getData("NOME"));
        $meio->CODIGO_NFE = $this->request->getData("CODIGO_NFE");
        
        if($tblMethod->save($meio)){
            $condicoes = array(); 
            parse_str($this->request->getData("CONDICOES"),$condicoes);
            foreach($condicoes['chCondicao'] as $key){
                $meio_cnd = $tblMethodCnd->newEntity();
                $meio_cnd->IDMEIOPAGAMENTO     = $meio->IDMEIOPAGAMENTO;
                $meio_cnd->IDCONDICAOPAGAMENTO = $key;
                $tblMethodCnd->save($meio_cnd);
            }
            $retorno = true;
        }
        else{
            $retorno = false;
        }
        return $this->response->withStringBody( $retorno );
    }

    /*****************************CONDICAO DE PAGAMENTO********************************/
    /**
	* Metodo que monta o filtro de busca da condicao de pagamento
	* 
	* @return string
	*/
    public function paymentConditionFilter(){

        $this->Filter->addFilter("Nome","TXT_PAYMENT_CONDITION_SEARCH_NAME","text");
        
        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }
    
    /**
	* Metodo da pagina principal das condicoes de pagamento
	* 
	* @return null
	*/
    public function paymentCondition(){		
        $this->set('url_filter','/financial/payment_condition_filter');
        $this->set('url_data','/financial/payment_condition_data');
    }
    
    /**
	* Metodo que monta os dados da listagem das condicoes de pagamento
	* 
	* @return null
	*/
    public function paymentConditionData(){
        $tblCondition = TableRegistry::get('SysCondicaoPagamento');
        
        $query = $tblCondition->find();
        $query->select(['IDCONDICAOPAGAMENTO','NOME','DIAS_RECEBIMENTO','ATALHO','EXIBIR_PDV','TAXA_ADM']);
        
        if($this->request->getData("TXT_PAYMENT_CONDITION_SEARCH_NAME")!=""){
            $query->where(function ($exp){
                return $exp->like('NOME','%'.$this->request->getData('TXT_PAYMENT_CONDITION_SEARCH_NAME').'%');
            });
        }
        
        $this->set('data_list',$this->paginate($query,['limit' => 10]));
    }

	/**
	* Metodo que exibe a tela de cadastro ou edicao de uma condicao de pagamento
	* @param int $_idCondicao codigo da condicao de pagamento
	* 
	* @return null
	*/
    public function paymentConditionCreate($_idCondicao=""){
        if($_idCondicao!=""){
            $this->set('condicao',TableRegistry::get('SysCondicaoPagamento')->get($_idCondicao));
        }
    }

	/**
	* Metodo que salva as informacoes de uma condicao de pagamento
	* 
	* @return boolean
	*/
    public function paymentConditionSave(){
        $this->autoRender = false;
        
        $tblCond = TableRegistry::get('SysCondicaoPagamento');
        
        //verifica se eh edicao ou novo registro
        if($this->request->getData("IDCONDICAOPAGAMENTO")!=""){
            $condicao = $tblCond->get($this->request->getData("IDCONDICAOPAGAMENTO"));
        }else{
            $condicao = $tblCond->newEntity();
        }
        $condicao->NOME             = mb_strtoupper($this->request->getData("NOME"));
        $condicao->PARCELAS         = $this->request->getData("PARCELAS");
        $condicao->DIAS_RECEBIMENTO = $this->request->getData("DIAS_RECEBIMENTO");
        $condicao->ATALHO           = (($this->request->getData("ATALHO")!="")?mb_strtoupper($this->request->getData("ATALHO")):NULL);
        $condicao->EXIBIR_PDV       = $this->request->getData("EXIBIR_PDV");
        $condicao->TAXA_ADM         = $this->request->getData("TAXA_ADM");
        
        return $this->response->withStringBody( ($tblCond->save($condicao))?true:false );
    }

    /*****************************BANDEIRAS DE CARTÕES*******************************/
    /**
	* Metodo que monta os filtros das bandeiras de cartao
	* 
	* @return string
	*/
    public function cardFlagFilter(){
        $this->Filter->addFilter("Nome","TXT_CARD_FLAG_SEARCH_NAME","text");
        
        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }
    
    /**
	* Metodo da pagina principal das bandericas de cartao
	* 
	* @return null
	*/
    public function cardFlag(){		
        $this->set('url_filter','/financial/card_flag_filter');
        $this->set('url_data','/financial/card_flag_data');
    }
    
    /**
	* Metodo que monta os dados da busca das bandeiras de cartao
	* 
	* @return null
	*/
    public function cardFlagData(){
        $tblFlag = TableRegistry::get('SysBandeiraCartao');
        
        $query = $tblFlag->find();
        $query->select(['IDBANDEIRA','NOME','ICONE']);
        
        if($this->request->getData("TXT_CARD_FLAG_SEARCH_NAME")!=""){
            $query->where(function ($exp){
                return $exp->like('NOME','%'.$this->request->getData('TXT_CARD_FLAG_SEARCH_NAME').'%');
            });
        }
        
        $this->set('data_list',$this->paginate($query,['limit' => 10]));
    }

	/**
	* Metodo exibe a tela para cadastro ou edicao de bandeira de cartao
	* @param int $_idBandeira codigo da bandeira de cartao
	* 
	* @return null
	*/
    public function cardFlagCreate($_idBandeira=""){
        if($_idBandeira!=""){
            $this->set('bandeira',TableRegistry::get('SysBandeiraCartao')->get($_idBandeira));
        }
        $this->set('meios',TableRegistry::get('SysMeioPagamento')->find());
    }

	/**
	* Metodo que salva os dados de uma bandeira de cartao
	* 
	* @return boolean
	*/
    public function cardFlagSave(){
		
        $tblFlag = TableRegistry::get('SysBandeiraCartao');
        if($this->request->getData("IDBANDEIRA")!=""){
            $flag = $tblFlag->get($this->request->getData("IDBANDEIRA"));
        }else{
            $flag = $tblFlag->newEntity();
        }
        $flag->IDMEIOPAGAMENTO = $this->request->getData("IDMEIOPAGAMENTO");
        $flag->NOME            = mb_strtoupper($this->request->getData("NOME"));
        $flag->ICONE           = strtolower($this->request->getData("ICONE"));
        
        return $this->response->withStringBody( ($tblFlag->save($flag))?true:false );
    }

    /*****************************TIPOS DE DESPESA********************************/
    /**
	* Metodo que monta os filtros dos tipos de despesa
	* 
	* @return string
	*/
    public function expenseTypeFilter(){

        $this->Filter->addFilter("Nome","TXT_EXPENSE_TYPE_SEARCH_NAME","text");
		
		$ops = array();
        
        $opta = new \stdClass();
        $opta->key  = "Sim";
        $opta->value = "1";
        $ops[] = $opta;
        
        $optm = new \stdClass();
        $optm->key  = "N&atilde;o";
        $optm->value = "0";
        $ops[] = $optm;
		
		$this->Filter->addFilter("Exibe para Loja","CB_EXPENSE_TYPE_SEARCH_SHOW_STORE","combo",$ops);
		
		$opt = array();
        
        $opt1 = new \stdClass();
        $opt1->key  = "Sim";
        $opt1->value = "1";
        $opt[] = $opt1;
        
        $opt2 = new \stdClass();
        $opt2->key  = "N&atilde;o";
        $opt2->value = "0";
        $opt[] = $opt2;
		
		$this->Filter->addFilter("Exibe no Calend&aacute;rio","CB_EXPENSE_TYPE_SEARCH_SAVE_CALENDAR","combo",$opt);
        
        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }
    
    /**
	* Metodo que monta a pagina principal dos tipos de despesa
	* 
	* @return null
	*/
    public function expenseType(){
        $this->set('url_filter','/financial/expense_type_filter');
        $this->set('url_data','/financial/expense_type_data');
    }
    
    /**
	* Metodo que monta os dados da listagem dos tipos de despesa
	* 
	* @return null
	*/
    public function expenseTypeData(){
        $tblExpense = TableRegistry::get('SysTipoDespesa');
        
        $query = $tblExpense->find();
        $query->select(['IDTIPODESPESA','NOME','EXIBE_LOJA','SALVA_CALENDARIO']);
        
        if($this->request->getData("TXT_EXPENSE_TYPE_SEARCH_NAME")!=""){
            $query->where(function ($exp){
                return $exp->like('NOME','%'.$this->request->getData('TXT_EXPENSE_TYPE_SEARCH_NAME').'%');
            });
        }
		
		if($this->request->getData("CB_EXPENSE_TYPE_SEARCH_SHOW_STORE")!=""){
			$query->where(['EXIBE_LOJA' => $this->request->getData("CB_EXPENSE_TYPE_SEARCH_SHOW_STORE")]);
		}
		
		if($this->request->getData("CB_EXPENSE_TYPE_SEARCH_SAVE_CALENDAR")!=""){
			$query->where(['SALVA_CALENDARIO' => $this->request->getData("CB_EXPENSE_TYPE_SEARCH_SAVE_CALENDAR")]);
		}
        
        $this->set('data_list',$this->paginate($query,['limit' => 10]));
    }

	/**
	* Metodo que exibe a tela para cadastro ou edicao de tipo de despesa
	* @param int $_idTipoDespesa codigo do tipo de despesa
	* 
	* @return null
	*/
    public function expenseTypeCreate($_idTipoDespesa=""){
        if($_idTipoDespesa!=""){
            $this->set('tipodespesa',TableRegistry::get('SysTipoDespesa')->get($_idTipoDespesa));
        }
    }

	/**
	* Metodo que salva os dados do tipo de despesa
	* 
	* @return null
	*/
    public function expenseTypeSave(){
        $tblDesp = TableRegistry::get('SysTipoDespesa');
        if($this->request->getData("IDTIPODESPESA")!=""){
            $despesa = $tblDesp->get($this->request->getData("IDTIPODESPESA"));
        }else{
            $despesa = $tblDesp->newEntity();
        }
        $despesa->NOME             = mb_strtoupper($this->request->getData("NOME"));
        $despesa->EXIBE_LOJA       = mb_strtoupper($this->request->getData("EXIBE_LOJA"));
        $despesa->SALVA_CALENDARIO = mb_strtoupper($this->request->getData("SALVA_CALENDARIO"));
		
        return $this->response->withStringBody( ($tblDesp->save($despesa))?true:false );
    }

    /************************* OPERACAO FINANCEIRA ****************************/
    /**
	* Metodo que monta os filtros da operacao financeira
	* 
	* @return string
	*/
    public function financialOperationFilter(){
        $this->Filter->addFilter("Nome","TXT_FINANCIAL_TRANSACT_SEARCH_NAME","text");
        
        $ops = array();
        
        $opte = new \stdClass();
        $opte->key   = "Entradas";
        $opte->value = "E";
        $ops[] = $opte;
        
        $opts = new \stdClass();
        $opts->key   = "Sa&iacute;das";
        $opts->value = "S";
        $ops[] = $opts;
        
        $opti = new \stdClass();
        $opti->key   = "Saldo Inicial";
        $opti->value = "I";
        $ops[] = $opti;
        
        $this->Filter->addFilter("Tipo da Opera&ccedil;&atilde;o","CB_FINANCIAL_TRANSACT_SEARCH_OPERATION_TYPE","combo",$ops);
        
        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }
    
    /**
	* Metodo que monta a pagina principal das operacoes financeiras
	* 
	* @return null
	*/
    public function financialOperation(){
        $this->set('url_filter','/financial/financial_operation_filter');
        $this->set('url_data','/financial/financial_operation_data');
    }
    
    /**
	* Metodo que monta os dados da busca das operacoes financeiras
	* 
	* @return null
	*/
    public function financialOperationData(){
        $tblTransact = TableRegistry::get('SysOperacaoFinanceira');
        
        $query = $tblTransact->find();
        $query->select(['IDOPERACAOFINANCEIRA','NOME','TIPO_OPERACAO','ORDEM']);
        
        if($this->request->getData("TXT_FINANCIAL_TRANSACT_SEARCH_NAME")!=""){
            $query->where(function ($exp){
                return $exp->like('NOME','%'.$this->request->getData('TXT_FINANCIAL_TRANSACT_SEARCH_NAME').'%');
            });
        }
        
        if($this->request->getData("CB_FINANCIAL_TRANSACT_SEARCH_OPERATION_TYPE")!=""){
            $query->where(['TIPO_OPERACAO' => $this->request->getData("CB_FINANCIAL_TRANSACT_SEARCH_OPERATION_TYPE")]);
        }
        
        $query->order(['ORDEM' => 'ASC']);
        
        $this->set('data_list',$this->paginate($query,['limit' => 10]));
    }

	/**
	* Metodo que monta a tela para cadastro ou edicao de operacao financeira
	* @param int $_idOperacaoFinanceira codigo da operacao financeira
	* 
	* @return null
	*/
    public function financialOperationCreate($_idOperacaoFinanceira=""){
        if($_idOperacaoFinanceira!=""){
            $this->set('operacao_financeira',TableRegistry::get('SysOperacaoFinanceira')->get($_idOperacaoFinanceira));
        }
    }
    
    /**
	* Metodo que salva as informacoes da operacao financeira
	* 
	* @return boolean
	*/
    public function financialOperationSave(){
        
        $tblFinanc = TableRegistry::get('SysOperacaoFinanceira');
        if($this->request->getData("IDOPERACAOFINANCEIRA")!=""){
            $operacao = $tblFinanc->get($this->request->getData("IDOPERACAOFINANCEIRA"));
        }else{
            $operacao = $tblFinanc->newEntity();
        }
        $operacao->NOME          = mb_strtoupper($this->request->getData("NOME"));
        $operacao->TIPO_OPERACAO = $this->request->getData("TIPO_OPERACAO");
        
        return $this->response->withStringBody( $tblFinanc->save($operacao)?true:false );
    }
    
    /**
	* Metodo que exibe a pagina inicial de ordenacao de operacoes financeiras
	* 
	* @return null
	*/
    public function financialOperationOrder(){

	}
	
	/**
	* Metodo que lista as operacoes financeiras com base no tipo de operacao
	* Esse metodo eh utilizado para realizar a ordenacao de operacoes
	* 
	* @return html
	*/
	public function financialOperationList(){
		$retorno = "";
		$operations = TableRegistry::get('SysOperacaoFinanceira')->find()->where(['TIPO_OPERACAO' => $this->request->getData("TIPO_OPERACAO")])->order(['ORDEM' => 'ASC']);
		
		//se houver registros monta a tabela que serah retornada
		//com as operacoes financeiras e um campo para preenchimento
		if($operations->count()>0){
			$retorno = "<table class='table table-striped'>";
			$retorno.= "<thead>";
			$retorno.= "<tr>";
			$retorno.= "<th>Opera&ccedil;&atilde;o Financeira</th><th>Ordem</th>";
			$retorno.= "</tr>";
			$retorno.= "</thead>";
			$retorno.= "<tbody>";
			foreach($operations as $op){
				$retorno.= "<tr>";
				$retorno.= "<td>".$op->NOME."</td>";
				$retorno.= "<td><input type='number' class='form-control form-control-sm' name='txtFinOpOrder[]' id='txtFinOp".$op->IDOPERACAOFINANCEIRA."' value='".$op->ORDEM."'><input type='hidden' id='txtFinOp[]' name='txtFinOp[]' value='".$op->IDOPERACAOFINANCEIRA."'></td>";
				$retorno.= "</tr>";
			}
			$retorno.= "</tbody>";
			$retorno.= "</table>";
		}
		
		return $this->response->withStringBody( $retorno );
	}
	
	/**
	* Metodo que salva a ordenacao de operacoes financeiras
	* 
	* @return boolean
	*/
	public function financialOperationOrderSave(){
		$retorno = false;
		
		$ids    = $this->request->getData("txtFinOp");
		$orders = $this->request->getData("txtFinOpOrder");
		$table  = TableRegistry::get('SysOperacaoFinanceira');
		
		for($i=0;$i<count($ids);$i++){
			print($orders[$i]);
			$oper = $table->get($ids[$i]);
			$oper->ORDEM = $orders[$i];
			$retorno = ($table->save($oper)?true:false);
		}

		return $this->response->withStringBody( $retorno );
	}
    /*****************************ORCAMENTO********************************/
    /**
	* Metodo que monta os filtros do orcamento
	* 
	* @return string
	*/
    public function budgetFilter(){

        $this->Filter->addFilter("Nome","TXT_BUDGET_SEARCH_NAME","text");
        
        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }
    
    /**
	* Metodo que monta a pagina principal do orcamento
	* 
	* @return
	*/
    public function budget(){
    	$this->set('title',"Or&ccedil;amento");
    	
        $this->set('url_filter','/financial/budget_filter');
        $this->set('url_data','/financial/budget_data');
    }
    
    /**
	* Metodo que monta os dados da busca de orcamento
	* 
	* @return null
	*/
    public function budgetData(){
        $tblBudget = TableRegistry::get('SysOrcamento');
        $tmpTable = TableRegistry::get('TmpOrcamentoValor');
        
        $query = $tblBudget->find();
        $query->select(['IDLOJA','NOME','ANO','LOJA' => 'L.NOME'])
        ->join([
        	'table' => 'sys_loja',
        	'alias' => 'L',
        	'type'  => 'inner',
        	'conditions' => 'L.IDLOJA=SysOrcamento.IDLOJA'
        ]);
        
        if($this->request->getData("TXT_BUDGET_SEARCH_NAME")!=""){
            $query->where(function ($exp){
                return $exp->like('NOME','%'.$this->request->getData('TXT_BUDGET_SEARCH_NAME').'%');
            });
        }
        
        $tmpTable->deleteAll(array('1 = 1'));
        
        $this->set('data_list',$this->paginate($query,['limit' => 10]));
    }
    
    /**
	* Metodo que exibe a tela de cadastro ou edicao de orcamento
	* @param int $_idOrcamento codigo do orcamento
	* 
	* @return null
	*/
    public function budgetCreate($_idLoja="",$_ano=""){
        $this->set('store_list',
        	TableRegistry::get('SysLoja')->find()
        	->where(
        		function($exp){ 
        			return $exp->notEq("IDLOJA",TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR); 
        		})
        	);
        
        if($_idLoja!="" && $_ano!=""){
            $this->set('orcamento',TableRegistry::get('SysOrcamento')->get(['IDLOJA' => $_idLoja,'ANO' => $_ano]));
        }
    }
    
    /**
	* Metodo que salva as informacoes de orcamento
	* 
	* @return boolean
	*/
    public function budgetSave(){

        $retorno = false;
        
        $tblOrca = TableRegistry::get('SysOrcamento');
        $tmpTable = TableRegistry::get('TmpOrcamentoValor');
        
        //verifica se o orcamento eh alteracao ou novo
        if($this->request->getData("txtIdBudget")!=""){
            $orcamento = $tblOrca->get(['IDLOJA' => $this->request->getData("cbStore"),'ANO' => $this->request->getData("txtBudgetYear")]);
            //sendo alteracao apaga todos os registros da tabela de valores para inputar novos
            TableRegistry::get('SysOrcamentoValor')->deleteAll(['IDLOJA' => $this->request->getData("cbStore"),'ANO' => $this->request->getData("txtBudgetYear")]);
        }else{
            $orcamento = $tblOrca->newEntity();
        }
        $orcamento->NOME   = mb_strtoupper($this->request->getData("txtBudgetName"));
        $orcamento->ANO    = $this->request->getData("txtBudgetYear");
        $orcamento->IDLOJA = $this->request->getData("cbLoja");
        
        $ops = $this->request->getData("txtIdOperacaoFinanceira");
        $jan = $this->request->getData("txtValJan");
        $fev = $this->request->getData("txtValFev");
        $mar = $this->request->getData("txtValMar");
        $abr = $this->request->getData("txtValAbr");
        $mai = $this->request->getData("txtValMai");
        $jun = $this->request->getData("txtValJun");
        $jul = $this->request->getData("txtValJul");
        $ago = $this->request->getData("txtValAgo");
        $set = $this->request->getData("txtValSet");
        $out = $this->request->getData("txtValOut");
        $nov = $this->request->getData("txtValNov");
        $dez = $this->request->getData("txtValDez");
        if($tblOrca->save($orcamento)){
            
            for($i=0;$i<count($ops);$i++){
                $tblOrcaVal = TableRegistry::get('SysOrcamentoValor');
                
                $orcjan                       = $tblOrcaVal->newEntity();
                $orcjan->IDLOJA               = $orcamento->IDLOJA;
                $orcjan->IDOPERACAOFINANCEIRA = $ops[$i];
                $orcjan->ANO                  = $orcamento->ANO;
                $orcjan->MES                  = 1;
                $orcjan->VALOR                = str_replace(",","",$jan[$i]);
                $retorno = $tblOrcaVal->save($orcjan)?true:false;
                
                $orcfev                       = $tblOrcaVal->newEntity();
                $orcfev->IDLOJA               = $orcamento->IDLOJA;
                $orcfev->IDOPERACAOFINANCEIRA = $ops[$i];
                $orcfev->ANO                  = $orcamento->ANO;
                $orcfev->MES                  = 2;
                $orcfev->VALOR                = str_replace(",","",$fev[$i]);
                $retorno = $tblOrcaVal->save($orcfev)?true:false;
                
                $orcmar                       = $tblOrcaVal->newEntity();
                $orcmar->IDLOJA               = $orcamento->IDLOJA;
                $orcmar->IDOPERACAOFINANCEIRA = $ops[$i];
                $orcmar->ANO                  = $orcamento->ANO;
                $orcmar->MES                  = 3;
                $orcmar->VALOR                = str_replace(",","",$mar[$i]);
                $retorno = $tblOrcaVal->save($orcmar)?true:false;
                
                $orcabr                       = $tblOrcaVal->newEntity();
                $orcabr->IDLOJA               = $orcamento->IDLOJA;
                $orcabr->IDOPERACAOFINANCEIRA = $ops[$i];
                $orcabr->ANO                  = $orcamento->ANO;
                $orcabr->MES                  = 4;
                $orcabr->VALOR                = str_replace(",","",$abr[$i]);
                $tblOrcaVal->save($orcabr)?true:false;
                
                $orcmai                       = $tblOrcaVal->newEntity();
                $orcmai->IDLOJA               = $orcamento->IDLOJA;
                $orcmai->IDOPERACAOFINANCEIRA = $ops[$i];
                $orcmai->ANO                  = $orcamento->ANO;
                $orcmai->MES                  = 5;
                $orcmai->VALOR                = str_replace(",","",$mai[$i]);
                $retorno = $tblOrcaVal->save($orcmai)?true:false;
                
                $orcjun                       = $tblOrcaVal->newEntity();
                $orcjun->IDLOJA               = $orcamento->IDLOJA;
                $orcjun->IDOPERACAOFINANCEIRA = $ops[$i];
                $orcjun->ANO                  = $orcamento->ANO;
                $orcjun->MES                  = 6;
                $orcjun->VALOR                = str_replace(",","",$jun[$i]);
                $retorno = $tblOrcaVal->save($orcjun)?true:false;
                
                $orcjul                       = $tblOrcaVal->newEntity();
                $orcjul->IDLOJA               = $orcamento->IDLOJA;
                $orcjul->IDOPERACAOFINANCEIRA = $ops[$i];
                $orcjul->ANO                  = $orcamento->ANO;
                $orcjul->MES                  = 7;
                $orcjul->VALOR                = str_replace(",","",$jul[$i]);
                $retorno = $tblOrcaVal->save($orcjul)?true:false;
                
                $orcago                       = $tblOrcaVal->newEntity();
                $orcago->IDLOJA               = $orcamento->IDLOJA;
                $orcago->IDOPERACAOFINANCEIRA = $ops[$i];
                $orcago->ANO                  = $orcamento->ANO;
                $orcago->MES                  = 8;
                $orcago->VALOR                = str_replace(",","",$ago[$i]);
                $retorno = $tblOrcaVal->save($orcago)?true:false;
                
                $orcset                       = $tblOrcaVal->newEntity();
                $orcset->IDLOJA               = $orcamento->IDLOJA;
                $orcset->IDOPERACAOFINANCEIRA = $ops[$i];
                $orcset->ANO                  = $orcamento->ANO;
                $orcset->MES                  = 9;
                $orcset->VALOR                = str_replace(",","",$set[$i]);
                $retorno = $tblOrcaVal->save($orcset)?true:false;
                
                $orcout                       = $tblOrcaVal->newEntity();
                $orcout->IDLOJA               = $orcamento->IDLOJA;
                $orcout->IDOPERACAOFINANCEIRA = $ops[$i];
                $orcout->ANO                  = $orcamento->ANO;
                $orcout->MES                  = 10;
                $orcout->VALOR                = str_replace(",","",$out[$i]);
                $retorno = $tblOrcaVal->save($orcout)?true:false;
                
                $orcnov                       = $tblOrcaVal->newEntity();
                $orcnov->IDLOJA               = $orcamento->IDLOJA;
                $orcnov->IDOPERACAOFINANCEIRA = $ops[$i];
                $orcnov->ANO                  = $orcamento->ANO;
                $orcnov->MES                  = 11;
                $orcnov->VALOR                = str_replace(",","",$nov[$i]);
                $retorno = $tblOrcaVal->save($orcnov)?true:false;
                
                $orcdez                       = $tblOrcaVal->newEntity();
                $orcdez->IDLOJA               = $orcamento->IDLOJA;
                $orcdez->IDOPERACAOFINANCEIRA = $ops[$i];
                $orcdez->ANO                  = $orcamento->ANO;
                $orcdez->MES                  = 12;
                $orcdez->VALOR                = str_replace(",","",$dez[$i]);
                $retorno = $tblOrcaVal->save($orcdez)?true:false;
            }
            
            $tmpTable->deleteAll(array('1 = 1'));
        }
        
        return $this->response->withStringBody( $retorno );
    }
    
    /**
	* Metodo que busca as informacoes de itens do orcamento
	* 
	* @return null
	*/
    public function budgetAccountsGet(){
        
        $operacoes = TableRegistry::get('SysOperacaoFinanceira')->find()
        ->where(['TIPO_OPERACAO' =>'E'])->orWhere(['TIPO_OPERACAO' => 'S'])
        ->order(['TIPO_OPERACAO' => 'ASC']);
        
        $newOps = null;
        foreach($operacoes as $operacao){
            $op = new \stdClass();
            $op->IDOPERACAOFINANCEIRA = $operacao->IDOPERACAOFINANCEIRA;
            $op->NOME          = $operacao->NOME;
            $op->TIPO_OPERACAO = $operacao->TIPO_OPERACAO;
            $op->UTILIZADA     = (TableRegistry::get('TmpOrcamentoValor')->find()->where(['IDOPERACAOFINANCEIRA' => $operacao->IDOPERACAOFINANCEIRA])->count()>0)?true:false;
            
            $newOps[] = $op;
        }
        
        $this->set('operacoes',$newOps);
    }
    
    /**
	* Metodo que adiciona uma operacao financeira na tabela temporaria do orcamento
	* @param int $_idOperacaoFinanceira codigo da operacao financeira
	* 
	* @return boolean
	*/
    public function bugdetAccountAdd($_idOperacaoFinanceira){
        $retorno = false;
        $tmpTable = TableRegistry::get('TmpOrcamentoValor');
        
        $operacao = TableRegistry::get('SysOperacaoFinanceira')->get($_idOperacaoFinanceira);
        
        //inclui os 12 meses do ano
        for($i=0;$i<12;$i++){
            $orcamento = $tmpTable->newEntity();
            $orcamento->IDOPERACAOFINANCEIRA = $_idOperacaoFinanceira;
            $orcamento->NOME_OPERACAO        = $operacao->NOME;
            $orcamento->ANO                  = date("Y");
            $orcamento->MES                  = $i+1;
            $orcamento->VALOR                = 0;
            $retorno = $tmpTable->save($orcamento)?true:false;
        }
        
        return $this->response->withStringBody( $retorno );
    }
    
    /**
	* Metodo que remove uma operacao financeira da tabela temporaria de orcamento
	* @param int $_idOperacaoFinanceira codigo da operacao financeira
	* 
	* @return boolean
	*/
    public function budgetAccountDel($_idOperacaoFinanceira){
        $tmp = TableRegistry::get('TmpOrcamentoValor');
        
        return $this->response->withStringBody( $tmp->deleteAll(['IDOPERACAOFINANCEIRA' => $_idOperacaoFinanceira])?true:false );
    }
    
    /**
	* Metodo que busca as informacoes de um orcamento
	* @param int $_idOrcamento codigo do orcamento
	* 
	* @return null
	*/
    public function budgetAccountsBasket($_idLoja="",$_ano=""){
               
        if($_idLoja!="" && $_ano!=""){
            $tmpItem = TableRegistry::get('TmpOrcamentoValor');
            
            if($tmpItem->find()->count()==0){
                $tblOrcaIt = TableRegistry::get('SysOrcamentoValor');
                $vals = $tblOrcaIt->find()->where(['IDLOJA' => $_idLoja,'ANO' => $_ano]);

                foreach($vals as $value){
                    //busca os dados do produto
                    $tblOpF = TableRegistry::get('SysOperacaoFinanceira');
                    $oper = $tblOpF->get($value->IDOPERACAOFINANCEIRA);
                    
                    $item = $tmpItem->newEntity();
                    $item->IDOPERACAOFINANCEIRA = $value->IDOPERACAOFINANCEIRA;
                    $item->NOME_OPERACAO = $oper->NOME;
                    $item->MES           = $value->MES;
                    $item->ANO           = $value->ANO;
                    $item->VALOR         = $value->VALOR;
                    
                    $tmpItem->save($item);
                }
            }
        }
        
        $sql = "SELECT CASE OPF.TIPO_OPERACAO WHEN 'I' THEN 1 WHEN 'E' THEN 2 ELSE 3 END AS ORD_TYPE,ORDEM,OPF.IDOPERACAOFINANCEIRA,NOME,TIPO_OPERACAO,"
                . "COALESCE(SUM(CASE WHEN MES=1 THEN VALOR END),0) AS `JAN`,"
                . "COALESCE(SUM(CASE WHEN MES=2 THEN VALOR END),0) AS `FEV`,"
                . "COALESCE(SUM(CASE WHEN MES=3 THEN VALOR END),0) AS `MAR`,"
                . "COALESCE(SUM(CASE WHEN MES=4 THEN VALOR END),0) AS `ABR`,"
                . "COALESCE(SUM(CASE WHEN MES=5 THEN VALOR END),0) AS `MAI`,"
                . "COALESCE(SUM(CASE WHEN MES=6 THEN VALOR END),0) AS `JUN`,"
                . "COALESCE(SUM(CASE WHEN MES=7 THEN VALOR END),0) AS `JUL`,"
                . "COALESCE(SUM(CASE WHEN MES=8 THEN VALOR END),0) AS `AGO`,"
                . "COALESCE(SUM(CASE WHEN MES=9 THEN VALOR END),0) AS `SET`,"
                . "COALESCE(SUM(CASE WHEN MES=10 THEN VALOR END),0) AS `OUT`,"
                . "COALESCE(SUM(CASE WHEN MES=11 THEN VALOR END),0) AS `NOV`,"
                . "COALESCE(SUM(CASE WHEN MES=12 THEN VALOR END),0) AS `DEZ` "
                . "FROM `tmp_orcamento_valor` OV "
                . "INNER JOIN `sys_operacao_financeira` OPF on OPF.IDOPERACAOFINANCEIRA=OV.IDOPERACAOFINANCEIRA "
                ."GROUP BY ANO,IDOPERACAOFINANCEIRA ORDER BY 1,2";
        $connection = ConnectionManager::get('default');
        $results = $connection->execute($sql)->fetchAll('assoc');
        
        $this->set('itens',$results);
    }
    
    /**
	* Metodo que exclui um orcamento
	* 
	* @return boolean
	*/
	public function budgetTrash(){		
		$tblReg = TableRegistry::get("SysOrcamentoValor");
		$retorno = false;
		
		$regs = $this->request->getData("check_list");
		
		for($i=0;$i<count($regs);$i++){
			$item = $tblReg->get($regs[$i]);
			$retorno = ($tblReg->delete($item))?true:false;
		}
		
		return $this->response->withStringBody( $retorno );
	}
    
    /*****************************ENTRADAS DO FLUXO DE CAIXA********************************/
    /**
	* Metodo que monta a tela principal das entradas de fluxo de caixa
	* 
	* @return null
	*/
    public function inflow(){
    	$this->set('title',"Entradas");
    	
        $this->set('url_filter','');
        $this->set('url_data','/financial/inflow_data');
    }
    
    /**
	* Metodo que lista as entradas do fluxo de caixa
	* 
	* @return null
	*/
    public function inflowData(){
        $tblInput = TableRegistry::get('SysOperacaoEntrada');
        
        $qryIn = $tblInput->find();
        $qryIn->select(['IDOPERACAOFINANCEIRA' => 'OPF.IDOPERACAOFINANCEIRA','OPERACAO_FINANCEIRA' => 'OPF.NOME'])
            ->join([
            	'alias' => 'OPF',
            	'table' => 'sys_operacao_financeira',
                'type'  => 'INNER',
                'conditions' => 'OPF.IDOPERACAOFINANCEIRA=SysOperacaoEntrada.IDOPERACAOFINANCEIRA'
        ])->group(['OPF.IDOPERACAOFINANCEIRA']);
        
        $qryPay = $tblInput->find();
        $qryPay->select(['IDOPERACAOFINANCEIRA','IDMEIOPAGAMENTO' => 'MP.IDMEIOPAGAMENTO','MEIO_PAGAMENTO' => 'MP.NOME'])
        	->join([
        	'alias' => 'MP',
            'table' => 'sys_meio_pagamento',
            'type'  => 'INNER',
            'conditions' => 'MP.IDMEIOPAGAMENTO=SysOperacaoEntrada.IDMEIOPAGAMENTO'
        ]);
        
        $tmp = TableRegistry::get('TmpEntradaFluxo');
        $tmp->deleteAll(array('1 = 1'));
        
        $this->set('data_list',$this->paginate($qryIn,['limit' => 10]));
        $this->set('pays',$qryPay);
    }
    
    /**
	* Metodo que exibe a tela para criacao de uma entrada de fluxo de caixa
	* Como eh uma associacao de operacao financeira x meio de pagamento
	* nao ha edicao de informacoes
	* 
	* @return null
	*/
    public function inflowCreate(){
        $this->set('operacaolist',  TableRegistry::get('SysOperacaoFinanceira')->find()->where(['TIPO_OPERACAO' =>'E'])->order(['NOME'=>'ASC']));
    }
    
    public function inflowBasket(){
    	$idOpFinanc = $this->request->getData("IDOPERACAOFINANCEIRA");
    	
    	//verifica se ha uma operacao financeira
		if($idOpFinanc!=""){
			//busca a tabela temporaria
            $tmpItem = TableRegistry::get('TmpEntradaFluxo');
            
            //se nao houver registros na tabela temporaria
            if($tmpItem->find()->count()==0){
                $table = TableRegistry::get('SysOperacaoEntrada');
                $vals = $table->find()->where(['IDOPERACAOFINANCEIRA' => $idOpFinanc]);

                foreach($vals as $value){
                    //transfere os dados para a tabela temporaria de trabalho                    
                    $item = $tmpItem->newEntity();
                    $item->IDOPERACAOFINANCEIRA = $value->IDOPERACAOFINANCEIRA;
                    $item->IDMEIOPAGAMENTO      = $value->IDMEIOPAGAMENTO;
                    $item->NOME                 = TableRegistry::get('SysMeioPagamento')->get($value->IDMEIOPAGAMENTO)->NOME;
                    
                    $tmpItem->save($item);
                }
            }
        }
        
        $this->set('itens',TableRegistry::get('TmpEntradaFluxo')->find());
	}
	
	public function inflowOptionsGet(){
		$operacoes = TableRegistry::get('SysMeioPagamento')->find();
        
        $newOps = null;
        foreach($operacoes as $operacao){
            $op = new \stdClass();
            $op->IDMEIOPAGAMENTO = $operacao->IDMEIOPAGAMENTO;
            $op->NOME            = $operacao->NOME;
            $op->UTILIZADA       = (TableRegistry::get('TmpEntradaFluxo')->find()->where(['IDMEIOPAGAMENTO' => $operacao->IDMEIOPAGAMENTO])->count()>0)?true:false;
            
            $newOps[] = $op;
        }
        
        $this->set('operacoes',$newOps);
	}
	
	/**
	* Metodo que adiciona um meio de pagamento na tabela temporaria 
	* de operacoes de entrada do fluxo de caixa
	* 
	* @return boolean
	*/
    public function inflowOptionAdd(){
        $retorno = false;
        $tmpTable = TableRegistry::get('TmpEntradaFluxo');
        
        $oper = $tmpTable->newEntity();
        $oper->IDOPERACAOFINANCEIRA = $this->request->getData("IDOPERACAOFINANCEIRA");
        $oper->IDMEIOPAGAMENTO      = $this->request->getData("IDMEIOPAGAMENTO");
        $oper->NOME                 = TableRegistry::get('SysMeioPagamento')->get( $this->request->getData("IDMEIOPAGAMENTO") )->NOME;
        $retorno = $tmpTable->save($oper)?true:false;
        
        return $this->response->withStringBody( $retorno );
    }
    
    /**
	* Metodo que remove uma operacao financeira da tabela temporaria de orcamento
	* @param int $_idOperacaoFinanceira codigo da operacao financeira
	* 
	* @return boolean
	*/
    public function inflowOptionDel(){
        $tmp = TableRegistry::get('TmpEntradaFluxo');
        
        return $this->response->withStringBody( $tmp->deleteAll(['IDMEIOPAGAMENTO' => $this->request->getData("IDMEIOPAGAMENTO")])?true:false );
    }
    
    /**
	* Metodo que apaga uma entrada do fluxo de caixa
	* 
	* @return boolean
	*/
    public function inflowDelete(){
        $retorno = false;
        $DATA = $this->request->getData("check_list");
        
        for($i=0;$i<count($DATA);$i++){
            $retorno = TableRegistry::get('SysOperacaoEntrada')->deleteAll(['IDOPERACAOFINANCEIRA' => $DATA[$i]])?true:false;
        }
        return $this->response->withStringBody( $retorno );
    }
    
    /**
	* Metodo que salva as informacoes da tabela temporaria na tabela oficial
	* 
	* @return boolean
	*/
    public function inflowSave(){
    	$retorno = false;
    	$tmp = TableRegistry::get('TmpEntradaFluxo');
    	$tbl = TableRegistry::get('SysOperacaoEntrada');
    	
    	
    	//limpa as informacoes da tabela oficial
    	$tbl->deleteAll(['IDOPERACAOFINANCEIRA' => $this->request->getData("IDOPERACAOFINANCEIRA")]);
    	
    	foreach($tmp->find()->where(['IDOPERACAOFINANCEIRA' => $this->request->getData("IDOPERACAOFINANCEIRA") ]) as $entrada){
			$oper = $tbl->newEntity();
			$oper->IDOPERACAOFINANCEIRA = $this->request->getData("IDOPERACAOFINANCEIRA");
			$oper->IDMEIOPAGAMENTO      = $entrada->IDMEIOPAGAMENTO;
			$retorno = $tbl->save($oper)?true:false;
		}
		
		$tmp->deleteAll(array('1 = 1'));
        
        return $this->response->withStringBody( $retorno );
    }
    
    /*****************************SAIDAS DO FLUXO DE CAIXA********************************/   
    public function outflow(){
    	$this->set('title',"Sa&iacute;das");
    	
        $this->set('url_filter','');
        $this->set('url_data','/financial/outflow_data');
    }
    
    /**
	* Metodo que busca as informacoes de saidas do fluxo de caixa
	* 
	* @return null
	*/
    public function outflowData(){
        $tblOutput = TableRegistry::get('SysOperacaoSaida');
        
        $qryOut = $tblOutput->find();
        $qryOut->select(['IDOPERACAOFINANCEIRA' => 'OPF.IDOPERACAOFINANCEIRA','OPERACAO_FINANCEIRA' => 'OPF.NOME'])
            ->join([
            	'alias' => 'OPF',
                'table' => 'sys_operacao_financeira',
                'type'  => 'INNER',
                'conditions' => 'OPF.IDOPERACAOFINANCEIRA=SysOperacaoSaida.IDOPERACAOFINANCEIRA'
        	])->group(['OPF.IDOPERACAOFINANCEIRA']);       
        
        $this->set('data_list',$this->paginate($qryOut,['limit' => 10]));
        
        $qryExp = $tblOutput->find();
        $qryExp->select(['IDOPERACAOFINANCEIRA' ,'IDTIPODESPESA' => 'TD.IDTIPODESPESA','TIPO_DESPESA' => 'TD.NOME'])
        	->join([
        		'alias' => 'TD',
                'table' => 'sys_tipo_despesa',
                'type'  => 'INNER',
                'conditions' => 'TD.IDTIPODESPESA=SysOperacaoSaida.IDTIPODESPESA'
        	]);
        
        $tmp = TableRegistry::get('TmpSaidaFluxo');
        $tmp->deleteAll(array('1 = 1'));
        
        $this->set('data_list',$this->paginate($qryOut,['limit' => 10]));
        $this->set('expenses',$qryExp);
    }
    
    /**
	* Metodo que exibe a pagina de criacao da saida do fluxo de caixa
	* Como eh uma associacao de operacao financeira e tipo de despesa
	* nao ha edicao das informacoes, apenas criacao e exclusao
	* 
	* @return null
	*/
    public function outflowCreate(){
        $this->set('operacaolist',  TableRegistry::get('SysOperacaoFinanceira')->find()->where(['TIPO_OPERACAO' =>'S'])->order(['NOME'=>'ASC']));
    }
    
    /**
	* Metodo que exclui uma saida do fluxo de caixa
	* 
	* @return
	*/
    public function outflowDelete(){
        $this->autoRender = false;
        $retorno = false;
        $DATA = $this->request->getData("check_list");
        
        for($i=0;$i<count($DATA);$i++){
            $retorno = TableRegistry::get('SysOperacaoSaida')->deleteAll(['IDOPERACAOFINANCEIRA' => $DATA[$i]])?true:false;
        }
        return $this->response->withStringBody( $retorno );
    }
    
    /**
	* Metodo que salva as informacoes da tabela temporaria na tabela oficial
	* 
	* @return boolean
	*/
    public function outflowSave(){
    	$retorno = false;
    	$tmp = TableRegistry::get('TmpSaidaFluxo');
    	$tbl = TableRegistry::get('SysOperacaoSaida');
    	
    	
    	//limpa as informacoes da tabela oficial
    	$tbl->deleteAll(['IDOPERACAOFINANCEIRA' => $this->request->getData("IDOPERACAOFINANCEIRA")]);
    	
    	foreach($tmp->find()->where(['IDOPERACAOFINANCEIRA' => $this->request->getData("IDOPERACAOFINANCEIRA") ]) as $entrada){
			$oper = $tbl->newEntity();
			$oper->IDOPERACAOFINANCEIRA = $this->request->getData("IDOPERACAOFINANCEIRA");
			$oper->IDTIPODESPESA        = $entrada->IDTIPODESPESA;
			$retorno = $tbl->save($oper)?true:false;
		}
		
		$tmp->deleteAll(array('1 = 1'));
        
        return $this->response->withStringBody( $retorno );
    }
    
    
    public function outflowBasket(){
    	
    	$idOpFinanc = $this->request->getData("IDOPERACAOFINANCEIRA");
    	
    	//verifica se ha uma operacao financeira
		if($idOpFinanc!=""){
			//busca a tabela temporaria
            $tmpItem = TableRegistry::get('TmpEntradaFluxo');
            
            //se nao houver registros na tabela temporaria
            if($tmpItem->find()->count()==0){
                $table = TableRegistry::get('SysOperacaoSaida');
                $vals = $table->find()->where(['IDOPERACAOFINANCEIRA' => $idOpFinanc]);

                foreach($vals as $value){
                    //transfere os dados para a tabela temporaria de trabalho                    
                    $item = $tmpItem->newEntity();
                    $item->IDOPERACAOFINANCEIRA = $value->IDOPERACAOFINANCEIRA;
                    $item->IDTIPODESPESA        = $value->IDTIPODESPESA;
                    $item->NOME                 = TableRegistry::get('SysTipoDespesa')->get($value->IDMEIOPAGAMENTO)->NOME;
                    
                    $tmpItem->save($item);
                }
            }
        }
        
        $this->set('itens',TableRegistry::get('TmpSaidaFluxo')->find());
	}
	
	public function outflowOptionsGet(){
		$operacoes = TableRegistry::get('SysTipoDespesa')->find()->order(['NOME' => 'ASC']);
        
        $newOps = null;
        foreach($operacoes as $operacao){
            $op = new \stdClass();
            $op->IDTIPODESPESA = $operacao->IDTIPODESPESA;
            $op->NOME          = $operacao->NOME;
            $op->UTILIZADA     = (TableRegistry::get('TmpSaidaFluxo')->find()->where(['IDTIPODESPESA' => $operacao->IDTIPODESPESA])->count()>0)?true:false;
            
            $newOps[] = $op;
        }
        
        $this->set('operacoes',$newOps);
	}
	
	/**
	* Metodo que adiciona um meio de pagamento na tabela temporaria 
	* de operacoes de entrada do fluxo de caixa
	* 
	* @return boolean
	*/
    public function outflowOptionAdd(){
        $retorno = false;
        $tmpTable = TableRegistry::get('TmpSaidaFluxo');
        
        $oper = $tmpTable->newEntity();
        $oper->IDOPERACAOFINANCEIRA = $this->request->getData("IDOPERACAOFINANCEIRA");
        $oper->IDTIPODESPESA        = $this->request->getData("IDTIPODESPESA");
        $oper->NOME                 = TableRegistry::get('SysTipoDespesa')->get( $this->request->getData("IDTIPODESPESA") )->NOME;
        $retorno = $tmpTable->save($oper)?true:false;
        
        return $this->response->withStringBody( $retorno );
    }
    
    /**
	* Metodo que remove uma operacao financeira da tabela temporaria de orcamento
	* @param int $_idOperacaoFinanceira codigo da operacao financeira
	* 
	* @return boolean
	*/
    public function outflowOptionDel(){
        $tmp = TableRegistry::get('TmpSaidaFluxo');
        
        return $this->response->withStringBody( $tmp->deleteAll(['IDTIPODESPESA' => $this->request->getData("IDTIPODESPESA")])?true:false );
    }
    
    /***************************IMPORTACAO DE EXTRATO BANCARIO**************************/
    /**
	* Metodo que exibe a tela inicial da importacao de extratos bancarios
	* 
	* @return
	*/
    public function bankStatementImport(){
     	$this->set('title',"Importar Extrato Banc&aacute;rio");   
    }
    
    /**
	* Metodo que exibe a tela para realizar ajustes no extrato antes 
	* de importar para o banco de dados
	* 
	* @return null
	*/
    public function bankStatementConsume(){
    	//realiza o uploa do arquivo de extrato bancario
    	$tmpFile = $this->request->getData("TXT_FILE_UPLOAD");
    	$file_path = TMP.DS.$tmpFile['name'];
        try{
            if(move_uploaded_file($tmpFile['tmp_name'], $file_path)){
                $objFile = new \stdClass();
                $objFile->name = $tmpFile['name'];
                $objFile->data = $this->file_get_contents_curl($file_path);
            }
        }catch(Exception $x){
            return $this->response->withStringBody( print_r($x) );
        }
    	
    	
        $tblDesp = TableRegistry::get('SysTipoDespesa');
        
        
        //Utiliza o padrao Ofxparser para fazer a importacao
       	//esse eh o padrao do Microsoft Money que varios bancos utilizam 
        $rows = array();
        $xml = base64_decode($this->request->getData("txtFileContent"));
        $this->Ofxparser->loadFromString($xml);
        foreach($this->Ofxparser->getDebits() as $debito){
            $line = new \stdClass();
            $line->DATA_MOVIMENTO = substr($debito['DTPOSTED'], 0, 4)."-".substr($debito['DTPOSTED'],4,2)."-".substr($debito['DTPOSTED'],6,2);
            $line->HISTORICO      = $debito['MEMO'];
            $line->NUM_DOCUMENTO  = $debito['CHECKNUM'];
            $line->OPERATION      = 'D';
            $line->VALOR          = str_replace("-","",(str_replace(",",".",$debito['TRNAMT'])));
            
            $rows[] = $line;
        }
        
        $this->set('rows',$rows);
                
        $this->set('despesaslist',$tblDesp->find()->order(['NOME' => 'ASC']));
    }
    
    /**
	* Metodo que salva as informacoes de um extrato bancario no banco de dados
	* 
	* @return boolean
	*/
    public function bankStatementProcess(){
        $retorno = false;
        $tblBank = TableRegistry::get('sysExtratoBancario');
                
        $data_movimento = $this->request->getData("data_movimento");
        $num_documento  = $this->request->getData("num_document");
        $historico      = $this->request->getData("historico");
        $valor          = $this->request->getData("valor");
        $tipo_despesa   = $this->request->getData("cb_tipo_despesa");
        
        for($i=0;$i<count($data_movimento);$i++){
            if($tipo_despesa[$i]!=""){
                $registro = $tblBank->find()->where(['DATA_MOVIMENTO' => $data_movimento[$i],'NUM_DOCUMENTO' => $num_documento[$i],'VALOR' => $valor[$i]]);
                if($registro->count()==0){
                    $extrato_bancario = $tblBank->newEntity();
                    $extrato_bancario->DATA_MOVIMENTO = $data_movimento[$i];
                    $extrato_bancario->NUM_DOCUMENTO  = $num_documento[$i];
                    $extrato_bancario->HISTORICO      = $historico[$i];
                    $extrato_bancario->VALOR          = $valor[$i];
                    $extrato_bancario->IDTIPODESPESA  = $tipo_despesa[$i];
                    $retorno = $tblBank->save($extrato_bancario)?true:false;
                }else{
                    $retorno = true; //sempre serah verdadeiro quando item ja for encontrado la
                }
            }
        }
        
        return $this->response->withStringBody( $retorno );
    }
    
    /**
	* Metodo que exibe os filtros de busca dos extratos bancarios
	* 
	* @return string
	*/
    public function bankStatementFilter(){
        $this->Filter->addFilter("Hist&oacute;rico do Extrato","TXT_BANK_STATEMENT_SEARCH_HISTORY","text");
        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }
    
    /**
	* Metodo que monta a pagina inicial da listagem de extratos bancarios
	* 
	* @return null
	*/
    public function bankStatement(){
    	$this->set('title',"Extratos Banc&aacute;rios Importados");
        $this->set('url_filter','/financial/bank_statement_filter');
        $this->set('url_data','/financial/bank_statement_data');
    }
    
    /**
	* Metodo que busca os dados do extrato bancario registrado no banco de dados
	* 
	* @return null
	*/
    public function bankStatementData(){
        $tblStatement = TableRegistry::get('SysExtratoBancario');
        
        $query = $tblStatement->find();
        $query->select(['IDEXTRATOBANCARIO','DATA_MOVIMENTO','VALOR','NUM_DOCUMENTO','HISTORICO']);
        
        if($this->request->getData("TXT_BANK_STATEMENT_SEARCH_HISTORY")!=""){
            $query->where(function ($exp){
                return $exp->like('HISTORICO','%'.$this->request->getData('TXT_BANK_STATEMENT_SEARCH_HISTORY').'%');
            });
        }
        $query->order(['IDEXTRATOBANCARIO' => 'DESC']);
        
        $this->Paginator->paginate($query,['limit' => 10]);
        
        $this->set('data_list',$this->paginate($query));
    }
}