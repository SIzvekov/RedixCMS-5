<?php //v.1.0.
/* RedixCMS 4.0*/
if($this->id)
{
	$sql = "SELECT * FROM `#__sitemap` WHERE `id`=".$this->id;
	$res = $this->query($sql);
	$page = $this->fetch_assoc($res);

	$_GET['com'] = $page['com_id'];
	$ACTION = 'edit';
}
if(isset($_GET['add']))
{
	$ACTION = 'add';
}

$com = intval($_GET['com'])?intval($_GET['com']):intval($_POST['pi-com_id']);

$sql = "SELECT * FROM `#h_components` WHERE `id`=".$com;
$row = $this->fetch_assoc($this->query($sql));

if(!$row['enabled'] && $ACTION == 'add') {
	$this->reload('/'.$this->adm_path.'/');
}
if($row['tpl_file'])
{
	$sql = "SELECT COUNT(`id`) as `num` FROM `#__sitemap` WHERE `tplfile`='".$row['tpl_file']."'";
	$added_num = $this->fetch_assoc($this->query($sql));
	$added_num = $added_num['num'];
}

if($ACTION == 'edit' && !isset($_POST['pi-id']))
{
	$_POST['pititle'] = $page['title'];
	$piurl = split('/',$page['url']);
	$_POST['piurl'] = end($piurl);
	$_POST['pi-pid'] = $page['pid'];
	$_POST['pi-id'] = $page['id'];
	$_POST['pi-meta_title'] = $page['meta_title'];
	$_POST['pi-meta_keywords'] = $page['meta_keywords'];
	$_POST['pi-meta_description'] = $page['meta_description'];
	$_POST['pi-include_in_pathway'] = $page['include_in_pathway'];
	$_POST['pi-pathway'] = $page['pathway'];
//	$_POST['pi-'] = $page[''];
	$_POST['record_id'] = $page['record_id'];

	$sql = "SELECT * FROM `#__menupunkti` WHERE `page_id`=".intval($_POST['pi-id']);
	$menu_row = $this->fetch_assoc($this->query($sql));
	$_POST['pi-menu-id'] = $menu_row['id'];
	$_POST['pi-menu'] = $menu_row['mid'];
	$_POST['pi-parpunktmenu'] = $menu_row['pid'];
	$_POST['pi-linktext'] = $menu_row['name'];
	$_POST['pi-linksort'] = $menu_row['sort'];
	$_POST['pi-openlinkin'] = $menu_row['target'];
}


if($row['add_button']=='razdel')
	if($row['tpl_file']=='') $thispagetype = 1;
	else $thispagetype = 2;
elseif($row['add_button']=='content')
	if($row['tpl_file']=='') $thispagetype = 4;
	else $thispagetype = 3;


