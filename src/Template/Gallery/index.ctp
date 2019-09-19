<?= $this->Html->css('/dropify/css/dropify.min.css') ?>
<?= $this->Html->css('magnific-popup.css') ?>

<?= $this->Html->script("isotope.pkgd.min.js");?>
<?= $this->Html->script("jquery.magnific-popup.min.js");?>
<?= $this->Html->script("masonry.pkgd.min.js");?>
<?= $this->Html->script("imagesloaded.pkgd.min.js");?>
<?= $this->Html->script("/dropify/js/dropify.min.js");?>
<br/>
<div id="divAlbum">
	
</div>

<!-- MODAL DE NOVO ALBUM -->
<form id="frmNewAlbum" name="frmNewAlbum">
	<input type="hidden" id="txtIdAlbum" name="txtIdAlbum">
	<div class="modal fade" tabindex="-1" role="dialog" id="modalNewAlbum">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Cria&ccedil;&atilde;o de &Aacute;lbum</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="form-group">
				<label for="txtAlbumName">Nome</label>
				<input type="text" id="txtAlbumName" name="txtAlbumName" class="form-control form-control-sm">
			</div>
		  </div>
		  <div class="modal-footer">
			<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i>  Salvar</button>
		  </div>
		</div>
	  </div>
	 </div>
	</div>
</form>

<!-- MODAL DE RENOMEAR A IMAGEM -->
<div class="modal fade" tabindex="-1" role="dialog" id="modalRename">
	<form id="frmRenameImage">
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

<!-- MODAL DE ENVIO DE IMAGEM -->
<form class="tabs-validation" novalidate id="frmUploadAndSave" name="frmUploadAndSave" enctype="multipart/form-data">
<div class="modal fade" tabindex="-1" role="dialog" id="modalUpload">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Envio de Imagem(ns)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<div class="form-group">
				<select class="form-control form-control-sm" id="txtIdAlbum" name="txtIdAlbum" required>
					<option value="">&laquo; Selecione o &aacute;lbum de destino &raquo;</option>
					<?php foreach($album_list as $album): ?>
					<option value="<?=$album->IDALBUM?>"><?=$album->NOME;?></option>
					<?php endforeach;?>
				</select>
			</div>
		  <div class="fallback">
			<input name="file" type="file" class="dropify"/>
		  </div>
      </div>
      <div class="modal-footer">
      	<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-file-import"></i> Enviar arquivo</button>
      </div>
    </div>
  </div>
</div>
</form>

<script>
var drEvent = null;
$(document).ready(function(){
	getAlbuns();
		// Basic
	drEvent = $('.dropify').dropify({
		messages:{
			'default': "Arraste um arquivo aqui ou clique",
		  	'replace': "Arraste um arquivo aqui ou clique para mudar",
		  	'remove' : "Remover",
		  	'error'  : "Ocorreu um erro ao enviar o arquivo"
		},
		error: {
		    'fileSize': 'O tamanho do arquivo \xE9 muito grande, m\xE1x: {{ value }}.',
		    'minWidth': 'A largura da imagem \xE9 muito pequena, min: {{ value }}}px.',
		    'maxWidth': 'A lartura da imagem \xE9 muito grande, m\xE1x: {{ value }}}px.',
		    'minHeight': 'A altura da imagem \xE9 muito pequena, min: {{ value }}}px.',
		    'maxHeight': 'A altura da imagem \xE9 muito grande, m\xE1x: {{ value }}px.',
		    'imageFormat': 'Formato de imagem n\xE3o permitido, apenas: {{ value }}.',
		    'fileExtension': 'Arquivo n\xE3o permitido, apenas: {{ value }}.'
		},
		allowedFileExtensions:['png', 'jpg', 'jpeg', 'gif', 'bmp']
	});
});

$(document).ajaxComplete(function(){
	var $container = $('.projects-wrapper');
	var $filter = $('#filter');
	// Initialize isotope 
	$container.isotope({
	  filter: '*',
	  layoutMode: 'masonry',
	  animationOptions: {
	      duration: 750,
	      easing: 'linear'
	  }
	});
  
	$container.imagesLoaded().progress( function() {
		$container.isotope('layout');
	});
  
	// Filter items when filter link is clicked
	$filter.find('a').click(function() {
		var selector = $(this).attr('data-filter');
		if(selector==undefined){
		return true;
		}

		$filter.find('a').removeClass('active');
		$(this).addClass('active');
		$container.isotope({
			filter: selector,
			animationOptions: {
				animationDuration: 750,
				easing: 'linear',
				queue: false,
			}
		});
		return false;
	});
});

function getAlbuns(){
	$.ajax({
		headers : {
			'X-CSRF-Token' : csrf
		},
		url     : '<?=$this->Url->build("/gallery/gallery_data/")?><?=$default_layout?>',
		method  : 'post',
		success : function(data){
			$("#divAlbum").html(data);
		}
	});
}

