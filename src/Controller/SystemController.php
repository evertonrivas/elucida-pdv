<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;
use Cake\Mailer\Email;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Number;
use Cake\I18n\Date;

class SystemController extends AppController{

    public function initialize() {
        parent::initialize();
        $this->loadComponent("Paginator");
        $this->loadComponent("Filter");
        ob_start("ob_gzhandler");
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->Auth->allow(['checkConfig']);
    }

    /**
	* Metodo que trata as informacoes da pagina inicial do sistema
	*
	* @return null
	*/
    public function index(){
		if($this->Auth->user()['role']!="admin"){
			//verifica se eh primeiro acesso
			if($this->Auth->user()['first_access']=="S"){
				$this->redirect(['controller' => 'Users','action' => 'password']);
			}else{
				//se nao for primeiro acesso e for vendedor
				if($this->Auth->user()['role']=="seller"){
					$this->redirect(['controller' => 'Retail','action' => 'boxStatusCheck']);
				}
			}
		}
    }

	/**
	* Metodo que exibe a pagina de opcoes do sistema
	*
	* @return null
	*/
	public function options(){
		$this->set('title',"Op&ccedil;&otilde;es do Sistema");
		$this->set('url_data','/system/options_data_get');
		$this->set('url_data_save','/system/options_save');
	}

	/**
	* Metodo que monta as opcoes do sistema
	*
	* @return null
	*/
	public function optionsDataGet(){
		$tblOpcao = TableRegistry::get('SysOpcao');
        $tblOpFin = TableRegistry::get('SysOperacaoFinanceira');
        $tblStore = TableRegistry::get('SysLoja');
        $tblTpDes = TableRegistry::get('SysTipoDespesa');
        $tblPayCd = TableRegistry::get('SysCondicaoPagamento');
        $tblPrdTp = TableRegistry::get('SysProdutoTipo');
        $tblPayMod = TableRegistry::get('SysMeioPagamento');
        $tblTemplate = TableRegistry::get('SysTemplateEmail');
        $tblJobTitle = TableRegistry::get('SysCargo');

        $this->set('store_list',$tblStore->find()->select(['IDLOJA','NOME'])->order(['NOME' => 'ASC']));
        $this->set('opfinanc_list',$tblOpFin->find()->select(['IDOPERACAOFINANCEIRA','NOME'])->order(['NOME' => 'ASC']));
        $this->set('tipdesp_list',$tblTpDes->find()->select(['IDTIPODESPESA','NOME'])->order(['NOME' => 'ASC']));
        $this->set('payment_list',$tblPayCd->find()->select(['IDCONDICAOPAGAMENTO','NOME'])->order(['NOME' => 'ASC']));
        $this->set('product_type_list',$tblPrdTp->find()->select(['IDPRODUTOTIPO','DESCRICAO'])->order(['DESCRICAO' => 'ASC']));
        $this->set('paymethod_list',$tblPayMod->find()->select(['IDMEIOPAGAMENTO','NOME'])->order(['NOME' => 'ASC']));
        $this->set('template_list',$tblTemplate->find()->select(['IDTEMPLATEEMAIL','NOME'])->order(['NOME' => 'ASC']));
        $this->set('job_title_list',$tblJobTitle->find()->select(['IDCARGO','NOME'])->order(['NOME' => 'ASC']));
        $this->set('tblOpcao',$tblOpcao);
	}

	/**
	* Metodo que salva as opcoes do sistema
	*
	* @return
	*/
	public function optionsSave(){
		$tblOpcao = TableRegistry::get("SysOpcao");
        $tblOpcao->deleteAll(null);

        $this->autoRender = false;

        $retorno = false;

        foreach($this->request->getData() as $name => $value){

            $opcao = $tblOpcao->newEntity();

            $opcao->OPCAO_NOME  = $name;
            $opcao->OPCAO_VALOR = $value;

            $retorno = ($tblOpcao->save($opcao))?true:false;
        }

        return $this->response->withStringBody( $retorno );
	}

	/**
	* Metodo que exibe a pagina inicial das lojas do sistema
	*
	* @return null
	*/
	public function store(){
		$this->set('title',"Lojas");
		$this->set('url_filter','/system/store_filter');
        $this->set('url_data','/system/store_data');
	}

	/**
	* Metodo que monta os filtros do sistema
	*
	* @return string
	*/
	public function storeFilter(){
        $this->Filter->addFilter("Nome","TXT_STORE_SEARCH_NAME","text");
        $this->Filter->addFilter("Respons&aacute;vel","TXT_STORE_SEARCH_RESPONSIBLE","text");
        $this->Filter->addFilter("CEP","TXT_STORE_SEARCH_CEP","text");
        $this->Filter->addFilter("Telefone","TXT_STORE_SEARCH_FONE","text");


		$ords = array();

		$ordn = new \stdClass();
		$ordn->key   = "nome";
		$ordn->value = "NOME";
		$ords[] = $ordn;

		$ordl = new \stdClass();
		$ordl->key   = "Respons&aacute;vel";
		$ordl->value = "RESPONSAVEL";
		$ords[] = $ordl;

		$ordr = new \stdClass();
		$ordr->key   = "Telefone";
		$ordr->value = "TELEFONE";
		$ords[] = $ordr;

		$this->Filter->addOrder($ords);

        return $this->response->withStringBody( $this->Filter->mountFilters() );
	}

