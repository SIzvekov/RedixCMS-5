<?
/*
	$curpage - текущая страница
	$colpage - число страниц
	$link    - ссылка содержащая [pagenum], который надо заменить на номер станицы
*/
?>
<div align=left>
<table border=0 class="navigation_bar">
	<tr>
		<TD>
			<strong><?=$this->core_echomui('adm_page')?> <?=$curpage?> <?=$this->core_echomui('adm_pagefrom')?> <?=$colpage?></strong>
		</TD>
<?
($curpage-4)>0 ? $from = $curpage-4 : $from=1;
($curpage+4)<=$colpage ? $to = $curpage+4 : $to=$colpage;

if($from!=1)
{?>
	<TD>
		&nbsp;<A title="<?=$this->core_echomui('adm_tomainpage')?>" href="<?echo str_replace("[pagenum]",1,$link)?>" id="linkblack">&lt;&lt;&lt;</A>&nbsp;
	</TD>
<?}

if($curpage>1){
?>
	<TD>
		&nbsp;<A title="<?=$this->core_echomui('adm_previouspage')?>" href="<?echo str_replace("[pagenum]",$curpage-1,$link)?>" id="linkblack">&lt;&lt;</A>&nbsp;
	</TD>
<?
}
for($i=$from;$i<=$to;$i++){
if($curpage==$i){?>
		<TD class="cpage">
			<STRONG>&nbsp;<?=$i?>&nbsp;</STRONG>
		</TD>
<?}else{?>
        <TD>
			&nbsp;<A title="<?=$this->core_echomui('adm_topage')?> <?=$i?> <?=$this->core_echomui('adm_page1')?>" href="<?echo str_replace("[pagenum]",$i,$link)?>" style="font-size:<?=$fontsize?>"><?=$i?></A>&nbsp;
		</TD>
<?}?>
<?}//for($i=$from;$i<=$to;$i++)
if($curpage<$colpage){
?>
	<TD>
		&nbsp;<A title="<?=$this->core_echomui('adm_nextpage')?>" href="<?echo str_replace("[pagenum]",$curpage+1,$link)?>" style="font-size:<?=$fontsize?>" id="linkblack">&gt;&gt;</A>&nbsp;
	</TD>
<?}
if($to!=$colpage)
{?>
	<TD>
		&nbsp;<A title="<?=$this->core_echomui('adm_tolastpage')?>" href="<?echo str_replace("[pagenum]",$colpage,$link)?>" style="font-size:<?=$fontsize?>" id="linkblack">&gt;&gt;&gt;</A>&nbsp;
	</TD>
<?}?>
	</TR>
</TABLE>
</div>