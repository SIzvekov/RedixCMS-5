<?php
/*
$name - ��� ����
$text - ����� � ���������
$genid - �������� ��������������� �����
$h - ������ ���������
$w - ������ ���������
$wys_type - ��� ������ ���������
*/
?>
<textarea id='txt-<?=$genid?>' style='display:none'><?=$text?></textarea>
<script type='text/javascript'>
<!--
var oFCKeditor = new FCKeditor( '<?=$name?>' ) ;
oFCKeditor.BasePath	= 'http://<?=HTTP_HOST?>/editor/fckeditor/' ;
oFCKeditor.Height = '<?=$h?>' ;
oFCKeditor.Width = '<?=$w?>' ;
oFCKeditor.Value	= document.getElementById('txt-<?=$genid?>').value;
oFCKeditor.ToolbarSet = '<?=$wys_type?>' ;
oFCKeditor.Create() ;
//-->
</script>
