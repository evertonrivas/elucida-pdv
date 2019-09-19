<?php if($data_list->count()>0):?>
<?php foreach($data_list as $data): ?>
<tr>
    <td><?php echo $data->IDFUNCIONARIO; ?></td>
    <td><?php echo $data->CPF; ?></td>
    <td><?php echo $data->NOME; ?></td>
    <td><?php echo $data->TELEFONE;?></td>
    <td><input type="radio" id="rdEmployer[]" name="rdEmployer[]" value="<?php echo $data->IDFUNCIONARIO; ?>"></td>
</tr>
<?php endforeach;
else:?>
<tr><td colspan="4"></td></tr>
<?php endif;