if($_POST['save'] || $_POST['app']){
	$get_url = split('/',$_POST['piurl']);
	$get_url = end($get_url);
	$sql = "SELECT `url` FROM `#__sitemap` WHERE `id`=".intval($_POST['pi-pid']);
	$pidurl = $this->fetch_assoc($this->query($sql));
	$get_url = $pidurl['url']."/".$get_url;
	$get_url = trim($get_url,'/');

	if($ACTION=='edit') $dopcond = '`id`!='.$this->id;
	else $dopcond = '1';

	$sql = "SELECT `id` FROM `#__sitemap` WHERE `url`='".addslashes($get_url)."' && ".$dopcond;
	$col = $this->num_rows($this->query($sql));
	if($col && $row['tpl_file'])
	{
		$error = 1;
		$this->adm_add_sys_mes($this->core_echomui('admc_error_duplicateurl'),"err");
	}
	if($row['tpl_file'] && !$get_url)
	{
		$error = 1;
		$this->adm_add_sys_mes($this->core_echomui('admc_error_nourl'),"err");
	}
	if(isset($_POST['pititle']) && !$_POST['pititle'])
	{
		$error = 1;
		$this->adm_add_sys_mes($this->core_echomui('admc_error_notitle'),"err");
	}

	if(!$_POST['pi-pid'] && !($row['parent_parts']=='root' || !$row['parent_parts']))
	{
		$error = 1;
		$this->adm_add_sys_mes($this->core_echomui('admc_error_noparent'),"err");
	}
	
	if(!$error) // if no errors, add or edit record	
	{

		$sitemapsql = "`#__sitemap` SET	
			`pid`=".intval($_POST['pi-pid']).",
			`url`='".addslashes($get_url)."',
			`title`='".addslashes($_POST['pititle'])."',
			`component`='".addslashes($row['use_component'])."',
			`template`='".addslashes($row['title'])."',
			`tplfile`='".addslashes($row['tpl_file'])."',
			`meta_title`='".addslashes($_POST['pi-meta_title'])."',
			`meta_keywords`='".addslashes($_POST['pi-meta_keywords'])."',
			`meta_description`='".addslashes($_POST['pi-meta_description'])."',
			`include_in_pathway`='".intval($_POST['pi-include_in_pathway'])."',
			`pathway`='".addslashes($_POST['pi-pathway'])."',
			`getsubpages`='".(intval($row['get_subpages'])?1:0)."',
			`getsubpages_deep`=".intval($row['get_subpages']).",
			`com_id`=".intval($row['id']);
		
		$add2sitemapsql = "INSERT INTO ".$sitemapsql;
		$edit2sitemapsql = "UPDATE ".$sitemapsql." WHERE `id`=".intval($_POST['pi-id']);

		$menusql = "`#__menupunkti` SET 
			`pid`=".intval($_POST['pi-parpunktmenu']).",
			`mid`=".intval($_POST['pi-menu']).",
			`name`='".addslashes($_POST['pi-linktext'])."',
			`sort`=".intval($_POST['pi-linksort']).",
			`link`='".addslashes($get_url)."',
			`public`='1',
			`target`='".addslashes($_POST['pi-openlinkin'])."'";
		
		$add2menusql = "INSERT INTO ".$menusql.", `page_id`={newpage_id}";
		if(intval($_POST['pi-menu-id']))
		{
			if($_POST['pi-menu']) $edit2menusql = "UPDATE ".$menusql." WHERE `id`=".intval($_POST['pi-menu-id']);
			else $edit2menusql = "DELETE FROM `#__menupunkti` WHERE `id`=".intval($_POST['pi-menu-id']);
		}elseif($_POST['pi-menu'])
		{
			$edit2menusql = "INSERT INTO ".$menusql.", `page_id`=".intval($_POST['pi-id']);
		}


	switch($ACTION)
	{
		case 'add':
			$gosorttopinsitemap = "UPDATE `#__sitemap` SET `sort`=(`sort`+1) WHERE `pid`=".intval($_POST['pi-pid']);

			switch($thispagetype)
			{
				case '1':
					$this->query($gosorttopinsitemap);
					$this->query($add2sitemapsql);
					$newpage_id = $this->insert_id();
				break;
				case '2':
					$this->query($gosorttopinsitemap);
					$this->query($add2sitemapsql.", `public`='1'");
					$newpage_id = $this->insert_id();

					if($_POST['pi-menu'])
					{
						$sql = str_replace("`page_id`={newpage_id}","`page_id`=".intval($newpage_id),$add2menusql);
						$this->query($sql);
					}
				break;
				case '3':
					$rec_id = 0;
					$this->adm_get_com_config($row['id']);
					$this->adm_go_upload_file(); // загружаем файлы
					$fields = $this->get_edit_fields();
					$sql = $this->adm_get_edit_sql("#".$this->adm_com_config['config']['tbl'],$fields);
					$res = $this->query($sql);
					$rec_id = $this->insert_id();

					$this->query($gosorttopinsitemap);
					$this->query($add2sitemapsql.", `public`='1',`record_id`=".intval($rec_id));
					$newpage_id = $this->insert_id();
					
					if($_POST['pi-menu'])
					{
						$sql = str_replace("`page_id`={newpage_id}","`page_id`=".intval($newpage_id),$add2menusql);
						$this->query($sql);
					}
				break;
				case '4':
					$_POST['allshowedfields'] = preg_replace("/\;pid\;/i",";",$_POST['allshowedfields']);
					$_POST['allshowedfields'] = preg_replace("/\;pid$/i","",$_POST['allshowedfields']);
					$_POST['allshowedfields'] = preg_replace("/^pid\;/i","",$_POST['allshowedfields']);
					$_POST['allshowedfields'] = $_POST['allshowedfields'].";pid";
					$_POST['pid'] = $_POST['pi-pid'];
					
					$rec_id = 0;
					$this->adm_get_com_config($row['id']);
					$this->adm_go_upload_file(); // загружаем файлы
					$fields = $this->get_edit_fields();
					$sql = $this->adm_get_edit_sql("#".$this->adm_com_config['config']['tbl'],$fields);
					$res = $this->query($sql);
					$rec_id = $this->insert_id();
				break;
			}
			$this->adm_add_sys_mes($this->core_echomui('admc_record_add'),"ok");
		break;
		case 'edit':
			switch($thispagetype)
			{
				case '1':
					$this->query($edit2sitemapsql);
				break;
				case '2':
					$this->query($edit2sitemapsql);
					$this->query($edit2menusql);
				break;
				case '3':
					$this->query($edit2sitemapsql);
					$this->query($edit2menusql);
					$rec_id = 0;
					$this->id = $_POST['record_id'];
					$this->adm_get_com_config($row['id']);
					$this->adm_go_upload_file(); // загружаем файлы
					$fields = $this->get_edit_fields();

					$sql = $this->adm_get_edit_sql("#".$this->adm_com_config['config']['tbl'],$fields);
if($this->get_user_ip()=='83.167.27.78'&&0)
{
echo $this->prefixed($sql);
	exit;
}
					$res = $this->query($sql);
					if(!$_POST['record_id']) $this->query("UPDATE `#__sitemap` SET `record_id`=".intval($this->insert_id())." WHERE `id`=".intval($_POST['id']));
				break;
			}
			$this->adm_add_sys_mes($this->core_echomui('admc_record_edit'),"ok");
			$newpage_id = intval($_POST['pi-id']);
		break;
	}
		if($_POST['app'] && $thispagetype<4) $link = '/'.$this->way_url_get.'/?id='.$newpage_id;
		else 
		{
			if($_GET['ref']) $link = $_GET['ref'];
			else $link = '/'.$this->adm_path.'/';
		}
		$this->reload($link);

	}//if(!$error)

}//f($_POST['save'] || $_POST['app'])



