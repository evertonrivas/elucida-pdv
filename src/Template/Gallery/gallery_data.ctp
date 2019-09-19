<div class="card"><div class="card-body">
<?php if($album_list->count()>0): ?>
	<div class='row'>
		<ul class="col container-filter mb-0" id="filter">
		<li><a class="btn btn-outline-dark active" data-filter="*">Todos</a></li>
	<?php foreach($album_list as $data): ?>
		<li><a class="btn btn-outline-dark" data-filter=".<?=str_replace(" ","_",(mb_strtolower($data->NOME)));?>"><?=$data->NOME?>(<?=$data->TOTAL;?>)</a></li>
	<?php endforeach; ?>
		<li><span data-target="#modalNewAlbum" data-toggle="modal" data-backdrop="static">
				<a class="btn btn-success" data-toggle="tooltip" href="#" title="Novo Album"><i class="fas fa-folder-plus text-white"></i></a>
			</span>
		</li>
	</ul></div>
<?php else: ?>
	<div class="text-center">Nenhum &aacute;lbum encontrado!</div>
<?php endif;?>
</div></div><br/>
<div class="card">
	<div class="card-body">
		<div class="row">
			<div class="col form-group text-right">
				<button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#modalUpload" data-backdrop="static"><i class="fas fa-cloud-upload-alt"></i> Enviar Imagem</button>
			</div>
		</div>
		<?php if($image_list->count()>0):?>
		<div class="row container-grid nf-col-3 projects-wrapper" id="">
			<?php foreach($image_list as $image): ?>
			<div class="col-lg-4 col-md-6 p-0 nf-item <?=str_replace(" ","_",(mb_strtolower($image->ALBUM)));?>">
				<div class="item-box">
                    <img class="item-container " src="/img/gallery/<?=$image->IDALBUM?>/<?=$image->ARQUIVO;?>" alt="7" />
                    <div class="item-mask">
                        <div class="item-caption">
                            <h5 class="text-white"><?=$image->NOME;?></h5>
                            <p class="text-white"><?=$this->Number->precision($image->TAMANHO,2)?> Kb</p>
                            <p>
                            	<a href="javascript:dropImage(<?=$image->IDIMAGEM; ?>)" data-toggle="tooltip" class="btn btn-sm btn-outline-light" data-placement="top" title="Remover"><i class="fas fa-eraser"></i></a>
								<a href="javascript:renameImage(<?=$image->IDIMAGEM; ?>,'<?=$image->NOME;?>')" class="btn btn-sm btn-outline-light" data-toggle="tooltip" data-placement="top" title="Renomear"><i class="fas fa-paragraph"></i></a>
								<?php if($default_layout==""): ?>
								<a href="javascript:selectImage(<?=$image->IDIMAGEM; ?>)" data-toggle="tooltip" class="btn btn-sm btn-outline-light" data-placement="top" title="Usar Imagem"><i class="fas fa-check"></i></a>
								<?php endif; ?>
                            </p>
                        </div>
                    </div>
				</div>
			</div>
			<?php endforeach;?>
		</div>
		<?php endif; ?>
	</div>
</div>