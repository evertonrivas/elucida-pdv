<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Number;
use Cake\I18n\Time;

class RetailController extends AppController{
    public function initialize() {
        parent::initialize();
        $this->loadComponent("Paginator");
        $this->loadComponent("Filter");
        ob_start("ob_gzhandler");
    }

    /**************************PDV***************************/

    /**
	* Metodo que exibe a tela de vendas
	*
	* @return null
	*/
    public function pos(){
    	//verifica se eh uma requisicao mobile para montar a tela de acordo
    	$this->set('is_mobile', $this->request->is('mobile'));

        $user = $this->Auth->user();

        //variaveis caso o usuario tenha clicado em voltar
        $_idcliente      = $this->request->query("IDCLIENTE");
        $_new_customer   = $this->request->query("NEW_CUSTOMER");
        $_desconto_indica = $this->request->query("INDICATION_DISCOUNT");
        $_idfuncionario  = $this->request->query("IDFUNCIONARIO");
        $_condicao_promo = $this->request->query("CONDICAO_PROMOCAO");
        $_outro_cpf      = $this->request->query("OUTRO_CPF");

        //envia para a tela o codigo da loja
        $this->set('IDLOJA',$user['storeid']);

        //busca quem sao os funcionarios autorizados para a venda
        //nesta loja
        $employers = TableRegistry::get('SysFuncionario')->find()
            ->select(['SysFuncionario.IDFUNCIONARIO','SysFuncionario.APELIDO'])
            ->join([
                'SFL' => [
                    'table' => 'sys_funcionario_loja',
                    'type'  => 'INNER',
                    'conditions' => 'SFL.IDFUNCIONARIO=SysFuncionario.IDFUNCIONARIO'
                ],
                'SC' => [
                    'table' => 'sys_cargo',
                    'type'  => 'INNER',
                    'conditions' => 'SC.IDCARGO=SysFuncionario.IDCARGO'
                ]
            ])->where(['SC.IDCARGO' => TableRegistry::get('SysOpcao')->get('COMPANY_SELLER')->OPCAO_VALOR])
            ->where(['FL.IDLOJA' => $user['storeid']])
            ->where(['SysFuncionario.STATUS' => 'E']);

        //define se ha necessidade de obrigar um cliente para a venda
        $this->set('EXIGE_CLIENTE',TableRegistry::get('SysOpcao')->get("REQUIRED_CUSTOMER")->OPCAO_VALOR);
        //define as condicoes de pagamento necessarias para a verificao das promocoes
        $this->set('condition_list',TableRegistry::get('SysCondicaoPagamento')->find()->where(['EXIBIR_PDV' => '1']));
        $this->set('employer_list',$employers);


        //se houver um codigo de cliente entao busca o nome do mesmo para exibir na caixa
        if($_idcliente!=""){
            $this->set('NOME_CLIENTE',TableRegistry::get('SysCliente')->get($_idcliente)->NOME);
        }


        //as seguintes variaveis sao passadas para a tela, pois vieram da tela de pagamento
        //quando o funcionario clicou em voltar

        //define o codigo do cliente
        $this->set('IDCLIENTE',$_idcliente);

        //define se eh um cliente novo (usado anteriormente para aplicar desconto por indicacao)
        //optei por deixar para tentar aproveitar no futuro
        $this->set('NEW_CUSTOMER',(int)$_new_customer);
        //idem a variavel anterior
        $this->set('INDICATION_DISCOUNT',(int)$_desconto_indica);

        //define o codigo do funcionario que realizou a venda
        $this->set('IDFUNCIONARIO',$_idfuncionario);
        //define a condicao da promocao
        $this->set('CONDICAO_PROMOCAO',$_condicao_promo);
        //define o codigo do outro CPF que nao eh o do cliente cadastrado
        $this->set('OUTRO_CPF',$_outro_cpf);

        //define o status do caixa, dependendo das condicoes redireciona
        $this->set('status_caixa',$this->boxStatus());
    }

	/**
	* Metodo que busca o itens de estao sendo vendidos
	*
	* @return null
	*/
    public function posBasketGet(){
        $user = $this->Auth->user();
        $data_list = TableRegistry::get('LojItemVenda')->find()->where(['IDUSUARIO' => $user['id'], 'IDLOJA' => $user['storeid']]);
        $this->set('total_data',$data_list->count());
        $this->set('data_list',$data_list);
    }

