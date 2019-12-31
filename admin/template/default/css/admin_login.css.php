<?php
header("Content-Type: text/css; charset=windows-1251");
$c_adm_template = "default";
$adm_path = "admin";
?>
body.login-body {background:#fff;font-family: Verdana; margin: 0px;padding: 0px;font-size : 10px;}
form{padding:0px; margin:0px;}
behavior: url(/templates/iepngfix.htc);
table.kkr {}
table.kkr td.kkr {padding: 0px;}
div.redixcmslogo {padding: 5px 10px 5px 10px; text-align:center;}
div.submit {padding: 0px 10px 0px 10px; text-align:center;}
a {color: #00f;text-decoration:underline;}
a:hover {color: #f00;text-decoration:none;}

.login-table {margin-bottom:3px;width:400px;}
.login-title-l {width:8px;}
.login-title {background:#006435;color:#FFFFFF;font-size:12px;font-weight:bold;padding-left:10px;}
.login-title-r {width:8px;}
.login-content {background:url('/<?=$adm_path?>/template/<?=$c_adm_template?>/img/login-bg.gif');border:1px solid #006435;border-top:0px;text-align:center;}
.login-error-title {color:#C50000;font-size:11px;font-weight:bold;padding:3px;text-align:right;vertical-align:top;width:35%;}
.login-error-content {color:#C50000;font-size:11px;padding:3px;}
.login-field-title {color:#43555f;font-size:11px;padding:3px;text-align:right;width:35%;}
.login-field-content {color:#43555f;font-size:11px;padding:3px;}
.login-button {background:#D4D0C8;border-bottom:solid 1px #808080;border-left:solid 1px #FFFFFF;border-right:solid 1px #808080;border-top:solid 1px #FFFFFF;color:#393939;font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif;font-size:11px;font-weight:normal;margin:10px 0px;padding:4px; height:50px;width:70px;cursor:pointer}
.login-register {padding-bottom:12px;}
.login-input {border:solid 1px #9C9C9C;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:11px;font-weight:normal;outline:4px;}
.login-copy {font-size:11px;}
table.login-help {margin:10px;}
table.login-help td {font-size:11px;}
img.png{behavior: url(/templates/iepngfix.htc);}