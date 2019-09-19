<nav>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a class="nav-item nav-link active" id="nav-financial-tab" data-toggle="tab" href="#nav-financial" role="tab" aria-controls="nav-financial" aria-selected="true">Financeiro</a>
    <a class="nav-item nav-link" id="nav-sales-tab" data-toggle="tab" href="#nav-sales" role="tab" aria-controls="nav-sales" aria-selected="false">Comercial</a>
    <a class="nav-item nav-link" id="nav-supplies-tab" data-toggle="tab" href="#nav-supplies" role="tab" aria-controls="nav-supplies" aria-selected="false">Suprimentos</a>
    <a class="nav-item nav-link" id="nav-tributary-tab" data-toggle="tab" href="#nav-tributary" role="tab" aria-controls="nav-hr" aria-selected="true">Tribut&aacute;rio</a>
    <a class="nav-item nav-link" id="nav-comunicate-tab" data-toggle="tab" href="#nav-comunicate" role="tab" aria-controls="nav-comunicate" aria-selected="false">Comunica&ccedil;&atilde;o</a>
    <a class="nav-item nav-link" id="nav-system-tab" data-toggle="tab" href="#nav-system" role="tab" aria-controls="nav-system" aria-selected="false">Sistema</a>
    <a class="nav-item nav-link" id="nav-integrations-tab" data-toggle="tab" href="#nav-integrations" role="tab" aria-controls="nav-system" aria-selected="false">Integra&ccedil;&otilde;es</a>
  </div>
