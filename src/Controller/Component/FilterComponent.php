<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * Description of FilterHelper
 *
 * @author hestilo
 */
class FilterComponent extends Component{
    private $filters;
	private $orders;
    
    /**
     * Define quais serao os filtros utilizados para a busca na tabela
     * @param string $_filterText Texto do label do filtro
     * @param string $_filterName Nome do filtro
     * @param string $_filterType Tipo do filtro (text, date, combo, check, mcombo, number, bet_date)
     * @param mixed $_filterData Dados do filtro sendo: array (composto por stdClass com key e value) ou plain data
     */
    public function addFilter($_filterText,$_filterName,$_filterType,$_filterData = null){
        $filter = new \stdClass();
        $filter->text = $_filterText;
        $filter->name = $_filterName;
        $filter->type = $_filterType;
        $filter->data = $_filterData;
        
        $this->filters[] = $filter;
    }
	
	/**
	 * Define campo para ordenacao utilizados junto com os filtros na tabela
	 * @param array  $_orderData Dados da ordenacao sendo um array composto por key e value
	 */
	public function addOrder($_orderData){
		$order = new \stdClass();
		$order->text = "Ordenar por:";
		$order->name = "CB_ORDER_FIELD";
		$order->data = $_orderData;
		
		$this->orders[] = $order;
	}
	
    /**
	* Metodo que adiciona filtros do tipo link que ficarao como button-group
	* @param array $_filterTextData Array contendo os dados de Texto e Dados
	*/
    public function addLinkFilter($_filterTextData){
		$filter = new \stdClass();
		$filter->type = 'link';
		$filter->data = $_filterTextData;
		$this->filters[] = $filter;
	}
    