function dropAlbum(idAlbum){
	bootbox.confirm({
		message: "Deseja realmente excluir este &aacute;lbum?<br><small><strong>* Aten&ccedil;&atilde;o:</strong> Esta a&ccedil;&atilde;o apagar&aacute; o &aacute;lbum e suas respectivas imagens do servidor. Se algum produto usar imagem desse &aacute;lbum, os dados ser&atilde;o perdidos.</small>",
		buttons:{
			confirm: {label: "Sim" },
			cancel: {label: "N&atilde;o"}
		},
		callback: function(result){
			if(result){
				$.ajax({
					headers : {
						'X-CSRF-Token' : csrf
					},
					url     : '<?=$this->Url->build("/gallery/album_drop")?>',
					data    : { IDALBUM : idAlbum },
					method  : 'post',
					success : function(data){
						if(data){
							bootbox.alert("&Aacute;bum apagado com sucesso!",function(){ getAlbuns() });
						}else{
							bootbox.alert("Ocorreu um erro ao tentar apagar o &aacute;lbum!");
						}
					}
				});
			}
		}
	});
}

function configureAlbum(idAlbum){
	$.ajax({
		headers:{
			'X-CSRF-Token':csrf
		},
		url      : '<?=$this->Url->build("/gallery/algum_get/")?>',
		data     : { IDALBUM: idAlbum },
		method   : 'post',
		dataType : 'json',
		success  : function(data){
			$("#txtIdAlbum").val(data.IDALBUM);
			$("#txtAlbumName").val(data.NOME);
			$("#txtAlbumTag").val(data.TAGS);
			$("#rngAlbumCol").val(data.COLUNAS);
			
			$("#modalNewAlbum").modal({
				backdrop:'static'
			});
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
					url     : '<?=$this->Url->build("/gallery/image_drop")?>',
					data    : { IDIMAGEM : idImage },
					method  : 'post',
					success : function(data){
						if(data){
							bootbox.alert("Imagem apagada com sucesso!",function(){ getAlbuns() });
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

function checkAba(){
	if(drEvent.length==undefined){
		bootbox.alert("Por favor selecione uma imagem para salvar!");
		return false;
	}
	return true;
}

$(document).on("submit","#frmNewAlbum",function(event){
	event.preventDefault();
	
	var dataForm = {
		IDALBUM : $("#txtIdAlbum").val(),
		NOME    : $("#txtAlbumName").val()
	};
	
	$.ajax({
		headers : {
			'X-CSRF-Token': csrf
		},
		url     : '<?=$this->Url->build("/gallery/album_save")?>',
		method  : 'post',
		data    : dataForm,
		success : function(data){
			if(data){
				bootbox.alert("&Aacute;lbum criado com sucesso!");
			}else{
				bootbox.alert("Ocorreu um erro ao tentar criar o &Aacute;lbum!");
			}
			$("#modalNewAlbum").modal("hide");
			getAlbuns();
		}
	});
});

$(document).on("submit","#frmRenameImage",function(event){
	event.preventDefault();
	$.ajax({
		headers: {
			'X-CSRF-Token' : csrf
		},
		url    : '<?=$this->Url->build("/gallery/image_rename")?>',
		data   : { IDIMAGEM: $("#txtIdImageNewName").val(), NOME : $("#txtImageNewName").val() },
		method : 'post',		
		success: function(data){
			if(data){
				bootbox.alert("Imagem renomeada com sucesso!",function(){ getAlbuns() });
			}else{
				bootbox.alert("Ocorreu um erro ao tentar renomear a imagem!");
			}
			
			$("#modalRename").modal("hide");
		}
	});
});

$(document).on("submit","#frmUploadAndSave",function(event){
	event.preventDefault();
	
	$.ajax({
		headers : {
			'X-CSRF-Token' : csrf
		},
        method  : 'POST',
        url     : '<?=$this->Url->build("/gallery/upload_and_save")?>',
        data    : new FormData(this),
        cache   : false,
        contentType : false,
        processData : false,
        success: function(data){
            if(data==true){
                bootbox.alert("<p class='text-center'>Imagem enviada com sucesso!</p>");
            }else{
                bootbox.alert("<p class='text-center'>Ocorreu um erro ao tentar enviar e salvar a imagem!</p>");
            }
            $("#modalUpload").modal("hide");
			getAlbuns();
        }
    });
});

$(document).on("hide.bs.modal","#modalUpload",function(){
	if(drEvent.length!=undefined){
		drEvent = drEvent.data('dropify');
		drEvent.resetPreview();
	}
	$("#frmUploadAndSave").removeClass("was-validated");
});
</script>