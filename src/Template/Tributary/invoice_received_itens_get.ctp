<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>C&oacute;d. Produto</th>
            <th>Produto</th>
            <th>NCM</th>
            <th>Qtde</th>
            <th>Pre&ccedil;o UN.</th>
            <th>V&iacute;nculo</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($itens as $item): ?>
        <tr>
            <td><?=$item->IDITEM;?></td>
            <td><?=$item->COD_PRODUTO;?></td>
            <td><?=$item->NOME_PRODUTO;?></td>
            <td><?=$item->NCM;?></td>
            <td><?=$item->QUANTIDADE_COMERCIAL;?></td>
            <td><?=$this->Number->currency($item->VALOR_UNITARIO,"BRL");?></td>
            <td><?php switch($item->TIPO_VINCULO){ 
                case 'P': echo "<span class='label label-success'>Permanente</span>"; break;
                case 'T': echo "<span class='label label-warning'>Tempor&aacute;rio</span>"; break;
                default: echo "<a href='javascript:addTmpLink(\"".$item->COD_PRODUTO."\",\"".$item->NOME_PRODUTO."\")'>Add Temp</a>&nbsp;&nbsp;<a href='javascript:addPermLink(\"".$item->COD_PRODUTO."\",\"".$item->NOME_PRODUTO."\")'>Add Perm.</a>";
            }?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>