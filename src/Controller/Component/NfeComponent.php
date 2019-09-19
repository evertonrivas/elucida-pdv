<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
/**
 * Description of NfeComponent
 *
 * @author hestilo
 */
class NfeComponent  extends Component{
	var $version = "4.0";
	var $type    = "NFE";
    
    public function initialize(array $config) {
        parent::initialize($config);
    }
    
    /**
	* Metodo que define a versao da NF-e para trabalho
	* @param string $_version Versao da NFE/NFCE
	* @param string $_type Tipo do que que serah trabalhado NFE, NFCE ou NFSE
	* 
	* @return null
	*/
    public function setCongig($_version="4.0",$_type="NFE"){
		$this->version = $_version;
		$this->type    = $_type;
	}
    
    /**
	* Metodo que extrai as informacoes da NF-e do arquivo XML
	* @param string $_xml
	* 
	* @return Object um objeto contendo das informacoes da NF-e
	*/
    public function parseXml($_xml){
        $dom = new \DOMDocument('1.0', 'utf-8');

        $dom->loadXML($_xml);

        $nfe = new \stdClass();

		//extrai as informacoes por partes da NFE
        $infNFe = $dom->getElementsByTagName("infNFe")->item(0);
        $ide = $dom->getElementsByTagName("ide")->item(0);
        $emit = $dom->getElementsByTagName("emit")->item(0);
        $avulsa = $dom->getElementsByTagName("avulsa")->item(0);
        $dest = $dom->getElementsByTagName("dest")->item(0);
        $retirada = $dom->getElementsByTagName("retirada")->item(0);
        
        $entrega = $dom->getElementsByTagName("entrega")->item(0);
        $enderEmit = $dom->getElementsByTagName("enderEmit")->item(0);
        $enderDest = $dom->getElementsByTagName("enderDest")->item(0);
        $det = $dom->getElementsByTagName("det");
        $cobr = $dom->getElementsByTagName("cobr")->item(0);
        $ICMSTot = $dom->getElementsByTagName("ICMSTot")->item(0);
        $ISSQNtot = $dom->getElementsByTagName("ISSQNtot")->item(0);
        $retTrib = $dom->getElementsByTagName("retTrib")->item(0);
        $transp = $dom->getElementsByTagName("transp")->item(0);
        $infAdic = $dom->getElementsByTagName("infAdic")->item(0);
        $procRef = $dom->getElementsByTagName("procRef")->item(0);
        $exporta = $dom->getElementsByTagName("exporta")->item(0);
        $compra = $dom->getElementsByTagName("compra")->item(0);
        $cana = $dom->getElementsByTagName("cana")->item(0);
        //A|versao do schema|id|

		//extrai as informacoes do cabecalho da NFE
        $nfe->id     = $infNFe->getAttribute("Id") ? $infNFe->getAttribute("Id") : '';
        $nfe->versao = $infNFe->getAttribute("versao");
        $nfe->ide    = new \stdClass();
        $nfe->ide->cUF     = $ide->getElementsByTagName('cUF')->item(0)->nodeValue;
        $nfe->ide->cNF     = $ide->getElementsByTagName('cNF')->item(0)->nodeValue;
        $nfe->ide->natOp   = $ide->getElementsByTagName('natOp')->item(0)->nodeValue;
        $nfe->ide->indPag  = $ide->getElementsByTagName('indPag')->item(0)->nodeValue;
        $nfe->ide->mod     = $ide->getElementsByTagName('mod')->item(0)->nodeValue;
        $nfe->ide->serie   = $ide->getElementsByTagName('serie')->item(0)->nodeValue;
        $nfe->ide->nNF     = $ide->getElementsByTagName('nNF')->item(0)->nodeValue;
        $nfe->ide->dhEmi    = $ide->getElementsByTagName('dhEmi')->item(0)->nodeValue;
        $nfe->ide->dSaiEnt = !empty($ide->getElementsByTagName('dSaiEnt')->item(0)->nodeValue) ? $ide->getElementsByTagName('dSaiEnt')->item(0)->nodeValue : '';
        $nfe->ide->hSaiEnt = !empty($ide->getElementsByTagName('hSaiEnt')->item(0)->nodeValue) ? $ide->getElementsByTagName('hSaiEnt')->item(0)->nodeValue : '';
        $nfe->ide->tpNF    = $ide->getElementsByTagName('tpNF')->item(0)->nodeValue;
        $nfe->ide->cMunFG  = $ide->getElementsByTagName('cMunFG')->item(0)->nodeValue;
        $nfe->ide->tpImp   = $ide->getElementsByTagName('tpImp')->item(0)->nodeValue;
        $nfe->ide->tpEmis  = $ide->getElementsByTagName('tpEmis')->item(0)->nodeValue;
        $nfe->ide->cDV     = $ide->getElementsByTagName('cDV')->item(0)->nodeValue;
        $nfe->ide->tpAmb   = $ide->getElementsByTagName('tpAmb')->item(0)->nodeValue;
        $nfe->ide->finNFe  = $ide->getElementsByTagName('finNFe')->item(0)->nodeValue;
        $nfe->ide->procEmi = $ide->getElementsByTagName('procEmi')->item(0)->nodeValue;
        $nfe->ide->verProc = $ide->getElementsByTagName('verProc')->item(0)->nodeValue;
        //$nfe->ide->dhCont  = $ide->getElementsByTagName('dhCont')->item(0)->nodeValue;
        //$nfe->ide->xJust   = $ide->getElementsByTagName('xJust')->item(0)->nodeValue;

        $nfe->emit = new \stdClass();
        $nfe->emit->xNome = !empty($emit->getElementsByTagName('xNome')->item(0)->nodeValue) ? $emit->getElementsByTagName('xNome')->item(0)->nodeValue : '';
        $nfe->emit->xFant = !empty($emit->getElementsByTagName('xFant')->item(0)->nodeValue) ? $emit->getElementsByTagName('xFant')->item(0)->nodeValue : '';
        $nfe->emit->IE    = !empty($emit->getElementsByTagName('IE')->item(0)->nodeValue) ? $emit->getElementsByTagName('IE')->item(0)->nodeValue : '';
        $nfe->emit->IEST  = !empty($emit->getElementsByTagName('IEST')->item(0)->nodeValue) ? $emit->getElementsByTagName('IEST')->item(0)->nodeValue : '';
        $nfe->emit->IM    = !empty($emit->getElementsByTagName('IM')->item(0)->nodeValue) ? $emit->getElementsByTagName('IM')->item(0)->nodeValue : '';
        $nfe->emit->CNAE  = !empty($emit->getElementsByTagName('CNAE')->item(0)->nodeValue) ? $emit->getElementsByTagName('CNAE')->item(0)->nodeValue : '';
        $nfe->emit->CRT   = !empty($emit->getElementsByTagName('CRT')->item(0)->nodeValue) ? $emit->getElementsByTagName('CRT')->item(0)->nodeValue : '';
        $nfe->emit->CNPJ  = !empty($emit->getElementsByTagName('CNPJ')->item(0)->nodeValue) ? $emit->getElementsByTagName('CNPJ')->item(0)->nodeValue : '';
        $nfe->emit->CPF   = !empty($emit->getElementsByTagName('CPF')->item(0)->nodeValue) ? $emit->getElementsByTagName('CPF')->item(0)->nodeValue : '';

        $nfe->emit->enderEmit = new \stdClass();
        $nfe->emit->enderEmit->xLgr    = !empty($enderEmit->getElementsByTagName("xLgr")->item(0)->nodeValue) ? $enderEmit->getElementsByTagName("xLgr")->item(0)->nodeValue : '';
        $nfe->emit->enderEmit->nro     = !empty($enderEmit->getElementsByTagName("nro")->item(0)->nodeValue) ? $enderEmit->getElementsByTagName("nro")->item(0)->nodeValue : '';
        $nfe->emit->enderEmit->xCpl    = !empty($enderEmit->getElementsByTagName("xCpl")->item(0)->nodeValue) ? $enderEmit->getElementsByTagName("xCpl")->item(0)->nodeValue : '';
        $nfe->emit->enderEmit->xBairro = !empty($enderEmit->getElementsByTagName("xBairro")->item(0)->nodeValue) ? $enderEmit->getElementsByTagName("xBairro")->item(0)->nodeValue : '';
        $nfe->emit->enderEmit->cMun    = !empty($enderEmit->getElementsByTagName("cMun")->item(0)->nodeValue) ? $enderEmit->getElementsByTagName("cMun")->item(0)->nodeValue : '';
        $nfe->emit->enderEmit->xMun    = !empty($enderEmit->getElementsByTagName("xMun")->item(0)->nodeValue) ? $enderEmit->getElementsByTagName("xMun")->item(0)->nodeValue : '';
        $nfe->emit->enderEmit->UF      = !empty($enderEmit->getElementsByTagName("UF")->item(0)->nodeValue) ? $enderEmit->getElementsByTagName("UF")->item(0)->nodeValue : '';
        $nfe->emit->enderEmit->CEP     = !empty($enderEmit->getElementsByTagName("CEP")->item(0)->nodeValue) ? $enderEmit->getElementsByTagName("CEP")->item(0)->nodeValue : '';
        $nfe->emit->enderEmit->cPais   = !empty($enderEmit->getElementsByTagName("cPais")->item(0)->nodeValue) ? $enderEmit->getElementsByTagName("cPais")->item(0)->nodeValue : '';
        $nfe->emit->enderEmit->xPais   = !empty($enderEmit->getElementsByTagName("xPais")->item(0)->nodeValue) ? $enderEmit->getElementsByTagName("xPais")->item(0)->nodeValue : '';
        $nfe->emit->enderEmit->fone    = !empty($enderEmit->getElementsByTagName("fone")->item(0)->nodeValue) ? $enderEmit->getElementsByTagName("fone")->item(0)->nodeValue : '';

        $i=0;
        foreach ($det as $d) {

            $ndet = new \stdClass();
            $ndet->nItem     = $det->item($i)->getAttribute("nItem");      	
            $ndet->infAdProd = !empty($det->item($i)->getElementsByTagName("infAdProd")->item(0)->nodeValue) ? $det->item($i)->getElementsByTagName("infAdProd")->item(0)->nodeValue : '';

            //instanciar os grupos de dados internos da tag det
            $prod = $det->item($i)->getElementsByTagName("prod")->item(0);

            $ndet->prod = new \stdClass();

            $ndet->prod->cProd    = !empty($prod->getElementsByTagName("cProd")->item(0)->nodeValue) ? $prod->getElementsByTagName("cProd")->item(0)->nodeValue : '';
            $ndet->prod->cEAN     = !empty($prod->getElementsByTagName("cEAN")->item(0)->nodeValue) ? $prod->getElementsByTagName("cEAN")->item(0)->nodeValue : '';
            $ndet->prod->xProd    = !empty($prod->getElementsByTagName("xProd")->item(0)->nodeValue) ? $prod->getElementsByTagName("xProd")->item(0)->nodeValue : '';
            $ndet->prod->NCM      = !empty($prod->getElementsByTagName("NCM")->item(0)->nodeValue) ? $prod->getElementsByTagName("NCM")->item(0)->nodeValue : '';
            $ndet->prod->EXTIPI   = !empty($prod->getElementsByTagName("EXTIPI")->item(0)->nodeValue) ? $prod->getElementsByTagName("EXTIPI")->item(0)->nodeValue : '';
            $ndet->prod->CFOP     = !empty($prod->getElementsByTagName("CFOP")->item(0)->nodeValue) ? $prod->getElementsByTagName("CFOP")->item(0)->nodeValue : '';
            $ndet->prod->uCom     = !empty($prod->getElementsByTagName("uCom")->item(0)->nodeValue) ? $prod->getElementsByTagName("uCom")->item(0)->nodeValue : '';
            $ndet->prod->qCom     = !empty($prod->getElementsByTagName("qCom")->item(0)->nodeValue) ? $prod->getElementsByTagName("qCom")->item(0)->nodeValue : '';
            $ndet->prod->vUnCom   = !empty($prod->getElementsByTagName("vUnCom")->item(0)->nodeValue) ? $prod->getElementsByTagName("vUnCom")->item(0)->nodeValue : '';
            $ndet->prod->vProd    = !empty($prod->getElementsByTagName("vProd")->item(0)->nodeValue) ? $prod->getElementsByTagName("vProd")->item(0)->nodeValue : '';
            $ndet->prod->cEANTrib = !empty($prod->getElementsByTagName("cEANTrib")->item(0)->nodeValue) ? $prod->getElementsByTagName("cEANTrib")->item(0)->nodeValue : '';
            $ndet->prod->uTrib    = !empty($prod->getElementsByTagName("uTrib")->item(0)->nodeValue) ? $prod->getElementsByTagName("uTrib")->item(0)->nodeValue : '';
            $ndet->prod->qTrib    = !empty($prod->getElementsByTagName("qTrib")->item(0)->nodeValue) ? $prod->getElementsByTagName("qTrib")->item(0)->nodeValue : '';
            $ndet->prod->vUnTrib  = !empty($prod->getElementsByTagName("vUnTrib")->item(0)->nodeValue) ? $prod->getElementsByTagName("vUnTrib")->item(0)->nodeValue : '';
            $ndet->prod->vFrete   = !empty($prod->getElementsByTagName("vFrete")->item(0)->nodeValue) ? $prod->getElementsByTagName("vFrete")->item(0)->nodeValue : '';
            $ndet->prod->vSeg     = !empty($prod->getElementsByTagName("vSeg")->item(0)->nodeValue) ? $prod->getElementsByTagName("vSeg")->item(0)->nodeValue : '';
            $ndet->prod->vDesc    = !empty($prod->getElementsByTagName("vDesc")->item(0)->nodeValue) ? $prod->getElementsByTagName("vDesc")->item(0)->nodeValue : '';
            $ndet->prod->vOutro   = !empty($prod->getElementsByTagName("vOutro")->item(0)->nodeValue) ? $prod->getElementsByTagName("vOutro")->item(0)->nodeValue : '';
            $ndet->prod->indTot   = !empty($prod->getElementsByTagName("indTot")->item(0)->nodeValue) ? $prod->getElementsByTagName("indTot")->item(0)->nodeValue : '';
            $ndet->prod->xPed     = !empty($prod->getElementsByTagName("xPed")->item(0)->nodeValue) ? $prod->getElementsByTagName("xPed")->item(0)->nodeValue : '';
            $ndet->prod->nItemPed = !empty($prod->getElementsByTagName("nItemPed")->item(0)->nodeValue) ? $prod->getElementsByTagName("nItemPed")->item(0)->nodeValue : '';
            
            
            //obtem as informacoes de impostos
            $imposto = $det->item($i)->getElementsByTagName("imposto")->item(0);
            $ICMS    = $imposto->getElementsByTagName("ICMS")->item(0);
            $ICMS00  = $ICMS->getElementsByTagName("ICMS00")->item(0);
            if($ICMS00){
                $ndet->prod->CSOSN = "500"; //10, 30, 60 e 70
            }
            
            $ICMS10  = $ICMS->getElementsByTagName("ICMS10")->item(0);
            if($ICMS10){
                $ndet->prod->CSOSN = "500";
            }
            
            $ICMS20 = $ICMS->getElementsByTagName("ICMS20")->item(0);
            if($ICMS20){
                $ndet->prod->CSOSN = "103";
            }
            
            $ICMS30 = $ICMS->getElementsByTagName("ICMS30")->item(0);
            if($ICMS30){
                $ndet->prod->CSOSN = "500";
            }
            
            $ICMS40 = $ICMS->getElementsByTagName("ICMS40")->item(0);
            if($ICMS40){
                $ndet->prod->CSOSN = "103";
            }
            
            $ICMS51 = $ICMS->getElementsByTagName("ICMS51")->item(0);
            if($ICMS51){
                $ndet->prod->CSOSN = "103";
            }
            
            $ICMS60 = $ICMS->getElementsByTagName("ICMS60")->item(0);
            if($ICMS60){
                $ndet->prod->CSOSN = "500";
            }
            
            $ICMS70 = $ICMS->getElementsByTagName("ICMS70")->item(0);
            if($ICMS70){
                $ndet->prod->CSOSN = "500";
            }
            
            $ICMS90 = $ICMS->getElementsByTagName("ICMS90")->item(0);
            if($ICMS90){
                $ndet->prod->CSOSN = "103";
            }
            
            $ICMSPart = $ICMS->getElementsByTagName("ICMSPart")->item(0);
            if($ICMSPart){
                if($ICMSPart->getElementsByTagName("CST")->item(0)->nodeValue=="10"){
                    $ndet->prod->CSOSN = "103";
                }
                else{
                    $ndet->prod->CSOSN = "103";
                }
            }
            
            $ICMSST = $ICMS->getElementsByTagName("ICMSST")->item(0);
            if($ICMSST){
                $ndet->prod->CSOSN = "500";
            }
            
            $ICMSSN101 = $ICMS->getElementsByTagName("ICMSSN101")->item(0);
            if($ICMSSN101){//103
                $ndet->prod->CSOSN = "103";
            }
            
            $ICMSSN102 = $ICMS->getElementsByTagName("ICMSSN102")->item(0);
            if($ICMSSN102){//103
                $ndet->prod->CSOSN = "103";
            }
            
            $ICMSSN201 = $ICMS->getElementsByTagName("ICMSSN201")->item(0);
            if($ICMSSN201){//500
                $ndet->prod->CSOSN = "500";
            }
            
            $ICMSSN202 = $ICMS->getElementsByTagName("ICMSSN202")->item(0);
            if($ICMSSN202){//500
                $ndet->prod->CSOSN = "500";
            }
            
            $ICMSSN203 = $ICMS->getElementsByTagName("ICMSSN203")->item(0);
            if($ICMSSN203){//500
                $ndet->prod->CSOSN = "500";
            }
            
            $ICMSSN500 = $ICMS->getElementsByTagName("ICMSSN500")->item(0);
            if($ICMSSN500){
                $ndet->prod->CSOSN = $ICMSSN500->getElementsByTagName("CSOSN")->item(0)->nodeValue;
            }
            
            $ICMSSN900 = $ICMS->getElementsByTagName("ICMSSN900")->item(0);
            if($ICMSSN900){
                $ndet->prod->CSOSN = "103";
            }

            $nfe->det[] = $ndet;

            $i++;
        } //fim foreach itens

        $nfe->vBC     = !empty($ICMSTot->getElementsByTagName("vBC")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vBC")->item(0)->nodeValue : '';
        $nfe->vICMS   = !empty($ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue : '';
        $nfe->vBCST   = !empty($ICMSTot->getElementsByTagName("vBCST")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vBCST")->item(0)->nodeValue : '';
        $nfe->vST     = !empty($ICMSTot->getElementsByTagName("vST")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vST")->item(0)->nodeValue : '';
        $nfe->vProd   = !empty($ICMSTot->getElementsByTagName("vProd")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vProd")->item(0)->nodeValue : '';
        $nfe->vFrete  = !empty($ICMSTot->getElementsByTagName("vFrete")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vFrete")->item(0)->nodeValue : '';
        $nfe->vSeg    = !empty($ICMSTot->getElementsByTagName("vSeg")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vSeg")->item(0)->nodeValue : '';
        $nfe->vDesc   = !empty($ICMSTot->getElementsByTagName("vDesc")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vDesc")->item(0)->nodeValue : '';
        $nfe->vII     = !empty($ICMSTot->getElementsByTagName("vII")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vII")->item(0)->nodeValue : '';
        $nfe->vIPI    = !empty($ICMSTot->getElementsByTagName("vIPI")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vIPI")->item(0)->nodeValue : '';
        $nfe->vPIS    = !empty($ICMSTot->getElementsByTagName("vPIS")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vPIS")->item(0)->nodeValue : '';
        $nfe->vCOFINS = !empty($ICMSTot->getElementsByTagName("vCOFINS")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vCOFINS")->item(0)->nodeValue : '';
        $nfe->vOutro  = !empty($ICMSTot->getElementsByTagName("vOutro")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vOutro")->item(0)->nodeValue : '';
        $nfe->vNF     = !empty($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue : '';

        if (isset($transp)) {
            //instancia sub grupos da tag transp
            $transporta = $dom->getElementsByTagName("transporta")->item(0);
            $retTransp = $dom->getElementsByTagName("retTransp")->item(0);
            $veicTransp = $dom->getElementsByTagName("veicTransp")->item(0);
            $reboque = $dom->getElementsByTagName("reboque");
            $vol = $dom->getElementsByTagName("vol");

            $nfe->transp = new \stdClass();

            $nfe->transp->transporta = new \stdClass();
            $nfe->transp->retTransp  = new \stdClass();
            $nfe->transp->veicTransp = new \stdClass();

            $nfe->modFrete = !empty($transp->getElementsByTagName("modFrete")->item(0)->nodeValue) ? $transp->getElementsByTagName("modFrete")->item(0)->nodeValue : '';
            if (isset($transporta)) {
                $nfe->transp->transporta->CNPJ   = !empty($transporta->getElementsByTagName("CNPJ")->item(0)->nodeValue) ? $transporta->getElementsByTagName("CNPJ")->item(0)->nodeValue : '';
                $nfe->transp->transporta->CPF    = !empty($transporta->getElementsByTagName("CPF")->item(0)->nodeValue) ? $transporta->getElementsByTagName("CPF")->item(0)->nodeValue : '';
                $nfe->transp->transporta->IE     = !empty($transporta->getElementsByTagName("IE")->item(0)->nodeValue) ? $transporta->getElementsByTagName("IE")->item(0)->nodeValue : '';
                $nfe->transp->transporta->xNome  = !empty($transporta->getElementsByTagName("xNome")->item(0)->nodeValue) ? $transporta->getElementsByTagName("xNome")->item(0)->nodeValue : '';
                $nfe->transp->transporta->xEnder = !empty($transporta->getElementsByTagName("xEnder")->item(0)->nodeValue) ? $transporta->getElementsByTagName("xEnder")->item(0)->nodeValue : '';
                $nfe->transp->transporta->xMun   = !empty($transporta->getElementsByTagName("xMun")->item(0)->nodeValue) ? $transporta->getElementsByTagName("xMun")->item(0)->nodeValue : '';
                $nfe->transp->transporta->UF     = !empty($transporta->getElementsByTagName("UF")->item(0)->nodeValue) ? $transporta->getElementsByTagName("UF")->item(0)->nodeValue : '';
            } // fim transporta
            //monta dados da reten磯 tributᲩa de transporte
            if (isset($retTransp)) {
                $nfe->transp->retTransp->vServ    = !empty($retTransp->getElementsByTagName("vServ")->item(0)->nodeValue) ? $retTransp->getElementsByTagName("vServ")->item(0)->nodeValue : '';
                $nfe->transp->retTransp->vBCRet   = !empty($retTransp->getElementsByTagName("vBCRet")->item(0)->nodeValue) ? $retTransp->getElementsByTagName("vBCRet")->item(0)->nodeValue : '';
                $nfe->transp->retTransp->pICMSRet = !empty($retTransp->getElementsByTagName("pICMSRet")->item(0)->nodeValue) ? $retTransp->getElementsByTagName("pICMSRet")->item(0)->nodeValue : '';
                $nfe->transp->retTransp->vICMSRet = !empty($retTransp->getElementsByTagName("vICMSRet")->item(0)->nodeValue) ? $retTransp->getElementsByTagName("vICMSRet")->item(0)->nodeValue : '';
                $nfe->transp->retTransp->CFOP     = !empty($retTransp->getElementsByTagName("CFOP")->item(0)->nodeValue) ? $retTransp->getElementsByTagName("CFOP")->item(0)->nodeValue : '';
                $nfe->transp->retTransp->cMunFG   = !empty($retTransp->getElementsByTagName("cMunFG")->item(0)->nodeValue) ? $retTransp->getElementsByTagName("cMunFG")->item(0)->nodeValue : '';
            } // fim rettransp
            //monta dados de identificacao dos veiculos utilizados no transporte
            if (isset($veicTransp)) {
                //X18|Placa|UF|RNTC|
                $nfe->transp->veicTransp->placa = !empty($veicTransp->getElementsByTagName("placa")->item(0)->nodeValue) ? $veicTransp->getElementsByTagName("placa")->item(0)->nodeValue : '';
                $nfe->transp->veicTransp->UF    = !empty($veicTransp->getElementsByTagName("UF")->item(0)->nodeValue) ? $veicTransp->getElementsByTagName("UF")->item(0)->nodeValue : '';
                $nfe->transp->veicTransp->RNTC  = !empty($veicTransp->getElementsByTagName("RNTC")->item(0)->nodeValue) ? $veicTransp->getElementsByTagName("RNTC")->item(0)->nodeValue : '';
            } //fim veicTransp
            //monta dados de identificacao dos reboques utilizados no transporte
            if (isset($reboque)) {
                foreach ($reboque as $n => $reb) {
                    $nfe->transp->reboque['placa'][] = !empty($reboque->item($n)->getElementsByTagName("placa")->item(0)->nodeValue) ? $reboque->item($n)->getElementsByTagName("placa")->item(0)->nodeValue : '';
                    $nfe->transp->reboque['UF'][]    = !empty($reboque->item($n)->getElementsByTagName("UF")->item(0)->nodeValue) ? $reboque->item($n)->getElementsByTagName("UF")->item(0)->nodeValue : '';
                    $nfe->transp->reboque['RNTC']    = !empty($reboque->item($n)->getElementsByTagName("RNTC")->item(0)->nodeValue) ? $reboque->item($n)->getElementsByTagName("RNTC")->item(0)->nodeValue : '';
                } //fim foreach
            } //fim reboque
            //monta dados dos volumes transportados
            if (isset($vol)) {
                foreach ($vol as $n => $volumes) {
                    //X26|QVol|Esp|Marca|NVol|PesoL|PesoB|
                    $nfe->transp->vol['qVol'][] = !empty($vol->item($n)->getElementsByTagName("qVol")->item(0)->nodeValue) ? $vol->item($n)->getElementsByTagName("qVol")->item(0)->nodeValue : '';
                    $nfe->transp->vol['esp'][] = !empty($vol->item($n)->getElementsByTagName("esp")->item(0)->nodeValue) ? $vol->item($n)->getElementsByTagName("esp")->item(0)->nodeValue : '';
                    $nfe->transp->vol['marca'][] = !empty($vol->item($n)->getElementsByTagName("marca")->item(0)->nodeValue) ? $vol->item($n)->getElementsByTagName("marca")->item(0)->nodeValue : '';
                    $nfe->transp->vol['nVol'][] = !empty($vol->item($n)->getElementsByTagName("nVol")->item(0)->nodeValue) ? $vol->item($n)->getElementsByTagName("nVol")->item(0)->nodeValue : '';
                    $nfe->transp->vol['pesoL'][] = !empty($vol->item($n)->getElementsByTagName("pesoL")->item(0)->nodeValue) ? $vol->item($n)->getElementsByTagName("pesoL")->item(0)->nodeValue : '';
                    $nfe->transp->vol['pesoB'][] = !empty($vol->item($n)->getElementsByTagName("pesoB")->item(0)->nodeValue) ? $vol->item($n)->getElementsByTagName("pesoB")->item(0)->nodeValue : '';
                    $lacres = $vol->item($n)->getElementsByTagName("lacres")->item(0);
                    //monta dados dos lacres utilizados
                    if (isset($lacres)) {
                        foreach ($lacres as $l => $lac) {
                            $nfe->transp->vol['lacres']['nLacre'][] = !empty($lacres->item($l)->getElementsByTagName("nLacre")->item(0)->nodeValue) ? $lacres->item($l)->getElementsByTagName("nLacre")->item(0)->nodeValue : '';
                        } //fim foreach lacre
                    } //fim lacres
                } //fim foreach volumes
            } //fim vol
        }//fim monta transp

        if (isset($cobr)) {
            //instancia sub grupos da tag cobr
            $nfe->cobr = new \stdClass();
            $fat = $dom->getElementsByTagName('fat')->item(0);
            $dup = $dom->getElementsByTagName('dup');
            //monta dados da fatura
            if (isset($fat)) {
                $nfe->cobr->fat = new \stdClass();
                $nfe->cobr->fat->nFat  = !empty($fat->getElementsByTagName("nFat")->item(0)->nodeValue) ? $fat->getElementsByTagName("nFat")->item(0)->nodeValue : '';
                $nfe->cobr->fat->vOrig = !empty($fat->getElementsByTagName("vOrig")->item(0)->nodeValue) ? $fat->getElementsByTagName("vOrig")->item(0)->nodeValue : '';
                $nfe->cobr->fat->vDesc = !empty($fat->getElementsByTagName("vDesc")->item(0)->nodeValue) ? $fat->getElementsByTagName("vDesc")->item(0)->nodeValue : '';
                $nfe->cobr->fat->vLiq  = !empty($fat->getElementsByTagName("vLiq")->item(0)->nodeValue) ? $fat->getElementsByTagName("vLiq")->item(0)->nodeValue : '';
            } //fim fat
            //monta dados das duplicatas
            if (isset($dup)) {
                foreach ($dup as $n => $duplicata) {
                    $ndup = new \stdClass();
                    $ndup->nDup  = !empty($dup->item($n)->getElementsByTagName("nDup")->item(0)->nodeValue) ? $dup->item($n)->getElementsByTagName("nDup")->item(0)->nodeValue : '';
                    $ndup->dVenc = !empty($dup->item($n)->getElementsByTagName("dVenc")->item(0)->nodeValue) ? $dup->item($n)->getElementsByTagName("dVenc")->item(0)->nodeValue : '';
                    $ndup->vDup  =!empty($dup->item($n)->getElementsByTagName("vDup")->item(0)->nodeValue) ? $dup->item($n)->getElementsByTagName("vDup")->item(0)->nodeValue : '';
                    $nfe->cobr->dup[] = $ndup;
                } //fim foreach
            } //fim dup
        } //fim cobr

        return $nfe;
    }

	/**
	* Metodo que montar o arquivo XML da NFE/NFCE/NFSE
	* @param object $object Objeto contendo as informacoes para criar o XML
	* 
	* @return string conteudo do arquivo XML
	*/
	public function mountXml($object){
		
	}
	
	/**
	* Metodo que realiza a validacao da NFE/NFCE com base no arquivo XSD
	* 
	* @return boolean
	*/
	public function validateXml(){
		
	}
}
