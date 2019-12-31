<?
header("Content-Type: text/css; charset=windows-1251");
$c_adm_template = "default";
$adm_path = "admin";
?>
/* настройка SELECT */
table.mainselect
{
	width:100px;
	border:1px solid #a4b97f;
}
td.textselect
{
	padding:2px;
	cursor:default;
}
td.picselect
{
	padding:2px;
}

table.itemsselect
{
	background-color:#ffffff;
	width:100px;
	border:1px solid #a4b97f;
	position:absolute;
}

td.strselectnormal
{
	padding:0 2 0 2;
	cursor:default;
}

td.strselectselected
{
	padding:0 2 0 2;
	cursor:default;
	background-color: #a4b97f;
	color: #ffffff;
}

td.selectoptgroup
{
	padding: 0 2 0 2;
	cursor: default;
	font-weight: bold;
	text-transform: italic;
}

/* закончили настройку select'a*/