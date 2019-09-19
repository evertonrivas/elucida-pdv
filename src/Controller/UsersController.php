<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\Http\Cookie\Cookie;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. */

/**
 * Description of UserController
 *
 * @author hestilo
 */
 class UsersController extends AppController{
	 
    public function initialize() {
        parent::initialize();
        $this->loadComponent("Paginator");
		
        $this->loadComponent("Filter");
        ob_start("ob_gzhandler");
    }
    
    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
		
		$this->Auth->allow(['logout','authenticate','forgotPassword','recoverPassword','newPassword','changePassword']);
    }
    
    public function filter(){ 
        $this->autoRender = false;

        $this->Filter->addFilter("Nome","TXT_USER_SEARCH_NAME","text");
        $this->Filter->addFilter("Login","TXT_USER_SEARCH_LOGIN","text");
        
        $ops = array();
        
        $opta = new \stdClass();
        $opta->key  = "Administrador";
        $opta->value = "admin";
        $ops[] = $opta;
        
        $optm = new \stdClass();
        $optm->key  = "Gerente";
        $optm->value = "manager";
        $ops[] = $optm;
        
        $optu = new \stdClass();
        $optu->key  = "Vendedor";
        $optu->value = "seller";
        $ops[] = $optu;
        
        $this->Filter->addFilter("Regra de Acesso","CB_USER_SEARCH_RULE","combo",$ops);
		
		$ords = array();
		
		$ordn = new \stdClass();
		$ordn->key   = "Nome";
		$ordn->value = "name";
		$ords[] = $ordn;
		
		$ordl = new \stdClass();
		$ordl->key   = "Usu&aacute;rio";
		$ordl->value = "username";
		$ords[] = $ordl;
		
		$ordr = new \stdClass();
		$ordr->key   = "Regra";
		$ordr->value = "role";
		$ords[] = $ordr;
		
		$this->Filter->addOrder($ords);
		
        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }
    
    public function index(){		
        $this->set('url_filter','/users/filter');
        $this->set('url_data','/users/list_data');
    }
    
    public function listData(){
        $tblUser = TableRegistry::get('SysUsers');
        
        $query = $tblUser->find();
        $query->select(['id','name','username','role']);
		$query->where(['trash' => 'N']);
		
        if($this->request->getData("TXT_USER_SEARCH_NAME")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('name','%'.$this->request->getData('TXT_USER_SEARCH_NAME').'%');
            });
        }
        if($this->request->getData("TXT_USER_SEARCH_LOGIN")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('username','%'.$this->request->getData('TXT_USER_SEARCH_LOGIN').'%');
            });
        }
        if($this->request->getData("CB_USER_SEARCH_RULE")!=""){
            $query->where(['role' => $this->request->getData("CB_USER_SEARCH_RULE")]);
        }        
        
		//Aqui entra a ordenacao de registros
		if($this->request->getData("CB_ORDER_FIELD")!=""){
			$query->order([$this->request->getData("CB_ORDER_FIELD") => $this->request->getData("CHK_ORDER_DIRECT")]);
		}
			
        $this->set('data_list',$this->paginate($query,['limit' => 10]));
    }

    public function create($_idUsuario="",$_isModal=false){
    	if($_isModal){
			$this->viewBuilder()->setLayout('dashboard');
		}
		
		$this->set('is_modal',$_isModal);
		$this->set('user',$this->Auth->user());
		
		$this->set('storelist',
			TableRegistry::get('SysLoja')->find()->select(['IDLOJA','NOME'])
		);
		
        if($_idUsuario!="" && $_idUsuario!=0){
            $this->set('usuario',TableRegistry::get('SysUsers')->get($_idUsuario));
        }
    }

    public function dataSave(){
        $retorno = false;
        
        $tblUser = TableRegistry::get('SysUsers');
		$sendMail = false;
        if($this->request->getData("IDUSUARIO")!=""){
            $usuario = $tblUser->get($this->request->getData("IDUSUARIO"));
            $usuario->modified = date("Y-m-d H:i:s");
        }else{
            $usuario = $tblUser->newEntity();
            $usuario->created = date("Y-m-d H:i:s");
			$sendMail = true;
        }
        
        $usuario->name      = htmlentities($this->request->getData("NOME"));
        $usuario->username  = mb_strtolower($this->request->getData("LOGIN"));
        $usuario->role      = $this->request->getData("REGRA");
        $usuario->storeid   = $this->request->getData("IDLOJA");
        $usuario->password  = (new DefaultPasswordHasher)->hash($this->request->getData("SENHA"));
		if($sendMail){
			$save = $tblUser->save($usuario)?true:false;
			if($save){
				$retorno = $this->sendEmail($usuario->name,$usuario->username,$this->request->getData("SENHA"),$usuario->id); 
			}else{
				$retorno = $save;
			}
		}else{
			$retorno = $tblUser->save($usuario)?true:false;
		}
		
		return $this->response->withStringBody($retorno);
    }
	
	public function trash(){		
		$tblReg = TableRegistry::get("SysUsers");
		$retorno = false;
		
		$regs = $this->request->getData("check_list");
		
		for($i=0;$i<count($regs);$i++){
			$item = $tblReg->get($regs[$i]);
			$item->trash = 'S';
			$item->trash_date = date("Y-m-d H:i:s");
			$retorno = ($tblReg->save($item))?true:false;
		}
		
		return $this->response->withStringBody( $retorno );
	}
    
    public function login(){
    	
    	if(!file_exists("../firstExec")){
			$this->viewBuilder()->setLayout('users');
	        
	        $browser = new \WhichBrowser\Parser(getallheaders());
	    	$this->set('browser',$browser);
	    	
	    	$min_version = new \stdClass();
	    	$min_version->chrome   = '75.0.3770';
	    	$min_version->edge     = '18';
	    	$min_version->iexplore = '11';
	    	$min_version->safari   = '5.1';
	    	$min_version->firefox  = '68';
	    	
	    	$this->set('min_version',$min_version);
	        
	        
	        $this->set('username',$this->request->getCookie('remember_me'));
		}
    	else{
			$this->redirect(['controller' => 'System','action' => 'check_config']);
		}
    }
	
	public function authenticate(){
		$retorno = 0;
		$user = $this->Auth->identify();
		
		//recebe o objeto response para modificar
		$response = $this->response;
		
		//campo remember
		$remember = $this->request->getData("remember");
		
		if($user){
			if($user['trash']=='N'){								
				if($user['role']=="admin"){
					$this->Auth->setUser($user);
					$retorno = 1;
				}else{
					//verifica se o funcionario estah
					//ainda admitido ou nao
					$func = TableRegistry::get('SysFuncionario')->find()->where(['IDUSUARIO' => $user['id']])->first();
					if($func->STATUS=="E"){
						$this->Auth->setUser($user);
						$retorno = 1;
					}else{
						$retorno = 2;
					}
				}
				
				//aqui salva a informacao do login para relembrar
				if($remember==1){
					$cookie = new Cookie(
						'remember_me',
						$this->request->getData("username"),
						new \DateTime("+1 year"),
						'/',
						"",
						false,
						true
					);
					
					//modifica o responde adicionando o cookie
					$response = $response->withCookie($cookie);
				}
			}else{
				$retorno = 2;
			}
		}
		
		//modifica o response adicionando a resposta
		$this->response = $response->withStringBody($retorno);
		
		return $this->response;
	}
    
    public function logout(){
		$this->autoRender = false;
        return $this->redirect($this->Auth->logout());
    }
	
	private function sendEmail($_nome,$_email,$_senha,$_id){
		$mail = new Email('default');
		
		$message = "<img src='http://".$this->request->env("SERVER_NAME")."/img/neugen.png'><br/>
		<p>Caro(a): <strong>".$_nome."</strong></p>
		<p>Foi criado um novo usu&aacute;rio para que consiga acessar o sistema Elucida da empresa <strong>".TableRegistry::get("SysOpcao")->get("COMPANY_NAME")->OPCAO_VALOR."</strong>.</p>
		<p>Para acessar o sistema utilize as informa&ccedil;&otilde;es abaixo:</p>
		<table style='border 1px solid #000000'><tr><td><strong>login:</strong> ".$_email."</td></tr><tr><td><strong>senha:</strong >".$_senha."</td></tr></table>
		<p>&nbsp;</p>
		<p>Clique <strong><a href='http://".$this->request->env("SERVER_NAME")."/'>aqui</a></strong> para acessar o sistema e trocar sua senha, ou cole a url abaixo no seu navegador<br/>
		http://".$this->request->env("SERVER_NAME")."/
		<p>Atenciosamente,</p>
		<p>".TableRegistry::get("SysOpcao")->get("NAME_DEFAULT_MAIL")->OPCAO_VALOR."</p>";
		
		return $mail->from([TableRegistry::get("SysOpcao")->get("DEFAULT_SYSTEM_MAIL")->OPCAO_VALOR => TableRegistry::get("SysOpcao")->get("NAME_DEFAULT_MAIL")->OPCAO_VALOR])
		->to([$_email => $_nome])
		->subject("Novo usuário no sistema ".TableRegistry::get('SysOpcao')->get("COMPANY_NAME")->OPCAO_VALOR)
		->emailFormat("html")
		->send($message)?true:false;
	}
	
	public function changePassword(){
		
		$tblUser = TableRegistry::get("SysUsers");
		
		$user = $tblUser->get($this->request->getData("IDUSUARIO"));
		$user->password  = (new DefaultPasswordHasher)->hash($this->request->getData("PASSWORD"));
		$user->first_access = 'N';
		
		$this->Auth->logout();
		
		return $this->response->withStringBody( $tblUser->save($user)?true:false );
	}
	
	public function forgotPassword(){
		$this->viewBuilder()->setLayout('users');
	}
	
	public function recoverPassword(){
		
		$tblUser = TableRegistry::get("SysUsers");
		
		$user = $tblUser->find()->where(['username' => $this->request->getData("username")])->first();
		if($user->trash=='S') 
			return $this->response->withStringBody( 2 );
		else
			return $this->response->withStringBody( $this->sendEmailPassword($user->name,$user->username,$user->id) );
	}
	
	private function sendEmailPassword($_nome,$_email,$_id){
		$mail = new Email('default');
		
		$message = "<img src='http://".$this->request->env("SERVER_NAME")."/img/neugen.png'><br/>
		<p>Caro(a): <strong>".$_nome."</strong></p>
		<p>Utilize o link abaixo para trocar a senha do seu usu&aacute;rio do sistema Elucida da empresa <strong>".TableRegistry::get("SysOpcao")->get("COMPANY_NAME")->OPCAO_VALOR."</strong>.</p>
		<p>&nbsp;</p>
		<p>Clique <strong><a href='http://".$this->request->env("SERVER_NAME")."/new_password/".md5($_id)."'>aqui</a></strong> criar uma nova sua senha, ou cole a url abaixo no seu navegador.<br/>
		http://".$this->request->env("SERVER_NAME")."/new_password/".md5($_id)."
		<p>Atenciosamente,</p>
		<p>".TableRegistry::get("SysOpcao")->get("NAME_DEFAULT_MAIL")->OPCAO_VALOR."</p>";
		
		return $mail->from([TableRegistry::get("SysOpcao")->get("DEFAULT_SYSTEM_MAIL")->OPCAO_VALOR => TableRegistry::get("SysOpcao")->get("NAME_DEFAULT_MAIL")->OPCAO_VALOR])
		->to([$_email => $_nome])
		->subject("Solicitação de nova senha no sistema ".TableRegistry::get('SysOpcao')->get("COMPANY_NAME")->OPCAO_VALOR)
		->emailFormat("html")
		->send($message)?true:false;
	}
	
	public function newPassword($_id){
		$this->viewBuilder()->setLayout('users');
		$this->set('usuario',TableRegistry::get('SysUsers')->find()->where(['md5(id)' => $_id])->first());
		
	}
	
	/**
	* Metodo que retorna a listagem de usuarios em formato json
	* @param boolean $withAdmin parametro que exclui administradores da busca
	* 
	* @return json
	*/
	public function json($withAdmin=false){
		$users = TableRegistry::get('SysUsers')->find()->select(['id','name','username']);
		if(!$withAdmin){
			$users->where(function($exp){ return $exp->notEq('role','admin'); });
		}
		
		return $this->response->withStringBody( json_encode($users) );
	}
	
	/**
	* Metodo que exibe a pagina para alteracao da senha
	* 
	* @return void
	*/
	public function password(){
		$user = TableRegistry::get("SysUsers")->get( $this->Auth->user()['id'] );
		
		$this->set('usuario',$user);
	}
}