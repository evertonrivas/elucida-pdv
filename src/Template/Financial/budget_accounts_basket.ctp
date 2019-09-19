<table class='table' style='font-size:12px!important'>
    <thead>
        <tr>
            <th>Opera&ccedil;&atilde;o Fin.</th>
            <th>Jan</th>
            <th>Fev</th>
            <th>Mar</th>
            <th>Abr</th>
            <th>Mai</th>
            <th>Jun</th>
            <th>Jul</th>
            <th>Ago</th>
            <th>Set</th>
            <th>Out</th>
            <th>Nov</th>
            <th>Dez</th>
        </tr>
    </thead>
    <tbody>
        <?php if(count($itens)>0):
            for($i=0;$i<count($itens);$i++): ?>
        <tr>
            <td><input type='hidden' id='txtIdOperacaoFinanceira[]' name='txtIdOperacaoFinanceira[]' value='<?=$itens[$i]['IDOPERACAOFINANCEIRA'];?>'><span class="<?php if($itens[$i]['TIPO_OPERACAO']=="S"){ echo "text-danger"; }else{ echo "text-success"; }?>"><?=$itens[$i]['NOME']?></span></td>
            <td><input type='text' id='txtValJan[]' name='txtValJan[]' class='form-control' style='font-size:10px!important' value='<?php echo $itens[$i]['JAN']; ?>'></td>
            <td><input type='text' id='txtValFev[]' name='txtValFev[]' class='form-control' style='font-size:10px!important' value='<?php echo $itens[$i]['FEV']; ?>'></td>
            <td><input type='text' id='txtValMar[]' name='txtValMar[]' class='form-control' style='font-size:10px!important' value='<?php echo $itens[$i]['MAR']; ?>'></td>
            <td><input type='text' id='txtValAbr[]' name='txtValAbr[]' class='form-control' style='font-size:10px!important' value='<?php echo $itens[$i]['ABR']; ?>'></td>
            <td><input type='text' id='txtValMai[]' name='txtValMai[]' class='form-control' style='font-size:10px!important' value='<?php echo $itens[$i]['MAI']; ?>'></td>
            <td><input type='text' id='txtValJun[]' name='txtValJun[]' class='form-control' style='font-size:10px!important' value='<?php echo $itens[$i]['JUN']; ?>'></td>
            <td><input type='text' id='txtValJul[]' name='txtValJul[]' class='form-control' style='font-size:10px!important' value='<?php echo $itens[$i]['JUL']; ?>'></td>
            <td><input type='text' id='txtValAgo[]' name='txtValAgo[]' class='form-control' style='font-size:10px!important' value='<?php echo $itens[$i]['AGO']; ?>'></td>
            <td><input type='text' id='txtValSet[]' name='txtValSet[]' class='form-control' style='font-size:10px!important' value='<?php echo $itens[$i]['SET']; ?>'></td>
            <td><input type='text' id='txtValOut[]' name='txtValOut[]' class='form-control' style='font-size:10px!important' value='<?php echo $itens[$i]['OUT']; ?>'></td>
            <td><input type='text' id='txtValNov[]' name='txtValNov[]' class='form-control' style='font-size:10px!important' value='<?php echo $itens[$i]['NOV']; ?>'></td>
            <td><input type='text' id='txtValDez[]' name='txtValDez[]' class='form-control' style='font-size:10px!important' value='<?php echo $itens[$i]['DEZ']; ?>'></td>
        </tr>
        <?php endfor; ?>
        <?php endif; ?>
</tbody>
</table>