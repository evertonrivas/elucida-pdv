<?php $time = new Cake\I18n\Time();?>
<div class='row'>
    <div class='col'>
        <label class='control-label'>Data de Abertura</label>
        <p class='form-control-static'><?php if(isset($caixa)){ echo $time->parseDateTime($caixa->DATA_ABERTURA)->i18nFormat("dd/MM/yyyy HH:mm:ss"); }else{ echo "00/00/0000 00:00:00"; }?></p>
    </div>
    <div class='col'>
        <label class='control-label'>Data de Fechamento</label>
        <p class='form-control-static'><?php if(isset($caixa)){ echo $time->parseDateTime($caixa->DATA_FECHAMENTO)->i18nFormat("dd/MM/yyyy HH:mm:ss"); }else{ echo "00/00/0000 00:00:00"; }?></p>
    </div>
</div>
<div class='row'>
    <div class='col'>
        <label class='control-label'>Valor de Abertura</label>
        <p class='form-control-static'><?php if(isset($caixa)){ echo $this->Number->currency($caixa->VALOR_ABERTURA,"BRL");}else{ echo $this->Number->currency(0,"BRL"); }?></p>
    </div>
    <div class='col'>
        <label class='control-label'>Valor de Fechamento</label>
        <p class='form-control-static'><?php if(isset($caixa)){ echo $this->Number->currency($caixa->VALOR_FECHAMENTO); }else{ echo $this->Number->currency(0,"BRL"); }?></p>
    </div>
</div>
<fieldset>
    <legend>Retirada(s)</legend>
    <table class='table'>
        <thead>
            <tr>
                <th>#</th>
                <th>Valor</th>
                <th>Observa&ccedil;&atilde;o</th>
            </tr>
        </thead>
        <tbody>
        <?php if(isset($caixa_retiradas)):
            foreach($caixa_retiradas as $sangria): ?>
            <tr>
                <td><?=$sangria->IDCAIXASANGRIA;?></td>
                <td><?=$this->Number->currency($sangria->VALOR,"BRL");?></td>
                <td><?=$sangria->OBSERVACAO;?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif;?>
        </tbody>
    </table>
</fieldset>
        
<fieldset>
    <legend>Debito(s)</legend>
        <table class='table'>
        <thead>
            <tr>
                <th>Meio Pagamento</th>
                <th>Valor Apurado</th>
                <th>Valor Informado</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($caixa_debitos)):
                foreach($caixa_debitos as $debito): ?>
            <tr>
                <td><?=$debito->MEIO_PAGAMENTO;?></td>
                <td><?=$this->Number->currency($debito->VALOR_APURADO,"BRL");?></td>
                <td><?=$this->Number->currency($debito->VALOR_INFORMADO,"BRL");?></td>
            </tr>
            <?php endforeach;
            endif; ?>
        </tbody>
    </table>
</fieldset>
