<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Number;
use Cake\I18n\Time;
use Cake\Filesystem\File;
use NFePHP\DA\NFe\Danfe;
use NFePHP\Nfe\Make;
use NFePHP\Ibpt\Ibpt;
use NFePHP\NFe\Complements;

class TributaryController extends AppController{


    public function initialize() {
        parent::initialize();
        $this->loadComponent("Paginator");
        $this->loadComponent("Filter");
        $this->loadComponent("Nfe");
        ob_start("ob_gzhandler");
    }

    /**
	* Metodo que exibe os filtros da listagem de notas fiscais recebidas
	*
	* @return html
	*/
    public function invoiceReceivedFilter(){


		$this->Filter->addFilter("N&uacute;mero da Nota","TXT_INVOICE_RECEIVED_SEARCH_NUMBER","number");
        $this->Filter->addFilter("Emitente","TXT_INVOICE_RECEIVED_SEARCH_SENDER","text");
        $this->Filter->addFilter("Data de emiss&atilde;o","TXT_INVOICE_RECEIVED_SEARCH_DATE","date");

        $ords = array();

			$ord2 = new \stdClass();
			$ord2->value = "FANTASIA_EMITENTE";
			$ord2->key   = "Emissor";
			$ords[] = $ord2;

			$ord3 = new \stdClass();
			$ord3->value = "NUMERO";
			$ord3->key   = "N&uacute;mero";
			$ords[] = $ord3;

			$ord4 = new \stdClass();
			$ord4->value = "DATA_EMISSAO";
			$ord4->key   = "Data de Emiss&atilde;o";
			$ords[] = $ord4;

			$ord5 = new \stdClass();
			$ord5->value = "VALOR_NOTA";
			$ord5->key   = "Valor da Nota";
			$ords[] = $ord5;

			$this->Filter->addOrder($ords);

        return $this->response->withStringBody( $this->Filter->mountFilters() );
	}

    /**
	* Metodo que exibe a pagina principal das notas fiscais recebidas
	* @param boolean $_toProcess flag que indica se sao apenas notas para processamento
	*
	* @return null
	*/
    public function invoiceReceived($_toProcess = false){
		$this->set('title',"Notas Fiscais Recebidas");
		$this->set('is_to_process',$_toProcess);
		$this->set('url_filter','/tributary/invoice_received_filter');
		$this->set('url_data','/tributary/invoice_received_data/'.$_toProcess);
	}

