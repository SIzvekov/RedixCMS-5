function showdialog(dialogclassname,ajaxpage,ajaxuri,ajaxsendform,padbgcolor,padopacity){if(document.getElementById('rx_bgpad')){alert('Error, Element \'rx_bgpad\' already exists');return false;}if(document.getElementById('rx_dialog')){alert('Error, Element \'rx_dialog\' already exists');return false;}if(document.getElementById('rx_dialog_closebut')){alert('Error, Element \'rx_dialog_closebut\' already exists');return false;}if(!padbgcolor)padbgcolor='#000';if(!padopacity)padopacity='0.7';var bgpad=document.createElement("div");bgpad.style.position='fixed';bgpad.style.zIndex='10000';bgpad.style.top='0';bgpad.style.left='0';bgpad.style.height='100%';bgpad.style.width='100%';bgpad.style.backgroundColor=padbgcolor;bgpad.style.filter='progid:DXImageTransform.Microsoft.Alpha(opacity=25)';bgpad.style.MozOpacity=padopacity;bgpad.style.KhtmlOpacity=padopacity;bgpad.style.opacity=padopacity;bgpad.id='rx_bgpad';bgpad.onclick=function(){closedialog();}
document.body.appendChild(bgpad);var dialog=document.createElement("div");dialog.className=dialogclassname;dialog.style.zIndex='10001';dialog.style.position='absolute';dialog.id='rx_dialog';dialog.innerHTML='<img src="/templates/_common_images/loader.gif" alt="" border="0" width="16" height="16"/>';document.body.appendChild(dialog);dialog.style.top=parseInt(dialog.offsetTop+getBodyScrollTop())+'px';var closebut=document.createElement("div");closebut.id='rx_dialog_closebut';closebut.style.zIndex='10002';closebut.style.position='absolute';closebut.style.top=(dialog.offsetTop-10)+'px';closebut.style.left=(dialog.offsetWidth+dialog.offsetLeft-20)+'px';closebut.style.background='url(\'/templates/_common_images/close.png\') no-repeat left top';closebut.style.width='30px';closebut.style.height='30px';closebut.style.margin='0px';closebut.style.padding='0px';closebut.style.cursor='pointer';closebut.innerHTML='&nbsp;';closebut.onclick=function(){closedialog();}
document.body.appendChild(closebut);loadXMLDoc('/ajax-index.php?page='+ajaxpage+'&'+ajaxuri,'rx_dialog',ajaxsendform);}function closedialog(){document.body.removeChild(document.getElementById('rx_bgpad'));document.body.removeChild(document.getElementById('rx_dialog'));document.body.removeChild(document.getElementById('rx_dialog_closebut'));}function getClientHeight(){return document.compatMode=='CSS1Compat'&&!window.opera?document.documentElement.clientHeight:document.body.clientHeight;}function getClientWidth(){return document.compatMode=='CSS1Compat'&&!window.opera?document.documentElement.clientWidth:document.body.clientWidth;}function getBodyScrollTop(){return self.pageYOffset||(document.documentElement&&document.documentElement.scrollTop)||(document.body&&document.body.scrollTop);}function getBodyScrollLeft(){return self.pageXOffset||(document.documentElement&&document.documentElement.scrollLeft)||(document.body&&document.body.scrollLeft);}function go_avtoriz(suff,fk){loadXMLDoc('/ajax-index.php?page=avtoriz'+(suff?'&fk='+fk:''),(suff?'fk_loginform':'avtorizblock'),'avtorizform'+suff);}function logoff(){loadXMLDoc('/ajax-index.php?page=avtoriz&logoff','avtorizblock');}function fk_vote(p){if(document.getElementById('fk_vote_btn-'+p)){document.getElementById('fk_vote_btn-'+p).value='подождите...';document.getElementById('fk_vote_btn-'+p).disabled=true;}loadXMLDoc('/ajax-index.php?page=fk_vote&p='+p);}function display_login_form(fk){document.getElementById('fk_loginform_block').style.display="block";loadXMLDoc('/ajax-index.php?page=avtoriz&fk='+fk+'','fk_loginform');}function close_fk_avt_form(){document.getElementById('fk_loginform_block').style.display="none";}
$(function () {
	if($("#seo-zone").length && $("#page_text").length){
		var pageText = $("#page_text").html();
		$("#page_text").remove();
		$("#seo-zone").html(pageText);
	}
	
	if(location.pathname == '/avtopark'){
		$("#seo-zone").html($("#seo-zone-def-inner").html());
		$("#seo-zone-def-inner").remove();
	}
	if(location.pathname == '/'){
		$("#seo-zone").html($("#seo-zone-def").html());
		$("#seo-zone-def").remove();
	}
});