	/**
	* Metodo que realiza busca das lojas do sistmea
	*
	* @return null
	*/
	public function storeData(){
		$tblStore = TableRegistry::get('SysLoja');

        $query = $tblStore->find();
        $query->select(['IDLOJA','NOME','RESPONSAVEL','TELEFONE']);

        if($this->request->getData("TXT_STORE_SEARCH_NAME")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('NOME','%'.$this->request->getData('TXT_STORE_SEARCH_NAME').'%');
            });
        }
        if($this->request->getData("TXT_STORE_SEARCH_RESPONSIBLE")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('RESPONSAVEL','%'.$this->request->getData('TXT_STORE_SEARCH_RESPONSIBLE').'%');
            });
        }
        if($this->request->getData("TXT_STORE_SEARCH_CEP")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('CEP','%'.$this->request->getData('TXT_STORE_SEARCH_CEP').'%');
            });
        }
        if($this->request->getData("TXT_STORE_SEARCH_FONE")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('TELEFONE','%'.$this->request->getData('TXT_STORE_SEARCH_FONE').'%');
            });
        }

        $this->set('store_list',$this->paginate($query,['limit' => 10]));
	}

	/**
	* Metodo que exibe a pagina de criacao de nova loja ou edicao de uma existente
	* @param int $_idLoja codigo da loja
	*
	* @return
	*/
	public function storeCreate($_idLoja=""){
		if($_idLoja!=""){
            $lojas = TableRegistry::get('SysLoja');
            $loja = $lojas->find()->where(['IDLOJA' => $_idLoja])->first();

            $cidades = TableRegistry::get('SysCidade');
            $cidade = $cidades->find()->where(['IDCIDADE' => $loja->IDCIDADE])->first();

            $this->set('loja',$loja);
            $this->set('cidade',$cidade);
        }
	}

	/**
	* Metodo que salva os dados de uma loja
	*
	* @return boolean
	*/
	public function storeSave(){
		//busca as informacoes da loja
        $tblLoja = TableRegistry::get('SysLoja');

        //evita que tente carregar o template
        $this->autoRender = false;

        if($this->request->getData("IDLOJA")!=""){
            $loja = $tblLoja->get($this->request->getData("IDLOJA"));
        }
        else{
            $loja = $tblLoja->newEntity();
        }

        $cnpj = str_replace(".","",$this->request->getData("CNPJ"));
        $cnpj = str_replace("-", "",$cnpj);
        $cnpj = str_replace("/", "",$cnpj);

        $loja->IDCIDADE                  = mb_strtoupper($this->request->getData("IDCIDADE"));
        $loja->ENDERECO_NUM              = $this->request->getData("NUM_ENDERECO");
        $loja->CEP                       = $this->request->getData("CEP");
        $loja->NOME                      = mb_strtoupper($this->request->getData("NOME"));
        $loja->ENDERECO                  = mb_strtoupper($this->request->getData("ENDERECO"));
        $loja->TELEFONE                  = $this->request->getData("TELEFONE");
        $loja->RESPONSAVEL               = ucwords($this->request->getData("RESPONSAVEL"));
        $loja->RESPONSAVEL_TEL           = $this->request->getData("TEL_RESPONSAVEL");
        $loja->BAIRRO                    = mb_strtoupper($this->request->getData("BAIRRO"));
        $loja->ENDERECO_COMPLEMENTO      = mb_strtoupper($this->request->getData("COMPLEMENTO_ENDERECO"));
        $loja->CNAE                      = $this->request->getData("CNAE");
        $loja->CNPJ                      = $cnpj;
        $loja->INSCRICAO_ESTADUAL        = $this->request->getData("INSCRICAO_ESTADUAL");
        $loja->INSCRICAO_MUNICIPAL       = $this->request->getData("INSCRICAO_MUNICIPAL");
        $loja->NOME_FANTASIA             = mb_strtoupper($this->request->getData("NOME_FANTASIA"));
        $loja->RAZAO_SOCIAL              = mb_strtoupper($this->request->getData("RAZAO_SOCIAL"));
        $loja->DESCONTO_MAXIMO_SEM_SENHA = $this->request->getData("DESCONTO_MAXIMO_SEM_SENHA");
        $loja->DESCONTO_SENHA            = md5($this->request->getData("DESCONTO_SENHA"));
        $loja->VENDE_ESTOQUE_ZERADO      = ($this->request->getData("VENDE_ESTOQUE_ZERADO")=="")?0:1;
        $loja->NFE_EMITE                 = ($this->request->getData("NFE_EMITE")=="")?0:1;
        $loja->NFE_AMBIENTE              = $this->request->getData("NFE_AMBIENTE");
        $loja->NFE_TIPO_EMISSAO          = $this->request->getData("NFE_TIPO_EMISSAO");
        $loja->NFE_TRIBUTACAO            = $this->request->getData("NFE_TRIBUTACAO");
        $loja->NFE_UF_DEST               = mb_strtoupper($this->request->getData("NFE_UF_DEST"));
        $loja->NFE_CSC                   = $this->request->getData("NFE_CSC");
        $loja->NFE_CSC_TOKEN             = $this->request->getData("NFE_CSC_TOKEN");
        $loja->NFE_CERT_PASSWORD         = $this->request->getData("NFE_CERT_PASSWORD");

        //realiza o upload do arquivo de certificado digital
        $file      = $this->request->getData("NFE_CERT_DIGITAL");
        $file_path = CONFIG.DS.$file['name'];

        if(move_uploaded_file($file['tmp_name'], $file_path)){
        	//grava o arquivo de certificado digital
			$loja->NFE_CERT_DIGITAL = $file_path;
		}else{
			//deixa em branco se nao houve upload
			$loja->NFE_CERT_DIGITAL = '';
		}

        return $this->response->withStringBody( $tblLoja->save($loja)?true:false );
	}

	/******************BANCOS***************************/
	/**
	* Metodo que exibe da pagina inicial do cadastro de bancos
	*
	* @return
	*/
	public function bank(){
		$this->set('title',"Bancos");
		$this->set('url_filter','/system/bank_filter');
        $this->set('url_data','/system/bank_data');
	}

	/**
	* Metodo que monta os filtros da busca de bancos
	*
	* @return string
	*/
	public function bankFilter(){

        $this->Filter->addFilter("Nome","TXT_BANK_SEARCH_NAME","text");
        $this->Filter->addFilter("C&oacute;digo Febraban","TXT_BANK_SEARCH_CODE","text");

        return $this->response->withStringBody( $this->Filter->mountFilters() );
	}

	/**
	* Metodo que filtra os dados dos bancos
	*
	* @return null
	*/
	public function bankData(){
		$tblBank = TableRegistry::get('SysBanco');

        $query = $tblBank->find();
        $query->select(['IDBANCO','NOME','COD_FEBRABAN']);

        if($this->request->getData("TXT_BANK_SEARCH_NAME")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('NOME','%'.$this->request->getData('TXT_BANK_SEARCH_NAME').'%');
            });
        }
        if($this->request->getData("TXT_BANK_SEARCH_CODE")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('CODFEBRABAN','%'.$this->request->getData('TXT_BANK_SEARCH_CODE').'%');
            });
        }

        $this->set('bank_list',$this->paginate($query,['limit' => 10]));
	}

	/**
	* Metodo que realiza a busca de dados de bancos e exibe a resposta em html
	* para ser adicionada ao modal de busca
	*
	* @return string
	*/
	public function bankFind(){
        //realiza a carga da tabela de trabalho
        $bancos = TableRegistry::get("SysBanco");

        //realiza a busca da infos da tabela
        $query = $bancos->find();
        $query->select(['IDBANCO','NOME','COD_FEBRABAN']);

        //monta a condicao de busca
        $_cod  = $this->request->getData('CODFEBRABAN');
        $_nome = $this->request->getData('NOME');

        if($_cod!=""){
            $query->where(['COD_FEBRABAN LIKE' => '%'.$_cod.'%']);
        }

        if($_nome!=""){
            $query->where(['NOME LIKE' => '%'.$_nome.'%']);
        }

        $html = "";
        foreach($query as $row){
            $html.= "<tr>";
            $html.= "<td><input type='hidden' id='txtBankResultCode_".$row->IDBANCO."' value='".$row->COD_FEBRABAN."'>".$row->COD_FEBRABAN."</td>";
            $html.= "<td><input type='hidden' id='txtBankResultNome_".$row->IDBANCO."' value='".$row->NOME."'>".$row->NOME."</td>";
            $html.= "<td><input type='radio' name='rdBanco[]' id='rdBanco[]' value='".$row->IDBANCO."'></td>";
        }

        return $this->response->withStringBody( $html );
    }

	/******************CIDADES***************************/

	/**
	* Metodo que monta os filtros da busca de cidades
	*
	* @return string
	*/
	public function cityFilter(){
        $this->autoRender = false;

        $this->Filter->addFilter("Nome","TXT_CITY_SEARCH_NAME","text");
        $this->Filter->addFilter("UF","TXT_CITY_SEARCH_UF","text");

        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    /**
	* Metodo que exibe a pagina inicial de cidades
	*
	* @return
	*/
    public function city(){
		$this->set('title',"Cidades");
        $this->set('url_filter','/system/city_filter');
        $this->set('url_data','/system/city_data');
    }

    /**
	* Metodo que realiza a busca dos dados das cidades
	*
	* @return null
	*/
    public function cityData(){
        $tblCity = TableRegistry::get('SysCidade');

        $query = $tblCity->find();
        $query->select(['IDCIDADE','NOME','UF','COD_IBGE']);
        if($this->request->data("TXT_CITY_SEARCH_NAME")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('NOME','%'.$this->request->data('TXT_CITY_SEARCH_NAME').'%');
            });
        }
        if($this->request->data("TXT_CITY_SEARCH_UF")!=""){
            $query->where(['UF' => $this->request->data('TXT_CITY_SEARCH_UF')]);
        }

        $this->set('city_list',$this->paginate($query,['limit' => 10]));
    }

	/**
	* Metodo que exibe a pagina de criacao ou edicao de cidade
	* @param int $_id codigo da cidade
	*
	* @return null
	*/
	public function cityCreate($_id){
		$this->set('cidade',TableRegistry::get("SysCidade")->get($_id));
	}

	/**
	* Metodo que salva as informacoes de uma cidade
	*
	* @return boolean
	*/
	public function citySave(){
		//busca as informacoes da loja
        $tblCity = TableRegistry::get('SysCidade');

        //evita que tente carregar o template
        $this->autoRender = false;

        if($this->request->getData("IDCIDADE")!=""){
            $city = $tblCity->get($this->request->getData("IDCIDADE"));
        }
        else{
            $city = $tblCity->newEntity();
        }

		$city->NOME     = $this->request->getData("NOME");
		$city->UF       = $this->request->getData("UF");
		$city->COD_IBGE = $this->request->getData("COD_IBGE");

		return $this->response->withStringBody( $tblCity->save($city)?true:false );
	}

	/**
	* Metodo que realiza a busca de cidades e retorna um html
	* para o modal de busca de cidades
	*
	* @return string
	*/
	public function cityFind(){
        //realiza a carga da tabela de trabalho
        $cidades = TableRegistry::get("SysCidade");

        //realiza a busca da infos da tabela
        $query = $cidades->find();
        $query->select(['IDCIDADE','COD_IBGE','NOME','UF']);

        //monta a condicao de busca
        $_codibge = $this->request->data('COD_IBGE');
        $_nome    = $this->request->data('NOME');
        $_uf      = $this->request->data('UF');

        if($_codibge!=""){
            $query->where(['CODIBGE LIKE' => '%'.$_codibge.'%']);
        }

        if($_nome!=""){
            $query->where(['NOME LIKE' => '%'.$_nome.'%']);
        }

        if($_uf!=""){
            $query->where(['UF' => $_uf]);
        }

        $html = "";
        foreach($query as $row){
            $html.= "<tr>";
            $html.= "<td>".$row->COD_IBGE."</td>";
            $html.= "<td><input type='hidden' id='txtCityResultNome_".$row->IDCIDADE."' value='".$row->NOME."'>".$row->NOME."</td>";
            $html.= "<td><input type='hidden' id='txtCityResultUF_".$row->IDCIDADE."' value='".$row->UF."'>".$row->UF."</td>";
            $html.= "<td><input type='radio' name='rdCidade[]' id='rdCidade[]' value='".$row->IDCIDADE."'></td>";
        }

        return $this->response->withStringBody( $html );
    }

    /**
	* Metodo que busca os dados de uma cidade atraves do id
	* @param int $_idCidade codigo da cidade
	*
	* @return json
	*/
    public function cityGetById($_idCidade){
        $this->autoRender = false;
        return $this->response->withStringBody( json_encode(TableRegistry::get('SysCidade')->get($_idCidade)) );
    }


	/******************FORNECEDORES***************************/

	/**
	* Metodo que monta os filtros da busca de fornecedores
	*
	* @return string
	*/
    public function providerFilter(){
        $this->Filter->addFilter("Ras&atilde;o Social","TXT_PROVIDER_SEARCH_NAME","text");
        $this->Filter->addFilter("Fantasia","TXT_PROVIDER_SEARCH_NICKNAME","text");
        $this->Filter->addFilter("Representante","TXT_PROVIDER_SEARCH_AGENT","text");
        $this->Filter->addFilter("Telefone","TXT_PROVIDER_SEARCH_FONE","text");


		$ords = array();

		$ord1 = new \stdClass();
		$ord1->key   = "Nome Fantasia";
		$ord1->value = "NOME_FANTASIA";
		$ords[] = $ord1;

		$ord2 = new \stdClass();
		$ord2->key   = "Telefone";
		$ord2->value = "TELEFONE";
		$ords[] = $ord2;

		$ord3 = new \stdClass();
		$ord3->key   = "Telefone 2";
		$ord3->value = "TELEFONE2";
		$ords[] = $ord3;

		$ord4 = new \stdClass();
		$ord4->key   = "Representante";
		$ord4->value = "REPRESENTANTE";
		$ords[] = $ord4;

		$this->Filter->addOrder($ords);

        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    /**
	* Metodo que exibe a pagina inicial de fornecedores
	*
	* @return null
	*/
    public function provider(){
		$this->set('title',"Fornecedores");
        $this->set('url_filter','/system/provider_filter');
        $this->set('url_data','/system/provider_data');
    }

    /**
	* Metodo que realiza a busca de fornecedores
	*
	* @return null
	*/
    public function providerData(){
        $tblProvider = TableRegistry::get('SysFornecedor');

        $query = $tblProvider->find();
        $query->select(['IDFORNECEDOR','FANTASIA','TELEFONE','TELEFONE2','REPRESENTANTE']);

        if($this->request->data("TXT_PROVIDER_SEARCH_NAME")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('RAZAO_SOCIAL','%'.$this->request->data('TXT_PROVIDER_SEARCH_NAME').'%');
            });
        }
        if($this->request->data("TXT_PROVIDER_SEARCH_NICKNAME")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('FANTASIA','%'.$this->request->data('TXT_PROVIDER_SEARCH_NICKNAME').'%');
            });
        }
        if($this->request->data("TXT_PROVIDER_SEARCH_AGENT")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('REPRESENTANTE','%'.$this->request->data('TXT_PROVIDER_SEARCH_AGENT').'%');
            });
        }
        if($this->request->data("TXT_PROVIDER_SEARCH_FONE")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('TELEFONE','%'.$this->request->data('TXT_PROVIDER_SEARCH_FONE').'%');
            })
            ->orWhere(function ($exp,$q){
                return $exp->like('TELEFONE2','%'.$this->request->data('TXT_PROVIDER_SEARCH_FONE').'%');
            });
        }

        $this->set('provider_list',$this->paginate($query,['limit' => 10]));
    }

    /**
	* Metodo que exibe a pagina para criacao ou edicao de fornecedor
	* @param int $_idFornecedor codigo do fornecedor (opcional)
	*
	* @return null
	*/
    public function providerCreate($_idFornecedor=""){
        if($_idFornecedor!=""){
            $providers = TableRegistry::get('SysFornecedor');
            $provider  = $providers->find()->where(['IDFORNECEDOR' => $_idFornecedor])->first();

            $tblCNPJ = TableRegistry::get('SysFornecedorCnpj');
            $cnpjs   = $tblCNPJ->find()->where(['IDFORNECEDOR' => $_idFornecedor]);

            $cidades = TableRegistry::get('SysCidade');
            $cidade  = $cidades->find()->where(['IDCIDADE' => $provider->IDCIDADE])->first();

            $bancos = TableRegistry::get('SysBanco');
            $banco  = $bancos->find()->where(['IDBANCO' => $provider->IDBANCO])->first();

            $this->set('fornecedor',$provider);
            $this->set('cnpjs',$cnpjs);
            $this->set('cidade',$cidade);
            $this->set('banco',$banco);
        }
    }

	/**
	* Metodo que salva as informacoes de um fornecedor
	*
	* @return boolean
	*/
    public function providerSave(){
        $retorno = false;

        $tblProvider = TableRegistry::get("SysFornecedor");
        $tblCNPJ = TableRegistry::get("SysFornecedorCnpj");
        //evita que tente carregar o template
        $this->autoRender = false;

        if($this->request->data("IDFORNECEDOR")!=""){
            $fornecedor = $tblProvider->get($this->request->data("IDFORNECEDOR"));
            $tblCNPJ->deleteAll(['IDFORNECEDOR' => $this->request->data("IDFORNECEDOR")]);
        }
        else{
            $fornecedor = $tblProvider->newEntity();
        }

        $fornecedor->RAZAO_SOCIAL    = mb_strtoupper($this->request->data("RAZAO_SOCIAL"));
        $fornecedor->FANTASIA        = mb_strtoupper($this->request->data("FANTASIA"));
        $fornecedor->CEP             = $this->request->data("CEP");
        $fornecedor->NUMERO_ENDERECO = $this->request->data("NUMERO_ENDERECO");
        $fornecedor->PRAZO_ENTREGA   = $this->request->data("PRAZO_ENTREGA");
        $fornecedor->TELEFONE        = $this->request->data("TELEFONE");
        $fornecedor->TELEFONE2       = $this->request->data("TELEFONE2");
        $fornecedor->ENDERECO        = $this->request->data("ENDERECO");
        $fornecedor->IDCIDADE        = $this->request->data("IDCIDADE");
        $fornecedor->REPRESENTANTE   = mb_strtoupper($this->request->data("REPRESENTANTE"));
        $fornecedor->IDBANCO         = $this->request->data("IDBANCO");
        $fornecedor->NUM_CONTA       = $this->request->data("NUM_CONTA");
        $fornecedor->AGENCIA         = $this->request->data("AGENCIA");
        $fornecedor->TIPO_CONTA      = $this->request->data("TIPO_CONTA");
        $fornecedor->NOME_CONTA      = $this->request->data("NOME_CONTA");
        $fornecedor->OBSERVACAO      = mb_strtoupper($this->request->data("OBSERVACAO"));

        if($tblProvider->save($fornecedor)){
            $retorno = true;

            $cnpjs = explode("\n",$this->request->data("CNPJS"));
            for($i=0;$i<count($cnpjs);$i++){
                if(trim($cnpjs[$i])!=""){

                    //limpa o cnpj para garantir que ira somente numeros
                    $strcnpj = str_replace(".","",$cnpjs[$i]);
                    $strcnpj = str_replace("/","",$strcnpj);
                    $strcnpj = str_replace("-","",$strcnpj);

                    $cnpj = $tblCNPJ->newEntity();
                    $cnpj->CNPJ = $strcnpj;
                    $cnpj->IDFORNECEDOR = $fornecedor->IDFORNECEDOR;

                    if($tblCNPJ->save($cnpj)){
                        $retorno = true;
                    }else{
                        $retorno = false;
                    }
                }
            }
        }

        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que retorna as informacoes de um fornecedor em forma json
	* @param int $_idProdutoTipo tipo de produto (opcional)
	*
	* @return json
	*/
    public function providerJson($_idProdutoTipo=""){
    	$this->autoRender = false;

		$tblFornece = TableRegistry::get('SysFornecedor');

		if($_idProdutoTipo!=""){
			$subquery = TableRegistry::get('SysProduto')->find()->select(['IDFORNECEDOR'])->distinct()->where(['IDPRODUTOTIPO' => $_idProdutoTipo]);
			$fornecedores = $tblFornece->find()->where(['IDFORNECEDOR IN' => $subquery],['IDFORNECEDOR' => 'integer[]']);
		}else{
			$fornecedores = $tblFornece->find();
		}
		//print_r($fornecedores->toArray());
		return $this->response->withStringBody( json_encode($fornecedores->toArray()) );
	}


	/*********************TEMPLATES DE E-MAIL*******************************/
    /**
     * Metodo que envia um e-mail com um template do sistema
     * @param Boolean $_is_ajax Se ajax imprime retorno
     * @return Boolean
     */
    public function sendTemplatedEmail($_is_ajax=false){
        $_cliente = $this->request->data("CLIENTE");
        $_template = $this->request->data("TEMPLATE");
        $_subject = $this->request->data("ASSUNTO");
        $_message_complement = $this->request->data("MENSAGEM");

        $template = TableRegistry::get('SysTemplateEmail')->get($_template);

        //verifica se o $_cliente eh um objeto
        if(!is_object($_cliente)){
            $_cliente = TableRegistry::get('SysCliente')->get($_cliente);
        }

        $htmltpl = $template->HTML;
        /*
         * cliente_id_md5
         * cliente_id
         * cliente_nome
         * cliente_email
         * data_ultima_compra
         * conteudo_ultima_compra
         * solicitacao
         * pedido_venda
         * codigo_cupom
         */

        $htmltpl = str_replace("{{cliente_id_md5}}", md5($_cliente->IDCLIENTE), $htmltpl);
        $htmltpl = str_replace("{{cliente_id}}", $_cliente->IDCLIENTE, $htmltpl);
        $htmltpl = str_replace("{{cliente_nome}}", $_cliente->NOME, $htmltpl);
        $htmltpl = str_replace("{{cliente_email}}", $_cliente->EMAIL, $htmltpl);


        //busca a informacao da ultima compra e substitui apenas a data
        if(strpos($htmltpl, "{{data_ultima_compra}}")!==false){
            $venda = TableRegistry::get('LojVenda')->find()->select(['DATA_VENDA'])->where(['IDCLIENTE' => $_cliente->IDCLIENTE])->order(['DATA_VENDA','DESC'])->first();
            $htmltpl = str_replace("{{data_ultima_compra}}",formatDate($venda->DATA_VENDA),$htmltpl);
        }

        //busca a informacao da ultima compra e substitui pelos produtos comprados
        /*if(strpos($htmltpl,"{{conteudo_ultima_compra}}")!==false){
            $this->load->model("retail/Venda");
            $this->Venda->get_last_by_customer($_cliente->IDCLIENTE);
            $html = "<table class='table table-striped'>";
            $html.= "<thead>";
            $html.= "<tr>";
            $html.= "<th>Produto</th>";
            $html.= "<th>Quantidade</th>";
            $html.= "</tr>";
            $html.= "</thead>";
            $html.= "<tbody>";
            foreach($this->Venda->ITENS as $item){
                $html.= "<tr>";
                $html.= "<td>".$item->NOME_PRODUTO."</td>";
                $html.= "<td>".$item->QUANTIDADE."</td>";
                $html.= "</tr>";
            }
            $html.= "</tbody>";
            $html.= "</table>";

            $htmltpl = str_replace("{{conteudo_ultima_compra}}",$html,$htmltpl);
        }*/

        //busca a informacao da solicitacao e substitui pelo conteudo
        if(strpos($htmltpl,"{{solicitacao}}")!==false){
            $htmltpl = str_replace("{{solicitacao}}",$_message_complement,$htmltpl);
        }

        //busca a informacao de pedido de venda
        if(strpos($htmltpl,"{{pedido_venda}}")!==false){
            $htmltpl = str_replace("{{pedido_venda}}",$_message_complement,$htmltpl);
        }

        //busca a informacao do cupom e substitui
        if(strpos($htmltpl,"{{codigo_cupom}}")!==false){
            $htmltpl = str_replace("{{codigo_cupom}}",$_message_complement,$htmltpl);
        }

        $message = $htmltpl;

        $tblOption = TableRegistry::get('SysOpcao');

        $mail = new Email('default');

        if($_is_ajax){
            $this->autoRender = false;
            return $this->response->withStringBody( $mail->from(
                $tblOption->get('DEFAULT_SYSTEM_MAIL')->OPCAO_VALOR,
                $tblOption->get('NAME_DEFAULT_MAIL')->OPCAO_VALOR
            )->to(
                $_cliente->EMAIL,
                $_cliente->NOME
            )->subject($_subject)
            ->emailFormat("html")
            ->send($message)?true:false );
        }else{
            return $mail->from(
                $tblOption->get('DEFAULT_SYSTEM_MAIL')->OPCAO_VALOR,
                $tblOption->get('NAME_DEFAULT_MAIL')->OPCAO_VALOR
            )->to(
                $_cliente->EMAIL,
                $_cliente->NOME
            )->subject($_subject)
            ->emailFormat("html")
            ->send($message)?true:false;
        }
    }

    /**
     * Metodo para envio de e-mail
     * @param string $_mail_to E-mail de destino
     * @param string $_subject Assunto do e-mail
     * @param string $_message Mensagem do e-mail
     * @return boolean or void
     */
    public function send_email($_mail_to,$_subject,$_message,$_is_ajax=false){
        $this->load->model("system/Opcao");

        $this->load->library("email");

        $this->email->from($this->Opcao->get_option("DEFAULT_SYSTEM_MAIL"), $this->Opcao->get_option("NAME_DEFAULT_MAIL"));
        $this->email->to($_mail_to);

        $this->email->subject($_subject);
        $this->email->message($_message);

        if(!$_is_ajax){
            return $this->email->send();
        }else{
            return $this->response->withStringBody( $this->email->send() );
        }
    }

    /**
	* Metodo que exibe os filtros da busca de templates
	*
	* @return string
	*/
    public function templateFilter(){
        $this->Filter->addFilter("Nome","TXT_TEMPLATE_SEARCH_NAME","text");
        $this->Filter->addFilter("Assunto","TXT_TEMPLATE_SEARCH_SUBJECT","text");

		$ords = array();

		$ordn = new \stdClass();
		$ordn->key   = "Nome";
		$ordn->value = "NOME";
		$ords[] = $ordn;

		$ordl = new \stdClass();
		$ordl->key   = "Assunto";
		$ordl->value = "ASSUNTO";
		$ords[] = $ordl;

		$this->Filter->addOrder($ords);

        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    /**
	* Metodo que exibe a pagina inicial dos templates
	*
	* @return null
	*/
    public function template(){
		$this->set('title',"Modelos de E-mail");
        $this->set('url_filter','/system/template_filter');
        $this->set('url_data','/system/template_data');
    }

    /**
	* Metodo que busca os dados de templates
	*
	* @return null
	*/
    public function templateData(){
        $tblTemplate = TableRegistry::get('SysTemplateEmail');

        $query = $tblTemplate->find();
        $query->select(['IDTEMPLATEEMAIL','NOME','ASSUNTO']);
        if($this->request->data("TXT_TEMPLATE_SEARCH_NAME")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('NOME','%'.$this->request->data('TXT_TEMPLATE_SEARCH_NAME').'%');
            });
        }
        if($this->request->data("TXT_TEMPLATE_SEARCH_SUBJECT")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('ASSUNTO','%'.$this->request->data('TXT_TEMPLATE_SEARCH_SUBJECT')."%");
            });
        }

        $this->Paginator->paginate($query,['limit' => 10]);

        $this->set('template_list',$this->paginate($query));
    }

	/**
	* Metodo que cria ou edita um templa
	* @param int $_id codigo do template
	*
	* @return null
	*/
    public function templateCreate($_id=""){
        if($_id!=""){
            $this->set('template',TableRegistry::get('SysTemplateEmail')->get($_id));
        }
    }

	/**
	* Metodo que salva os dados de um template
	*
	* @return boolean
	*/
    public function templateSave(){
        //busca as informacoes da loja
        $tblTemplate = TableRegistry::get('SysTemplateEmail');

        //evita que tente carregar o template
        $this->autoRender = false;

        if($this->request->data("IDTEMPLATEEMAIL")!=""){
            $template = $tblTemplate->get($this->request->data("IDTEMPLATEEMAIL"));
        }
        else{
            $template = $tblTemplate->newEntity();
        }

        $template->IDTEMPLATEEMAIL = $this->request->data("IDTEMPLATEEMAIL");
        $template->NOME            = mb_strtoupper($this->request->data("NOME"));
        $template->ASSUNTO         = $this->request->data("ASSUNTO");
        $template->HTML            = $this->request->data("HTML");

        return $this->response->withStringBody( $tblTemplate->save($template)?true:false );
    }

    /**
	* Metodo que exibe o resultados das buscas feitas por um usuario administrador
	*
	* @return null
	*/
    public function searchResultAdmin(){
		//realiza busca nos produtos
		$tblProd = TableRegistry::get("SysProduto")->find()->select(['IDPRODUTO','NOME','NOME_TAG','ESTRUTURA'])
		->where( array("MATCH(NOME, NOME_TAG) AGAINST('+".str_replace(' '," +",$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca nas contas a pagar
		$tblConta = TableRegistry::get('LojContasPagar')->find()->select(['IDCONTASPAGAR','OBSERVACAO'])
		->where( array("MATCH(OBSERVACAO) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca no cliente
		$tblCli = TableRegistry::get('SysCliente')->find()->select(['IDCLIENTE','NOME'])
		->where( array("MATCH(NOME) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca na avalidacao do cliente
		$tblAval = TableRegistry::get('SysClienteAvaliacaoVenda')->find()->select(['IDCLIENTE','IDVENDA','SUGESTAO'])
		->where( array("MATCH(SUGESTAO) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca no extrato bancario
		$tblExtrato = TableRegistry::get('SysExtratoBancario')->find()->select(['IDEXTRATOBANCARIO','HISTORICO'])
		->where( array("MATCH(HISTORICO) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca nos funcionarios
		$tblFunc = TableRegistry::get('SysFuncionario')->find()->select(['IDFUNCIONARIO','NOME','ENDERECO','APELIDO','BAIRRO','RECADOS'])
		->where( array("MATCH(NOME,ENDERECO,APELIDO,BAIRRO,RECADOS) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca nos fornecedores
		$tblForn = TableRegistry::get('SysFornecedor')->find()->select(['IDFORNECEDOR','RAZAO_SOCIAL','FANTASIA','ENDERECO','REPRESENTANTE','OBSERVACAO'])
		->where( array("MATCH(RAZAO_SOCIAL,FANTASIA,ENDERECO,REPRESENTANTE,OBSERVACAO) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca nas cidades
		$tblCity = TableRegistry::get('SysCidade')->find()->select(['IDCIDADE','NOME'])
		->where( array("MATCH(NOME) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca nos bancos
		$tblBank = TableRegistry::get('SysBanco')->find()->select(['IDBANCO','NOME'])
		->where( array("MATCH(NOME) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca nas lojas
		$tblStore = TableRegistry::get('SysLoja')->find()->select(['IDLOJA','NOME','ENDERECO','RAZAO_SOCIAL','NOME_FANTASIA'])
		->where( array("MATCH(NOME,ENDERECO,RAZAO_SOCIAL,NOME_FANTASIA,RESPONSAVEL) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca nos templates de e-mail
		$tblTpl = TableRegistry::get('SysTemplateEmail')->find()->select(['IDTEMPLATEEMAIL','NOME','ASSUNTO','HTML'])
		->where( array("MATCH(NOME,ASSUNTO,HTML) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca nas categorias
		$tblCat = TableRegistry::get('SysCategoria')->find()->select(['IDCATEGORIA','NOME'])
		->where( array("MATCH(NOME) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca nas regras de SKU
		$tblRule = TableRegistry::get('SysRegraSku')->find()->select(['IDREGRASKU','IDPRODUTOTIPO','REGRA'])
		->where( array("MATCH(REGRA) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca nos albuns
		$tblGall = TableRegistry::get('SysGaleriaAlbum')->find()->select(['IDALBUM','NOME'])
		->where( array("MATCH(NOME) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca nas imagens
		$tblImg = TableRegistry::get('SysGaleriaImagem')->find()->select(['IDIMAGEM','IDALBUM','NOME'])
		->where( array("MATCH(NOME) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		$this->set("product_list",$tblProd);
		$this->set("contas_pagar_list",$tblConta);
		$this->set("cliente_list",$tblCli);
		$this->set("avaliacao_venda_list",$tblAval);
		$this->set("extrato_list",$tblExtrato);
		$this->set("funcionario_list",$tblFunc);
		$this->set("fornecedor_list",$tblForn);
		$this->set("cidade_list",$tblCity);
		$this->set("banco_list",$tblBank);
		$this->set("loja_list",$tblStore);
		$this->set("template_list",$tblTpl);
		$this->set("categoria_list",$tblCat);
		$this->set("regra_list",$tblRule);
		$this->set("album_list",$tblGall);
		$this->set("imagem_list",$tblImg);

		$this->set('search_keyword',$this->request->getData("search_keyword"));
	}

	/**
	* Metodo que exibe o resultados das buscas feitas por um usuario gerente
	*
	* @return null
	*/
	public function searchResultManager(){
		$user = $this->Auth->user();
		//realiza busca nos produtos
		$tblProd = TableRegistry::get("SysProduto")->find()->select(['IDPRODUTO','NOME','NOME_TAG','ESTRUTURA'])
		->where( array("MATCH(NOME, NOME_TAG) AGAINST('+".str_replace(' '," +",$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca nas contas a pagar
		$tblConta = TableRegistry::get('LojContasPagar')->find()->select(['IDCONTASPAGAR','OBSERVACAO'])
		->where( array("MATCH(OBSERVACAO) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") )
		->where(['IDLOJA' => $user['storeid']]);

		//realiza busca no cliente
		$tblCli = TableRegistry::get('SysCliente')->find()->select(['IDCLIENTE','NOME'])
		->where( array("MATCH(NOME) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca na avalidacao do cliente
		$tblAval = TableRegistry::get('SysClienteAvaliacaoVenda')->find()->select(['IDCLIENTE','IDVENDA','SUGESTAO'])
		->where( array("MATCH(SUGESTAO) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") )
		->where(function($exp) use($user){
			return $exp->in('IDVENDA',TableRegistry::get('LojVenda')->find()->select(['IDVENDA'])->where(['IDLOJA' => $user['storeid']]));
		});

		//realiza busca nos funcionarios
		$tblFunc = TableRegistry::get('SysFuncionario')->find()->select(['IDFUNCIONARIO','NOME','ENDERECO','APELIDO','BAIRRO','RECADOS'])
		->where( array("MATCH(NOME,ENDERECO,APELIDO,BAIRRO,RECADOS) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") )
		->where(function($exp) use($user){
			return $exp->in('IDUSUARIO',TableRegistry::get('SysUsers')->find()->select(['id'])->where(['storeid' => $user['storeid']]));
		});

		//realiza busca nos albuns
		$tblGall = TableRegistry::get('SysGaleriaAlbum')->find()->select(['IDALBUM','NOME'])
		->where( array("MATCH(NOME,TAGS) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		//realiza busca nas imagens
		$tblImg = TableRegistry::get('SysGaleriaAlbum')->find()->select(['IDIMAGEM','IDALBUM','NOME'])
		->where( array("MATCH(NOME,ARQUIVO) AGAINST('+".str_replace(' ',' +',$this->request->getData("search_keyword"))."' IN BOOLEAN MODE)") );

		$this->set("product_list",$tblProd);
		$this->set("contas_pagar_list",$tblConta);
		$this->set("cliente_list",$tblCli);
		$this->set("avaliacao_venda_list",$tblAval);
		$this->set("funcionario_list",$tblFunc);

		$this->set('search_keyword',$this->request->getData("search_keyword"));
	}

	function updateCashFlow($lastMonth=null){
		ini_set('memory_limit', '512M');
        set_time_limit(0);

        $response = null;

        $operacao = (($lastMonth==NULL)?"atualiza&ccedil;&atilde;o":"reconstru&ccedil;&atilde;o");

        $tblFluxo  = TableRegistry::get('LojFluxoCaixa');
        $tblOption = TableRegistry::get('SysOpcao');
        $tblCap    = TableRegistry::get('LojContasPagar');
        /*
        $response = "Inciando atualiza&ccedil;&atilde;o do fluxo de caixa em ".date("d/m/Y H:i:s")."<br/>";

        if($lastMonth==null){
            //se nao houver data inicial estipula o primeiro dia do mes anterior
            $lastMonth = date('Y-m-01', strtotime('-1 months', strtotime(date('Y-m-d'))));
        }
        $lastDayActualMonth = date("Y-m-t");

        //limpa as informacoes migradas, de saldo inicial e importadas desde a data inicial
        $tblFluxo->query()->delete()
            ->where(function($exp,$q) use($lastMonth){
                return $exp->gte('DATA_MOVIMENTO',$lastMonth);
            })
            ->where(function($exp,$q){
                return $exp->in('TIPO_INFORMACAO',['M','S','E']);
            });

        /********* INICIO DA MIGRACAO DAS VENDAS E DESPESAS DE CARTAO *************/

	/*
        //busca as vendas realizadas entre o comeco e o fim do periodo de atualizacao do fluxo
        //normalmente mes anterior e fim do mes atual
        $vendas = TableRegistry::get('LojVenda')->find()
        	->select(['IDVENDA','IDLOJA','DATA_VENDA','SUBTOTAL','DESCONTO','TROCO'])
            ->where(function($exp,$q) use($lastMonth){
                return $exp->gte('DATE(DATA_VENDA)',$lastMonth);
            })
            ->where(function($exp,$q) use($lastDayActualMonth){
                return $exp->lte('DATE(DATA_VENDA)',$lastDayActualMonth);
            });

        foreach($vendas as $venda){
            //varre as informacoes de pagamento da venda para salvar o valor das parcelas
            $pag_venda = TableRegistry::get('LojVendaPagamento')->find()
            	->select(['IDCONDICAOPAGAMENTO','VALOR'])
            	->where(['IDVENDA' => $venda->IDVENDA]);

            //verifica o numero de pagamentos atrelados a venda
            if($pag_venda->count()>0){

            	//para cada pagamento realiza o procedimento
                foreach($pag_venda as $pagamento){
                    //busca as informacoes da condicao de pagamento para formatar as aparcelas
                    $cond_pag = TableRegistry::get('SysCondicaoPagamento')->get($pagamento->IDCONDICAOPAGAMENTO);
                    //busca o codigo do meio de pagamento
                    $idmeiopagamento = TableRegistry::get('SysMeioCondicao')->find()->select(['IDMEIOPAGAMENTO'])->where(['IDCONDICAOPAGAMENTO' => $pagamento->IDCONDICAOPAGAMENTO])->first()->IDMEIOPAGAMENTO;
                    //determina o valor de cada parcela
                    $valor_parcela = 0;
					//se houver dias de recebimento
					if($cond_pag->DIAS_RECEBIMENTO>0){
						//se for menor que um mes apenas adiciona o numero de dias
						if($cond_pag->DIAS_RECEBIMENTO<30){
							$data_parcela = new Date($venda->DATA_VENDA->format("Y-m-d"));
							$data_parcela->modify("+ ".$cond_pag->DIAS_RECEBIMENTO." ".($cond_pag->DIAS_RECEBIMENTO==1?" day":" days"));
						}else{
							//adiciona um mes e mais a diferenca em dias
							$data_parcela = new Date($venda->DATA_VENDA->format("Y-m-d"));
							$data_parcela->modify("+ 1 month");
							$modify = "+ ".($cond_pag->DIAS_RECEBIMENTO-30)." ".($cond_pag->DIAS_RECEBIMENTO==1?" day":" days");
							$data_parcela->modify($modify);
						}
					}else{
						$data_parcela = $venda->DATA_VENDA;
					}

					//busca o codigo da operacao financeira
                    $operacao_financeira = TableRegistry::get('SysOperacaoEntrada')->find()
                        ->select(['IDOPERACAOFINANCEIRA'])
                        ->where(['IDMEIOPAGAMENTO' => $idmeiopagamento])
                        ->first();

                    if($operacao_financeira){
                    	//calcula o valor da parcela dividindo pelo numero de parcelas caso sejam maiores que zero
                        $valor_parcela = ($pagamento->VALOR - $venda->TROCO)/(($cond_pag->PARCELAS==0)?1:$cond_pag->PARCELAS);

						//para cada uma das parcelas existentes
                        for($i=0;$i<$cond_pag->PARCELAS;$i++){

                        	//cria uma nova entrada no fluxo de caixa
                            $fluxo_caixa = $tblFluxo->newEntity();
                            //monta a estrutura de inclusao do fluxo de caixa para as entradas
                            $fluxo_caixa->IDLOJA               = $venda->IDLOJA;
                            $fluxo_caixa->DATA_MOVIMENTO       = $venda->DATA_VENDA;
                            $fluxo_caixa->TIPO_INFORMACAO      = 'M';
                            $fluxo_caixa->IDOPERACAOFINANCEIRA = $operacao_financeira->IDOPERACAOFINANCEIRA;
                            $fluxo_caixa->DATA_ENTRADA         = $data_parcela;
                            $fluxo_caixa->VALOR                = $valor_parcela;
                            $fluxo_caixa->HISTORICO            = "MIGRACAO FLUXO DE CAIXA (VENDA ".$venda->IDVENDA." no valor de " . Number::currency($venda->SUBTOTAL-$venda->DESCONTO,"BRL") . "".(($cond_pag->PARCELAS>1)?" parcela ".($i+1)." de ".$cond_pag->PARCELAS:"").")";

                            //salva as informacoes de entrada no fluxo de caixa
                            $tblFluxo->save($fluxo_caixa);

                            //aproveita para salvar a deducao de vendas
                            if((float)$cond_pag->TAXA_ADM > 0){
                                $perc_taxa = $cond_pag->TAXA_ADM/100;

                                $fluxo_caixa = $tblFluxo->newEntity();
                                $fluxo_caixa->IDLOJA          = $venda->IDLOJA;
                                $fluxo_caixa->DATA_ENTRADA    = $data_parcela;
								$fluxo_caixa->DATA_MOVIMENTO  = $data_parcela;
								$fluxo_caixa->TIPO_INFORMACAO = 'M';
                                $fluxo_caixa->VALOR           = $valor_parcela*$perc_taxa;
                                $fluxo_caixa->HISTORICO       = "MIGRACAO FLUXO DE CAIXA (TAXA SOBRE A VENDA ".$venda->IDVENDA." no valor de " . Number::currency($valor_parcela*$perc_taxa,"BRL") . " da parcela ".($i+1).")";
                                $fluxo_caixa->IDOPERACAOFINANCEIRA =(int)$tblOption->get("DEFAULT_FINANC_CARD")->OPCAO_VALOR;
                                $tblFluxo->save($fluxo_caixa);
                            }

                            //incrementa a data da parcela para tratar a proxima parcela
							if($cond_pag->DIAS_RECEBIMENTO>0){
								//se for mais que um mes
								if($cond_pag->DIAS_RECEBIMENTO<30){
									$data_parcela->modify("+ ".$cond_pag->DIAS_RECEBIMENTO." ".($cond_pag->DIAS_RECEBIMENTO==1?" day":" days"));
								}else{
									$data_parcela->modify("+ 1 month");
									$modify = "+ ".($cond_pag->DIAS_RECEBIMENTO-30)." ".($cond_pag->DIAS_RECEBIMENTO==1?" day":" days");
									$data_parcela->modify($modify);
								}
							}else{
								$data_parcela = $venda->DATA_VENDA;
							}
                        }//end for($i=0;$i<$cond_pag->PARCELAS;$i++)
                    }//end if($operacao_financeira)
                }//en foreach($pag_venda as $pagamento)
            }//end if $pag_venda->count()
        }// end foreach($vendas as $venda)
        $response .= "Fim da importa&ccedil;&atilde;o de vendas e inicio da importa&ccedil;&atilde;o de despesa do fluxo de caixa em ".date("d/m/Y H:i:s")."<br/>";*/

        /********* FIM DA MIGRACAO DAS VENDAS E DESPESAS DE CARTAO *************/

        /************** INICIO DA MIGRACAO DAS DESPESAS *********************/
        $contas = $tblCap->find()
            ->select([
            	'IDLOJA',
            	'IDCONTASPAGAR',
            	'DATA_VENCIMENTO',
            	'DATA_PAGAMENTO',
            	'IDTIPODESPESA',
            	'VALOR_PAGO',
            	'VALOR_ORIGINAL',
            	'DIFERENCA_PAGAMENTO',
            	'IDTIPODESPESA_PAGAMENTO',
            	'OBSERVACAO'])
            ->where(function($exp,$q) use($lastMonth){
                return $exp->gte('DATE(DATA_VENCIMENTO)',$lastMonth);
            });

        foreach($contas as $conta){
            //calcula a diferenca entre o vencimento e pagamento
            $data_vencimento = new \DateTime($conta->DATA_VENCIMENTO->format("Y-m-d"));
            $data_vencimento->setTime(0,0,0);
            if($conta->DATA_PAGAMENTO!=NULL){
                $data_pagamento = new \DateTime($conta->DATA_PAGAMENTO->format("Y-m-d"));
            }else{
                $data_pagamento = new \DateTime($conta->DATA_VENCIMENTO->format("Y-m-d"));
            }
            //zera a hora da data de pagamento
            $data_pagamento->setTime(0,0,0);

			//obtem a diferenca entre a data de pagamento e data de vencimento
            $diff = $data_vencimento->diff($data_pagamento);
            $totalDiff = $diff->format("%R%a"); //retorna o numero de dias com o sinal

			//busca a operacao financeira do fluxo de caixa
            $operacao_financeira = TableRegistry::get('SysOperacaoSaida')->find()->where(['IDTIPODESPESA' => $conta->IDTIPODESPESA]);

            //se houver alguma operacao financeira a conta eh classificada
            if($operacao_financeira){
                //se houver diferenca eh porque foi pago com atraso,
                //entao precisa registrar a saida do dinheiro total no dia do atraso
                if($totalDiff > 1 && ($conta->VALOR_PAGO-$conta->VALOR_ORIGINAL)>0){
                	//cria uma entidade para salvar no fluxo de caixa
                    $fluxo_caixa = $tblFluxo->newEntity();
                    $fluxo_caixa->IDLOJA          = $conta->IDLOJA;
                    $fluxo_caixa->DATA_MOVIMENTO  = $conta->DATA_VENCIMENTO;
                    $fluxo_caixa->DATA_ENTRADA    = ($conta->DATA_PAGAMENTO!=NULL)?$conta->DATA_PAGAMENTO:$conta->DATA_VENCIMENTO;
                    $fluxo_caixa->TIPO_INFORMACAO = 'M';
                    $fluxo_caixa->VALOR           = $conta->VALOR_ORIGINAL;
                    $fluxo_caixa->HISTORICO       = "MIGRACAO FLUXO DE CAIXA (CONTAS A PAGAR referente a '".$conta->OBSERVACAO."' - ID-1: {$conta->IDCONTASPAGAR}".(($conta->NUM_DOCUMENTO!="")?" DOC: ".$conta->NUM_DOCUMENTO:"").")";
                    $fluxo_caixa->IDOPERACAOFINANCEIRA = $operacao_financeira->IDOPERACAOFINANCEIRA;
                    $tblFluxo->save($fluxo_caixa);

                    //para o excedente serah utilizada a data de pagamento
                    //como data de entrada e data do movimento no fluxo de caixa
                    //essa rotina salva as informacoes referentes a juros ou multa por exemplo
                    $fluxo_caixa = $tblFluxo->newEntity();
                    $fluxo_caixa->IDLOJA          = $conta->IDLOJA;
                    $fluxo_caixa->DATA_ENTRADA    = $conta->DATA_PAGAMENTO;
                    $fluxo_caixa->DATA_MOVIMENTO  = $conta->DATA_PAGAMENTO;
                    $fluxo_caixa->TIPO_INFORMACAO = 'M';
                    $fluxo_caixa->VALOR           = $conta->DIFERENCA_PAGAMENTO;
                    $fluxo_caixa->HISTORICO       = "MIGRACAO FLUXO DE CAIXA (CONTAS A PAGAR referente a '".$conta->OBSERVACAO."' - ID-1: {$conta->IDCONTASPAGAR}".(($conta->NUM_DOCUMENTO!="")?" DOC: ".$conta->NUM_DOCUMENTO:"").")";
                    $fluxo_caixa->IDOPERACAOFINANCEIRA = TableRegistry::get('SysOperacaoSaida')->find()->where(['IDTIPODESPESA' => $conta->IDTIPODESPESA_PAGAMENTO])->IDOPERACAOFINANCEIRA;
                    $tblFluxo->save($fluxo_caixa);
                }
                else{
                    $fluxo_caixa = $tblFluxo->newEntity();
                    $fluxo_caixa->IDLOJA          = $conta->IDLOJA;
                    $fluxo_caixa->DATA_ENTRADA    = $conta->DATA_VENCIMENTO;
                    $fluxo_caixa->DATA_MOVIMENTO  = ($conta->DATA_PAGAMENTO!=NULL)?$conta->DATA_PAGAMENTO:$conta->DATA_VENCIMENTO;
                    $fluxo_caixa->TIPO_INFORMACAO = 'M';
                    $fluxo_caixa->VALOR           = $conta->VALOR_ORIGINAL;
                    $fluxo_caixa->HISTORICO       = "MIGRACAO FLUXO DE CAIXA (CONTAS A PAGAR SEM ATRASO referente a '".$conta->OBSERVACAO."' - ID: {$conta->IDCONTASPAGAR}".(($conta->NUM_DOCUMENTO!="")?" DOC: ".$conta->NUM_DOCUMENTO:"").")";
                    $fluxo_caixa->IDOPERACAOFINANCEIRA = (int)$operacao_financeira->IDOPERACAOFINANCEIRA;

                    $tblFluxo->save($fluxo_caixa);
                }
            }
        }
        $response.= "Fim da importa&ccedil;o de despesas e inicio da importa&ccedil;&atilde;o de extratos banc&aacute;rios do fluxo de caixa em ".date("d/m/Y H:i:s")."<br/>";

        /************** FIM DA MIGRACAO DAS DESPESAS *********************/

        /************** INICIO DA MIGRACAO DE EXTRATOS BANCARIOS *********************/
        $extratos = TableRegistry::get('SysExtratoBancario')->find()
			->select(['NUM_DOCUMENTO','HISTORICO','IDTIPODESPESA','DATA_MOVIMENTO','VALOR'])
            ->where(function($exp,$q) use($lastMonth){
                return $exp->gte('DATE(DATA_MOVIMENTO)',$lastMonth);
            })
            ->where(function($exp,$q) use($lastDayActualMonth){
                return $exp->lte('DATE(DATA_MOVIMENTO)',$lastDayActualMonth);
            })->toArray();
        foreach($extratos as $extrato){

            $fluxo_caixa = $tblFluxo->newEntity();
            $fluxo_caixa->IDLOJA          = $tblOption->get("DEFAULT_STORE")->OPCAO_VALOR;
            $fluxo_caixa->DATA_MOVIMENTO  = $extrato->DATA_MOVIMENTO;
            $fluxo_caixa->DATA_ENTRADA    = $extrato->DATA_MOVIMENTO;
            $fluxo_caixa->TIPO_INFORMACAO = 'E';
            $fluxo_caixa->VALOR           = $extrato->VALOR;
            $fluxo_caixa->HISTORICO       = "MIGRACAO FLUXO DE CAIXA (EXTRATO '".$extrato->NUM_DOCUMENTO."' - ".$extrato->HISTORICO.")";
            $fluxo_caixa->IDOPERACAOFINANCEIRA = $this->getFinancialOutput($extrato->IDTIPODESPESA);
            $tblFluxo->save($fluxo_caixa);
        }
        $response .= "Fim da importa&ccedil;&atilde;o de extratos banc&aacute;rios e inicio da composi&ccedil;&atilde;o de saldo em ".date("d/m/Y H:i:s")."<br/>";

        /************** FIM DA MIGRACAO DE EXTRATOS BANCARIOS *********************/

        /************** INICIO DA COMPOSICAO DE SALDO *********************/
        $monthBeforeLastMonth = date('Y-m-d', strtotime('-1 months', strtotime($lastMonth)));

        $sql = "SELECT DISTINCT MONTH(DATE_ADD(DATA_ENTRADA,INTERVAL 1 MONTH)) AS MES, ";
        $sql.= "YEAR(DATE_ADD(DATA_ENTRADA,INTERVAL 1 MONTH)) AS ANO FROM loj_fluxo_caixa";
        $sql.= " WHERE DATE(DATA_ENTRADA)>=$monthBeforeLastMonth";
        $connection = ConnectionManager::get('default');
        $periodos = $connection->execute($sql)->fetchAll('assoc');

        for($i=0;$i<count($periodos);$i++){
            $data_periodo = $periodos[$i]["ANO"]."-".$periodos[$i]["MES"]."-01";

            $sql = "SELECT ((SELECT COALESCE(SUM(VALOR),0) FROM loj_fluxo_caixa FC ";
            $sql.= "INNER JOIN sys_operacao_financeira OPF ON OPF.IDOPERACAOFINANCEIRA=FC.IDOPERACAOFINANCEIRA WHERE DATA_ENTRADA < '$data_periodo' AND OPF.TIPO_OPERACAO='E')) - ";
            $sql.= "((SELECT COALESCE(SUM(VALOR),0) FROM loj_fluxo_caixa FC ";
            $sql.= "INNER JOIN sys_operacao_financeira OPF ON OPF.IDOPERACAOFINANCEIRA=FC.IDOPERACAOFINANCEIRA WHERE DATA_ENTRADA < '$data_periodo' AND OPF.TIPO_OPERACAO='S')) ";
            $sql.= "AS VALOR from loj_fluxo_caixa LIMIT 1,1";
            $connection = ConnectionManager::get('default');
            $results = $connection->execute($sql)->fetchAll('assoc');
            $saldo = $results[0]['VALOR'];

            $fluxo_caixa = $tblFluxo->newEntity();
            $fluxo_caixa->IDLOJA          = $tblOption->get("DEFAULT_STORE")->OPCAO_VALOR;
            $fluxo_caixa->DATA_ENTRADA    = $data_periodo;
            $fluxo_caixa->DATA_MOVIMENTO  = $data_periodo;
            $fluxo_caixa->TIPO_INFORMACAO = 'S';
            $fluxo_caixa->VALOR           = $saldo;
            $fluxo_caixa->HISTORICO       = "MIGRACAO FLUXO CAIXA - COMPOSICAO SALDO INICIAL";
            $fluxo_caixa->IDOPERACAOFINANCEIRA = $tblOption->get("DEFAULT_FINANC_TRANS")->OPCAO_VALOR;
            $tblFluxo->save($fluxo_caixa);
        }
        $response .= "Fim da composi&ccedil;&atilde;o de saldo inicial e fim da ".$operacao." do fluxo de caixa em ".date("d/m/Y H:i:s");

        return $this->response->withStringBody( $response );
	}

	/**
	* Metodo que verifica a situacao das configuracoes basicas para que o sistema seja executado
	*
	* @return null
	*/
	public function checkConfig(){

	}
}