	/**
	* Metodo que realiza a busca das informacoes de notas fiscais recebidas
	* @param boolean $_onlyForProcess flag que verifica seh eh para listar apenas notas fiscais para processar
	*
	* @return null
	*/
	public function invoiceReceivedData($_onlyForProcess = false){
		$tblInvoice = TableRegistry::get('SysNfeRecebida');
        $this->set('to_process',$_onlyForProcess);

        //se for para processamento limpa a tabela temporaria de vinculos
        if($_onlyForProcess){
            $tmpTable = TableRegistry::get('TmpNfeRecebidaVinculo');
            $tmpTable->deleteAll(array('1 = 1'));
        }

        $query = $tblInvoice->find();
        $query->select(['IDNFERECEBIDA','FANTASIA_EMITENTE','NUMERO','DATA_EMISSAO','VALOR_NOTA'])->where(['PROCESSADA' => (($_onlyForProcess)?"0":"1")]);

        if($this->request->getData("TXT_INVOICE_RECEIVED_SEARCH_NUMBER")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('NUMERO','%'.$this->request->getData('TXT_INVOICE_RECEIVED_SEARCH_NUMBER').'%');
            });
        }
        if($this->request->getData("TXT_INVOICE_RECEIVED_SEARCH_SENDER")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('FANTASIA_EMITENTE','%'.$this->request->getData('TXT_INVOICE_RECEIVED_SEARCH_SENDER').'%');
            });
        }
        if($this->request->getData("TXT_INVOICE_RECEIVED_SEARCH_DATE")!=""){
            $query->where(function($exp,$q){
                return $exp->eq('DATE(DATA_EMISSAO)',substr($this->request->getData("TXT_INVOICE_RECEIVED_SEARCH_DATE"), 6,4)."-".substr($this->request->getData("TXT_INVOICE_RECEIVED_SEARCH_DATE"),3,2)."-".substr($this->request->getData("TXT_INVOICE_RECEIVED_SEARCH_DATE"),0,2));
            });
        }

        //verifica se foi enviada ordenacao, se nao foi a padrao eh por data de emissao em ordem decrescente
        if($this->request->getData("CB_ORDER_FIELD")!=""){
            $query->where([$this->request->getData("CB_ORDER_FIELD") => $this->request->getData("CHK_ORDER_DIRECT")]);
        }else{
			$query->order(['DATA_EMISSAO' => 'DESC']);
		}

        $this->set('invoice_list',$this->paginate($query,['limit' => 10]));
	}

	/**
	* Metodo que dispensa o processamento de uma ou mais notas fiscais recebidas
	*
	* @return boolean
	*/
	public function invoiceReceivedDismissProcess(){
        $retorno = false;

        $tblNfe = TableRegistry::get('SysNfeRecebida');
        $nfes = $this->request->getData("check_list");

        for($i=0;$i<count($nfes);$i++){
            $nfe = $tblNfe->get($nfes[$i]);
            $nfe->PROCESSADA = 1;
            $retorno = $tblNfe->save($nfe)?true:false;
        }
        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que exibe a tela de preparacao para o processamento de uma nota fiscal recebida
	* @param int $_idNFeRecebida codigo da nota fiscal recebida
	*
	* @return null
	*/
    public function invoiceReceivedPrepareToProcess($_idNFeRecebida){
    	$this->set('title',"Processamento de Nota Fiscal Recebida");

    	//carrega para a pagina as informacoes da nota fiscal
        $this->set('nfe_recebida',TableRegistry::get('SysNfeRecebida')->get($_idNFeRecebida));
    }

    /**
	* Metodo que realiza o processamento de uma determinada Nota Fiscal Recebida
	* @param int $_idNFe codigo da nota fiscal
	*
	* @return null
	*/
    public function invoiceReceivedProcess($_idNFe=""){
        $this->autoRender = false;

        $tmpLink = TableRegistry::get('TmpNfeRecebidaVinculo');
        $tblNfe  = TableRegistry::get('SysNfeRecebida');
        $idnfe_recebida = ($_idNFe!="")?$_idNFe:$this->request->getData("txtIdNfeRecebida");
        $nfe_recebida = $tblNfe->get($idnfe_recebida);
        $nfe_rec_itens = TableRegistry::get('SysNfeRecebidaItem')->find()->where(['IDNFERECEBIDA' => $idnfe_recebida]);

        //varre cada item da nota fiscal recebida
        foreach($nfe_rec_itens as $nfe_item){

            //verifica se possui vinculo permanente
            if($this->hasPermanentLink($nfe_item)){

                $tblLink = TableRegistry::get('SysProdutoNfeRecebidaItem');

                //verifica se o vinculo eh completo com codigo e nome do produto ou apenas por codigo do produto
                $vinculo = null;
                if($tblLink->find()->where(['COD_PRODUTO' => $nfe_item->COD_PRODUTO,'NOME_PROD_NFE' => $nfe_item->NOME_PRODUTO])->count()>0){
                    $vinculo = $tblLink->find()->where(['COD_PRODUTO' => $nfe_item->COD_PRODUTO,'NOME_PROD_NFE' => $nfe_item->NOME_PRODUTO])->first();
                }elseif($tblLink->find()->where(['COD_PRODUTO' => $nfe_item->COD_PRODUTO])->count()){
                    $vinculo = $tblLink->find()->where(['COD_PRODUTO' => $nfe_item->COD_PRODUTO])->first();
                }

                if($vinculo!=null){
                    //atualiza a quantidade do produto no estoque da loja default
                    $tblStock = TableRegistry::get('LojEstoque');
                    $stock = $tblStock->get(['IDLOJA' => TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR,'IDPRODUTO9' => $vinculo->IDPRODUTO]);
                    $stock->QUANTIDADE         = ($nfe_item->QUANTIDADE_COMERCIAL*$vinculo->QTDE_DESTINO);
                    $stock->DATA_ULTIMA_COMPRA = date("Y-m-d");

                    $tblStock->save($stock);

                    //busca as informacoes do produto e tambem
                    //atualiza as informacoes fiscais
                    $tblProd = TableRegistry::get('SysProduto');
                    $prod = $tblProd->get($vinculo->IDPRODUTO);
                    $prod->CSOSN = $nfe_item->CSOSN;
                    $prod->NCM   = $nfe_item->NCM;
                    $tblProd->save($prod);

                    //realiza o tratamento das divergencias de precos
                    if($prod->PRECO_COMPRA<Number::precision($nfe_item->VALOR_UNITARIO/$vinculo->QTDE_DESTINO,2)){
                        $tblDiv = TableRegistry::get('TmpNfeDivergencia');
                        $div = $tblDiv->newEntity();
                        $div->IDPRODUTO     = $vinculo->IDPRODUTO;
                        $div->NOME_PRODUTO  = $prod->NOME;
                        $div->SKU_PRODUTO   = $prod->SKU;
                        $div->PRECO_NFE     = Number::precision($nfe_item->VALOR_UNITARIO/$vinculo->QTDE_DESTINO,2);
                        $div->PRECO_PRODUTO = $prod->PRECO_COMPRA;

                        $tblDiv->save($div);
                    }

                    //salva as informacoes de movimentacao de estoque
                    $tblMove = TableRegistry::get('LojMovimentoEstoque');
                    $move = $tblMove->newEntity();
                    $move->IDLOJA         = TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR;
                    $move->IDPRODUTO      = $vinculo->IDPRODUTO;
                    $move->DATA_MOVIMENTO = date("Y-m-d H:i:s");
                    $move->QUANTIDADE     = ($nfe_item->QUANTIDADE_COMERCIAL*$vinculo->QTDE_DESTINO);
                    $move->TIPO_OPERACAO  = 'C';
                    $move->OPERACAO       = '+';
                    $move->PRECO_CUSTO    = Number::precision($nfe_item->VALOR_UNITARIO/$vinculo->QTDE_DESTINO,2);
                    $move->NOME_PRODUTO   = $prod->NOME;
                    $move->SKU_PRODUTO    = $prod->SKU;
                    $tblMove->save($move);

                    //verifica se ha necessidade de enviar para etiqueta
                    if($this->request->getData("chkEtiqueta")){
                        $tblEtq = TableRegistry::get('SysEtiqueta');
                        $tag = $tblEtq->newEntity();
                        $tag->IDPRODUTO = $vinculo->IDPRODUTO;
                        $tag->QUANTIDADE = ($nfe_item->QUANTIDADE_COMERCIAL*$vinculo->QTDE_DESTINO);
                        $tblEtq->save($tag);
                    }
                }
            }elseif($this->hasTemporaryLink($nfe_item)){ //verifica se o link eh temporario
                $vinculo = $tmpLink->get(['COD_PRODUTO_NFE' => $nfe_item->COD_PRODUTO,'NOM_PRODUTO_NFE' =>$nfe_item->NOME_PRODUTO]);

                $prods_vinculados = explode(";",$vinculo->PRODUTOS);
                $qtdes_vinculados = explode(";",$vinculo->QUANTIDADES);

                //varre a lista de produtos vinculados ao produto da NF-e
                for($i=0;$i<count($prods_vinculados);$i++){
                    if($prods_vinculados[$i]!=""){
                        //atualiza a quantidade do produto no estoque da loja default
                        $tblStock = TableRegistry::get('LojEstoque');
                        $stock = $tblStock->get(['IDLOJA' => TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR,'IDPRODUTO' => $prods_vinculados[$i]]);
                        $stock->QUANTIDADE         += $qtdes_vinculados[$i];
                        $stock->DATA_ULTIMA_COMPRA = date("Y-m-d");
                        $tblStock->save($stock);

                        //busca as informacoes do produto e tambem
                        //atualiza as informacoes fiscais
                        $tblProd = TableRegistry::get('SysProduto');
                        $prod = $tblProd->get($prods_vinculados[$i]);
                        $prod->CSOSN = $nfe_item->CSOSN;
                        $prod->NCM   = $nfe_item->NCM;
                        $tblProd->save($prod);

                        //realiza o tratamento das divergencias de precos
                        if($prod->PRECO_COMPRA<$nfe_item->VALOR_UNITARIO){
                            $tblDiv = TableRegistry::get('TmpNfeDivergencia');
                            $div = $tblDiv->newEntity();
                            $div->IDPRODUTO     = $prods_vinculados[$i];
                            $div->NOME_PRODUTO  = $prod->NOME;
                            $div->SKU_PRODUTO   = $prod->SKU;
                            $div->PRECO_NFE     = $nfe_item->VALOR_UNITARIO;
                            $div->PRECO_PRODUTO = $prod->PRECO_COMPRA;
                            $tblDiv->save($div);
                        }

                        //salva as informacoes de movimentacao de estoque
                        $tblMove = TableRegistry::get('LojMovimentoEstoque');
                        $move = $tblMove->newEntity();
                        $move->IDLOJA         = TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR;
                        $move->IDPRODUTO      = $prods_vinculados[$i];
                        $move->DATA_MOVIMENTO = date("Y-m-d H:i:s");
                        $move->QUANTIDADE     = $qtdes_vinculados[$i];
                        $move->TIPO_OPERACAO  = 'C';
                        $move->OPERACAO       = '+';
                        $move->PRECO_CUSTO    = $nfe_item->VALOR_UNITARIO;
                        $move->NOME_PRODUTO   = $prod->NOME;
                        $move->SKU_PRODUTO    = $prod->SKU;
                        $tblMove->save($move);

                        //verifica se ha necessidade de enviar para etiqueta
                        if($this->request->getData("chkEtiqueta")){
                            $tblEtq = TableRegistry::get('SysEtiqueta');
                            $tag = $tblEtq->newEntity();
                            $tag->IDPRODUTO = $prods_vinculados[$i];
                            $tag->QUANTIDADE = $qtdes_vinculados[$i];
                            $tblEtq->save($tag);
                        }
                    }
                }
            }
        }

        //limpa os links temporarios
        $tmpLink->deleteAll(array('1 = 1'));

        $nfe_recebida->PROCESSADA = 1;
        $tblNfe->save($nfe_recebida);

        //redireciona para a pagina de resultado do processamento
        $this->redirect(['controller'=>'tributary','action' => 'invoiceReceivedProcessResult']);
    }

    /**
	* Metodo que exibe o resultado do processamento da nota fiscal
	*
	* @return null
	*/
    public function invoiceReceivedProcessResult(){
        $this->set('total_divergencias',TableRegistry::get('TmpNfeDivergencia')->find()->count());
        $this->set('divergencias',TableRegistry::get('TmpNfeDivergencia')->find());
    }

    /**
	* Metodo que exibe a pagina para importacao de nota fiscal
	*
	* @return null
	*/
    public function invoiceReceivedImport(){
    	$this->set('title',"Importa&ccedil;&atilde;o de Nota Fiscal Recebida");
	}

	/**
	* Metodo que realiza o consumo das informacoes da NF-e importada
	*
	* @return null
	*/
	public function invoiceReceivedConsume(){
		$this->set('title',"Resultado do Processamento de Nota Fiscal Recebida");

		//verifica se salva as informacoes de duplicatas
        $save_calendar = $this->request->getData("chSaveCalendar");

        $process = null;

        //varre os arquivos enviados
        foreach($this->request->getData("TXT_FILE_UPLOAD") as $file){

        	//realiza o upload dos arquivos para leitura
        	//joga o arquivo na pasta temporaria do sistema
	    	$file_path = TMP.DS.$file['name'];
	        try{
	            if(move_uploaded_file($file['tmp_name'], $file_path)){

	            	//abre o arquivo e realiza a leitura
	            	$fl = new File($file_path);
	            	$xml = $fl->read();
	            	$fl->close();

	            	//coloca os dados do arquivo salvo em um array pra passar para a tela
	            	$process[] = $this->nfeSave($xml,$save_calendar);
	            }
	        }catch(Exception $x){
	            return $this->response->withStringBody( print_r($x) );
	        }
        }

        $this->set('process_list',$process);
    }

    /**
	* Metodo que salva uma nota fiscal eletronica no banco de dados
	* @param string $_xml contteudo do arquivo XML
	* @param boolean $_saveCalendar verifica se salva as informacoes no calendario
	*
	* @return Um objeto com o status do processamento (1 = Importada, 2 = Nao Importada) e tambem o numero da nota fiscal
	*/
    private function nfeSave($_xml, $_saveCalendar=true){
    	$retorno = new \stdClass();
        $retorno->STATUS      = "2";
        $retorno->NUMERO_NOTA = 0;

        $_nfe = $this->Nfe->parseXml($_xml);

        $tblNfe  = TableRegistry::get('SysNfeRecebida');
        $tblNfeI = TableRegistry::get('SysNfeRecebidaItem');
        $tblNfeD = TableRegistry::get('SysNfeRecebidaDuplicata');
        $tblCap  = TableRegistry::get('LojContasPagar');

        //verifica se a nota fiscal ja encontra-se no banco de dados
        //assim evita que varias notas sejam salvas caso o usuarios
        //faca refresh na pagina
        if($tblNfe->find()->where(['NUMERO' => $_nfe->ide->nNF])->count()==0){
	        $nfe_recebida = $tblNfe->newEntity();
	        $nfe_recebida_itens = array();
	        $nfe_recebida_dupl  = array();

	        $CPFCNPJ = !empty($_nfe->emit->CNPJ) ? $_nfe->emit->CNPJ : $_nfe->emit->CPF;

	        //busca o fornecedor da NF-e conforme o CNPJ cadastrado no sistema
	        $fornecedor = TableRegistry::get('SysFornecedor')->find()
	            ->join([
	                'table' => 'sys_fornecedor_cnpj',
	                'alias' => 'C',
	                'type'  => 'INNER',
	                'conditions' => 'SysFornecedor.IDFORNECEDOR=C.IDFORNECEDOR'
	            ])->where(['CNPJ' => $CPFCNPJ])->first();

	        $volume = 0;

			//verifica se ha informacoes de transporte
			//para buscar o numero de volumes contidos
			//na nota
	        if(isset($_nfe->transp)){
	            if(isset($_nfe->transp->vol)){
	                if(isset($_nfe->transp->vol['qVol'])){
	                    for($i=0;$i<count($_nfe->transp->vol['qVol']);$i++){
	                        $volume+= $_nfe->transp->vol['qVol'][$i];
	                    }
	                }
	            }
	        }

	        //Monta a estrutura da NF-e para salvar
	        $nfe_recebida->NUMERO            = $_nfe->ide->nNF;
	        $nfe_recebida->DATA_EMISSAO      = substr($_nfe->ide->dhEmi,0,19);
	        $nfe_recebida->VALOR_PRODUTOS    = (float)$_nfe->vProd;
	        $nfe_recebida->VALOR_NOTA        = (float)$_nfe->vNF;
	        $nfe_recebida->VALOR_FRETE       = (float)$_nfe->vFrete;
	        $nfe_recebida->NUM_VOLUME        = (float)$volume;
	        $nfe_recebida->CPFCNPJ_EMITENTE  = $CPFCNPJ;
	        $nfe_recebida->NOME_EMITENTE     = $_nfe->emit->xNome;
	        $nfe_recebida->FANTASIA_EMITENTE = (($fornecedor!=null)?$fornecedor->FANTASIA : "N&Atilde;O CADASTRADO");
	        $nfe_recebida->NFEID             = $_nfe->id;
	        $nfe_recebida->PROCESSADA        = 0;
	        $nfe_recebida->TIPO_ENTRADA      = "M"; //a entrada pode ser manual ou "A" automatica
	        $nfe_recebida->ARQUIVO_XML       = $_xml;

	        $retorno->NUMERO_NOTA = $nfe_recebida->NUMERO;

	        $retorno_nfe = $tblNfe->save($nfe_recebida)?true:false;

	    	//verifica se salvou a nota no banco de dados
	        if($retorno_nfe){

	            //realiza o salvamento dos itens da NF-e
	            for($i=0;$i<count($_nfe->det);$i++){

	                $itnfe = $tblNfeI->newEntity();
	                $itnfe->IDNFERECEBIDA        = $nfe_recebida->IDNFERECEBIDA;
	                $itnfe->IDITEM               = $_nfe->det[$i]->nItem;
	                $itnfe->EAN_ITEM             = $_nfe->det[$i]->prod->cEAN;
	                $itnfe->COD_PRODUTO          = $_nfe->det[$i]->prod->cProd;
	                $itnfe->NOME_PRODUTO         = $_nfe->det[$i]->prod->xProd;
	                $itnfe->NCM                  = $_nfe->det[$i]->prod->NCM;
	                $itnfe->UNIDADE_COMERCIAL    = $_nfe->det[$i]->prod->uCom;
	                $itnfe->QUANTIDADE_COMERCIAL = $_nfe->det[$i]->prod->qCom;
	                $itnfe->VALOR_UNITARIO       = $_nfe->det[$i]->prod->vUnCom;
	                $itnfe->CSOSN                = $_nfe->det[$i]->prod->CSOSN;

	                $tblNfeI->save($itnfe);
	            }


	            //realiza o looping buscando as cobrancas para salvar no contas a pagar
	            //e na tabela de duplicadas na NF-e
	            if(isset($_nfe->cobr)){
	                if(isset($_nfe->cobr->dup)){
	                    for($i=0;$i<count($_nfe->cobr->dup);$i++){

	                        $dup = $tblNfeD->newEntity();
	                        $dup->IDNFERECEBIDA   = $nfe_recebida->IDNFERECEBIDA;
	                        $dup->NUM_DUPLICATA   = $_nfe->cobr->dup[$i]->nDup;
	                        $dup->DATA_VENCIMENTO = $_nfe->cobr->dup[$i]->dVenc;
	                        $dup->VALOR_DUPLICATA = $_nfe->cobr->dup[$i]->vDup;

	                        $tblNfeD->save($dup);

	                        if($_saveCalendar){
	                            //monta em qual hora deverah salvar do calendario
	                            $connection = ConnectionManager::get('default');
	                            $results = $connection->execute("SELECT MAX(HOUR(DATA_VENCIMENTO)) AS LAST_HOUR FROM loj_contas_pagar WHERE DATE(DATA_VENCIMENTO)='".$_nfe->cobr->dup[$i]->dVenc."'")->fetchAll('assoc');
	                            $hora = $results[0]['LAST_HOUR'];

	                            $cap = $tblCap->newEntity();
	                            $cap->IDLOJA        = TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR;
	                            $cap->IDTIPODESPESA = TableRegistry::get('SysOpcao')->get('DEFAULT_EXPENSE_TYPE')->OPCAO_VALOR;
	                            $cap->TEM_REPEDICAO = 0;
	                            $cap->DATA_VENCIMENTO = $_nfe->cobr->dup[$i]->dVenc." $hora:00:00";
	                            $cap->NUM_DOCUMENTO   = $_nfe->cobr->dup[$i]->nDup;
	                            $cap->VALOR_ORIGINAL  = $_nfe->cobr->dup[$i]->vDup;
	                            $cap->OBSERVACAO      = $nfe_recebida->FANTASIA_EMITENTE." ".($i+1)."/".count($_nfe->cobr->dup);
	                            $tblCap->save($cap);
	                        }
	                    }
	                }
	            }
	            $retorno->STATUS = "1";
	        }
	        else{
	            $retorno->STATUS = "2";
	        }
		}else{
			//se a nota ja estiver salva, apenas retorna o numero e status de processada
			$retorno->NUMERO_NOTA = $_nfe->ide->nNF;
			$retorno->STATUS = "1";
		}

        return $retorno;
    }

    /**
	* Metodo que verifica se o item da nota fiscal recebida tem um link permanente
	* @param object $item item da nota fiscal eletronica
	*
	* @return boolean
	*/
    private function hasPermanentLink($item){
        $totalItens = 0;
        $total_both = 0;
        $total_code = 0;

        $tblNfeItem = TableRegistry::get('SysProdutoNfeRecebidaItem');
        //busca o total de itens pelo nome e codigo do produto
        $total_both = $tblNfeItem->find()->where(['COD_PRODUTO' => $item->COD_PRODUTO,'NOME_PROD_NFE' => $item->NOME_PRODUTO])->count();

        //busca o total de itens apenas pelo codigo do produto
        $total_code = $tblNfeItem->find()->where(['COD_PRODUTO' => $item->COD_PRODUTO])->count();

        //realiza o somatorio do que encontrou, isso significa que ah vinculo
        $totalItens = $total_both + $total_code;

        return ($totalItens>0)?true:false;
    }

    /**
	* Metodo que verifica se ha um link temporario associado a um produto
	* @param object $item item da nota fiscal recebida
	*
	* @return boolean
	*/
    private function hasTemporaryLink($item){
        $tmpItens = TableRegistry::get('TmpNfeRecebidaVinculo');

        $total = $tmpItens->find()->where(['COD_PRODUTO_NFE' => $item->COD_PRODUTO,'NOM_PRODUTO_NFE' => $item->NOME_PRODUTO])->count();

        return ($total>0)?true:false;
    }

    /**
	* Metodo que busca os itens da nota fiscal recebida
	* @param int $_idNFeRecebida codigo da nota fiscal recebida
	*
	* @return null
	*/
    public function invoiceReceivedItensGet($_idNFeRecebida){

        $itens_nfe = TableRegistry::get('SysNfeRecebidaItem')->find()->select(['IDITEM','COD_PRODUTO', 'NOME_PRODUTO', 'NCM', 'QUANTIDADE_COMERCIAL', 'VALOR_UNITARIO'])->where(['IDNFERECEBIDA' => $_idNFeRecebida]);

        $itens_vinculo = null;
        foreach($itens_nfe as $item){
            $it = new \stdClass();
            $it->IDITEM       = $item->IDITEM;
            $it->COD_PRODUTO  = $item->COD_PRODUTO;
            $it->NOME_PRODUTO = $item->NOME_PRODUTO;
            $it->NCM          = $item->NCM;
            $it->QUANTIDADE_COMERCIAL = $item->QUANTIDADE_COMERCIAL;
            $it->VALOR_UNITARIO = $item->VALOR_UNITARIO;
            if($this->hasPermanentLink($it)){
                $it->TIPO_VINCULO = 'P';
            }elseif($this->hasTemporaryLink($it)){
                $it->TIPO_VINCULO = 'T';
            }else{
                $it->TIPO_VINCULO = 'N';
            }

            $itens_vinculo[] = $it;
        }

        $this->set('itens',$itens_vinculo);
    }

    public function invoiceReceivedLinkAdd(){
    	$retorno = false;
        $COD_PRODUTO = $this->request->getData("COD_PRODUTO");
        $NOM_PRODUTO = $this->request->getData("NOM_PRODUTO");
        $TIP_VINCULO = $this->request->getData("TIP_VINCULO");
        $IDPRODUTO   = $this->request->getData("ID_PRODUTO");
        $QUANTIDADE  = (int)$this->request->getData("QUANTIDADE");

        if($TIP_VINCULO=="P"){
            $tblPermLink = TableRegistry::get('SysProdutoNfeRecebidaItem');
            $permLink = $tblPermLink->newEntity();
            $permLink->IDPRODUTO     = $IDPRODUTO;
            $permLink->COD_PRODUTO   = $COD_PRODUTO;
            $permLink->NOME_PROD_NFE = $NOM_PRODUTO;
            //indiferente do que for informado o destino sempre serah 1 para o vinculo permanente afim de evitar problemas no estoque
            $permLink->QTDE_DESTINO  = 1;
            $retorno = $tblPermLink->save($permLink)?true:false;
        }else{
            //se a quantidade estiver zerada ou em branco nao salvara
            if($QUANTIDADE>0){
                $tmpVinc = TableRegistry::get('TmpNfeRecebidaVinculo');
                if($tmpVinc->find()->where(['COD_PRODUTO_NFE' => $COD_PRODUTO,'NOM_PRODUTO_NFE' => $NOM_PRODUTO])->count()>0){
                    $vinc = $tmpVinc->get(['COD_PRODUTO_NFE' => $COD_PRODUTO,'NOM_PRODUTO_NFE' => $NOM_PRODUTO]);
                    $vinc->PRODUTOS .= $IDPRODUTO.";";
                    $vinc->QUANTIDADES .= $QUANTIDADE.";";
                }else{
                    $vinc = $tmpVinc->newEntity();
                    $vinc->COD_PRODUTO_NFE = $COD_PRODUTO;
                    $vinc->NOM_PRODUTO_NFE = $NOM_PRODUTO;
                    $vinc->PRODUTOS = $IDPRODUTO.";";
                    $vinc->QUANTIDADES = $QUANTIDADE.";";
                }
                $retorno = $tmpVinc->save($vinc)?true:false;
            }
        }

        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que exibe a Danfe de uma NFe Recebida
	* @param int $_idNFeRecebida codigo da nota fiscal recebida
	*
	* @return PDF
	*/
    public function invoiceReceivedShow($_idNFeRecebida){

    	$response = $this->response;

        $nfe = TableRegistry::get('SysNfeRecebida')->get($_idNFeRecebida);

        $danfe = new Danfe($nfe->ARQUIVO_XML,'P','A4');
        $id = $danfe->montaDANFE();
        $pdf = $danfe->render();

        //define o tipo do arquivo
        $response = $response->withType('pdf');

        //define o conteudo do arquivo
        $response = $response->withStringBody($pdf);

        return $response;
    }

    /*********************** ENVIO DE NOTAS FISCAIS **********************************/

    /**
	* Metodo que cria o arquivo XML da Nota Fiscal Eletronica
	* @param int $_idVenda codigo da venda
	* @param int $_cpf_na_nota flag se deseja CPF na nota
	* @param string $_cpf_alternativo CPF que nao eh o do cadastro do cliente
	*
	* @return Object
	*/

	private function _mountNfce($_idVenda,$_cpf_na_nota,$_cpf_alternativo){
		$retorno = null;

    	$_idVenda = $this->request->getData("IDVENDA");

    	//busca as informacoes da venda
    	$venda       = TableRegistry::get('LojVenda')->get($_idVenda);
        $venda_itens = TableRegistry::get('LojVendaProduto')->find()->where(['IDVENDA' =>$_idVenda]);
        $venda_paga  = TableRegistry::get('LojVendaPagamento')->find()->where(['IDVENDA' => $_idVenda]);
        $loja        = TableRegistry::get('SysLoja')->get($venda->IDLOJA);
        $cidade      = TableRegistry::get('SysCidade')->get($loja->IDCIDADE);
        $options     = TableRegistry::get('SysOpcao');

        $tem_prazo = true;
        //verifica se ha credito no pagamento para jogar a NFC-e no prazo
        foreach($_venda_pagamento as $pagamento){
            $idpagamento = TableRegistry::get('SysMeioCondicao')->find()->where(['IDCONDICAOPAGAMENTO' => $pagamento->IDCONDICAOPAGAMENTO])->first()->IDMEIOPAGAMENTO;
            $meio_pagamento = TableRegistry::get('SysMeioPagamento')->get($idpagamento);

            //se for dinheiro
            if($meio_pagamento->CODIGO_NFE=="01"){
                $tem_prazo = false;
            }

            //se for cheque
            if($meio_pagamento->CODIGO_NFE=="02"){
                $tem_prazo = false;
            }

            //se for debito
            if($meio_pagamento->CODIGO_NFE=="04"){
                $tem_prazo = false;
            }

            //se for outros
            if($meio_pagamento->CODIGO_NFE=="00"){
                $tem_prazo = false;
            }

            //aqui monta as informacoes de pagamento

            //MELHORAR ISSO
            if(!$tem_prazo){
				$detPag = new \stdClass();
				$detPag->tPag   = $meio_pagamento->CODIGO_NFE;
				$detPag->vPag   = number_format($pagamento->VALOR,2);
				$detPag->indPag = 0;
				$nfe->tagdetPag($detPag); //para o pagamento com dinheiro
			}else{
				$detPag = new \stdClass();
				$detPag->tPag   = $meio_pagamento->CODIGO_NFE;
				$detPag->vPag   = number_format($pagamento->VALOR,2);

			}
        }

        //informacoes de pagamento
		$pag = new \stdClass();
		$pag->vTroco = null;

		$nfe->tagpag($pag);

    	//inicia a criacao da NF-e
		$nfe = new Make();

		//monta o objeto com as informacoes da NF-e/NFC-e
		$inf_nfe = new \stdClass();
		$inf_nfe->versao   = $options->get('NFE_VERSION')->OPCAO_VALOR;
		$inf_nfe->Id       = null;
		$inf_nfe->pk_nItem = null;

		$nfe->taginfNFe($inf_nfe);

		$DATA_EMISSAO = date("Y-m-d H:i:s");

		//monta o objeto com as informacoes do cabecalho da Nfe
		$ide = new \stdClass();
		$ide->cUF      = $cidade->CODUF;
		$ide->natOp    = 'VENDA DE PRODUTO AO CONSUMIDOR';
		$ide->cNF      = $this->invoiceGenRandomCode();
		$ide->mod      = 65; //sempre serah NFC-e para esse sistema
		$ide->serie    = $options->get('NFCE_SERIE')->OPCAO_VALOR;
		$ide->nNF      = $this->invoiceGetNextNum();
		$ide->dhEmi    = str_replace(" ","T",$DATA_EMISSAO)."-03:00"; //coloca o GMT do Brasil que eh -3
		$ide->dhSaiEnt = null;
		$ide->tpNF     = "1";
		$ide->idDest   = '1'; //1=Operação interna; 2=Operação interestadual; 3=Operação com exterior.
		$ide->cMunFG   = $cidade->CODIBGE;
		$ide->tpImp    = '4'; //0=Sem geração de DANFE; 1=DANFE normal, Retrato; 2=DANFE normal, Paisagem;
                                //3=DANFE Simplificado; 4=DANFE NFC-e; 5=DANFE NFC-e em mensagem eletrônica
                                //(o envio de mensagem eletrônica pode ser feita de forma simultânea com a impressão do DANFE;
                                //usar o tpImp=5 quando esta for a única forma de disponibilização do DANFE).
        $ide->tpEmis   = $loja->NFE_TIPO_EMISSAO;//1=Emissão normal (não em contingência);
								//2=Contingência FS-IA, com impressão do DANFE em formulário de segurança;
								//3=Contingência SCAN (Sistema de Contingência do Ambiente Nacional);
								//4=Contingência DPEC (Declaração Prévia da Emissão em Contingência);
								//5=Contingência FS-DA, com impressão do DANFE em formulário de segurança;
								//6=Contingência SVC-AN (SEFAZ Virtual de Contingência do AN);
								//7=Contingência SVC-RS (SEFAZ Virtual de Contingência do RS);
								//9=Contingência off-line da NFC-e (as demais opções de contingência são válidas também para a NFC-e);
								//Nota: Para a NFC-e somente estão disponíveis e são válidas as opções de contingência 5 e 9.
		$ide->cDV      = null; //serah adicionado ao montar a NF-e
		$ide->tpAmb    = $loja->NFE_AMBIENTE; //1=Produção; 2=Homologação
		$ide->finNFe   = '1'; //1=NF-e normal; 2=NF-e complementar; 3=NF-e de ajuste; 4=Devolução/Retorno.
		$ide->indFinal = '1'; //0=Não; 1=Consumidor final;
		$ide->indPres  = '1'; //0=Não se aplica (por exemplo, Nota Fiscal complementar ou de ajuste);
								//1=Operação presencial;
								//2=Operação não presencial, pela Internet;
								//3=Operação não presencial, Teleatendimento;
								//4=NFC-e em operação com entrega a domicílio;
								//9=Operação não presencial, outros.
        $ide->procEmi  = '0'; //0=Emissão de NF-e com aplicativo do contribuinte;
								//1=Emissão de NF-e avulsa pelo Fisco;
								//2=Emissão de NF-e avulsa, pelo contribuinte com seu certificado digital, através do site do Fisco;
								//3=Emissão NF-e pelo contribuinte com aplicativo fornecido pelo Fisco.
        $ide->verProc  = '1.0'; //versão do aplicativo emissor


        $ide->verProc  = null;
		$ide->dhCont   = null;
		$ide->xJust    = null;

		$nfe->tagide($emite);

		//dados do emitente
		$emite = new \stdClas();
		$emite->CNPJ  = $loja->CNPJ;
		$emite->CPF   = null;
		$emite->xNome = $loja->RAZAO_SOCIAL;
        $emite->xFant = $loja->NOME_FANTASIA;
        $emite->IE    = $loja->INSCRICAO_ESTADUAL;
        $emite->IEST  = null;
        $emite->IM    = $loja->INSCRICAO_MUNICIPAL;
        $emite->CNAE  = $loja->CNAE;
        $emite->CRT   = '1';

        $nfe->tagemit($emite);

        //Dados do endereco do emitente
        $endemit = new \stdClass();
        $endemit->xLgr    = $loja->ENDERECO;
        $endemit->nro     = $loja->NUM_ENDERECO;
        $endemit->xCpl    = $loja->COMPLEMENTO_ENDERECO;
        $endemit->xBairro = $loja->BAIRRO;

        $endemit->cMun  = $cidade->CODIBGE;
        $endemit->xMun  = $cidade->NOME;
        $endemit->UF    = $cidade->UF;
        $endemit->CEP   = str_replace("-","",$loja->CEP);
        $endemit->cPais = '1058';
        $endemit->xPais = 'BRASIL';
        $endemit->fone  = str_replace("-","",
        	str_replace(" ","",
        		str_replace(")","",
        			str_replace("(","",$loja->TELEFONE)
        		)
        	)
        );

		$nfe->tagenderEmit($endemit);

		//Adiciona o cliente em caso queira CPF na Nota
        if($_venda->IDCLIENTE!=""){
            if($_venda->IDCLIENTE!=0){
                if($_cpf_na_nota==1){
                    $cliente = TableRegistry::get('SysCliente')->get($_venda->IDCLIENTE);

                    //destinatário
                    //melhorar o codigo para quando o cliente for pessoa juridica e tiver CNPJ
                    $dest = new \stdClass();
                    $dest->CNPJ          = null;
                    $dest->CPF           = (($_cpf_alternativo!="")?$_cpf_alternativo:$cliente->CPF);
                    $dest->idEstrangeiro = NULL;
                    $dest->xNome         = (($_cpf_alternativo!="")?$_cpf_alternativo:$cliente->NOME);
                    $dest->indIEDest     = null;
                    $dest->IE            = null;
                    $dest->ISUF          = null;
                    $dest->IM            = null;
                    $dest->email         = (($_cpf_alternativo!="")?$_cpf_alternativo:$cliente->EMAIL);
                    $nfe->tagdest($dest);
                }
            }
        }


        //produtos e kits
        $i = 1;
        $valTotal = 0;
        $totalTributos = 0;
        $totalFederal = 0;
        $totalEstadual = 0;
        $totalMunicipal = 0;
        foreach($_venda_itens as $item_venda){
        	//busca as informacoes do produto
            $produto = TableRegistry::get('SysProduto')->get($item_venda->IDPRODUTO);

            //se for um produto simples
            if($produto->ESTRUTURA=="S"){

				//produto da NF-e
            	$prod = new \stdClass();
                $prod->item     = $i;
                $prod->cProd    = $produto->SKU;
                $prod->cEAN     = null;
                $prod->xProd    = $produto->NOME;
                $prod->NCM      = $produto->NCM;
                $prod->cBenef   = null;
                $prod->EXTIPI   = null;
                $prod->CFOP     = '5102';
                $prod->uCom     = $item_venda->UNIDADE_MEDIDA;
                $prod->qCom     = $item_venda->QUANTIDADE;
                $prod->vUnCom   = number_format($item_venda->PRECO_UNITARIO,2);
                $prod->vProd    = number_format($item_venda->PRECO_UNITARIO,2);
                $prod->cEANTrib = null;
                $prod->uTrib    = $item_venda->UNIDADE_MEDIDA;
                $prod->qTrib    = $item_venda->QUANTIDADE;
                $prod->vUnTrib  = number_format($item_venda->PRECO_UNITARIO,2);
                $prod->vFrete   = null;
                $prod->vSeg     = null;
                $prod->vDesc    = (($item_venda->DESCONTO>0)?number_format($item_venda->DESCONTO,2):'');
                $prod->vOutro   = null;
                $prod->indTot   = 1;
                $prod->xPed     = null;
                $prod->nItemPed = null;
                $prod->nFCI     = null;
                $nfe->tagprod($prod);

                //incrementa o contador do produto
                $i++;

            	$valTotal+= $item_venda->PRECO_UNITARIO*$item_venda->QUANTIDADE;

                //realiza a busca dos impostos do site IBPT
                if($options->get('TAXES_APIKEY')!=""){

	                $ibpt = new Ibpt($loja->CNPJ,$options->get('TAXES_APIKEY')->OPCAO_VALOR);

	                $ibpt_resp = $ibpt->productTaxes(
	                	$cidade->UF,
	                	$produto->NCM,
	                	0,
	                	$produto->NOME,
	                	$produto->UNIDADE_MEDIDA,
	                	$produto->PRECO_VENDA,
	                	'SEM GTIN',
	                	null
	                );

	                $imp = new \stdClass();
	                $imp->item = $prod->item;
	                $imp->vTotTrib = number_format(($item_venda->PRECO_UNITARIO*$item_venda->QUANTIDADE)*(($ibpt_resp->Nacional+$ibpt_resp->Estadual+$ibpt_resp->Importado+$ibpt_resp->Municipal)/100),2);
	                $totalTributos  += $imp->vTotTrib;

	                $totalFederal   += number_format(($item_venda->PRECO_UNITARIO*$item_venda->QUANTIDADE)*$ibpt_resp->Nacional,2);
	                $totalEstadual  += number_format(($item_venda->PRECO_UNITARIO*$item_venda->QUANTIDADE)*$ibpt_resp->Estadual,2);
	                $totalImportado += number_format(($item_venda->PRECO_UNITARIO*$item_venda->QUANTIDADE)*$ibpt_resp->Importado,2);
	                $totalMunicipal += number_format(($item_venda->PRECO_UNITARIO*$item_venda->QUANTIDADE)*$ibpt_resp->Municipal,2);

	                $nfe->tagimposto($imp);
				}

                //ICMS
                $icmssn = new \stdClass();
                $icmssn->item        = $prod->item;
                $icmssn->orig        = 0;
                $icmssn->CSOSN       = ($produto->CSOSN=="")?"103":$produto->CSOSN;
                $icmssn->pCredSN     = 0;
                $icmssn->vCredICMSSN = 0;
                $icmssn->modBCST     = null;
				$icmssn->pMVAST      = null;
				$icmssn->pRedBCST    = null;
				$icmssn->vBCST       = null;
				$icmssn->pICMSST     = null;
				$icmssn->vICMSST     = null;
				$icmssn->vBCFCPST    = null; //incluso no layout 4.00
				$icmssn->pFCPST      = null; //incluso no layout 4.00
				$icmssn->vFCPST      = null; //incluso no layout 4.00
				$icmssn->vBCSTRet    = null;
				$icmssn->pST         = null;
				$icmssn->vICMSSTRet  = null;
				$icmssn->vBCFCPSTRet = null; //incluso no layout 4.00
				$icmssn->pFCPSTRet   = null; //incluso no layout 4.00
				$icmssn->vFCPSTRet   = null; //incluso no layout 4.00
				$icmssn->modBC       = null;
				$icmssn->vBC         = null;
				$icmssn->pRedBC      = null;
				$icmssn->pICMS       = null;
				$icmssn->vICMS       = null;
				$icmssn->pRedBCEfet  = null;
				$icmssn->vBCEfet     = null;
				$icmssn->pICMSEfet   = null;
				$icmssn->vICMSEfet   = null;
				$icmssn->vICMSSubstituto = null;

                $nfe->tagICMSSN($icmssn);
            }
            else{
            	//aqui inicia o tratamento do produto composto
                $valTotalItens = 0;
                $prodItens = TableRegistry::get('LojVendaProdutoItem')->find()->where(['IDLOJA' => $loja->IDLOJA,'IDVENDA' => $_idVenda,'IDPRODUTO' => $produto->IDPRODUTO]);
                //varre os itens que compoem o produto
                foreach($prodItens as $prdIt){

                    $itProd = TableRegistry::get('SysProduto')->get($prdIt->IDPRODUTO_FILHO);

                    $prod = new \stdClass();
	                $prod->item     = $i;
	                $prod->cProd    = $prdIt->SKU_PRODUTO_FILHO;
	                $prod->cEAN     = null;
	                $prod->xProd    = $prdIt->NOME_PRODUTO_FILHO;
	                $prod->NCM      = $itProd->NCM;
	                $prod->cBenef   = null;
	                $prod->EXTIPI   = null;
	                $prod->CFOP     = '5102';
	                $prod->uCom     = $itProd->UNIDADE_MEDIDA;
	                $prod->qCom     = $item_venda->QUANTIDADE;
	                $prod->vUnCom   = number_format($prdIt->PRECO_VENDA,2);
	                $prod->vProd    = number_format($prdIt->PRECO_VENDA,2);
	                $prod->cEANTrib = null;
	                $prod->uTrib    = $itProd->UNIDADE_MEDIDA;
	                $prod->qTrib    = $item_venda->QUANTIDADE;
	                $prod->vUnTrib  = number_format($prdIt->PRECO_VENDA,2);
	                $prod->vFrete   = null;
	                $prod->vSeg     = null;
	                $prod->vDesc    = (($prdIt->DESCONTO>0)?number_format($prdIt->DESCONTO,2):null);
	                $prod->vOutro   = null;
	                $prod->indTot   = 1;
	                $prod->xPed     = null;
	                $prod->nItemPed = null;
	                $prod->nFCI     = null;
	                $nfe->tagprod($prod);

	                //incrementa o contador do item
                    $i++;

	                //realiza a busca dos impostos do site IBPT
	                if($options->get('TAXES_APIKEY')!=""){

		                $ibpt = new Ibpt($loja->CNPJ,$options->get('TAXES_APIKEY')->OPCAO_VALOR);

		                $ibpt_resp = $ibpt->productTaxes(
		                	$cidade->UF,
		                	$itProd->NCM,
		                	0,
		                	$prdIt->NOME_PRODUTO_FILHO,
		                	$itProd->UNIDADE_MEDIDA,
		                	$prdIt->PRECO_VENDA,
		                	'SEM GTIN',
		                	null
		                );

		                $imp = new \stdClass();
		                $imp->item = $i;
		                $imp->vTotTrib = number_format(($prdIt->PRECO_VENDA*$item_venda->QUANTIDADE)*(($ibpt_resp->Nacional+$ibpt_resp->Estadual+$ibpt_resp->Importado+$ibpt_resp->Municipal)/100),2);
		                $totalTributos  += $imp->vTotTrib;

		                $totalFederal   += number_format(($itProd->PRECO_VENDA*$item_venda->QUANTIDADE)*$ibpt_resp->Nacional,2);
		                $totalEstadual  += number_format(($itProd->PRECO_VENDA*$item_venda->QUANTIDADE)*$ibpt_resp->Estadual,2);
		                $totalImportado += number_format(($itProd->PRECO_VENDA*$item_venda->QUANTIDADE)*$ibpt_resp->Importado,2);
		                $totalMunicipal += number_format(($itProd->PRECO_VENDA*$item_venda->QUANTIDADE)*$ibpt_resp->Municipal,2);

		                $nfe->tagimposto($nItem, $tributos );
					}

	                //ICMS
	                $icmssn = new \stdClass();
	                $icmssn->item        = $i;
	                $icmssn->orig        = 0;
	                $icmssn->CSOSN       = ($itProd->CSOSN=="")?"103":$itProd->CSOSN;
	                $icmssn->pCredSN     = 2.0;
	                $icmssn->vCredICMSSN = 20.00;
	                $icmssn->modBCST     = null;
					$icmssn->pMVAST      = null;
					$icmssn->pRedBCST    = null;
					$icmssn->vBCST       = null;
					$icmssn->pICMSST     = null;
					$icmssn->vICMSST     = null;
					$icmssn->vBCFCPST    = null; //incluso no layout 4.00
					$icmssn->pFCPST      = null; //incluso no layout 4.00
					$icmssn->vFCPST      = null; //incluso no layout 4.00
					$icmssn->vBCSTRet    = null;
					$icmssn->pST         = null;
					$icmssn->vICMSSTRet  = null;
					$icmssn->vBCFCPSTRet = null; //incluso no layout 4.00
					$icmssn->pFCPSTRet   = null; //incluso no layout 4.00
					$icmssn->vFCPSTRet   = null; //incluso no layout 4.00
					$icmssn->modBC       = null;
					$icmssn->vBC         = null;
					$icmssn->pRedBC      = null;
					$icmssn->pICMS       = null;
					$icmssn->vICMS       = null;
					$icmssn->pRedBCEfet  = null;
					$icmssn->vBCEfet     = null;
					$icmssn->pICMSEfet   = null;
					$icmssn->vICMSEfet   = null;
					$icmssn->vICMSSubstituto = null;

	                $nfe->tagICMSSN($icmssn);
                }

                //adiciona este item extra para fechar o preco se houver divergencia
                //entre os itens do kit
                if($valTotalItens < $item_venda->PRECO_UNITARIO){
                	$prod = new \stdClass();
	                $prod->item     = $i;
	                $prod->cProd    = "EMB-DEF";
	                $prod->cEAN     = null;
	                $prod->xProd    = "EMBALAGEM PARA PRESENTE";
	                $prod->NCM      = "63052000";
	                $prod->cBenef   = null;
	                $prod->EXTIPI   = null;
	                $prod->CFOP     = '5102';
	                $prod->uCom     = 'UN'; //melhorar aqui para quando colocar restaurante
	                $prod->qCom     = 1;
	                $prod->vUnCom   = number_format($item_venda->PRECO_UNITARIO-$valTotalItens,2);
	                $prod->vProd    = number_format($item_venda->PRECO_UNITARIO-$valTotalItens,2);
	                $prod->cEANTrib = null;
	                $prod->uTrib    = 'UN';//idem uCom
	                $prod->qTrib    = 1;
	                $prod->vUnTrib  = number_format($item_venda->PRECO_UNITARIO-$valTotalItens,2);
	                $prod->vFrete   = null;
	                $prod->vSeg     = null;
	                $prod->vDesc    = (($item_venda->DESCONTO>0)?number_format($item_venda->DESCONTO,2):'');
	                $prod->vOutro   = null;
	                $prod->indTot   = 1;
	                $prod->xPed     = null;
	                $prod->nItemPed = null;
	                $prod->nFCI     = null;
	                $nfe->tagprod($prod);

					//incrementa o contador
                    $i++;

                    $valTotal+= $prod->vUnTrib;


                    //realiza a busca dos impostos do site IBPT
	                if($options->get('TAXES_APIKEY')!=""){

		                $ibpt = new Ibpt($loja->CNPJ,$options->get('TAXES_APIKEY')->OPCAO_VALOR);

		                $ibpt_resp = $ibpt->productTaxes(
		                	$cidade->UF,
		                	$produto->NCM,
		                	0,
		                	$produto->NOME,
		                	'UN',
		                	$prod->vUnTrib,
		                	'SEM GTIN',
		                	null
		                );

		                $imp = new \stdClass();
		                $imp->item = $prod->item;
		                $imp->vTotTrib = number_format($prod->vUnTrib*(($ibpt_resp->Nacional+$ibpt_resp->Estadual+$ibpt_resp->Importado+$ibpt_resp->Municipal)/100),2);
		                $totalTributos  += $imp->vTotTrib;

		                $totalFederal   += number_format($prod->vUnTrib*$ibpt_resp->Nacional,2);
		                $totalEstadual  += number_format($prod->vUnTrib*$ibpt_resp->Estadual,2);
		                $totalImportado += number_format($prod->vUnTrib*$ibpt_resp->Importado,2);
		                $totalMunicipal += number_format($prod->vUnTrib*$ibpt_resp->Municipal,2);

		                $nfe->tagimposto($imp);
					}

                    //ICMS
	                $icmssn = new \stdClass();
	                $icmssn->item        = $prod->item;
	                $icmssn->orig        = 0;
	                $icmssn->CSOSN       = ($produto->CSOSN=="")?"103":$produto->CSOSN;
	                $icmssn->pCredSN     = 2.0;
	                $icmssn->vCredICMSSN = 20.00;
	                $icmssn->modBCST     = null;
					$icmssn->pMVAST      = null;
					$icmssn->pRedBCST    = null;
					$icmssn->vBCST       = null;
					$icmssn->pICMSST     = null;
					$icmssn->vICMSST     = null;
					$icmssn->vBCFCPST    = null; //incluso no layout 4.00
					$icmssn->pFCPST      = null; //incluso no layout 4.00
					$icmssn->vFCPST      = null; //incluso no layout 4.00
					$icmssn->vBCSTRet    = null;
					$icmssn->pST         = null;
					$icmssn->vICMSSTRet  = null;
					$icmssn->vBCFCPSTRet = null; //incluso no layout 4.00
					$icmssn->pFCPSTRet   = null; //incluso no layout 4.00
					$icmssn->vFCPSTRet   = null; //incluso no layout 4.00
					$icmssn->modBC       = null;
					$icmssn->vBC         = null;
					$icmssn->pRedBC      = null;
					$icmssn->pICMS       = null;
					$icmssn->vICMS       = null;
					$icmssn->pRedBCEfet  = null;
					$icmssn->vBCEfet     = null;
					$icmssn->pICMSEfet   = null;
					$icmssn->vICMSEfet   = null;
					$icmssn->vICMSSubstituto = null;

	                $nfe->tagICMSSN($icmssn);
                }

				$valTotal+= $item_venda->PRECO_UNITARIO*$item_venda->QUANTIDADE;
            }
        }

        $icmstot = new stdClass();
		$icmstot->vBC        = 0.00;
		$icmstot->vICMS      = 0.00;
		$icmstot->vICMSDeson = 0.00;
		$icmstot->vFCP       = 0.00; //incluso no layout 4.00
		$icmstot->vBCST      = 0.00;
		$icmstot->vST        = 0.00;
		$icmstot->vFCPST     = 0.00; //incluso no layout 4.00
		$icmstot->vFCPSTRet  = 0.00; //incluso no layout 4.00
		$icmstot->vProd      = number_format($_venda->SUBTOTAL,2);
		$icmstot->vFrete     = 0.00;
		$icmstot->vSeg       = 0.00;
		$icmstot->vDesc      = number_format($_venda->DESCONTO,2);
		$icmstot->vII        = 0.00;
		$icmstot->vIPI       = 0.00;
		$icmstot->vIPIDevol  = 0.00; //incluso no layout 4.00
		$icmstot->vPIS       = 0.00;
		$icmstot->vCOFINS    = 0.00;
		$icmstot->vOutro     = 0.00;
		$icmstot->vNF        = number_format($_venda->SUBTOTAL,2);
		$icmstot->vTotTrib   = number_format($totalTributos,2);

        $nfe->tagICMSTot($icmstot);

        // Calculo de carga tributária similar ao IBPT - Lei 12.741/12
        $textoIBPT = "Valor Aprox. Tributos R$ {$totalTributos} - {$totalFederal} Federal, {$totalEstadual} Estadual, {$totalMunicipal} Municipal e Importado {$totalImportado}.";

        $infAdic = new \stdClass();
        $infAdic->infAdFisco = "";
        $infAdic->infCpl = "Venda Nº{$_venda->IDVENDA} - {$textoIBPT}";
        $nfe->taginfAdic($infAdic);

        //frete (sem frete)
        $frt = new stdClass();
		$frt->modFrete = 9;

		$nfe->tagtransp($frt);

        //monta o XML da NFC-e
        $resp = $nfe->monta();
        if($resp){
            //monta o objeto de retorno com algumas informacoes
			//necessarias para salvar no banco de dados
			$retorno->DATA_EMISSAO = $DATA_EMISSAO;
			$retorno->VALOR_NOTA   = $valTotal;
			$retorno->IDVENDA      = $_idVenda;
			$retorno->CHAVE_NFE    = $nfe->getChave();
			$retorno->ARQUIVO_XML  = $nfe->getXML();
        }

        return $retorno;
	}

	/**
	* Metodo que assina o arquivo XML da NF-e
	* @param string $xml string do arquivo XML
	* @param int $idLoja codigo da loja (necesario para algumas configuracoes)
	*
	* @return Arquivo XML assinado
	*/
	private function _signNFe($xml,$idLoja){
		$xmlAssinado = null;

		$store = TableRegistry::get('SysLoja')->get($idLoja);
		$config = [
			'atualizacao' => date("Y-m-d H:i:s"),
			'tpAmb'       => $store->NFE_AMBIENTE,
			'razaosocial' => $store->RAZAO_SOCIAL,
			'cnpj'        => $store->CNPJ,
			"ie"          => $store->INSCRICAO_ESTADUAL,
			"siglaUF"     => TableRegistry::get('SysCidade')->get($store->IDCIDADE)->COD_UF,
			"schemes"     => "PL_009_V4",
			"versao"      => '4.00',
			"tokenIBPT"   => TableRegistry::get('SysOpcao')->get('TAXES_APIKEY')->OPCAO_VALOR,
			"CSC"         => $store->NFE_CSC,
			"CSCid"       => $store->NFE_CSC_TOKEN,
			"aProxyConf"  => [
				"proxyIp"   => "",
				"proxyPort" => "",
				"proxyUser" => "",
				"proxyPass" => ""
			]
		];

		//converte a variavel de configuracao
		$configJson = json_encode($config);

		//certificado digital
		$this->file_get_contents_curl($store->NFE_CERT_DIGITAL);

		//realiza a assinatura do certificado
		$tools = new NFePHP\NFe\Tools($configJson, NFePHP\Common\Certificate::readPfx($certificadoDigital, $store->NFE_CERT_PASSWORD));
		try {
			// O conteudo do XML assinado fica armazenado na variavel $xmlAssinado
		    $xmlAssinado = $tools->signNFe($xml);
		} catch (\Exception $e) {
		    //aqui voce trata possiveis exceptions da assinatura
		    exit($e->getMessage());
		}

		return $xmlAssinado;
	}

	/**
	* Metodo que realiza os testes da SEFAZ e transmite o arquivo XML assinado
	* @param int $_xml string contendo o XML assinado
	*
	* @return
	*/
	private function _sendNfe($_xml){
		$retorno = null;

		//monta o lote para envio da NF-e
		try {
		    $idLote = str_pad($this->invoiceGetNextNum(), 15, '0', STR_PAD_LEFT); // Identificador do lote
		    $resp = $tools->sefazEnviaLote([$_xml], $idLote);

		    $st = new NFePHP\NFe\Common\Standardize();
		    $std = $st->toStd($resp);
		    if ($std->cStat != 103) {
		        //erro registrar e voltar
		        exit("[$std->cStat] $std->xMotivo");
		    }
		    $recibo = $std->infRec->nRec; // Vamos usar a variável $recibo para consultar o status da nota
		} catch (\Exception $e) {
		    //aqui você trata possiveis exceptions do envio
		    exit($e->getMessage());
		}

		//consulta o recibo
		try {
		    $protocolo = $tools->sefazConsultaRecibo($recibo);
		} catch (\Exception $e) {
		    //aqui você trata possíveis exceptions da consulta
		    exit($e->getMessage());
		}

		$request = "<XML conteudo original do documento que quer protocolar>";
		$response = "<XML conteudo do retorno com a resposta da SEFAZ>";

		try {
		    $retorno = Complements::toAuthorize($request, $response);
		} catch (\Exception $e) {
		    $retorno = "Erro: " . $e->getMessage();
		}

		return $retorno;
	}

	/**
	* Metodo que realiza o tratamento das informacoes da NFC-e
	* Entre os tratamentos estao:
	* montagem do XML,
	* assinatura do XML,
	* teste e envio ao SEFAZ,
	* envio do XML para o e-mail do cliente se houver cliente
	*
	* @return Objeto contendo os dados da operacao
	*/
    public function invoiceSaleSend(){
    	$retorno = new \stdClass();
        $retorno->IDNFCE = 0;
        $retorno->STATUS = NULL;
        $retorno->IDVENDA = $this->request->getData("IDVENDA");

        $IDLOJA = TableRegistry::get('LojVenda')->get( $this->request->getData("IDVENDA") )->IDLOJA;


    	//chama a montagem do XML da NF-e
    	$obj_nfe = $this->_mountNfce(
    		$this->request->getData("IDVENDA"),
    		$this->request->getData("CPFNANOTA"),
    		$this->request->getData("CPFALTERNA"));
        if($obj_nfe!=null){
        	//assina o arquivo XML
        	$xmlSigned = $this->_signNFe($obj_nfe->ARQUIVO_XML,$IDLOJA);
			if($xmlSigned!=null){
				//verifica se eh um XML no retorno do envio
				$snd = $this->_sendNFe($xmlSigned);
				if(XMLReader::isValid($snd)){
					//salva as informacoes da nota no banco de dados
					$tblNfe = TableRegistry::get('SysNfce');
					$itnfe  = $tblNfe->newEntity();
					$itnfe->DATA_EMISSAO = $obj_nfe->DATA_EMISSAO;
					$itnfe->VALOR_NOTA   = $obj_nfe->VALOR_NOTA;
					$itnfe->IDVENDA      = $obj_nfe->IDVENDA;
					$itnfe->CHAVE_NFE    = $obj_nfe->CHAVE_NFE;
					$itnfe->ARQUIVO_XML  = $obj_nfe->ARQUIVO_XML;
					$tblNfe->save($itnfe);

					$retorno->IDNFCE = $itnfe->IDNFCE;
				}else{

				}
			}
		}else{
			$retorno->STATUS = "H&aacute; um problema no XML da NFC-e, por favor verifique!";
		}



        return $this->response->withStringBody( json_encode($retorno) );
	}

	/**
	* Metodo que gera o codigo randomico da nota fiscal eletronica
	* @param int $length tamanho (padrao = 8)
	*
	* @return
	*/
	private function invoiceGenRandomCode($length=8){
        $numero = '';
        for ($x=0;$x<$length;$x++){
            $numero .= rand(0,9);
        }
        return $numero;
    }

    /**
	* Metodo que gera o proximo numero de nota fiscal
	* se nao houver registro retorna 1
	* @return int
	*/
    private function invoiceGetNextNum(){
        $sql = "SELECT IFNULL(MAX(IDNFCE)+1,1) AS IDNFCE FROM sys_nfce";
        $connection = ConnectionManager::get('default');
        $results = $connection->execute($sql)->fetchAll('assoc');
        return $results[0]['IDNFCE'];
    }
    /************************************* DEVOLUCOES ********************************************/

    /**
	* Metodo que monta os filtros da busca de devolucoes
	*
	* @return string
	*/
    public function listReturnFilter(){
		$this->Filter->addFilter("N&uacute;mero da Nota","TXT_INVOICE_RETURNED_SEARCH_NUMBER","number");
        $this->Filter->addFilter("Data de emiss&atilde;o","TXT_INVOICE_RETURNED_SEARCH_DATE","date");

        $ords = array();

		$ord3 = new \stdClass();
		$ord3->value   = "NUMERO";
		$ord3->key = "N&uacute;mero";
		$ords[] = $ord3;

		$ord4 = new \stdClass();
		$ord4->value   = "DATA_EMISSAO";
		$ord4->key = "Data de Emiss&atilde;o";
		$ords[] = $ord4;

		$ord5 = new \stdClass();
		$ord5->value   = "VALOR_NOTA";
		$ord5->key = "Valor da Nota";
		$ords[] = $ord5;

		$this->Filter->addOrder($ords);

        return $this->response->withStringBody( $this->Filter->mountFilters() );
	}

    /**
	* Metodo que monta a pagina inicial da listagem de devolucoes
	*
	* @return null
	*/
    public function listReturn(){
		$this->set('title','Devolu&ccedil;&otilde;es');

		$this->set('url_filter','/tributary/list_return_filter');
		$this->set('url_data','/tributary/list_return_data');
	}

	/**
	* Metodo que realiza a busca das informacoes de devolucoes
	*
	* @return null
	*/
	public function listReturnData(){
        $query = TableRegistry::get('SysNfeDevolucao')->find();
        $query->select(['IDNFEDEVOLUCAO','NOME_DESTINATARIO','NUMERO','DATA_EMISSAO','VALOR_NOTA']);

        if($this->request->getData("TXT_INVOICE_RETURNED_SEARCH_NUMBER")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('NUMERO','%'.$this->request->getData('TXT_INVOICE_RETURNED_SEARCH_NUMBER').'%');
            });
        }
        if($this->request->getData("TXT_INVOICE_RETURNED_SEARCH_DATE")!=""){
            $query->where(function($exp,$q){
                return $exp->eq('DATE(DATA_EMISSAO)',substr($this->request->getData("TXT_INVOICE_RETURNED_SEARCH_DATE"), 6,4)."-".substr($this->request->getData("TXT_INVOICE_RECEIVED_SEARCH_DATE"),3,2)."-".substr($this->request->getData("TXT_INVOICE_RECEIVED_SEARCH_DATE"),0,2));
            });
        }

        //verifica se foi enviada ordenacao, se nao foi a padrao eh por data de emissao em ordem decrescente
        if($this->request->getData("CB_ORDER_FIELD")!=""){
            $query->where([$this->request->getData("CB_ORDER_FIELD") => $this->request->getData("CHK_ORDER_DIRECT")]);
        }else{
			$query->order(['DATA_EMISSAO' => 'DESC']);
		}

        $this->set('invoice_list',$this->paginate($query,['limit' => 10]));
	}

	public function issueReturn(){
		$this->set('title','Devolu&ccedil;&atilde;o de Mercadoria');
		$this->set('nfe_recebida_list',TableRegistry::get('SysNfeRecebida')->find()->select(['IDNFERECEBIDA','NUMERO','NOME_EMITENTE']));
	}

	public function issueReturnItens(){
		$this->set('item_list',TableRegistry::get('SysNfeRecebidaItem')->find()->select(['IDITEM','COD_PRODUTO','NOME_PRODUTO','QUANTIDADE_COMERCIAL','VALOR_UNITARIO'])->where(['IDNFERECEBIDA' => $this->request->getData("IDNFERECEBIDA")]));
    }

    public function makeReturn(){
        $this->autoRender = false;
        print_r($this->request->getData());
    }
}
