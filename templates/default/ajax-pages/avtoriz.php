<?
if($_GET['fk']) $this->avtform_fk = $_GET['fk'];
else $this->avtform_fk = 0;
if(isset($_GET['logoff'])) unset($_SESSION['user']);
echo $this->core_modul('blank', 'avtoriz');

if($_SESSION['user']['id'] && $_SESSION['user']['group']['id']==3)
{
	$_RESULT['avtorized'] = 1;
	$_RESULT['fk'] = $_GET['fk'];
}
?>