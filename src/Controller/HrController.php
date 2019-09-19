<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class HrController extends AppController{
	//put your code here
    
    public function initialize() {
        parent::initialize();
        $this->loadComponent("Paginator");
        $this->loadComponent("Filter");
        ob_start("ob_gzhandler");
    }
    
    /**************************** FUNCIONARIOS ********************************/
    /**
	* Metodo que monta os filtros da busca de funcionarios
	* 
	* @return null
	*/
    public function employerFilter(){

        $this->Filter->addFilter("Nome","TXT_EMPLOYER_SEARCH_NAME","text");
        $this->Filter->addFilter("Apelido","TXT_EMPLOYER_SEARCH_NICKNAME","text");
        
        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }
    
    /**
	* Metodo que exibe a pagina inicial dos funcionarios
	* 
	* @return null
	*/
    public function employer(){
        $this->set('url_filter','/hr/employer_filter');
        $this->set('url_data','/hr/employer_data');
        $this->set('title',"Colaboradores");
    }
    
    /**
	* Metodo que lista os dados dos funcionarios
	* 
	* @return null
	*/
    public function employerData(){
        $table = TableRegistry::get('SysFuncionario');
        
        $query = $table->find();
        $query->select(['IDFUNCIONARIO','NOME','TELEFONE','DATA_CADASTRO','APELIDO']);
        
        $user = $this->Auth->user();
        
        //se o funcionario nao for um administrador
        if($user!="admin"){
        	//filtra apenas os funcionarios pertecentes a loja
			$query->join([
				'alias' => 'USR',
				'table' => 'sys_users',
				'type'  => 'inner',
				'conditions' => 'USR.id=SysFuncionario.IDUSUARIO'
			]);
			$query->where(['USR.storeid' => $user['storeid']]);
		}
        
        if($this->request->getData("TXT_EMPLOYER_SEARCH_NAME")!=""){
            $query->where(function ($exp){
                return $exp->like('NOME','%'.$this->request->getData('TXT_EMPLOYER_SEARCH_NAME').'%');
            });
        }
        
        if($this->request->getData("TXT_EMPLOYER_SEARCH_NICKNAME")!=""){
			$query->where(function($exp){
				return $exp->like('NOME','%'.$this->requiest->getData('TXT_EMPLOYER_SEARCH_NICKNAME').'%');
			});
		}
        
        $this->set('data_list',$this->paginate($query,['limit' => 10]));
    }
    
    /**
	* Metodo que exibe a tela para cadastro ou edicao de um funcionario
	* @param int $_idFuncionario Codigo do funcionario (opcional)
	* 
	* @return null
	*/
    public function employerCreate($_idFuncionario=""){
        $this->set('benefits',TableRegistry::get('SysBeneficioTipo')->find());
        $this->set('offices',TableRegistry::get('SysCargo')->find());
        $this->set('journeylist',TableRegistry::get('SysJornadaTrabalho')->find());
        
        if($_idFuncionario!=""){
            $this->set('employer',TableRegistry::get('SysFuncionario')->get($_idFuncionario));
            $this->set('employer_benefits',TableRegistry::get('SysFuncionarioBeneficioTipo')->find()->where(['IDFUNCIONARIO' => $_idFuncionario]));
        }
    }
    
    /**
	* Metodo que salva os dados de um funcionario no banco de dados
	* 
	* @return boolean
	*/
    public function employerSave(){    	
        $retorno = false;
        
        $table = TableRegistry::get('SysFuncionario');
        
        if($this->request->getData("IDFUNCIONARIO")!=""){
            $funcionario = $table->get($this->request->getData("IDFUNCIONARIO"));
        }else{
            $funcionario = $table->newEntity();
        }
        
        $newcpf = $this->request->getData("CPF");
        $newcpf = str_replace(".", "", $newcpf);
        $newcpf = str_replace("-", "", $newcpf);
        
        $time = new Time();
        
        $funcionario->NOME           = mb_strtoupper($this->request->getData("NOME"));
        $funcionario->APELIDO        = mb_strtoupper($this->request->getData("APELIDO"));
        $funcionario->EMAIL          = strtolower($this->request->getData("EMAIL"));
        $funcionario->NASCIMENTO     = $time->parseDate($this->request->getData("NASCIMENTO"))->i18nFormat("yyyy-MM-dd");
        $funcionario->RG             = $this->request->getData("RG");
        $funcionario->CPF            = $newcpf;
        $funcionario->DATA_CADASTRO  = $time->parseDate($this->request->getData("DATA_CADASTRO"))->i18nFormat("yyyy-MM-dd");
        
        $funcionario->IDCARGO        = $this->request->getData("IDCARGO");
        $funcionario->STATUS         = $this->request->getData("STATUS");
        $funcionario->DATA_DEMISSAO  = (($this->request->getData("DATA_DEMISSAO")!="")?$time->parseDate($this->request->getData("DATA_DEMISSAO"))->i18nFormat("yyyy-MM-dd"):NULL);
        
        $funcionario->ENDERECO       = mb_strtoupper($this->request->getData("ENDERECO"));
        $funcionario->CEP            = $this->request->getData("CEP");
        $funcionario->IDCIDADE       = $this->request->getData("IDCIDADE");
        $funcionario->BAIRRO         = mb_strtoupper($this->request->getData("BAIRRO"));
        $funcionario->TELEFONE       = $this->request->getData("TELEFONE");
        $funcionario->TELEFONE2      = $this->request->getData("TELEFONE2");
        $funcionario->RECADOS        = mb_strtoupper($this->request->getData("RECADOS"));
        
        $funcionario->IDUSUARIO      = $this->request->getData("IDUSUARIO");
        
        $retorno = $table->save($funcionario)?true:false;
        
        return $this->response->withStringBody( $retorno );
    }
    
    /**
	* Metodo que busca as informacoes de um funcionario
	* @param int $_idFuncionario codigo do funcionario
	* 
	* @return object
	*/
    public function employerGetInfo($_idFuncionario){
        
        return $this->response->withStringBody( json_encode(TableRegistry::get('SysFuncionario')->get($_idFuncionario)) );
    }
    
    /**
	* Metodo que mostra o resultado da busca de funcionarios de um dialog
	* 
	* @return null
	*/
    public function employerDialog(){
        //realiza a carga da tabela de trabalho
        $table = TableRegistry::get("SysFuncionario");
        
        //realiza a busca da infos da tabela
        $query = $table->find();
        $query->select(['IDFUNCIONARIO','NOME','CPF','TELEFONE']);
        
        if($this->request->getData('TXT_EMPLOYER_CPF')!=""){
            $query->where(function($exp){ return $exp->like('CPF','%'.$this->request->getData('TXT_EMPLOYER_CPF').'%'); });
        }
        
        if($this->request->getData('TXT_EMPLOYER_NAME')!=""){
            $query->where(function($exp){ return $exp->like('NOME','%'.$this->request->getData('TXT_EMPLOYER_NAME').'%'); });
        }
        
        if($this->request->getData("TXT_EMPLOYER_FONE")!=""){
            $query->where(function($exp){ return $exp->like('TELEFONE','%'.$this->request->getData("TXT_EMPLOYER_FONE").'%'); });
        }
        
        $this->set('data_list',$this->paginate($query,['limit' => 10]));
    }
    
    /**************************** CARGOS **************************************/
    public function jobTitleFilter(){
		
		$this->Filter->addFilter("Nome","TXT_JOBTITLE_SEARCH_NAME","text");
		
		$list_jttype = TableRegistry::get('SysCargoTipo')->find()->select(['IDCARGOTIPO','NOME']);
		foreach($list_jttype as $jttype){
			$reg = new \stdClass();
			$reg->key   = $jttype->NOME;
			$reg->value = $jttype->IDCARGOTIPO;
			$data[] = $reg; 
		}
		
		$this->Filter->addFilter("Hierarquia de Cargo","CB_JOBTITLE_SEARCH_IDJOBTITLETYPE","combo",$data);
        
        return $this->response->withStringBody( $this->Filter->mountFilters() );
	}
	
	public function jobTitle(){
		$this->set('title',"Cargos");
		
		$this->set('url_filter','/hr/job_title_filter');
        $this->set('url_data','/hr/job_title_data');
	}
	
	public function jobTitleData(){
		$table = TableRegistry::get('SysCargo');
        
        $query = $table->find();
        $query->select(['IDCARGO','CARGO_TIPO' => 'CTP.NOME','NOME'])
        ->join([
    		'alias' => 'CTP',
    		'table' => 'sys_cargo_tipo',
    		'type'  => 'inner',
    		'conditions' => 'CTP.IDCARGOTIPO=SysCargo.IDCARGOTIPO'
        ]);
        
        if($this->request->getData("TXT_JOBTITLE_SEARCH_NAME")!=""){
            $query->where(function ($exp){
                return $exp->like('NOME','%'.$this->request->getData('TXT_JOBTITLE_SEARCH_NAME').'%');
            });
        }
        
        if($this->request->getData("CB_JOBTITLE_SEARCH_IDJOBTITLETYPE")!=""){
			$query->where(["CTP.IDCARGOTIPO" => $this->requiest->getData('CB_JOBTITLE_SEARCH_IDJOBTITLETYPE')]);
		}
        
        $this->set('data_list',$this->paginate($query,['limit' => 10]));
	}
	
	public function jobTitleCreate($_idJobTitle=""){
		$this->set('salary_list',TableRegistry::get('SysSalario')->find()->select(['IDSALARIO','NOME']));
		$this->set('jobtitle_list',TableRegistry::get('SysCargoTipo')->find()->select(['IDCARGOTIPO','NOME']));
		
		if($_idJobTitle!=""){
			$this->set('job_title',TableRegistry::get('SysCargo')->get($_idJobTitle));
		}
	}
	
	public function jobTitleSave(){
		$retorno = false;
		
		$table = TableRegistry::get('SysCargo');
		
		if($this->request->getData("IDCARGO")!=""){
			$cargo = $table->get($this->request->getData("IDCARGO"));
		}else{
			$cargo = $table->newEntity();
		}
		
		$cargo->IDCARGOTIPO = $this->request->getData("IDCARGOTIPO");
		$cargo->NOME        = mb_strtoupper($this->request->getData("NOME"));
		
		$retorno = $table->save($cargo)?true:false;
		
		return $this->response->withStringBody( $retorno );
	}
	
	/************************ HIERARQUIAS DE CARGOS *********************************/
	public function jobTitleTypeFilter(){
		
		$this->Filter->addFilter("Nome","TXT_JOBTITLE_SEARCH_NAME","text");
        
        return $this->response->withStringBody( $this->Filter->mountFilters() );
	}
	
	public function jobTitleType(){
		$this->set('title',__("Job Titles Hierarchy"));
		
		$this->set('url_filter','/hr/job_title_type_filter');
        $this->set('url_data','/hr/job_title_type_data');
	}
	
	public function jobTitleTypeData(){
		$table = TableRegistry::get('SysCargoTipo');
        
        $query = $table->find();
        $query->select(['IDCARGOTIPO','NOME']);
        
        if($this->request->getData("TXT_EMPLOYER_SEARCH_NAME")!=""){
            $query->where(function ($exp){
                return $exp->like('NOME','%'.$this->request->getData('TXT_EMPLOYER_SEARCH_NAME').'%');
            });
        }
        
        $this->set('data_list',$this->paginate($query,['limit' => 10]));
	}
	
	public function jobTitleTypeCreate($_idJobTitleType=""){
		if($_idJobTitleType!=""){
			$this->set('job_title_type',TableRegistry::get('SysCargoTipo')->get($_idJobTitleType));
		}
	}
	
	public function jobTitleTypeSave(){
		$retorno = false;
		
		$table = TableRegistry::get('SysCargoTipo');
		
		if($this->request->getData("IDCARGOTIPO")!=""){
			$cargo_tipo = $table->get($this->request->getData("IDCARGOTIPO"));
		}else{
			$cargo_tipo = $table->newEntity();
		}
		$cargo_tipo->NOME        = mb_strtoupper($this->request->getData("NOME"));
		
		$retorno = $table->save($cargo_tipo)?true:false;
		
		return $this->response->withStringBody( $retorno );
	}
	
	/************************** FUNCIONARIO X LOJA ****************************/
    public function employerStoreFilter(){

        $this->Filter->addFilter("Nome","TXT_EMPLOYER_SEARCH_NAME","text");
        
        $pops = array();
        $tblStore = TableRegistry::get('SysLoja');
        $stores = $tblStore->find()->select(['IDLOJA','NOME'])
            ->where(function($exp){ return $exp->notEq('IDLOJA',TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR); })
            ->order(['NOME' => 'ASC']);
        foreach($stores as $store){
            $opt = new \stdClass();
            $opt->key = $store->NOME;
            $opt->value = $store->IDLOJA;
            $pops[] = $opt;
        }
        
        $this->Filter->addFilter('Loja',"CB_EMPLOYER_SEARCH_STORE","combo",$pops);
        
        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }
    
    public function employerStore(){
    	$this->set('title',__("Employer X Store"));
        $this->set('url_filter','/hr/employer_store_filter');
        $this->set('url_data','/hr/employer_store_data'); 
    }
    
    public function employerStoreData(){
        $table = TableRegistry::get('SysFuncionarioLoja');
        
        $query = $table->find();
        $query->select(['IDFUNCIONARIO' => 'F.IDFUNCIONARIO','IDLOJA' => 'L.IDLOJA','FUNCIONARIO' => 'F.NOME','LOJA' => 'L.NOME']);
        $query->join([
                'F' => [
                    'table'      => 'sys_funcionario',
                    'conditions' => 'F.IDFUNCIONARIO=SysFuncionarioLoja.IDFUNCIONARIO',
                    'type'       => 'INNER'
                ],
                'L' => [
                    'table'      => 'sys_loja',
                    'conditions' => 'L.IDLOJA=SysFuncionarioLoja.IDLOJA',
                    'type'       => 'INNER'
                ]
            ]);
        if($this->request->data("TXT_EMPLOYER_SEARCH_NAME")!=""){
            $query->where(function ($exp){
                return $exp->like('F.NOME','%'.$this->request->data('TXT_EMPLOYER_SEARCH_NAME').'%');
            });
        }
        if($this->request->data("CB_EMPLOYER_SEARCH_STORE")!=""){
            $query->where(['L.IDLOJA' => $this->request->data("CB_EMPLOYER_SEARCH_STORE")]);
        }

        $this->Paginator->paginate($query,['limit' => 10]);
        
        $this->set('data_list',$this->paginate($query));
    }

    public function employerStoreCreate(){
        $this->set('stores',TableRegistry::get('SysLoja')->find()->select(['IDLOJA','NOME'])->where(function($exp){ return $exp->notEq('IDLOJA',TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR); }));
    }

    public function employerStoreSave(){        
        $retorno = false;
        $table = TableRegistry::get('SysFuncionarioLoja');
		
		$funcLoja = $table->newEntity();
        $funcLoja->IDFUNCIONARIO = $this->request->data("IDFUNCIONARIO");
        $funcLoja->IDLOJA        = $this->request->data("LOJA");

        $retorno = $table->save($funcLoja)?true:false;
        
        return $this->response->withStringBody( $retorno );
    }
    
    public function employerStoreRemove($_idFuncionario,$_idLoja){
        
        $table = TableRegistry::get('SysFuncionarioLoja');
        
        $funcLoja = $table->get(['IDFUNCIONARIO' => $_idFuncionario,'IDLOJA' => $_idLoja]);
        
        return $this->response->withStringBody( $table->delete($funcLoja)?true:false );
    }
}