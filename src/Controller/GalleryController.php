<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;
use \Cake\Event\Event;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\I18n\Number;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. */

/**
 * Description of GalleryController
 *
 * @author hestilo
 */
 class GalleryController extends AppController{
	 
	public function initialize() {
        parent::initialize();
        $this->loadComponent("Paginator");
		
        $this->loadComponent("Filter");
        ob_start("ob_gzhandler");
    }
    
    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
		
    }
	
	public function index($_defaultLayout=""){
		if($_defaultLayout=="") $this->viewBuilder()->setLayout("gallery");
		$albuns = TableRegistry::get("SysGaleriaAlbum")->find()->select(['IDALBUM' => 'SysGaleriaAlbum.IDALBUM','NOME','CRIADO_EM','MODIFICADO_EM','TOTAL' => '(SELECT COUNT(IDIMAGEM) AS TOTAL FROM sys_galeria_imagem WHERE IDALBUM=SysGaleriaAlbum.IDALBUM)']);
		$this->set("album_list",$albuns);
		$this->set('default_layout',$_defaultLayout);
	}
	
	public function galleryData($_defaultLayout=""){
		//realiza a busca das informacoes do album
		$albuns = TableRegistry::get("SysGaleriaAlbum")->find()->select(['IDALBUM' => 'SysGaleriaAlbum.IDALBUM','NOME','CRIADO_EM','MODIFICADO_EM','TOTAL' => '(SELECT COUNT(IDIMAGEM) AS TOTAL FROM sys_galeria_imagem WHERE IDALBUM=SysGaleriaAlbum.IDALBUM)']);
		$this->set("album_list",$albuns);
		
		$images = TableRegistry::get("SysGaleriaImagem")->find()->select(['IDIMAGEM','ALBUM' => '(SELECT NOME FROM sys_galeria_album WHERE IDALBUM=SysGaleriaImagem.IDALBUM)','NOME','ARQUIVO','TAMANHO','DIMENSAO','CRIADO_EM','MODIFICADO_EM','IDALBUM']);
		
		$this->set("image_list",$images);
		$this->set('default_layout',$_defaultLayout);
	}
	
	public function albumSave(){
		$tbl = TableRegistry::get("SysGaleriaAlbum");
		
		if($this->request->getData("IDALBUM")!=""){
			$album = $tbl->get($this->request->getData("IDALBUM"));
			$album->MODIFICADO_EM = date("H-m-d H:i:s");
		}else{
			$album = $tbl->newEntity();
			$album->CRIADO_EM = date("H-m-d H:i:s");
		}
		
		$album->NOME    = $this->request->getData("NOME");
		
		$retorno = $tbl->save($album);
		
		if($retorno){
			$this->createFolder($album->IDALBUM);
		}
		
		return $this->response->withStringBody( $retorno );
	}
	
	public function albumDrop(){
		//busca o album 
		$tblAlb = TableRegistry::get("SysGaleriaAlbum");
		$tblImg = TableRegistry::get("SysGaleriaImagem");
		
		//busca as informacoes do album
		$alb = $tblAlb->get($this->request->getData("IDALBUM"));
		
		//busca as imagens do album
		foreach($tblImg->find()->where(["IDALBUM" => $alb->IDALBUM]) as $img){
			//monta o objeto para excluir o arquivo
			$file = new File(WWW_ROOT.'img'.DS.'gallery'.DS.$alb->IDALBUM.DS.$img->ARQUIVO);
			$file->delete(); //remove o arquivo
			
			$tblImg->delete($img); //remove do banco de dados
		}
		
		//monta o objeto para excluir a pasta do album
		$fld = new Folder(WWW_ROOT.'img'.DS.'gallery'.DS.$alb->IDALBUM);
		
		//remove a pasta do album
		$fld->delete();
		
		return $this->response->withStringBody( $tblAlb->delete($alb) );
	}
	
	public function algumGet(){
		$album = TableRegistry::get('SysGaleriaAlbum')->get($this->request->getData("IDALBUM"));
		
		return $this->response->withStringBody( json_encode($album) );
	}
	
	/**
	* Metodo que cria a pasta fisica onde serao armazenadas as imagens
	* @param int $_idAlbum codigo identificador do album
	* 
	* @return
	*/
	private function createFolder($_idAlbum){
		$path_gallery = WWW_ROOT.'img'.DS.'gallery';
		$path_album   = $path_gallery.DS.$_idAlbum;
		
		if(!is_dir($path_gallery)){
			new Folder($path_gallery,true,0755);
		}
		
		if(!is_dir($path_album)){
			new Folder($path_album,true,0755);
		}
	}
	
	public function uploadAndSave(){
		$this->autoRender = false;
		
		$album_path = WWW_ROOT.'img'.DS.'gallery'.DS.$this->request->getData("txtIdAlbum");
		
        try{
            $tmpFile = $this->request->getData("file");
			$destiny = $album_path.DS.$tmpFile['name'];
            //print_r($this->request->getData());
            if(move_uploaded_file($tmpFile['tmp_name'], $destiny)){

				$tbl = TableRegistry::get("SysGaleriaImagem");
				$imagem = $tbl->newEntity();
				$imagem->IDALBUM = $this->request->getData("txtIdAlbum");
				$imagem->NOME    = substr($tmpFile['name'],0,strpos($tmpFile['name'],'.'));
				$imagem->ARQUIVO = $tmpFile['name'];
				$imagem->TAMANHO = $tmpFile['size']/1024;
				$imagem->DIMENSAO = "";
				$imagem->CRIADO_EM = date("Y-m-d H:i:s");
				
				$tbl->save($imagem);
				
				
                return $this->response->withStringBody( true );
            }
        }catch(Exception $ex){
            return $this->response->withStringBody( false );
        }
	}
	
	public function uploadFromUrl(){
		$this->autoRender = false;
		
		$content = $this->file_get_contents_curl($this->request->getData("URL"));
		$img_name = substr($this->request->getData("URL"),strrpos($this->request->getData("URL"),"/")+1, (strrpos($this->request->getData("URL"),".")-strrpos($this->request->getData("URL"),"/"))+3 );
		
		//salva a imagem na pasta do album
		$fp = fopen(WWW_ROOT.'img'.DS.'gallery'.DS.$this->request->getData("IDALBUM").DS.$img_name,"w");
		fwrite($fp,$content);
		fclose($fp);
		
		$file = new File(WWW_ROOT.'img'.DS.'gallery'.DS.$this->request->getData("IDALBUM").DS.$img_name);
		
		$tbl = TableRegistry::get("SysGaleriaImagem");
		$imagem = $tbl->newEntity();
		$imagem->IDALBUM = $this->request->getData("IDALBUM");
		$imagem->NOME    = substr($img_name,0,strpos($img_name,'.'));
		$imagem->ARQUIVO = $img_name;
		$imagem->TAMANHO = $file->size()/1024;
		$imagem->DIMENSAO = "";
		$imagem->CRIADO_EM = date("Y-m-d H:i:s");
		
		return $this->response->withStringBody( $tbl->save($imagem) ? true: false );	
		
	}
	
	public function imageDrop(){
		$tbl = TableRegistry::get("SysGaleriaImagem");
		$image = $tbl->get($this->request->getData("IDIMAGEM"));
		
		$file = new File(WWW_ROOT.'img'.DS.'gallery'.DS.$image->IDALBUM.DS.$image->ARQUIVO);
		$file->delete();
		
		return $this->response->withStringBody( $tbl->delete($image)?true:false );
	}
	
	public function imageRename(){
		$tbl = TableRegistry::get("SysGaleriaImagem");
		$img = $tbl->get($this->request->getData("IDIMAGEM"));
		$img->NOME = $this->request->getData("NOME");
		
		return $this->response->withStringBody( $tbl->save($img)?true:false );
	}
	
	public function image($_id){
		$img = TableRegistry::get("SysGaleriaImagem")->get($_id);
		
		return $this->response->withStringBody( json_encode($img) );
	}
 }