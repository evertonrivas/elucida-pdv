<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\View\Helper;

use Cake\View\Helper;

/**
 * Description of DialogHelper
 *
 * @author hestilo
 */
class DialogHelper extends Helper{
    public function city(){
        $html = '<!--INICIO DO MODAL DE BUSCA DE CIDADES-->
        <div class="modal fade" id="modalSearchCity" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">'.__("City Search").'</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <form id="frmFindCity">
                    <div class="form-group">
                    	<div class="table-responsive">
	                        <table class="table">
	                            <thead>
	                                <tr>
	                                    <th>'.__("IBGE Cod").'</th>
	                                    <th>'.__("Name").'</th>
	                                    <th>'.__("State").'</th>
	                                    <th>'.__("Action").'</th>
	                                </tr>
	                                <tr>
	                                    <th><input type="text" class="form-control-sm form-control text-uppercase" id="txtCityCodIBGE" autocomplete="off"></th>
	                                    <th><input type="text" class="form-control-sm form-control text-uppercase" id="txtCityNome" autocomplete="off"></th>
	                                    <th><input type="text" class="form-control-sm form-control text-uppercase" id="txtCityProvince" autocomplete="off"></th>
	                                    <th><button type="submit" class="btn btn-primary btn-sm" id="btnFind"><i class="fas fa-search"></i> '.__("Filter").'</button></th>
	                                </tr>
	                            </thead>
	                        </table>
	                        <div style="max-height: 250px!important;height:250px!important;overflow-y: scroll!important;overflow-x:hidden!important;display:block!important;">
	                        <table class="table table-striped" id="tblResultCity">
	                            <tbody>

	                            </tbody>
	                        </table>
	                        </div>
                        </div>
                    </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" id="btnUseCity"><i class="fas fa-check"></i> '.__("Use Selected").'</button>
              </div>
            </div>
          </div>
        </div>';
        
        echo $html;
    }
    
    public function bank(){
        $html = '<!--INICIO DO MODAL DE BUSCA DE BANCOS-->
<div class="modal fade" id="modalSearchBank" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
		<h5 class="modal-title" id="myModalLabel">'.__("Bank Search").'</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <form id="frmFindBank">
            <div class="form-group">
                <table class="table">
                    <thead>
                        <tr>
                            <th>C&oacute;d. Febraban</th>
                            <th>Nome</th>
                            <th>A&ccedil;&atilde;o</th>
                        </tr>
                        <tr>
                            <th><input type="text" class="form-control-sm form-control text-uppercase" id="txtBankCodFebraban" autocomplete="off"></th>
                            <th><input type="text" class="form-control-sm form-control text-uppercase" id="txtBankNome" autocomplete="off"></th>
                            <th><button type="submit" class="btn btn-primary btn-sm" id="btnFind"><i class="fas fa-search"></i> '.__("Filter").'</button></th>
                        </tr>
                    </thead>
                </table>
                <div style="max-height: 250px!important;height:250px!important;overflow-y: scroll!important;overflow-x:hidden!important;display:block!important;">
                <table class="table table-striped" id="tblResultBank">
                	<tbody>

                    </tbody>
                </table>
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success btn-sm" id="btnUseBank"><i class="fas fa-check"></i> '.__("Use Selected").'</button>
      </div>
    </div>
  </div>
</div>';
        
        echo $html;
    }
    
