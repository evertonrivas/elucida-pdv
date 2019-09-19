<div class="form-group">
    <div class="row">
        <div class="col">
            <label>Descri&ccedil;&atilde;o</label>
            <input class="form-control-plaintext" type="text" value="<?php echo $cupom->DESCRICAO; ?>">
        </div>
        <div class="col">
            <label class="control-label">Tipo de Cupom</label>
            <input type="text" class="form-control-plaintext" value="<?php switch($cupom->TIPO_CUPOM){
                case 'D': echo "Desconto"; break;
                case 'A': echo "Pedido de Compra"; break;
                case 'P': echo "Vale Presente"; break;
            }
?>">
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <div class="col">
            <label class="control-label">Valor</label>
            <p class="form-control-static"><?php if($cupom->TIPO_VALOR=="$"){ echo $this->Number->currency($cupom->VALOR,"BRL"); }else{ echo $cupom->VALOR."%"; }?></p>
        </div>
        <div class="col">
            <label class="control-label">Data de validade (Opcional)</label>
            <p class="form-control-static"><?php if($cupom->DATA_VALIDADE!=NULL){ echo $cupom->DATA_VALIDADE->format("d/m/Y"); }?></p>
        </div>
        <div class="col">
            <label class="control-label">C&oacute;digo</label>
            <p class="form-control-static"><?php echo $cupom->CODIGO;?></p>
        </div>
        
    </div>
</div>
<div class="form-group">
    <label class="control-label">Observa&ccedil;&atilde;o</label>
    <input type="text" class="form-control-plaintext" value="<?php echo $cupom->OBSERVACAO; ?>">
</div>