var _cmTimeOut = null;
var delayTime = 1000;
var gid = '';
function showsubmenu(id)
{
	if(!id || !document.getElementById(id)) return;
	var i=1;
	while(document.getElementById('sublink'+i)){document.getElementById('sublink'+i).style.display='none';i++;}
	document.getElementById(id).style.display='block';
}
function hidmenu(i)
{
	if(!gid || !document.getElementById(gid)) return;
	document.getElementById(gid).style.display='none';
	
	id = gid.split("_");
	newid = id[0];
	for(k=1;k<id.length;k++)
	{
		newid = newid+'_'+id[k];
	}
	id = newid;
	curvalue = document.getElementById(id).value;
	var j=1;
	while(document.getElementById(id+'_optvalue_'+j))
	{
		if(document.getElementById(id+'_optvalue_'+j).value == curvalue) document.getElementById(id+'_'+j).className = 'strselectselected';
		else document.getElementById(id+'_'+j).className = 'strselectnormal';
		j++;
	}
}

function starttimeout(id)
{
	clearTimeout (_cmTimeOut);
	gid = id;
	_cmTimeOut = window.setTimeout('hidmenu ()', delayTime);
}

function cltimeout()
{
	clearTimeout (_cmTimeOut);
}

function overstr(id,i)
{
	var j=1;
	var element = document.getElementById(id+'_'+i);
	if(!element) return false;
	while(document.getElementById(id+'_'+j))
	{
		document.getElementById(id+'_'+j).className = 'strselectnormal';
		j++;
	}
	element.className = 'strselectselected';
}

function outstr(id)
{
	var j=1;
	while(document.getElementById(id+'_'+j))
	{
		document.getElementById(id+'_'+j).className = 'strselectnormal';
		j++;
	}
}

function checkstr(id,i)
{
	var j=1;
	var element = document.getElementById(id+'_'+i);
	if(!element) return false;
	while(document.getElementById(id+'_'+j))
	{
		document.getElementById(id+'_'+j).className = 'strselectnormal';
		j++;
	}
	element.className = 'strselectselected';
	document.getElementById(id).value=document.getElementById(id+'_optvalue_'+i).value;
	document.getElementById(id+'_maintext').innerHTML = document.getElementById(id+'_opttext_'+i).innerHTML;
	hidmenu();
}
//-->