    public function product_multiple($_idLoja=""){
        $html = '<!-- DIALOG DE BUSCA DE PRODUTOS -->
            <div class="modal fade" id="modalProductMultiple" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
            <form id="frmProductDialogMultiple" class="form-inline">
            <input type="hidden" id="TXT_PRODUCTM_DIALOG_STORE" name="TXT_PRODUCTM_DIALOG_STORE" value="'.$_idLoja.'"/>
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    	<h4 class="modal-title" id="modalLabel">Sele&ccedil;&atilde;o de produtos</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body" id="productDialogResult">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Produto</th>
                                    <th>A&ccedil;&atilde;o</th>
                                </tr>
                                <tr>
                                    <th><input type="text" class="form-control text-uppercase form-control-sm" id="TXT_PRODUCTM_DIALOG_SEARCH_SKU" name="TXT_PRODUCTM_DIALOG_SEARCH_SKU" autocomplete="off"></th>
                                    <th><input type="text" class="form-control text-uppercase form-control-sm" id="TXT_PRODUCTM_DIALOG_SEARCH_NAME" name="TXT_PRODUCTM_DIALOG_SEARCH_NAME" autocomplete="off"></th>
                                    <th><button type="submit" class="btn btn-primary btn-sm" id="btnFilter"><i class="fas fa-search"></i> '.__("Filter").'</button></th>
                                </tr>
                            </thead>
                        </table>
                        <div style="max-height: 250px!important;height:250px!important;overflow-y: scroll!important;overflow-x:hidden!important;display:block!important;">
                        <table class="table table-striped" id="tableSearchProductMultiple">
                            <tbody>

                            </tbody>
                        </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> '.__("Close").'</button>
                    </div>
                </div>
            </div>
            </form>
        </div>';
        echo $html;
    }
    
    public function product_single($_idLoja=""){
        $html = '<!-- DIALOG DE BUSCA DE PRODUTOS -->
            <div class="modal fade" id="modalProductSingle" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
            <form id="frmProductDialogSingle" class="form-inline">
            <input type="hidden" id="TXT_PRODUCTS_DIALOG_STORE" name="TXT_PRODUCTS_DIALOG_STORE" value="'.$_idLoja.'"/>
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    	<h4 class="modal-title" id="modalLabel">Sele&ccedil;&atilde;o de produtos</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body" id="productDialogResult">
	                        <table class="table">
	                            <thead>
	                                <tr>
	                                    <th>SKU</th>
	                                    <th>Produto</th>
	                                    <th>Pre&ccedil;o de Venda</th>                                    
	                                    <th>A&ccedil;&atilde;o</th>
	                                </tr>
	                                <tr>
	                                    <th><input type="text" class="form-control text-uppercase form-control-sm" id="TXT_PRODUCTS_DIALOG_SEARCH_SKU" name="TXT_PRODUCTS_DIALOG_SEARCH_SKU" autocomplete="off"></th>
	                                    <th><input type="text" class="form-control text-uppercase form-control-sm" id="TXT_PRODUCTS_DIALOG_SEARCH_NAME" name="TXT_PRODUCTS_DIALOG_SEARCH_NAME" autocomplete="off"></th>
	                                    <th>&nbsp;</th>
	                                    <th><button type="submit" class="btn btn-primary btn-sm" id="btnFilterPrd"><i class="fas fa-search"></i> '.__("Filter").'</button></th>
	                                </tr>
	                            </thead>
	                    	</table>
	                    	<div style="max-height: 250px!important;height:250px!important;overflow-y: scroll!important;overflow-x:hidden!important;display:block!important;">
	                    	<table class="table table-striped" id="tableSearchProductSingle">
	                            <tbody>

	                            </tbody>
	                        </table>
	                        </div>
                    </div>
                    <div class="modal-footer">
                    	<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i> '.__("Close").'</button>
                        <button type="button" class="btn btn-success btn-sm" data-dismiss="modal" id="btnUseSingleProduct"><i class="fas fa-check"></i> '.__("Use Selected").'</button>
                    </div>
                </div>
            </div>
            </form>
        </div>';
        echo $html;
    }
    
