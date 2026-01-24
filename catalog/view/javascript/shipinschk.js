function OCTgeturlparam(param) {
	var urlParams = new URLSearchParams(window.location.search);
	return urlParams.get(param) !== null ? param+'='+urlParams.get(param) : '';
}
var OCT4_GET_LANG = '';
var shipinschk_ur1201 = 'index.php?route=total/shipinschk/';
var shipinschk_url230 = 'index.php?route=extension/total/shipinschk/';
var shipinschk_url401 = 'index.php?route=extension/shipinschk/total/shipinschk|';
var shipinschk_url402 = 'index.php?route=extension/shipinschk/total/shipinschk.';

var shipinschk = {
	'initjson': function(flag) {
		$.ajax({
			url: shipinschk_url230 + 'getcache' + OCT4_GET_LANG,
			dataType: 'json',
			cache: true,
			success: function(json) {
 				$('.shipinschk_table').remove();
				if(json['tbl'] != '' && json['tbl'].length) {					
					setInterval(function(){
						if($('.shipinschk_table').length) {
							/*Do nothing if already exists*/
						} else {
							if($('#collapse-shipping-method').length) {
								$('#collapse-shipping-method .panel-body').prepend(json['tbl']); 
							} else if($('#checkout-shipping-method').length) { /*OC4*/
								$('#checkout-shipping-method').prepend(json['tbl']); 
							} else if($('div#agree-panel').length) { /*xcheckout*/ 
								$('div#agree-panel').before(json['tbl']); 
							} else if($('.checkout-cart').length) { /*J2*/
								$('.checkout-cart').before(json['tbl']); 
							} else if($('.cart-section').length) { /*J3*/
								$('.cart-section').before(json['tbl']); 
							}
						}						
					}, 100);
				}
 			}
		});
	},
	'save': function() {
		$.ajax({
			url: shipinschk_url230 + 'save' + OCT4_GET_LANG,
			type: 'post',
			data: $('input[name*="shipinschkradio"]:checked, input[name*="shipinschk_fee"], input[name*="shipinschk_ratetype"], input[name*="shipinschk_tax_class_id"]'),
			dataType: 'json',
			beforeSend: function() {
				$('.shipinschkradio').before('<i class="fa fa-spinner fa.spin" aria-hidden="true" style="font-size:16px;"></i>');
			},
			complete: function() {
				$('.shipinschk_table').find('.fa-spinner').remove();
			},
			success: function(json) {
				if(json) {
					$('.shipinschk_table').prepend(json['msg']);
					setTimeout(function(){ $('.shipinschk_table .label').remove(); }, 2000);				
					
					if($('.cart-section').length) { /*J3*/							
						 _QuickCheckout.save();
					} else if($('.checkout-cart').length) { /*J2*/ 
						$(document).trigger('journal_checkout_reload_cart');
					} else if($('#collapse-shipping-method').length) { /*OC DEFAULT*/
						//$('#collapse-checkout-confirm .panel-body').load('index.php?route=checkout/confirm');
					} else if($('#checkout-shipping-method').length) { /*OC4*/
						$('#checkout-confirm').load('index.php?route=checkout/confirm.confirm');
					} else if($('div#shipping_method').length) { /*xcheckout*/
						$.ajax({
							url: shipinschk_url230 + 'ocxresponse' + OCT4_GET_LANG,
							dataType: 'json',
							success: function(json) {
								if(json){ $('#loginPage #totals , #addressPage #totals , .footer_totals .totals , #guestPage #totals, #paymentPage #totals').html(json.xtotals); }
							}
						});
					}				
				}
			}
		});
	},			
}
$(document).ready(function() {
	$('.shipinschk_table').remove();
	setTimeout(function(){ shipinschk.initjson(); }, 1000);
});
$(document).delegate('input[name*="shipinschkradio"]', 'click', function() {	
	shipinschk.save();
});