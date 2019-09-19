<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\View\Helper;

use Cake\View\Helper;

/**
 * Description of FormatHelper
 *
 * @author hestilo
 */
class MaskHelper extends Helper{
    
    /**
     * Metodo que aplica mascara a um valor
     * @param string $val Valor que receberah a mascara
     * @param string $mask Mascara que serah aplicada utilizando # (ex: CEP - ##.###-###)
     * @return string
     */
    public function apply($val, $mask){
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++){
            if($mask[$i] == '#'){
                if(isset($val[$k])){
                    $maskared .= $val[$k++];
                }
            }
            else{
                if(isset($mask[$i])){
                    $maskared .= $mask[$i];
                }
            }
        }
        return $maskared;
    }
}