    public function pdf(){
        $html = '<!--MODAL DE EXIBICAO DA ARQUIVO PDF-->
            <div class="modal fade" id="modalPdf" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel">Exibi&ccedil;&atilde;o de PDF</h4>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="txtFileToOpen" id="txtFileToOpen"/>
                    <iframe id="frmPdf" name="frmPdf" frameborder="0" style="min-height:500px; max-height:550px; overflow-y:scroll;width:100%"></iframe>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" id="btnClosePdfModal"><i class="fas fa-window-close"></i> '.__("Close").'</button>
                  </div>
                </div>
              </div>
            </div>';
        echo $html;
    }

    public function customer_find($_enabled_new=true){
        $html = '<!-- MODAL DE BUSCA DE CLIENTE -->
            <div class="modal fade" id="modalFindCliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <form id="frmFindCliente" class="form">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        	<h4 class="modal-title" id="myModalLabel">Busca de Cliente</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>CPF</th>
                                        <th>Nome</th>
                                        <th>Telefone</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                    <tr>
                                        <td class="first-column">&nbsp;</td>
                                        <td><input type="text" class="form-control text-uppercase form-control-sm" id="TXT_CUSTOMER_SEARCH_TAXVAT" name="TXT_CUSTOMER_SEARCH_TAXVAT" autocomplete="off"></td>
                                        <td><input type="text" class="form-control text-uppercase form-control-sm" id="TXT_CUSTOMER_SEARCH_NAME" name="TXT_CUSTOMER_SEARCH_NAME" autocomplete="off"></td>
                                        <td><input type="number" class="form-control form-control-sm" id="TXT_CUSTOMER_SEARCH_PHONE" name="TXT_CUSTOMER_SEARCH_PHONE"></td>
                                        <td><button type="submit" class="btn btn-primary btn-sm" id="btnFilterCli" name="btnFilterCli"><i class="fas fa-search"></i> '.__("Filter").'</button> </td>
                                    </tr>
                                </thead>
                            </table>
                            <div style="max-height: 250px!important;height:250px!important;overflow-y: scroll!important;overflow-x:hidden!important;">
                            <table class="table table-striped" id="tblFindCustomer">
                                <tbody>

                                </tbody>
                            </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-secondary btn-sm'.(($_enabled_new)?'':' disabled').'" href="javascript:modalNewCustomer()"'.(($_enabled_new)?'':' disabled=""').'><i class="fas fa-plus-circle"></i> '.__("New").' '.__("Customer").'</a>
                            <button type="button" class="btn btn-success btn-sm" id="btnUseCustomer"><i class="fas fa-check"></i> '.__("Use Selected").'</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>';
        echo $html;
    }
    
