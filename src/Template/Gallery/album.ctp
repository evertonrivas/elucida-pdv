<?=$this->Html->script("dropzone.js");?>
<?=$this->Html->css("dropzone.css");?>
<div class="card">
	<div class="card-header">
		<div class="row">
			<div class="col">
				<div class="btn-group">
					<button type="button" id="btnUpload" name="btnUpload"  class="btn btn-outline-secondary btn-sm"><i class="fas fa-images"></i> Enviar Imagem</button>
						<button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="javascript:inputUrl();">Adicionar usando URL</a>
					</div>
				</div>
			</div>
			<div class="col text-right"><a href="/gallery/"><i class="fas fa-angle-double-left"></i> Voltar aos &aacute;lbuns</a></div>
		</div>
	</div>
	<div class="card-body" id="divAlbum">
		
	</div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modalUpload">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Envio de Imagem(ns)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<form action="/gallery/upload_and_save" class="dropzone needsclick dz-clickable" id="my-dropzone">
			<input type="hidden" id="_csrfToken" name="_csrfToken" value=<?= json_encode($this->request->getParam('_csrfToken')) ?>>
			<input type="hidden" id="txtIdAlbum" name="txtIdAlbum" value="<?=$ID_ALBUM;?>">
		  <div class="fallback">
			<input name="file" type="file" multiple />
		  </div>
		</form>
      </div>
    </div>
  </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modalRename">
	<form id="frmRename">
	<input type="hidden" name="txtIdImageNewName" id="txtIdImageNewName" value="">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edi&ccedil;&atilde;o de Imagem</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<div class="form-group">
			<label for="txtImageNewName">Nome</label>
			<input type="text" id="txtImageNewName" name="txtImageNewName" class="form-control" value="">
		</div>
      </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times-circle"></i></button>
		<button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check-circle"></i></button>
	  </div>
    </div>
  </div>
  </form>
</div>

<form id="frmUploadUrl">
	<div class="modal" tabindex="-1" role="dialog" id="modalUrl">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">URL da Imagem</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<label for="txtImageUrl">Nome</label>
					<div class="input-group mb-3">						
						<input type="text" id="txtImageUrl" name="txtImageUrl" class="form-control" value="" placeholder="http://your-image-url">
						<div class="input-group-append">
							<button class="btn btn-outline-success" type="submit" id="button-addon2" title="Enviar"><i class="fas fa-upload"></i></button>
						</div>
						<small class="text-danger">Cuide para que o arquivo possua um extens&atilde;o conhecida (jpg, gif, png ou bmp)</small>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>


<script>
$(document).ready(function(){
	getImages();
});

Dropzone.options.myDropzone = {
	parallelUploads     : 5,
	maxFiles            : 5,
	acceptedFiles       : 'image/*',
	dictDefaultMessage  : 'Arraste até 5 arquivos aqui ou clique para selecionar',
	dictFallbackMessage : 'Seu navegador não suporta arrastar e soltar arquivos',
	dictFileTooBig      : '"Arquivo muito grande ({{filesize}}MiB). Tamanho máximo permitido: {{maxFilesize}}MiB."',
	dictInvalidFileType : 'Você não pode enviar esse tipo de arquivo',
	
	success:function(){
		$("#modalUpload").modal("hide");
		getImages();
		this.removeAllFiles();
	}
};

$(document).on("click","#btnUpload",function(){
	$("#modalUpload").modal({
		backdrop: 'static'
	});
});

$(document).on("submit","#frmRename",function(event){
	event.preventDefault();
	$.ajax({
		headers: {
			'X-CSRF-Token' : csrf
		},
		url    : '/gallery/image_rename',
		data   : { IDIMAGEM: $("#txtIdImageNewName").val(), NOME : $("#txtImageNewName").val() },
		method : 'post',		
		success: function(data){
			if(data){
				bootbox.alert("Imagem renomeada com sucesso!",function(){ getImages() });
			}else{
				bootbox.alert("Ocorreu um erro ao tentar renomear a imagem!");
			}
			
			$("#modalRename").modal("hide");
		}
	});
});

$(document).on("submit","#frmUploadUrl",function(event){
	event.preventDefault();
	
	$.ajax({
		headers: {
			'X-CSRF-Token' : csrf
		},
		url    : '/gallery/upload_from_url',
		data   : { URL : $("#txtImageUrl").val(),IDALBUM: <?=$ID_ALBUM; ?> },
		method : 'post',		
		success: function(data){
			$("#modalUrl").modal("hide");
			$("#txtImageUrl").val("");
			if(data){
				bootbox.alert("Imagem enviada com sucesso!",function(){ getImages() });
			}else{
				bootbox.alert("Ocorreu um erro ao tentar enviar a imagem!");
			}
		}
	});
});

function getImages(){
	$.ajax({
		headers : {
			'X-CSRF-Token' : csrf
		},
		url     : '/gallery/album_data',
		data    : { IDALBUM : <?=$ID_ALBUM; ?> },
		method  : 'post',
		success : function(data){
			$("#divAlbum").html(data);
		}
	});
}

function dropImage(idImage){
	bootbox.confirm({
		message: "Deseja realmente apagar esta imagem?<br/><small><strong>* Aten&ccedil;&atilde;o</strong> isso poder&aacute; causar problema em algum produto que use a imagem.</small>",
		buttons:{
			confirm: { label:"Sim" },
			cancel: { label:"N&atilde;o" }
		},
		callback: function(result){
			if(result){
				$.ajax({
					headers : {
						'X-CSRF-Token' : csrf
					},
					url     : '/gallery/image_drop',
					data    : { IDIMAGEM : idImage },
					method  : 'post',
					success : function(data){
						if(data){
							bootbox.alert("Imagem apagada com sucesso!",function(){ getImages() });
						}else{
							bootbox.alert("Ocorreu um erro ao tentar apagar a imagem!");
						}
					}
				});
			}
		}
	});
}

function renameImage(idImagem,nome){
	$("#modalRename").modal({
		backdrop: 'static'
	});
	
	$("#txtImageNewName").val(nome);
	$("#txtIdImageNewName").val(idImagem);
}

function inputUrl(){
	$("#modalUrl").modal({
		backdrop: 'static'
	});
}
</script>