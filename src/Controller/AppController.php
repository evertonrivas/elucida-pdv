<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');

        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
		
		$this->loadComponent('Auth', [
			'authenticate' => [
				'Form'	=> ['userModel' => 'SysUsers' ]
			]
        ]);
        
        $this->set('user',$this->Auth->user());
    }
    
    public function beforeFilter(Event $event){
		$this->Auth->autoRedirect = false;
	}
    
    /**
	* Metodo que busca os dados de um arquivo a partir de uma URL
	* @param string $url endereco do arquivo online
	* 
	* @return String contendo o conteudo do arquivo 
	*/
    public function file_get_contents_curl($url) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        //curl_setopt($ch, CURLOPT_HEADER, 0);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token token="a24a24203c1988ea43da13be38c25583"'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_URL, $url);     

        $data = curl_exec($ch);
        curl_close($ch);

        return $data; 
    }
    
    /**
	* Metodo que converte uma data no formato dd/mm/YYYY no formato YYYY-mm-dd
	* @param string $data data padrao brasileiro
	* 
	* @return string
	*/
    public function dateToDatabase($data){
        return substr($data, 6,4)."-".substr($data,3,2)."-".substr($data,0,2);
    }
    
    /**
	* Metodo que forma numeros acima de 1000 em formato curto para dashboard
	* @param float $number Numero que serah formatado
	* @param int $precision Precisao em casas demcimais
	* 
	* @return string
	*/
    public function numberShorten($number,$precision = 2){
		// Setup default $divisors if not provided
	    if (!isset($divisors)) {
	        $divisors = array(
	            pow(1000, 0) => '', // 1000^0 == 1
	            pow(1000, 1) => 'K', // Thousand
	            pow(1000, 2) => 'M', // Million
	            pow(1000, 3) => 'B', // Billion
	            pow(1000, 4) => 'T', // Trillion
	            pow(1000, 5) => 'Qa', // Quadrillion
	            pow(1000, 6) => 'Qi', // Quintillion
	        );    
	    }

	    // Loop through each $divisor and find the
	    // lowest amount that matches
	    foreach ($divisors as $divisor => $shorthand) {
	        if (abs($number) < ($divisor * 1000)) {
	            // We found a match!
	            break;
	        }
	    }

	    // We found our match, or there were no matches.
	    // Either way, use the last defined value for $divisor.
	    return number_format($number / $divisor, $precision) . $shorthand;
	}
}