    public function customer_new(){
        $html = '<!-- MODAL DE CADASTRO DO CLIENTE -->
            <div class="modal fade" id="modalNewCustomer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <form id="frmCadCliente" class="needs-validation" novalidate>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    	<h4 class="modal-title" id="myModalLabel">Cadastro de Cliente</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                          <div class="form-group">
                              <label>Nome:</label>
                              <input type="text" id="TXT_CUSTOMER_NEW_NAME" name="TXT_CUSTOMER_NEW_NAME" class="form-control text-uppercase form-control-sm" autocomplete="off" required/>
                              <input type="hidden" id="TXT_CUSTOMER_NEW_DATEADD" name="TXT_CUSTOMER_NEW_DATEADD" value="'.date("Y-m-d").'"/>
                              <input type="hidden" id="TXT_CUSTOMER_NEW_ID" name="TXT_CUSTOMER_NEW_ID" value=""/>
                          </div>
                          <div class="form-group">
                              <label>E-mail:</label>
                              <input type="email" id="TXT_CUSTOMER_NEW_EMAIL" name="TXT_CUSTOMER_NEW_EMAIL" class="form-control text-lowercase form-control-sm" autocomplete="off" required/>
                          </div>
                          <div class="row">
	                          <div class="form-group col-sm">
	                              <label>Nascimento:</label>
	                              <div class="input-group input-group-sm">
	                                <input type="text" id="TXT_CUSTOMER_NEW_BIRTHDAY" name="TXT_CUSTOMER_NEW_BIRTHDAY" class="form-control form-control-sm date" data-provide="datepicker" data-date-format="dd/mm/yyyy" data-date-language="pt-BR" data-date-autoclose="true" data-date-today-highlight="true" required>
	                                <div class="input-group-append">
	                                	<span class="input-group-text">
	                                		<i class="fas fa-calendar-alt"></i>
	                                	</span>
                                	</div>
	                            </div>
	                          </div>
	                          <div class="form-group col-sm">
	                              <label>CPF:</label>
	                              <input type="text" id="TXT_CUSTOMER_NEW_TAXVAT" name="TXT_CUSTOMER_NEW_TAXVAT" class="form-control" placeholder="___.___.___-__" required/>
	                          </div>
                          </div>
                          <div class="row">
	                          <div class="col-sm form-group">
	                              <label>CEP:</label>
	                              <input type="text" id="TXT_CUSTOMER_NEW_ZIPCODE" name="TXT_CUSTOMER_NEW_ZIPCODE" class="form-control" placeholder="_____-___" required/>
	                          </div>
	                          <div class="col-sm form-group">
	                              <label>G&ecirc;nero:</label>
	                              <select id="TXT_CUSTOMER_NEW_GENDER" name="TXT_CUSTOMER_NEW_GENDER" class="form-control" required>
	                                  <option value=""></option>
	                                  <option value="1">Masculino</option>
	                                  <option value="2">Feminino</option>
	                                  <option value="3">Transexual</option>
	                              </select>
	                          </div>
                          </div>
                          <div class="row">
	                          <div class="form-group col-sm">
	                              <label>Telefone</label>
	                              <input type="tel" id="TXT_CUSTOMER_NEW_PHONE" name="TXT_CUSTOMER_NEW_PHONE" class="form-control form-control-sm" pattern="\([0-9]{2}\)[\s][0-9]{4,5}-[0-9]{4,5}" required/>
	                          </div>
	                          <div class="form-group col-sm">
	                              <label>Telefone 2</label>
	                              <input type="tel" id="TXT_CUSTOMER_NEW_PHONE2" name="TXT_CUSTOMER_NEW_PHONE2" class="form-control form-control-sm" pattern="\([0-9]{2}\)[\s][0-9]{4,5}-[0-9]{4,5}"/>
	                          </div>
                          </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-download"></i> '.__("Save and Close").'</button>
                    </div>
                </div>
            </div>
            </form>
        </div>';
        echo $html;
    }
    
    public function employer_find(){
        echo '<div class="modal fade" id="modalFindEmployer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <form id="frmFindEmployer" class="form">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h4 class="modal-title" id="myModalLabel">Busca de Funcion&aacute;rio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>CPF</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input type="text" class="form-control text-uppercase form-control-sm" id="TXT_EMPLOYER_CPF" name="TXT_EMPLOYER_CPF" autocomplete="off"></td>
                            <td><input type="text" class="form-control text-uppercase form-control-sm" id="TXT_EMPLOYER_NAME" name="TXT_EMPLOYER_NAME" autocomplete="off"></td>
                            <td><input type="number" class="form-control form-control-sm" id="TXT_EMPLOYER_FONE" name="TXT_EMPLOYER_FONE"></td>
                            <td><button type="submit" class="btn btn-primary btn-sm" id="btnFilterEmpl" name="btnFilterEmpl"><i class="fas fa-search"></i> '.__("Filter").'</button> </td>
                        </tr>
                    </thead>
                </table>
                <div style="max-height: 250px!important;height:250px!important;overflow-y: scroll!important;overflow-x:hidden!important;">
                <table class="table table-striped" id="tblFindEmployer">
                    <tbody>
                        
                    </tbody>
                </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" id="btnUseEmployer"><i class="fas fa-check"></i> '.__("Use Selected").'</button>
            </div>
        </div>
    </div>
    </form>
</div>';
    }
}