</nav>
<div class="tab-content" id="nav-tabContent">
	<!-- Financeiro -->
	<div class="tab-pane fade show active" id="nav-financial" role="tabpanel" aria-labelledby="nav-financial-tab">
		<br/>
			<div class="form-row">
				<div class="form-group col-6">
					<label for="DEFAULT_FINANC_TRANS">Opera&ccedil;&atilde;o Financeira de Saldo Inicial</label>
					<select id='DEFAULT_FINANC_TRANS' name='DEFAULT_FINANC_TRANS' class='form-control form-control-sm'>
                        <option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($opfinanc_list as $operacao):?>
                        <option value='<?= $operacao->IDOPERACAOFINANCEIRA;?> '<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'DEFAULT_FINANC_TRANS']))? (($tblOpcao->get("DEFAULT_FINANC_TRANS")->OPCAO_VALOR==$operacao->IDOPERACAOFINANCEIRA)? " selected":"") :"" );?>><?= $operacao->NOME; ?></option>
                        <?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-6">
					<label for='DEFAULT_EXPENSE_FINANC'>Tipo de Despesa padr&atilde;o para fatura(s) de NF-e</label>
					<select id='DEFAULT_EXPENSE_FINANC' name='DEFAULT_EXPENSE_TYPE' class='form-control form-control-sm'>
                        <option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($tipdesp_list as $despesa): ?>
                        <option value='<?=$despesa->IDTIPODESPESA; ?>'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'DEFAULT_EXPENSE_TYPE']))? (($tblOpcao->get("DEFAULT_EXPENSE_TYPE")->OPCAO_VALOR==$despesa->IDTIPODESPESA)? " selected":"") :"" );?>><?=$despesa->NOME;?></option>
                        <?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-6">
					<label for='DEFAULT_FINANC_CARD'>Opera&ccedil;&atilde;o Financeira de Taxas de Cart&atilde;o</label>
                    <select id='DEFAULT_FINANC_CARD' name='DEFAULT_FINANC_CARD' class='form-control form-control-sm'>
                        <option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($opfinanc_list as $operacao):?>
                        <option value='<?= $operacao->IDOPERACAOFINANCEIRA;?> '<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'DEFAULT_FINANC_CARD']))? (($tblOpcao->get("DEFAULT_FINANC_CARD")->OPCAO_VALOR==$operacao->IDOPERACAOFINANCEIRA)? " selected":"") :"" );?>><?= $operacao->NOME; ?></option>
                        <?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-6">
					<label for='DEFAULT_EXPENSE_REMOVAL'>Tipo de Despesa padr&atilde;o para Sangria de Caixa</label>
                    <select id='DEFAULT_EXPENSE_REMOVAL' name='DEFAULT_EXPENSE_REMOVAL' class='form-control form-control-sm'>
                        <option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($tipdesp_list as $despesa): ?>
                        <option value='<?=$despesa->IDTIPODESPESA;?>'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'DEFAULT_EXPENSE_REMOVAL']))? (($tblOpcao->get("DEFAULT_EXPENSE_REMOVAL")->OPCAO_VALOR==$despesa->IDTIPODESPESA)? " selected":"") :"" );?>><?=$despesa->NOME;?></option>
                        <?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-sm">
					<label for="FIRST_TIME_EVENT">Hor&aacute;rio do primeiro evento do calend&aacute;rio</label>
					<input type="number" id="FIRST_TIME_EVENT" name="FIRST_TIME_EVENT" class="form-control form-control-sm" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'FIRST_TIME_EVENT']))?$tblOpcao->get("FIRST_TIME_EVENT")->OPCAO_VALOR:'';?>"/>
				</div>
			</div>
	</div>

	<!-- Comercial -->
	<div class="tab-pane fade" id="nav-sales" role="tabpanel" aria-labelledby="nav-sales-tab">
		<br/><fieldset><legend>Condi&ccedil;&atilde;o de Pagamento</legend>
			<div class="form-row">
				<div class="form-group col-3">
					<label for='PAYMENT_CONDITION_DISCOUNT'>Cupom Desconto</label>
					<select class='form-control form-control-sm' id='PAYMENT_CONDITION_DISCOUNT' name='PAYMENT_CONDITION_DISCOUNT'>
						<option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($payment_list as $condicao): ?>
						<option value='<?=$condicao->IDCONDICAOPAGAMENTO;?>'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'PAYMENT_CONDITION_DISCOUNT']))? (($tblOpcao->get("PAYMENT_CONDITION_DISCOUNT")->OPCAO_VALOR==$condicao->IDCONDICAOPAGAMENTO)? " selected":"") :"" );?>><?=$condicao->NOME;?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-3">
					<label for='PAYMENT_CONDITION_ORDER_SELL'>Pedido de Compra</label>
					<select class='form-control form-control-sm' id='PAYMENT_CONDITION_ORDER_SELL' name='PAYMENT_CONDITION_ORDER_SELL'>
						<option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($payment_list as $condicao): ?>
						<option value='<?=$condicao->IDCONDICAOPAGAMENTO;?>'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'PAYMENT_CONDITION_ORDER_SELL']))? (($tblOpcao->get("PAYMENT_CONDITION_ORDER_SELL")->OPCAO_VALOR==$condicao->IDCONDICAOPAGAMENTO)? " selected":"") :"" );?>><?=$condicao->NOME;?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-3">
					<label for='PAYMENT_CONDITION_GIFT'>Vale Presente</label>
					<select class='form-control form-control-sm' id='PAYMENT_CONDITION_GIFT' name='PAYMENT_CONDITION_GIFT'>
						<option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($payment_list as $condicao): ?>
						<option value='<?=$condicao->IDCONDICAOPAGAMENTO;?>'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'PAYMENT_CONDITION_GIFT']))? (($tblOpcao->get("PAYMENT_CONDITION_GIFT")->OPCAO_VALOR==$condicao->IDCONDICAOPAGAMENTO)? " selected":"") :"" );?>><?=$condicao->NOME;?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-3">
					<label for='PAYMENT_CONDITION_CHANGE'>Troca</label>
					<select class='form-control form-control-sm' id='PAYMENT_CONDITION_CHANGE' name='PAYMENT_CONDITION_CHANGE'>
						<option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($payment_list as $condicao): ?>
						<option value='<?=$condicao->IDCONDICAOPAGAMENTO;?>'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'PAYMENT_CONDITION_CHANGE']))? (($tblOpcao->get("PAYMENT_CONDITION_CHANGE")->OPCAO_VALOR==$condicao->IDCONDICAOPAGAMENTO)? " selected":"") :"" );?>><?=$condicao->NOME;?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</fieldset>
		<fieldset><legend>Vendas</legend>
			<div class="form-row">
			<div class="form-group col-6">
				<label for='REQUIRED_CUSTOMER'>Exige cadastro de cliente na compra</label>
		        <select class='form-control form-control-sm' id='REQUIRED_CUSTOMER' name='REQUIRED_CUSTOMER'>
					<option value='0'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'REQUIRED_CUSTOMER']))? (($tblOpcao->get("REQUIRED_CUSTOMER")->OPCAO_VALOR=="0")? " selected":"") :"" );?>>N&atilde;o</option>
					<option value='1'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'REQUIRED_CUSTOMER']))? (($tblOpcao->get("REQUIRED_CUSTOMER")->OPCAO_VALOR=="1")? " selected":"") : "");?>>Sim</option>
				</select>
			</div>
			<div class="form-group col-6">
				<label for="COMPANY_SELLER">Cargo de Vendedor</label>
				<select class="form-control form-control-sm" id="COMPANY_SELLER" name="COMPANY_SELLER">
					<option value="">&laquo; Selecione &raquo;</option>
					<?php foreach($job_title_list as $job_title):?>
					<option value="<?=$job_title->IDCARGO;?>"<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'COMPANY_SELLER']))? (($tblOpcao->get("COMPANY_SELLER")->OPCAO_VALOR==$job_title->IDCARGO)? " selected":"") :"" );?>><?=$job_title->NOME;?></option>
					<?PHP endforeach; ?>
				</select>
			</div>
		</div>
		</fieldset>
		<fieldset><legend>Meio de Pagamento</legend>
			<div class="form-row">
				<div class="form-group col-4">
					<label for='PAYMENT_METHOD_MONEY'>Dinheiro</label>
					<select class='form-control form-control-sm' id='PAYMENT_METHOD_MONEY' name='PAYMENT_METHOD_MONEY'>
						<option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($paymethod_list as $modo): ?>
						<option value='<?=$modo->IDMEIOPAGAMENTO;?>'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'PAYMENT_METHOD_MONEY']))? (($tblOpcao->get("PAYMENT_METHOD_MONEY")->OPCAO_VALOR==$modo->IDMEIOPAGAMENTO)? " selected":"") :"" );?>><?=$modo->NOME;?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-4">
					<label for='PAYMENT_METHOD_GIFT'>Vale Presente</label>
					<select class='form-control form-control-sm' id='PAYMENT_METHOD_GIFT' name='PAYMENT_METHOD_GIFT'>
						<option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($paymethod_list as $modo): ?>
						<option value='<?=$modo->IDMEIOPAGAMENTO;?>'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'PAYMENT_METHOD_GIFT']))? (($tblOpcao->get("PAYMENT_METHOD_GIFT")->OPCAO_VALOR==$modo->IDMEIOPAGAMENTO)? " selected":"") :"" );?>><?=$modo->NOME;?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-4">
					<label for='PAYMENT_METHOD_CHANGE'>Vale Troca</label>
					<select class='form-control form-control-sm' id='PAYMENT_METHOD_CHANGE' name='PAYMENT_METHOD_CHANGE'>
						<option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($paymethod_list as $modo): ?>
						<option value='<?=$modo->IDMEIOPAGAMENTO;?>'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'PAYMENT_METHOD_CHANGE']))? (($tblOpcao->get("PAYMENT_METHOD_CHANGE")->OPCAO_VALOR==$modo->IDMEIOPAGAMENTO)? " selected":"") :"" );?>><?=$modo->NOME;?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</fieldset>
	</div>

	<!-- Suprimentos -->
	<div class="tab-pane fade" id="nav-supplies" role="tabpanel" aria-labelledby="nav-supplies-tab">
		<br/><div class="form-row">
			<div class="form-group col-6">
				<label for='DEFAULT_PRODUCT_TYPE'>Tipo de Produto padr&atilde;o</label>
				<select class="form-control form-control-sm" id="DEFAULT_PRODUCT_TYPE" name="DEFAULT_PRODUCT_TYPE">
					<option value=''>&laquo; Selecione &raquo;</option>
					<?php foreach($product_type_list as $product_type): ?>
					<option value='<?=$product_type->IDPRODUTOTIPO;?>'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'DEFAULT_PRODUCT_TYPE']))? (($tblOpcao->get("DEFAULT_PRODUCT_TYPE")->OPCAO_VALOR==$product_type->IDPRODUTOTIPO)? " selected":"") :"" );?>><?=$product_type->DESCRICAO;?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group col-6">
				<label for="BARCODE_SIZE">Tamanho do c&oacute;digo de Barras</label>
				<input type='number' class='form-control form-control-sm' id='BARCODE_SIZE' name='BARCODE_SIZE' value='<?=($tblOpcao->exists(['OPCAO_NOME' => 'BARCODE_SIZE']))?$tblOpcao->get("BARCODE_SIZE")->OPCAO_VALOR:"";?>'/>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm">
				<label for="TRANSFER_EXPIRATION_DATE">Prazo de validade da(s) transfer&ecirc;ncia (Dias)</label>
				<input type="number" class="form-control form-control-sm" id="TRANSFER_EXPIRATION_DATE" name="TRANSFER_EXPIRATION_DATE" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'TRANSFER_EXPIRATION_DATE']))?$tblOpcao->get("TRANSFER_EXPIRATION_DATE")->OPCAO_VALOR:"2";?>"/>
			</div>
		</div>
	</div>


	<!-- Tributario -->
	<div class="tab-pane fade" id="nav-tributary" role="tabpanel" aria-labelledby="nav-tributary-tab">
		<br/>
		<div class="form-row">
			<div class="form-group col-4">
				<label for='NFE_VERSION'>Vers&atilde;o da NF-e/NFC-e</label>
				<input type="text" id="NFE_VERSION" name="NFE_VERSION" class="form-control form-control-sm" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'NFE_VERSION']))?$tblOpcao->get("NFE_VERSION")->OPCAO_VALOR:'';?>" automcomplete="off" placeholder="4.0"/>
			</div>
			<div class="form-group col-4">
				<label for="NFCE_SERIE">S&eacute;rie da NFC-e</label>
				<input type="text" id="NFCE_SERIE" name="NFCE_SERIE" class="form-control form-control-sm" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'NFCE_SERIE']))?$tblOpcao->get("NFCE_SERIE")->OPCAO_VALOR:'';?>" automcomplete="off" placeholder="1"/>
			</div>
			<div class="form-group col-4">
				<label for="NFE_SERIE">S&eacute;rie da NF-e</label>
				<input type="text" id="NFE_SERIE" name="NFE_SERIE" class="form-control form-control-sm" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'NFE_SERIE']))?$tblOpcao->get("NFE_SERIE")->OPCAO_VALOR:'';?>" automcomplete="off" placeholder="1"/>
			</div>
		</div>
	</div>

	<!-- Comunicacao -->
	<div class="tab-pane fade" id="nav-comunicate" role="tabpanel" aria-labelledby="nav-comunicate-tab">
		<br/>
		<fieldset><legend>E-mail Padr&atilde;o</legend>
			<div class="form-row">
				<div class="form-group col-4">
					<label for="SYSTEM_DEFAULT_MAIL_SERVER">Servidor</label>
					<input type="text" name="SYSTEM_DEFAULT_MAIL_SERVER" id="SYSTEM_DEFAULT_MAIL_SERVER" class="form-control text-lowercase form-control-sm" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'SYSTEM_DEFAULT_MAIL_SERVER']))?$tblOpcao->get("SYSTEM_DEFAULT_MAIL_SERVER")->OPCAO_VALOR:'';?>" autocomplete="off"/>
                </div>
                <div class="form-group col-2">
                    <label for="SYSTEM_DEFAULT_MAIL_PORT">Porta</label>
                    <input type="number" name="SYSTEM_DEFAULT_MAIL_PORT" id="SYSTEM_DEFAULT_MAIL_PORT" class="form-control form-control-sm" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'SYSTEM_DEFAULT_MAIL_PORT']))?$tblOpcao->get("SYSTEM_DEFAULT_MAIL_PORT")->OPCAO_VALOR:'';?>" autocomplete="off"/>
                </div>
				<div class="form-group col-3">
					<label for="SYSTEM_DEFAULT_MAIL_USER">Login</label>
					<input type="text" name="SYSTEM_DEFAULT_MAIL_USER" id="SYSTEM_DEFAULT_MAIL_USER" class="form-control form-control-sm" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'SYSTEM_DEFAULT_MAIL_USER']))?$tblOpcao->get("SYSTEM_DEFAULT_MAIL_USER")->OPCAO_VALOR:'';?>" autocomplete="off"/>
				</div>
				<div class="form-group col-3">
					<label for="SYSTEM_DEFAULT_MAIL_PASS">Senha</label>
					<input type="password" name="SYSTEM_DEFAULT_MAIL_PASS" id="SYSTEM_DEFAULT_MAIL_PASS" class="form-control form-control-sm" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'SYSTEM_DEFAULT_MAIL_PASS']))?$tblOpcao->get("SYSTEM_DEFAULT_MAIL_PASS")->OPCAO_VALOR:'';?>" autocomplete="off"/>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-6">
					<label for='DEFAULT_SYSTEM_MAIL'>E-mail padr&atilde;o do sistema</label>
					<input type='email' class='form-control text-lowercase form-control-sm' id='DEFAULT_SYSTEM_MAIL' name='DEFAULT_SYSTEM_MAIL' value='<?=($tblOpcao->exists(['OPCAO_NOME' => 'DEFAULT_SYSTEM_MAIL']))?$tblOpcao->get("DEFAULT_SYSTEM_MAIL")->OPCAO_VALOR:"";?>' autocomplete='off'>
				</div>
				<div class="form-group col-6">
					<label for='NAME_DEFAULT_MAIL'>Identifica&ccedil;&atilde;o do e-mail padr&atilde;o</label>
					<input type='text' class='form-control form-control-sm' id='NAME_DEFAULT_MAIL' name='NAME_DEFAULT_MAIL' value='<?=($tblOpcao->exists(['OPCAO_NOME' => 'NAME_DEFAULT_MAIL']))?$tblOpcao->get("NAME_DEFAULT_MAIL")->OPCAO_VALOR:"";?>' autocomplete='off'>
				</div>
			</div>
		</fieldset>
		<fieldset><legend>Modelos de e-mails</legend>
			<div class="form-row">
				<div class="form-group col-9">
					<label for='TEMPLATE_BIRTHDAY'>Modelo de E-mail para Aniversariante</label>
					<select class="form-control form-control-sm" id="TEMPLATE_BIRTHDAY" name="TEMPLATE_BIRTHDAY">
						<option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($template_list as $template):?>
						<option value="<?=$template->IDTEMPLATEEMAIL;?>"<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'TEMPLATE_BIRTHDAY']))? (($tblOpcao->get("TEMPLATE_BIRTHDAY")->OPCAO_VALOR==$template->IDTEMPLATEEMAIL)? " selected":"") :"" );?>><?=$template->NOME;?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-3">
					<label for="BIRTHDAY_PERCENT">% Desconto para o Aniversariante</label>
					<input type="number" id="BIRTHDAY_PERCENT" name="BIRTHDAY_PERCENT" class="form-control form-control-sm" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'BIRTHDAY_PERCENT']))?$tblOpcao->get("BIRTHDAY_PERCENT")->OPCAO_VALOR:"";?>"/>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-8">
					<label for='TEMPLATE_BIRTHDAY_STORE'>Modelo de E-mail de Anivers&aacute;rio da Empresa</label>
					<select class="form-control form-control-sm" id="TEMPLATE_BIRTHDAY_STORE" name="TEMPLATE_BIRTHDAY_STORE">
						<option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($template_list as $template):?>
						<option value="<?=$template->IDTEMPLATEEMAIL;?>"<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'TEMPLATE_BIRTHDAY_STORE']))? (($tblOpcao->get("TEMPLATE_BIRTHDAY_STORE")->OPCAO_VALOR==$template->IDTEMPLATEEMAIL)? " selected":"") :"" );?>><?=$template->NOME;?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-2">
					<label for="BIRTHDAY_STORE_PERCENT">% de Desconto</label>
					<input type="number" id="BIRTHDAY_STORE_PERCENT" name="BIRTHDAY_STORE_PERCENT" class="form-control form-control-sm" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'BIRTHDAY_STORE_PERCENT']))?$tblOpcao->get("BIRTHDAY_STORE_PERCENT")->OPCAO_VALOR:"";?>"/>
				</div>
				<div class="form-group col-2">
					<label for="BIRTHDAY_STORE_MONTH">M&ecirc;s do Anivers&aacute;rio</label>
					<select class="form-control form-control-sm" id="BIRTHDAY_STORE_MONTH" name="BIRTHDAY_STORE_MONTH">
						<option value="">&laquo; Selecione &raquo;</option>
						<?php for($i=1;$i<13;$i++): ?>
						<option value="<?=(($i<10)?"0$i":$i);?>"<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'BIRTHDAY_STORE_MONTH']))? (($tblOpcao->get("BIRTHDAY_STORE_MONTH")->OPCAO_VALOR==(($i<10)?"0$i":$i) )? " selected":"") :"" );?>><?=(($i<10)?"0$i":$i)?></option>
						<?php endfor;?>
					</select>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-6">
					<label for='TEMPLATE_REQUEST'>Modelo de E-mail utilizado nas solicita&ccedil;&otilde;es dos clientes</label>
					<select id='TEMPLATE_REQUEST' name='TEMPLATE_REQUEST' class='form-control form-control-sm'>
						<option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($template_list as $template): ?>
						<option value='<?=$template->IDTEMPLATEEMAIL;?>'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'TEMPLATE_REQUEST']))? (($tblOpcao->get("TEMPLATE_REQUEST")->OPCAO_VALOR==$template->IDTEMPLATEEMAIL)? " selected":"") :"" );?>><?=$template->NOME;?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-6">
					<label for='TEMPLATE_RATING'>Modelo de E-mail utilizado nas avalia&ccedil;&otilde;es de venda e produtos</label>
					<select id='TEMPLATE_RATING' name='TEMPLATE_RATING' class='form-control form-control-sm'>
						<option value=''>&laquo; Selecione &raquo;</option>
						<?php foreach($template_list as $template): ?>
						<option value='<?=$template->IDTEMPLATEEMAIL;?>'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'TEMPLATE_RATING']))? (($tblOpcao->get("TEMPLATE_RATING")->OPCAO_VALOR==$template->IDTEMPLATEEMAIL)? " selected":"") :"" );?>><?=$template->NOME;?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</fieldset>
	</div>

	<!-- Sistema -->
	<div class="tab-pane fade" id="nav-system" role="tabpanel" aria-labelledby="nav-system-tab">
		<br/>
		<div class="form-group">
			<label for="COMPANY_NAME">Nome da Empresa</label>
			<input type="text" id="COMPANY_NAME" name="COMPANY_NAME" class="form-control form-control-sm" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'COMPANY_NAME']))?$tblOpcao->get("COMPANY_NAME")->OPCAO_VALOR:"";?>">
		</div>
		<div class="form-group">
			<label for='DEFAULT_STORE'>Loja Padr&atilde;o do Sistema</label>
			<select id='DEFAULT_STORE' name='DEFAULT_STORE' class='form-control form-control-sm'>
				<option value=''>&laquo; Selecione &raquo;</option>
				<?php foreach($store_list as $loja): ?>
				<option value='<?=$loja->IDLOJA;?>'<?=( ($tblOpcao->exists(['OPCAO_NOME' => 'DEFAULT_STORE']))? (($tblOpcao->get("DEFAULT_STORE")->OPCAO_VALOR==$loja->IDLOJA)? " selected":"") :"" );?>><?=$loja->NOME;?></option>
				<?php endforeach; ?>
			</select>
		</div>
				<div class="row">
			<div class="form-group col">
				<label for="CRON_CASHFLOW">Hor&aacute;rio de atualiza&ccedil;&atilde;o do Fluxo de Caixa</label>
				<input type="time" class="form-control form-control-sm" name="CRON_CASHFLOW" id="CRON_CASHFLOW" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'CRON_CASHFLOW']))?$tblOpcao->get("CRON_CASHFLOW")->OPCAO_VALOR:"";?>"/>
			</div>
			<div class="form-group col">
				<label for="CRON_CUSTOMER_BIRTHDAY">Hor&aacute;rio para envio do cart&atilde;o de aniversariante</label>
				<input type="time" class="form-control form-control-sm" name="CRON_CUSTOMER_BIRTHDAY" id="CRON_CUSTOMER_BIRTHDAY" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'CRON_CUSTOMER_BIRTHDAY']))?$tblOpcao->get("CRON_CUSTOMER_BIRTHDAY")->OPCAO_VALOR:"";?>"/>
			</div>
		</div>
		<div class="row">
			<div class="form-group col">
				<label for="CRON_COMPANY_BIRTHDAY">Hor&aacute;rio de envio do cart&atilde;o de aniver&aacute;rio da empresa</label>
				<input type="time" class="form-control form-control-sm" name="CRON_COMPANY_BIRTHDAY" id="CRON_COMPANY_BIRTHDAY" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'CRON_COMPANY_BIRTHDAY']))?$tblOpcao->get("CRON_COMPANY_BIRTHDAY")->OPCAO_VALOR:"";?>"/>
			</div>
			<div class="form-group col">
				<label for="CRON_PROCESS_TRANSFER">Hor&aacute;rio de processamento das transfer&ecirc;ncias (validade)</label>
				<input type="time" class="form-control form-control-sm" name="CRON_PROCESS_TRANSFER" id="CRON_PROCESS_TRANSFER" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'CRON_PROCESS_TRANSFER']))?$tblOpcao->get("CRON_PROCESS_TRANSFER")->OPCAO_VALOR:"";?>"/>
			</div>
		</div>
	</div>

	<!-- Integracoes -->
	<div class="tab-pane fade" id="nav-integrations" role="tabpanel" aria-labelledby="nav-integrations-tab">
		<br/>
		<div class='form-group'>
            <label for='TAXES_APIKEY'>IBPT ApiKey</label>
			<div class="input-group mb-3 input-group-sm">
				<input type='text' class='form-control' id='TAXES_APIKEY' name='TAXES_APIKEY' value='<?=($tblOpcao->exists(['OPCAO_NOME' => 'URL_TAXES']))?$tblOpcao->get("URL_TAXES")->OPCAO_VALOR:"";?>'/>
				<div class="input-group-append">
					<button class="btn btn-outline-secondary" type="button" id="button-addon2" onclick="javascript:help('WS_TAXES')">?</button>
				</div>
			</div>
        </div>
		<div class='form-group'>
            <label for='URL_ZIPCODE'>WebService CEP</label>
			<div class="input-group mb-3 input-group-sm">
				<input type='text' class='form-control' id='URL_ZIPCODE' name='URL_ZIPCODE' placeholder='http://viacep.com.br/ws/{{ZIP_CODE}}/json/' value='<?=($tblOpcao->exists(['OPCAO_NOME' => 'URL_ZIPCODE']))?$tblOpcao->get("URL_ZIPCODE")->OPCAO_VALOR:"";?>'/>
				<div class="input-group-append">
					<button class="btn btn-outline-secondary" type="button" id="button-addon3" onclick="javascript:help('WS_POSTAL')">?</button>
				</div>
			</div>
        </div>
		<fieldset><legend>MailChimp <a href="javascript:help('MAILCHIMP');">?</a></legend>
			<div class="form-group">
				<label for="MAILCHIMP_API">Api - Add or update a list member</label>
				<input type="text" class="form-control form-control-sm" id="MAILCHIMP_API" name="MAILCHIMP_API" placeholder='https://usX.api.mailchimp.com/3.0/lists/{list_id}/members/{subscriber_hash}' value='<?=($tblOpcao->exists(['OPCAO_NOME' => 'MAILCHIMP_API']))?$tblOpcao->get("MAILCHIMP_API")->OPCAO_VALOR:"";?>'/>
			</div>
			<div class="form-group">
				<label for="MAILCHIMP_APIKEY">ApiKey</label>
				<input type="text" class="form-control form-control-sm" id="MAILCHIMP_APIKEY" name="MAILCHIMP_APIKEY" placeholder="" value="<?=($tblOpcao->exists(['OPCAO_NOME' => 'MAILCHIMP_APIKEY']))?$tblOpcao->get("MAILCHIMP_APIKEY")->OPCAO_VALOR:"";?>"/>
			</div>
		</fieldset>
	</div>
</div>
