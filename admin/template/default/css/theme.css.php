<?
header("Content-Type: text/css; charset=windows-1251");
$c_adm_template = "default";
$adm_path = "admin";
?>
.ThemeOfficeSubMenu
{
	position:	absolute;
	visibility:	hidden;
	z-index:	100;
	border:		0;
	padding:	0;

	overflow:	visible;
	border:		1px solid #8C867B;

	filter:progid:DXImageTransform.Microsoft.Shadow(color=#BDC3BD, Direction=135, Strength=4);
}

.ThemeOfficeSubMenuTable
{
	overflow:	visible;
}

.ThemeOfficeMainItem,.ThemeOfficeMainItemHover,.ThemeOfficeMainItemActive,
.ThemeOfficeMenuItem,.ThemeOfficeMenuItemHover,.ThemeOfficeMenuItemActive
{
	border:		0;
	cursor:		default;
	white-space:	nowrap;
}


.ThemeOfficeMainItemHover,.ThemeOfficeMainItemActive /* фон главного меню (пид = 0)  при наведении*/
{
	background-color:	#f1e8e6;
}

.ThemeOfficeMenuItem /* фон пунктов меню с пид>0 в обычном режиме */
{
	background-color:	#F1F3F5;
}

.ThemeOfficeMenuItemHover,.ThemeOfficeMenuItemActive /* фон пунктов меню с пид>0 при наведении */
{
	background-color:	#f1e8e6;
}


/* horizontal main menu */

.ThemeOfficeMainItem /* пид=0 позиция пунктов меню */
{
	padding: 4px 1px 4px 1px;
	border-right:	1px solid #F1F3F5;
	border-left:	1px solid #F1F3F5;
}

td.ThemeOfficeMainItemHover,td.ThemeOfficeMainItemActive /* пид=0 пункты меню при наведении */
{
	padding: 4px 1px 4px 1px;
	border-right:	1px solid #c24733;
	border-left:	1px solid #c24733;
}

.ThemeOfficeMainFolderLeft,.ThemeOfficeMainItemLeft,
.ThemeOfficeMainFolderText,.ThemeOfficeMainItemText,
.ThemeOfficeMainFolderRight,.ThemeOfficeMainItemRight
{
	background-color:	inherit;
}

/* vertical main menu sub components */

td.ThemeOfficeMainFolderLeft,td.ThemeOfficeMainItemLeft
{
	padding-top:	2px;
	padding-bottom:	2px;
	padding-left:	0px;
	padding-right:	2px;

	border-top:	1px solid #c24733;
	border-bottom:	1px solid #c24733;
	border-left:	1px solid #c24733;

	background-color:	inherit;
}

td.ThemeOfficeMainFolderText,td.ThemeOfficeMainItemText
{
	padding-top:	2px;
	padding-bottom:	2px;
	padding-left:	5px;
	padding-right:	5px;

	border-top:	1px solid #c24733;
	border-bottom:	1px solid #c24733;

	background-color:	inherit;
	white-space:	nowrap;
}

td.ThemeOfficeMainFolderRight,td.ThemeOfficeMainItemRight
{
	padding-top:	2px;
	padding-bottom:	2px;
	padding-left:	0px;
	padding-right:	0px;

	border-top:	1px solid #c24733;
	border-bottom:	1px solid #c24733;
	border-right:	1px solid #c24733;

	background-color:	inherit;
}

tr.ThemeOfficeMainItem td.ThemeOfficeMainFolderLeft,
tr.ThemeOfficeMainItem td.ThemeOfficeMainItemLeft
{
	padding-top:	3px;
	padding-bottom:	3px;
	padding-left:	1px;
	padding-right:	2px;

	white-space:	nowrap;

	border:		0;
	background-color:	inherit;
}

tr.ThemeOfficeMainItem td.ThemeOfficeMainFolderText,
tr.ThemeOfficeMainItem td.ThemeOfficeMainItemText
{
	padding-top:	3px;
	padding-bottom:	3px;
	padding-left:	5px;
	padding-right:	5px;

	border:		0;
	background-color:	inherit;
}

tr.ThemeOfficeMainItem td.ThemeOfficeMainItemRight,
tr.ThemeOfficeMainItem td.ThemeOfficeMainFolderRight
{
	padding-top:	3px;
	padding-bottom:	3px;
	padding-left:	0px;
	padding-right:	1px;

	border:		0;
	background-color:	inherit;
}

/* sub menu sub components */

.ThemeOfficeMenuFolderLeft,.ThemeOfficeMenuItemLeft
{
	padding-top:	2px;
	padding-bottom:	2px;
	padding-left:	1px;
	padding-right:	3px;

	border-top:	1px solid #c24733;
	border-bottom:	1px solid #c24733;
	border-left:	1px solid #c24733;

	background-color:	inherit;
	white-space:	nowrap;
}

.ThemeOfficeMenuFolderText,.ThemeOfficeMenuItemText
{
	padding-top:	2px;
	padding-bottom:	2px;
	padding-left:	5px;
	padding-right:	5px;

	border-top:	1px solid #c24733;
	border-bottom:	1px solid #c24733;

	background-color:	inherit;
	white-space:	nowrap;
}

.ThemeOfficeMenuFolderRight,.ThemeOfficeMenuItemRight
{
	padding-top:	2px;
	padding-bottom:	2px;
	padding-left:	0px;
	padding-right:	0px;

	border-top:	1px solid #c24733;
	border-bottom:	1px solid #c24733;
	border-right:	1px solid #c24733;

	background-color:	inherit;
	white-space:	nowrap;
}

.ThemeOfficeMenuItem .ThemeOfficeMenuFolderLeft,
.ThemeOfficeMenuItem .ThemeOfficeMenuItemLeft
{
	padding-top:	3px;
	padding-bottom:	3px;
	padding-left:	2px;
	padding-right:	3px;

	white-space:	nowrap;

	border: 	0px;
	background-color:	#DDE1E6;
}

.ThemeOfficeMenuItem .ThemeOfficeMenuFolderText,
.ThemeOfficeMenuItem .ThemeOfficeMenuItemText
{
	padding-top:	3px;
	padding-bottom:	3px;
	padding-left:	5px;
	padding-right:	5px;

	border:		0px;
	background-color:	inherit;
}

.ThemeOfficeMenuItem .ThemeOfficeMenuFolderRight,
.ThemeOfficeMenuItem .ThemeOfficeMenuItemRight
{
	padding-top:	3px;
	padding-bottom:	3px;
	padding-left:	0px;
	padding-right:	1px;

	border:		0;
	background-color:	inherit;
}

/* menu splits */

.ThemeOfficeMenuSplit
{
	margin:		2px;
	height:		1px;
	overflow:	hidden;
	background-color:	inherit;
	border-top:	1px solid #C6C3BD;
}

/* image shadow animation */

.ThemeOfficeMenuItem img.seq1
{
	display:	inline;
}

.ThemeOfficeMenuItemHover seq2,
.ThemeOfficeMenuItemActive seq2
{
	display:	inline;
}

.ThemeOfficeMenuItem .seq2,
.ThemeOfficeMenuItemHover .seq1,
.ThemeOfficeMenuItemActive .seq1
{
	display:	none;
}


/* inactive settings */
div.inactive td.ThemeOfficeMainItemHover, div.inactive td.ThemeOfficeMainItemActive
{
	border-top: 0px;
	border-right:	1px solid #f1f3f5;
	border-left:	1px solid #f1f3f5;
}

div.inactive .ThemeOfficeMainItem {
	color: #bbb;

}

div.inactive span.ThemeOfficeMainItemText {
	color: #aaa;
}

div.inactive .ThemeOfficeMainItemHover, div.inactive .ThemeOfficeMainItemActive
{
	background-color:	#f1f3f5;
}

td.mmenu
{
	padding-left: 5pt;
	padding-right: 5pt;
	background-color:	#f1f3f5;
	border-bottom:	1px solid #cccccc;
}

.dynamic-tab-pane-control.tab-pane {
	position:	relative;
	/*width:		100%;		 width needed weird IE bug */
	/*margin-right:	-2px;	 to make room for the shadow */
}
.dynamic-tab-pane-control .tab-row .tab {
	width: 140px;
	height: 16px;
	background-image: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/tab.png);
	position: relative;
	top: 0;
	display: inline;
	float: left;
	overflow: hidden;
	cursor: pointer;
	margin: 1px -1px 1px 2px;
	padding: 2px 0px 0px 0px;
	border: 0;
	z-index: 1;
	font: 11px Tahoma, Helvetica, sans-serif;
	white-space: nowrap;
	text-align: center;
}
.dynamic-tab-pane-control .tab-row .tab.selected {
	width: 150px !important;
	height: 18px !important;
	background-image:	url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/tab_active.png) !important;
	background-repeat: no-repeat;
	border-bottom-width:	0;
	z-index: 3;
	padding:	2px 0px 0px 0px;
	margin: 1px -3px -3px 0px;
	top: -2px;
	font: 11px Tahoma, Helvetica, sans-serif;
}
.dynamic-tab-pane-control .tab-row .tab a {
	font:	11px Tahoma, Helvetica, sans-serif;
	color: #333;
	text-decoration: none;
	cursor: pointer;
}
.dynamic-tab-pane-control .tab-row .tab.hover {
	font:	11px Tahoma, Helvetica, sans-serif;
	width: 140px;
	height: 16px;
	background-image:	url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/tab_hover.png);
	background-repeat: no-repeat;
}
.dynamic-tab-pane-control .tab-page {

	border: 1px solid rgb( 145, 155, 156 );
	background: rgb( 252, 252, 254 );
	z-index: 2;
	position: relative;
	top: -2px;
	font: 11px Tahoma, Helvetica, sans-serif;
	color: #333;

	/*244, 243, 238*/
	/* 145, 155, 156*/
	padding:	5px;
	width: 97%;  /* stupid stupid stupid IE!!! */
	float: left;

}
.dynamic-tab-pane-control .tab-row {
	z-index: 1;
	white-space: nowrap;
}

.mytab_normal {
	background-image: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/tab.png);
	background-position: center top;
	background-repeat:no-repeat;
	height:16px;
	width:140px;
	text-align:center;
	cursor: pointer;
}
.mytab_hover {
	background-image: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/tab_hover.png);
	background-position: center top;
	background-repeat:no-repeat;
	height:16px;
	width:140px;
	text-align:center;
	cursor: pointer;
}
.mytab_active {
	background-image: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/tab_active.png);
	background-position: center top;
	background-repeat:no-repeat;
	height:16px;
	width:150px;
	text-align:center;
}

img.png {behavior: url(/templates/iepngfix.htc);}