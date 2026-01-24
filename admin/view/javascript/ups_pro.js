//==============================================================================
// UPS Pro v2025-6-25
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

function getQueryVariable(variable) {
	var vars = window.location.search.substring(1).split('&');
	for (i = 0; i < vars.length; i++) {
		var pair = vars[i].split('=');
		if (pair[0] == variable) return pair[1];
	}
	return false;
}

var upsProSeparator;
var upsProRoute;
var upsProToken;

$(document).ready(function(){
	var route = getQueryVariable('route');
	
	if (route.indexOf('.') != -1 || (route == 'sale/order' && $('.float-end').length)) {
		upsProSeparator = '.';
	} else if (route.indexOf('|') != -1) {
		upsProSeparator = '|';
	} else {
		upsProSeparator = '/';
	}
	
	upsProRoute = 'extension/' + (upsProSeparator == '/' ? '' : 'ups_pro/') + 'shipping/ups_pro' + upsProSeparator;
	
	var token = getQueryVariable('token');
	upsProToken = (token) ? '&token=' + token : '&user_token=' + getQueryVariable('user_token');
	
	if (route == 'sale/order') {
		return;
	}
	
	$.ajax({
		type: 'GET',
		url: 'index.php?route=' + upsProRoute + 'addButton' + upsProToken,
		success: function(data) {
			if (data.indexOf('createUpsLabel') != -1) {
				$('#ups-pro-script').after(data);
			}
		},
		error: function(xhr, status, error) {
			alert(xhr.responseText ? xhr.responseText : error);
		}
	});
});

function createUpsLabel(element, order_id) {
	if (!confirm('This operation cannot be undone. Continue?')) return;
	
	var orderListPage = true;
	if (!order_id) {
		orderListPage = false;
		order_id = getQueryVariable('order_id');
	}
	
	$.ajax({
		type: 'POST',
		url: 'index.php?route=' + upsProRoute + 'createShippingLabel' + upsProToken,
		data: {order_id: order_id},
		beforeSend: function() {
			element.attr('style', 'opacity: 0.5; pointer-events: none;').find('i').addClass('fa-spin');
		},
		success: function(data) {
			element.removeAttr('style').find('i').removeClass('fa-spin');
			
			$('body').append('<div id="overlay" onclick="$(\'#overlay, #popup\').fadeOut().remove()" style="display: none; background: #000; opacity: 0.5; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 99999;"></div><div id="popup" style="display: none; background: #FFF; border-radius: 5px; box-shadow: 0 5px 15px rgb(0 0 0 / 50%); max-height: 80%; overflow: scroll; overflow-x: auto; overflow-y: auto; padding: 0 15px 15px 15px; position: fixed; top: 15%; left: 0; right: 0; margin-left: auto; margin-right: auto; width: 370px; z-index: 100000;"><span onclick="$(\'#overlay, #popup\').fadeOut().remove()" style="cursor: pointer; float: right; font-size: 24px;">&times;</span><br>' + data + '</div>');
			
			$('#overlay, #popup').fadeIn();
			
			if (orderListPage) {
				$('a[onclick^="cancelUpsShipment"], a[onclick^="sendUpsCustomerEmail"]').remove();
			} else {
				$('#history').load('index.php?route=sale/order' + upsProSeparator + 'history&order_id=' + order_id + upsProToken);
			} 
		},
		error: function(xhr, status, error) {
			alert(xhr.responseText ? xhr.responseText : error);
		}
	});
}

function cancelUpsShipment(element, shipment_number) {
	if (!confirm('This operation cannot be undone. Continue?')) return;
	
	var order_id = getQueryVariable('order_id');
	
	$.ajax({
		type: 'POST',
		url: 'index.php?route=' + upsProRoute + 'cancelShipment' + upsProToken,
		data: {order_id: order_id, shipment_number: shipment_number},
		beforeSend: function() {
			element.attr('style', 'opacity: 0.5; pointer-events: none; margin-top: 5px;');
		},
		success: function(data) {
			if (data) {
				alert(data);
			} else {
				alert('Success!');
				$('#overlay, #popup').fadeOut().remove();
				$('#history').load('index.php?route=sale/order' + upsProSeparator + 'history&order_id=' + order_id + upsProToken);
			}
		},
		error: function(xhr, status, error) {
			alert(xhr.responseText ? xhr.responseText : error);
		}
	});
}

function sendUpsCustomerEmail(element) {
	if (!confirm('This operation cannot be undone. Continue?')) return;
	
	var order_id = getQueryVariable('order_id');
	
	$.ajax({
		type: 'POST',
		url: 'index.php?route=' + upsProRoute + 'sendCustomerEmail' + upsProToken,
		data: {order_id: order_id},
		beforeSend: function() {
			element.attr('style', 'opacity: 0.5; pointer-events: none; margin-top: 5px;');
		},
		success: function(data) {
			if (data) {
				alert(data);
			} else {
				alert('Success!');
				$('#history').load('index.php?route=sale/order' + upsProSeparator + 'history&order_id=' + order_id + upsProToken);
			}
		},
		error: function(xhr, status, error) {
			alert(xhr.responseText ? xhr.responseText : error);
		}
	});
}
