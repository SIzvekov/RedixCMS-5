function JsHttpRequest(){var t=this;t.onreadystatechange=null;t.readyState=0;t.responseText=null;t.responseXML=null;t.status=200;t.statusText="OK";t.responseJS=null;t.caching=false;t.loader=null;t.session_name="PHPSESSID";t._ldObj=null;t._reqHeaders=[];t._openArgs=null;t._errors={inv_form_el:'Invalid FORM element detected: name=%, tag=%',must_be_single_el:'If used, <form> must be a single HTML element in the list.',js_invalid:'JavaScript code generated by backend is invalid!\n%',url_too_long:'Cannot use so long query with GET request (URL is larger than % bytes)',unk_loader:'Unknown loader: %',no_loaders:'No loaders registered at all, please check JsHttpRequest.LOADERS array',no_loader_matched:'Cannot find a loader which may process the request. Notices are:\n%'}
t.abort=function(){with(this){if(_ldObj&&_ldObj.abort)_ldObj.abort();_cleanup();if(readyState==0){return;}if(readyState==1&&!_ldObj){readyState=0;return;}_changeReadyState(4,true);}}
t.open=function(method,url,asyncFlag,username,password){with(this){if(url.match(/^((\w+)\.)?(GET|POST)\s+(.*)/i)){this.loader=RegExp.$2?RegExp.$2:null;method=RegExp.$3;url=RegExp.$4;}try{if(document.location.search.match(new RegExp('[&?]'+session_name+'=([^&?]*)'))||document.cookie.match(new RegExp('(?:;|^)\\s*'+session_name+'=([^;]*)'))){url+=(url.indexOf('?')>=0?'&':'?')+session_name+"="+this.escape(RegExp.$1);}}catch(e){}_openArgs={method:(method||'').toUpperCase(),url:url,asyncFlag:asyncFlag,username:username!=null?username:'',password:password!=null?password:''}
_ldObj=null;_changeReadyState(1,true);return true;}}
t.send=function(content){if(!this.readyState){return;}this._changeReadyState(1,true);this._ldObj=null;var queryText=[];var queryElem=[];if(!this._hash2query(content,null,queryText,queryElem))return;var hash=null;if(this.caching&&!queryElem.length){hash=this._openArgs.username+':'+this._openArgs.password+'@'+this._openArgs.url+'|'+queryText+"#"+this._openArgs.method;var cache=JsHttpRequest.CACHE[hash];if(cache){this._dataReady(cache[0],cache[1]);return false;}}var loader=(this.loader||'').toLowerCase();if(loader&&!JsHttpRequest.LOADERS[loader])return this._error('unk_loader',loader);var errors=[];var lds=JsHttpRequest.LOADERS;for(var tryLoader in lds){var ldr=lds[tryLoader].loader;if(!ldr)continue;if(loader&&tryLoader!=loader)continue;var ldObj=new ldr(this);JsHttpRequest.extend(ldObj,this._openArgs);JsHttpRequest.extend(ldObj,{queryText:queryText.join('&'),queryElem:queryElem,id:(new Date().getTime())+""+JsHttpRequest.COUNT++,hash:hash,span:null});var error=ldObj.load();if(!error){this._ldObj=ldObj;JsHttpRequest.PENDING[ldObj.id]=this;return true;}if(!loader){errors[errors.length]='- '+tryLoader.toUpperCase()+': '+this._l(error);}else{return this._error(error);}}return tryLoader?this._error('no_loader_matched',errors.join('\n')):this._error('no_loaders');}
t.getAllResponseHeaders=function(){with(this){return _ldObj&&_ldObj.getAllResponseHeaders?_ldObj.getAllResponseHeaders():[];}}
t.getResponseHeader=function(label){with(this){return _ldObj&&_ldObj.getResponseHeader?_ldObj.getResponseHeader(label):null;}}
t.setRequestHeader=function(label,value){with(this){_reqHeaders[_reqHeaders.length]=[label,value];}}
t._dataReady=function(text,js){with(this){if(caching&&_ldObj)JsHttpRequest.CACHE[_ldObj.hash]=[text,js];responseText=responseXML=text;responseJS=js;if(js!==null){status=200;statusText="OK";}else{status=500;statusText="Internal Server Error";}_changeReadyState(2);_changeReadyState(3);_changeReadyState(4);_cleanup();}}
t._l=function(args){var i=0,p=0,msg=this._errors[args[0]];while((p=msg.indexOf('%',p))>=0){var a=args[++i]+"";msg=msg.substring(0,p)+a+msg.substring(p+1,msg.length);p+=1+a.length;}return msg;}
t._error=function(msg){msg=this._l(typeof(msg)=='string'?arguments:msg)
msg="JsHttpRequest: "+msg;if(!window.Error){throw msg;}else if((new Error(1,'test')).description=="test"){throw new Error(1,msg);}else{throw new Error(msg);}}
t._hash2query=function(content,prefix,queryText,queryElem){if(prefix==null)prefix="";if((''+typeof(content)).toLowerCase()=='object'){var formAdded=false;if(content&&content.parentNode&&content.parentNode.appendChild&&content.tagName&&content.tagName.toUpperCase()=='FORM'){content={form:content};}for(var k in content){var v=content[k];if(v instanceof Function)continue;var curPrefix=prefix?prefix+'['+this.escape(k)+']':this.escape(k);var isFormElement=v&&v.parentNode&&v.parentNode.appendChild&&v.tagName;if(isFormElement){var tn=v.tagName.toUpperCase();if(tn=='FORM'){formAdded=true;}else if(tn=='INPUT'||tn=='TEXTAREA'||tn=='SELECT'){}else{return this._error('inv_form_el',(v.name||''),v.tagName);}queryElem[queryElem.length]={name:curPrefix,e:v};}else if(v instanceof Object){this._hash2query(v,curPrefix,queryText,queryElem);}else{if(v===null)continue;if(v===true)v=1;if(v===false)v='';queryText[queryText.length]=curPrefix+"="+this.escape(''+v);}if(formAdded&&queryElem.length>1){return this._error('must_be_single_el');}}}else{queryText[queryText.length]=content;}return true;}
t._cleanup=function(){var ldObj=this._ldObj;if(!ldObj)return;JsHttpRequest.PENDING[ldObj.id]=false;var span=ldObj.span;if(!span)return;ldObj.span=null;var closure=function(){span.parentNode.removeChild(span);}
JsHttpRequest.setTimeout(closure,50);}
t._changeReadyState=function(s,reset){with(this){if(reset){status=statusText=responseJS=null;responseText='';}readyState=s;if(onreadystatechange)onreadystatechange();}}
t.escape=function(s){return escape(s).replace(new RegExp('\\+','g'),'%2B');}}JsHttpRequest.COUNT=0;JsHttpRequest.MAX_URL_LEN=2000;JsHttpRequest.CACHE={};JsHttpRequest.PENDING={};JsHttpRequest.LOADERS={};JsHttpRequest._dummy=function(){};JsHttpRequest.TIMEOUTS={s:window.setTimeout,c:window.clearTimeout};JsHttpRequest.setTimeout=function(func,dt){window.JsHttpRequest_tmp=JsHttpRequest.TIMEOUTS.s;if(typeof(func)=="string"){id=window.JsHttpRequest_tmp(func,dt);}else{var id=null;var mediator=function(){func();delete JsHttpRequest.TIMEOUTS[id];}
id=window.JsHttpRequest_tmp(mediator,dt);JsHttpRequest.TIMEOUTS[id]=mediator;}window.JsHttpRequest_tmp=null;return id;}
JsHttpRequest.clearTimeout=function(id){window.JsHttpRequest_tmp=JsHttpRequest.TIMEOUTS.c;delete JsHttpRequest.TIMEOUTS[id];var r=window.JsHttpRequest_tmp(id);window.JsHttpRequest_tmp=null;return r;}
JsHttpRequest.query=function(url,content,onready,nocache){var req=new this();req.caching=!nocache;req.onreadystatechange=function(){if(req.readyState==4){onready(req.responseJS,req.responseText);}}
req.open(null,url,true);req.send(content);}
JsHttpRequest.dataReady=function(d){var th=this.PENDING[d.id];delete this.PENDING[d.id];if(th){th._dataReady(d.text,d.js);}else if(th!==false){throw"dataReady(): unknown pending id: "+d.id;}}
JsHttpRequest.extend=function(dest,src){for(var k in src)dest[k]=src[k];}
JsHttpRequest.LOADERS.xml={loader:function(req){JsHttpRequest.extend(req._errors,{xml_no:'Cannot use XMLHttpRequest or ActiveX loader: not supported',xml_no_diffdom:'Cannot use XMLHttpRequest to load data from different domain %',xml_no_headers:'Cannot use XMLHttpRequest loader or ActiveX loader, POST method: headers setting is not supported, needed to work with encodings correctly',xml_no_form_upl:'Cannot use XMLHttpRequest loader: direct form elements using and uploading are not implemented'});this.load=function(){if(this.queryElem.length)return['xml_no_form_upl'];if(this.url.match(new RegExp('^([a-z]+://[^\\/]+)(.*)','i'))){if(RegExp.$1.toLowerCase()!=document.location.protocol+'//'+document.location.hostname.toLowerCase()){return['xml_no_diffdom',RegExp.$1];}}var xr=null;if(window.XMLHttpRequest){try{xr=new XMLHttpRequest()}catch(e){}}else if(window.ActiveXObject){try{xr=new ActiveXObject("Microsoft.XMLHTTP")}catch(e){}if(!xr)try{xr=new ActiveXObject("Msxml2.XMLHTTP")}catch(e){}}if(!xr)return['xml_no'];var canSetHeaders=window.ActiveXObject||xr.setRequestHeader;if(!this.method)this.method=canSetHeaders&&this.queryText.length?'POST':'GET';if(this.method=='GET'){if(this.queryText)this.url+=(this.url.indexOf('?')>=0?'&':'?')+this.queryText;this.queryText='';if(this.url.length>JsHttpRequest.MAX_URL_LEN)return['url_too_long',JsHttpRequest.MAX_URL_LEN];}else if(this.method=='POST'&&!canSetHeaders){return['xml_no_headers'];}this.url+=(this.url.indexOf('?')>=0?'&':'?')+'JsHttpRequest='+(req.caching?'0':this.id)+'-xml';var id=this.id;xr.onreadystatechange=function(){if(xr.readyState!=4)return;xr.onreadystatechange=JsHttpRequest._dummy;req.status=null;try{req.status=xr.status;req.responseText=xr.responseText;}catch(e){}if(!req.status)return;try{var rtext=req.responseText||'{ js: null, text: null }';eval('JsHttpRequest._tmp = function(id) { var d = '+rtext+'; d.id = id; JsHttpRequest.dataReady(d); }');}catch(e){return req._error('js_invalid',req.responseText)}JsHttpRequest._tmp(id);JsHttpRequest._tmp=null;};xr.open(this.method,this.url,true,this.username,this.password);if(canSetHeaders){for(var i=0;i<req._reqHeaders.length;i++){xr.setRequestHeader(req._reqHeaders[i][0],req._reqHeaders[i][1]);}xr.setRequestHeader('Content-Type','application/octet-stream');}xr.send(this.queryText);this.span=null;this.xr=xr;return null;}
this.getAllResponseHeaders=function(){return this.xr.getAllResponseHeaders();}
this.getResponseHeader=function(label){return this.xr.getResponseHeader(label);}
this.abort=function(){this.xr.abort();this.xr=null;}}}
JsHttpRequest.LOADERS.script={loader:function(req){JsHttpRequest.extend(req._errors,{script_only_get:'Cannot use SCRIPT loader: it supports only GET method',script_no_form:'Cannot use SCRIPT loader: direct form elements using and uploading are not implemented'})
this.load=function(){if(this.queryText)this.url+=(this.url.indexOf('?')>=0?'&':'?')+this.queryText;this.url+=(this.url.indexOf('?')>=0?'&':'?')+'JsHttpRequest='+this.id+'-'+'script';this.queryText='';if(!this.method)this.method='GET';if(this.method!=='GET')return['script_only_get'];if(this.queryElem.length)return['script_no_form'];if(this.url.length>JsHttpRequest.MAX_URL_LEN)return['url_too_long',JsHttpRequest.MAX_URL_LEN];var th=this,d=document,s=null,b=d.body;if(!window.opera){this.span=s=d.createElement('SCRIPT');var closure=function(){s.language='JavaScript';if(s.setAttribute)s.setAttribute('src',th.url);else s.src=th.url;b.insertBefore(s,b.lastChild);}}else{this.span=s=d.createElement('SPAN');s.style.display='none';b.insertBefore(s,b.lastChild);s.innerHTML='Workaround for IE.<s'+'cript></'+'script>';var closure=function(){s=s.getElementsByTagName('SCRIPT')[0];s.language='JavaScript';if(s.setAttribute)s.setAttribute('src',th.url);else s.src=th.url;}}JsHttpRequest.setTimeout(closure,10);return null;}}}
JsHttpRequest.LOADERS.form={loader:function(req){JsHttpRequest.extend(req._errors,{form_el_not_belong:'Element "%" does not belong to any form!',form_el_belong_diff:'Element "%" belongs to a different form. All elements must belong to the same form!',form_el_inv_enctype:'Attribute "enctype" of the form must be "%" (for IE), "%" given.'})
this.load=function(){var th=this;if(!th.method)th.method='POST';th.url+=(th.url.indexOf('?')>=0?'&':'?')+'JsHttpRequest='+th.id+'-'+'form';if(th.method=='GET'){if(th.queryText)th.url+=(th.url.indexOf('?')>=0?'&':'?')+th.queryText;if(th.url.length>JsHttpRequest.MAX_URL_LEN)return['url_too_long',JsHttpRequest.MAX_URL_LEN];var p=th.url.split('?',2);th.url=p[0];th.queryText=p[1]||'';}var form=null;var wholeFormSending=false;if(th.queryElem.length){if(th.queryElem[0].e.tagName.toUpperCase()=='FORM'){form=th.queryElem[0].e;wholeFormSending=true;th.queryElem=[];}else{form=th.queryElem[0].e.form;for(var i=0;i<th.queryElem.length;i++){var e=th.queryElem[i].e;if(!e.form){return['form_el_not_belong',e.name];}if(e.form!=form){return['form_el_belong_diff',e.name];}}}if(th.method=='POST'){var need="multipart/form-data";var given=(form.attributes.encType&&form.attributes.encType.nodeValue)||(form.attributes.enctype&&form.attributes.enctype.value)||form.enctype;if(given!=need){return['form_el_inv_enctype',need,given];}}}var d=form&&(form.ownerDocument||form.document)||document;var ifname='jshr_i_'+th.id;var s=th.span=d.createElement('DIV');s.style.position='absolute';s.style.display='none';s.style.visibility='hidden';s.innerHTML=(form?'':'<form'+(th.method=='POST'?' enctype="multipart/form-data" method="post"':'')+'></form>')+'<iframe name="'+ifname+'" id="'+ifname+'" style="width:0px; height:0px; overflow:hidden; border:none"></iframe>'
if(!form){form=th.span.firstChild;}d.body.insertBefore(s,d.body.lastChild);var setAttributes=function(e,attr){var sv=[];var form=e;if(e.mergeAttributes){var form=d.createElement('form');form.mergeAttributes(e,false);}for(var i=0;i<attr.length;i++){var k=attr[i][0],v=attr[i][1];sv[sv.length]=[k,form.getAttribute(k)];form.setAttribute(k,v);}if(e.mergeAttributes){e.mergeAttributes(form,false);}return sv;}
var closure=function(){top.JsHttpRequestGlobal=JsHttpRequest;var savedNames=[];if(!wholeFormSending){for(var i=0,n=form.elements.length;i<n;i++){savedNames[i]=form.elements[i].name;form.elements[i].name='';}}var qt=th.queryText.split('&');for(var i=qt.length-1;i>=0;i--){var pair=qt[i].split('=',2);var e=d.createElement('INPUT');e.type='hidden';e.name=unescape(pair[0]);e.value=pair[1]!=null?unescape(pair[1]):'';form.appendChild(e);}for(var i=0;i<th.queryElem.length;i++){th.queryElem[i].e.name=th.queryElem[i].name;}var sv=setAttributes(form,[['action',th.url],['method',th.method],['onsubmit',null],['target',ifname]]);form.submit();setAttributes(form,sv);for(var i=0;i<qt.length;i++){form.lastChild.parentNode.removeChild(form.lastChild);}if(!wholeFormSending){for(var i=0,n=form.elements.length;i<n;i++){form.elements[i].name=savedNames[i];}}}
JsHttpRequest.setTimeout(closure,100);return null;}}}
function loadXMLDoc(url,innerid,form_id,noshowloader){ajaxuploadfile(url,innerid,form_id,noshowloader);}function ajaxuploadfile(url,innerid,form_id,noshowloader){var req=new JsHttpRequest();req.onreadystatechange=function(){if(req.readyState==4){if(innerid&&document.getElementById(innerid)){document.getElementById(innerid).innerHTML=req.responseText;}if(req.responseJS.alert){alert(req.responseJS.alert);}if(req.responseJS.location){location.href=req.responseJS.location;}if(req.responseJS.fk_callback){if(req.responseJS.fk_noauth==1){display_login_form(req.responseJS.p);}else{if(req.responseJS.was==1){alert(req.responseJS.wastext);i=1;while(document.getElementById('votebtn-'+i)){document.getElementById('votebtn-'+i).innerHTML='<i><small>'+req.responseJS.wastext+'</small></i>';i++;}}else{document.getElementById('fkscore'+req.responseJS.p).innerHTML=req.responseJS.score;i=1;while(document.getElementById('votebtn-'+i)){document.getElementById('votebtn-'+i).innerHTML='<i><small>'+req.responseJS.wastext+'</small></i>';i++;}alert('Ваш голос принят, спасибо!');}}if(document.getElementById('fk_vote_btn-'+req.responseJS.p)){document.getElementById('fk_vote_btn-'+req.responseJS.p).value='Проголосовать';document.getElementById('fk_vote_btn-'+req.responseJS.p).disabled=false;}}if(req.responseJS.avtorized){close_fk_avt_form();if(req.responseJS.fk>0){fk_vote(req.responseJS.fk);}}}}
req.open(null,url,true);if(form_id&&document.getElementById(form_id)){form=document.getElementById(form_id);req.send({'form':form});}else if(form_id&&document.forms[form_id]){form=document.forms[form_id];req.send({'form':form});}else{req.send();}if(innerid&&document.getElementById(innerid)&&noshowloader!=1){}}