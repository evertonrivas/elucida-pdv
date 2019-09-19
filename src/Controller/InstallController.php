<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\ConnectionManager;
use Phinx\Wrapper\TextWrapper;
use Phinx\Console\PhinxApplication;
use Cake\Auth\DefaultPasswordHasher;

class InstallController extends AppController{
	//put your code here

    public function initialize() {
        parent::initialize();
        $this->loadComponent("Paginator");
        $this->loadComponent("Filter");
        ob_start("ob_gzhandler");
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->Auth->allow(['index','save']);
    }

    public function index(){
        $text = new TextWrapper( new PhinxApplication(),['configuration' => '../phinx.yml']);
        $text->getMigrate("production","20190910134141");
    }

    public function save(){
        $retorno = false;

        //salva as informacoes do usuario admin
        $tblUsers = TableRegistry::get("SysUsers");
        $user = $tblUsers->newEntity();
        $user->username = $this->request->getData("USER_LOGIN");
        $user->password = (new DefaultPasswordHasher)->hash($this->request->getData("USER_SENHA"));
        $user->role     = "admin";
        $user->created  = date("Y-m-d H:i:s");
        $user->storeid  = 0;
        $user->name     = $this->request->getData("USER_NAME");
        $user->trash    = "N";
        $user->first_access = "S";
        $retorno = $tblUsers->save($user)?true:false;

        //cria a loja padrao do sistema
        $tblStore = TableRegistry::get('SysLoja');
        $store = $tblStore->newEntity();
        $store->IDCIDADE = 0;
        $store->CEP      = '';
        $store->NOME     = $this->request->getData("SYS_STORE_DEFAULT");
        $store->ENDERECO = '';
        $store->ENDERECO_NUM = 0;
        $store->TELEFONE = '-';
        $store->RESPONSAVEL = $this->request->getData("USER_NAME");
        $store->BAIRRO   = '';
        $store->DESCONTO_MAXIMO_SEM_SENHA = 0.0;
        $store->DESCONTO_SENHA = '';
        $retorno = $tblStore->save($store)?true:false;

        //cria o tipo de produto padrao da loja
        $tblType = TableRegistry::get('SysProdutoTipo');
        $tProd  = $tblType->newEntity();
        $tProd->DESCRICAO = $this->request->getData("SUP_TIPO_PRODUTO");
        $retorno = $tblType->save($tProd)?true:false;

        //cria o tipo do cargo do vendedor
        $tblCargoT = TableRegistry::get('SysCargoTipo');
        $tipoCargo = $tblCargoT->newEntity();
        $tipoCargo->NOME = 'VENDAS';
        $tipoCargo->DESATIVADO = '0';
        $retorno = $tblCargoT->save($tipoCargo)?true:false;

        //cria o cargo do vendedor
        $tblCargo = TableRegistry::get('SysCargo');
        $cargo = $tblCargo->newEntity();
        $cargo->IDCARGOTIPO = $tipoCargo->IDCARGOTIPO;
        $cargo->NOME = $this->request->getData("COM_CARGO_VENDEDOR");
        $retorno = $tblCargo->save($cargo)?true:false;

        //salva as opcoes do sistema
        $tblOption = TableRegistry::get('SysOpcao');
        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME  = 'DEFAULT_STORE';
        $opt->OPCAO_VALOR = $store->IDLOJA;
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME  = 'COMPANY_NAME';
        $opt->OPCAO_VALOR = $this->request->getData("SYS_COMPANY_NAME");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'CRON_CASHFLOW';
        $opt->OPCAO_VALOR = $this->request->getData("SYS_HORA_FLUXO");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'CRON_CUSTOMER_BIRTHDAY';
        $opt->OPCAO_VALOR = $this->request->getData("SYS_HORA_NIVER");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'CRON_COMPANY_BIRTHDAY';
        $opt->OPCAO_VALOR = $this->request->getData("SYS_HORA_NIVER_LOJA");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'BIRTHDAY_STORE_MONTH';
        $opt->OPCAO_VALOR = $this->request->getData("SYS_MES_NIVER_LOJA");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'CRON_PROCESS_TRANSFER';
        $opt->OPCAO_VALOR = $this->request->getData("SYS_HORA_TRANSFER");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'DEFAULT_PRODUCT_TYPE';
        $opt->OPCAO_VALOR = $tProd->IDPRODUTOTIPO;
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'BARCODE_SIZE';
        $opt->OPCAO_VALOR = $this->request->getData("SUP_BARCODE_SIZE");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'TRANSFER_EXPIRATION_DATE';
        $opt->OPCAO_VALOR = $this->request->getData("SUP_PRAZO_TRANSFER");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'REQUIRED_CUSTOMER';
        $opt->OPCAO_VALOR = $this->request->getData("COM_EXIGE_CLIENTE");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'COMPANY_SELLER';
        $opt->OPCAO_VALOR = $cargo->IDCARGO;
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'FIRST_TIME_EVENT';
        $opt->OPCAO_VALOR = $this->request->getData("FIN_FIRST_HOUR");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'SYSTEM_DEFAULT_MAIL_SERVER';
        $opt->OPCAO_VALOR = $this->request->getData("COM_MAIL_SERVER");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'SYSTEM_DEFAULT_MAIL_PORT';
        $opt->OPCAO_VALOR = $this->request->getData("COM_MAIL_PORT");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'SYSTEM_DEFAULT_MAIL_USER';
        $opt->OPCAO_VALOR = $this->request->getData("COM_MAIL_LOGIN");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'SYSTEM_DEFAULT_MAIL_PASS';
        $opt->OPCAO_VALOR = $this->request->getData("COM_MAIL_SENHA");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'DEFAULT_SYSTEM_MAIL';
        $opt->OPCAO_VALOR = $this->request->getData("COM_MAIL_MAIL");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'NAME_DEFAULT_MAIL';
        $opt->OPCAO_VALOR = $this->request->getData("COM_MAIL_NAME");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'BIRTHDAY_PERCENT';
        $opt->OPCAO_VALOR = $this->request->getData("COM_DESC_NIVER");
        $retorno = $tblOption->save($opt)?true:false;

        $opt = $tblOption->newEntity();
        $opt->OPCAO_NOME = 'BIRTHDAY_STORE_PERCENT';
        $opt->OPCAO_VALOR = $this->request->getData("COM_DESC_NIVER_LOJA");
        $retorno = $tblOption->save($opt)?true:false;

        if($retorno){
            \rename("../firstExec","../firstExecDone");
        }

        return $this->response->WithStringBody( $retorno );
    }
}