// yandexMetrika цели
$(function () {
	/*
	1. Необходимо перенастроить цель на калькулятор, сделать ее необходимо
	как составную цель в 2 шага:
	Шаг 1 - клик на кнопку "калькулятор"
	Шаг 2 - клик на кнопку "заказать"
	
	*/
	$("#calc_bottom").on('click', function(){
		yaMetrikaGoal('calcButtonClick');
	});
	$(".calcbutton").on('click', function(){
		yaMetrikaGoal('calcOrder');
	});

	/*
	2. Необходимо перенастроить цель на Автопарк лимузинов, сделать ее
	необходимо как составную цель в 2 шага:
	Шаг 1 - клик на раздел "автопарк лимузинов"
	Шаг 2 - клик на кнопку "заказать" из раздела
	*/
	$('.topmenu').find("a[href='/avtopark']").attr('href','/avtopark?from=topmenu');
	$('.item-holder').find("a.order_container").on('click', function(){
		if(getUrlParameter('from') == 'topmenu'){
			yaMetrikaGoal('avtoparkOrder');
		}
	});

	/*
	3. Необходимо настроить новую цель на категорию Аренда автомобилей
	бизнес класса А, сделать ее необходимо как составную цель в 2 шага:
	Шаг 1 - клик на раздел "Аренда автомобилей бизнес класса А"
	Шаг 2 - клик на кнопку "заказать" из раздела
	*/
	catalogGoal('arenda_avtomobilei_biznes_klassa', 'catalog_BCA', 'avtoparkBCA');
	
	/*
	4. Необходимо настроить новую цель на категорию Аренда микроавтобусов,
	сделать ее необходимо как составную цель в 2 шага:
	Шаг 1 - клик на раздел "Аренда микроавтобусов"
	Шаг 2 - клик на кнопку "заказать" из раздела
	*/
	catalogGoal('arenda__mikroavtobusov', 'catalog_minivan', 'avtoparkOrderMinivan');

	/*
	5. Необходимо настроить новую цель на категорию Аренда автобусов,
	сделать ее необходимо как составную цель в 2 шага:
	Шаг 1 - клик на раздел "Аренда автобусов"
	Шаг 2 - клик на кнопку "заказать" из раздела
	*/
	catalogGoal('arenda_avtobusov', 'catalog_bus', 'avtoparkOrderBus');

	/*
	6. Необходимо настроить новую цель на категорию Лимузин на свадьбу,
	сделать ее необходимо как составную цель в 2 шага:
	Шаг 1 - клик на раздел "Лимузин на свадьбу"
	Шаг 2 - клик на кнопку "заказать" из раздела
	*/
	catalogGoal('svadba', 'catalog_weddign', 'avtoparkOrderWeddign');

	/*
	7. Необходимо настроить новую цель на категорию Встреча из роддома,
	сделать ее необходимо как составную цель в 2 шага:
	Шаг 1 - клик на раздел "Встреча из роддома"
	Шаг 2 - клик на кнопку "заказать" из раздела
	*/
	catalogGoal('vstrecha_iz_roddoma', 'catalog_maternity', 'avtoparkOrderMaternity');

	/*
	8. Необходимо настроить новую цель на категорию Прокат лимузина в День
	рождения, сделать ее необходимо как составную цель в 2 шага:
	Шаг 1 - клик на раздел "Прокат лимузина в День рождения"
	Шаг 2 - клик на кнопку "заказать" из раздела
	*/
	catalogGoal('den_rojdeniya', 'catalog_birthday', 'avtoparkOrderBirthday');
	
	/*
	9. Необходимо настроить новую цель на категорию Девичник в лимузине,
	сделать ее необходимо как составную цель в 2 шага:
	Шаг 1 - клик на раздел "Девичник в лимузине"
	Шаг 2 - клик на кнопку "заказать" из раздела
	*/
	catalogGoal('devichnik_v_limuzine', 'catalog_bachelor', 'avtoparkOrderBachelorParty');

	/*
	10. Необходимо настроить новую цель на категорию Романтическое свидание
	в лимузине, сделать ее необходимо как составную цель в 2 шага:
	Шаг 1 - клик на раздел "Романтическое свидание в лимузине"
	Шаг 2 - клик на кнопку "заказать" из раздела
	*/
	catalogGoal('romanticheskoe_svidanie', 'catalog_date', 'avtoparkOrderDate');

	/*
	11. Необходимо настроить новую цель на категорию Аренда лимузина для
	трансфера, сделать ее необходимо как составную цель в 2 шага:
	Шаг 1 - клик на раздел "Аренда лимузина для трансфера"
	Шаг 2 - клик на кнопку "заказать" из раздела
	*/
	catalogGoal('transfer', 'catalog_transfer', 'avtoparkOrderTransfer');

	/*
	12. Необходимо настроить новую цель на кнопку "мы перезвоним" в футере
	сайта, сделать ее необходимо как составную цель в 2 шага:
	Шаг 1 - клик на кнопку "мы перезвоним"
	Шаг 2 - клик на кнопку "заказать" из формы
	*/
	$("input[type='button'][value='Мы перезвоним']").on('click', function(){
		yaMetrikaGoal('CallBackButtonClick');
	});

	/*
	13. Необходимо настроить составную цель в 3 шага, на категорию: "Прокат
	автомобилей премиум класса".
	1 шаг - Клик на категорию "Прокат автомобилей премиум класса"
	2 шаг - Клик на кнопку "заказать" из раздела
	3 шаг - Клик на кнопку "заказать" из открывшейся формы.
	*/
	catalogGoal('prokat_avtomobilei_premium-klassa', 'rent_premium', 'rentPremiumClass');

	/*
	14. Необходимо настроить составную цель в 3 шага, на категорию: "Аренда внедорожников"
	1 шаг - Клик на категорию "Аренда внедорожников"
	2 шаг - Клик на кнопку "заказать" из раздела
	3 шаг - Клик на кнопку "заказать" из открывшейся формы.
	*/
	catalogGoal('arenda_vnedorojnikov', 'offRoad', 'offRoad');

	/*
	15. Необходимо настроить составную цель в 3 шага, на категорию: "Прокат минивенов"
	1 шаг - Клик на категорию "Прокат минивенов"
	2 шаг - Клик на кнопку "заказать" из раздела
	3 шаг - Клик на кнопку "заказать" из открывшейся формы.
	*/
	catalogGoal('prokat_minivenov', 'minivan', 'minivan');

	$("#order-form").on('submit', function(){
		// if($("#orderModal").find("#contact-form-action").val() == 'callback'){
			yaMetrikaGoal('submitForm');
		// }
	});
});

