<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class InstallMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        if(!$this->hasTable("loj_caixa")){
            $loj_caixa = $this->table('loj_caixa',['id' => false, 'primary_key' => ['IDCAIXA']]);
            $loj_caixa->addColumn('IDCAIXA','integer',['limit' => 11])
            ->addColumn('IDLOJA','integer',['limit' => 11])
            ->addColumn('DATA_ABERTURA','datetime')
            ->addColumn('DATA_FECHAMENTO','datetime',['null' => true])
            ->addColumn('VALOR_ABERTURA','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('VALOR_FECHAMENTO','decimal',['precision' => 10,'scale' => 2,'null' => true])
            ->addColumn('IDUSUARIO_ABERTURA','integer',['limit' => 11])
            ->addColumn('IDUSUARIO_FECHAMENTO','integer',['limit' => 11, 'null' => true])
            ->addColumn('STATUS','char',['limit' => 1])
            ->create();
        }

        if(!$this->hasTable("loj_caixa_debito")){
            $loj_caixa_debito = $this->table('loj_caixa_debito',['id' => false, 'primary_key' => ['IDCAIXA','IDMEIOPAGAMENTO']]);
            $loj_caixa_debito->addColumn('IDCAIXA','integer',['limit' => 11])
            ->addColumn('IDMEIOPAGAMENTO','integer',['limit' => 11])
            ->addColumn('IDLOJA','integer',['limit' => 11])
            ->addColumn('VALOR_APURADO','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('VALOR_INFORMADO','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("loj_caixa_retirada")){
            $loj_caixa_retirada = $this->table("loj_caixa_retirada",['id' => false, 'primary_key' => ['IDCAIXARETIRADA']]);
            $loj_caixa_retirada->addColumn('IDCAIXARETIRADA','integer',['limit' => 11])
            ->addColumn('IDLOJA','integer',['limit' => 11])
            ->addColumn('IDTIPODESPESA','integer',['limit' => 11,'null' => true])
            ->addColumn('OBSERVACAO','string',['limit' => 255,'null' => true])
            ->addColumn('VALOR','decimal',['precision' => 10,'scale' => 2,'null' => true])
            ->create();
        }

        if(!$this->hasTable("loj_caixa_sangria")){
            $loj_caixa_sangria = $this->table("loj_caixa_sangria",['id' => 'IDCAIXASANGRIA', 'primary_key' => ['IDCAIXA','IDCAIXASANGRIA']]);
            $loj_caixa_sangria->addColumn('IDCAIXA','integer',['limit' => 11])
            ->addColumn('IDLOJA','integer',['limit' => 11])
            ->addColumn('VALOR','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('OBSERVACAO','text')
            ->create();
        }

        if(!$this->hasTable("loj_contas_pagar")){
            $loj_contas_pagar = $this->table('loj_contas_pagar',['id' => 'IDCONTASPAGAR','engine' => 'MyISAM']);
            $loj_contas_pagar->addColumn('IDLOJA','integer')
            ->addColumn('IDTIPODESPESA','integer')
            ->addColumn('IDTIPODESPESA_PAGAMENTO','integer',['null' =>true])
            ->addColumn('DATA_VENCIMENTO','datetime')
            ->addColumn('DATA_PAGAMENTO','datetime',['null' => true])
            ->addColumn('VALOR_ORIGINAL','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('VALOR_PAGO','decimal',['precision' => 10,'scale' => 2, 'null' => true])
            ->addColumn('DIFERENCA_PAGAMENTO','decimal',['precision' => 10,'scale' => 2, 'null' => true])
            ->addColumn('GOOGLE_CALENDAR','smallinteger',['limit' => 6,'null' => true])
            ->addColumn('NUM_DOCUMENTO','string',['limit' => 20,'null' => true])
            ->addColumn('OBSERVACAO','string',['limit' => 255])
            ->addColumn('TEM_REPETICAO','smallinteger',['limit' => 6, 'default' => '0'])
            ->addColumn('IDEVENTOPAI','integer',['null' => true])
            ->addIndex('OBSERVACAO',['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("loj_devolucao")){
            $loj_devolucao = $this->table("loj_devolucao",['id' => 'IDDEVOLUCAO','engine' => 'MyISAM']);
            $loj_devolucao->addColumn('IDLOJA','integer')
            ->addColumn('DATA_DEVOLUCAO','datetime')
            ->addColumn('VALOR_TOTAL','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('CHAVE_NFE','string',['limit' => 255,'null' => true])
            ->addColumn('UTILIZADO','smallinteger',['limit' => 6, 'default' => '0'])
            ->addColumn('UTILIZADO_EM','date',['null' => true])
            ->addColumn('OBSERVACAO','string',['limit' => 255])
            ->addIndex('OBSERVACAO',['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("loj_devolucao_produto")){
            $loj_devolucao_produto = $this->table("loj_devolucao_produto",['id' => false, 'primary_key' => ['IDDEVOLUCAO','IDPRODUTO']]);
            $loj_devolucao_produto->addColumn('IDDEVOLUCAO','integer')
            ->addColumn('IDPRODUTO','integer')
            ->addColumn('IDLOJA','integer')
            ->addColumn('QUANTIDADE','integer')
            ->addColumn('PRECO_UNITARIO','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("loj_estoque")){
            $loj_estoque = $this->table('loj_estoque',['id' => false, 'primary_key' => ['IDLOJA','IDPRODUTO']]);
            $loj_estoque->addColumn('IDLOJA','integer')
            ->addColumn('IDPRODUTO','integer')
            ->addColumn('QTDE_MIN','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('QTDE_BUY','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('QTDE_MAX','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('REPO_TIME','integer',['default' => '0'])
            ->addColumn('QUANTIDADE','integer')
            ->addColumn('CALCULA_REPOSICAO','smallinteger',['limit' => 1, 'default' => '0'])
            ->addColumn('DATA_ENTRADA','datetime')
            ->addColumn('DATA_ULTIMA_COMPRA','date',['null' => true])
            ->addColumn('DATA_ULTIMA_VENDA','datetime',['null' => true])
            ->create();
        }

        if(!$this->hasTable("loj_estoque_saldo")){
            $loj_estoque_saldo = $this->table('loj_estoque_saldo',['id' => false, 'primary_key' =>['IDLOJA','ANO','MES','IDPRODUTO']]);
            $loj_estoque_saldo->addColumn('IDLOJA','integer')
            ->addColumn('ANO','integer')
            ->addColumn('MES','integer')
            ->addColumn('IDPRODUTO','integer')
            ->addColumn('NOME_PRODUTO','string',['limit' => 255])
            ->addColumn('SKU_PRODUTO','string',['limit' => 45])
            ->addColumn('VALOR_ESTOQUE_INICIAL','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('VALOR_ENTRADA','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('VALOR_SAIDAS','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('VALOR_ESTOQUE_FINAL','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('VALOR_ESTOQUE_MEDIO','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("loj_fluxo_caixa")){
            $loj_fluxo_caixa = $this->table("loj_fluxo_caixa",['id' => 'IDFLUXOCAIXA']);
            $loj_fluxo_caixa->addColumn('IDLOJA','integer')
            ->addColumn('IDOPERACAOFINANCEIRA','integer')
            ->addColumn('DATA_ENTRADA','date')
            ->addColumn('DATA_MOVIMENTO','date',['default' => '0000-00-00'])
            ->addColumn('VALOR','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('HISTORICO','string',['limit' => 255])
            ->addColumn('TIPO_INFORMACAO','char',['limit' => 1])
            ->create();
        }

        if(!$this->hasTable("loj_item_expresso")){
            $loj_item_expresso = $this->table("loj_item_expresso",['id' => false, 'primary_key' => ['IDLOJA','IDPRODUTO']]);
            $loj_item_expresso->addColumn('IDLOJA','integer')
            ->addColumn('IDPRODUTO','integer')
            ->create();
        }

        if(!$this->hasTable("loj_item_troca")){
            $loj_item_troca = $this->table("loj_item_troca",['id' => false, 'primary_key' => ['IDLOJA','IDPRODUTO','IDUSUARIO']]);
            $loj_item_troca->addColumn('IDLOJA','integer')
            ->addColumn('IDPRODUTO','integer')
            ->addColumn('IDUSUARIO','integer')
            ->addColumn('QUANTIDADE','integer')
            ->addColumn('PRECO_UNITARIO','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('NOME_PRODUTO','string',['limit' => 255])
            ->create();
        }

        if(!$this->hasTable("loj_item_venda")){
            $loj_item_venda = $this->table("loj_item_venda",['id' => false, 'primary_key' => ['IDLOJA','IDPRODUTO','IDUSUARIO']]);
            $loj_item_venda->addColumn('IDLOJA','integer')
            ->addColumn('IDPRODUTO','integer')
            ->addColumn('IDUSUARIO','integer')
            ->addColumn('QUANTIDADE','integer')
            ->addColumn('PRECO_UN','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('DESCONTO','decimal',['precision' => 10,'scale' => 2, 'default' => '0.00'])
            ->addColumn('SUBTOTAL','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('NOME_PRODUTO','string',['limit' => 255])
            ->addColumn('SKU_PRODUTO','string',['limit' => 45])
            ->addColumn('UNIDADE_MEDIDA','char',['limit' => 2])
            ->create();
        }

        if(!$this->hasTable("loj_movimento_estoque")){
            $loj_movimento_estoque = $this->table("loj_movimento_estoque",['id' => 'IDMOVIMENTOESTOQUE']);
            $loj_movimento_estoque->addColumn('IDLOJA','integer')
            ->addColumn('IDPRODUTO','integer')
            ->addColumn('DATA_MOVIMENTO','datetime')
            ->addColumn('QUANTIDADE','integer')
            ->addColumn('TIPO_OPERACAO','char',['limit' => 1])
            ->addColumn('OPERACAO','char',['limit' => 2])
            ->addColumn('PRECO_CUSTO','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('NOME_PRODUTO','string',['limit' => 255])
            ->addColumn('SKU_PRODUTO','string',['limit' => 45])
            ->addColumn('IDPRODUTO_PAI','integer',['null' => true])
            ->create();
        }

        if(!$this->hasTable("loj_promocao")){
            $loj_promocao = $this->table('loj_promocao',['id' => 'IDPROMOCAO']);
            $loj_promocao->addColumn('IDLOJA','integer')
            ->addColumn('NOME','string',['limit' => 255])
            ->addColumn('DATA_INICIAL','date')
            ->addColumn('DATA_FINAL','date')
            ->create();
        }

        if(!$this->hasTable("loj_promocao_produto")){
            $loj_promocao_produto = $this->table("loj_promocao_produto",['id' => false, 'primary_key' => ['IDPROMOCAO','IDPRODUTO','IDCONDICAOPAGAMENTO']]);
            $loj_promocao_produto->addColumn('IDPROMOCAO','integer')
            ->addColumn('IDPRODUTO','integer')
            ->addColumn('IDCONDICAOPAGAMENTO','integer')
            ->addColumn('PRECO_PROMO','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("loj_solicitacao_cliente")){
            $loj_solicitacao_cliente = $this->table("loj_solicitacao_cliente",['id' => 'IDSOLICITACAO']);
            $loj_solicitacao_cliente->addColumn('IDLOJA','integer')
            ->addColumn("IDCLIENTE",'integer')
            ->addColumn("DATA_SOLICITACAO",'date')
            ->addColumn('FORMA_CONTATO','char',['limit' => 1])
            ->addColumn('DESEJO','string',['limit' => 255])
            ->addColumn('ATENDIDO','smallinteger',['limit' => 1])
            ->addColumn('ATENDIDO_DATA','datetime',['null' => true])
            ->create();
        }

        if(!$this->hasTable("loj_venda")){
            $loj_venda = $this->table("loj_venda",['id' => 'IDVENDA']);
            $loj_venda->addColumn('IDLOJA','integer')
            ->addColumn('DATA_VENDA','datetime')
            ->addColumn('SUBTOTAL','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('DESCONTO','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('VALOR_PAGO','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('TROCO','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('OPERADOR','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('IDFUNCIONARIO','integer')
            ->addColumn('IDCLIENTE','integer')
            ->create();
        }

        if(!$this->hasTable("loj_venda_pagamento")){
            $loj_venda_pagamento = $this->table("loj_venda_pagamento",['id' => false, 'primary_key' => ['IDVENDA','IDCONDICAOPAGAMENTO','IDLOJA']]);
            $loj_venda_pagamento->addColumn('IDVENDA','integer')
            ->addColumn('IDCONDICAOPAGAMENTO','integer')
            ->addColumn('IDLOJA','integer')
            ->addColumn('VALOR','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("loj_venda_produto")){
            $loj_venda_produto = $this->table("loj_venda_produto",['id' => false, 'primary_key' => ['IDVENDA','IDPRODUTO']]);
            $loj_venda_produto->addColumn('IDVENDA','integer')
            ->addColumn('IDPRODUTO','integer')
            ->addColumn('IDLOJA','integer')
            ->addColumn('QUANTIDADE','integer')
            ->addColumn('PRECO_UNITARIO','decimal',['precision' => 10, 'scale' =>2])
            ->addColumn('SUBTOTAL','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('DESCONTO','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('NOME_PRODUTO','string',['limit' => 255])
            ->addColumn('SKU_PRODUTO','string',['limit' => 45])
            ->addColumn('UNIDADE_MEDIDA','char',['limit' => 2])
            ->create();
        }

        if(!$this->hasTable("loj_venda_produto_item")){
            $loj_venda_produto_item = $this->table("loj_venda_produto_item",['id' => false, 'primary_key' =>['IDVENDA','IDPRODUTO','IDPRODUTO_FILHO']]);
            $loj_venda_produto_item->addColumn('IDVENDA','integer')
            ->addColumn('IDPRODUTO','integer')
            ->addColumn('IDPRODUTO_FILHO','integer')
            ->addColumn('IDLOJA','integer')
            ->addColumn('NOME_PRODUTO_FILHO','string',['limit' => 255])
            ->addColumn('SKU_PRODUTO_FILHO','string',['limit' => 45])
            ->addColumn('DESCONTO','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('PRECO_VENDA','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("sys_atributo")){
            $sys_atributo = $this->table('sys_atributo',['id' => 'IDATRIBUTO']);
            $sys_atributo->addColumn('IDPRODUTOTIPO','integer')
            ->addColumn('NOME','string',['limit' => 50])
            ->create();
        }

        if(!$this->hasTable("sys_atributo_opcao")){
            $sys_atributo_opcao = $this->table('sys_atributo_opcao',['id' => false, 'primary_key' => ['IDATRIBUTO','VALOR']]);
            $sys_atributo_opcao->addColumn('IDATRIBUTO','integer')
            ->addColumn('VALOR','string',['limit' => 50])
            ->addColumn('TEXTO','string',['limit' => 50])
            ->create();
        }

        if(!$this->hasTable("sys_banco")){
            $sys_banco = $this->table('sys_banco',['id' => 'IDBANCO','engine' => 'MyISAM']);
            $sys_banco->addColumn('NOME','string',['limit' => 100])
            ->addColumn('COD_FEBRABAN','string',['limit' => 5])
            ->addIndex('NOME',['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_bandeira_cartao")){
            $sys_bandeira_cartao = $this->table('sys_bandeira_cartao',['id' => 'IDBANDEIRA']);
            $sys_bandeira_cartao->addColumn('IDMEIOPAGAMENTO','integer')
            ->addColumn('NOME','string',['limit' => 45])
            ->addColumn('ICONE','string',['limit' => 255])
            ->addColumn('EDITAVEL','smallinteger',['limit' => 1])
            ->create();
        }

        if(!$this->hasTable("sys_cargo")){
            $sys_cargo = $this->table('sys_cargo',['id' => 'IDCARGO']);
            $sys_cargo->addColumn('IDCARGOTIPO','integer')
            ->addColumn('NOME','string',['limit' => 255])
            ->create();
        }

        if(!$this->hasTable("sys_cargo_tipo")){
            $sys_cargo_tipo = $this->table("sys_cargo_tipo",['id' => 'IDCARGOTIPO']);
            $sys_cargo_tipo->addColumn('NOME','string',['limit' => 255])
            ->addColumn('DESATIVADO','integer')
            ->addColumn('DESATIVADO_DATA','date',['null' => true])
            ->create();
        }

        if(!$this->hasTable("sys_categoria")){
            $sys_categoria = $this->table('sys_categoria',['id' => 'IDCATEGORIA','engine' => 'MyISAM']);
            $sys_categoria->addColumn('NOME','string',['limit' => 100])
            ->addColumn('CATEGORIA_PAI','integer',['null' => true])
            ->addIndex('NOME',['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_categoria_produto")){
            $sys_categoria_produto = $this->table('sys_categoria_produto',['id' => false, 'primary_key' => ['IDCATEGORIA','IDPRODUTO']]);
            $sys_categoria_produto->addColumn('IDCATEGORIA','integer')
            ->addColumn('IDPRODUTO','integer')
            ->create();
        }

        if(!$this->hasTable("sys_cidade")){
            $sys_cidade = $this->table('sys_cidade',['id' => 'IDCIDADE','engine' => 'MyISAM']);
            $sys_cidade->addColumn('NOME','string',['limit' => 255])
            ->addColumn('UF','char',['limit' => 3])
            ->addColumn('COD_UF','integer')
            ->addColumn('COD_IBGE','integer',['null' => true])
            ->addIndex('NOME',['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_cliente")){
            $sys_cliente = $this->table("sys_cliente",['id' => 'IDCLIENTE','engine' => 'MyISAM']);
            $sys_cliente->addColumn('NASCIMENTO','date')
            ->addColumn('GENERO','smallinteger',['limit' => 6])
            ->addColumn('CPFCNPJ','string',['limit' => 30])
            ->addColumn('CEP','string',['limit' => 10])
            ->addColumn('TELEFONE','string',['limit' => 30])
            ->addColumn('TELEFONE2','string',['limit' => 30])
            ->addColumn('NOME','string',['limit' => 255])
            ->addColumn('EMAIL','string',['limit' => 255])
            ->addColumn('DATA_CADASTRO','date',['null' => true])
            ->addColumn('CODIBGE','integer',['null' => true])
            ->addColumn('BAIRRO','string',['limit' => 255,'null' => true])
            ->addIndex('NOME',['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_cliente_avaliacao_produto")){
            $sys_cliente_avaliacao_produto = $this->table('sys_cliente_avaliacao_produto',['id' => false, 'primary_key' => ['IDCLIENTE','IDPRODUTO']]);
            $sys_cliente_avaliacao_produto->addColumn('IDCLIENTE','integer')
            ->addColumn('IDPRODUTO','integer')
            ->addColumn('ESTRELAS','smallinteger',['limit' => 1])
            ->addColumn('OPINIAO','text')
            ->create();
        }

        if(!$this->hasTable("sys_cliente_avaliacao_venda")){
            $sys_cliente_avaliacao_venda = $this->table('sys_cliente_avaliacao_venda',['id' => false, 'primary_key' => ['IDCLIENTE','IDVENDA',],'engine' => 'MyISAM']);
            $sys_cliente_avaliacao_venda->addColumn('IDCLIENTE','integer')
            ->addColumn('IDVENDA','integer')
            ->addColumn('ESTRELA_EMPENHO','smallinteger',['limit' => 1])
            ->addColumn('ESTRELA_CONHECIMENTO','smallinteger',['limit' => 1])
            ->addColumn('ESTRELA_VELOCIDADE','smallinteger',['limit' => 1])
            ->addColumn('ESTRELA_APRESENTACAO','smallinteger',['limit' => 1])
            ->addColumn('SUGESTAO','string',['limit' => 255])
            ->addIndex('SUGESTAO',['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_cliente_indicacao")){
            $sys_cliente_indicacao = $this->table('sys_cliente_indicacao',['id' => false, 'primary_key' => ['INDICADOR','INDICADO']]);
            $sys_cliente_indicacao->addColumn('INDICADOR','integer')
            ->addColumn('INDICADO','integer')
            ->addColumn('VALOR_DESCONTO','integer')
            ->addColumn('VALIDO_ATE','datetime')
            ->addColumn('STATUS','smallinteger',['limit' => 2])
            ->create();
        }

        if(!$this->hasTable("sys_condicao_pagamento")){
            $sys_condicao_pagamento = $this->table('sys_condicao_pagamento',['id' => 'IDCONDICAOPAGAMENTO']);
            $sys_condicao_pagamento->addColumn('PARCELAS','smallinteger',['limit' => 6])
            ->addColumn('DIAS_RECEBIMENTO','smallinteger',['limit' => 6])
            ->addColumn('TAXA_ADM','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('EXIBIR_PDV','smallinteger',['limit' => 6])
            ->addColumn('NOME','string',['limit' => 50])
            ->addColumn('EDITAVEL','smallinteger',['limit' => 1])
            ->addColumn('ATALHO','char',['limit' => 1])
            ->create();
        }

        if(!$this->hasTable("sys_consignado")){
            $sys_consignado = $this->table("sys_consignado",['id' => 'IDCONSIGNADO']);
            $sys_consignado->addColumn('IDFORNECEDOR','integer')
            ->addColumn('DATA_ABERTURA','date')
            ->addColumn('STATUS','char',['limit' => 1])
            ->addColumn('DATA_FECHAMENTO','date',['null' => true])
            ->create();
        }

        if(!$this->hasTable("sys_consignado_produto")){
            $sys_consignado_produto = $this->table('sys_consignado_produto',['id' => false, 'primary_key' => ['IDCONSIGNADO','IDPRODUTO']]);
            $sys_consignado_produto->addColumn('IDCONSIGNADO','integer')
            ->addColumn('IDPRODUTO','integer')
            ->addColumn('QTDE_ADQUIRIDA','integer')
            ->addColumn('QTDE_VENDIDA','integer')
            ->addColumn('QTDE_DEVOLVIDA','integer')
            ->addColumn('QTDE_PAGA','integer')
            ->addColumn('PRECO_COMPRA','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('SKU_PRODUTO','string',['limit' => 45])
            ->addColumn('NOME_PRODUTO','string',['limit' => 255])
            ->create();
        }

        if(!$this->hasTable("sys_cupom")){
            $sys_cupom = $this->table('sys_cupom',['id' => 'IDCUPOM']);
            $sys_cupom->addColumn('CODIGO','string',['limit' => 50])
            ->addColumn('TIPO_VALOR','char',['limit' => 1])
            ->addColumn('VALOR','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('TIPO_CUPOM','char',['limit' => 1])
            ->addColumn('DATA_CRIACAO','date')
            ->addColumn('UTILIZADO','char',['limit' => 1])
            ->addColumn('DESCRICAO','string',['limit' => 255])
            ->addColumn('DATA_UTILIZACAO','date',['null' => true])
            ->addColumn('DATA_VALIDADE','date',['null' => true])
            ->addColumn('OBSERVACAO','string',['limit' => 255])
            ->create();
        }

        if(!$this->hasTable("sys_extrato_bancario")){
            $sys_extrato_bancario = $this->table('sys_extrato_bancario',['id' => 'IDEXTRATOBANCARIO','engine' => 'MyISAM']);
            $sys_extrato_bancario->addColumn('IDTIPODESPESA','integer')
            ->addColumn('DATA_MOVIMENTO','date')
            ->addColumn('VALOR','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('NUM_DOCUMENTO','char',['limit' => 7])
            ->addColumn('HISTORICO','string',['limit' => 255])
            ->addIndex('HISTORICO',['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_fornecedor")){
            $sys_fornecedor = $this->table('sys_fornecedor',['id' => 'IDFORNECEDOR','engine' => 'MyISAM']);
            $sys_fornecedor->addColumn('RAZAO_SOCIAL','string',['limit' => 255])
            ->addColumn('FANTASIA','string',['limit' => 255])
            ->addColumn('CEP','char',['limit' => 9])
            ->addColumn('NUMERO_ENDERECO','integer')
            ->addColumn('PRAZO_ENTREGA','integer')
            ->addColumn('TELEFONE','string',['limit' => 30])
            ->addColumn('TELEFONE2','string',['limit' => 30])
            ->addColumn('ENDERECO','string',['limit' => 300])
            ->addColumn('IDCIDADE','integer')
            ->addColumn('REPRESENTANTE','string',['limit' => 50])
            ->addColumn('IDBANCO','integer',['null' => true])
            ->addColumn('NUM_CONTA','string',['null' => true, 'limit' => 45])
            ->addColumn('AGENCIA','string',['null' => true, 'limit' => 45])
            ->addColumn('TIPO_CONTA','char',['null' => true, 'limit' => 1])
            ->addColumn('NOME_CONTA','string',['null' => true, 'limit' => 45])
            ->addColumn('OBSERVACAO','string',['null' => true, 'limit' => 300])
            ->addIndex(['RAZAO_SOCIAL','FANTASIA','ENDERECO','REPRESENTANTE','OBSERVACAO'],['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_fornecedor_cnpj")){
            $sys_fornecedor_cnpj = $this->table("sys_fornecedor_cnpj",['id' => false, 'primary_key' => ['IDFORNECEDOR','CNPJ']]);
            $sys_fornecedor_cnpj->addColumn('IDFORNECEDOR','integer')
            ->addColumn('CNPJ','string',['limit' => 20])
            ->create();
        }

        if(!$this->hasTable("sys_funcionario")){
            $sys_funcionario = $this->table('sys_funcionario',['id' => 'IDFUNCIONARIO','engine' => 'MyISAM']);
            $sys_funcionario->addColumn('NOME','string',['limit' => 255])
            ->addColumn('APELIDO','string',['limit' => 50])
            ->addColumn('EMAIL','string',['limit' => 255])
            ->addColumn('NASCIMENTO','date')
            ->addColumn('RG','string',['limit' => 50])
            ->addColumn('CPF','string',['limit' => 15])
            ->addColumn('DATA_CADASTRO','date')
            ->addColumn('STATUS','char',['limit' => 1])
            ->addColumn('ENDERECO','string',['limit' => 255])
            ->addColumn('BAIRRO','string',['limit' => 100])
            ->addColumn('CEP','string',['limit' => 10])
            ->addColumn('IDCIDADE','integer')
            ->addColumn('TELEFONE','string',['limit' => 20])
            ->addColumn('TELEFONE2','string',['limit' => 20])
            ->addColumn('RECADOS','string',['limit' => 80])
            ->addColumn('IDCARGO','integer')
            ->addColumn('DATA_DEMISSAO','date',['null' => true])
            ->addColumn('DESATIVADO','smallinteger',['limit' => 1, 'default' => '0'])
            ->addColumn('DATA_DESATIVADO','date',['null' => true])
            ->addColumn('IDUSUARIO','integer')
            ->addIndex(['NOME','ENDERECO','APELIDO','BAIRRO','RECADOS'],['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_funcionario_loja")){
            $sys_funcionario_loja = $this->table('sys_funcionario_loja',['id' => false, 'primary_key' => ['IDFUNCIONARIO','IDLOJA']]);
            $sys_funcionario_loja->addColumn('IDFUNCIONARIO','integer')
            ->addColumn('IDLOJA','integer')
            ->create();
        }

        if(!$this->hasTable("sys_galeria_album")){
            $sys_galeria_album = $this->table("sys_galeria_album",['id' => 'IDALBUM','engine' => 'MyISAM']);
            $sys_galeria_album->addColumn('NOME','string',['limit' => 80])
            ->addColumn('CRIADO_EM','datetime')
            ->addColumn('MODIFICADO_EM','datetime')
            ->addIndex('NOME',['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_galeria_imagem")){
            $sys_galeria_imagem = $this->table("sys_galeria_imagem",['id' => 'IDIMAGEM','engine' => 'MyISAM']);
            $sys_galeria_imagem->addColumn('IDALBUM','integer')
            ->addColumn('NOME','string',['limit' => 255])
            ->addColumn('ARQUIVO','string',['limit' => 255])
            ->addColumn('TAMANHO','float')
            ->addColumn('DIMENSAO','string',['limit' => 45])
            ->addColumn('CRIADO_EM','datetime')
            ->addColumn('MODIFICADO_EM','datetime',['null' => true])
            ->addIndex('NOME',['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_loja")){
            $sys_loja = $this->table('sys_loja',['id' => 'IDLOJA','engine' => 'MyISAM']);
            $sys_loja->addColumn('IDCIDADE','integer')
            ->addColumn('CEP','char',['limit' => 9])
            ->addColumn('NOME','string',['limit' => 70])
            ->addColumn('ENDERECO','string',['limit' => 255])
            ->addColumn('ENDERECO_NUM','integer')
            ->addColumn('ENDERECO_COMPLEMENT','string',['limit' => 50,'null' => true])
            ->addColumn('TELEFONE','string',['limit' => 50])
            ->addColumn('RESPONSAVEL','string',['limit' => 50])
            ->addColumn('RESPONSAVEL_TEL','string',['limit' => 30,'null' => true])
            ->addColumn('BAIRRO','string',['limit' => 80])
            ->addColumn('CNAE','string',['limit' => 30,'null' => true])
            ->addColumn('CNPJ','string',['limit' => 30,'null' => true])
            ->addColumn('INSCRICAO_ESTADUAL','string',['limit' => 30, 'null' => true])
            ->addColumn('INSCRICAO_MUNICIPAL','string',['limit' => 30,'null' => true])
            ->addColumn('NOME_FANTASIA','string',['limit' => 255,'null' => true])
            ->addColumn('RAZAO_SOCIAL','string',['limit' => 255,'null' => true])
            ->addColumn('DESCONTO_MAXIMO_SEM_SENHA','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('DESCONTO_SENHA','string',['limit' => 255])
            ->addColumn('VENDE_ESTOQUE_ZERADO','smallinteger',['limit' => 1, 'default' => '0'])
            ->addColumn('NFE_EMITE','smallinteger',['limit' => 2,'default' => '0','comment' => '0 = Nao, 1 = Sim, 2 = Escolher'])
            ->addColumn('NFE_AMBIENTE','smallinteger',['limit' => 1, 'null' => true])
            ->addColumn('NFE_TIPO_EMISSAO','smallinteger',['limit' => 1, 'null' => true])
            ->addColumn('NFE_TRIBUTACAO','decimal',['precision' => 10,'scale' => 2, 'null' => true])
            ->addColumn('NFE_UF_DEST','char',['limit' => 2, 'null' => true])
            ->addColumn('NFE_CSC','string',['limit' => 255, 'null' => true])
            ->addColumn('NFE_CSC_TOKEN','string',['limit' => 10, 'null' => true])
            ->addIndex(['NOME','ENDERECO','RAZAO_SOCIAL','NOME_FANTASIA','RESPONSAVEL'],['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_meio_condicao")){
            $sys_meio_condicao = $this->table('sys_meio_condicao',['id' => false, 'primary_key' => ['IDMEIOPAGAMENTO','IDCONDICAOPAGAMENTO']]);
            $sys_meio_condicao->addColumn('IDMEIOPAGAMENTO','integer')
            ->addColumn('IDCONDICAOPAGAMENTO','integer')
            ->create();
        }

        if(!$this->hasTable("sys_meio_pagamento")){
            $sys_meio_pagamento = $this->table('sys_meio_pagamento',['id' => 'IDMEIOPAGAMENTO']);
            $sys_meio_pagamento->addColumn('NOME','string',['limit' => 50])
            ->addColumn('CODIGO_NFE','string',['limit' => 3])
            ->addColumn('EDITAVEL','smallinteger',['limit' => 1])
            ->create();
        }

        if(!$this->hasTable("sys_ncm")){
            $sys_ncm = $this->table("sys_ncm",['id' => 'IDNCM']);
            $sys_ncm->addColumn('CODIGO_NCM','string',['limit' => 8])
            ->addColumn('NOME','string',['limit' => 100])
            ->create();
        }

        if(!$this->hasTable("sys_nfce")){
            $sys_nfce = $this->table('sys_nfce',['id' => 'IDNFCE']);
            $sys_nfce->addColumn('DATA_EMISSAO','datetime')
            ->addColumn('VALOR_NOTA','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('IDVENDA','integer')
            ->addColumn('CHAVE_NFE','string',['limit' => 200])
            ->addColumn('ARQUIVO_XML','text',['limit' => MysqlAdapter::TEXT_LONG])
            ->create();
        }

        if(!$this->hasTable("sys_nfe_devolucao")){
            $sys_nfe_devolucao = $this->table('sys_nfe_devolucao',['id' => 'IDNFEDEVOLUCAO']);
            $sys_nfe_devolucao->addColumn('NUMERO','integer')
            ->addColumn('NFE_ORIGEM','string',['limit' => 45])
            ->addColumn('NFEID','string',['limit' => 45])
            ->addColumn('DATA_EMISSAO','datetime')
            ->addColumn('CPFCNPJ_DESTINATARIO','string',['limit' => 45])
            ->addColumn('NOME_DESTINATARIO','string',['limit' => 45])
            ->addColumn('VALOR_NOTA','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('ARQUIVO_XML','text',['limit' => MysqlAdapter::TEXT_LONG])
            ->create();
        }

        if(!$this->hasTable("sys_nfe_recebida")){
            $sys_nfe_recebida = $this->table("sys_nfe_recebida",['id' => 'IDNFERECEBIDA']);
            $sys_nfe_recebida->addColumn('NUMERO','integer')
            ->addColumn('DATA_EMISSAO','datetime')
            ->addColumn('VALOR_PRODUTOS','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('VALOR_NOTA','decimal',['precision' => 10, 'scale' =>2])
            ->addColumn('VALOR_FRETE','decimal',['precision' => 10, 'scale' =>2])
            ->addColumn('NUM_VOLUME','integer')
            ->addColumn('CPFCNPJ_EMITENTE','string',['limit' => 45])
            ->addColumn('NOME_EMITENTE','string',['limit' => 255])
            ->addColumn('FANTASIA_EMITENTE','string',['limit' => 255])
            ->addColumn('NFEID','string',['limit' => 255])
            ->addColumn('PROCESSADA','integer',['limit' => 1,'default' => '0'])
            ->addColumn('TIPO_ENTRADA','char',['limit' => 1, 'default' => 'A'])
            ->addColumn('ARQUIVO_XML','text',['limit' => MysqlAdapter::TEXT_LONG])
            ->create();
        }

        if(!$this->hasTable("sys_nfe_recebida_duplicata")){
            $sys_nfe_recebida_duplicata = $this->table('sys_nfe_recebida_duplicata',['id' => 'IDNFEDUPLICATA']);
            $sys_nfe_recebida_duplicata->addColumn('IDNFERECEBIDA','integer')
            ->addColumn('NUM_DUPLICATA','string',['limit' => 45])
            ->addColumn('DATA_VENCIMENTO','datetime')
            ->addColumn('VALOR_DUPLICATA','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("sys_nfe_recebida_item")){
            $sys_nfe_recebida_item = $this->table('sys_nfe_recebida_item',['id' => false, 'primary_key' => ['IDNFERECEBIDA','IDITEM']]);
            $sys_nfe_recebida_item->addColumn('IDNFERECEBIDA','integer')
            ->addColumn('IDITEM','integer')
            ->addColumn('EAN_ITEM','string',['limit' => 45])
            ->addColumn('COD_PRODUTO','string',['limit' => 45])
            ->addColumn('NOME_PRODUTO','string',['limit' => 255])
            ->addColumn('NCM','string',['limit' => 20])
            ->addColumn('CSOSN','string',['limit' => 10])
            ->addColumn('UNIDADE_COMERCIAL','char',['limit'=> 5])
            ->addColumn('QUANTIDADE_COMERCIAL','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('VALOR_UNITARIO','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("sys_nfe_recebida_nsu")){
            $sys_nfe_recebida_nsu = $this->table("sys_nfe_recebida_nsu",['id' => false, 'primary_key' => ['NSU']]);
            $sys_nfe_recebida_nsu->addColumn('NSU','integer')
            ->addColumn('CHAVE_NFE','string',['limit' => 100])
            ->addColumn('CNPJ_FORNECEDOR','string',['limit' => 30])
            ->addColumn('DATA_HORA_EMISSAO','datetime')
            ->create();
        }

        if(!$this->hasTable("sys_opcao")){
            $sys_opcao = $this->table("sys_opcao",['id' => false, 'primary_key' => 'OPCAO_NOME']);
            $sys_opcao->addColumn('OPCAO_NOME','string',['limit' => 30])
            ->addColumn('OPCAO_VALOR','string',['limit' => 255])
            ->create();
        }

        if(!$this->hasTable("sys_operacao_entrada")){
            $sys_operacao_entrada = $this->table("sys_operacao_entrada",['id' => false, 'primary_key' => ['IDOPERACAOFINANCEIRA','IDMEIOPAGAMENTO']]);
            $sys_operacao_entrada->addColumn('IDOPERACAOFINANCEIRA','integer')
            ->addColumn('IDMEIOPAGAMENTO','integer')
            ->create();
        }

        if(!$this->hasTable("sys_operacao_financeira")){
            $sys_operacao_financeira = $this->table("sys_operacao_financeira",['id' => 'IDOPERACAOFINANCEIRA']);
            $sys_operacao_financeira->addColumn('NOME','string',['limit' => 80])
            ->addColumn('TIPO_OPERACAO','char',['limit' => 1])
            ->addColumn('ORDEM','smallinteger',['limit' => 6])
            ->create();
        }

        if(!$this->hasTable("sys_operacao_saida")){
            $sys_operacao_saida = $this->table("sys_operacao_saida",['id' => false, 'primary_key' => ['IDOPERACAOFINANCEIRA','IDTIPODESPESA']]);
            $sys_operacao_saida->addColumn('IDOPERACAOFINANCEIRA','integer')
            ->addColumn('IDTIPODESPESA','integer')
            ->create();
        }

        if(!$this->hasTable("sys_orcamento")){
            $sys_orcamento = $this->table("sys_orcamento",['id' => false, 'primary_key' => ['IDLOJA','ANO']]);
            $sys_orcamento->addColumn('IDLOJA','integer')
            ->addColumn('ANO','integer')
            ->addColumn('NOME','string',['limit' => 250])
            ->create();
        }

        if(!$this->hasTable("sys_orcamento_valor")){
            $sys_orcamento_valor = $this->table("sys_orcamento_valor",['id' => false, 'primary_key' => ['IDLOJA','IDOPERACAOFINANCEIRA','ANO','MES']]);
            $sys_orcamento_valor->addColumn('IDLOJA','integer')
            ->addColumn('IDOPERACAOFINANCEIRA','integer')
            ->addColumn('ANO','integer')
            ->addColumn('MES','integer')
            ->addColumn('VALOR','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("sys_parceiro")){
            $sys_parceiro = $this->table("sys_parceiro",['id' => 'IDPARCEIRO']);
            $sys_parceiro->addColumn("NOME",'string',['limit' => 255])
            ->addColumn("CODIGO_CUPOM","string",["limit" => 50])
            ->addColumn("PERC_DESCONTO","decimal",["precision" => 10,'scale' => 2])
            ->addColumn("DATA_INICIO","date")
            ->addColumn("DATA_FIM","date",['null' => true])
            ->create();
        }

        if(!$this->hasTable("sys_pedido_compra")){
            $sys_pedido_compra = $this->table("sys_pedido_compra",['id' => 'IDPEDIDOCOMPRA']);
            $sys_pedido_compra->addColumn('IDFORNECEDOR','integer')
            ->addColumn("DATA_PEDIDO","date")
            ->addColumn("VALOR_TOTAL","decimal",['precision' => 10,'scale' => 2])
            ->addColumn("FORMA_PAGAMENTO","string",['limit' => 50])
            ->addColumn("ARQUIVO","string",['limit' => 255])
            ->addColumn("CONTEUDO","text")
            ->create();
        }

        if(!$this->hasTable("sys_pedido_venda")){
            $sys_pedido_venda = $this->table("sys_pedido_venda",['id' => 'IDPEDIDOVENDA']);
            $sys_pedido_venda->addColumn('IDCLIENTE','integer')
            ->addColumn('DATA_PEDIDO','datetime')
            ->addColumn('TOTAL_PAGO','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('CUPOM','string',['limit' => 25])
            ->addColumn('STATUS','char',['limit' => 1])
            ->addColumn('OBSERVACAO','string',['limit' => 255])
            ->create();
        }

        if(!$this->hasTable("sys_pedido_venda_item")){
            $sys_pedido_venda_item = $this->table("sys_pedido_venda_item",['id' => false, 'primary_key' => ['IDPEDIDOVENDA','IDPRODUTO']]);
            $sys_pedido_venda_item->addColumn('IDPEDIDOVENDA','integer')
            ->addColumn('IDPRODUTO','integer')
            ->addColumn('NOME_PRODUTO','string',['limit' => 255])
            ->addColumn('SKU_PRODUTO','string',['limit' => 45])
            ->addColumn('QUANTIDADE','integer')
            ->create();
        }

        if(!$this->hasTable("sys_produto")){
            $sys_produto = $this->table("sys_produto",['id' => 'IDPRODUTO','engine' => 'MyISAM']);
            $sys_produto->addColumn('IDPRODUTOTIPO','integer')
            ->addColumn('IDFORNECEDOR','integer')
            ->addColumn('PRECO_COMPRA','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('PRECO_VENDA','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('MARKUP','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('DATA_CADASTRO','date')
            ->addColumn('ESTRUTURA','char',['limit' => 1, 'comment' => "S = Simples, C = Composta, V = Variavel"])
            ->addColumn('CODIGO_BARRA','string',['limit' => 45])
            ->addColumn('SKU','string',['limit' => 45])
            ->addColumn('COD_FORNECE','string',['limit' => 45])
            ->addColumn('NCM','string',['limit' => 45 ,'null' =>true])
            ->addColumn('CSOSN','string',['limit' => 8, 'null' => true])
            ->addColumn('NOME','string',['limit' => 255])
            ->addColumn('NOME_TAG','string',['limit' => 50])
            ->addColumn('IMAGEM','string',['limit' => 255, 'null' => true])
            ->addColumn('UNIDADE_MEDIDA','char',['limit' => 2])
            ->addColumn('STATUS','char',['limit' => 1, 'comment' => "A = Ativo, D = Desativado",'default' => 'A'])
            ->addColumn('DATA_DESATIVADO','datetime',['null' => true])
            ->addIndex(['SKU','CODIGO_BARRA'],['unique' => true])
            ->addIndex(["CODIGO_BARRA"])
            ->addIndex(['NOME','NOME_TAG'],['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_produto_atributo")){
            $sys_produto_atributo = $this->table('sys_produto_atributo',['id' => false, 'primary_key' => ['IDPRODUTO','IDATRIBUTO']]);
            $sys_produto_atributo->addColumn('IDPRODUTO','integer')
            ->addColumn('IDATRIBUTO','integer')
            ->addColumn('VALOR','string',['limit' => 255])
            ->create();
        }

        if(!$this->hasTable("sys_produto_item")){
            $sys_produto_item = $this->table("sys_produto_item",['id' => false, 'primary_key' => ['IDPRODUTO','IDPRODUTO_FILHO']]);
            $sys_produto_item->addColumn('IDPRODUTO','integer')
            ->addColumn('IDPRODUTO_FILHO','integer')
            ->create();
        }

        if(!$this->hasTable("sys_produto_nfe_recebida_item")){
            $sys_produto_nfe_recebida_item = $this->table("sys_produto_nfe_recebida_item",['id' => false, 'primary_key' => ['IDPRODUTO','COD_PRODUTO','NOME_PROD_NFE']]);
            $sys_produto_nfe_recebida_item->addColumn('IDPRODUTO','integer')
            ->addColumn('COD_PRODUTO','string',['limit' => 45])
            ->addColumn('NOME_PROD_NFE','string',['limit' => 255])
            ->addColumn('QTDE_ORIGEM','integer')
            ->addColumn('QTDE_DESTINO','integer')
            ->create();
        }

        if(!$this->hasTable("sys_produto_tipo")){
            $sys_produto_tipo = $this->table('sys_produto_tipo',['id' => 'IDPRODUTOTIPO']);
            $sys_produto_tipo->addColumn('DESCRICAO','string',['limit' => 255])
            ->create();
        }

        if(!$this->hasTable("sys_regra_sku")){
            $sys_regra_sku = $this->table('sys_regra_sku',['id' => 'IDREGRASKU','engine' => 'MyISAM']);
            $sys_regra_sku->addColumn('IDPRODUTOTIPO','integer')
            ->addColumn('REGRA','text')
            ->addColumn('FORMATO_REGRA','string',['limit' => 45])
            ->addIndex('REGRA',['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_template_email")){
            $sys_template_email = $this->table("sys_template_email",['id' => 'IDTEMPLATEEMAIL','engine' => 'MyISAM']);
            $sys_template_email->addColumn('NOME','string',['limit' => 255])
            ->addColumn('ASSUNTO','string',['limit' => 255,'null' => true])
            ->addColumn('HTML','text')
            ->addIndex(['NOME','ASSUNTO','HTML'],['type' => 'fulltext'])
            ->create();
        }

        if(!$this->hasTable("sys_tipo_despesa")){
            $sys_tipo_despesa = $this->table("sys_tipo_despesa",['id' => 'IDTIPODESPESA']);
            $sys_tipo_despesa->addColumn("NOME",'string',['limit' => 100])
            ->addColumn('EXIBE_LOJA','smallinteger',['limit' => 6])
            ->addColumn('SALVA_CALENDARIO','smallinteger',['limit' => 6])
            ->create();
        }

        if(!$this->hasTable("sys_transferencia")){
            $sys_transferencia = $this->table("sys_transferencia",['id' => 'IDTRANSFERENCIA']);
            $sys_transferencia->addColumn('NOME','string',['limit' => 50])
            ->addColumn('IDLOJA_ORIGEM','integer')
            ->addColumn('IDLOJA_DESTINO','integer')
            ->addColumn('DATA_CRIACAO','datetime')
            ->addcolumn('DATA_VALIDADE','datetime')
            ->addColumn('DATA_EXECUCAO','datetime',['null' => true])
            ->addColumn('STATUS','char',['limit' => 1, 'comment' => "F = Finalizada, P = Pendente, C = Cancelada, D = Divergente"])
            ->create();
        }

        if(!$this->hasTable("sys_transferencia_item")){
            $sys_transferencia_item = $this->table("sys_transferencia_item",['id' => false, 'primary_key' => ['IDTRANSFERENCIA','IDPRODUTO']]);
            $sys_transferencia_item->addColumn('IDTRANSFERENCIA','integer')
            ->addColumn('IDPRODUTO','integer')
            ->addColumn('QUANTIDADE','integer')
            ->addColumn('NOME_PRODUTO','string',['limit' => 255])
            ->addColumn('SKU_PRODUTO','string',['limit' => 45])
            ->addColumn('PRECO_CUSTO','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("sys_users")){
            $sys_users = $this->table('sys_users');
            $sys_users->addColumn('username','string',['limit' => 50])
            ->addColumn('password','string',['limit' => 255])
            ->addColumn('role','string',['limit' => 20])
            ->addColumn('created','datetime')
            ->addColumn('modified','datetime',['null' => true])
            ->addColumn('storeid','integer')
            ->addColumn('name','string',['limit' => 255])
            ->addColumn('trash','char',['limit' => 1])
            ->addColumn('trash_date','datetime',['null' => true])
            ->addColumn('first_access','char',['limit' => 1])
            ->create();
        }

        if(!$this->hasTable("sys_vale_presente")){
            $sys_vale_presente = $this->table('sys_vale_presente',['id' => 'IDVALEPRESENTE']);
            $sys_vale_presente->addColumn('CODIGO','string',['limit' => 45])
            ->addColumn('DATA_EMISSAO','datetime')
            ->addColumn('VALOR','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('UTILIZADO','smallinteger',['limit' => 6])
            ->addColumn('NOME','string',['limit' => 100])
            ->create();
        }

        if(!$this->hasTable("tmp_cupom")){
            $tmp_cupom = $this->table("tmp_cupom",['id' => false, 'primary_key' => 'IDCUPOM']);
            $tmp_cupom->addColumn('IDCUPOM','integer')
            ->addColumn('IDLOJA','integer')
            ->addColumn('IDUSUARIO','integer')
            ->addColumn('TIPO_VALOR','char',['limit' => 1])
            ->addColumn('TIPO_CUPOM','char',['limit' => 1])
            ->addColumn('VALOR','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("tmp_entrada_fluxo")){
            $tmp_entrada_fluxo = $this->table('tmp_entrada_fluxo',['id' => false, 'primary_key' => ['IDOPERACAOFINANCEIRA','IDMEIOPAGAMENTO']]);
            $tmp_entrada_fluxo->addColumn('IDOPERACAOFINANCEIRA','integer')
            ->addColumn('IDMEIOPAGAMENTO','integer')
            ->addColumn('NOME','string',['limit' => 45])
            ->create();
        }

        if(!$this->hasTable("tmp_nfe_divergencia")){
            $tmp_nfe_divergencia = $this->table('tmp_nfe_divergencia',['id' => false, 'primary_key' => 'IDPRODUTO']);
            $tmp_nfe_divergencia->addColumn('IDPRODUTO','integer')
            ->addColumn('NOME_PRODUTO','string',['limit' => 255])
            ->addColumn('SKU_PRODUTO','string',['limit' => 45])
            ->addColumn('PRECO_NFE','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('PRECO_PRODUTO','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("tmp_nfe_recebida_vinculo")){
            $tmp_nfe_recebida_vinculo = $this->table("tmp_nfe_recebida_vinculo",['id' => false, 'primary_key' => ['COD_PRODUTO_NFE','NOM_PRODUTO_NFE']]);
            $tmp_nfe_recebida_vinculo->addColumn('COD_PRODUTO_NFE','string',['limit' => 45])
            ->addColumn('NOM_PRODUTO_NFE','string',['limit' => 255])
            ->addColumn('PRODUTOS','string',['limit' => 255])
            ->addColumn('QUANTIDADES','string',['limit' => 255])
            ->create();
        }

        if(!$this->hasTable("tmp_orcamento_valor")){
            $tmp_orcamento_valor = $this->table("tmp_orcamento_valor",['id' => false,'primary_key' => ['IDOPERACAOFINANCEIRA','ANO','MES']]);
            $tmp_orcamento_valor->addColumn('IDOPERACAOFINANCEIRA','integer')
            ->addColumn('ANO','integer')
            ->addColumn('MES','integer')
            ->addColumn('NOME_OPERACAO','string',['limit' => 255])
            ->addColumn('VALOR','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("tmp_pagamento")){
            $tmp_pagamento = $this->table("tmp_pagamento",['id' => false, 'primary_key' => ['IDCONDICAOPAGAMENTO','IDLOJA','IDUSUARIO']]);
            $tmp_pagamento->addColumn('IDCONDICAOPAGAMENTO','integer')
            ->addColumn('IDLOJA','integer')
            ->addColumn('IDUSUARIO','integer')
            ->addColumn('CONDICAO_PAGAMENTO','string',['limit' => 255])
            ->addColumn('VALOR_PAGO','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('VALOR_PARCELA','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("tmp_produto_item")){
            $tmp_produto_item = $this->table('tmp_produto_item',['id' => false, 'primary_key' => ['IDPRODUTO']]);
            $tmp_produto_item->addColumn('IDPRODUTO','integer')
            ->addColumn('SKU_PRODUTO','string',['limit' => 45])
            ->addColumn('PRECO_VENDA','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("tmp_promocao")){
            $tmp_promocao = $this->table("tmp_promocao",['id' => false, 'primary_key' => ['IDPRODUTO','IDCONDICAOPAGAMENTO']]);
            $tmp_promocao->addColumn('IDPRODUTO','integer')
            ->addColumn('IDCONDICAOPAGAMENTO','integer')
            ->addColumn('CONDICAO_PAGAMENTO','string',['limit' => 255])
            ->addColumn('PRECO_PROMO','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('SKU','string',['limit' => 45])
            ->addColumn('NOME','string',['limit' => 255])
            ->addColumn('PRECO_VENDA','decimal',['precision' => 10,'scale' => 2])
            ->create();
        }

        if(!$this->hasTable("tmp_saida_fluxo")){
            $tmp_saida_fluxo = $this->table("tmp_saida_fluxo",['id' => false, 'primary_key' => ['IDOPERACAOFINANCEIRA','IDTIPODESPESA']]);
            $tmp_saida_fluxo->addColumn('IDOPERACAOFINANCEIRA','integer')
            ->addColumn('IDTIPODESPESA','integer')
            ->addColumn('NOME','string',['limit' => 45])
            ->create();
        }

        if(!$this->hasTable("tmp_transferencia_item")){
            $tmp_transferencia_item = $this->table("tmp_transferencia_item",['id' => false, 'primary_key' => ['IDPRODUTO']]);
            $tmp_transferencia_item->addColumn('IDPRODUTO','integer')
            ->addColumn('QUANTIDADE','integer')
            ->addColumn('NOME_PRODUTO','string',['limit' => 255])
            ->addColumn('SKU_PRODUTO','string',['limit' => 45])
            ->addColumn('PRECO_CUSTO','decimal',['precision' => 10,'scale' => 2])
            ->addColumn('DISPONIVEL_TRNASFER','integer')
            ->create();
        }
    }
}
