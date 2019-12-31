<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<?=$this->core_modul("htmlhead");?>
</head>
<body>
<style type="text/css"> 
	body {padding: 10px;width: auto;}
	h1 {color: #F0AB00; font-size:10em; font-weight:bold; float:right;margin:0px;}
	h2 {font-size:2em; color:#036633; border-bottom: 1px dotted #036633; padding:95px 0 15px; font-weight:normal;margin:0px;}
</style> 
 <div>
<?if(file_exists(DOCUMENT_ROOT."/templates/".$this->config['tpl']."/images/logo.jpg")){?><div align="center"><a href="/<?=$this->param?>/"><img src="/templates/<?=$this->config['tpl']?>/images/logo.jpg" border="0"/></a></div><?}?>
	
	<h1>404</h1> 
	<h2><?=$this->core_echomui('error404-pagenotfound')?></h2> 
	<p> 
	<?=$this->core_echomui('error404-describe')?>
	</p> 
	<p> 
	<?=$this->core_echomui('error404-go2home')?>
	<a href="<?=($this->config['use_param']?'/'.$this->param.'/':'/')?>">http://<?=HTTP_HOST?>/</a>	</p> 

</div> 
<?=$this->core_modul('blank', 'newton')?>
</body>
</html>