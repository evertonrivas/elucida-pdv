<table class="table table-striped" style="font-size:12px!important">
    <thead>
       <tr>
            <th>Produto</th>
            <th>Pre&ccedil;o UN.</th>
            <th>Qtde</th>
            <th>#</th>
       </tr>
    </thead>
    <tbody>
    <?php foreach($data_list as $itemTroca): ?>
        <tr>
            <td><?=$itemTroca->NOME_PRODUTO;?></td>				
            <td><?=$this->Number->currency($itemTroca->PRECO_UNITARIO,"BRL");?></td>
            <td><?=$itemTroca->QUANTIDADE;?></td>
            <td><a href="#" onclick="dropItem('<?=$itemTroca->IDPRODUTO;?>','<?=$itemTroca->NOME_PRODUTO;?>');"><span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span></a></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>