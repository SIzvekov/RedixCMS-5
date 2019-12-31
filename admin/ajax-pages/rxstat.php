<?
if($_SESSION['rxstatistic']['nocache']!=$_GET['nocache']) die('session expired');
//print_r($_SESSION['rxstatistic']);
echo "<b>".$this->core_echomui('rxstat_part_title_'.$_GET['part'])."</b><br/>";
switch($_GET['part'])
{
	case 'muifiles':
		if(!sizeof($_SESSION['rxstatistic']['muifilesloaded'])) echo $this->core_echomui('rxstat_no');
		foreach($_SESSION['rxstatistic']['muifilesloaded'] as $file)
		{
			$usefile = str_replace(DOCUMENT_ROOT,"",$file);
			echo "<div class='rxstatsubitem' onmouseover=\"this.className='rxstatsubitem_h'\" onmouseout=\"this.className='rxstatsubitem'\">".++$i.". <a href='http://".HTTP_HOST.$usefile."' target='_blank'>".$usefile."</a></div>";
		}
	break;
	case 'muifiles_error':
		if(!sizeof($_SESSION['rxstatistic']['muifilesloaded_error'])) echo $this->core_echomui('rxstat_no');
		foreach($_SESSION['rxstatistic']['muifilesloaded_error'] as $file)
		{
			$usefile = str_replace(DOCUMENT_ROOT,"",$file);
			$i++;
			echo "<div class='rxstatsubitem' onmouseover=\"document.getElementById('createfile-".$i."').style.display='block';this.className='rxstatsubitem_h'\" onmouseout=\"document.getElementById('createfile-".$i."').style.display='none';this.className='rxstatsubitem'\">".$i.". ".$usefile."<br/><a href='' style='font-size:10px;display:none' id='createfile-".$i."'>".$this->core_echomui('rxstat_createmuifile')."</a></div>";
		}
	break;
	case 'mui':
		if(!sizeof($_SESSION['rxstatistic']['mui'])) echo $this->core_echomui('rxstat_no');
		foreach($_SESSION['rxstatistic']['mui'] as $code=>$val)
		{
			echo "<div class='rxstatsubitem' onmouseover=\"this.className='rxstatsubitem_h'\" onmouseout=\"this.className='rxstatsubitem'\">".++$i.". ".$code." => ".$val."</div>";
		}		
	break;
	case 'muierror':
		if(!sizeof($_SESSION['rxstatistic']['muierror'])) echo $this->core_echomui('rxstat_no');
		foreach($_SESSION['rxstatistic']['muierror'] as $code)
		{
			echo "<div class='rxstatsubitem' onmouseover=\"this.className='rxstatsubitem_h'\" onmouseout=\"this.className='rxstatsubitem'\">".++$i.". ".$code."</div>";
		}		
	break;
	case 'muiblank':
		if(!sizeof($_SESSION['rxstatistic']['muiblank'])) echo $this->core_echomui('rxstat_no');
		foreach($_SESSION['rxstatistic']['muiblank'] as $code)
		{
			echo "<div class='rxstatsubitem' onmouseover=\"this.className='rxstatsubitem_h'\" onmouseout=\"this.className='rxstatsubitem'\">".++$i.". ".$code."</div>";
		}		
	break;
	case 'params_list':
		if(!sizeof($_SESSION['rxstatistic']['params_list'])) echo $this->core_echomui('rxstat_no');
		foreach($_SESSION['rxstatistic']['params_list'] as $item)
		{
			echo "<div class='rxstatsubitem' onmouseover=\"this.className='rxstatsubitem_h'\" onmouseout=\"this.className='rxstatsubitem'\">".++$i.". ".$item['par']." [".$item['par_name']."], prefix: ".$item['db_prefix'].($item['default']?' ('.$this->core_echomui('rxstat_params_list_def').')':'')."</div>";
		}		
	break;
	case 'db_sql':
		if(!sizeof($_SESSION['rxstatistic']['db_sql'])) echo $this->core_echomui('rxstat_no');
		foreach($_SESSION['rxstatistic']['db_sql'] as $code)
		{
			echo "<div class='rxstatsubitem' onmouseover=\"this.className='rxstatsubitem_h'\" onmouseout=\"this.className='rxstatsubitem'\">".++$i.". ".$code."</div>";
		}		
	break;
	case 'core_error':
		if(!sizeof($_SESSION['rxstatistic']['core_error'])) echo $this->core_echomui('rxstat_no');
		foreach($_SESSION['rxstatistic']['core_error'] as $code)
		{
			echo "<div class='rxstatsubitem' onmouseover=\"this.className='rxstatsubitem_h'\" onmouseout=\"this.className='rxstatsubitem'\">".++$i.". ".$code."</div>";
		}		
	break;
	case 'db_errors':
		if(!sizeof($_SESSION['rxstatistic']['db_errors'])) echo $this->core_echomui('rxstat_no');
		foreach($_SESSION['rxstatistic']['db_errors'] as $code)
		{
			echo "<div class='rxstatsubitem' onmouseover=\"this.className='rxstatsubitem_h'\" onmouseout=\"this.className='rxstatsubitem'\">".++$i.". ".$code."</div>";
		}		
	break;
	
	default:
		echo "wrong part '".$_GET['part']."'";
}
?>