function getUrlParameterString(){
	var sPageURL = '';
	isHashUrlMarker = window.location.hash.substr(1,1);
	if(isHashUrlMarker == '!'){
		url = window.location.hash.substr(2);
		if(url){
			url = url.split('?');
			if(url[1]){
				sPageURL = url[1] += '&';
			}
		}
	}
	sPageURL += window.location.search.substring(1);
	return sPageURL;
}

function getUrlParameter(sParam)
{
	var url = '';

	sPageURL = getUrlParameterString();
	if(!sPageURL) return;

    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++)
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam)
        {
            return sParameterName[1];
        }
    }
    return null;
}

function catalogGoal(pageUrl, urlParam, yaGoal){
	$('.item-holder').find("a[href='/praisi/"+pageUrl+"']").attr('href','/praisi/'+pageUrl+'?from='+urlParam);
	$('.item-holder').find("a[href='praisi/"+pageUrl+"']").attr('href','praisi/'+pageUrl+'?from='+urlParam);
	$('.item-holder').find("a.order_container").on('click', function(){
		if(getUrlParameter('from') == urlParam){
			yaMetrikaGoal(yaGoal);
		}
	});
}

function yaMetrikaGoal(goalID){
	console.log('YA Metrika. Goal reached: '+goalID);
	yaCounter17949775.reachGoal(goalID);
}
