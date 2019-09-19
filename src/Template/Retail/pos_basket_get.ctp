<table class="table table-striped" style="font-size:12px!important">
    <thead>
       <tr>
            <th>Produto</th>
            <!--<th>R$ UN.</th>-->
            <th>Qtde</th>
            <th>Fidel.</th>
            <th>Subtotal</th>
            <th>#</th>
       </tr>
    </thead>
    <tbody>
        <?php if($data_list): ?>
        <?php foreach($data_list as $data): ?>
        <tr>
            <td><?=$data->NOME_PRODUTO;?></td>
            <!--<td><?=$this->Number->currency($data->PRECO_UN,"BRL");?></td>-->
            <td><?=$data->QUANTIDADE;?></td>
            <td><?=$this->Number->currency($data->DESCONTO,"BRL");?></td>
            <td><?=$this->Number->currency($data->SUBTOTAL,"BRL");?></td>
            <td><a href="#" onclick="productDel('<?=$data->IDPRODUTO;?>','<?=$data->NOME_PRODUTO?>');"><span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span></a></td>
        </tr>
        <?php endforeach;?>
        <?php endif;?>
    </tbody>
</table>
<?php if($total_data>0): ?>
<script>
unlockScreen();
</script>
<?php endif;?>