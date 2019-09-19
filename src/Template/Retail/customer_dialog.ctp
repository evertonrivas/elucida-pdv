<?php if($data_list->count()>0): ?>
<?php foreach($data_list as $data): ?>
    <tr>
        <td class='text-center first-column'><input type='radio' id='rdCliente[]' name='rdCliente[]' value='<?=$data->IDCLIENTE;?>'></td>
        <td><?=(($data->CPF!="")?$this->Mask->apply($data->CPF,"###.###.###-##"):"");?></td>
        <td><?=$data->NOME;?></td>
        <td><?=$data->TELEFONE;?></td>
        <td>&nbsp;</td>
    </tr>
<?php endforeach;?>
<?php else: ?>
    <tr>
        <td colspan='5' class='text-center'>Nenhum cliente encontrado, por favor realize o cadastro!</td>
    </tr>
<?php endif; ?>
