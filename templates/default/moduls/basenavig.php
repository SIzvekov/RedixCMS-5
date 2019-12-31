<?
//echo NAVIG_CUR_PAGE; - current page
//echo NAVIG_COLONPAGE; - number records on the page
//echo NAVIG_COLALL; - how many all records
//echo NAVIG_COLPAGE; - how many all pages

if(sizeof($this->navigation[$navig_code]) && is_array($this->navigation[$navig_code]))
{
 $NAVIG_CUR_PAGE = $this->navigation[$navig_code]['NAVIG_CUR_PAGE'];
 $NAVIG_COLONPAGE = $this->navigation[$navig_code]['NAVIG_COLONPAGE'];
 $NAVIG_COLALL = $this->navigation[$navig_code]['NAVIG_COLALL'];
 $NAVIG_COLPAGE = $this->navigation[$navig_code]['NAVIG_COLPAGE'];
}else{
 $NAVIG_CUR_PAGE = NAVIG_CUR_PAGE;
 $NAVIG_COLONPAGE = NAVIG_COLONPAGE;
 $NAVIG_COLALL = NAVIG_COLALL;
 $NAVIG_COLPAGE = NAVIG_COLPAGE;
}
if($NAVIG_COLPAGE>1){

$start = ($NAVIG_CUR_PAGE-1);
$end = ($NAVIG_CUR_PAGE+1);

if($start<1) {$start = 1;$end=3;}
if($end>$NAVIG_COLPAGE){$end =$NAVIG_COLPAGE;if($end>2) $start = ($end-2);}
?>
<div class="pagenavig">
 <div class="pagelinks">
  <?if($start!=1){?><a href="?page=1">1</a>&nbsp;<?}?>
  <?if($start!=1 && ($start-1)!=1){?>...&nbsp;<?}?>
  <?for($i=$start;$i<=$end;$i++){?><a href="?page=<?=$i?>"><?echo $NAVIG_CUR_PAGE==$i?'<span style="font-weight: bold;">'.$i.'</span>':$i?></a>&nbsp;
 <?}?>
  <?if($end!=$NAVIG_COLPAGE && ($end+1)!=$NAVIG_COLPAGE){?>...&nbsp;<?}?>
  <?if($end!=$NAVIG_COLPAGE){?><a href="?page=<?=$NAVIG_COLPAGE?>"><?=$NAVIG_COLPAGE?></a>&nbsp;<?}?>
 </div>
</div>
<?}?>