    public function mountFilters(){
        $html = "";
		if(is_array($this->orders) && count($this->orders)>0){
			$html .= "<div class='row'><div class='col-md-6'>";
		}
		
		//Montagem dos campos de filtros
        foreach($this->filters as $filter){
            switch($filter->type){
                case 'text': $html.= "<div class='form-group'>";
                    $html.= "<label for='".$filter->name."'>".$filter->text."</label>";
                    $html.= "<input type='text' id='".$filter->name."' name='".$filter->name."' class='form-control form-control-sm text-uppercase' autocomplete='off'/>";
                    $html.= "</div>"; 
                    break;
                case 'link' : $html.= "<div class='btn-group text-center' data-toggle='buttons'>";
                	foreach($filter->data as $link){
                		$html.= "<label class='btn btn-default btn-sm' onclick='setLink(\"".$link->data."\");'>";
						$html.= "<input type='radio' name='options' id='option_".$link->data."'>".$link->text;
						$html.= "</label>";
					}                	
                	$html.= "</div>"; 
                	break;
                case 'check': 
					$html.= "<div class='custom-control custom-switch'>";
					$html.= "<input type='checkbox' class='custom-control-input' id='".$filter->name."' mame='".$filter->name."' value='".$filter->data."'>";
					$html.= "<label class='custom-control-label' for='".$filter->name."'>".$filter->text."</label>";
					$html.= "</div>";
                    break;
                case 'number': $html.= "<div class='form-group'>";
                    $html.= "<label class='control-label' for='".$filter->name."'>".$filter->text."</label>";
                    $html.= "<input type='number' id='".$filter->name."' name='".$filter->name."' class='form-control form-control-sm text-uppercase' autocomplete='off'/>";
                    $html.= "</div>"; 
                    break;
                case 'date':
					$html.= "<label for='".$filter->name."'>".$filter->text."</label>";
                    $html.= "<div class='input-group input-group-sm mb-3'>";
						$html.= "<input type='text' class='form-control date' id='".$filter->name."' name='".$filter->name."' autocomplete='off' data-provide='datepicker' data-date-format='dd/mm/yyyy' data-date-language='pt-BR' data-date-autoclose='true' data-date-today-highlight='true'/>";
						$html.= "<div class='input-group-append'>";
							$html.= "<span class='input-group-text'><i class='far fa-calendar-alt'></i></span>";
						$html.= "</div>";
                    $html.= "</div>";
                    break;
                case 'combo': 
                    $html.= "<div class='form-group'>";
                    $html.= "<label class='control-label' for='".$filter->name."'>".$filter->text."</label>";
                    $html.= "<select id='".$filter->name."' name='".$filter->name."' class='form-control form-control-sm text-uppercase'>";
                    $html.= "<option value=''>&laquo; Selecione &raquo;</option>";
                    foreach($filter->data as $fdata){
                        $html.= "<option value='".$fdata->value."'>".$fdata->key."</option>";
                    }
                    $html.= "</select>";
                    $html.= "</div>";
                    break;
                case 'mcombo': $html.= "<div class='form-group'>";
                    $html.= "<label class='control-label' for='".$filter->name."'>".$filter->text."</label>";
                    $html.= "<select id='".$filter->name."' name='".$filter->name."' multiple class='form-control form-control-sm text-uppercase'>";
                    $html.= "<option value=''>&laquo; Selecione &raquo;</option>";
                    foreach($filter->data as $data){
                        $html.= "<option value='".$data->value."'>".$data->key."</option>";
                    }
                    $html.= "</select>";
                    $html.= "</div>";
                    break;
                case 'bet_date':
                    $html.= "<div class='form-row'>";
						$html.= "<div class='form-group col-md-6'>";
							$html.= "<label class='control-label' for='".$filter->name."'>".$filter->text[0]."</label>";
							$html.= "<div class='input-group input-group-sm mb-3'>";
								$html.= "<input type='text' class='form-control date' id='".$filter->name."' name='".$filter->name."' autocomplete='off' data-provide='datepicker' data-date-format='dd/mm/yyyy' data-date-language='pt-BR' data-date-autoclose='true' data-date-today-highlight='true'/>";
								$html.= "<div class='input-group-append'>";
									$html.= "<span class='input-group-text'><i class='far fa-calendar-alt'></i></span>";
								$html.= "</div>";
							$html.= "</div>";
						$html.= "</div>";

						$html.= "<div class='form-group col-md-6'>";
							$html.= "<label class='control-label' for='".$filter->name."1'>".$filter->text[1]."</label>";
							$html.= "<div class='input-group input-group-sm mb-3'>";
								$html.= "<input type='text' class='form-control date' id='".$filter->name."1' name='".$filter->name."1' autocomplete='off' data-provide='datepicker' data-date-format='dd/mm/yyyy' data-date-language='pt-BR' data-date-autoclose='true' data-date-today-highlight='true'/>";
								$html.= "<div class='input-group-append'>";
									$html.= "<span class='input-group-text'><i class='far fa-calendar-alt'></i></span>";
								$html.= "</div>";
							$html.= "</div>";
						$html.= "</div>";
                    $html.= "</div>";
                    break;
            }
        }
		if(is_array($this->orders) && count($this->orders)>0){
			$html .= "</div><div class='col-md-6'>";
			$html .= "<div class='form-check form-check-inline'>";
				$html .= "<input class='form-check-input' type='radio' name='CHK_ORDER_DIRECT' id='CHK_ORDER_ASC' value='ASC' checked>";
				$html .= "<label class='form-check-label' for='CHK_ORDER_ASC'>Crescente</label>";
			$html .= "</div>";
			$html .= "<div class='form-check form-check-inline'>";
				$html .= "<input class='form-check-input' type='radio' name='CHK_ORDER_DIRECT' id='CHK_ORDER_DESC' value='DESC'>";
				$html .= "<label class='form-check-label' for='CHK_ORDER_DESC'>Decrescente</label>";
			$html.= "</div>";
		}
		
		//Montagem dos campos de ordenacao
		if(is_array($this->orders) && count($this->orders)>0){
			foreach($this->orders as $order){
				$html.= "<div class='form-group'>";
				$html.= "<label for='".$order->name."'>".$order->text."</label>";
				$html.= "<select id='".$order->name."' name='".$order->name."' class='form-control form-control-sm text-uppercase'>";
				$html.= "<option value=''>&laquo; Selecione &raquo;</option>";
				foreach($order->data as $fdata){
					$html.= "<option value='".$fdata->value."'>".$fdata->key."</option>";
				}
				$html.= "</select>";
				$html.= "</div>";
				break;
			}
		}
		
		if(is_array($this->orders) && count($this->orders)>0){
			$html .= "</div></div>";
		}
        $html.= "<div class='form-group text-right'><button type='button' class='btn btn-warning btn-sm' id='btnResetFilter' name='btnResetFilter' onclick='clearFilter();'><i class='fas fa-eraser'></i> Limpar Filtros</button> <button type='submit' class='btn btn-primary btn-sm' id='btnFilter'><i class='fas fa-search'></i> Filtrar</button></div>";
		$html.= "<input type='hidden' id='LNK_DATA' name='LNK_DATA'>";
        return $html;
    }
}
