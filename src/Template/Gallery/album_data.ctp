<?php if($data_list->count()>0): $i = 0; ?>
	<?php foreach($data_list as $data): ?>
		<?php if($i==0): ?>
			<div class='row'>
		<?php endif; ?>
				<div class='col-sm-<?php switch($colunas){ case 2: echo 6; break; case 3: echo 4; break; case 4: echo 3; break; case 6: echo 2; break; }?>'>
					<div class='card border-secondary' style="min-height:360px!important;">
						<div style="padding:6px!important;"><img src="/img/gallery/<?=$data->IDALBUM?>/<?=$data->ARQUIVO;?>" class="card-img-top" alt="" style="max-height:170px!important;"></div>
						<div class="card-body" style="valign:bottom!important;">
							<strong><?=$data->NOME;?></strong><br/>
							<small><?=$data->CRIADO_EM->format("d/m/Y H:i:s")?></small><br/>
							<small><?=$this->Number->precision($data->TAMANHO,2)?> Kb</small><br/>
						</div>
						<div class="card-footer text-right">
							<a href="javascript:dropImage(<?=$data->IDIMAGEM; ?>)" class="btn btn-outline-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Remover"><i class="fas fa-eraser"></i></a>
							<a href="javascript:renameImage(<?=$data->IDIMAGEM; ?>,'<?=$data->NOME;?>')" class="btn btn-outline-info btn-sm" data-toggle="tooltip" data-placement="top" title="Renomear"><i class="fas fa-paragraph"></i></a>
							<a href="javascript:selectImage(<?=$data->IDIMAGEM; ?>)" class="btn btn-outline-success btn-sm" data-toggle="tooltip" data-placement="top" title="Usar Imagem"><i class="fas fa-check"></i></a>
						</div>
					</div>
				</div>
		<?php if($i==$colunas-1): $i = 0; ?>
			</div><br/>
		<?php else: $i++; endif;  ?>
	<?php endforeach; ?>
<?php else: ?>
<div class='text-center'>Nenhuma imagem encontrada!</div>
<?php endif;?>