$this->adm_show_sys_mes();
$file = "_pages/".$this->way."/content.php";//".$row['add_button']."
$this->core_readmuifile(DOCUMENT_ROOT."/".$this->adm_path."/ajax-pages/addpart/lang/".$this->muiparam().".txt");


if(file_exists($file) && (!$row['limit'] || ($row['limit'] && $added_num<$row['limit']) || $ACTION=='edit')) {
	echo $this->adm_open_edit_form();
	require($file);
	$save = array("value"=>$this->core_echomui('admc1_button_save'),"class"=>"savebutton","alter_class"=>"savebutton_h");
	if($thispagetype<4) $app = array("value"=>$this->core_echomui('admc1_button_applay'),"class"=>"appbutton","alter_class"=>"appbutton_h");
	$cancel = array("value"=>$this->core_echomui('admc1_button_cancel'),"class"=>"cancelbutton","alter_class"=>"cancelbutton_h");
	if($_GET['ref']) $cancel['script']="location.href='".$_GET['ref']."';return false;";

	echo "<div class='clear'></div>";
	
	echo $this->adm_show_edit_toolbar($save,$app,$cancel);
	
	echo "<input type='hidden' value='".(($row['parent_parts']=='root')?0:$_POST['pi-pid'])."' id='sel-pid' name='pi-pid' />";
	echo "<input type='hidden' name='pi-id' value='".$_POST['pi-id']."'>";
	echo "<input type='hidden' name='pi-menu-id' value='".$_POST['pi-menu-id']."'>";
	echo "<input type='hidden' name='pi-com_id' value='".$com."'>";
	echo "<input type='hidden' name='record_id' value='".$_POST['record_id']."'>";
	echo "<input type='hidden' name='menupunktid' value='".$_POST['menupunktid']."'>";
	echo "</form>";
}elseif(!($row['limit'] && $added_num<$row['limit'])) echo "limit exceeded";
else echo "bad";

?>