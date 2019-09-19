<div class='col-sm'>
<label class='text-danger'>Total aberto no m&ecirc;s corrente: </label>
<strong><?=$this->Number->currency($total_month->TOTAL,"BRL");?></strong>
</div>
<div class='col-sm'>
<label class='text-danger'>Total aberto na semana atual:</label> 
<strong><?=$this->Number->currency($total_week->TOTAL,"BRL");?></strong>
</div>
<div class='col-sm text-right'>
<label class='text-danger'>Total aberto hoje:</label> 
<strong><?=$this->Number->currency($total_today->TOTAL,"BRL");?></strong>
</div>