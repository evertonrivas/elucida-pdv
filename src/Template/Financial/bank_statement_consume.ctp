<?php $time = new Cake\I18n\Time();?>
<form id="frmRegs" name="frmRegs">
<div class="card">
    <div class="card-header">
    	<div class="row">
    		<div class="col-sm">
    			<i class="fas fa-angle-right"></i> Processar importa&ccedil;&atilde;o de Extrato Banc&aacute;rio
    		</div>
    		<div class="col-sm">
    			<button type="submit" id="btnSave" name="btnSave" class="btn btn-primary btn-sm"><i class="fas fa-hdd"></i> Salvar</button>
    		</div>
    	</div>
    </div>
    <div class="card-body">
        <table class="table" style="font-size:12px!important">
            <thead>
                <tr>
                    <th>Data do Movimento</th>
                    <th>Documento</th>
                    <th>Hist&oacute;rico</th>
                    <th>Valor</th>
                    <th>Opera&ccedil;&atilde;o Financeira</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows as $row):?>
                <?php if($row->OPERATION=="D"): ?>
                <?php if(strpos($row->HISTORICO,"PACOTE")!==false ||
                        strpos($row->HISTORICO,"IOF")!==false ||
                        strpos($row->HISTORICO,"ADIANT")!==false ||
                        strpos($row->HISTORICO,"MORA")!==false || 
                        strpos($row->HISTORICO,"JUROS")!==false ||
                        strpos($row->HISTORICO,"ENCARGO") !==false ||
                        strpos($row->HISTORICO,"TARIFA") !==false ||
                        $row->HISTORICO=="DOC"){ $red_open = "<font color='#FF0000'>"; $red_close = "</font>"; }else{ $red_open="";$red_close=""; } ?>
                <?php $data = new \Datetime($row->DATA_MOVIMENTO);?>
                <tr>
                    <td><?php echo $red_open.$data->format("d/m/Y").$red_close; ?><input type="hidden" id="data_movimento[]" name="data_movimento[]" value="<?php echo $row->DATA_MOVIMENTO; ?>"></td>
                    <td><?php echo $red_open.$row->NUM_DOCUMENTO.$red_close; ?><input type="hidden" name="num_document[]" id="num_documento[]" value="<?php echo $row->NUM_DOCUMENTO; ?>"></td>
                    <td><?php echo $red_open.$row->HISTORICO.$red_close; ?><input type="hidden" name="historico[]" id="historico[]" value="<?php echo $row->HISTORICO; ?>"></td>
                    <td class="text-right"><?php echo $red_open.$this->Number->currency($row->VALOR,"BRL").$red_close; ?><input type="hidden" name="valor[]" id="valor[]" value="<?php echo $row->VALOR; ?>"></td>
                    <td>
                        <select name="cb_tipo_despesa[]" id="cb_tipo_despesa[]" class="form-control" style="font-size:12px!important">
                            <option value="">&laquo; Selecione &raquo;</option>
                            <?php foreach($despesaslist as $despesa):?>
                            <option value="<?php echo $despesa->IDTIPODESPESA; ?>"><?php echo $despesa->NOME; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</form><br/>
<script>

$(document).on("submit","#frmRegs",function(event){
	event.preventDefault();
	
    $.ajax({
    	headers:{
			'X-CSRF-Token':csrf
		},
        method: 'post',
        url: '<?=$this->Url->build("/financial/bank_statement_process")?>',
        data: $(this).serialize(),
        success: function(data){
            if(data){
            	bootbox.alert("Importa&ccedil;&atilde;o de extrato banc&aacute;rio realizada com sucesso!",function(){ document.location.href='<?=$this->Url->build("/financial/bank_statement_import")?>'; });
            }else{
                bootbox.alert("Ocorreu um problema ao tentar importar o Extrato banc&aacute;rio!");
            }
        }
    });
});
</script>