	/**
	* Metodo que busca os totais da venda
	*
	* @return null
	*/
    public function posBasketTotals(){
        $user = $this->Auth->user();
        $tblItens = TableRegistry::get('LojItemVenda')->find()->where(['IDUSUARIO' => $user['id'], 'IDLOJA' => $user['storeid']]);

        $totais = new \stdClass();
        $totais->DESCONTO = $tblItens->sumOf('DESCONTO');
        $totais->SUBTOTAL = $tblItens->sumOf('SUBTOTAL');

        $tmpCupom = TableRegistry::get('TmpCupom');
        $this->set('cupom',$tmpCupom->find()->where(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']])->first());

        $this->set('totais',$totais);
    }

	/**
	* Metodo que adiciona um produto na cesta de compras
	* @param string $_barcode Codigo de barras
	* @param int $_qty quantidade que estah sendo vendida
	* @param float $_preco preco quando promocional ou quando alterado na tela
	*
	* @return boolean
	*/
    public function posBasketAdd($_barcode, $_qty, $_preco=0){
        $retorno = false;

        $tblItem = TableRegistry::get('LojItemVenda');
        $user = $this->Auth->user();

        //busca as informações do produto antes de adicioná-lo ao pdv
        $codigo_barra = (strlen($_barcode)==14)?$this->Produto->CODIGO_BARRA = substr($_barcode,1,14):$_barcode;

        //busca o produto a partir do codigo de barras
        $produto = TableRegistry::get('SysProduto')->find()->where(['CODIGO_BARRA' => $codigo_barra])->first();

        //atualiza o preco do produto para o preco promocional ou outro
        if($_preco!=0){
            $produto->PRECO_VENDA = $_preco;
        }

		//busca as informacoes do item de venda para
		//com a finalidade de descobrir se o mesmo ja
		//esta associado a venda
        $search = $tblItem->find()->where(['IDPRODUTO' => $produto->IDPRODUTO,'IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']])->count();
        if($search > 0){
            //realiza verificacao do estoque total do produto
            $stock = TableRegistry::get('LojEstoque')->get(['IDLOJA' => $user['storeid'],'IDPRODUTO' => $produto->IDPRODUTO]);

            //se o item ja estiver associado a venda apenas
            //incrementa sua quantidade
            $item_venda = $tblItem->get(['IDLOJA' => $user['storeid'],'IDPRODUTO' => $produto->IDPRODUTO,'IDUSUARIO' => $user['id']]);
            $nova_quantidade = $item_venda->QUANTIDADE+$_qty;

            //tratamento para evitar que ultrapasse a quantidade
            //disponivel em estoque
            if($stock->QUANTIDADE >= ($nova_quantidade)){
                $item_venda->QUANTIDADE = $nova_quantidade;
                $item_venda->SUBTOTAL  = $item_venda->QUANTIDADE*$produto->PRECO_VENDA;
            }
            else{
                $retorno = false;
                exit;
            }
        }else{
        	//cria um novo item de venda
            $item_venda = $tblItem->newEntity();
            $item_venda->IDLOJA         = $user['storeid'];
            $item_venda->IDUSUARIO      = $user['id'];
            $item_venda->DESCONTO       = 0;
            $item_venda->IDPRODUTO      = $produto->IDPRODUTO;
            $item_venda->QUANTIDADE     = $_qty;
            $item_venda->PRECO_UN       = $produto->PRECO_VENDA;
            $item_venda->SUBTOTAL       = $produto->PRECO_VENDA*$_qty;
            $item_venda->NOME_PRODUTO   = ($produto->NOME_TAG!="")?$produto->NOME_TAG:$produto->NOME;
            $item_venda->SKU_PRODUTO    = $produto->SKU;
            $item_venda->UNIDADE_MEDIDA = $produto->UNIDADE_MEDIDA;
        }
        //se a quantidade for maior que zero entao salva os dados do
        //item de venda
        if($_qty>0){
            $retorno = $tblItem->save($item_venda)?true:false;
        }else{
            $retorno = true;
        }

        return $this->response->withStringBody($retorno);
    }

    /**
	* Metodo que remove um item das vendas
	*
	* @return boolean
	*/
    public function posBasketDel(){

        $idproduto = $this->request->getData("IDPRODUTO");

        $user = $this->Auth->user();

        $tblItem = TableRegistry::get('LojItemVenda');
        $item = $tblItem->get(['IDLOJA' => $user['storeid'],'IDPRODUTO' => $idproduto,'IDUSUARIO' => $user['id']]);

        return $this->response->withStringBody( $tblItem->delete($item)?true:false );
    }

	/**
	* Metodo que limpa os dados dos itens da venda
	*
	* @return boolean
	*/
    public function posBasketClear(){
        $retorno = false;
        $user = $this->Auth->user();
        $tblItem = TableRegistry::get('LojItemVenda');
        TableRegistry::get('TmpPagamento')->deleteAll(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']]);
        TableRegistry::get('TmpCupom')->deleteAll(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']]);
        $retorno = $tblItem->deleteAll(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']])?true:false;

        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que retorna os itens de venda no formato json
	* para serem usados na selecao de promocao quando aplicada
	*
	* @return json
	*/
    public function posBasketItens(){

        $user = $this->Auth->user();
        $data_list = TableRegistry::get('LojItemVenda')->find()->where(['IDUSUARIO' => $user['id'], 'IDLOJA' => $user['storeid']]);

        return $this->response->withStringBody( json_encode($data_list) );
    }

	/**
	* Metodo que aplica um desconto em um produto ou em toda a venda
	*
	* @return float
	*/
    public function posDiscountApply(){

        $user = $this->Auth->user();

        $_valor   = $this->request->getData("VALOR");
        $_produto = $this->request->getData("PRODUTO");
        $tblItens = TableRegistry::get('LojItemVenda');
        $itens_venda = $tblItens->find()->where(['IDUSUARIO' => $user['id'], 'IDLOJA' => $user['storeid']]);

        //verifica se o desconto serah aplicado em um produto
        //ou se serah aplicado sobre toda a venda
        if($_produto!=""){
            foreach($itens_venda as $item){
                if($_produto==$item->IDPRODUTO){
                    //verifica se o desconto eh percenttual
                    if(strpos($_valor, "pct")!==false){
                        $desconto = round(($item->PRECO_UN*$item->QUANTIDADE)*(str_replace("pct", "", $_valor)/100),1);
                    }else{
                        $desconto = round($_valor,1);
                    }
                    $item->DESCONTO = $desconto;
                    $tblItens->save($item);
                }
            }
        }
        else{ //caso contrario sera aplicado em todos os produtos da venda
            $subtotal = $itens_venda->sumOf('SUBTOTAL');

            //verifica se o desconto eh percentual
            if(strpos($_valor, "pct")!==false){
                foreach($itens_venda as $item){
                    $item->DESCONTO = round(($item->PRECO_UN*$item->QUANTIDADE)*(str_replace("pct", "", $_valor)/100),1);
                    $tblItens->save($item);
                }
            }
            else{
            	//transforma o valor cheio em percentual
            	//para aplicar em cada produto
                $perc_desc = $_valor/$subtotal;
                foreach($itens_venda as $item){
                    $item->DESCONTO = round(($item->PRECO_UN*$item->QUANTIDADE)*($perc_desc),1);
                    $tblItens->save($item);
                }
            }
        }

        return $this->response->withStringBody( number_format($itens_venda->sumOf("DESCONTO"),2) );
    }

    /**
	* Metodo que verifica o tamanho do desconto que serah aplicado
	*
	* @return boolean
	*/
    public function posDiscountCheckSize(){
        $retorno = false;
        $user = $this->Auth->user();

        $senha_desconto = TableRegistry::get('SysLoja')->get($user['storeid'])->DESCONTO_SENHA;
        $desconto_sem_senha = TableRegistry::get('SysLoja')->get($user['storeid'])->DESCONTO_MAXIMO_SEM_SENHA;
        $_valor = $this->request->getData("VALOR");
        $_senha = $this->request->getData("SENHA");

        //verifica se o desconto eh percetual ou nao
        if(strpos($_valor, "pct")!==false){
            $valDesconto = str_replace("pct","",$_valor);
            $valDesconto = $valDesconto/100;

            //verifica se o valor do desconto eh maior
            //do que o permitido sem senha
            if($valDesconto > $desconto_sem_senha){
            	//se a senha nao foi digitada retorna
            	//a solicitacao
                if($_senha==""){
                    $retorno = "needpass";
                }else{
                	//se a senha estiver correta
                	//retorna que pode aplicar o desconto
                    if($senha_desconto==$_senha){
                        $retorno = "canapply";
                    }else{
                    	//retorna erro na senha
                        $retorno = "wrongpass";
                    }
                }
            }
            else{
            	//retorna que nao pode aplicar o desconto
                $retorno = "canapply";
            }
        }else{
        	//se o desconto for por valor
            $tblItens = TableRegistry::get('LojItemVenda')->find()->where(['IDUSUARIO' => $user['id'], 'IDLOJA' => $user['storeid']]);
            $subtotal = $tblItens->sumOf('SUBTOTAL');

            //converte o valor do desconto em percentual para realizar
            //o teste em relacao ao que ha nas configuracoes
            if( ($_valor/$subtotal)>$desconto_sem_senha){
                if($_senha==""){
                    $retorno = "needpass";
                }else{
                    if($senha_desconto==$_senha){
                        $retorno = "canapply";
                    }
                    else{
                        $retorno = "wrongpass";
                    }
                }
            }
            else{
                $retorno = "canapply";
            }
        }

        return $this->response->withStringBody($retorno);
    }

    /**
	* Metodo que aplica um cupom em uma venda
	* salvando as informacoes primeiramente em
	* uma tabela temporaria, jah que os cupons
	* sao aplicados ao fim da venda
	*
	* @return boolean
	*/
    public function posCupomApply(){
        $user = $this->Auth->user();

        $tblCupom = TableRegistry::get('SysCupom');
        $tmpCupom = TableRegistry::get('TmpCupom');

        //limpa os descontos aplicados no carrinho
        $tblItem   = TableRegistry::get('LojItemVenda');
        $data_list = $tblItem->find()->where(['IDUSUARIO' => $user['id'], 'IDLOJA' => $user['storeid']]);
        foreach($data_list as $item){
            $item->DESCONTO = 0;
            $tblItem->save($item);
        }

        //recebe o codigo do cupom
        $_codigo = $this->request->getData("CUPOM");

        //cria um nova instancia
        $cupom = $tmpCupom->newEntity();

        //define a variavel de retorno
        $retorno = false;

        //verifica seh nao foi enviado um codigo em branco
        if(trim($_codigo)!=""){
            //verifica se exite o cupom na tabela de cupons do sistema
            $_exist = ($tblCupom->find()->where(['CODIGO' => $_codigo])->count()>0)?true:false;
            if($_exist>0){
                //busca as informacoes do cupom
                $_cupom = $tblCupom->find()->where(['CODIGO' => $_codigo])->first();

                //verifica se estah utilizado ou se eh indeterminado
                if($_cupom->UTILIZADO=="N" || $_cupom->UTILIZADO=="I"){
                    //garante que nao serah adicionado mais de uma vez o mesmo cupom ou serao usados 2 cupons juntos
                    if($tmpCupom->find()->where(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']])->count()==0){

                        $cupom->TIPO_VALOR = $_cupom->TIPO_VALOR;
                        $cupom->IDCUPOM    = $_cupom->IDCUPOM;
                        $cupom->TIPO_CUPOM = $_cupom->TIPO_CUPOM;
                        //se for percentual o desconto entao nao precisa inverter o valor
                        $cupom->VALOR      = ($_cupom->TIPO_VALOR=="%")?$_cupom->VALOR:($_cupom->VALOR*-1);
                        $cupom->IDLOJA     = $user['storeid'];
                        $cupom->IDUSUARIO  = $user['id'];
                        $retorno = ($tmpCupom->save($cupom))?true:false;
                    }
                }
            }else{
            	//verifica se eh um cupom de devolucao
            	//entao acaba buscando em outro lugar
                $tblDevol  = TableRegistry::get('LojDevolucao');
                $_exist = $tblDevol->find()->where(['IDDEVOLUCAO' => $_codigo])->count();
                //verifica se existe a devolucao
                if($_exist){
                    $devolucao = $tblDevol->get($_codigo);
                    //verifica se jah foi realizada
                    if($devolucao->UTILIZADO=="N"){
                        $cupom->VALOR      = $devolucao->VALOR_TOTAL*-1;
                        $cupom->TIPO_VALOR = "$";
                        $cupom->IDCUPOM    = $_codigo;
                        $cupom->TIPO_CUPOM = "T";
                        $cupom->IDLOJA     = $user['storeid'];
                        $cupom->IDUSUARIO  = $user['id'];

						//se o valor da devolucao foi maior que zero
						//aplica a devolucao
                        if($devolucao->VALOR_TOTAL > 0){
                            $retorno = ($tmpCupom->save($cupom))?true:false;
                        }
                        else{
                        	//caso contrario remove a devolucao
                            $retorno = ($tmpCupom->delete($cupom))?-1:false;
                        }
                    }
                }else{
                	//se na for uma devolucao verifica se eh uma parceria
                    $tblPartner = TableRegistry::get('SysParceiro');
                    $_exist = $tblPartner->find()->where(['CODIGO_CUPOM' => $_codigo]);
                    //verifica se existe o parceiro com o codigo de cupom informado
                    if($_exist->count()>0){
                        $parceiro = $_exist->first();
                        $cupom->VALOR   = $parceiro->PERC_DESCONTO;
                        $cupom->TIPO_VALOR = "%";
                        $cupom->IDCUPOM    = $_codigo;
                        $cupom->TIPO_CUPOM = "D";
                        $cupom->IDLOJA     = $user['storeid'];
                        $cupom->IDUSUARIO  = $user['id'];
                        $retorno = ($tmpCupom->save($cupom))?true:false;
                    }else{
                    	//se nao encontrar nenhum cupom mas tiver algum codigo associado
                    	//remove todos os cupons existens para a venda
                        $retorno = $tmpCupom->deleteAll(['IDUSUARIO' => $user['id'],'IDLOJA' => $user['storeid']])?-1:false;
                    }
                }
            }
        }else{
            //remove todos os cupons do usuario que estah executando venda na loja
            $tmpCupom->deleteAll(['IDUSUARIO' => $user['id'],'IDLOJA' => $user['storeid']]);
            $retorno = false;
        }

        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que busca os itens expressos que serao exibidos no PDV
	*
	* @return null
	*/
    public function posExpressItensGet(){
        $user = $this->Auth->user();

        //busca os itens simples que foram adicionados na tabela de itens expressos
        $express_s = TableRegistry::get('SysProduto')->find()
            ->select(['X.IDPRODUTO','CODIGO_BARRA','IMAGEM','QUANTIDADE' => 'E.QUANTIDADE','NOME'])
            ->join([
                'X' =>[
                    'table' => 'loj_item_expresso',
                    'type'  => 'INNER',
                    'conditions' => 'X.IDPRODUTO=SysProduto.IDPRODUTO'
                ],
                'E' =>[
                    'table' => 'loj_estoque',
                    'type'  => 'INNER',
                    'conditions' => 'E.IDLOJA=X.IDLOJA AND E.IDPRODUTO=SysProduto.IDPRODUTO'
                ]
            ])
            ->where(['X.IDLOJA' => $user['storeid'],'ESTRUTURA' => 'S','STATUS' => 'A'])
            ->where(function($exp,$q){
                return $exp->gt('E.QUANTIDADE',0);
            })->order(['NOME'=>'ASC']);
        $this->set('simple_products',$express_s);

        //busca os itens compostos que foram adicionados na tabela de itens expressos
        $express_c =TableRegistry::get('SysProduto')->find()
            ->select(['X.IDPRODUTO','CODIGO_BARRA','IMAGEM','NOME'])
            ->join([
                'X' =>[
                    'table' => 'loj_item_expresso',
                    'type'  => 'INNER',
                    'conditions' => 'X.IDPRODUTO=SysProduto.IDPRODUTO'
                ],
                'E' =>[
                    'table' => 'loj_estoque',
                    'type'  => 'INNER',
                    'conditions' => 'E.IDLOJA=X.IDLOJA AND E.IDPRODUTO=SysProduto.IDPRODUTO'
                ]
            ])
            ->where(['X.IDLOJA' => $user['storeid'],'ESTRUTURA' => 'C','STATUS' => 'A'])->order(['NOME'=>'ASC']);

        //no caso dos itens compostos ainda eh
        //realizada a verificacao de disponibilidade
        $tem_quantidade = false;
        $composto = array();

        //varre cada produto composto que eh expresso em busca de seus itens
        foreach($express_c as $complex){
            //debug($complex);
            $itens = TableRegistry::get('SysProdutoItem')->find()->select(['QUANTIDADE' => 'E.QUANTIDADE'])
                ->join([
                    'table' => 'loj_estoque',
                    'type'  => 'INNER',
                    'alias' => 'E',
                    'conditions' => 'E.IDPRODUTO=SysProdutoItem.IDPRODUTO_FILHO'
                ])
                ->where(['SysProdutoItem.IDPRODUTO' => $complex['X']['IDPRODUTO']])
                ->where(['E.IDLOJA' => $user['storeid']]);
            $tem_quantidade = true;
            //verifica se cada item tem quantidade em estoque
            foreach($itens as $item){
            	if($tem_quantidade){
					$tem_quantidade = ($item->QUANTIDADE > 0)?true:false;
				}
            }

            if($tem_quantidade){
                $composto[] = $complex;
            }
        }
        $this->set('complex_products',$composto);
    }

    /**
	* Metodo que adiciona um produto como item expresso
	*
	* @return
	*/
    public function posExpressItemAdd(){

        $user = $this->Auth->user();

        $tblExpress = TableRegistry::get('loj_item_expresso');
        $express    = $tblExpress->newEntity();

        $express->IDLOJA    = $user['storeid'];
        $express->IDPRODUTO = $this->request->getData("IDPRODUTO");
        return $this->response->withStringBody( $tblExpress->save($express)?true:false );
    }

    /**
	* Metodo que exibe a tela de processamento de pagamentos
	*
	* @return null
	*/
    public function paymentProcess(){
        $user = $this->Auth->user();
        $tblItens = TableRegistry::get('LojItemVenda')->find()->where(['IDUSUARIO' => $user['id'], 'IDLOJA' => $user['storeid']]);

        //cria um objeto com os totais
        $totais = new \stdClass();
        $totais->DESCONTO = $tblItens->sumOf('DESCONTO');
        $totais->SUBTOTAL = $tblItens->sumOf('SUBTOTAL');
        $totais->VALOR_CUPOM = 0;

        //busca as condicoes de pagamento disponiveis
        $this->set('condicoes_pagamento',TableRegistry::get('SysCondicaoPagamento')->find()->where(['EXIBIR_PDV' => 1]));

        //tratamento para incluir o valor do cupom
        $tmpCupom = TableRegistry::get('TmpCupom');
        //verifica se existe um cupom
        if($tmpCupom->find()->where(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']])->count()>0){

            //verifica se o cupom ja nao foi adicionado ao pagamento
            $cupom = $tmpCupom->find()->where(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']])->first();

            switch($cupom->TIPO_CUPOM){
                case 'P':{//vale presente
                    $this->paymentAdd( (int)TableRegistry::get('SysOpcao')->get("PAYMENT_CONDITION_GIFT")->OPCAO_VALOR ,($cupom->VALOR*-1),false);
                }break;
                case 'D':{//desconto
                    $totais->VALOR_CUPOM = ($cupom->TIPO_VALOR=="%")?round($totais->SUBTOTAL*($cupom->VALOR/100),1):$cupom->VALOR;
                    $this->paymentAdd( (int)TableRegistry::get('SysOpcao')->get("PAYMENT_CONDITION_DISCOUNT")->OPCAO_VALOR ,$totais->VALOR_CUPOM,false);
                }break;
                case 'A':{//Antecipacao de vendas
                    $this->paymentAdd( (int)TableRegistry::get('SysOpcao')->get("PAYMENT_CONDITION_ORDER_SELL")->OPCAO_VALOR ,($cupom->VALOR*-1),false);
                }break;
                case 'T':{//Troca
                    $this->paymentAdd( (int)TableRegistry::get('SysOpcao')->get("PAYMENT_CONDITION_CHANGE")->OPCAO_VALOR ,($cupom->VALOR*-1),false);
                }
            }
        }

        //passa para a tela o objeto de totais
        $this->set('totais',$totais);

        //limpa tracos e pontos do CPF ou CNPJ
        $_cpf_alternativo = str_replace("/","",str_replace("-","",str_replace(".","",$this->request->getData("txtOtherTaxvat"))));
        //$desconto_indica = (int)$this->request->getData('txtDescontoIndicacao');
        $this->set('IDCLIENTE',$this->request->getData("txtIdCliente"));
        //$this->set('NEW_CUSTOMER',(int)$this->request->getData("txtNewCustomer"));
        //$this->set('INDICATION_DISCOUNT',$desconto_indica);
        $this->set('NOME_CLIENTE',$this->request->getData("txtNomeCliente"));
        $this->set('IDFUNCIONARIO',(int)$this->request->getData("txtIdFuncionario"));
        $this->set('CPF_NA_NOTA',(int)($this->request->getData("chCpfNota")));
        $this->set('CPF_ALTERNATIVO',$_cpf_alternativo);
        $this->set('CONDICAO_PROMOCAO',$this->request->getData("txtIdCondicaoPromo"));
        $this->set('IDLOJA',$this->request->getData("txtLoja"));
        //$this->set('VALUE_MIN_INDICATION',TableRegistry::get('SysOpcao')->get('VALUE_MIN_INDICATION')->OPCAO_VALOR);

        $this->set('TIPO_EMISSAO_NOTA',TableRegistry::get('SysLoja')->get($user['storeid'])->NFE_EMITE);
    }

    /**
	* Metodo que retorna os detalhes das opcoes de pagamento que o cliente
	* estah utilizando, isso serve para mostrar valor de parcelas
	* e tambem para casos de pagamento com mais de um meio
	*
	* @return null
	*/
    public function paymentsGet(){
        $user = $this->Auth->user();
        $tmpPayment = TableRegistry::get('TmpPagamento');

        $this->set('payments',$tmpPayment->find()->where(['IDUSUARIO' => $user['id'],'IDLOJA' => $user['storeid']]));
    }

    /**
	* Metodo que monta o somatorios dos totais de pagamento incluindo o troco
	* @param boolean $print indica se o retorno eh para ajax ou para funcao
	*
	* @return 0 ou troco
	*/
    public function paymentGetTotal($print=true){

        $user = $this->Auth->user();
        $payments = TableRegistry::get('TmpPagamento')->find()->where(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']]);
        $total = 0;
        if(isset($payments)){
            foreach($payments as $pay){
                $total += $pay->VALOR_PAGO;
            }
        }
        if($print){
            return $this->response->withStringBody( $total );
        }else{
            return $total;
        }
    }

    /**
	* Metodo que adiciona uma condicao de pagamento na finalizacao de venda
	* @param int $_idcondicaopagamento codigo da condicao de pagamento
	* @param float $_valor valor que estah sendo usado para a condicao
	* @param boolean $print flag que indica se retorna para a funcao ou exibe retorno
	*
	* @return
	*/
    public function paymentAdd($_idcondicaopagamento, $_valor, $print=true){
        $user = $this->Auth->user();
        $tmpPay = TableRegistry::get('TmpPagamento');
        //busca as informacoes da session
        $payments = $tmpPay->find()->where(['IDCONDICAOPAGAMENTO' => $_idcondicaopagamento,'IDUSUARIO' => $user['id'],'IDLOJA' => $user['storeid']]);
        $retorno = false;

        $cond_pag = TableRegistry::get('SysCondicaoPagamento')->get($_idcondicaopagamento);
        //se a condicao de pagamento ainda nao foi utilizada
        if($payments->count()==0){
            $payment = $tmpPay->newEntity();
            $payment->IDCONDICAOPAGAMENTO = $_idcondicaopagamento;
            $payment->IDLOJA              = $user['storeid'];
            $payment->IDUSUARIO           = $user['id'];
            $payment->VALOR_PAGO          = $_valor;
            $payment->CONDICAO_PAGAMENTO  = $cond_pag->NOME;
            $payment->VALOR_PARCELA       = ($_valor/(($cond_pag->PARCELAS==0)?1:$cond_pag->PARCELAS));
            $retorno = $tmpPay->save($payment)?true:false;
        }else{
            //apenas atualiza os valores da condicao
            $payment = $tmpPay->get(['IDCONDICAOPAGAMENTO' => $_idcondicaopagamento,'IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']]);
            $payment->VALOR_PAGO    = $_valor;
            $payment->VALOR_PARCELA = ($_valor/(($cond_pag->PARCELAS==0)?1:$cond_pag->PARCELAS));
            $retorno = $tmpPay->save($payment)?true:false;
        }

        if($print){
            return $this->response->withStringBody( $retorno );
        }
        else{
            return $retorno;
        }
    }

    /**
	* Metodo que remove uma condicao de pagamento ja indicada
	* @param int $_idcondicaopagamento codigo da condicao de pagamento
	* @param boolean $print flag que indica se imprime ou retorna
	*
	* @return
	*/
    public function paymentDel($_idcondicaopagamento,$print=true){

        $user = $this->Auth->user();
        $tmpPay  = TableRegistry::get('TmpPagamento');
        $payment = $tmpPay->get(['IDCONDICAOPAGAMENTO' => $_idcondicaopagamento,'IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']]);
        $retorno = $tmpPay->delete($payment)?true:false;

        if($print){
            return $this->response->withStringBody( $retorno );
        }else{
            return $retorno;
        }
    }

    /**************************TROCAS***************************/

    /**
	* Metodo que exibe a tela de realizacao de trocas
	*
	* @return
	*/
    public function exchangeMake(){
        $this->set('user',$this->Auth->user());
    }

    /**
	* Metodo que monta os filtros da listagem de trocas
	*
	* @return null
	*/
    public function exchangeFilter(){

        $user = $this->Auth->user();

        $useds = new \stdClass();
        $useds->key    = "Utilizado";
        $useds-> value = "1";
        $used[] = $useds;

        $usedn = new \stdClass();
        $usedn->key    = "N&atilde;o Utilizado";
        $usedn-> value = "0";
        $used[] = $usedn;


        $this->Filter->addFilter("Utilizado","CB_CHANGE_SEARCH_USED","combo",$used);
        $this->Filter->addFilter("Nome","TXT_CHANGE_SEARCH_DATE","date");

        //se estiver no sistema administrativo adiciona a loja na filtragem
        if($user['role']=="admin"){
            $pops = array();
            $tblStore = TableRegistry::get('SysLoja');
            $stores = $tblStore->find()->select(['IDLOJA','NOME'])->order(['NOME' => 'ASC']);
            foreach($stores as $store){
                $opt = new \stdClass();
                $opt->key = $store->NOME;
                $opt->value = $store->IDLOJA;
                $pops[] = $opt;
            }
            $this->Filter->addFilter('Loja',"CB_CHANGE_SEARCH_STORE","combo",$pops);
        }

        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    /**
	* Metodo principal de exibicao da listagem de trocas
	*
	* @return null
	*/
    public function exchange(){
        $user = $this->Auth->user();

        $this->set('title',"Listar Trocas");

        //se for usuario nao realizarah filtragem de trocas
        //ele apenas terah acesso as troca do momento
        //isso evita excesso de informacoes ao usuario
        //e evita malandragem
        if($user['role']!="user"){
            $this->set('url_filter','/retail/exchange_filter');
        }
        $this->set('url_data','/retail/exchange_data');
    }

    /**
	* Metodo que realiza a carga de dados da listagem
	*
	* @return null
	*/
    public function exchangeData(){
        $user = $this->Auth->user();
        $tblDevolve = TableRegistry::get('LojDevolucao');


        //se o usuario nao for administrador
       	//buscarah apenas a lojas que o usuario faz parte
        if($user['role']!="admin"){
            $query = $tblDevolve->find()
            ->select(['IDDEVOLUCAO','DATA_DEVOLUCAO','UTILIZADO','VALOR_TOTAL'])
            ->where(['IDLOJA' => $user['storeid']]);

            //filtros do gerente
            if($this->request->getData("TXT_CHANGE_SEARCH_DATE")!=""){
                $time = new Time();
                $query->where(function ($exp,$q){
                    return $exp->eq('DATE(DATA_DEVOLUCAO)', substr($this->request->getData("TXT_CHANGE_SEARCH_DATE"), 6,4)."-".substr($this->request->getData("TXT_SALE_SEARCH_DATE"),3,2)."-".substr($this->request->getData("TXT_SALE_SEARCH_DATE"),0,2) );
                });
            }
            if($this->request->getData("CB_CHANGE_SEARCH_USED")!=""){
                $query->where(['UTILIZADO' => $this->request->getData("CB_CHANGE_SEARCH_USED")]);
            }

            //se for usuario lista apenas as trocas do dia
            if($user['role']=="user"){
                $query->where(['DATE(DATA_DEVOLUCAO)' => date("Y-m-d")]);
                $query->where(['UTILIZADO' => 1]);
            }
            $this->set('IS_ADMIN',false);
        }else{
            $query = $tblDevolve->find()
            ->select(['LOJA'=>'L.NOME','IDDEVOLUCAO','DATA_DEVOLUCAO','UTILIZADO','VALOR_TOTAL'])
            ->join([
                'table' => 'sys_loja',
                'type'  => 'INNER',
                'alias' => 'L',
                'conditions' => 'L.IDLOJA=LojDevolucao.IDLOJA'
            ]);

            //filtros do administrador
            if($this->request->getData("TXT_CHANGE_SEARCH_DATE")!=""){
                $query->where(function ($exp,$q){
                    return $exp->eq('DATE(DATA_DEVOLUCAO)', substr($this->request->getData("TXT_CHANGE_SEARCH_DATE"), 6,4)."-".substr($this->request->getData("TXT_SALE_SEARCH_DATE"),3,2)."-".substr($this->request->getData("TXT_SALE_SEARCH_DATE"),0,2) );
                });
            }
            if($this->request->getData("CB_CHANGE_SEARCH_STORE")!=""){
                $query->where(['L.DILOJA' => $this->request->getData("CB_CHANGE_SEARCH_STORE")]);
            }
            if($this->request->getData("CB_CHANGE_SEARCH_USED")!=""){
                $query->where(['UTILIZADO' => $this->request->getData("CB_CHANGE_SEARCH_USED")]);
            }
            $this->set("IS_ADMIN",true);
        }

        $query->order(['DATA_DEVOLUCAO' => 'DESC']);

        $this->set('data_list',$this->paginate($query,['limit' => 10]));
    }

	/**
	* Metodo que retorna os itens de uma devolucao
	*
	* @return null
	*/
    public function exchangeItens(){
        $user = $this->Auth->user();
        $tblChange = TableRegistry::get('LojItemTroca');

        $this->set('data_list',$tblChange->find()->where(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']]));
    }

	/**
	* Metodo que calcula o valor total de uma devolucao
	*
	* @return null
	*/
    public function exchangeTotals(){
        $user = $this->Auth->user();
        $tblChange = TableRegistry::get('LojItemTroca')->find()->select(['TOTAL' => 'PRECO_UNITARIO*QUANTIDADE'])->where(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']]);

        $subtotal = $tblChange->sumOf("TOTAL");

        $this->set('SUBTOTAL',$subtotal);
    }

	/**
	* Metodo que adiciona um novo item na devolucao
	*
	* @return boolean
	*/
    public function exchangeItemAdd(){
        $user = $this->Auth->user();
        $tblProd = TableRegistry::get('SysProduto');
        $tblItem = TableRegistry::get('LojItemTroca');

        $CODIGO_BARRA = $this->request->getData("PRODUTO");
        if(strlen($CODIGO_BARRA)==14){
            $CODIGO_BARRA = substr($CODIGO_BARRA,1,14);
        }

        $prod = $tblProd->find()->where(['CODIGO_BARRA' => $CODIGO_BARRA])->first();

        $item_troca = $tblItem->newEntity();
        $item_troca->IDLOJA         = $user['storeid'];
        $item_troca->IDUSUARIO      = $user['id'];
        $item_troca->IDPRODUTO      = $prod->IDPRODUTO;
        $item_troca->QUANTIDADE     = $this->request->getData("QUANTIDADE");
        $item_troca->PRECO_UNITARIO = $prod->PRECO_VENDA;
        $item_troca->NOME_PRODUTO   = $prod->NOME;

        return $this->response->withStringBody( $tblItem->save($item_troca)?true:false );
    }

    /**
	* Metodo que exclui um item da devolucao
	*
	* @return boolean
	*/
    public function exchangeItemDel(){
        $user = $this->Auth->user();
        $tblChange = TableRegistry::get('LojItemTroca');
        $item = $tblChange->get(['IDLOJA' => $user['storeid'],'IDPRODUTO' => $this->request->getData("IDPRODUTO"),'IDUSUARIO' => $user['id']]);
        return $this->response->withStringBody( $tblChange->delete($item)?true:false ) ;
    }

	/**
	* Metodo que limpa os itens de uma devolucao
	* @param boolean $_print verifica se imprime o resultado ou retorna
	*
	* @return bolean
	*/
    public function exchangeItensClear($_print = true){
        $user = $this->Auth->user();
        $tblItem = TableRegistry::get('LojItemTroca');
        if($_print){
            return $this->response->withStringBody( $tblItem->deleteAll(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']])?true:false );
        }else{
            return $tblItem->deleteAll(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']])?true:false;
        }
    }

	/**
	* Metodo que executa uma troca no sistema
	* @param string $_key chave da nota fiscal eletronica, necessario para devolucoes atraves de nota
	*
	* @return int or false
	*/
    public function exchangeExecute($_key = null){
		$retorno = false;

        $user = $this->Auth->user();
        $tblChange   = TableRegistry::get('LojDevolucao');
        $tblChangeIt = TableRegistry::get('LojDevolucaoProduto');
        $tblItem     = TableRegistry::get('LojItemTroca');
        $tblProd     = TableRegistry::get('SysProduto');
        $tblProdIt   = TableRegistry::get('SysProdutoItem');
        $tblStock    = TableRegistry::get('LojEstoque');
        $tblMove     = TableRegistry::get('LojMovimentoEstoque');

        $devolve = $tblChange->newEntity();
        $devolve->IDLOJA = $user['storeid'];
        $devolve->DATA_DEVOLUCAO = date("Y-m-d H:i:s");
        $devolve->CHAVE_NFCE = $_key;
        $devolve->UTILIZADO = 0;
        $devolve->VALOR_TOTAL = $this->request->getData("VALOR_TOTAL");

        //salva as informacoes da devolucao
        if($tblChange->save($devolve)){
            foreach($tblItem->find()->where(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']]) as $item){
                $item_devolvido = $tblChangeIt->newEntity();
                $item_devolvido->IDDEVOLUCAO = $devolve->IDDEVOLUCAO;
                $item_devolvido->IDPRODUTO   = $item->IDPRODUTO;
                $item_devolvido->IDLOJA      = $devolve->IDLOJA;
                $item_devolvido->PRECO_UNITARIO = $item->PRECO_UNITARIO;
                $tblChangeIt->save($item_devolvido);

                $produto = $tblProd->get($item->IDPRODUTO);

                //realizando movimentacao de estoque
                //verifica se o produto eh composto, se for realiza a inclusao dos itens
                if($produto->ESTRUTURA=="S"){
                    //realizar aqui a baixa da quantidade do estoque para produtos simples
                    $estoque = $tblStock->get(['IDLOJA' => $devolve->IDLOJA,'IDPRODUTO' => $item->IDPRODUTO]);
                    $estoque->QUANTIDADE       += $item->QUANTIDADE;
                    $tblStock->save($estoque);
                    //realia aqui o registro de movimentacao do estoque
                    $movimento = $tblMove->newEntity();
                    $movimento->IDLOJA         = $devolve->IDLOJA;
                    $movimento->DATA_MOVIMENTO = date("Y-m-d H:i:s");
                    $movimento->QUANTIDADE     = $item->QUANTIDADE;
                    $movimento->TIPO_OPERACAO  = 'D';
                    $movimento->OPERACAO       = '+';
                    $movimento->PRECO_CUSTO    = $produto->PRECO_COMPRA;
                    $movimento->NOME_PRODUTO   = $produto->NOME;
                    $movimento->SKU_PRODUTO    = $produto->SKU;
                    $movimento->IDPRODUTO      = $item->IDPRODUTO;
                    $tblMove->save($movimento);
                }else{
                    foreach($tblProdIt->find()->where(['IDPRODUTO' => $item->IDPRODUTO]) as $prd_item){
                        //busca as informacoes do produto filho
                        $PRODUTO = $tblProd->get($prd_item->IDPRODUTO_FILHO);

                        //realizar aqui a baixa da quantidade no estoque de cada produto filho do produto composto
                        $estoque = $tblStock->get(['IDLOJA' => $devolve->IDLOJA,'IDPRODUTO' => $prd_item->IDPRODUTO_FILHO]);
                        $estoque->QUANTIDADE       += $item->QUANTIDADE;
                        $tblStock->save($estoque);

                        //realia aqui o registro de movimentacao de estoque de cada item filho do produto composto
                        $movimento = $tblMove->newEntity();
                        $movimento->IDLOJA         = $devolve->IDLOJA;
                        $movimento->DATA_MOVIMENTO = date("Y-m-d H:i:s");
                        $movimento->QUANTIDADE     = $item->QUANTIDADE;
                        $movimento->TIPO_OPERACAO  = 'D';
                        $movimento->OPERACAO       = '+';
                        $movimento->PRECO_CUSTO    = $PRODUTO->PRECO_COMPRA;
                        $movimento->NOME_PRODUTO   = $PRODUTO->NOME;
                        $movimento->SKU_PRODUTO    = $PRODUTO->SKU;
                        $movimento->IDPRODUTO      = $prd_item->IDPRODUTO_FILHO;
                        $tblMove->save($movimento);
                    }
                }
            }

            //limpa os itens ja salvos
            $this->changeItensClear(false);

            $retorno = $devolve->IDDEVOLUCAO;
        }

        return $this->request->withStringBody($retorno);
    }

    /**
	* Metodo que desfaz uma devolucao
	*
	* @return boolean
	*/
    public function exchangeReverse(){
    	$retorno   = false;
        $tblDev    = TableRegistry::get('LojDevolucao');
        $tblDevIt  = TableRegistry::get('LojDevolucaoProduto');
        $tblMove   = TableRegistry::get('LojMovimentoEstoque');
        $tblStk    = TableRegistry::get('LojEstoque');
        $devolucao = $tblDev->get($this->request->getData("IDDEVOLUCAO"));

        //varre os itens de uma devolucao
        foreach($tblDevIt->find()->where(['IDDEVOLUCAO' => $devolucao->IDDEVOLUCAO]) as $item){
            //busca as informacoes do produto
            $produto = TableRegistry::get('SysProduto')->get($item->IDPRODUTO);

            if($produto->ESTRUTURA=='C'){
                foreach(TableRegistry::get('SysProdutoItem')->find()->where(['IDPRODUTO' => $produto->IDPRODUTO]) as $pitem){
                    //busca o estoque da loja que fez a venda para o produto especifico
                    $stock = $tblStk->get(['IDLOJA' => $devolucao->IDLOJA,'IDPRODUTO' => $pitem->IDPRODUTO_FILHO]);
                    $produto_filho = TableRegistry::get('SysProduto')->get($pitem->IDPRODUTO_FILHO);

                    //retira novamente do estoque o produto que foi adicionado
                    $stock->QUANTIDADE-= $item->QUANTIDADE;
                    $tblStk->save($stock);

                    //realiza aqui o registro de movimentacao do estoque
                    $movimento = $tblMove->newEntity();
                    $movimento->IDLOJA         = $devolucao->IDLOJA;
                    $movimento->DATA_MOVIMENTO = date("Y-m-d H:i:s");
                    $movimento->QUANTIDADE     = $pitem->QUANTIDADE;
                    $movimento->TIPO_OPERACAO  = 'E';
                    $movimento->OPERACAO       = '-';
                    $movimento->PRECO_CUSTO    = $produto_filho->PRECO_COMPRA;
                    $movimento->NOME_PRODUTO   = $produto_filho->NOME;
                    $movimento->SKU_PRODUTO    = $produto_filho->SKU;
                    $movimento->IDPRODUTO      = $produto_filho->IDPRODUTO;
                    $tblMove->save($movimento);
                }
            }else{
                //busca o estoque da loja que fez a venda para o produto especifico
                $stock = $tblStk->get(['IDLOJA' => $devolucao->IDLOJA,'IDPRODUTO' => $item->IDPRODUTO]);

                //retira novamente do estoque o produto que foi adicionado
                $stock->QUANTIDADE -= $item->QUANTIDADE;
                $tblStk->save($stock);

                //realiza aqui o registro de movimentacao do estoque
                $movimento = $tblMove->newEntity();
                $movimento->IDLOJA         = $devolucao->IDLOJA;
                $movimento->DATA_MOVIMENTO = date("Y-m-d H:i:s");
                $movimento->QUANTIDADE     = $item->QUANTIDADE;
                $movimento->TIPO_OPERACAO  = 'E';
                $movimento->OPERACAO       = '-';
                $movimento->PRECO_CUSTO    = $produto->PRECO_COMPRA;
                $movimento->NOME_PRODUTO   = $produto->NOME;
                $movimento->SKU_PRODUTO    = $produto->SKU;
                $movimento->IDPRODUTO      = $produto->IDPRODUTO;
                $tblMove->save($movimento);

                //grava as informacoes do movimento de estoque
                $this->Estoque->DATA_MOVIMENTO = date("Y-m-d H:i:s");
                $this->Estoque->TIPO_OPERACAO  = 'E';
                $this->Estoque->OPERACAO = '-';
                $this->Estoque->PRECO_CUSTO  = $this->Produto->PRECO_COMPRA;
                $this->Estoque->NOME_PRODUTO = $this->Produto->NOME;
                $this->Estoque->SKU_PRODUTO  = $this->Produto->SKU;
                $this->Estoque->save_move();
            }
        }
        //apaga a devolucao do banco de dados
        //echo $tblItem->deleteAll(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']])?true:false;
        if($tblDev->deleteAll(['IDDEVOLUCAO' => $devolucao->IDDEVOLUCAO])){
            $retorno = $tblDevIt->deleteAll(['IDDEVOLUCAO' => $devolucao->IDDEVOLUCAO])?true:false;
        }

        return $this->request->withStringBody( $retorno );
    }

    /**
	* Metodo que exibe uma devolucao
	* @param int $_idDevolucao codigo da devolucao
	*
	* @return null
	*/
    public function exchangeShow($_idDevolucao){
        $this->viewBuilder()->layout('clear');

        $dev = TableRegistry::get('LojDevolucao')->get($_idDevolucao);
        $dev_itens = null;
        foreach(TableRegistry::get('LojDevolucaoProduto')->find()->where(['IDDEVOLUCAO' => $_idDevolucao]) as $it){
            $item = new \stdClass();
            $item->IDPRODUTO    = $it->IDPRODUTO;
            $item->NOME_PRODUTO = TableRegistry::get('SysProduto')->get($it->IDPRODUTO)->NOME;
            $item->QUANTIDADE   = $it->QUANTIDADE;
            $item->PRECO_UNITARIO = $it->PRECO_UNITARIO;
            $dev_itens[] = $item;
        }

        $this->set('devolucao',$dev);
        $this->set('devolucao_itens',$dev_itens);
        $this->set('loja',TableRegistry::get('SysLoja')->get($dev->IDLOJA));
    }

    /****************************CLIENTES******************************/
    /**
	* Metodo que exibe os filtros da listagem de clientes
	*
	* @return html
	*/
    public function customerFilter(){

        $links = array();
        for($i=65;$i<91;$i++){
			$lnk = new \stdClass();
			$lnk->text = \chr($i);
			$lnk->data = \chr($i);
			$links[] = $lnk;
		}

        $this->Filter->addLinkFilter($links);

        $this->Filter->addFilter("Nome","TXT_CUSTOMER_SEARCH_NAME","text");
        $this->Filter->addFilter("CPF","TXT_CUSTOMER_SEARCH_TAXVAT","text");
        $this->Filter->addFilter("E-mail","TXT_CUSTOMER_SEARCH_MAIL","text");
        $this->Filter->addFilter("Telefone","TXT_CUSTOMER_SEARCH_PHONE","text");

        $ops = array();

        $opts = new \stdClass();
        $opts->key  = "Masculino";
        $opts->value = "1";
        $ops[] = $opts;

        $optn = new \stdClass();
        $optn->key  = "Feminino";
        $optn->value = "2";
        $ops[] = $optn;

        $this->Filter->addFilter("Sexo","CB_CUSTOMER_SEARCH_GENDER","combo",$ops);

        $this->Filter->addFilter("Sem Importa&ccedil;&atilde;o das informa&ccedil;&otilde;es de Bairro e Cidade","CHK_CUSTOMER_SEARCH_NOCITY","check","1");

        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    /**
	* Metodo que exibe a tela inicial inicial da listagem de clientes
	*
	* @return null
	*/
    public function customer(){

    	$this->set('user',$this->Auth->user());

    	$this->set('title',"Clientes");

        $this->set('url_filter','/retail/customer_filter');
        $this->set('url_data','/retail/customer_data');
    }

    /**
	* Metodo que busca os dados do cliente
	*
	* @return null
	*/
    public function customerData(){
        $tblCustomer = TableRegistry::get('SysCliente');

        $this->set('user',$this->Auth->user());

        $query = $tblCustomer->find();
        $query->select(['IDCLIENTE','NOME','TELEFONE','EMAIL','CPF','CODIBGE','CEP']);

        if($this->request->getData("TXT_CUSTOMER_SEARCH_NAME")!=""){
            $query->where(array("MATCH(NOME) AGAINST('+".str_replace(' '," +",$this->request->getData('TXT_CUSTOMER_SEARCH_NAME'))."' IN BOOLEAN MODE)"));
        }
        if($this->request->getData("TXT_CUSTOMER_SEARCH_TAXVAT")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('CPF','%'.$this->request->getData('TXT_CUSTOMER_SEARCH_TAXVAT').'%');
            });
        }
        if($this->request->getData("TXT_CUSTOMER_SEARCH_MAIL")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('EMAIL','%'.$this->request->getData('TXT_CUSTOMER_SEARCH_MAIL').'%');
            });
        }
        if($this->request->getData("TXT_CUSTOMER_SEARCH_PHONE")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('TELEFONE','%'.$this->request->getData("TXT_CUSTOMER_SEARCH_PHONE").'%');
            });
        }
        if($this->request->getData("CB_CUSTOMER_SEARCH_GENDER")!=""){
            $query->where(['GENERO' => $this->request->getData('CB_CUSTOMER_SEARCH_GENDER')]);
        }
        if($this->request->getData("CHK_CUSTOMER_SEARCH_NOCITY")=="1"){
            $query->where(function($exp,$q){
                return $exp->isNull('CODIBGE');
            });
            $query->where(function($exp,$q){
                return $exp->notEq('CEP','');
            });
        }
        if($this->request->getData("LNK_DATA")!=""){
			$query->where(function($exp){
				return $exp->like('NOME',$this->request->getData("LNK_DATA").'%');
			});
		}

        $this->set('data_list',$this->paginate($query,['limit' => 10]));
    }

    /**
	* Metodo que exibe a tela para cadastro ou edicao de cliente
	* @param int $_idCliente Codigo do cliente (opcional)
	*
	* @return null
	*/
    public function customerCreate($_idCliente=""){
        $this->set('is_mobile', $this->request->is('mobile'));

        if($_idCliente!=""){
            $cliente = TableRegistry::get('SysCliente')->get($_idCliente);
            $this->set('cliente',$cliente);
        }
    }

    /**
	* Metodo que salva as informacoes de um cliente
	* @param boolean $_ispos flag que indica se a operacao estah sendo
	* realizada na tela de vendas
	*
	* @return boolean
	*/
    public function customerSave($_ispos=false){
        $tblCli = TableRegistry::get("SysCliente");

        $return = new \stdClass();
        $return->IDCLIENTE = 0;
        $return->NOME = "";

        //verifica se jah existe ou seh eh novo
        if($this->request->getData("IDCLIENTE")==""){
            $cliente = $tblCli->newEntity();
        }else{
            $cliente = $tblCli->get($this->request->getData("IDCLIENTE"));
        }

        //limpa o CPF
        $newcpf = $this->request->getData("CPF");
        $newcpf = str_replace(".", "", $newcpf);
        $newcpf = str_replace("-", "", $newcpf);

        $time = new Time();

        //formata a data de nascimento
		$cliente->NASCIMENTO = $time->parseDate($this->request->getData("NASCIMENTO"))->i18nFormat("yyyy-MM-dd");
		$cliente->GENERO     = $this->request->getData("GENERO");
		$cliente->CPF        = $newcpf;
	    $cliente->CEP        = $this->request->getData("CEP");
		$cliente->TELEFONE   = $this->request->getData("TELEFONE");
		$cliente->TELEFONE2  = $this->request->getData("TELEFONE2");
		$cliente->NOME       = mb_strtoupper($this->request->getData("NOME"));
		$cliente->EMAIL      = mb_strtolower($this->request->getData("EMAIL"));
        $cliente->DATA_CADASTRO = $this->request->getData("DATA_CADASTRO");

        //verifica se o CPF eh valido
        if(!$this->customerHasValidTaxvat($newcpf)){
            $return->IDCLIENTE = -1;
            $return->NOME      = "CPF Inv&aacute;lido, por favor verifique!";
        }else{
        	//vefifica se o cadastro jah existe atraves do CPF
            if($tblCli->find()->where(['CPF' => $newcpf])->count()>0){
                $cli = $tblCli->find()->select(['IDCLIENTE','DATA_CADASTRO'])->where(['CPF' => $newcpf])->first();
                $cliente->IDCLIENTE = $cli->IDCLIENTE;
                $cliente->DATA_CADASTRO = $cli->DATA_CADASTRO;
                //se nao tiver uma data de cadastro define uma
                if($cliente->DATA_CADASTRO=="0000-00-00" || $cliente->DATA_CADASTRO==NULL){
                    $cliente->DATA_CADASTRO = date("Y-m-d");
                }

                //salva as informacoes do cliente e prepara o retorno
                if($tblCli->save($cliente)){
                    $return->IDCLIENTE = $cliente->IDCLIENTE;
                    $return->NOME      = $cliente->NOME;
                }
            }elseif($tblCli->find()->where(['EMAIL' => $cliente->EMAIL])->count()>0){
            	//verifica se jah existe o cadastro atraves do email
                $cli = $tblCli->find()->select(['IDCLIENTE','DATA_CADASTRO'])->where(['EMAIL' => $cliente->EMAIL])->first();
                $cliente->IDCLIENTE = $cli->IDCLIENTE;
                $cliente->DATA_CADASTRO = $cli->DATA_CADASTRO;
                //se nao tiver data de cadastro define uma
                if($cliente->DATA_CADASTRO=="0000-00-00" || $cliente->DATA_CADASTRO==NULL){
                    $cliente->DATA_CADASTRO = date("Y-m-d");
                }

                if($tblCli->save($cliente)){
                    $return->IDCLIENTE = $cliente->IDCLIENTE;
                    $return->NOME      = $cliente->NOME;
                }
            }else{//NAO ENCONTRADO CLIENTE NEM POR E-MAIL, NEM POR CPF
                if($tblCli->save($cliente)){
                    $return->IDCLIENTE = $cliente->IDCLIENTE;
                    $return->NOME      = $cliente->NOME;
                }else{
                    $return->NOME      = "Ocorreu um erro ao tentar salvar o cliente!";
                    $return->IDCLIENTE = 0;
                }
            }
        }

        if($_ispos){
			return $this->response->withStringBody( json_encode($return) );
		}else{
			return ($return->IDCLIENTE>0)?true:false;
		}
    }

    /**
	* Metodo que verifica o numero do CPF do cliente
	* @param string $cpf CPF
	*
	* @return boolean
	*/
    private function customerHasValidTaxvat($cpf){
        // Verifica se um número foi informado
	    if(empty($cpf)) {
	        return false;
	    }

	    // Elimina possivel mascara
	    $cpf = preg_replace('[^0-9]', '', $cpf);
	    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

	    // Verifica se o numero de digitos informados é igual a 11
	    if (strlen($cpf) != 11) {
	        return false;
	    }
	    // Verifica se nenhuma das sequências invalidas abaixo
	    // foi digitada. Caso afirmativo, retorna falso
	    else if ($cpf == '00000000000' ||
	        $cpf == '11111111111' ||
	        $cpf == '22222222222' ||
	        $cpf == '33333333333' ||
	        $cpf == '44444444444' ||
	        $cpf == '55555555555' ||
	        $cpf == '66666666666' ||
	        $cpf == '77777777777' ||
	        $cpf == '88888888888' ||
	        $cpf == '99999999999') {
	        return false;
	     // Calcula os digitos verificadores para verificar se o
	     // CPF é válido
	     } else {

	        for ($t = 9; $t < 11; $t++) {

	            for ($d = 0, $c = 0; $c < $t; $c++) {
	                $d += $cpf{$c} * (($t + 1) - $c);
	            }
	            $d = ((10 * $d) % 11) % 10;
	            if ($cpf{$c} != $d) {
	                return false;
	            }
	        }

	        return true;
	    }
    }

    /**
	* Metodo que retorna as informacoes de um determinado cliente
	*
	* @return Object com os dados do cliente
	*/
    public function customerGetInfo(){

        $cliente = TableRegistry::get('SysCliente')->get($this->request->getData("IDCLIENTE"));

        $cli = new \stdClass();
        $cli->NOME = $cliente->NOME;
        $cli->IDCLIENTE  = $cliente->IDCLIENTE;
        $cli->NASCIMENTO = $cliente->NASCIMENTO;
        $cli->GENERO     = $cliente->GENERO; // 1 = Masculino, 2 = Feminino
        $cli->CPF        =  $cliente->CPF;
        $cli->CEP        = $cliente->CEP;
        $cli->TELEFONE   = $cliente->TELEFONE;
        $cli->TELEFONE2  = $cliente->TELEFONE2;
        $cli->EMAIL      = $cliente->EMAIL;
        $cli->DATA_CADASTRO = $cliente->DATA_CADASTRO;

        if($cli->CPF=="" || $cli->NASCIMENTO=="" || $cli->EMAIL=="" || $cli->TELEFONE=="" || $cli->CEP==""){
            $cli->STATUS_CADASTRO = 0;
        }
        else{
            $dtatual = new \Datetime(date("Y-m-d"));
            $dtnasc = new \Datetime($cli->NASCIMENTO->format("Y-m-d"));

            if($dtnasc->diff($dtatual)->format("%Y")>100 || $dtnasc->diff($dtatual)->format("%Y")<18){
                $cli->STATUS_CADASTRO = 0;
            }else{
                $cli->STATUS_CADASTRO = 1;
            }
        }

        return $this->response->withStringBody( json_encode($cli) );
    }

    /**
	* Metodo que retorna o resultado da busca em um dialog de clientes
	*
	* @return null
	*/
    public function customerDialog(){
        $query = TableRegistry::get('SysCliente')->find()->select(['IDCLIENTE','CPF','NOME','TELEFONE']);

		if($this->request->getData("TXT_CUSTOMER_SEARCH_ID")!=""){
			$query->where(['IDCLIENTE' => $this->request->getData("TXT_CUSTOMER_SEARCH_ID")]);
		}

        if($this->request->getData("TXT_CUSTOMER_SEARCH_NAME")!=""){
            $query->where(array("MATCH(NOME) AGAINST('+".str_replace(' '," +",$this->request->getData('TXT_CUSTOMER_SEARCH_NAME'))."' IN BOOLEAN MODE)"));
        }

        if($this->request->getData("TXT_CUSTOMER_SEARCH_TAXVAT")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('CPF','%'.$this->request->getData("TXT_CUSTOMER_SEARCH_TAXVAT").'%');
            });
        }

        if($this->request->getData("TXT_CUSTOMER_SEARCH_PHONE")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('TELEFONE','%'.$this->request->getData("TXT_CUSTOMER_SEARCH_PHONE").'%');
            })
                    ->orWhere(function ($exp,$q){
                return $exp->like('TELEFONE2','%'.$this->request->getData("TXT_CUSTOMER_SEARCH_PHONE").'%');
            });
        }

        $this->set('data_list',$query);
    }

    /**
	* Metodo que importa as informacoes de endereco de um cliente
	*
	* @return boolean or -1
	*/
    public function customerImportOrigin(){
    	$retorno = false;

        $tblCli = TableRegistry::get('SysCliente');
        $tblOpt = TableRegistry::get('SysOpcao');

        $cliente = $tblCli->get($this->request->getData("IDCLIENTE"));

        $url = str_replace("{{ZIP_CODE}}", str_replace("-","",$cliente->CEP) ,$tblOpt->get("URL_ZIPCODE")->OPCAO_VALOR );
        $result = $this->file_get_contents_curl($url);
        $obj = json_decode($result);

        if(isset($obj->cod_ibge)){
        	if($obj->resultado_txt!="CEP invalido"){
				$query = $tblCli->query();
	            $query->update()
	                ->set(['BAIRRO' => ((isset($obj->bairro))?$obj->bairro:"Centro"),'CODIBGE' => $obj->cod_ibge])
	                ->where(['IDCLIENTE' => $cliente->IDCLIENTE])
	                ->execute();
	            $retorno = true;
			}
            else{
				$retorno = -1;
			}
        }

        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que exporta as informacoes basicas do cliente para um arquivo csv
	*
	* @return Response
	*/
    public function customerExportMail(){
        $this->autoRender = false;

        $tblCli = TableRegistry::get('SysCliente');
        $clientes = $tblCli->find()->select(['FNOME' => 'substring_index(NOME," ",1)','LNAME' => 'REVERSE(substring_index(REVERSE(NOME), " ", 1))','EMAIL'])
            ->where(function($exp,$q){
                return $exp->notEq('EMAIL','');
            })->order(['EMAIL' => 'ASC']);

        $idx  = 1;
        $data = "Id Cliente,First Name,Last Name,Email Address\r\n";
        foreach($clientes as $cliente){
            if(filter_var($cliente->EMAIL, FILTER_VALIDATE_EMAIL)){
                $data.= $idx.",".$cliente->FNOME.",".$cliente->LNAME.",".$cliente->EMAIL."\r\n";
                $idx++;
            }
        }

        $this->response->body($data);
        $this->response->type("csv");
        $this->response->download("emails-".date("dmY").".csv");

        return $this->response;
    }

    /**
	* Metodo que busca as informacoes de compra de um determinado cliente
	* @param int $_idCliente Codigo do cliente
	*
	* @return null
	*/
    public function customerBuy($_idCliente){
        $this->viewBuilder()->layout("gallery");

        $buys = TableRegistry::get('LojVenda')->find()
            ->select(['IDVENDA','DATA_VENDA','SUBTOTAL','DESCONTO','VALOR_PAGO'])
            ->where(['IDCLIENTE' => $_idCliente]);
        $sales = $this->Paginator->paginate($buys,['limit' => 10]);
        $this->set('data_buy',$sales);

        $products = array();
        foreach($sales as $sale){
            foreach(TableRegistry::get('LojVendaProduto')->find()
                ->select(['IDVENDA','SKU_PRODUTO','NOME_PRODUTO','QUANTIDADE','PRECO_UNITARIO'])
                ->where(['IDVENDA' => $sale->IDVENDA]) as $product){
                    $prd = new \stdClass();
                    $prd->IDVENDA      = $product->IDVENDA;
                    $prd->SKU_PRODUTO  = $product->SKU_PRODUTO;
                    $prd->NOME_PRODUTO = $product->NOME_PRODUTO;
                    $prd->QUANTIDADE   = $product->QUANTIDADE;
                    $prd->PRECO_UNITARIO = $product->PRECO_UNITARIO;
                    $prd->UNIDADE_MEDIDA = $product->UNIDADE_MEDIDA;

                    $products[] = $prd;
                }
        }
        $this->set('data_item',$products);
    }

    /**
	* Metodo que veficia se um cliente possui desconto por indicacao
	*
	* @return float
	*/
    public function customerGetDiscount(){

		$cliente_indicacao = TableRegistry::get('SysClienteIndicacao')->find()
			->where(['INDICADOR' => $this->request->getData("IDCLIENTE"),'STATUS' => '0'])
			->where(function($exp){
				return $exp->gte('DATE(VALIDO_ATE)',date("Y-m-d"));
			});
		return $this->response->withStringBody( $cliente_indicacao->sumOf('VALOR_DESCONTO') );
	}

    /**************************SOLICITAÇÕES***************************/
    /**
	* Metodo que exibe os filtros da tela de solicitacoes do cliente
	*
	* @return html
	*/
    public function requestFilter(){

        $this->Filter->addFilter("Cliente","TXT_REQUEST_SEARCH_CUSTOMER","text");
        $this->Filter->addFilter("Desejo","TXT_REQUEST_SEARCH_WISH","text");

        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    /**
	* Metodo que exibe a tela principal de solicitacoes dos clientes
	*
	* @return null
	*/
    public function requestList(){
        $this->set('url_filter','retail/request_filter');
        $this->set('url_data','retail/request_list_data');
        $this->set('template',TableRegistry::get('SysOpcao')->get('TEMPLATE_REQUEST')->OPCAO_VALOR);
    }

    /**
	* Metodo que busca as solicitacoes de clientes
	* @param int $_idLoja Codigo da loja onde foi realizado o pedido (opcional)
	*
	* @return
	*/
    public function requestListData($_idLoja = ""){
        $tblRequest = TableRegistry::get('LojSolicitacaoCliente');

        $query = $tblRequest->find();
        $query->select(['IDSOLICITACAO','C.IDCLIENTE','C.NOME','C.EMAIL','C.TELEFONE','DATA_SOLICITACAO','DESEJO','FORMA_CONTATO','ATENDIDO'])
            ->join([
                'table' => 'sys_cliente',
                'type'  => 'INNER',
                'alias' => 'C',
                'conditions' => 'C.IDCLIENTE=LojSolicitacaoCliente.IDCLIENTE'
            ]);

        if($_idLoja!=""){
            $query->where(['IDLOJA' => $_idLoja]);
        }

        if($this->request->getData("TXT_REQUEST_SEARCH_CUSTOMER")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('C.NOME','%'.$this->request->getData('TXT_REQUEST_SEARCH_CUSTOMER').'%');
            });
        }
        if($this->request->getData("TXT_REQUEST_SEARCH_WISH")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('DESEJO','%'.$this->request->getData('TXT_REQUEST_SEARCH_WISH').'%');
            });
        }

        $query->order(['ATENDIDO' => 'ASC','DATA_SOLICITACAO' => 'ASC']);
        $this->Paginator->paginate($query,['limit' => 10]);

        $this->set('data_list',$this->paginate($query));
    }

	/**
	* Metodo que exibe a tela para criacao ou edicao de uma solicitacao
	* @param int $_idSolicitacao codigo da solicitacao
	*
	* @return null
	*/
    public function requestCreate($_idSolicitacao=""){
        if($_idSolicitacao!=""){
            $tblSol   = TableRegistry::get('LojSolicitacaoCliente');
            $solicita = $tblSol->get($_idSolicitacao);
            $this->set('solicitacao',$solicita);

            $this->set('cliente',TableRegistry::get('SysCliente')->get($solicita->IDCLIENTE));
        }
    }

	/**
	* Metodo que salva os dados de um solicitacao
	*
	* @return
	*/
    public function requestDataSave(){

        $user = $this->Auth->user();
        $tblSol = TableRegistry::get('LojSolicitacaoCliente');

        if($this->request->getData("IDSOLICITACAO")==""){
            $solicitacao = $tblSol->newEntity();
        }else{
            $solicitacao = $tblSol->get($this->request->getData("IDSOLICITACAO"));
        }

        $time = new Time();

        $solicitacao->IDCLIENTE        = $this->request->getData("IDCLIENTE");
        $solicitacao->DATA_SOLICITACAO = $time->parseDate($this->request->getData("DATA_SOLICITACAO"))->i18nFormat("yyyy-MM-dd");
        $solicitacao->DESEJO           = mb_strtoupper($this->request->getData("DESEJO"));
        $solicitacao->IDLOJA           = $user['storeid'];
        $solicitacao->FORMA_CONTATO    = $this->request->getData("FORMA_CONTATO");

        if($solicitacao->IDLOJA==""){
            $solicitacao->IDLOJA = TableRegistry::get('SysOpcao')->get("DEFAULT_STORE")->OPCAO_VALOR;
        }
        return $this->response->withStringBody( $tblSol->save($solicitacao)?true:false );
    }

    /**
	* Metodo que define uma solicitacao como atendida
	* @param int $_atendido 0 ou 1
	*
	* @return boolean
	*/
    public function requestSetFinished($_atendido){
        $tblSolicita = TableRegistry::get('LojSolicitacaoCliente');

        $retorno = false;
        $solicitacoes = $this->request->getData("check_list");

        for($i=0;$i<count($solicitacoes);$i++){
            $solicita = $tblSolicita->get($solicitacoes[$i]);
            $solicita->ATENDIDO = $_atendido;
            $solicita->ATENDIDO_DATA = date("Y-m-d H:i:s");
            $retorno = $tblSolicita->save($solicita)?true:false;
        }
        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que busca as informacoes do email do cliente da solicitacao
	*
	* @return Objeto conteodo os dados do cliente e o desejo
	*/
    public function requestGetMailInfo(){

        $solicitacao = TableRegistry::get('LojSolicitacaoCliente')->get($this->request->getData("IDSOLICITACAO"));
        $cliente     = TableRegistry::get('SysCliente')->get($solicitacao->IDCLIENTE);

        $data = new \stdClass();
        $data->IDCLIENTE     = $cliente->IDCLIENTE;
        $data->CLIENTE       = $cliente->NOME;
        $data->EMAIL_CLIENTE = $cliente->EMAIL;
        $data->DESEJO        = $solicitacao->DESEJO;

        return $this->response->withStringBody( json_encode($data) );
    }

    /**************************VENDAS***************************/
    /**
	* Metodo que retorna os filtros da listagem de vendas
	*
	* @return html
	*/
    public function saleFilter(){

        $user = $this->Auth->user();
        if($user['role']=="admin"){
            $pops = array();
            $tblStore = TableRegistry::get('SysLoja');
            $stores = $tblStore->find()->select(['IDLOJA','NOME'])->order(['NOME' => 'ASC']);
            foreach($stores as $store){
                $opt = new \stdClass();
                $opt->key = $store->NOME;
                $opt->value = $store->IDLOJA;
                $pops[] = $opt;
            }
            $this->Filter->addFilter('Loja',"CB_SALE_SEARCH_STORE","combo",$pops);
            $this->Filter->addFilter("Data da Venda","TXT_SALE_SEARCH_DATE","date");

            $ords = array();

			$ord1 = new \stdClass();
			$ord1->value   = "L.IDLOJA";
			$ord1->key = "Loja";
			$ords[] = $ord1;

			$ord2 = new \stdClass();
			$ord2->value   = "DATA_VENDA";
			$ord2->key = "Data da Venda";
			$ords[] = $ord2;

			$ord3 = new \stdClass();
			$ord3->value   = "SUBTOTAL";
			$ord3->key = "Subtotal";
			$ords[] = $ord3;

			$ord4 = new \stdClass();
			$ord4->value   = "DESCONTO";
			$ord4->key = "Desconto";
			$ords[] = $ord4;

			$ord5 = new \stdClass();
			$ord5->value   = "VALOR_PAGO";
			$ord5->key = "Valor Pago";
			$ords[] = $ord5;

			$ord6 = new \stdClass();
			$ord6->value   = "TROCO";
			$ord6->key = "Troco";
			$ords[] = $ord6;

			$this->Filter->addOrder($ords);
        }elseif($user['role']=="manager"){
            $this->Filter->addFilter("Data da Venda","TXT_SALE_SEARCH_DATE","date");

            $ords = array();

			$ord2 = new \stdClass();
			$ord2->value   = "DATA_VENDA";
			$ord2->key = "Data da Venda";
			$ords[] = $ord2;

			$ord3 = new \stdClass();
			$ord3->value   = "SUBTOTAL";
			$ord3->key = "Subtotal";
			$ords[] = $ord3;

			$ord4 = new \stdClass();
			$ord4->value   = "DESCONTO";
			$ord4->key = "Desconto";
			$ords[] = $ord4;

			$ord5 = new \stdClass();
			$ord5->value   = "VALOR_PAGO";
			$ord5->key = "Valor Pago";
			$ords[] = $ord5;

			$ord6 = new \stdClass();
			$ord6->value   = "TROCO";
			$ord6->key = "Troco";
			$ords[] = $ord6;

			$this->Filter->addOrder($ords);
        }
        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    /**
	* Metodo que exibe a tela princiapl da listagem de vendas
	*
	* @return null
	*/
    public function sale(){
    	$this->set('title',"Listagem de Vendas");

        $user = $this->Auth->user();
        if($user['role']!="user"){
            $this->set('url_filter','/retail/sale_filter');
        }else{
            $this->set('url_filter',"");
        }

        $this->set('url_data','/retail/sale_data');
    }

    /**
	* Metodo que trata a busca das informacoes de vendas
	*
	* @return null
	*/
    public function saleData(){
        $user = $this->Auth->user();
        $tblSell = TableRegistry::get('LojVenda');
        //busca apenas as vendas de determinada loja
        if($user['role']!="admin"){
            $query = $tblSell->find()
            ->select(['IDVENDA','DATA_VENDA','SUBTOTAL','DESCONTO','VALOR_PAGO','TROCO'])
            ->where(['IDLOJA' => $user['storeid']]);

            //filtros do gerente
            if($this->request->getData("TXT_SALE_SEARCH_DATE")!=""){
                $query->where(['DATE(DATA_VENDA)' => substr($this->request->getData("TXT_SALE_SEARCH_DATE"), 6,4)."-".substr($this->request->getData("TXT_SALE_SEARCH_DATE"),3,2)."-".substr($this->request->getData("TXT_SALE_SEARCH_DATE"),0,2) ]);
            }
            if($user['role']=="user"){
                $query->where(function ($exp,$q){
                    return $exp->eq('DATE(DATA_VENDA)', date("Y-m-d") );
                });
            }
            $this->set('IS_ADMIN',false);
        }else{
            $query = $tblSell->find()
            ->select(['L.NOME','IDVENDA','DATA_VENDA','SUBTOTAL','DESCONTO','VALOR_PAGO','TROCO'])
            ->join([
                'table' => 'sys_loja',
                'type'  => 'INNER',
                'alias' => 'L',
                'conditions' => 'L.IDLOJA=LojVenda.IDLOJA'
            ]);

            //filtros do administrador
            if($this->request->getData("TXT_SALE_SEARCH_DATE")!=""){
                $query->where(function ($exp,$q){
                    return $exp->eq('DATE(DATA_VENDA)', substr($this->request->getData("TXT_SALE_SEARCH_DATE"), 6,4)."-".substr($this->request->getData("TXT_SALE_SEARCH_DATE"),3,2)."-".substr($this->request->getData("TXT_SALE_SEARCH_DATE"),0,2) );
                });
            }
            if($this->request->getData("CB_SALE_SEARCH_STORE")!=""){
                $query->where(['L.DILOJA' => $this->request->getData("CB_SALE_SEARCH_STORE")]);
            }
            $this->set("IS_ADMIN",true);
        }

        $query->order(['DATA_VENDA' => 'DESC']);
        $this->Paginator->paginate($query,['limit' => 10]);

        $this->set('data_list',$this->paginate($query));
    }

	/**
	* Exibe as informacoes de uma venda
	* @param int $_idVenda Codigo da venda
	*
	* @return null
	*/
    public function saleShow($_idVenda){
        $this->viewBuilder()->layout('gallery');

        //busca as informacoes da venda
        $venda = TableRegistry::get('LojVenda')->get($_idVenda);
        //busca os itens vendidos
        $venda_it  = TableRegistry::get('LojVendaProduto')->find()->where(["IDVENDA" => $_idVenda]);
        //busca as condicoes de pagamento
        $venda_pag = TableRegistry::get('LojVendaPagamento')->find()
            ->select(['CP.NOME','CP.IDCONDICAOPAGAMENTO','VALOR'])
            ->where(['IDVENDA' => $_idVenda])
            ->join([
                'table' => 'sys_condicao_pagamento',
                'type'  => 'INNER',
                'alias' => 'CP',
                'conditions' => 'CP.IDCONDICAOPAGAMENTO=LojVendaPagamento.IDCONDICAOPAGAMENTO'
            ]);
        //busca a nota fiscal da venda
        $nfce  = TableRegistry::get('SysNfce')->find()->where(['IDVENDA' => $_idVenda])->first();

        $this->set('venda',$venda);
        $this->set('PAGAMENTOS',$venda_pag);
        $this->set('ITENS_VENDA',$venda_it);
        if($nfce){
            $this->set('nfce',$nfce);
            $this->set('tem_nota_fiscal',($nfce->IDNFCE>0)?true:false);
        }else{
            $this->set('tem_nota_fiscal',false);
        }

		//busca o funcionario que realizou a venda
        if($venda->IDFUNCIONARIO!=""){
            if($venda->IDFUNCIONARIO!=0){
                $this->set('employer',TableRegistry::get('SysFuncionario')->get($venda->IDFUNCIONARIO));
            }
        }

        //busca os dados do cliente se houver um associado
        if($venda->IDCLIENTE!=""){
            if($venda->IDCLIENTE!=0){
                $this->set('cliente',TableRegistry::get('SysCliente')->get($venda->IDCLIENTE));
            }
        }
    }

	/**
	* Metodo que realiza o estorno de uma venda
	* OBS: ESSE METODO NAO ESTHA FUNCIONANDO O CODIGO EH DE UMA VERSAO
	* ANTIGA DO SISTEMA
	* @param int $_idVenda Codigo da venda
	*
	* @return boolean
	*/
    public function reverseSale($_idVenda){
        $this->load->model("retail/Venda");
        $this->load->model("stock/Produto");
        $this->load->model("stock/Estoque");
        $this->Venda->IDVENDA = $_idVenda;
        $this->Venda->get_by_id();

        foreach($this->Venda->ITENS as $item){
            //busca as informacoes do produto
            $this->Produto->IDPRODUTO = $item->IDPRODUTO;
            $this->Produto->get_by_id();

            if($this->Produto->ESTRUTURA=='C'){
                foreach($this->Produto->ITENS as $pitem){
                    //busca o estoque da loja que fez a venda para o produto especifico
                    $this->Estoque->IDLOJA     = $this->Venda->IDLOJA;
                    $this->Estoque->IDPRODUTO  = $pitem->IDPRODUTO;
                    $this->Estoque->get_by_id();

                    //re-incrementa o estoque do produto para a loja que fez a venda
                    $this->Estoque->QUANTIDADE+= $pitem->QUANTIDADE;
                    $this->Estoque->save_entry();

                    //grava as informacoes do movimento de estoque
                    $this->Estoque->DATA_MOVIMENTO = date("Y-m-d H:i:s");
                    $this->Estoque->TIPO_OPERACAO  = 'E';
                    $this->Estoque->OPERACAO = '+';
                    $this->Estoque->PRECO_CUSTO  = $pitem->PRECO_COMPRA;
                    $this->Estoque->NOME_PRODUTO = $pitem->NOME;
                    $this->Estoque->SKU_PRODUTO  = $pitem->SKU;
                    $this->Estoque->save_move();
                }
            }else{
                //busca o estoque da loja que fez a venda para o produto especifico
                $this->Estoque->IDLOJA     = $this->Venda->IDLOJA;
                $this->Estoque->IDPRODUTO  = $item->IDPRODUTO;
                $this->Estoque->get_by_id();

                //re-incrementa o estoque do produto para a loja que fez a venda
                $this->Estoque->QUANTIDADE += $item->QUANTIDADE;
                $this->Estoque->save_entry();

                //grava as informacoes do movimento de estoque
                $this->Estoque->DATA_MOVIMENTO = date("Y-m-d H:i:s");
                $this->Estoque->TIPO_OPERACAO  = 'E';
                $this->Estoque->OPERACAO = '+';
                $this->Estoque->PRECO_CUSTO  = $this->Produto->PRECO_COMPRA;
                $this->Estoque->NOME_PRODUTO = $this->Produto->NOME;
                $this->Estoque->SKU_PRODUTO  = $this->Produto->SKU;
                $this->Estoque->save_move();
            }
        }
        return $this->response->withStringBody( $this->Venda->disable_entry() );
    }

    /**
	* Metodo que salva os dados de uma venda no banco de dados
	*
	* @return object
	*/
    public function saleSave(){
        $user = $this->Auth->user();
        $result['SUCCESS']     = false;
        $result['EMITE_NFCE']  = false;
        $result['IDVENDA']     = 0;
        $result['IDDEVOLUCAO'] = 0;

        //tabelas
        $tblStock = TableRegistry::get('LojEstoque');
        $tblProd  = TableRegistry::get('SysProduto');
        $tblProdIt= TableRegistry::get('SysProdutoItem');
        $tblMove  = TableRegistry::get('LojMovimentoEstoque');
        $tblVenda = TableRegistry::get('LojVenda');
        $tblDevolve   = TableRegistry::get('LojDevolucao');
        $tblVendaProd = TableRegistry::get('LojVendaProduto');
        $tblVendaProdIt = TableRegistry::get('LojVendaProdutoItem');
        $tblVendaPay  = TableRegistry::get('LojVendaPagamento');
        $tmpCupom = TableRegistry::get('TmpCupom');
        $tblCupom = TableRegistry::get('SysCupom');
        $tmpPay   = TableRegistry::get('TmpPagamento');
        $tblItens = TableRegistry::get('LojItemVenda');

        //busca os itens da venda da tabela de itens de venda
        $itens_venda = $tblItens->find()->where(['IDUSUARIO' => $user['id'], 'IDLOJA' => $user['storeid']]);

        //monta um objeto para tratamento dos totais da venda
        $totais = new \stdClass();
        $totais->DESCONTO = $itens_venda->sumOf('DESCONTO');
        $totais->SUBTOTAL = $itens_venda->sumOf('SUBTOTAL');

        //cria uma entidade de venda para salvar no banco de dados
        $venda = $tblVenda->newEntity();
        $venda->IDLOJA     = $user['storeid'];
        $venda->IDSUARIO   = $user['id'];
        $venda->DATA_VENDA = date("Y-m-d H:i:s");
        $venda->SUBTOTAL   = $totais->SUBTOTAL;
        $venda->DESCONTO   = $totais->DESCONTO;
        $venda->VALOR_PAGO = $totais->SUBTOTAL-$totais->DESCONTO;
        $val_troco         = $this->paymentGetTotal(false)-($totais->SUBTOTAL-$totais->DESCONTO);

        //se houver vale troca, o troco eh zerado, pois serah gerado um contra-vale
        if($tmpCupom->find()->where(['IDLOJA' => $venda->IDLOJA,'IDUSUARIO' => $user['id'],'TIPO_CUPOM' => 'T'])->count()>0){
            $val_troco = 0;
        }
        $venda->TROCO         = $val_troco;
        $venda->OPERADOR      = mb_strtolower($user['username']);
        $venda->IDFUNCIONARIO = (int)$this->request->getData("txtIdFuncionario");
        $venda->IDCLIENTE     = (int)$this->request->getData("txtIdCliente");

        //salva as informacoes da venda
        if($tblVenda->save($venda)){

        	//Se houver indicacao realiza o acumulo de pontos ao indicador
        	/*if($this->request->getData("txtIndicacao")!=""){
				$indicador = (int)$this->request->getData("txtIndicacao");
				$indicado  = $venda->IDCLIENTE;
				$perc_indica = (int)TableRegistry::get('SysOpcao')->get('DISCOUNT_BY_INDICATION')->OPCAO_VALOR;

				//salva as informacoes da indicacao em uma tabela de indicacoes
				$tblIndica = TableRegistry::get('SysClienteIndicacao');
				$indicacao = $tblIndica->newEntity();
				$indicacao->INDICADOR      = $indicador;
				$indicacao->INDICADO       = $indicado;
				$indicacao->VALOR_DESCONTO = $perc_indica;
				$indicacao->VALIDO_ATE     = date('Y-m-d', strtotime("+1 year", strtotime(date("Y-m-d"))));
				$indicacao->STATUS         = "0";
				$tblIndica->save($indicacao);
			}

			//Se houver consumo do desconto de indicacao realiza a baixa
			if($this->request->getData("txtIndicationDiscount")!="0"){
				$tblIndicacao = TableREgistry::get('SysClienteIndicacao');
				foreach($tblIndicacao->find()->where(['INDICADOR' => $venda->IDCLIENTE,'STATUS' => 0])->order(['VALIDO_ATE' => 'DESC']) as $indicacao){
					$indicacao->STATUS = 1;
					$tblIndicacao->save($indicacao);
				}
			}*/

            //salva os itens da venda e realiza o movimento de estoque
            foreach($itens_venda as $item){
                $venda_prod = $tblVendaProd->newEntity();
                $venda_prod->IDVENDA        = $venda->IDVENDA;
                $venda_prod->IDPRODUTO      = $item->IDPRODUTO;
                $venda_prod->IDLOJA         = $venda->IDLOJA;
                $venda_prod->QUANTIDADE     = $item->QUANTIDADE;
                $venda_prod->PRECO_UNITARIO = $item->PRECO_UN;
                $venda_prod->SUBTOTAL       = $item->SUBTOTAL;
                $venda_prod->DESCONTO       = $item->DESCONTO;
                $venda_prod->NOME_PRODUTO   = $item->NOME_PRODUTO;
                $venda_prod->SKU_PRODUTO    = $item->SKU_PRODUTO;
                $venda_prod->UNIDADE_MEDIDA = $item->UNIDADE_MEDIDA;

                $tblVendaProd->save($venda_prod);

                //verifica se a loja vende estoque zerado, se sim nao precia movimentar estoque
                if(TableRegistry::get('SysLoja')->get($venda->IDLOJA)->VENDE_ESTOQUE_ZERADO=="0"){
                    //verifica se o produto eh composto, se for realiza a inclusao dos itens
                    if($tblProd->get($item->IDPRODUTO)->ESTRUTURA=="S"){
                        //realizar aqui a baixa da quantidade do estoque para produtos simples
                        $estoque = $tblStock->get(['IDLOJA' => $venda->IDLOJA,'IDPRODUTO' => $item->IDPRODUTO]);
                        $estoque->QUANTIDADE        = $estoque->QUANTIDADE - $item->QUANTIDADE;
                        $estoque->DATA_ULTIMA_VENDA = $venda->DATA_VENDA;
                        $tblStock->save($estoque);
                        //realia aqui o registro de movimentacao do estoque
                        $movimento = $tblMove->newEntity();
                        $movimento->IDLOJA         = $venda->IDLOJA;
                        $movimento->DATA_MOVIMENTO = date("Y-m-d H:i:s");
                        $movimento->QUANTIDADE     = $item->QUANTIDADE;
                        $movimento->TIPO_OPERACAO  = 'V';
                        $movimento->OPERACAO       = '-';
                        $movimento->PRECO_CUSTO    = $tblProd->get($item->IDPRODUTO)->PRECO_COMPRA;
                        $movimento->NOME_PRODUTO   = $item->NOME_PRODUTO;
                        $movimento->SKU_PRODUTO    = $item->SKU_PRODUTO;
                        $movimento->IDPRODUTO      = $item->IDPRODUTO;
                        $tblMove->save($movimento);
                    }else{

						//busca os itens do produto
						$prd_itens = $tblProdIt->find()->where(['IDPRODUTO' => $item->IDPRODUTO]);

						//realiza o calculo do desconto por Item
						//apenas divide o total do desconto pelo
						//numero de subitens
						$desconto_item = 0;
						if($item->DESCONTO>0){
							$desconto_item = $item->DESCONTO/$prd_itens->count();
						}

                        foreach($prd_itens as $prd_item){
                            //busca as informacoes do produto filho
                            $PRODUTO = $tblProd->get($prd_item->IDPRODUTO_FILHO);


                            //salva os sub-produtos do produto composto
                            $venda_prd_it = $tblVendaProdIt->newEntity();
                            $venda_prd_it->IDLOJA             = $venda->IDLOJA;
                            $venda_prd_it->IDVENDA            = $venda->IDVENDA;
                            $venda_prd_it->IDPRODUTO          = $item->IDPRODUTO;
                            $venda_prd_it->IDPRODUTO_FILHO    = $prd_item->IDPRODUTO_FILHO;
                            $venda_prd_it->NOME_PRODUTO_FILHO = $PRODUTO->NOME;
                            $venda_prd_it->SK_PRODUTO_FILHO   = $PRODUTO->SKU;
                            $venda_prd_it->DESCONTO           = $desconto_item;
                            $venda_prd_it->PRECO_VENDA        = $PRODUTO->PRECO_VENDA;
                            $tblVendaProdIt->save($venda_prd_it);

                            //realizar aqui a baixa da quantidade no estoque de cada produto filho do produto composto
                            $estoque = $tblStock->get(['IDLOJA' => $venda->IDLOJA,'IDPRODUTO' => $prd_item->IDPRODUTO_FILHO]);
                            $estoque->QUANTIDADE        = $estoque->QUANTIDADE - 1;
                            $estoque->DATA_ULTIMA_VENDA = $venda->DATA_VENDA;
                            $tblStock->save($estoque);

                            //realia aqui o registro de movimentacao de estoque de cada item filho do produto composto
                            $movimento = $tblMove->newEntity();
                            $movimento->IDLOJA         = $venda->IDLOJA;
                            $movimento->DATA_MOVIMENTO = date("Y-m-d H:i:s");
                            $movimento->QUANTIDADE     = 1;
                            $movimento->TIPO_OPERACAO  = 'V';
                            $movimento->OPERACAO       = '-';
                            $movimento->PRECO_CUSTO    = $PRODUTO->PRECO_COMPRA;
                            $movimento->NOME_PRODUTO   = $PRODUTO->NOME;
                            $movimento->SKU_PRODUTO    = $PRODUTO->SKU;
                            $movimento->IDPRODUTO      = $prd_item->IDPRODUTO_FILHO;
                            $tblMove->save($movimento);
                        }
                    }
                }
            }

            //salva os pagamentos da venda
            foreach($tmpPay->find() as $tmp_pay){
                $venda_pag = $tblVendaPay->newEntity();
                $venda_pag->IDVENDA = $venda->IDVENDA;
                $venda_pag->IDLOJA  = $venda->IDLOJA;
                $venda_pag->IDCONDICAOPAGAMENTO = $tmp_pay->IDCONDICAOPAGAMENTO;
                $venda_pag->VALOR   = $tmp_pay->VALOR_PAGO;
                $tblVendaPay->save($venda_pag);
            }

            //tratamento de cupons e devolucao
            $pode_emitir_nota = false;
            $cupons = $tmpCupom->find()->where(['IDLOJA' => $venda->IDLOJA,'IDUSUARIO' => $user['id']]);
            if($cupons->count()>0){
                $tmp_cupom = $cupons->first();
                //se o cupom nao for de troca
                if($tmp_cupom->TIPO_CUPOM!="T"){

                	//indica que pode aparecer na nota fiscal se nao for um cupom de troca
                	$pode_emitir_nota = false;

                    $cupom = $tblCupom->get($tmp_cupom->IDCUPOM);
                    //sinaliza o cupom como utilizado se ele nao for um cupom indeterminado na utilizacao
                    if($cupom->UTILIZADO!="I"){
						$cupom->UTILIZADO = 'S';
					}
                    $cupom->DATA_UTILIZACAO = $venda->DATA_VENDA;
                    $cupom->OBSERVACAO = "CUPOM UTILIZADO PARA PAGAMENTO NA VENDA ".$venda->IDVENDA." EM ".date("d/m/Y");
                    $tblCupom->save($cupom);
                }else{

                    $devolve = $tblDevolve->get($tmp_cupom->IDCUPOM);
                    $devolve->UTILIZADO    = 1;
                    $devolve->UTILIZADO_EM = $venda->DATA_VENDA;
                    $devolve->OBSERVACAO   = "VALE-TROCA UTILIZADO PARA PAGAMENTO NA VENDA - ".$venda->IDVENDA;
                    $tblDevolve->save($devolve);

                    //verifica se ha necessidade de gerar contra-vale
                    //se o valor do cupom for maior que o subtotal da venda
                    //entao serah gerado contra-vale automaticamente
                    if($tmp_cupom->VALOR > $totais->SUBTOTAL){
                        $devolucao = $tblDevolve->newEntity();
                        $devolucao->IDLOJA         = $venda->IDLOJA;
                        $devolucao->DATA_DEVOLUCAO = $venda->DATA_VENDA;
                        //o valor do vale troca serah apenas da sobra
                        $devolucao->VALOR_TOTAL    = abs($totais->SUBTOTAL-($tmp_cupom->VALOR*-1));
                        $devolucao->UTILIZADO      = 0;
                        $devolucao->OBSERVACAO     = "CONTRA-VALE REFERENTE A VENDA: ".$venda->IDVENDA." REALIZADA EM ".date("d/m/Y");
                        $devolucao->ITENS = "";
                        $tblDevolve->save($devolucao);
                        $result['IDDEVOLUCAO'] = $devolucao->IDDEVOLUCAO;
                    }
                }
            }

            //limpeza das tabelas de item_venda, tmp_pagamento e tmp_cupom
            $tblItens->deleteAll(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']]);
            $tmpPay->deleteAll(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']]);
            $tmpCupom->deleteAll(['IDLOJA' => $user['storeid'],'IDUSUARIO' => $user['id']]);

            //se puder emitir nota com o cupom
            //verifica se o sistema estah autorizado a emitir nota
            if($pode_emitir_nota){
                //verifica qual o tipo de emissao da NFC-e
                $result_emite = TableRegistry::get('SysLoja')->get($user['storeid'])->NFE_EMITE;
                //se a emissao for opcional
                if($result_emite=="2"){
                    //verifica a situacao do check de emissao
                    $result['EMITE_NFCE'] = ($this->request->getData("chkEmiteNota")!="")?true:false;
                }else{
                    //verifica a condicao do banco de dados
                    $result['EMITE_NFCE'] = $result_emite;
                }
            }

            //marca a emissao da venda como sucesso
            $result['SUCCESS'] = true;
            //adiciona o ID da venda para emitir a NFC-e caso haja necessidade
            $result['IDVENDA'] = $venda->IDVENDA;
        }

        return $this->response->withStringBody( json_encode($result) );
    }

    /**
     * Metodo que exibe um ticket de venda (Cupom nao fiscal), caso o sistema nao emita NFC-e
     * null
     */
    public function ticket($_idVenda){
        $venda    = TableRegistry::get("LojVenda")->get($_idVenda);
        $loja     = TableRegistry::get("SysLoja")->get($venda->IDLOJA);
        $cidade   = TableRegistry::get("SysCidade")->get($loja->IDCIDADE);
        $cliente  = (($venda->IDCLIENTE!=0)?TableRegistry::get('SysCliente')->get($venda->IDCLIENTE):NULL);
        $itVenda  = TableRegistry::get('LojVendaProduto')->find()->where(['IDVENDA' => $venda->IDVENDA]);
        $vendaPag = TableRegistry::get('LojVendaPagamento')->find()->where(['IDVENDA' => $venda->IDVENDA]);
        $func     = (($venda->IDFUNCIONARIO!=0)?TableRegistry::get('SysFuncionario')->get($venda->IDFUNCIONARIO):NULL);
        $cargo    = (($venda->IDFUNCIONARIO!=0)?TableRegistry::get('SysCargo')->get($func->IDCARGO):NULL);

        $this->response = $this->response->withType('pdf');

        $mpdf = new \Mpdf\Mpdf();

        $css = "body{";
        $css.= 'font-family: "Times New Roman", Times, serif;';
        $css.= "font-size: 12px;";
        $css.= "}";
        $css.= ".column-70{
			float: left;
			width: 70%;
        }
        .column-10{
            float: left;
            width: 10%;
        }
        .column-20{
            float: left;
            width: 20%;
        }
		.column-30{
			float: right;
			width: 30%;
        }
        .column-40{
            float: left;
            width: 40%;
        }
        .column-50{
            width: 50%;
            float: left;
        }
        .column-60{
            width: 60%;
            float: left;
        }
		.row:after{
			content: \"\";
			display: table;
            clear: both;
        }
        .text-right{
            text-align:right;
        }";

        $mpdf->WriteHTML($css,\Mpdf\HTMLParserMode::HEADER_CSS);

        $html = "<div style='text-align:center'><strong style='font-size:18px;'>".$loja->NOME."</strong><br/>";
        $html.= $loja->RAZAO_SOCIAL."<br>";
        $html.= $loja->ENDERECO.", ".$loja->ENDERECO_NUM.(($loja->ENDERECO_COMPLEMENTO!="")?" - ".$loja->ENDERECO_COMPLEMENTO:"")."<br/>";
        $html.= $loja->BAIRRO." - ".$loja->CEP." - ".$cidade->NOME."/".$cidade->UF."<br/>";
        $html.= $loja->TELEFONE."</div><br/>";
        $html.= "<div class='row'>";
        $html.= "<div class='column-50'><strong>CNPJ:</strong> ".$loja->CNPJ."</div>";
        $html.= "<div class='column-50 text-right'>Inscri&ccedil;&atilde;o Estadual: ".$loja->INSCRICAO_ESTADUAL."</div>";
        $html.= "</div><br/>";
        $html.= "CLIENTE: ".(($cliente!=NULL)?$cliente->NOME:"")."<br/>";
        $html.= "<div class='row'>";
        $html.= "<div class='column-50'>".$venda->DATA_VENDA->format("d/m/Y H:i:s")."</div>";
        $html.= "<div class='column-50 text-right'><strong><span style='font-size:8px'>COMPROVANTE DE VENDA</span><br/>N&deg; ".str_pad($venda->IDVENDA,10,"0",STR_PAD_LEFT)."</strong></div>";
        $html.= "</div>";
        $html.= "<hr size='1'>";
        $html.= "<div calss='row'>";
        $html.= "<div class='column-60'>PRODUTO</div>";
        $html.= "<div class='column-20'>QTD X VALOR</div>";
        $html.= "<div class='column-20 text-right'>SUBTOTAL</div>";
        $html.= "</div>";
        $html.= "<hr size='1'>";
        foreach($itVenda as $item){
            $number = new Number();
            $html.= "<div class='row'>";
            $html.= "<div class='column-60'>".$item->IDPRODUTO." - ".$item->NOME_PRODUTO."</div>";
            $html.= "<div class='column-20'>".$item->QUANTIDADE." X ".$number->currency($item->PRECO_UNITARIO,"BRL")."</div>";
            $html.= "<div class='column-20 text-right'>".$number->currency($item->SUBTOTAL,"BRL")."</div>";
            $html.= "</div>";
        }
        $html.= "<hr size='1'>";
        $html.= "<strong style='font-size:14px;'>Total da Nota: ".$number->currency($venda->SUBTOTAL,"BRL")."</strong><br/>";
        $html.= "<strong style='font-size:14px;'>Valor Recebido: ".$number->currency($venda->SUBTOTAL,"BRL")."</strong><br/>";
        $html.= "<strong style='font-size:14px;'>Troco: ".$number->currency($venda->TROCO,"BRL")."</strong><br/>";
        $html.= "<hr size='1'>";
        $html.= (($cargo!=NULL)?$cargo->NOME:"VENDEDOR").": ".(($func!=NULL)?$func->NOME:"");
        $html.= "<hr size='1'>";
        $html.= "<p>PAGAMENTO: <br/>";
        $html.= "<div class='row'>";
        $html.= "<div class='column-50'>Condi&ccedil;&atilde;o</div>";
        $html.= "<div class='column-50 text-right'>Valor</div>";
        $html.= "</div>";
        foreach($vendaPag as $pag){
            $html.= "<div class='row'>";
            $html.= "<div class='column-50'>".TableRegistry::get('SysCondicaoPagamento')->get($pag->IDCONDICAOPAGAMENTO)->NOME."</div>";
            $html.= "<div class='column-50 text-right'>".$number->currency($pag->VALOR,"BRL")."</div>";
            $html.= "</div>";
        }

        $html.= "</p>";

        $mpdf->WriteHTML($html,\Mpdf\HTMLParserMode::HTML_BODY);

        $mpdf->Output();

        return $this->response;
    }

    /******************ABERTURA E FECHAMENTO DE CAIXA ***********************/

    /**
	* Metodo que exibe a tela de fechamento de caixa
	*
	* @return null
	*/
    public function boxClose(){
        $this->set('bandeiras',TableRegistry::get('SysBandeiraCartao')->find());
        $this->set('caixa_status',$this->boxStatus());
        $this->set('valorCaixa',$this->request->query("valorCaixa"));
    }

    private function boxCloseCalculation($_dateOpen){
        $user = $this->Auth->user();

        $sql = "SELECT IDMEIOPAGAMENTO, "
            . "NOME,(SELECT CASE MC.IDMEIOPAGAMENTO WHEN 1 THEN IFNULL(SUM(VP.VALOR-V.TROCO),0) ELSE SUM(VP.VALOR) END "
            . "FROM loj_venda V "
            . "INNER JOIN loj_venda_pagamento VP on VP.IDVENDA=V.IDVENDA "
            . "INNER JOIN sys_condicao_pagamento CP on CP.IDCONDICAOPAGAMENTO=VP.IDCONDICAOPAGAMENTO "
            . "INNER JOIN sys_meio_condicao MC ON MC.IDCONDICAOPAGAMENTO=CP.IDCONDICAOPAGAMENTO "
            . "WHERE DATE(V.DATA_VENDA)=DATE('".$_dateOpen."') AND MC.IDMEIOPAGAMENTO=GPAG.IDMEIOPAGAMENTO "
            . "AND V.IDLOJA=".$user['storeid'].") AS VALOR_APURADO "
            . "FROM sys_meio_pagamento GPAG";
        $connection = ConnectionManager::get('default');
        $results = $connection->execute($sql)->fetchAll('assoc');

        return $results;
    }

    public function boxCloseCheck(){
        $user = $this->Auth->user();
        $tblBox = TableRegistry::get('LojCaixa');
        $tblOption = TableRegistry::get('SysOpcao');

        $total_retirada = 0;
        $venda_total_dinheiro = 0;

        //busca as informacoes do ultimo caixa aberto
        $caixa = $tblBox->find()
            ->select(['IDCAIXA','VALOR_ABERTURA','DATA_ABERTURA','PERIODO_ABERTURA','DATA_FECHAMENTO'])
            ->where(['IDLOJA' => $user['storeid']])
            ->order(['IDCAIXA' => 'DESC'])->first();

        //debug($caixa);

        $VALOR_FECHAMENTO = str_replace(",","",$this->request->getData("valorCaixa"));
        $VALOR_MAQUINA1   = str_replace(",","",$this->request->getData("valorMaquina1"));
        $VALOR_MAQUINA2   = str_replace(",","",$this->request->getData("valorMaquina2"));
        $BANDEIRAS        = $this->request->getData("txtIdBandeira");

        $valores_fecha = $this->boxCloseCalculation($caixa->DATA_ABERTURA->format("Y-m-d"));

        $valores_caixa = null;

        //realiza a montagem dos valores de caixa por grupo realizando o somatorio
        for($i=0;$i<count($BANDEIRAS);$i++){
            $existe = false;

            $val = new \stdClass();
            $val->IDMEIOPAGAMENTO = TableRegistry::get('SysBandeiraCartao')->get($BANDEIRAS[$i])->IDMEIOPAGAMENTO;
            $val->VALOR_DIGITADO   = $VALOR_MAQUINA1[$i]+$VALOR_MAQUINA2[$i];
            if(!is_array($valores_caixa)){
                $valores_caixa[] = $val;
            }else{
                for($j=0;$j<count($valores_caixa);$j++){
                    if($valores_caixa[$j]->IDMEIOPAGAMENTO==$val->IDMEIOPAGAMENTO){
                        $valores_caixa[$j]->VALOR_DIGITADO += $val->VALOR_DIGITADO;
                        $existe = true;
                    }
                }
                if(!$existe){ $valores_caixa[] = $val; }
            }
        }

        //inclui nos valores do fechamento o valor digitado
        $nvalores_fecha = NULL;
        //varre os valores apurados
        foreach($valores_fecha as $val_fechamento){
            $nval_fecha = new \stdClass();
            $nval_fecha->IDMEIOPAGAMENTO = $val_fechamento['IDMEIOPAGAMENTO'];
            $nval_fecha->NOME            = $val_fechamento['NOME'];
            $nval_fecha->VALOR_APURADO   = $val_fechamento['VALOR_APURADO'];
            $nval_fecha->VALOR_DIGITADO  = 0;

            //varre os valores digitados no caixa
            foreach($valores_caixa as $val_caixa){
                if($val_fechamento['IDMEIOPAGAMENTO']==$val_caixa->IDMEIOPAGAMENTO){
                    $nval_fecha->VALOR_DIGITADO = $val_caixa->VALOR_DIGITADO;
                }
            }

            //tratamento da informacao de dinheiro
            if($val_fechamento['IDMEIOPAGAMENTO']==$tblOption->get("PAYMENT_METHOD_MONEY")->OPCAO_VALOR){
                $venda_total_dinheiro = $nval_fecha->VALOR_APURADO;
                $nval_fecha->VALOR_DIGITADO = $nval_fecha->VALOR_APURADO;
            }

            //tradamento da informacao de vale presente
            if($val_fechamento['IDMEIOPAGAMENTO']==$tblOption->get("PAYMENT_METHOD_GIFT")->OPCAO_VALOR){
                $nval_fecha->VALOR_DIGITADO = $nval_fecha->VALOR_APURADO;
            }

            //tratamento da informacao de Troca
            if($val_fechamento['IDMEIOPAGAMENTO']==$tblOption->get("PAYMENT_METHOD_CHANGE")->OPCAO_VALOR){
                $nval_fecha->VALOR_DIGITADO = $nval_fecha->VALOR_APURADO;
            }
            $nvalores_fecha[] = $nval_fecha;
        }

        foreach(TableRegistry::get('LojCaixaRetirada')->find()->where(['IDLOJA' => $user['storeid']]) as $removal){
            $nremoval = new \stdClass();
            $nremoval->IDCAIXARETIRADA = $removal->IDCAIXARETIRADA;
            $nremoval->OBSERVACAO      = $removal->OBSERVACAO;
            $nremoval->VALOR           = $removal->VALOR;

            //realiza o somatorio do total de retiradas do caixa
            $total_retirada += $removal->VALOR;

            $nremoval->NOME_TIPO_DESPESA = TableRegistry::get('SysTipoDespesa')->get($removal->IDTIPODESPESA)->NOME;
            $REMOVALS[] = $nremoval;
        }

        if(isset($REMOVALS)){
            $this->set('retiradalist',$REMOVALS);
        }

        $sobra_ou_falta = $VALOR_FECHAMENTO-(($caixa->VALOR_ABERTURA+$venda_total_dinheiro)-$total_retirada);

        $this->set('pagamentolist',$nvalores_fecha);
        $this->set('valor_fechamento',$VALOR_FECHAMENTO);
        $this->set('venda_total_dinheiro',$venda_total_dinheiro);
        $this->set('total_retirada',$total_retirada);
        $this->set('valor_abertura',$caixa->VALOR_ABERTURA);
        $this->set('sobra_ou_falta',($sobra_ou_falta>0)?0:$sobra_ou_falta);
    }

    /**
	* Metodo que executa o fechamento de caixa
	*
	* @return boolean
	*/
    public function boxCloseExecute(){
    	$retorno = false;

        $user = $this->Auth->user();
        $tblBox     = TableRegistry::get('LojCaixa');
        $tblDebit   = TableRegistry::get('LojCaixaDebito');
        $tblRemoval = TableRegistry::get('LojCaixaRetirada');
        $tblRemoved = TableRegistry::get('LojCaixaSangria');
        $tblCap     = TableRegistry::get('LojContasPagar');
        $tblDesp    = TableRegistry::get('SysTipoDespesa');

        //busca o ultimo caixa da loja
        $caixa = $tblBox->find()
            ->where(['IDLOJA' => $user['storeid']])
            ->order(['IDCAIXA' => 'DESC'])->first();

        $IDMEIOPAGAMENTO  = $this->request->getData("IDMEIOPAGAMENTO");
        $VALOR_DIGITADO   = $this->request->getData("VALOR_DIGITADO");
        $VALOR_APURADO    = $this->request->getData("VALOR_APURADO");
        $VALOR_SANGRIA    = str_replace(",","",$this->request->getData("VALOR_SANGRIA"));
        $VALOR_FECHAMENTO = str_replace(",","",$this->request->getData("VALOR_FECHAMENTO"));

        $caixa->DATA_FECHAMENTO      = date("Y-m-d H:i:s");
        $caixa->IDUSUARIO_FECHAMENTO = $user['id'];
        $caixa->VALOR_FECHAMENTO     = $VALOR_FECHAMENTO-$VALOR_SANGRIA;
        $caixa->STATUS               = 'F';

        //salva as informacoes do fechamento
        if($tblBox->save($caixa)){
            //salva os valores dos debitos
            for($i=0;$i<count($VALOR_DIGITADO);$i++){
                $debito = $tblDebit->newEntity();
                $debito->IDCAIXA          = $caixa->IDCAIXA;
                $debito->IDMEIOPAGAMENTO  = $IDMEIOPAGAMENTO[$i];
                $debito->IDLOJA           = $caixa->IDLOJA;
                $debito->VALOR_APURADO    = (float)str_replace(",","",$VALOR_APURADO[$i]);
                $debito->VALOR_INFORMADO  = (float)str_replace(",","",$VALOR_DIGITADO[$i]);
                $tblDebit->save($debito);
            }

            //caso haja valor de sangria salva ele como retirada de caixa nas sangrias do caixa
            if($VALOR_SANGRIA!=""){
                $removal = $tblRemoved->newEntity();
                $removal->IDCAIXA        = $caixa->IDCAIXA;
                $removal->IDLOJA         = $caixa->IDLOJA;
                $removal->VALOR          = $VALOR_SANGRIA;
                $removal->OBSERVACAO     = "SANGRIA DE CAIXA";
                $tblRemoved->save($removal);
            }

            //salva os valores das retiradas no calendario quando houver necessidade
            foreach($tblRemoval->find()->where(['IDLOJA' => $caixa->IDLOJA]) as $retirada){
                $despesa = $tblDesp->get($retirada->IDTIPODESPESA);
                if($despesa->SALVA_CALENDARIO){
                    $contas_pagar = $tblCap->newEntity();
                    $contas_pagar->IDLOJA          = $caixa->IDLOJA;
                    $contas_pagar->IDTIPODESPESA   = $retirada->IDTIPODESPESA;
                    $contas_pagar->DATA_VENCIMENTO = date("Y-m-d H:i:s");
                    $contas_pagar->DATA_PAGAMENTO  = $contas_pagar->DATA_VENCIMENTO;
                    $contas_pagar->VALOR_ORIGINAL  = $retirada->VALOR;
                    $contas_pagar->VALOR_PAGO      = $retirada->VALOR;
                    $contas_pagar->DIFERENCA_PAGAMENTO = 0;
                    $contas_pagar->TEM_REPETICAO       = 0;
                    $contas_pagar->OBSERVACAO          = $retirada->OBSERVACAO;
                    $tblCap->save($contas_pagar);
                }

                //realiza a inclusao na tabela de sangrias indiferente de salvar no calendario ou nao
                $removal = $tblRemoved->newEntity();
                $removal->IDCAIXA        = $caixa->IDCAIXA;
                $removal->IDLOJA         = $caixa->IDLOJA;
                $removal->VALOR          = $retirada->VALOR;
                $removal->OBSERVACAO     = $despesa->NOME." - ".$retirada->OBSERVACAO;
                $tblRemoved->save($removal);
            }

            //limpa a tabela de retiradas
            $tblRemoval->deleteAll(array('1 = 1'));
            $retorno = true;
        }

        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que exibe os detalhes do fechamento de caixa de uma
	* determinada data
	*
	* @return null
	*/
    public function boxCloseView(){
        $tblStore = TableRegistry::get('SysLoja');

		//define a variavel com todas as lojas do sistema
		//exceto a loja padrao
        $this->set('storelist',
            $tblStore->find()
                ->where(function($exp,$q){
                    return $exp->notEq('IDLOJA',TableRegistry::get('SysOpcao')->get("DEFAULT_STORE")->OPCAO_VALOR);
                })
        );
    }

    /**
	* Metodo que busca as informacoes de um fechamento de caixa
	*
	* @return null
	*/
    public function boxCloseViewData(){

        $user = $this->Auth->user();
        $tblBox = TableRegistry::get('LojCaixa');

        $caixa = $tblBox->find()
            ->where(['IDLOJA' => ($this->request->getData("LOJA")==""?$user['storeid']:$this->request->getData("LOJA"))])
            ->where(function($exp,$q){
                return $exp->eq('DATE(DATA_ABERTURA)', (($this->request->getData("DATA")=="")?date("Y-m-d"):(substr($this->request->getData("DATA"), 6,4)."-".substr($this->request->getData("DATA"),3,2)."-".substr($this->request->getData("DATA"),0,2))) );
            })->first();
        $this->set('caixa',$caixa);

        if($caixa){

            $this->set('caixa_retiradas',TableRegistry::get('LojCaixaSangria')->find()->where(['IDCAIXA' => $caixa->IDCAIXA]));

            $debitos = array( );
            foreach(TableRegistry::get('LojCaixaDebito')->find()->where(['IDCAIXA' => $caixa->IDCAIXA]) as $caixa_debito){
                $meio_pag = TableRegistry::get('SysMeioPagamento')->get($caixa_debito->IDMEIOPAGAMENTO);
                $deb = new \stdClass();
                $deb->MEIO_PAGAMENTO  = $meio_pag->NOME;
                $deb->VALOR_APURADO   = $caixa_debito->VALOR_APURADO;
                $deb->VALOR_INFORMADO = $caixa_debito->VALOR_INFORMADO;
                $debitos[] = $deb;
            }

            $this->set('caixa_debitos',$debitos);
        }
    }

    /**
	* Metodo que retorna qual o status do caixa com base na data do dia
	*
	* @return int Os status sao: 2 - Fechado no dia, 4 - Fechado no dia anterior, 1 - Aberto no dia, 3 - Aberto no dia anterior, -1 nao existe informacao sobre o caixa
	*/
    private function boxStatus(){
        $user = $this->Auth->user();
        $tblBox = TableRegistry::get('LojCaixa');

        //busca as informacoes sobre o caixa no banco de dados em ordem decrescente
        $caixas = $tblBox->find()
            ->select(['IDCAIXA','DATA_ABERTURA','PERIODO_ABERTURA','DATA_FECHAMENTO','STATUS'])
            ->where(['IDLOJA' => $user['storeid']])
            ->order(['IDCAIXA' => 'DESC']);
        //se houver alguma informacao
        if($caixas->count()>0){
        	//pega apenas o primeiro registro, ou seja, o ultimo na verdade
            $caixa = $caixas->first();

            //busca a data de abertura do banco de dados
            $data_abertura   = new \DateTime($caixa->DATA_ABERTURA->format("Y-m-d"));
            //monta a data atual do dia
            $data_hoje       = new \DateTime(date("Y-m-d"));

            //se o status do registro for F, significa que estah fechado
            if($caixa->STATUS=='F'){
            	//se a data de abertura e fechamento forem iguais
                if($data_abertura==$data_hoje){
                    return 2;//Fechado hoje
                }
                //se a data de abertura foi menor que hoje
                if($data_hoje>=$data_abertura){
                    return 4;//Fechado anterior (correto)
                }
            }else{
            	//se a data de abertura for igual ao dia atual
                if($data_abertura==$data_hoje){
                    return 1; //Aberto hoje
                }
                //se a data de abertura for menor que a do dia atual
                if($data_hoje>=$data_abertura){
                    return 3;//Aberto anterior
                }
            }
        }else{
            return -1; //nao existe informacao sobre o caixa
        }
    }

    /**
	* Metodo que direciona conforme o status do caixa
	*
	* @return null
	*/
    public function boxStatusCheck(){
        switch($this->boxStatus()){
            case 1: $this->redirect(['controller' => 'Retail','action' => 'pos']); break;
            case 2: $this->redirect(['controller' => 'Retail','action' => 'boxOpen']); break;
            case 3: $this->redirect(['controller' => 'Retail','action' => 'boxClose']); break;
            case 4: $this->redirect(['controller' => 'Retail','action' => 'boxOpen']); break;
            case -1: $this->redirect(['controller' => 'Retail','action' => 'boxOpen']); break;
        }
    }

	/**
	* Medodo que exibe a tela para abertura de caixa
	*
	* @return null
	*/
    public function boxOpen(){
    	$this->set('user',$this->Auth->user());
        $this->set('caixa_status',$this->boxStatus());
    }

    /**
	* Metodo que executa a abertura de caixa
	*
	* @return boolean or -1
	*/
    public function boxOpenExecute(){
        $retorno = false;
        $user = $this->Auth->user();
        $tblBox = TableRegistry::get('LojCaixa');

        $last_closed = $tblBox->find()
                ->select(['VALOR_FECHAMENTO'])
                ->where(['IDLOJA' => $user['storeid'],'DATA_FECHAMENTO IS NOT' => NULL])
                ->order(['IDCAIXA' => 'DESC'])->first();

        $VALOR_ABERTURA = $this->request->getData("VALOR_ABERTURA");

        //verifica se existe algum fechamento
        if($last_closed){

	        if(number_format($last_closed->VALOR_FECHAMENTO,2)==number_format($VALOR_ABERTURA,2) ){
	            $caixa = $tblBox->newEntity();
	            $caixa->IDLOJA = $user['storeid'];
	            $caixa->DATA_ABERTURA    = date("Y-m-d H:i:s");
	            $caixa->PERIODO_ABERTURA = $this->request->getData("PERIODO_ABERTURA");
	            $caixa->VALOR_ABERTURA   = $VALOR_ABERTURA;
	            $caixa->IDUSUARIO_ABERTURA = $user['id'];
	            $caixa->STATUS             = 'A';
	            $retorno = $tblBox->save($caixa)?true:false;
	        }else{
	            $retorno = -1;
	        }
        }else{
			$caixa = $tblBox->newEntity();
            $caixa->IDLOJA = $user['storeid'];
            $caixa->DATA_ABERTURA    = date("Y-m-d H:i:s");
            $caixa->PERIODO_ABERTURA = $this->request->getData("PERIODO_ABERTURA");
            $caixa->VALOR_ABERTURA   = $VALOR_ABERTURA;
            $caixa->IDUSUARIO_ABERTURA = $user['id'];
            $caixa->STATUS             = 'A';
            $retorno = $tblBox->save($caixa)?true:false;
		}

        return $this->response->withStringBody( $retorno );
    }

    /******************RETIRADA DE CAIXA ***********************/
    /**
	* Metodo que exibe a tela de registro das retiradas de caixa
	*
	* @return null
	*/
    public function removal(){
    	$this->set('user',$this->Auth->user());
        $this->set('tipodespesalist',TableRegistry::get('SysTipoDespesa')->find()->where(['EXIBE_LOJA' => 1])->order(['NOME' => 'ASC']) );
    }

    /**
	* Metodo que salva as informacoes de uma retirada de caixa
	*
	* @return boolean
	*/
    public function removalSave(){
        $user = $this->Auth->user();

        $tblRetirada = TableRegistry::get('LojCaixaRetirada');
        $retirada = $tblRetirada->newEntity();
        $ultima_retirada = $tblRetirada->find()->select(['IDCAIXARETIRADA'])->where(['IDLOJA' => $user['storeid']])->order(['IDCAIXARETIRADA' => 'DESC']);
        $retirada->IDCAIXARETIRADA = ($ultima_retirada->count()>0)?($ultima_retirada->first()->IDCAIXARETIRADA+1):1;
        $retirada->IDTIPODESPESA   = $this->request->getData("IDTIPODESPESA");
        $retirada->VALOR           = str_replace(",", "", $this->request->getData("VALOR"));
        $retirada->OBSERVACAO      = mb_strtoupper($this->request->getData("OBSERVACAO"));
        $retirada->IDLOJA          = $user['storeid'];
        return $this->response->withStringBody( $tblRetirada->save($retirada)?true:false );
    }

    /****************** PROMOCOES ***********************/
    /**
	* Metodo que exibe os filtros da listagem de promocoes
	*
	* @return html
	*/
    public function promotionFilter(){

        $this->Filter->addFilter("Nome da Promo&ccedil;&atilde;o","TXT_PROMO_SEARCH_NAME","text");

        $pops = array();
        $tblStore = TableRegistry::get('SysLoja');
        $stores = $tblStore->find()->select(['IDLOJA','NOME'])->order(['NOME' => 'ASC']);
        foreach($stores as $store){
            $opt = new \stdClass();
            $opt->key = $store->NOME;
            $opt->value = $store->IDLOJA;
            $pops[] = $opt;
        }
        $this->Filter->addFilter('Loja',"CB_PROMO_SEARCH_STORE","combo",$pops);
        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    /**
	* Metodo que exibe a pagina inicial da listagem de promocoes
	*
	* @return null
	*/
    public function promotion(){

    	$this->set('title',"Promo&ccedil;&otilde;es");

        $this->set('url_filter','/retail/promotion_filter');
        $this->set('url_data','/retail/promotion_data');
    }

    /**
	* Metodo que trata a busca de dados de promocoes
	*
	* @return null
	*/
    public function promotionData(){
        $tblPromo = TableRegistry::get('LojPromocao');

        $query = $tblPromo->find()
            ->join([
                'table' => 'sys_loja',
                'type'  => 'INNER',
                'alias' => 'L',
                'conditions' => "L.IDLOJA=LojPromocao.IDLOJA"
            ]);
        $query->select(['IDPROMOCAO','LojPromocao.NOME','L.NOME','DATA_INICIAL','DATA_FINAL']);

        if($this->request->getData("CB_PROMO_SEARCH_STORE")!=""){
            $query->where(['L.IDLOJA' => $this->request->getData("CB_PROMO_SEARCH_STORE")]);
        }
        if($this->request->getData("TXT_PROMO_SEARCH_NAME")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('LojPromocao.NOME','%'.$this->request->getData("TXT_PROMO_SEARCH_NAME").'%');
            });
        }

        $this->set('data_list',$this->paginate($query,['limit' => 10]));

        //garante que nao haverao registros na tabela
        TableRegistry::get('TmpPromocao')->deleteAll(array('1 = 1'));
    }

	/**
	* Metodo que exibe a tela para cadastro ou edicao de promocao
	* @param int $_idPromocao codigo da promocao (opcional)
	*
	* @return null
	*/
    public function promotionCreate($_idPromocao=""){
        if($_idPromocao!=""){
            $this->set('promo',TableRegistry::get('LojPromocao')->get($_idPromocao));
        }

        $this->set('storelist',TableRegistry::get('SysLoja')->find()->where(function($exp,$q){ return $exp->notEq('IDLOJA',TableRegistry::get('SysOpcao')->get("DEFAULT_STORE")->OPCAO_VALOR); }));
        $this->set('conditionlist',TableRegistry::get('SysCondicaoPagamento')->find()->where(['EXIBIR_PDV' => 1]));
    }

    /**
	* Metodo que salva as informacoes de uma promocao
	*
	* @return boolean
	*/
    public function promotionSave(){
        $retorno = false;
        $tmpPromo = TableRegistry::get('TmpPromocao');
        $tblPromo = TableRegistry::get('LojPromocao');
        $tblPromoIt = TableRegistry::get('LojPromocaoProduto');

        if($this->request->getData("txtIdPromotion")!=""){
            $promocao = $tblPromo->get($this->request->getData("txtIdPromotion"));
            $tblPromoIt->deleteAll(['IDPROMOCAO' => $promocao->IDPROMOCAO]);
        }else{
            $promocao = $tblPromo->newEntity();
        }
        $time = new Time();

        $promocao->IDLOJA       = $this->request->getData("cbPromoStore");
	$promocao->NOME         = mb_strtoupper($this->request->getData("txtPromoName"));
	$promocao->DATA_INICIAL = $time->parseDate($this->request->getData("txtPromoInicio"))->i18nFormat("yyyy-MM-dd");
	$promocao->DATA_FINAL   = $time->parseDate($this->request->getData("txtPromoFim"))->i18nFormat("yyyy-MM-dd");
        if($tblPromo->save($promocao)){
            foreach($tmpPromo->find() as $itens){
                $item_promo = $tblPromoIt->newEntity();
                $item_promo->IDPROMOCAO = $promocao->IDPROMOCAO;
                $item_promo->IDPRODUTO  = $itens->IDPRODUTO;
                $item_promo->IDCONDICAOPAGAMENTO = $itens->IDCONDICAOPAGAMENTO;
                $item_promo->PRECO_PROMO = $itens->PRECO_PROMO;
                $retorno = $tblPromoIt->save($item_promo)?true:false;
            }
        }

        $tmpPromo->deleteAll(array('1 = 1'));
        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que adiciona um item em uma promocao
	*
	* @return boolean
	*/
    public function promotionItemAdd(){
        $retorno = false;
        $tmpPromo  = TableRegistry::get('TmpPromocao');
        $produto = TableRegistry::get('SysProduto')->get($this->request->getData("txtPromoProductId"));
        $CONDICOES = $this->request->getData("chkCondicaoPagamento");

        //verifica se foram definidas condicoes de pagamento
        if(count($CONDICOES)>0){
            for($i=0;$i<count($CONDICOES);$i++){
                if($tmpPromo->find()->where(['IDPRODUTO' => $produto->IDPRODUTO,'IDCONDICAOPAGAMENTO' => $CONDICOES[$i]])->count()==0){
                    $item_promo = $tmpPromo->newEntity();
                    $item_promo->IDPRODUTO      = $produto->IDPRODUTO;
                    $item_promo->NOME           = $produto->NOME;
                    $item_promo->SKU            = $produto->SKU;
                    $item_promo->PRECO_VENDA    = $produto->PRECO_VENDA;
                    $item_promo->IDCONDICAOPAGAMENTO = $CONDICOES[$i];
                    $item_promo->CONDICAO_PAGAMENTO  = TableRegistry::get('SysCondicaoPagamento')->get($CONDICOES[$i])->NOME;
                    $item_promo->PRECO_PROMO         = $this->request->getData("txtPromoProductPrice");
                    $retorno = $tmpPromo->save($item_promo)?true:false;
                }
            }
        }else{
            $item_promo = $tmpPromo->newEntity();
            $item_promo->IDPRODUTO      = $produto->IDPRODUTO;
            $item_promo->NOME           = $produto->NOME;
            $item_promo->SKU            = $produto->SKU;
            $item_promo->PRECO_VENDA    = $produto->PRECO_VENDA;
            $item_promo->IDCONDICAOPAGAMENTO = 0;
            $item_promo->CONDICAO_PAGAMENTO  = "QUALQUER";
            $item_promo->PRECO_PROMO         = $this->request->getData("txtPromoProductPrice");
            $retorno = $tmpPromo->save($item_promo)?true:false;
        }
        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que remove um item de uma promocao
	* @param int $_idProduto Codigo do produto
	* @param int $_idCondPagamento codigo da condicao de pagamento
	*
	* @return
	*/
    public function promotionItemDel($_idProduto,$_idCondPagamento){
        $tblPromo = TableRegistry::get('TmpPromocao');
        $item = $tblPromo->get(['IDPRODUTO' => $_idProduto,'IDCONDICAOPAGAMENTO' => $_idCondPagamento]);
        return $this->response->withStringBody( $tblPromo->delete($item)?true:false );
    }

    /**
	* Metodo que busca todos os itens de uma promocao
	* @param int $_idPromocao codigo da promocao
	*
	* @return
	*/
    public function promotionItens($_idPromocao=""){
        $tmpPromo = TableRegistry::get('TmpPromocao');
        $tblItem  = TableRegistry::get('LojPromocaoProduto');
        $tblProd  = TableRegistry::get('SysProduto');

        //verifica se ha itens na tabeal temporaria
        if($tmpPromo->find()->count()==0){
            //varre os itens da promocao
            foreach($tblItem->find()->where(['IDPROMOCAO' => $_idPromocao]) as $promo){

                $condicao = TableRegistry::get('SysCondicaoPagamento')->find()->where(['IDCONDICAOPAGAMENTO' => $promo->IDCONDICAOPAGAMENTO]);

                $produto = $tblProd->get($promo->IDPRODUTO);
                $promo_item = $tmpPromo->newEntity();
                $promo_item->IDPRODUTO           = $promo->IDPRODUTO;
                $promo_item->IDCONDICAOPAGAMENTO = $promo->IDCONDICAOPAGAMENTO;
                $promo_item->CONDICAO_PAGAMENTO  = ($condicao->count()>0)?$condicao->first()->NOME:"QUALQUER";
                $promo_item->PRECO_PROMO         = $promo->PRECO_PROMO;
                $promo_item->NOME                = $produto->NOME;
                $promo_item->SKU                 = $produto->SKU;
                $promo_item->PRECO_VENDA         = $produto->PRECO_VENDA;
                $tmpPromo->save($promo_item);
            }
        }

        $this->set('itens',$tmpPromo->find());
    }

    /**
	* Metodo que verifica se um item estah ou nao
	* dentro da promocao
	*
	* @return objeto contendo o item ou nul
	*/
    public function promoItenIn(){
        $user = $this->Auth->user();

        $produto = $this->request->getData("IDPRODUTO");
        $promocao = TableRegistry::get('LojPromocao')->find()
            ->select(['IDCONDICAOPAGAMENTO' =>'PP.IDCONDICAOPAGAMENTO','PRECO_PROMO' => 'PP.PRECO_PROMO'])
            ->join([
                'table' => 'loj_promocao_produto',
                'alias' => 'PP',
                'type'  => 'INNER',
                'conditions' => 'LojPromocao.IDPROMOCAO=PP.IDPROMOCAO'
            ])->where(['PP.IDPRODUTO' => $produto])
            ->where(function ($exp) {
                return $exp->lte('LojPromocao.DATA_INICIAL', date("Y-m-d"));
            })
            ->where(function ($exp){
                return $exp->gte('LojPromocao.DATA_FINAL', date("Y-m-d"));
            })
            ->where(['IDLOJA' => $user['storeid']]);
        return $this->response->withStringBody( $promocao->count()>0?json_encode( $promocao->first() ):NULL );
    }

    /**
	* Metodo que busca o preco de um produto promocional
	*
	* @return float
	*/
    public function productPromotionalGetPrice(){

        $user = $this->Auth->user();

        $return = TableRegistry::get('LojPromocao')->find()
            ->select(['PRECO_PROMO' => 'PP.PRECO_PROMO'])
            ->join([
                'table' => 'loj_promocao_produto',
                'alias' => 'PP',
                'type'  => 'INNER',
                'conditions' => 'LojPromocao.IDPROMOCAO=PP.IDPROMOCAO'
            ])
            ->where(function ($exp){ return $exp->gte('DATE(LojPromocao.DATA_FINAL)',date("Y-m-d")); } )
            ->where(function ($exp){ return $exp->lte('DATE(LojPromocao.DATA_INICIAL)',date("Y-m-d")); })
            ->where(['IDLOJA' => $user['storeid'],'PP.IDPRODUTO' => $this->request->getData("txtIdProdutoPromo")]);
        if($this->request->getData("cbCondPagamentoPromo")!=0){
            $return->where(['PP.IDCONDICAOPAGAMENTO' => $this->request->getData("cbCondPagamentoPromo")]);
        }

        return $this->response->withStringBody( $return->first()->PRECO_PROMO );
    }

    /****************** PEDIDOS DE VENDA ***********************/
	/*
	OBS: OS PEDIDOS DE VENDA NAO FORAM IMPLENTADOS NESSE SISTEMA
	POIS NAO EH O OBJETIVO, O CODIGO FICARAH NA CLASSE, MAS NAO
	SERAH USADO
	*/
    public function list_sale_order(){
        $this->load->model("retail/Pedido_venda");
        $this->load->model("system/Loja");
        $this->load->helper("sistema");

        $where = null;
        $order = null;
        $limit = null;

        //filtros
        $filtroData    = $this->input->get("txtPedidoData");
        $filtroCliente = $this->input->get("txtPedidoCliente");
        $filtroFone    = $this->input->get("txtPedidoFone");
        $filtroStatus  = $this->input->get("cbPedidoStatus");

        if($filtroData!=""){
            $data['filtroData'] = $filtroData;
            $where['key'][] = "ped.DATA_PEDIDO";
            $where['operator'][] = "=";
            $where['value'][] = "'".dateToDatabase($filtroData)."'";
        }else{
            $data['filtroData'] = "";
        }

        if($filtroCliente!=""){
            $data['filtroCliente'] = $filtroCliente;
            $where['key'][] = "cli.NOME";
            $where['operator'][] = "like";
            $where['value'][] = "'%".$filtroCliente."%'";
        }else{
            $data['filtroCliente'] = "";
        }

        if($filtroFone!=""){
            $data['filtroFone'] = $filtroFone;
            $where['key'][] = "cli.TELEFONE";
            $where['operator'][] = "like";
            $where['value'][] = "'%".$filtroFone."%'";
        }else{
            $data['filtroFone'] = "";
        }

        if($filtroStatus!=""){
            $data['filtroStatus'] = $filtroStatus;
            $where['key'][] = "sol.IDLOJA";
            $where['operator'][] = "=";
            $where['value'][] = $filtroStatus;
        }else{
            $data['filtroStatus'] = "";
        }

        $order["key"][] = "DATA_PEDIDO";
        $order["order"][] = "ASC";

        $this->configPag['total_rows'] = $this->Pedido_venda->count($where);
        $this->configPag['base_url'] = base_url('index.php/retail/'.__FUNCTION__);

        $this->pagination->initialize($this->configPag);

        $data['page'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $limit['page'] = $data['page'];
        $limit['rows'] = $this->configPag['per_page'];

        //call the model function to get the department data
        $data['requestlist'] = $this->Pedido_venda->get($where,$order,$limit);

        $data['pagination'] = $this->pagination->create_links();
        $data['storelist'] = $this->Loja->get();

        $this->load->view("templates/header");
        $this->load->view("pages/retail/view_".__FUNCTION__,$data);
        $this->load->view("templates/footer");
    }

    public function create_sale_order(){
        $this->load->view("templates/header");
        $this->load->view("pages/retail/view_sale_order");
        $this->load->view("templates/footer");
    }

    public function save_sale_order_data(){
        $this->load->model("retail/Pedido_venda");
        $this->load->model("stock/Produto");

        $this->Pedido_venda->IDPEDIDOVENDA = $this->request->getData("IDPEDIDOVENDA");
        $this->Pedido_venda->DATA_PEDIDO   = date("Y-m-d H:i:s");
        $this->Pedido_venda->TOTAL_PAGO    = str_replace(",", "", $this->request->getData("VALOR_PAGO"));
        $this->Pedido_venda->STATUS        = 'P';
        $this->Pedido_venda->CUPOM         = $this->_gen_sale_order_cupom($this->Pedido_venda->TOTAL_PAGO);

        $itens = $this->request->getData("IDITENS");
        $qtdes = $this->request->getData("QUANTIDADE");

        $nitem = null;

        for($i=0;$i<count($itens);$i++){
            $it = new stdClass();
            $it->IDPRODUTO    = $itens[$i];
            $this->Produto->IDPRODUTO = $it->IDPRODUTO;
            $this->Produto->get_by_id();
            $it->NOME_PRODUTO = $this->Produto->NOME;
            $it->SKU_PRODUTO  = $this->Produto->SKU;
            $it->QUANTIDADE   = $qtdes[$i];

            $nitem[] = $it;
        }

        $this->Pedido_venda->ITENS = $nitem;

        if($this->Pedido_venda->save_entry()){
            echo $this->Pedido_venda->CUPOM;
        }
        else{
            echo "0";
        }
    }

    /****************************** CUPONS ************************************/
    /**
	* Metodo que exibe os filtros da listagem de cupons
	*
	* @return html
	*/
    public function cuponFilter(){

        $this->Filter->addFilter("Descri&ccedil;&atilde;o","TXT_CUPOM_SEARCH_NOME","text");

        $ops = array();

        $optp = new \stdClass();
        $optp->key  = "Presente";
        $optp->value = "P";
        $ops[] = $optp;

        $optd = new \stdClass();
        $optd->key  = "Desconto";
        $optd->value = "D";
        $ops[] = $optd;

        $opta = new \stdClass();
        $opta->key  = "Antecipa&ccedil;&atilde;o de Venda";
        $opta->value = "A";
        $ops[] = $opta;

        $this->Filter->addFilter("Tipo","CB_CUPOM_SEARCH_TYPE","combo",$ops);

        $used = array();

        $uses = new \stdClass();
        $uses->key  = "Sim";
        $uses->value = "S";
        $used[] = $uses;

        $usen = new \stdClass();
        $usen->key  = "N&atilde;o";
        $usen->value = "N";
        $used[] = $usen;

        $usee = new \stdClass();
        $usee->key  = "Expirado";
        $usee->value = "E";
        $used[] = $usee;
        $this->Filter->addFilter("Utilizado","CB_CUPOM_SEARCH_USED","combo",$used);

        $this->Filter->addFilter("Data de Crica&ccedil;&atilde;o","TXT_CUPOM_SEARCH_DATECREATED","date");

        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    /**
	* Metodo que exibe a pagina principal da lisstagem de cupons
	*
	* @return null
	*/
    public function cupon(){
    	$this->set('title',"Listagem de Cupons");
        $this->set('url_filter','/retail/cupon_filter');
        $this->set('url_data','/retail/cupon_data');
    }

    /**
	* Metodo que trata a busca dos dados da listagem
	*
	* @return null
	*/
    public function cuponData(){
        $tblCupom = TableRegistry::get('SysCupom');

        $query = $tblCupom->find();
        $query->select(['DESCRICAO', 'DATA_CRIACAO', 'UTILIZADO', 'TIPO_CUPOM', 'CODIGO', 'IDCUPOM', 'TIPO_VALOR', 'VALOR']);

        if($this->request->getData("TXT_CUPOM_SEARCH_NOME")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('DESCRICAO','%'.$this->request->getData('TXT_CUPOM_SEARCH_NOME').'%');
            })->orWhere(function ($exp,$q){
                return $exp->like('OBSERVACAO','%'.$this->request->getData('TXT_CUPOM_SEARCH_NOME').'%');
            });
        }

        if($this->request->getData("CB_CUPOM_SEARCH_TYPE")!=""){
            $query->where(['TIPO_CUPOM' => $this->request->getData("CB_CUPOM_SEARCH_TYPE")]);
        }

        if($this->request->getData("CB_CUPOM_SEARCH_USED")!=""){
            $query->where(['UTILIZADO' => $this->request->getData("CB_CUPOM_SEARCH_USED")]);
        }

        if($this->request->getData("TXT_CUPOM_SEARCH_DATECREATED")!=""){
			$query->where(['DATE(DATA_CRIACAO)' => substr($this->request->getData("TXT_CUPOM_SEARCH_DATECREATED"), 6,4)."-".substr($this->request->getData("TXT_CUPOM_SEARCH_DATECREATED"),3,2)."-".substr($this->request->getData("TXT_CUPOM_SEARCH_DATECREATED"),0,2) ]);
		}

        $this->set('data_list',$this->paginate($query,['limit' => 10]));
    }

    /**
	* Metodo que gera cupons conforme parametrizacao na tela de cupons
	*
	* @return boolean
	*/
    public function cuponGen(){

        $retorno = false;
        $tblCupom = TableRegistry::get('SysCupom');

        $time = new Time();

        for($i=0;$i<(int)$this->request->getData("QUANTIDADE");$i++){
            $cupom = $tblCupom->newEntity();
            $cupom->DESCRICAO       = mb_strtoupper($this->request->getData("DESCRICAO"));
            $cupom->TIPO_CUPOM      = $this->request->getData("TIPO_CUPOM");
            $cupom->VALOR           = $this->request->getData("VALOR");
            $cupom->TIPO_VALOR      = $this->request->getData("TIPO_VALOR");
            $cupom->DATA_CRIACAO    = date("Y-m-d");
            $cupom->DATA_UTILIZACAO = NULL;
            $cupom->DATA_VALIDADE   = (($this->request->getData("DATA_VALIDADE")!="")?$time->parseDate($this->request->getData("DATA_VALIDADE"))->i18nFormat("yyyy-MM-dd"):NULL);
            $cupom->UTILIZADO       = $this->request->getData("UTILIZADO");
            $cupom->OBSERVACAO      = (($this->request->getData("OBSERVACAO")!="")?mb_strtoupper($this->request->getData("OBSERVACAO")):NULL);
            $cupom->CODIGO          = $this->cuponGenCode($this->request->getData("TIPO_CUPOM"),$this->request->getData("VALOR"));
            $retorno = $tblCupom->save($cupom)?true:false;
        }

        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que gera os codigos do cupons
	* @param string $_tipo (P = Vale Presente, D = Desconto,  A = Antecipacao de Venda)
	* @param undefined $_valor
	*
	* @return string codigo do cupon
	*/
    private function cuponGenCode($_tipo,$_valor){
        $new_valor = str_replace(",","",$_valor);
        $new_valor = str_replace(".", "", $new_valor);

        switch($_tipo){
            case 'P':{//Vale Presente = YYYY00000RRRR : 13
                return date("Y").str_pad($new_valor,5,'0',STR_PAD_LEFT).rand(1000,9999);
            }break;
            case 'D':{//Desconto = DSCYYYYMM0000RRRRR : 18
                return "DSC".date("Ym").str_pad($new_valor, 4, '0', STR_PAD_LEFT).rand(10000,99999);
            }break;
            case 'A':{//Antecipacao de vendas YYYYmmddHHiiss000000 : 20
                return date("YmdHis").str_pad($new_valor, 6, '0', STR_PAD_LEFT);
            }break;
        }
    }

    /**
	*
	*
	* @return
	*/
    public function cuponTagExport(){

        $cupons = $this->request->getData("check_list");
        $time = new Time();

        $download = null;
        for($i=0;$i<count($cupons);$i++){
            $cupom = TableRegistry::get('SysCupom')->get($cupons[$i]);

            $formattedValue = ($cupom->TIPO_VALOR=="$")? Number::currency($cupom->VALOR,"BRL") : $cupom->VALOR."%";

            $download.= ($i+1).";".$cupom->CODIGO.";".$cupom->DESCRICAO.";".$formattedValue.";".(($cupom->DATA_VALIDADE!=NULL)?$time->parseDate($this->request->getData("DATA_VALIDADE"))->i18nFormat("yyyy-MM-dd"):"").";\r\n";
        }

        $response = $this->response;

        $response = $response->withStringBody($download);
        $response = $response->withType("txt");
        $response = $response->withDownload("cupons-".date("dmY").".txt");
        return $response;
    }

    /**
	* Metodo que exibe os dados de um cupom
	* @param int $_idCupom codigo do cupom
	*
	* @return
	*/
    public function cuponShow($_idCupom){
        $this->viewBuilder()->layout('gallery');
        $this->set('cupom',TableRegistry::get('SysCupom')->get($_idCupom));
    }

    /****************************** PARCEIROS ************************************/

    public function partnerFilter(){
        $this->Filter->addFilter("Nome","TXT_PARTNER_SEARCH_NAME","text");

        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    public function partner(){
    	$this->set('title',"Parceiros");

        $this->set('url_filter','/retail/partner_filter');
        $this->set('url_data','/retail/partner_data');
    }

    public function partnerData(){
        $tblPartner = TableRegistry::get('SysParceiro');

        $query = $tblPartner->find();
        $query->select(['IDPARCEIRO','NOME','PERC_DESCONTO','CODIGO_CUPOM']);
        if($this->request->getData("TXT_PARTNER_SEARCH_NAME")!=""){
            $query->where(function ($exp){
                return $exp->like('NOME','%'.$this->request->getData('TXT_PARTNER_SEARCH_NAME').'%');
            });
        }

        $this->Paginator->paginate($query,['limit' => 10]);

        $this->set('data_list',$this->paginate($query));
    }

    public function partnerCreate($_idParceiro=""){
        $this->set('is_mobile', $this->request->is('mobile'));

        if($_idParceiro!=""){
            $this->set('parceiro',TableRegistry::get('SysParceiro')->get($_idParceiro));
        }
    }

    public function partnerSave(){
        $tblPartner = TableRegistry::get('SysParceiro');
        if($this->request->getData("IDPARCEIRO")!=""){
            $parceiro = $tblPartner->get($this->request->getData("IDPARCEIRO"));
        }else{
            $parceiro = $tblPartner->newEntity();
        }

        $time = new Time();

        $parceiro->NOME          = mb_strtoupper($this->request->getData("NOME"));
        $parceiro->CODIGO_CUPOM  = mb_strtoupper($this->request->getData("CODIGO_CUPOM"));
        $parceiro->PERC_DESCONTO = $this->request->getData("PERC_DESCONTO");
        $parceiro->DATA_INICIO   = $time->parseDate($this->request->getData("DATA_INICIO"))->i18nFormat("yyyy-MM-dd");
        $parceiro->DATA_FIM      = ($this->request->getData("DATA_FIM")!="")?$time->parseDate($this->request->getData("DATA_FIM"))->i18nFormat("yyyy-MM-dd"):"";
        return $this->response->withStringBody( $tblPartner->save($parceiro)?true:false );
    }
}
