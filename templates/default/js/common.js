$(document).ready(function(){var phones=$('div.info2').find('strong');$(phones).each(function(index,element){if(index!=0){var innerText=$(element).html();var phone=innerText.replace(/-/g,"");innerText='<a href="tel:+7343'+phone+'">'+innerText+'</a>';if($.isNumeric(phone)){$(element).html(innerText);}}});var phones=$('div.info1').find('strong');$(phones).each(function(index,element){if(index!=0){var innerText=$(element).html();var phone=innerText.replace(/-/g,"");innerText='<a href="tel:+7343'+phone+'">'+innerText+'</a>';if($.isNumeric(phone)){$(element).html(innerText);}}});var phones=$('dl.phone').find('strong');$(phones).each(function(index,element){var innerText=$(element).html();var phone=innerText.replace(/-/g,"");innerText='<a href="tel:+7343'+phone+'">'+innerText+'</a>';if($.isNumeric(phone)){$(element).html(innerText);}});});