<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;
use Cake\Auth\DefaultPasswordHasher;
use \Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\Datasource\ConnectionManager;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. */

/**
 * Description of UserController
 *
 * @author hestilo
 */
class StockController extends AppController{

	public function initialize() {
        parent::initialize();
        $this->loadComponent("Paginator");

        $this->loadComponent("Filter");
        ob_start("ob_gzhandler");
    }

	public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
    }

	/**/
	public function dialogNcmFind(){
		$this->autoRender = false;
        $tblNCM = TableRegistry::get('SysNcm');

        $nome   = $this->request->getData("NOME_NCM");
        $codigo = $this->request->getData("CODIG_NCM");

        $ncms = $tblNCM->find();
        $ncms->select(['CODIGO_NCM','NOME']);

        if($nome!=""){
            $ncms->where(['NOME LIKE' => '%'.$nome.'%']);
        }

        if($codigo!=""){
            $ncms->where(['CODIGO_NCM LIKE' => '%'.$codigo.'%']);
        }

        $retorno = "";
        foreach($ncms as $ncm){
            $retorno .= "<tr>";
            $retorno.= "<td class='text-center' style='width:45px!important;'><input type='radio' id='rdNCM[]' name='rdNCM[]' value='".$ncm->CODIGO_NCM."'></td>";
            $retorno.= "<td style='width:299px!important;'>".$ncm->CODIGO_NCM."</td>";
            $retorno.= "<td style='width:300px!important;'>".$ncm->NOME."</td>";
            $retorno.= "<td style='width:122px!important;'>&nbsp;</td>";
            $retorno.= "</tr>";
        }

        return $this->response->withStringBody( ($retorno=="")?"<tr><td colspan='4' class='text-center'>Nenhum NCM encontrado!</td></tr>":$retorno );
    }

	/******************TIPO DE PRODUTO ***************************/
	public function productType(){
		$this->set('title',"Tipos de Produtos");
		$this->set('url_filter','/stock/product_type_filter');
        $this->set('url_data','/stock/product_type_data');
	}

	public function productTypeFilter(){
		$this->autoRender = false;

        $this->Filter->addFilter("Nome","TXT_PRODUCT_TYPE_SEARCH_NAME","text");

        return $this->response->withStringBody( $this->Filter->mountFilters() );
	}

	public function productTypeData(){
		$query = TableRegistry::get('SysProdutoTipo')->find();

        $query->select(['IDPRODUTOTIPO','DESCRICAO']);

        if($this->request->getData("TXT_PRODUCT_TYPE_SEARCH_NAME")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('DESCRICAO','%'.$this->request->getData('TXT_PRODUCT_TYPE_SEARCH_NAME').'%');
            });
        }

        $this->set('product_type_list',$this->paginate($query,['limit' => 10]));
	}

	public function productTypeSave(){
		$this->autoRender = false;

		$retorno = new \stdClass();
		$retorno->SALVOU = false;
		$retorno->IDPRODUTOTIPO = NULL;

		$tblProdType = TableRegistry::get("SysProdutoTipo");

		if($this->request->getData("IDPRODUTOTIPO")!=""){
            $prodType = $tblProdType->get($this->request->getData("IDPRODUTOTIPO"));
        }
        else{
            $prodType = $tblProdType->newEntity();
        }

		$prodType->DESCRICAO = mb_strtoupper($this->request->getData("DESCRICAO"));

		if($tblProdType->save($prodType)){
			$retorno->SALVOU = true;
			$retorno->IDPRODUTOTIPO = $prodType->IDPRODUTOTIPO;
		}

		return $this->response->withStringBody( json_encode($retorno) );
	}

	public function productTypeCreate($_id=""){
		if($_id!=""){
			$this->set('tipo_produto',TableRegistry::get('SysProdutoTipo')->get($_id));
		}
	}

	public function productTypeGetAttributes($_idProdutoTipo=""){
        $this->autoRender = false;

        if($_idProdutoTipo!=""){

            $tblAttr  = TableRegistry::get('SysAtributo');
            $tblAttro = TableRegistry::get('SysAtributoOpcao');

            $atributos = $tblAttr->find()->where(['IDPRODUTOTIPO' => $_idProdutoTipo]);

            if($atributos->count()>0){
				$i = 0;
				$html = "";
                foreach($atributos as $atributo){

					if($i%2==0){
						$html .= "<div class='row'>";
					}
                    $html.= "<div class='form-group col-md-6'>";
                    $html.= "<label for='lst_atributo[".$atributo->IDATRIBUTO."]'>".$atributo->NOME."</label>";
                    $html.= "<select name='lst_atributo[".$atributo->IDATRIBUTO."]' id='lst_atributo[".$atributo->IDATRIBUTO."]' class='form-control' required>";

					$html.= "<option value=''>&laquo; Selecione &raquo;</option>";
                    foreach($tblAttro->find()->where(['IDATRIBUTO' => $atributo->IDATRIBUTO])->order(['TEXTO' => 'ASC']) as $opcao){
                        $html.= "<option value='".$opcao->VALOR."'>".$opcao->TEXTO."</option>";
                    }
                    $html.= "</select>";
                    $html.= "</div>";

					if($i%2==0){
						$html.= "</div>";
					}
					$i++;
                }
            }
        }
        else{
            $html=  "";
        }

		return $this->response->withStringBody( $html );
    }

	/******************ATRIBUTO***************************/
	public function attributeSave(){
		$this->autoRender = false;

		$retorno = false;

		$tblAttr = TableRegistry::get("SysAtributo");
		if($this->request->getData("IDATRIBUTO")!=""){
			$attr = $tblAttr->get($this->request->getData("IDATRIBUTO"));
		}
		else{
			$attr = $tblAttr->newEntity();
		}
		$attr->IDPRODUTOTIPO = $this->request->getData("IDPRODUTOTIPO");
		$attr->NOME = $this->request->getData("NOME");

		if( $tblAttr->save($attr)){
			$tblAttrOpt = TableRegistry::get("SysAtributoOpcao");

			$tblAttrOpt->deleteAll(['IDATRIBUTO' => $this->request->getData("IDATRIBUTO")]);

			$values = explode("\n",$this->request->getData("DATA_ATRIBUTO"));
			foreach($values as $value){
				if(trim($value)!=""){
					$resp = explode("=",$value);

					$opt = $tblAttrOpt->newEntity();
					$opt->IDATRIBUTO = $attr->IDATRIBUTO;
					$opt->VALOR      = trim($resp[1]);
					$opt->TEXTO      = ucfirst($resp[0]);

					$retorno = $tblAttrOpt->save($opt)?true:false;
				}
			}
		}

		return $this->response->withStringBody( $retorno );
	}

	public function attributeDelete(){
		$tblAttr = TableRegistry::get('SysAtributo');
		$attr = $tblAttr->get($this->request->getData("IDATRIBUTO"));

		$retorno = false;

		if($tblAttr->delete($attr)){
			$retorno = TableRegistry::get("SysAtributoOpcao")->deleteAll(["IDATRIBUTO" => $this->request->getData("IDATRIBUTO")]);
		}

		return $this->response->withStringBody( $retorno );
	}

	public function attributeGetBasket(){
		$this->autoRender = false;

		$html = "<table class='table table-striped'>";
		$html.= "<thead>";
		$html.= "<tr>";
		$html.= "<th>Nome</th>";
		$html.= "<th style='width:200px!important;' class='text-center'>A&ccedil;&atilde;o</th>";
		$html.= "</tr>";
		$html.= "</thead>";

		$tblAttr   = TableRegistry::get("SysAtributo");
		$atributos = $tblAttr->find()->select(["IDATRIBUTO","NOME"])->Where(['IDPRODUTOTIPO' => $this->request->getData("IDPRODUTOTIPO")]);

		$html.= "<tbody>";
		if($atributos->count()>0){
			foreach($atributos as $atributo){
				$html.= "<tr>";
				$html.= "<td>".$atributo->NOME."</td>";
				$html.= "<td class='text-center'><a href='javascript:editAttribute(".$atributo->IDATRIBUTO.")' class='btn btn-light btn-sm'><i class='fas fa-edit'></i> Editar</a> <a href='javascript:delAttribute(".$atributo->IDATRIBUTO.")' class='btn btn-danger btn-sm'><i class='fas fa-trash-alt'></i> Excluir</a></td>";
			}
		}
		$html.= "</tbody>";

		return $this->response->withStringBody( $html );
	}

	public function attributeGet(){
		$this->autoRender = false;

		$retorno = new \stdClass();
		$retorno->IDATRIBUTO = NULL;
		$retorno->NOME       = NULL;
		$retorno->VALORES    = NULL;

		$tblAttr  = TableRegistry::get("SysAtributo");
		$atributo = $tblAttr->get($this->request->getData("IDATRIBUTO"));
		$opcoes   = TableRegistry::get("SysAtributoOpcao")->find()->select(['VALOR','TEXTO'])->Where(['IDATRIBUTO' => $atributo->IDATRIBUTO]);

		$retorno->IDATRIBUTO = $atributo->IDATRIBUTO;
		$retorno->NOME       = $atributo->NOME;

		foreach($opcoes as $opc){
			$retorno->VALORES.= $opc->TEXTO." = ".$opc->VALOR."\n";
		}

		return $this->response->withStringBody( json_encode($retorno) );
	}

	/*****************************CATEGORIA********************************/
    private function categoryGetOrdered(){
        $sql = "SELECT * FROM( SELECT CATEGORIA_PAI,IDCATEGORIA, NOME, IDCATEGORIA as SORT1,"
                . "1 AS SORT2 FROM sys_categoria WHERE CATEGORIA_PAI IS NULL UNION SELECT "
                . "CATEGORIA_PAI,IDCATEGORIA, CONCAT(' - ',NOME) AS NOME, CATEGORIA_PAI as SORT1, "
                . "NOME AS SORT2 FROM sys_categoria WHERE CATEGORIA_PAI IS NOT NULL ORDER BY NOME ASC ) "
                . "AS V ORDER BY SORT1,SORT2,IDCATEGORIA";
        $connection = ConnectionManager::get('default');
        $results = $connection->execute($sql)->fetchAll('assoc');

        return $results;
    }
    public function categoryFilter(){
        $this->autoRender = false;

        $this->Filter->addFilter("Nome","TXT_CATEGORY_SEARCH_NAME","text");

        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    public function category(){
        $tblOpcao = TableRegistry::get('SysOpcao');

        $this->set('title',"Categorias");
        $this->set('url_filter','/stock/category_filter');
        $this->set('url_data','/stock/category_data');
    }

    public function categoryData(){
        $tblCategory = TableRegistry::get('SysCategoria');

        $query = $tblCategory->find();
        $query->select(['IDCATEGORIA','NOME']);

        if($this->request->getData("TXT_CATEGORY_SEARCH_NAME")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('NOME','%'.$this->request->getData('TXT_CATEGORY_SEARCH_NAME').'%');
            });
        }

        $this->set('category_list',$this->paginate($query,['limit' => 10]));
    }

    public function categoryCreate($_idCategoria=""){
        $tblCategory = TableRegistry::get('SysCategoria');
        if($_idCategoria!=""){
            $categoria = $tblCategory->get($_idCategoria);

            $this->set('categoria',$categoria);
        }

        $this->set('categoria_pai',$tblCategory->find()->where(function($exp,$q){ return $exp->isNull('CATEGORIA_PAI'); }));
    }

    public function categorySave(){
        $retorno = false;
        //busca as informacoes das categorias
        $tblCategory = TableRegistry::get('SysCategoria');

        //evita que tente carregar o template
        $this->autoRender = false;

        if($this->request->getData("IDCATEGORIA")!=""){
            $categoria = $tblCategory->get($this->request->getData("IDCATEGORIA"));
        }
        else{
            $categoria = $tblCategory->newEntity();
        }

        $categoria->IDCATEGORIA   = $this->request->getData("IDCATEGORIA");
        $categoria->CATEGORIA_PAI = ($this->request->getData("CATEGORIA_PAI")!="")?$this->request->getData("CATEGORIA_PAI"):NULL;
        $categoria->NOME          = mb_strtoupper($this->request->getData("NOME"));

        return $this->response->withStringBody( $tblCategory->save($categoria)?true:false );
    }

	/****************************REGRA DE SKU******************************/
    public function skuRuleFilter(){
        $this->autoRender = false;

        $this->Filter->addFilter("Tipo de Produto","TXT_PRODUCT_TYPE_SEARCH_NAME","text");
        $this->Filter->addFilter("Formato","TXT_SKU_RULE_SEARCH_FORMAT","text");

        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    public function skuRule(){
		$this->set('title',"Regras de SKU");
        $this->set('url_filter','/stock/sku_rule_filter');
        $this->set('url_data','/stock/sku_rule_data');
    }

    public function skuRuleData(){
        $tblSkuRule = TableRegistry::get('SysRegraSku');

        $query = $tblSkuRule->find()
        	->select(['IDREGRASKU','DESCRICAO_REGRA' => 'pt.DESCRICAO','IDPRODUTOTIPO' => 'pt.IDPRODUTOTIPO','FORMATO_REGRA'])
        	->join([
            'table' => 'sys_produto_tipo',
            'alias' => 'pt',
            'type' => 'INNER',
            'conditions' => 'pt.IDPRODUTOTIPO = SysRegraSku.IDPRODUTOTIPO'
        ]);

        if($this->request->getData("TXT_PRODUCT_TYPE_SEARCH_NAME")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('pt.DESCRICAO','%'.$this->request->getData('TXT_PRODUCT_TYPE_SEARCH_NAME').'%');
            });
        }

        if($this->request->getData("TXT_SKU_RULE_SEARCH_FORMAT")!=""){
            $query->where(function($exp,$q){
                return $exp->like('FORMATO_REGRA','%'.$this->request->getData('TXT_SKU_RULE_SEARCH_FORMAT').'%');
            });
        }

        $this->set('sku_rule_list',$this->paginate($query,['limit' => 10]));
    }

    public function skuRuleCreate($_idRegraSKU="",$_idProdutoTipo=""){
        $tblProductType = TableRegistry::get('SysProdutoTipo');
        $this->set('produto_tipo',$tblProductType->find());

        $tblRuleSku = TableRegistry::get('SysRegraSku');
        if($_idRegraSKU!=""){
            $regra = $tblRuleSku->get(['IDREGRASKU' =>$_idRegraSKU, 'IDPRODUTOTIPO' => $_idProdutoTipo]);

            $this->set('regra_sku',$regra);
        }
    }

	/**
	* Metodo que salva uma regra de SKU
	*
	* @return boolean
	*/
    public function skuRuleSave(){
        $tblRule = TableRegistry::get("SysRegraSku");
        $this->autoRender = false;

        if($this->request->getData("IDREGRASKU")!=""){
            $regra_sku = $tblRule->get(["IDREGRASKU" => $this->request->getData("IDREGRASKU"),"IDPRODUTOTIPO" => $this->request->getData("IDPRODUTOTIPO")]);
        }else{
            $regra_sku = $tblRule->newEntity();
        }

        $regra_sku->IDPRODUTOTIPO = $this->request->getData("IDPRODUTOTIPO");
        $regra_sku->REGRA         = $this->request->getData("REGRA");
        $regra_sku->FORMATO_REGRA = mb_strtoupper($this->request->getData("FORMATO_REGRA"));

        return $this->response->withStringBody( $tblRule->save($regra_sku)?true:false );
    }

    /*******************************PRODUTO***************************************/

    /**
     * Realiza a ativacao ou desativacao de um produto no sistema e no site
     * @param char $_status D = Desativado, A = Ativa
     *
     * @return boolean
     */
    public function productChangeStatus($_status){
        $tblProd = TableRegistry::get('SysProduto');
        $retorno = false;

        $produtos = $this->request->getData("check_list");
        for($i=0;$i<count($produtos);$i++){
            $produto = $tblProd->get($produtos[$i]);

            $produto->STATUS = $_status;
            $produto->DESATIVADO_DATA = ($_status=='D')?date("Y-m-d H:i:s"):NULL;
            $retorno = $tblProd->save($produto)?true:false;

        }
        return $this->response->withStringBody( $retorno );
    }

    /**
     * Realiza a alteracao de precos em massa
     * @param decimal $_preco
     *
     * @return boolean
     */
    public function productChangePrice($_preco){
        $tblProd = TableRegistry::get('SysProduto');
        $retorno = false;

        $produtos = $this->request->getData("check_list");
        print_r($this->request->getData());
        for($i=0;$i<count($produtos);$i++){
            $produto = $tblProd->get($produtos[$i]);

            $produto->PRECO_VENDA = $_preco;
            $retorno = $tblProd->save($produto)?true:false;

        }
        return $this->response->withStringBody( $retorno );
    }

	/**
	* Metodo que cria um codigo de barras unico para um produto que nao possui
	*
	* @return string
	*/
	public function productBarcodeGenerate(){
        $this->autoRender = false;
        $tblProd = TableRegistry::get('SysProduto')->find();
		$lastId = 0;
		$lastId = ($tblProd->count()>0)?$tblProd->last()->IDPRODUTO:0;
        return $this->response->withStringBody( '1'.str_pad($lastId+1,(((int)TableRegistry::get('SysOpcao')->get("BARCODE_SIZE")->OPCAO_VALOR)-1),'0',STR_PAD_LEFT) );
    }

    /**
	* Verifica a existencia do codigo de barras no sistema
	* Assim como o SKU o codigo de barras nao deve se repetir no sistema
	* @param string $_barCode codigo de barras para verificar
	*
	* @return boolean
	*/
    public function productBarcodeExist($_barCode=""){
        $this->autoRender = false;
        $tblProd = TableRegistry::get('SysProduto');

		$retorno = ($tblProd->find()->where(['CODIGO_BARRA' => $_barCode])->count()>0)?true:false;

		if($_barCode==""){ $retorno = false; }

        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que realiza a busca de um produto
	* @param int $_idProduto Codigo do produto
	* @param int $_store Codigo da Loja
	*
	* @return json
	*
    public function productFind($_idProduto,$_store=""){
        $prod = TableRegistry::get('SysProduto')->get($_idProduto);

        $this->autoRender = false;

        $produto = new \stdClass();
        $produto = $prod;

        //se existir uma loja para buscar
        if($_store!=""){
        	//verifica se o produto eh simples ou composto
            if($produto->ESTRUTURA=="S"){
            	//busca a quantidade em estoque do produto simples
                $produto->QUANTIDADE_ESTOQUE = TableRegistry::get('LojEstoque')->get(['IDLOJA' => $_store,'IDPRODUTO' => $_idProduto])->QUANTIDADE;
            }else{
            	//busca a disponibilidade dos itens de um produto composto para saber se ele estah disponivel
                $tem_quantidadde = false;
                $itens = TableRegistry::get('SysProdutoItem')->find()->where(['IDPRODUTO' => $_idProduto]);
                foreach($itens as $itProd){
                    $qtde = TableRegistry::get('LojEstoque')->get(['IDLOJA' => $_store,'IDPRODUTO' => $itProd->IDPRODUTO_FILHO])->QUANTIDADE;
                    if($qtde>0){
                        $tem_quantidade = true;
                    }else{
                        $tem_quantidade = false;
                        break;
                    }
                }

                if($tem_quantidade){
                    $produto->QUANTIDADE_ESTOQUE = 0;
                }else{
                    $produto->QUANTIDADE_ESTOQUE = -1;
                }
            }
        }else{
        	//soma a quantidade total do estoque do produto
            $estoques = TableRegistry::get('LojEstoque')->find()->where(['IDPRODUTO' => $_idProduto]);
            foreach($estoques as $estoque){
                $produto->QUANTIDADE_ESTOQUE += $estoque->QUANTIDADE;
            }
        }

        return $this->response->withStringBody( json_encode($produto) );
    }*/

    /**
	* Metodo que busca sa informacoes de um determinado produto
	* @param string $_id se for um codigo de barras serah uma string, senao serah um int
	* @param string $_typeId tipo do id de busca (ID ou BARCODE)
	* @param int $_store id da loja
	*
	* @return
	*/
    public function productGetInfo($_id,$_typeId,$_store=""){

        //se o tipo for ID, entao busca as informacoes com base no id do produto
        if($_typeId=="ID"){
            $prod = TableRegistry::get('SysProduto')->get($_id);
        }else{
        	//se o tamanho do ID for igual ao codigo de barras entao define a variavel codigo com o ID.
            if(strlen($_id)<=(int)TableRegistry::get('SysOpcao')->get("BARCODE_SIZE")->OPCAO_VALOR){
                $codigo = $_id;
            }else{
            	//se o tamanho do ID superar o tamanho do codigo de barras entao eh ignorado o primeiro caracter (bug)
                $codigo = substr($_id,1,TableRegistry::get('SysOpcao')->get("BARCODE_SIZE")->OPCAO_VALOR);
            }
            //busca as informacoes do produto
            $prod = TableRegistry::get('SysProduto')->find()->where(['CODIGO_BARRA' => $codigo])->first();
        }

        //evita procura por template
        $this->autoRender = false;

        $produto = new \stdClass();
        $produto = $prod;

        //se a loja estiver preenchida
        if($_store!=""){
        	//verifica se eh um produto simples ou composto
            if($produto->ESTRUTURA=="S"){
            	//busca as informacoes de estoque da loja
                $produto->QUANTIDADE_ESTOQUE = TableRegistry::get('LojEstoque')->get(['IDLOJA' => $_store,'IDPRODUTO' => $prod->IDPRODUTO])->QUANTIDADE;
            }else{
            	//assume que nao possui quantidade disponivel
                $tem_quantidade = false;

                //varre os itens do produto
                $itens = TableRegistry::get('SysProdutoItem')->find()->where(['IDPRODUTO' => $_id]);
                foreach($itens as $itProd){
                	//para cada item busca a quantidade
                    $qtde = TableRegistry::get('LojEstoque')->get(['IDLOJA' => $_store,'IDPRODUTO' => $itProd->IDPRODUTO_FILHO])->QUANTIDADE;

                    //se for superior a zero, entao existe no estoque
                    if($qtde>0){
                        $tem_quantidade = true;
                    }else{
                    	//se achou alugm produto em falta para o loop
                        $tem_quantidade = false;
                        break;
                    }
                }

                //se tiver quantidade define o estoque como zero
                if($tem_quantidade){
                    $produto->QUANTIDADE_ESTOQUE = 0;
                }else{
                    $produto->QUANTIDADE_ESTOQUE = -1;
                }
            }
        }else{
        	//define a quantidade em estoque para zero
            $produto->QUANTIDADE_ESTOQUE = 0;
            //soma os estoques de todas as lojas
            $estoques = TableRegistry::get('LojEstoque')->find()->where(['IDPRODUTO' => $prod->IDPRODUTO]);
            foreach($estoques as $estoque){
                $produto->QUANTIDADE_ESTOQUE += $estoque->QUANTIDADE;
            }
        }

        //verifica se a loja pode vender produto zerado, se puder adiciona
        //adiciona um item para facilitar as validacoes ja existentes no sistema
        if($_store!=""){
            if($produto->QUANTIDADE_ESTOQUE == 0){
                if(TableRegistry::get('SysLoja')->get($_store)->VENDE_ESTOQUE_ZERADO=="1"){
                    $produto->QUANTIDADE_ESTOQUE = 1;
                }
            }
        }

        return $this->response->withStringBody( json_encode($produto) );
    }

    /**
	* Metodo utilizado pelos Dialogs de produto para realizar busca
	* @param string $_template nome do template que serah usado para exibir o resultado (product_dialog ou product_dialog_multiple)
	*
	* @return null
	*/
    public function productDialog($_template="product_dialog"){
        $tblProduto = TableRegistry::get("SysProduto");

        $idloja = $this->request->getData("IDLOJA");
        $sku    = $this->request->getData("SKU");
        $nome   = $this->request->getData("NOME");

        $produtos = $tblProduto->find()->select(['IDPRODUTO','NOME','SKU','PRECO_VENDA'])
            ->join([
                'table' => 'loj_estoque',
                'alias' => 'E',
                'type'  => 'INNER',
                'conditions' => 'SysProduto.IDPRODUTO = E.IDPRODUTO'
            ])->where(['STATUS' => 'A']);

        if($idloja!=""){
            $produtos->where(['E.IDLOJA' => $idloja]);
        }else{
            $produtos->where(['E.IDLOJA' => TableRegistry::get('SysOpcao')->get("DEFAULT_STORE")->OPCAO_VALOR]);
        }

        if($sku!=""){
            $produtos->where(function($exp,$q) use($sku){
                return $exp->like('SKU','%'.mb_strtoupper($sku).'%');
            });
        }

        if($nome!=""){
            $produtos->where( array("MATCH(SysProduto.NOME, NOME_TAG) AGAINST('+".str_replace(' '," +",$nome)."' IN BOOLEAN MODE)") );
        }

        /*if($qtcon!=""){
            switch($qtcon){
                case ">"  : $produtos->where(function($exp,$q) use($qtde){ return $exp->gt("E.QUANTIDADE",$qtde); }); break;
                case "<"  : $produtos->where(function($exp,$q) use($qtde){ return $exp->lt("E.QUANTIDADE",$qtde); }); break;
                case ">=" : $produtos->where(function($exp,$q) use($qtde){ return $exp->gte("E.QUANTIDADE",$qtde); }); break;
                case "<=" : $produtos->where(function($exp,$q) use($qtde){ return $exp->lte("E.QUANTIDADE",$qtde); }); break;
                case "<>" : $produtos->where(function($exp,$q) use($qtde){ return $exp->notEq("E.QUANTIDADE",$qtde); }); break;
                case "="  : $produtos->where(function($exp,$q) use($qtde){ return $exp->eq("E.QUANTIDADE",$qtde); }); break;
            }
        }else{
            if($qtde!=""){
                $produtos->where(['E.QUANTIDADE' => $qtde]);
            }
        }*/

        $this->set('produtos',$produtos);

        if($_template!=""){
            $this->render($_template);
        }
    }

    /**
	* Metodo que monta o SKU do produto conforme o tipo de produto
	* @param int $_idProdutoTipo
	* @param string $_codFornece
	* @param array $_atributos
	*
	* @return string codigo SKU do produto formatado
	*/
    private function productMountSku($_idProdutoTipo,$_codFornece,$_atributos){
        $tblSku  = TableRegistry::get('SysRegraSku');
        $regra = $tblSku->find()->where(['IDPRODUTOTIPO' => $_idProdutoTipo])->first();

        $sql = $regra->REGRA;

        //verifica se a regra possui SQL
        if($sql!=""){
            $sql = str_replace("%COD_FORNECE",$_codFornece,$sql);

            $i = 1;
            foreach($_atributos as $atributo){
                $sql = str_replace("%A".$i,$atributo->IDATRIBUTO,$sql);
                $sql = str_replace("%V".$i,$atributo->VALOR,$sql);
                $i++;
            }

            $connection = ConnectionManager::get('default');
            $results = $connection->execute($sql)->fetchAll('assoc');

            return $results[0]['SKU'];
        }
        return $_codFornece;
    }

	/**
	* Metodo que verifica se o SKU do produto ja encontra-se no sistema
	* O SKU precisa ser unico, pois eh o identificador de estoque do produto
	* @return boolean
	*/
	public function productSkuExist(){
        $this->autoRender = false;

        //verifica se o produto possui atributos
        if($this->request->getData("ATRIBUTOS")!=""){
            //monta os atributos em objetos e os coloca em um array
            //isso eh necessario para verificar o SKU
            $attrs = array();
            parse_str($this->request->getData("ATRIBUTOS"),$attrs);
            foreach($attrs['lst_atributo'] as $key => $value){
                $atr = new \stdClass();
                $atr->IDATRIBUTO = $key;
                $atr->VALOR      = $value;
                $atributos[]     = $atr;
            }

			//utiliza o metodo productMountSku
            $sku = $this->productMountSku(
                $this->request->getData("IDPRODUTOTIPO"),
                mb_strtoupper($this->request->getData("COD_FORNECE")),
                $atributos
            );
        }else{
            $sku = $this->request->getData("COD_FORNECE");
        }

        //retorna a resposta se ha SKU no banco
        return $this->response->withStringBody( (TableRegistry::get('SysProduto')->find()->where(['SKU' => $sku])->count()>0)?true:false );
    }

	/***************************** PRODUTO SIMPLES ********************************/

	/**
	* Metodo que monta os filtros da listagem do produto
	*
	* @return string
	*/
    public function singleProductFilter(){
        $this->autoRender = false;

        $this->Filter->addFilter("Nome","TXT_PRODUCT_SEARCH_NAME","text");
        $this->Filter->addFilter("SKU","TXT_PRODUCT_SEARCH_SKU","text");

        $pops = array();
        $tblProvider = TableRegistry::get('SysFornecedor');
        $providers = $tblProvider->find()->select(['IDFORNECEDOR','FANTASIA'])->order(['FANTASIA' => 'ASC']);
        foreach($providers as $provider){
            $opt = new \stdClass();
            $opt->key = $provider->FANTASIA;
            $opt->value = $provider->IDFORNECEDOR;
            $pops[] = $opt;
        }
        $this->Filter->addFilter('Fornecedor',"CB_PRODUCT_SEARCH_PROVIDER","combo",$pops);

        $popt = array();
        $tblProdt = TableRegistry::get('SysProdutoTipo');
        $prodtype = $tblProdt->find()->select(['IDPRODUTOTIPO','DESCRICAO'])->order(['DESCRICAO' => 'ASC']);
        foreach($prodtype as $type){
            $opt = new \stdClass();
            $opt->key = $type->DESCRICAO;
            $opt->value = $type->IDPRODUTOTIPO;
            $popt[] = $opt;
        }

        $this->Filter->addFilter('Tipo de Produto',"CB_PRODUCT_SEARCH_TYPE","combo",$popt);
        $this->Filter->addFilter("Apenas o que possuir quantidade em estoque (indiferente da loja)","CHK_PRODUCT_SEARCH_INSTOCK","check","1");
        $this->Filter->addFilter("Apenas o que </strong>N&Atilde;O</strong> possuir quantidade em estoque (exceto loja DEFAULT)","CHK_PRODUCT_SEARCH_NOSTOCK","check","1");
        $this->Filter->addFilter("Apenas o que </strong>N&Atilde;O</strong> possuir NCM ou CSOSN","CHK_PRODUCT_SEARCH_FISCAL","check","1");


		$ords = array();

		$ord1 = new \stdClass();
		$ord1->key   = "Nome";
		$ord1->value = "NOME";
		$ords[] = $ord1;

		$ord2 = new \stdClass();
		$ord2->key   = SKU;
		$ord2->value = "SKU";
		$ords[] = $ord2;

		$ord3 = new \stdClass();
		$ord3->key   = "Pre&ccedil;o de Compra";
		$ord3->value = "PRECO_COMPRA";
		$ords[] = $ord3;

		$ord4 = new \stdClass();
		$ord4->key   = "Pre&ccedil;o de Venda";
		$ord4->value = "PRECO_VENDA";
		$ords[] = $ord4;

		$this->Filter->addOrder($ords);


        return $this->response->withStringBody(  $this->Filter->mountFilters() );
    }

    /**
	* Metodo principal da listagem de produtos
	* Esse metodo define qual a url que buscarah os filtros e qual buscarah os dados
	*
	* @return
	*/
    public function singleProduct(){
		$this->set('title',"Produtos Simples");
        $this->set('url_filter','/stock/single_product_filter');
        $this->set('url_data','/stock/single_product_data');
    }

    /**
	* Metodo que busca os dados da listagem do produto
	*
	* @return null
	*/
    public function singleProductData(){
        $tblProduct = TableRegistry::get('SysProduto');

        $query = $tblProduct->find();
        $query->select(['IDPRODUTO','NOME','SKU','PRECO_VENDA','PRECO_COMPRA','STATUS'])->where(['ESTRUTURA' => 'S']);

        //filtro pelo nome do produto
        if($this->request->getData("TXT_PRODUCT_SEARCH_NAME")!=""){
            $query->where(array("MATCH(NOME, NOME_TAG, SITE_SHORT_DESCRIPTION, SITE_DESCRIPTION, OBSERVACAO) AGAINST('+".str_replace(' '," +",$this->request->getData('TXT_PRODUCT_SEARCH_NAME'))."' IN BOOLEAN MODE)"));
        }
        //filtro pelo SKU do produto
        if($this->request->getData("TXT_PRODUCT_SEARCH_SKU")!=""){
            $query->where(function ($exp){
                return $exp->like('SKU','%'.$this->request->getData('TXT_PRODUCT_SEARCH_SKU').'%');
            });
        }

        //filtro pelo fornecedor do produto
        if($this->request->getData("CB_PRODUCT_SEARCH_PROVIDER")!=""){
            $query->where(['IDFORNECEDOR' => $this->request->getData('CB_PRODUCT_SEARCH_PROVIDER')]);
        }
        //filtro pelo tipo de produto
        if($this->request->getData("CB_PRODUCT_SEARCH_TYPE")!=""){
            $query->where(['IDPRODUTOTIPO' => $this->request->getData('CB_PRODUCT_SEARCH_TYPE')]);
        }
        //filtra apenas os que possuem estoque
        if($this->request->getData("CHK_PRODUCT_SEARCH_INSTOCK")=="1"){
            $subquery = TableRegistry::get('LojEstoque')->find()->select(['IDPRODUTO'])
                    ->where(function($exp){
                        return $exp->notEq('QUANTIDADE','0');
                    });
            $query->where(['IDPRODUTO IN' => $subquery]);
        }
        //filtra apenas os que nao possuem estoque em loja alguma (exceto loja padrao)
        if($this->request->getData("CHK_PRODUCT_SEARCH_NOSTOCK")=="1"){
            $subquery = TableRegistry::get('LojEstoque')->find()->select(['IDPRODUTO'])
                    ->where(function($exp){
                        return $exp->eq('QUANTIDADE',0);
                    })->where(function($exp){
                        return $exp->notEq('IDLOJA',TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR);
                    });
            $query->where(['IDPRODUTO IN' => $subquery]);
        }
        //filtra apenas os produtos que apresentam algum problema na parte fiscal (sem NCM ou CSOSN)
        if($this->request->getData("CHK_PRODUCT_SEARCH_FISCAL")=="1"){
        	$query->where(function($exp){ return $exp->or_(['CSOSN' => ''])->add(['NCM' => '']); });
        }
		//Aqui entra a ordenacao de registros

		//verifica se usarah uma ordenacao personalizada, por padrao a ordenacao eh por status e nome
		if($this->request->getData("CB_ORDER_FIELD")!=""){
			$query->order([$this->request->getData("CB_ORDER_FIELD") => $this->request->getData("CHK_ORDER_DIRECT")]);
		}else{
			$query->order(['STATUS' => 'ASC', 'NOME' => 'ASC']);
		}

        $this->set('product_list',$this->paginate($query,['limit' => 10]));
    }

	/**
	* Metodo que monta a tela de cadastro ou edicao do produto
	* @param int $_idProduto Codigo do produto se for editado
	* @param boolean $_adjust verifica se estah ajustando um produto da NF-e
	*
	* @return null
	*/
    public function singleProductCreate($_idProduto="",$_adjust=false){
        //verifica se ha integracao do sistema com e-commerce
        $tblOption = TableRegistry::get('SysOpcao');

		//define o tipo de produto padrao conforme as opcoes do sistema
		//serve para deixar pre-selecionado em caso de produto novo, assim nao
		//precisa escolher toda vez
        $this->set('default_product_type',$tblOption->get("DEFAULT_PRODUCT_TYPE")->OPCAO_VALOR);

        //busca a lista de fornecedores
        $tblProvider = TableRegistry::get('SysFornecedor');
        $this->set('provider_list',$tblProvider->find()->select(['IDFORNECEDOR','FANTASIA'])->order(['FANTASIA' => 'ASC']));

        //busca a lista de tipos de produtos
        $tblProdType = TableRegistry::get('SysProdutoTipo');
        $this->set('producttype_list',$tblProdType->find()->order(['DESCRICAO' => 'ASC']));

        //busca a lista de categorias ordenada pai -> filho e alfabetico
        $this->set('category_list',$this->categoryGetOrdered());

        //caso haja ajustes vindos do processamento de NF-e
        $this->set('adjust',$_adjust);

        //se for um produto existente no sistema
        if($_idProduto!=""){
            $tblProduct = TableRegistry::get('SysProduto');
            $product = $tblProduct->get($_idProduto);
            $this->set('produto',$product);

            //busca as categorias do produto
            $tblProductCat = TableRegistry::get('SysCategoriaProduto');
            $prodCategory = $tblProductCat->find()->where(['IDPRODUTO' => $_idProduto]);

            $this->set('produto_cat',$prodCategory);

            //busca os atributos do produto
            $this->set('produto_attr',TableRegistry::get('SysProdutoAtributo')->find()->where(['IDPRODUTO' => $_idProduto]));
        }
    }

	/**
	* Metodo que salva as informacoes do produto
	*
	* @return boolean
	*/
    public function singleProductSave(){
        $this->autoRender = false;

        $retorno = false;
        $tblProd = TableRegistry::get('SysProduto');
        $tblCat  = TableRegistry::get("SysCategoriaProduto");
        $tblAtr  = TableRegistry::get('SysProdutoAtributo');
        $tblStock= TableRegistry::get('LojEstoque');
        $atributos  = array();
        $categorias = array();

        //verifica se ha atributos associados ao produto
        if($this->request->getData("ATRIBUTOS")!=""){
            //monta cada atributo em um objeto e coloca em um array
            $attrs = array();
            parse_str($this->request->getData("ATRIBUTOS"),$attrs);
            foreach($attrs['lst_atributo'] as $key => $value){
                $atr = new \stdClass();
                $atr->IDATRIBUTO = $key;
                $atr->VALOR      = $value;
                $atributos[]     = $atr;
            }
        }

        //verifica se eh um produto existente ou novo
        //se for um produto existente apaga todas as categorias para recriar mais adiante
        if($this->request->getData("IDPRODUTO")!=""){
            $produto = $tblProd->get((int)$this->request->getData("IDPRODUTO"));
            $tblCat->deleteAll(['IDPRODUTO' => $this->request->getData("IDPRODUTO")]);
        }
        else{
            $produto = $tblProd->newEntity();
        }
        $produto->IDPRODUTOTIPO = $this->request->getData("IDPRODUTOTIPO");
        $produto->IDFORNECEDOR  = $this->request->getData("IDFORNECEDOR");
        $produto->NOME          = mb_strtoupper($this->request->getData("NOME"));
        $produto->NOME_TAG      = ($this->request->getData("NOME_TAG")!=""?mb_strtoupper($this->request->getData("NOME_TAG")):NULL);
        $produto->CODIGO_BARRA  = $this->request->getData("CODIGO_BARRA");
        $produto->PRECO_COMPRA  = str_replace(",", "", $this->request->getData("PRECO_COMPRA"));
        $produto->MARKUP        = $this->request->getData("MARKUP");
        $produto->PRECO_VENDA   = str_replace(",", "", $this->request->getData("PRECO_VENDA"));
        $produto->COD_FORNECE   = mb_strtoupper($this->request->getData("COD_FORNECE"));
        $produto->NCM           = ($this->request->getData("NCM")!="")?$this->request->getData("NCM"):NULL;
        $produto->CSOSN         = ($this->request->getData("CSOSN")!="")?$this->request->getData("CSOSN"):NULL;
        $produto->SKU           = $this->productMountSku(
                $this->request->getData("IDPRODUTOTIPO"),
                mb_strtoupper($this->request->getData("COD_FORNECE")),
                $atributos
            );
        $produt->UNIDADE_MEDIDA = $this->request->getData("UNIDADE_MEDIDA");
        //define a estrutura do produto como Simples
        $produto->ESTRUTURA     = 'S';
        $produto->DATA_CADASTRO = $this->request->getData("DATA_CADASTRO");

        //imagem
        $produto->IMAGEM = ($this->request->getData("IMG_NAME")!="")? $this->request->getData("IMG_NAME"): NULL;

		$retorno = $tblProd->save($produto)?true:false;
        if($retorno){
            //Monta as novas categorias para salvar
            $cats = $this->request->getData("CATEGORIAS");
            $cats = str_replace("cbCategory=","",$cats);
            $cats = explode('&',$cats);
            foreach($cats as $categoria){
                $cat = $tblCat->newEntity();
                $cat->IDCATEGORIA = $categoria;
                $cat->IDPRODUTO   = $produto->IDPRODUTO;
                $categorias[] = $cat;
                $tblCat->save($cat);
            }

            //se houverem atributos de produtos salva-os
            if(count($atributos)>0){
                foreach($atributos as $attr){
                    $atributo = $tblAtr->newEntity();
                    $atributo->IDPRODUTO  = $produto->IDPRODUTO;
                    $atributo->IDATRIBUTO = $attr->IDATRIBUTO;
                    $atributo->VALOR      = $attr->VALOR;
                    $tblAtr->save($atributo);
                }
            }

            //verifica se ha registro no estoque, se nao houver cria um para a loja padrao
            //isso evita que o produto nao possa ser encontrado no sistema
            if($tblStock->find()->where(['IDPRODUTO' => $produto->IDPRODUTO])->count()==0){
                $stock = $tblStock->newEntity();
                $stock->IDLOJA     = TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR;
                $stock->IDPRODUTO  = $produto->IDPRODUTO;
                $stock->QTDE_MIN   = 0;
                $stock->QTDE_BUY   = 0;
                $stock->QTDE_MAX   = 0;
                $stock->REPO_TIME  = 0;
                $stock->QUANTIDADE = 0;
                $stock->CALCULA_REPOSICAO = '0';
                $stock->DATA_ENTRADA = date("Y-m-d H:i:s");
                $tblStock->save($stock);
            }

            //se for ajuste remove o produto das divergencias da NF-e
            if($this->request->getData("ADJUST")=="1"){
                $tblDiv = TableRegistry::get('TmpNfeDivergencia');
                $div = $tblDiv->get($produto->IDPRODUTO);
                $tblDiv->delete($div);
            }
        }

        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que busca as informacoes de estoque do produto
	* @param int $_idProduto Codigo do produto
	*
	* @return NULL
	*/
    public function singleProductGetStock($_idProduto){
        $tblStock = TableRegistry::get('LojEstoque');

        $this->set('estoques',
            $tblStock->find()
            ->select(['LOJA' => 'L.NOME','DATA_ENTRADA','QUANTIDADE','DATA_ULTIMA_COMPRA','DATA_ULTIMA_VENDA'])
            ->where(['IDPRODUTO' => $_idProduto])
            ->join([
                'table' => 'sys_loja',
                'alias' => 'L',
                'type'  => 'INNER',
                'conditions' => 'LojEstoque.IDLOJA=L.IDLOJA'
            ])
            ->where(function($exp,$q){ return $exp->notEq('L.IDLOJA',TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR); })
        );
    }

        /**
	* Metodo que exibe a tela principal de alocacao de produto
	*
	* @return null
	*/
    public function singleProductAlocation(){
		$this->set('title',"Aloca&ccedil;&atilde;o de Produto Simples");
	}

	/**
	* Metodo que busca se um determinado produto estah ou nao em um estoque
	* @param int $_idProduto codigo do produto
	*
	* @return json
	*/
	public function singleProductStock($_idProduto){
		$retorno = NULL;

		//realiza a busca de todos as lojas que possuem o produto
		//exceto a loja padrao, isso evitarah que o produto nao apareca em buscas
		$tblStock = TableRegistry::get("LojEstoque")->find()->select(['IDLOJA','LOJA' => 'l.NOME'])
		->join([
			'table' => 'sys_loja',
			'type'  => 'inner',
			'alias' => 'L',
			'conditions' => 'L.IDLOJA=LojEstoque.IDLOJA'
		])
		->Where(['IDPRODUTO' => $_idProduto])
		->Where(function($exp,$q){
			return $exp->notEq("L.IDLOJA",TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR);
		});

		$htmlWith = "<table class='table'><thead><tr><th>#</th><th>Estoque</th><th>A&ccedil;&atilde;o</th></tr></thead><tbody>";

		foreach($tblStock as $stock){
			$htmlWith .= "<tr><td>".$stock->IDLOJA."</td>
							<td>".$stock->LOJA."</td>
							<td><div class='btn-group' role='group'>
								<a href='javascript:removeStock(".$stock->IDLOJA.",".$_idProduto.")' class='btn btn-danger btn-sm'><i class='fas fa-trash-alt'></i></a>
								<a href='javascript:configStock(".$stock->IDLOJA.",".$_idProduto.")' class='btn btn-primary btn-sm'><i class='fas fa-cog'></i></a>
							</div></td>";
		}
		$htmlWith.="</tbody></table>";

		$retorno['EXIST'] = $htmlWith;

		//realiza busca de todas as lojas que nao possuem o produto
		$subquery = TableRegistry::get("LojEstoque")->find()->select(['IDLOJA'])
		->where(['IDPRODUTO' => $_idProduto]);

		$tblStore = TableRegistry::get("SysLoja")->find()->select(['IDLOJA','NOME'])
		->where(function($exp,$q) use($subquery){
			return $exp->notIn('IDLOJA',$subquery);
		});

		$htmlWithout = "<table class='table'><thead><tr><th>#</th><th>Estoque</th><th>A&ccedil;&atilde;o</th></tr></thead><tbody>";

		foreach($tblStore as $store){
			$htmlWithout .= "<tr><td>".$store->IDLOJA."</td><td>".$store->NOME."</td><td><a href='javascript:addStock(".$store->IDLOJA.",".$_idProduto.")' class='btn btn-success btn-sm'><i class='fas fa-angle-right'></i></a></td>";
		}
		$htmlWithout .= "</tbody></table>";

		$retorno['NOTEXIST'] = $htmlWithout;

		return $this->response->withStringBody( json_encode($retorno) );
	}

	/**
	* Metodo que remove um produto de um ou mais estoque de loja
	*
	* @return boolean
	*/
	public function singleProductDelStock(){
		$retorno = false;

		$tblStoque = TableRegistry::get("LojEstoque");

		if($this->request->getData("STORE")==""){

			//apaga de todas as lojas diferentes da loja padrao
			$retorno = $tblStoque->deleteAll([
				'IDPRODUTO' => $this->request->getData("IDPRODUTO"),
				function($exp){
					return $exp->notEq("IDLOJA",TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR);
				}
			])?true:false;
		}else{
			//apaga apenas da loja selecionadas
			$retorno = $tblStoque->deleteAll([
				'IDPRODUTO' => $this->request->getData("IDPRODUTO"),
				'IDLOJA'    => $this->request->getData("STORE")
			])?true:false;
		}

		return $this->response->withStringBody( $retorno );
	}

	/**
	* Metodo que adiciona um produto a um ou mais estoque de loja
	*
	* @return boolean
	*/
	public function singleProductAddStock(){
		$retorno = false;
		$tblStock = TableRegistry::get("LojEstoque");

		if($this->request->getData("STORE")==""){
			//busca todas as lojas que o produto estah alocado
			$subquery = $tblStock->find()->select(["IDLOJA"])->where(['IDPRODUTO' => $this->request->getData("IDPRODUTO")]);

			//busca todas as lojas que nao possuem estoque do produto
			$lojas = TableRegistry::get("SysLoja")->find()->select(['IDLOJA'])
			->where(function($exp){
				//remove a busca a loja padrao
				return $exp->notEq("IDLOJA",TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR);
			})
			->where(function($exp) use($subquery){
				//remove da busca as lojas jah associadas ao produto
				return $exp->notIn("IDLOJA",$subquery);
			});

			foreach($lojas as $loja){
				$newStock                    = $tblStock->newEntity();
				$newStock->IDPRODUTO         = $this->request->getData("IDPRODUTO");
	            $newStock->IDLOJA            = $loja->IDLOJA;
	            $newStock->QTDE_MIN          = 0;
	            $newStock->QTDE_BUY          = 0;
	            $newStock->QTDE_MAX          = 0;
	            $newStock->REPO_TIME         = 0;
	            $newStock->QUANTIDADE        = 0;
	            $newStock->CALCULA_REPOSICAO = 0;//nao calcula reposicao por padrao
	            $newStock->DATA_ENTRADA      = date("Y-m-d");

	            $retorno = $tblStock->save($newStock)?true:false;
			}
		}else{
			$newStock = $tblStock->newEntity();
            $newStock->IDPRODUTO         = $this->request->getData("IDPRODUTO");
            $newStock->IDLOJA            = $this->request->getData("STORE");
            $newStock->QTDE_MIN          = 0;
            $newStock->QTDE_BUY          = 0;
            $newStock->QTDE_MAX          = 0;
            $newStock->REPO_TIME         = 0;
            $newStock->QUANTIDADE        = 0;
            $newStock->CALCULA_REPOSICAO = 0;//nao calcula reposicao por padrao
            $newStock->DATA_ENTRADA      = date("Y-m-d");

            $retorno = $tblStock->save($newStock)?true:false;
		}

		return $this->response->withStringBody( $retorno );
	}

	public function singleProductConfigStock($_idProduto,$_idLoja){
		$this->viewBuilder()->setLayout("gallery");

		$this->set('store',TableRegistry::get('SysLoja')->get($_idLoja));
		$this->set('product',TableRegistry::get('SysProduto')->get($_idProduto));
		$this->set('stock',
			TableRegistry::get("LojEstoque")->get(['IDLOJA' => $_idLoja,'IDPRODUTO' => $_idProduto])
		);
	}

	public function singleProductStockSave(){

        $tblStock = TableRegistry::get('LojEstoque');
        $stock = $tblStock->get(['IDLOJA' => $this->request->getData('IDLOJA'),'IDPRODUTO' => $this->request->getData("IDPRODUTO")]);

        //altera apenas os valores de quantidades minima e maxima
        $stock->QTDE_MIN  = $this->request->getData("QTDE_MIN");
        $stock->QTDE_BUY  = $this->request->getData("QTDE_BUY");
        $stock->QTDE_MAX  = $this->request->getData("QTDE_MAX");
        $stock->CALCULA_REPOSICAO = $this->request->getData("CALCULA_REPOSICAO");

        return $this->response->withStringBody( $tblStock->save($stock)?true:false );
	}

    /****************************PRODUTO COMPOSTO (KIT)******************************/
    /**
	* Metodo em monta os filtros da listagem de produtos compostos
	*
	* @return string
	*/
    public function compositeProductFilter(){
        $this->autoRender = false;

        $this->Filter->addFilter("Nome","TXT_KIT_SEARCH_NAME","text");
        $this->Filter->addFilter("SKU","TXT_KIT_SEARCH_SKU","text");

        $pops = array();
        $tblProvider = TableRegistry::get('SysFornecedor');
        $providers = $tblProvider->find()->select(['IDFORNECEDOR','FANTASIA'])->order(['FANTASIA' => 'ASC']);
        foreach($providers as $provider){
            $opt = new \stdClass();
            $opt->key = $provider->FANTASIA;
            $opt->value = $provider->IDFORNECEDOR;
            $pops[] = $opt;
        }
        $this->Filter->addFilter('Fornecedor',"CB_KIT_SEARCH_PROVIDER","combo",$pops);

        		$ords = array();

		$ord1 = new \stdClass();
		$ord1->key   = "Nome";
		$ord1->value = "NOME";
		$ords[] = $ord1;

		$ord2 = new \stdClass();
		$ord2->key   = "SKU";
		$ord2->value = "SKU";
		$ords[] = $ord2;

		$ord4 = new \stdClass();
		$ord4->key   = "Pre&ccedil;o de Venda";
		$ord4->value = "PRECO_VENDA";
		$ords[] = $ord4;

		$this->Filter->addOrder($ords);

        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    /**
	* Metodo que monta a pagina de listagem dos produtos compostos
	* Aqui sao definidas as URLs de filtro e de busca de dados
	*
	* @return
	*/
    public function compositeProduct(){
    	$this->set('title',"Produtos Compostos");
        $this->set('url_filter','/stock/composite_product_filter');
        $this->set('url_data','/stock/composite_product_data');
    }

    /**
	* Metodo que realiza a busca dos dados dos produtos compostos
	*
	* @return null
	*/
    public function compositeProductData(){
        $tblProduct = TableRegistry::get('SysProduto');

        $query = $tblProduct->find();
        $query->select(['IDPRODUTO','NOME','SKU','PRECO_VENDA','STATUS'])->where(['ESTRUTURA' => 'C']);

        //realiza a busca por nome
        if($this->request->getData("TXT_KIT_SEARCH_NAME")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('NOME','%'.$this->request->getData('TXT_KIT_SEARCH_NAME').'%');
            });
        }

        //realiza a busca por SKU
        if($this->request->getData("TXT_KIT_SEARCH_SKU")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('SKU','%'.$this->request->getData('TXT_KIT_SEARCH_SKU').'%');
            });
        }

        //realiza a busca por fornecedor
        if($this->request->getData("CB_KIT_SEARCH_PROVIDER")!=""){
            $query->where(['IDFORNECEDOR' => $this->request->getData('CB_KIT_SEARCH_PROVIDER')]);
        }

        $this->set('product_kit_list',$this->paginate($query,['limit' => 10]));

        //realiza a limpeza da tabela temporaria
        $tmpItem = TableRegistry::get("TmpProdutoItem");
        $tmpItem->deleteAll(array("1 = 1"));
    }

	/**
	* Metodo que monta um novo produto ou altera um existente
	* @param int $_idProduto
	*
	* @return boolean
	*/
    public function compositeProductCreate($_idProduto=""){
        //busca a lista de fornecedores
        $tblProvider = TableRegistry::get('SysFornecedor');
        $this->set('provider_list',$tblProvider->find()->select(['IDFORNECEDOR','FANTASIA']));

        //busca a lista de categorias ordenada pai -> filho e alfabetico
        $this->set('category_list',$this->categoryGetOrdered());

        //se for edicao busca os dados do produto
        if($_idProduto!=""){
            $tblProduct = TableRegistry::get('SysProduto');
            $product = $tblProduct->get($_idProduto);
            $this->set('produto',$product);

            $tblProductCat = TableRegistry::get('SysCategoriaProduto');
            $prodCategory = $tblProductCat->find()->where(['IDPRODUTO' => $product->IDPRODUTO]);

            $this->set('produto_cat',$prodCategory);
        }
    }

	/**
	* Metodo que adiciona um item ao produto
	* @param int $_idProduto codigo do produto que serah adicionado
	*
	* @return boolean
	*/
    public function compositeProductItemAdd($_idProduto){

        $this->autoRender = false;

        //realiza a busca das informacoes do produto
        $tblProd = TableRegistry::get('SysProduto');
        $prod = $tblProd->get($_idProduto);

        //salva na tabela temporaria que serve de apoio
        $tmpItem = TableRegistry::get('TmpProdutoItem');
        $item = $tmpItem->newEntity();
        $item->IDPRODUTO = $_idProduto;
        $item->NOME_PRODUTO = $prod->NOME;
        $item->SKU_PRODUTO  = $prod->SKU;
        $item->PRECO_VENDA  = $prod->PRECO_VENDA;

        return $this->response->withStringBody( ($tmpItem->save($item))?true:false );
    }

	/**
	* Metodo que remove um item do produto
	* @param int $_idProduto codigo do item que serah removido
	*
	* @return boolean
	*/
    public function compositeProductItemRemove($_idProduto){

        $this->autoRender = false;

        $tmpItem = TableRegistry::get('TmpProdutoItem');
        $item = $tmpItem->get($_idProduto);
        return $this->response->withStringBody( ($tmpItem->delete($item))?true:false );
    }

	/**
	* Metodo que busca os items que fazem parte do produto
	* @param int $_idComposite codigo do item composto
	*
	* @return null
	*/
    public function compositeProductItensGet($_idComposite=""){
        $tmpItem = TableRegistry::get('TmpProdutoItem');

        //verifica se eh um produto jah existente
        if($_idComposite!=""){
        	//verifica se possui itens
            if($tmpItem->find()->count()==0){

            	//havendo itens busca todos os pertencentes ao produto
                $tblProdIt = TableRegistry::get('SysProdutoItem');
                $prods = $tblProdIt->find()->where(['IDPRODUTO' => $_idComposite]);

				//adiciona item-a-item na tabela temporaria
				//isso foi feito para facilitar a edicao do item
				//e evitar problemas durante uma provavel venda
                foreach($prods as $prod){
                    //busca os dados do produto
                    $tblProd = TableRegistry::get('SysProduto');
                    $prd = $tblProd->get($prod->IDPRODUTO_FILHO);

                    $item = $tmpItem->newEntity();
                    $item->IDPRODUTO    = $prd->IDPRODUTO;
                    $item->NOME_PRODUTO = $prd->NOME;
                    $item->SKU_PRODUTO  = $prd->SKU;
                    $item->PRECO_VENDA  = $prd->PRECO_VENDA;

                    $tmpItem->save($item);
                }
            }
        }

        $this->set('itens',$tmpItem->find());
    }

	/**
	* Metodo que monta a sugestao de preco do produto baseado no preco dos itens
	*
	* @return float
	*/
    public function compositeProductMountPrice(){
        $this->autoRender = false;

        //busca e soma o preco de venda de cada item do produto
        $tblItem = TableRegistry::get('TmpProdutoItem');
        $total = 0;
        foreach($tblItem->find() as $item){
            $total += $item->PRECO_VENDA;
        }
        return $this->response->withStringBody( number_format($total,2) );
    }

	/**
	* Metodo que salva um produto composto
	*
	* @return boolean
	*/
    public function compositeProductSave(){

    	$retorno = false;

        $tblProd = TableRegistry::get('SysProduto');
        $tblCat  = TableRegistry::get('SysCategoriaProduto');
        $tblItem = TableRegistry::get('SysProdutoItem');
        $tmpItem = TableRegistry::get('TmpProdutoItem');
        $tblStock= TableRegistry::get('LojEstoque');
        $categorias = array();
        $atributos = array();

        //evita que tente carregar o template
        $this->autoRender = false;

        //verifica se eh um novo produto ou um existente
        if($this->request->getData("IDPRODUTO")!=""){
            $produto = $tblProd->get((int)$this->request->getData("IDPRODUTO"));
            $tblCat->deleteAll(['IDPRODUTO' => $this->request->getData("IDPRODUTO")]);
            $tblItem->deleteAll(['IDPRODUTO' => $this->request->getData("IDPRODUTO")]);
        }
        else{
            $produto = $tblProd->newEntity();
        }

        //um KIT de produto sempre serah um tipo de produto padrao
        $produto->IDPRODUTOTIPO  = TableRegistry::get('SysOpcao')->get('DEFAULT_PRODUCT_TYPE')->OPCAO_VALOR;
        $produto->IDFORNECEDOR   = $this->request->getData("IDFORNECEDOR");
        $produto->NOME           = mb_strtoupper($this->request->getData("NOME"));
        $produto->NOME_TAG       = mb_strtoupper($this->request->getData("NOME_TAG"));
        $produto->CODIGO_BARRA   = $this->request->getData("CODIGO_BARRA");
        $produto->PRECO_COMPRA   = 0;
        $produto->MARKUP         = 0;
        $produto->PRECO_VENDA    = $this->request->getData("PRECO_VENDA");
        $produto->COD_FORNECE    = $this->request->getData("SKU");
        $produto->NCM            = NULL;
        $produto->SKU            = $this->request->getData("SKU"); //nesse caso nao ha regra de SKU
        $produto->UNIDADE_MEDIDA = 'UN'; //sempre serah unidade, nao faz sentido kit com itens por kg
        $produto->ESTRUTURA      = 'C';
        $produto->DATA_CADASTRO  = $this->request->getData("DATA_CADASTRO");
        $produto->CSOSN          = NULL;

        //imagem
        $produto->IMAGEM         = ($this->request->getData("IMG_NAME")!="")? $this->request->getData("IMG_NAME"): NULL;

		$retorno = $tblProd->save($produto)?true:false;

        if($retorno){

            //salva cada categoria do produto
            $cats = $this->request->getData("CATEGORIAS");
            $cats = str_replace("cbCategory=","",$cats);
            $cats = explode('&',$cats);
            foreach($cats as $categoria){
                $cat = $tblCat->newEntity();
                $cat->IDCATEGORIA = $categoria;
                $cat->IDPRODUTO   = $produto->IDPRODUTO;
                $tblCat->save($cat);
                $categorias[] = $cat;
            }

            //busca da tabela temporaria cada item do produto e
            //salva no local definitivo
            foreach($tmpItem->find() as $it){
                $item = $tblItem->newEntity();
                $item->IDPRODUTO       = $produto->IDPRODUTO;
                $item->IDPRODUTO_FILHO = $it->IDPRODUTO;
                $tblItem->save($item);
            }

            //limpa a tabela temporaria de itens
            $tmpItem->deleteAll(array(" 1 = 1"));

            //verifica se ha registro no estoque, se nao houver cria um para a loja padrao do sistema
            //isso evita que o produto nao possa ser encontrado no sistema
            if($tblStock->find()->where(['IDPRODUTO' => $produto->IDPRODUTO])->count()==0){
                $stock = $tblStock->newEntity();
                $stock->IDLOJA     = TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR;
                $stock->IDPRODUTO  = $produto->IDPRODUTO;
                $stock->QTDE_MIN   = 0;
                $stock->QTDE_BUY   = 0;
                $stock->QTDE_MAX   = 0;
                $stock->REPO_TIME  = 0;
                $stock->QUANTIDADE = 0;
                $stock->CALCULA_REPOSICAO = '0';
                $stock->DATA_ENTRADA = date("Y-m-d H:i:s");
                $tblStock->save($stock);
            }

        }

        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que exibe a tela de ajuste de estoque de produto
	*
	* @return null
	*/
    public function adjustment($_idLoja="",$_idProduto="",$_qtdeDig=0){
    	if($_idLoja!="" && $_idProduto!=""){
			$this->viewBuilder()->setLayout("gallery");

			$produto = TableRegistry::get('SysProduto')->get($_idProduto);

			$estoque = TableRegistry::get('LojEstoque')->get(['IDLOJA' => $_idLoja,'IDPRODUTO' => $_idProduto]);

			$this->set('produto',$produto);
			$this->set('estoque',$estoque);
			$this->set('digitado',$_qtdeDig);
			$this->set('loja',$_idLoja);
		}

		//verifica se eh mobile para trocar o layout
		$this->set('is_mobile', $this->request->is('mobile'));

		$this->set('title',"Ajuste de Estoque");

    	//busca as lojas disponiveis no sistema com excessao da loja padrao
        $lojas = TableRegistry::get('SysLoja')->find()->where(function($exp,$q){ return $exp->notEq('IDLOJA',TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR); });
        $this->set('store_list',$lojas);
	}

	/**
	* Metodo que salva um ajuste de estoque
	*
	* @return null
	*/
	public function adjustmentSave(){
		$retorno = false;

		$tblEstoque = TableRegistry::get('LojEstoque');

        $quant = $this->request->getData("QUANTIDADE");
        $loja  = $this->request->getData("IDLOJA");
        $prod  = $this->request->getData("IDPRODUTO");
        $toper = NULL; //tipo de operacao
        $oper  = NULL; //operador

        //busca a informacao de estoque para realizar o ajuste
        $estoque = $tblEstoque->get(['IDLOJA' => $loja,'IDPRODUTO' => $prod]);

        //com base na operacao incrementa ou decrementa o estoque e prepara a operacao do movimento de estoque
        switch($this->request->getData("OPERACAO")){
            case 'A+':
                $estoque->QUANTIDADE += $quant;
                $toper = 'A';
                $oper  = '+';
            break;
            case 'A-':
                $estoque->QUANTIDADE -= $quant;
                $toper = 'A';
                $oper  = '-';
            break;
            case 'C':
                $estoque->QUANTIDADE        += $quant;
                $estoque->DATA_ULTIMA_COMPRA = date("Y-m-d");
                $toper = 'C';
                $oper  = '+';
            break;
            case 'V':
                $estoque->QUANTIDADE      -= $quant;
                $estoque->DATA_ULTIMA_VENDA = date("Y-m-d");
                $toper = 'V';
                $oper  = '-';
            break;
        }

        if($tblEstoque->save($estoque)){
            $retorno = $this->stockMove($loja, $prod, $quant, $toper, $oper);
        }
        else{
            $retorno = false;
        }
        return $this->response->withStringBody( $retorno );
	}

	/**
	* Metodo que salva o movimento de estoque
	* @param int $_idLoja Codigo da Loja
	* @param int $_idProduto Codigo do Produto
	* @param int $_quantidade Quantidade do movimento
	* @param string $_tipo_operacao Tipo de operacao que deve ser (V = Venda, C = Compra, A = Adicao)
	* @param undefined $_operacao Operacao que deve ser(- = decrescimo, + = acrescimo)
	*
	* @return
	*/
	private function stockMove($_idLoja,$_idProduto,$_quantidade,$_tipo_operacao,$_operacao){
        $tblMove = TableRegistry::get('LojMovimentoEstoque');
        $prd = TableRegistry::get('SysProduto')->get($_idProduto);


        $movimento = $tblMove->newEntity();
        $movimento->IDLOJA         = $_idLoja;
        $movimento->DATA_MOVIMENTO = date("Y-m-d H:i:s");
        $movimento->QUANTIDADE     = $_quantidade;
        $movimento->TIPO_OPERACAO  = $_tipo_operacao;
        $movimento->OPERACAO       = $_operacao;
        $movimento->PRECO_CUSTO    = $prd->PRECO_COMPRA;
        $movimento->NOME_PRODUTO   = $prd->NOME;
        $movimento->SKU_PRODUTO    = $prd->SKU;
        $movimento->IDPRODUTO      = $_idProduto;

        return  $this->response->withStringBody( $tblMove->save($movimento)?true:false );
    }

    /*****************************TRANSFERÊNCIA****************************/

    /**
	* Metodo que exibbe os filtros da listagem de transferencias
	*
	* @return null
	*/
    public function interstoreTransferFilter(){
        $this->autoRender = false;

        $this->Filter->addFilter("Identifica&ccedil;&atilde;o","TXT_TRANSFER_SEARCH_IDENTITY","text");

        $pops = array();
        $tblStore = TableRegistry::get('SysLoja');
        $stores = $tblStore->find()->select(['IDLOJA','NOME'])
        ->where(function($exp){
			return $exp->notEq("IDLOJA",TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR);
		})
        ->order(['NOME' => 'ASC']);
        foreach($stores as $store){
            $opt = new \stdClass();
            $opt->key = $store->NOME;
            $opt->value = $store->IDLOJA;
            $pops[] = $opt;
        }
        $this->Filter->addFilter('Loja Origem',"CB_TRANSFER_SEARCH_ORIGIN","combo",$pops);

        $pops = NULL;
        $pops = array();
        foreach($stores as $store){
            $opt = new \stdClass();
            $opt->key = $store->NOME;
            $opt->value = $store->IDLOJA;
            $pops[] = $opt;
        }
        $this->Filter->addFilter('Loja Destino',"CB_TRANSFER_SEARCH_DESTINY","combo",$pops);

        $ops = array();

        $optc = new \stdClass();
        $optc->key  = "Cancelada";
        $optc->value = "C";
        $ops[] = $optc;

        $optf = new \stdClass();
        $optf->key  = "Finalizada";
        $optf->value = "F";
        $ops[] = $optf;

        $optp = new \stdClass();
        $optp->key  = "Pendente";
        $optp->value = "P";
        $ops[] = $optp;

        $this->Filter->addFilter("Status","CB_TRANSFER_SEARCH_STATUS","combo",$ops);

        return $this->response->withStringBody( $this->Filter->mountFilters() );
    }

    /**
	* Metodo que exibe a listagem das transferencias
	*
	* @return null
	*/
    public function interstoreTransfer(){
    	$this->set('title',"Transfer&ecirc;ncia entre lojas");
        $this->set('url_filter','/stock/interstore_transfer_filter');
        $this->set('url_data','/stock/interstore_transfer_data');

        $this->set('store_list',TableRegistry::get('SysLoja')->find()->where(function ($exp,$q){ return $exp->notEq('IDLOJA',TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR); }));

        $this->set('user',$this->Auth->user());
    }

    public function interstoreTransferData(){
        $tblTrans = TableRegistry::get('SysTransferencia');

        $user = $this->Auth->user();

        $query = $tblTrans->find()
                ->select(['IDTRANSFERENCIA','NOME','DATA_CRIACAO','DATA_VALIDADE','STATUS','ORIGEM' => 'O.NOME','DESTINO' => 'D.NOME'])
                ->join([
                    'O' => [
                        'table' => 'sys_loja',
                        'type' => 'INNER',
                        'conditions' => 'O.IDLOJA = SysTransferencia.IDLOJA_ORIGEM',
                    ],
                    'D' => [
                        'table' => 'sys_loja',
                        'type' => 'INNER',
                        'conditions' => 'D.IDLOJA = SysTransferencia.IDLOJA_DESTINO',
                    ]
                ]);

        //se o usuario nao for administrador soh conseguira
        //ver a transferencias que saem da sua loja ou que irao
        //chegar na sua loja
        if($user['role']!="admin"){
			$query->where(function($exp) use($user){
				return $exp->or_(['IDLOJA_ORIGEM' => $user['storeid']])->add(['IDLOJA_DESTINO' => $user['storeid'] ]);
			});
		}

        if($this->request->getData("TXT_TRANSFER_SEARCH_IDENTITY")!=""){
            $query->where(function ($exp,$q){
                return $exp->like('SysTransferencia.NOME','%'.$this->request->getData('TXT_TRANSFER_SEARCH_IDENTITY').'%');
            });
        }
        if($this->request->getData("CB_TRANSFER_SEARCH_ORIGIN")!=""){
            $query->where(['IDLOJA_ORIGEM' => $this->request->getData("CB_TRANSFER_SEARCH_ORIGIN")]);
        }
        if($this->request->getData("CB_TRANSFER_SEARCH_DESTINY")!=""){
            $query->where(['IDLOJA_DESTINO' => $this->request->getData("CB_TRANSFER_SEARCH_DESTINY")]);
        }
        if($this->request->getData("CB_TRANSFER_SEARCH_STATUS")!=""){
            $query->where(['STATUS' => $this->request->getData('CB_TRANSFER_SEARCH_STATUS')]);
        }

        $query->order(['IDTRANSFERENCIA' => 'DESC']);

        $this->set('transfer_list',$this->paginate($query,['limit' => 10]));
    }

    /**
	* Metodo que exibe a tela de criacao ou edicao de transferencia
	* @param int $_idTransferencia codigo da transferencia (nao obrigatorio)
	*
	* @return null
	*/
    public function interstoreTransferCreate($_idTransferencia=""){
    	$user = $this->Auth->user();

    	$this->set('user',$user);

    	$this->set('validade',TableRegistry::get("SysOpcao")->get("TRANSFER_EXPIRATION_DATE")->OPCAO_VALOR);

    	//busca a listagem de lojas existentes
        $this->set('store_list',
        	TableRegistry::get('SysLoja')
        		->find()
        		->where(function($exp){
        			return $exp->notEq("IDLOJA",TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR);
        		})
        	);
        if($_idTransferencia!=""){
            $this->set('transfer',TableRegistry::get('SysTransferencia')->get($_idTransferencia));
        }
    }

	/**
	* Metodo que adiciona um produto na tabela temporaria para transferir
	* @param int $_idProduto Codigo do produto
	* @param int $_origem Codigo da loja de origem
	*
	* @return boolean
	*/
    public function interstoreTransferItemAdd($_idProduto,$_origem){

        $tblProd = TableRegistry::get('SysProduto');
        $prod = $tblProd->get($_idProduto);

        $tmpItem = TableRegistry::get('TmpTransferenciaItem');
        $item = $tmpItem->newEntity();
        $item->IDPRODUTO = $_idProduto;
        $item->NOME_PRODUTO = $prod->NOME;
        $item->SKU_PRODUTO  = $prod->SKU;
        $item->PRECO_CUSTO  = $prod->PRECO_COMPRA;
        $item->DISPONIVEL_TRANSFER = TableRegistry::get('LojEstoque')->get(['IDLOJA' => $_origem,'IDPRODUTO' => $prod->IDPRODUTO])->QUANTIDADE;

        return $this->response->withStringBody( ($tmpItem->save($item))?true:false );
    }

	/**
	* Metodo que remove um produto da tabela temporaria de transferencias
	* @param int $_idProduto Codigo do Produto
	*
	* @return
	*/
    public function interstoreTransferItemDel($_idProduto){

        $tmpItem = TableRegistry::get('TmpTransferenciaItem');
        $item = $tmpItem->get($_idProduto);
        echo ($tmpItem->delete($item))?true:false;
    }

	/**
	* Metodo que busca os itens da transferencia disponiveis na tabela temporaria
	* @param int $_idTransferencia Codigo da Transferencia
	*
	* @return null
	*/
    public function iterstoreTransferItens($_idTransferencia=""){
        $tmpItem = TableRegistry::get('TmpTransferenciaItem');

        if($_idTransferencia!=""){
            $transfer = TableRegistry::get('SysTransferencia')->get($_idTransferencia);
            if($tmpItem->find()->count()==0){
                $tblTransIt = TableRegistry::get('SysTransferenciaItem');
                $prods = $tblTransIt->find()->where(['IDTRANSFERENCIA' => $_idTransferencia]);

                foreach($prods as $prod){
                    //busca os dados do produto
                    $tblProd = TableRegistry::get('SysProduto');
                    $prd = $tblProd->get($prod->IDPRODUTO);

                    $item = $tmpItem->newEntity();
                    $item->IDPRODUTO    = $prd->IDPRODUTO;
                    $item->NOME_PRODUTO = $prd->NOME;
                    $item->SKU_PRODUTO  = $prd->SKU;
                    $item->PRECO_CUSTO  = $prd->PRECO_COMPRA;
                    $item->DISPONIVEL_TRANSFER = TableRegistry::get('LojEstoque')->get(['IDLOJA' => $transfer->IDLOJA_ORIGEM,'IDPRODUTO' => $prod->IDPRODUTO])->QUANTIDADE;

                    $tmpItem->save($item);
                }
            }
        }

        $this->set('transfer_itens',$tmpItem->find());
    }

	/**
	* Metodo que salva as informacoes de uma transferencia
	*
	* @return boolean
	*/
    public function interstoreTransferSave(){
    	$retorno = false;

        $tblTransfer = TableRegistry::get('SysTransferencia');
        $tblItem = TableRegistry::get('SysTransferenciaItem');
        $tblTmpItem = TableRegistry::get('TmpTransferenciaItem');

        //verifica seh uma edicao ou criacao de nova transferencia
        if($this->request->getData("txtIdTransfer")!=""){
            $transfer = $tblTransfer->get($this->request->getData("txtIdTransfer"));
            //remove todos os itens existente da transferencia existente antes de adicionar novos
            $tblItem->deleteAll(['IDTRANSFERENCIA' => $this->request->getData("txtIdTransfer")]);
        }
        else{
            $transfer = $tblTransfer->newEntity();
            $transfer->DATA_CRIACAO  = date("Y-m-d H:i:s");
            //se for uma transferencia nova colocarah a data de validade com X dias apos o dia atual
			//essa data de validade serve para o agendador de tarefas e pode ser definida nas opcoes do sistema
            $transfer->DATA_VALIDADE = date('Y-m-d', strtotime("+".TableRegistry::get("SysOpcao")->get("TRANSFER_EXPIRATION_DATE")->OPCAO_VALOR." day", strtotime(date("Y-m-d"))));
        }

        $transfer->NOME           = mb_strtoupper($this->request->getData("txtTransferNome"));
        $transfer->IDLOJA_ORIGEM  = $this->request->getData("cbOrigem");
        $transfer->IDLOJA_DESTINO = $this->request->getData("cbDestino");

        $qtds = $this->request->getData("txtQuantidade");
        $its  = $this->request->getData("txtIdProduto");
        //busca os itens que estao na tabela temporaria
        $itens = $tblTmpItem->find();
        $newItens = null;

        //varre os itens selecionados pelo usuario
        foreach($itens as $item){
            //varre os itens que o usuario mandou transferir
            for($i=0;$i<count($qtds);$i++){
                //verifica se a quantidade dos itens que serao transferidos eh superior a zero
                if($qtds[$i]>0){
                    //verifica se o item enviado eh igual ao que esta na tabela temporaria
                    //necessario para atualizar a quantidade do item antes de
                    //adiciona-lo aos itens completos que serao salvos
                    if($item->IDPRODUTO==$its[$i]){

                        //precisa remontar o item, pois precisa do id da transferencia nele
                        $it = new \stdClass();
                        $it->IDTRANSFERENCIA = null;
                        $it->IDPRODUTO    = $item->IDPRODUTO;
                        $it->QUANTIDADE   = $qtds[$i];
                        $it->NOME_PRODUTO = $item->NOME_PRODUTO;
                        $it->SKU_PRODUTO  = $item->SKU_PRODUTO;
                        $it->PRECO_CUSTO  = $item->PRECO_CUSTO;
                        $newItens[] = $it;
                    }
                }
            }
        }

        //se o status nao estiver definido coloca como P (Pendente)
        if($transfer->STATUS==""){
            $transfer->STATUS = "P";
        }

        if($tblTransfer->save($transfer)){
            foreach($newItens as $it){
                $it->IDTRANSFERENCIA = $transfer->IDTRANSFERENCIA;
                $tblItem->save($it);
            }

            TableRegistry::get('TmpTransferenciaItem')->deleteAll(array('1 = 1'));
            $retorno = true;
        }

        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que executa manualmente uma transferencia entre lojas
	* @param int $_idTransferencia codigo da transferencia
	*
	* @return boolean
	*/
    public function interstoreTransferExecute($_idTransferencia=""){
        $retorno = false;

        $tblTransfer = TableRegistry::get('SysTransferencia');
        $tblStock    = TableRegistry::get('LojEstoque');

        //realiza o processo de transferencia para loja
        if($_idTransferencia!=""){
            //busca dos dados da transferencia
            $transfer = $tblTransfer->get($_idTransferencia);
            if($transfer){
                $data_validade = new DateTime($transfer->DATA_VALIDADE);
                $data_atual = new DateTime();
                $data_atual->setTime(0,0,0);

                $diff = $data_atual->diff($data_validade);
                $totalDiff = $diff->format("%R%a");
                $transfer->DATA_EXECUCAO = date("Y-m-d H:i:s");
                if($totalDiff<1){
                    $transfer->STATUS = 'F'; //define o status como Finalizado

                    //realiza a movimentacao de retirada do estoque de origem
                    foreach(TableRegistry::get('SysTransferenciaItem')->find()->where(['IDTRANSFERENCIA' => $transfer->IDTRANSFERENCIA]) as $item){

                        $this->stockMove($transfer->IDLOJA_ORIGEM, $item->IDPRODUTO, $item->QUANTIDADE, 'T', '-');

                        //realiza a busca do estoque de origem
                        $stock_orign = $tblStock->get(['IDLOJA' => $transfer->IDLOJA_ORIGEM,'IDPRODUTO' => $item->IDPRODUTO]);

                        //baixa completamente o estoque de origem
                        if($stock_orign){
                            $data_compra_origem = $stock_orign->DATA_ULTIMA_COMPRA;
                            $stock_orign->QUANTIDADE  = 0;
                            $tblStock->save($stock_orign);
                        }

                        //salva o movimento de entrada no estoque de destino
                        $this->stockMove($transfer->IDLOJA_DESTINO,$item->IDPRODUTO,$item->QUANTIDADE,'T','+');


                        //realiza a busca do estoque de destino
                        $stock_dest = $tblStock->get(['IDLOJA' => $transfer->IDLOJA_DESTINO,'IDPRODUTO' => $item->IDPRODUTO]);

                        //realiza o incremento no estoque de destino e atualiza a data da compra
                        if($stock_dest){
                            $stock_dest->DATA_ULTIMA_COMPRA = $data_compra_origem;
                            $stock_dest->QUANTIDADE += $item->QUANTIDADE;
                            $retorno = ($tblStock->save($stock_dest))?true:false;
                        }else{
                        	//se o produto nao estiver registrado na loja de destino cria
                        	//seu registro e realiza a transferencia
                            $nstock_dest                     = $tblStock->newEntity();
                            $nstock_dest->IDLOJA             = $transfer->IDLOJA_DESTINO;
                            $nstock_dest->IDPRODUTO          = $item->IDPRODUTO;
                            $nstock_dest->QTDE_MIN           = 0;
                            $nstock_dest->QTDE_BUY           = 0;
                            $nstock_dest->QTDE_MAX           = 0;
                            $nstock_dest->REPO_TIME          = 0;
                            $nstock_dest->CALCULA_REPOSICAO  = '0';
                            $nstock_dest->DATA_ULTIMA_COMPRA = $data_compra_origem;
                            $nstock_dest->QUANTIDADE         = $item->QUANTIDADE;
                            $nstock_dest->DATA_ENTRADA       = date("Y-m-d");
                            $nstock_dest->DAT_ULTIMA_COMPRA  = date("Y-m-d");
                            $retorno = ($tblStock->save($nstock_dest))?true:false;
                        }
                    }
                }else{
                    $transfer->STATUS = 'C'; //define o status como Cancelado
                }
                $retorno = ($tblTransfer->save($transfer))?true:false;
            }
        }else{
            //realiza o processo de transferencia em massa
            $transfers = $this->request->getData("check_list");
            for($i=0;$i<count($transfers);$i++){

                //busca os dados da transferencia
                $transfer = $tblTransfer->get($transfers[$i]);
                if($transfer){
                    $transfer->STATUS = 'F';//muda o status para finalizado
                    $transfer->DATA_EXECUCAO = date("Y-m-d H:i:s");

                    foreach(TableRegistry::get('SysTransferenciaItem')->find()->where(['IDTRANSFERENCIA' => $transfer->IDTRANSFERENCIA]) as $item){

                        //salva o movimento de saida do estoque de origem
                        $this->stockMove($transfer->IDLOJA_ORIGEM, $item->IDPRODUTO, $item->QUANTIDADE, 'T', '-');

                        //realiza a busca do estoque de origem
                        $stock_orign = $tblStock->get(['IDLOJA' => $transfer->IDLOJA_ORIGEM,'IDPRODUTO' => $item->IDPRODUTO]);

                        //baixa completamente o estoque de origem
                        if($stock_orign){
                            $data_compra_origem = $stock_orign->DATA_ULTIMA_COMPRA;
                            $stock_orign->QUANTIDADE  = 0;
                            $tblStock->save($stock_orign);
                        }

                        //salva o movimento de entrada no estoque de destino
                        $this->stockMove($transfer->IDLOJA_DESTINO,$item->IDPRODUTO,$item->QUANTIDADE,'T','+');

                        //realiza a busca do estoque de destino
                        $find_dest = $tblStock->find()->where(['IDLOJA' => $transfer->IDLOJA_DESTINO,'IDPRODUTO' => $item->IDPRODUTO]);

                        //realiza o incremento no estoque de destino e atualiza a data da compra se ha um estoque de destino
                        if($find_dest->count()>0){
                            $stock_dest = $find_dest->first();
                            $stock_dest->DATA_ULTIMA_COMPRA = $data_compra_origem;
                            $stock_dest->QUANTIDADE += $item->QUANTIDADE;
                            $retorno = ($tblStock->save($stock_dest))?true:false;
                        }else{
                            //se o produto nao estiver associado ao estoque de destino
                            //cria-se a associacao jah com as informacoes da
                            //transferencia
                            $nstock_dest                     = $tblStock->newEntity();
                            $nstock_dest->IDLOJA             = $transfer->IDLOJA_DESTINO;
                            $nstock_dest->IDPRODUTO          = $item->IDPRODUTO;
                            $nstock_dest->QTDE_MIN           = 0;
                            $nstock_dest->QTDE_BUY           = 0;
                            $nstock_dest->QTDE_MAX           = 0;
                            $nstock_dest->REPO_TIME          = 0;
                            $nstock_dest->CALCULA_REPOSICAO  = '0';
                            $nstock_dest->DATA_ULTIMA_COMPRA = $data_compra_origem;
                            $nstock_dest->QUANTIDADE         = $item->QUANTIDADE;
                            $nstock_dest->DATA_ENTRADA       = date("Y-m-d");
                            $nstock_dest->DAT_ULTIMA_COMPRA  = date("Y-m-d");
                            $retorno = ($tblStock->save($nstock_dest))?true:false;
                        }
                    }
                    $retorno = ($tblTransfer->save($transfer))?true:false;
                }
            }
        }
        return $this->response->withStringBody( $retorno );
    }

    /**
	* Metodo que exibe as informacoes de uma transferencia
	* @param undefined $_idTransferencia
	*
	* @return null
	*/
    public function interstoreTransferShow($_idTransferencia){
        $this->viewBuilder()->layout('clear');

        $transfer = TableRegistry::get('SysTransferencia')->get($_idTransferencia);
        $tblStore = TableRegistry::get('SysLoja');
        $this->set('transfer',$transfer);
        $this->set('transfer_itens',TableRegistry::get('SysTransferenciaItem')->find()->where(['IDTRANSFERENCIA' => $_idTransferencia]));

        $this->set('origem',$tblStore->get($transfer->IDLOJA_ORIGEM));
        $this->set('destino',$tblStore->get($transfer->IDLOJA_DESTINO));
    }

    /**
	* Metodo que cancela manualmente transferencia(s) selecionada(s)
	*
	* @return boolean
	*/
    public function interstoreTransferCancel(){
        $retorno = false;
        $tblTrans = TableRegistry::get('SysTransferencia');

        $transfers = $this->request->getData("check_list");
        for($i=0;$i<count($transfers);$i++){

            $trans = $tblTrans->get($transfers[$i]);
            $trans->STATUS = 'C';
            $retorno = $tblTrans->save($trans)?true:false;
        }
        return $this->response->withStringBody($retorno);
    }

    /**
	* Metodo que monta uma transferencia atraves das informacoes do fornecedor
	* Esse metodo eh necessario pois o processamento das notas ficais cai diretamente
	* na loja padrao do sistema e depois sao enviados para a loja de destino
	* @return boolean
	*/
    public function interstoreTransferMountByProvider(){
        $this->autoRender = false;
        $retorno = false;
        $tblProd       = TableRegistry::get('SysProduto');
        $tblTransfer   = TableRegistry::get('SysTransferencia');
        $tblTransItem  = TableRegistry::get('SysTransferenciaItem');
        $default_store = TableRegistry::get('SysOpcao')->get("DEFAULT_STORE")->OPCAO_VALOR;

        $transfer = $tblTransfer->newEntity();
        $transfer->DATA_CRIACAO    = date("Y-m-d H:i:s");
        $transfer->DATA_VALIDADE   = date('Y-m-d', strtotime("+2 day", strtotime(date("Y-m-d"))));
        $transfer->NOME            = TableRegistry::get('SysFornecedor')->get($this->request->getData("IDFORNECEDOR"))->FANTASIA." ".date("d/m/Y");
        $transfer->IDLOJA_ORIGEM   = $default_store;
        $transfer->IDLOJA_DESTINO  = $this->request->getData("LOJA_DESTINO");
        $transfer->STATUS          = 'P';

        if($tblTransfer->save($transfer)){
            $produtos = $tblProd->find()->select(['E.IDPRODUTO','E.QUANTIDADE','SKU','NOME','PRECO_COMPRA'])
                ->join([
                    'table' => 'loj_estoque',
                    'alias' => 'E',
                    'type'  => 'INNER',
                    'conditions' => 'SysProduto.IDPRODUTO = E.IDPRODUTO'
                ])
                ->where(['IDFORNECEDOR' => $this->request->getData("IDFORNECEDOR")])
                ->where(['E.IDLOJA' => TableRegistry::get('SysOpcao')->get("DEFAULT_STORE")->OPCAO_VALOR])
                ->where(function ($exp,$q){ return $exp->notEq('QUANTIDADE','0'); });

            foreach($produtos as $produto){
                $item = $tblTransItem->newEntity();
                $item->IDTRANSFERENCIA = $transfer->IDTRANSFERENCIA;
                $item->IDPRODUTO    = $produto['E']['IDPRODUTO'];
                $item->QUANTIDADE   = $produto['E']['QUANTIDADE'];
                $item->NOME_PRODUTO = $produto->NOME;
                $item->SKU_PRODUTO  = $produto->SKU;
                $item->PRECO_CUSTO  = $produto->PRECO_COMPRA;
                $retorno = $tblTransItem->save($item)?true:false;
            }
        }

        return $this->response->withStringBody( $retorno );
    }

    /**
     * Obtem os fornecedores dos produtos que possuem quantidade maior do que 1
     * na loja padrao do sistmea
     *
     * @return json or null
     */
    public function stockTransferRescueProvider(){

        $estoques = TableRegistry::get('LojEstoque')->find()->select(['IDPRODUTO'])
            ->where(function ($exp,$q){ return $exp->notEq('QUANTIDADE','0'); })
            ->where(['IDLOJA' => TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR]);

        $fornecedores = null;

        foreach($estoques as $estoque){
            $forn = TableRegistry::get('SysProduto')->find()->select(['IDFORNECEDOR'])->where(['IDPRODUTO' => $estoque->IDPRODUTO])->first()->IDFORNECEDOR;
            $fornece = new \stdClass();
            $fornece->IDFORNECEDOR = $forn;
            $fornece->FANTASIA     = TableRegistry::get('SysFornecedor')->get($forn)->FANTASIA;

            if($fornecedores!=null){
                if(!in_array($fornece,$fornecedores)){
                    $fornecedores[] = $fornece;
                }
            }else{
                $fornecedores[] = $fornece;
            }
        }

        if(is_array($fornecedores)){
            return $this->response->withStringBody( json_encode($fornecedores) );
        }

        return $this->response->withStringBody(null);
    }

    /**
	* Metodo que exibe a pagina inicial de preparacao para
	* inventariar estoque
	*
	* @return null
	*/
    public function prepareToInventory(){
		$this->set('store_list',
			TableRegistry::get('SysLoja')->find()
			->select(['IDLOJA','NOME'])
			->where(function($exp){
            	return $exp->notEq('IDLOJA',TableRegistry::get('SysOpcao')->get("DEFAULT_STORE")->OPCAO_VALOR);
        	})
        );
        $this->set('product_type_list',TableRegistry::get('SysProdutoTipo')->find()->order(['DESCRICAO' => 'ASC']));

        $this->set('title','Invent&aacute;rio de Estoque');
	}

	public function prepareToInventoryData(){
		//busca as informacoes de produtos ativos conforme
		//o tipo de produto e a loja onde estah disponivel
		$query = TableRegistry::get('SysProduto')->find()
			->select(['IDPRODUTO','NOME','SKU'])
			->where(['IDPRODUTOTIPO' => $this->request->getData('IDPRODUTOTIPO'),'STATUS' => 'A'])
			->where(function($exp){
				return $exp->in('IDPRODUTO',TableRegistry::get('LojEstoque')->find()->select(['IDPRODUTO'])->where(['IDLOJA' => $this->request->getData("IDLOJA")]));
			 });

		//se houver fornecedor entao filtra
		if($this->request->getData("IDFORNECEDOR")!=""){
			$query->where(['IDFORNECEDOR' => $this->request->getData("IDFORNECEDOR")]);
		}
		$this->set('data_list',$query);

		//define as variaveis de filtragem, pois serah importantes durante a validacao
		$this->set('IDLOJA',$this->request->getData("IDLOJA"));
		$this->set('IDFORNECEDOR',$this->request->getData("IDFORNECEDOR"));
		$this->set('IDPRODUTOTIPO',$this->request->getData("IDPRODUTOTIPO"));
	}

	public function prepareToInventoryPrint($idLoja,$idProdutoTipo,$idFornecedor=""){
		$this->viewBuilder()->setLayout("gallery");
		$query = TableRegistry::get('SysProduto')->find()
			->select(['IDPRODUTO','NOME','SKU'])
			->where(['IDPRODUTOTIPO' => $idProdutoTipo,'STATUS' => 'A'])
			->where(function($exp) use($idLoja){
				return $exp->in('IDPRODUTO',TableRegistry::get('LojEstoque')->find()->select(['IDPRODUTO'])->where(['IDLOJA' => $idLoja]));
			 });

		//se houver fornecedor entao filtra
		if($this->request->getData("IDFORNECEDOR")!=""){
			$query->where(['IDFORNECEDOR' => $idFornecedor]);
		}
		$this->set('data_list',$query);
	}

	public function validateInventory(){
		$this->set('IDLOJA',$this->request->getData("IDLOJA"));
		$this->set('IDFORNECEDOR',$this->request->getData("IDFORNECEDOR"));
		$this->set('IDPRODUTOTIPO',$this->request->getData("IDPRODUTOTIPO"));

		$this->set('title',"Valida&ccedil;&atilde;o de Invent&aacute;rio");

		$produtos = $this->request->getData("txtIdProduto");
		$qtdes    = $this->request->getData("txtQuantity");

		//varre os produtos enviados e busca as informacoes existentes para exibir na tela
		$data = null;
		if(isset($produtos)){
			for($i=0;$i<count($produtos);$i++){
				$prod = TableRegistry::get('SysProduto')->get($produtos[$i]);
				$prd = new \stdClass();
				$prd->IDPRODUTO = $produtos[$i];
				$prd->SKU       = $prod->SKU;
				$prd->NOME      = $prod->NOME;
				$prd->DIGITADO  = $qtdes[$i];
				$prd->EXISTENTE = TableRegistry::get('LojEstoque')->get(['IDLOJA' => $this->request->getData("IDLOJA"),'IDPRODUTO' => $produtos[$i]])->QUANTIDADE;
				$data[] = $prd;
			}
		}

		$this->set('data_list',$data);
	}

	/**
	* Metodo que monta a pagina inicial de consulta de estoque para determinada loja
	*
	* @return null
	*/
	public function consult(){

	}

	/**
	* Metodo que realiza a busca da consulta de estoque
	*
	* @return null
	*/
	public function consultData(){
		$query = TableRegistry::get('LojEstoque')->find()
			->select(['LOJA' => 'L.NOME','QUANTIDADE'])
			->join([
				'alias' => 'L',
				'table' => 'sys_loja',
				'type'  => 'inner',
				'conditions' => 'L.IDLOJA=LojEstoque.IDLOJA'
			])
			->where(['IDPRODUTO' => $this->request->getData("IDPRODUTO")])
			->where(function($exp){
				return $exp->notIn('L.IDLOJA',TableRegistry::get('SysOpcao')->get('DEFAULT_STORE')->OPCAO_VALOR);
			});
		$this->set('data_list',$query);
	}
}
