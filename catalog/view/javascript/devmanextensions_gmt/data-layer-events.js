function googleMarketingTools_Ajax_Events_Setup() {
	if (typeof $ !== "undefined") {
		$(document).ajaxSuccess(function (event, xhr, settings, json) {
			if (settings.url.includes("checkout/cart/add") && !json.error) {
				// Measuring add to cart
				if (json['gtm_id']) {
					var object = googleMarketingTools_FormatJsonCart(json);
					_GoogleMarketingTools.trackAddToCart(object);
				}
			} else if (settings.url.includes("account/wishlist/add") && !json.error) {
				// Measuring add to wishlist
				if (json['gtm_id']) {
					var object = googleMarketingTools_FormatJsonCart(json);
					_GoogleMarketingTools.trackAddToWishlist(object);
				}
			} else if ((settings.url.includes("checkout/cart/remove") || settings.url.includes("extension/d_quickcheckout/cart/update")) && !json.error && typeof json['atc_id'] !== undefined) {
				// Measuring remove from cart
				if (json['gtm_id']) {
					var object = googleMarketingTools_FormatJsonCart(json);
					_GoogleMarketingTools.trackRemoveFromCart(object);
				}
			} else if ((settings.url.includes('checkout/payment_method/save') || settings.url.includes('checkout/payment_method/validate')) && !json.error && typeof json['payment_info_event'] !== 'undefined') {
				_GoogleMarketingTools.trackPaymentInformation(json.payment_info_event);
			} else if ((settings.url.includes('checkout/shipping_method/save') || settings.url.includes('checkout/shipping_method/validate')) && !json.error && typeof json['shipping_info_event'] !== 'undefined') {
				_GoogleMarketingTools.trackShippingInfo(json.shipping_info_event);
			}
		});
	} else {
		setTimeout(googleMarketingTools_Ajax_Events_Setup, 250);
	}
}

googleMarketingTools_Ajax_Events_Setup();

function googleMarketingTools_Start(result) {
	// Send main data to Google Marketing Tools
	var general_data = {
		'store_name': result.general_data.store_name,
		'lang': gmt_language,
		'current_view': result.general_data.current_view,
		'current_list': result.general_data.current_list,
		'current_currency': result.general_data.current_currency,
		'userId': result.general_data.userId,
		'clientId': result.general_data.clientId,
		'gclDc': result.general_data.gclDc,
		'email': result.general_data.email,
		'debug_enabled': this.debug_enabled,
		'event_id': result.general_data.event_id
	}

	_GoogleMarketingTools.init(general_data);

	if (typeof result.fb_pixel_track_info !== 'undefined') {
		_GoogleMarketingTools.setFacebookPixelTrack(result.fb_pixel_track_info);
	}

	if (typeof result.products_listed !== 'undefined') {
		_GoogleMarketingTools.setProducts(result.products_listed);
	}

	if (typeof result.promotions_listed !== 'undefined') {
		_GoogleMarketingTools.trackPromotionsListed(result.promotions_listed);
	}

	if (typeof result.general_data.cart_products !== 'undefined') {
		_GoogleMarketingTools.setCartProducts(result.general_data.cart_products);
	}

	if (typeof result.product_details !== 'undefined') {
		_GoogleMarketingTools.trackProductPage(result.product_details);
	}

	if (
		result.general_data.current_view == 'search' &&
		typeof result.general_data.string_searched !== 'undefined' &&
		typeof result.products_listed !== 'undefined' &&
		typeof result.products_listed) {
		_GoogleMarketingTools.trackSearchPage(result.general_data.string_searched);
	}

	if (result.general_data.current_view == 'category') {
		_GoogleMarketingTools.trackCategoryPage(result.general_data.current_categories);
	}

	if (result.general_data.current_view == 'homepage') {
		_GoogleMarketingTools.trackHomePage();
	}

	if (result.general_data.current_view == 'cart') {
		_GoogleMarketingTools.trackCartPage();
	}

	if (result.general_data.current_view == 'account_success') {
		var object = {};
		var user = googleMarketingTools_FormatJsonCustomer(result.customer);

		object.method = 'Email';

		if (typeof user != 'undefined') {
			object.user = user;
		}

		_GoogleMarketingTools.trackAccountRegistered(object);
	}

	if (result.general_data.current_view == 'contact_success') {
		var object = {};
		var user = googleMarketingTools_FormatJsonCustomer(result.customer);
		if (typeof user != 'undefined') {
			object.user = user;
		} else {
			object.user = '';
		}

		_GoogleMarketingTools.trackContactFormSubmit(object);
	}

	if (result.general_data.current_view == 'checkout') {
		_GoogleMarketingTools.trackCheckoutPage();
	}

	if (typeof result.order_data !== 'undefined') {
		_GoogleMarketingTools.trackPurchase(result.order_data);
	}

	if (typeof result.account_logged_event !== 'undefined') {
		var object = {};
		var user = googleMarketingTools_FormatJsonCustomer(result.account_logged_event);

		object.method = 'Email';

		if (typeof user != 'undefined') {
			object.user = user;
		}

		_GoogleMarketingTools.trackLogin(object);
	}

	_GoogleMarketingTools.checkIsNormalPage();
}

function googleMarketingTools_FormatJsonCart(json) {
	var object_formatted = {};

	object_formatted.id = typeof json['gtm_id'] != 'undefined' ? json['gtm_id'] : '';
	object_formatted.id_ee = typeof json['gtm_id_ee'] != 'undefined' ? json['gtm_id_ee'] : '';
	object_formatted.id_gdr_1 = typeof json['gtm_id_gdr_1'] != 'undefined' ? json['gtm_id_gdr_1'] : '';
	object_formatted.id_gdr_2 = typeof json['gtm_id_gdr_2'] != 'undefined' ? json['gtm_id_gdr_2'] : '';
	object_formatted.id_fb = typeof json['gtm_id_fb'] != 'undefined' ? json['gtm_id_fb'] : '';
	object_formatted.price = typeof json['gtm_price'] != 'undefined' ? json['gtm_price'] : '';
	object_formatted.price_eur = typeof json['gtm_price_eur'] != 'undefined' ? json['gtm_price_eur'] : '';
	object_formatted.name = typeof json['gtm_name'] != 'undefined' ? json['gtm_name'] : '';
	object_formatted.category = typeof json['gtm_category'] != 'undefined' ? json['gtm_category'] : '';
	object_formatted.brand = typeof json['gtm_brand'] != 'undefined' ? json['gtm_brand'] : '';
	object_formatted.quantity = typeof json['gtm_quantity'] != 'undefined' ? json['gtm_quantity'] : 1;
	object_formatted.variant = typeof json['gtm_variant'] != '' ? json['gtm_variant'] : '';
	object_formatted.image_url = typeof json['gtm_image'] != '' ? json['gtm_image'] : '';
	object_formatted.url = typeof json['url'] != '' ? json['url'] : '';
	object_formatted.sku = typeof json['gtm_sku'] != '' ? json['gtm_sku'] : '';
	object_formatted.fb_event_id = typeof json['fb_event_id'] != '' ? json['fb_event_id'] : '';

	return object_formatted;
}

function googleMarketingTools_FormatJsonCustomer(customer_info) {
	var user_formatted = {};

	if (typeof customer_info === 'undefined') {
		return user_formatted;
	}

	user_formatted.id = typeof customer_info['user_id'] != 'undefined' ? customer_info['user_id'] : '';
	user_formatted.email = typeof customer_info['email'] != 'undefined' ? customer_info['email'] : '';
	user_formatted.phone = typeof customer_info['phone'] != 'undefined' ? customer_info['phone'] : '';

	if (typeof customer_info['address'] != 'undefined') {
		user_formatted.address = customer_info['address'];
	}

	return user_formatted;
}

function gmt_rand(min, max) {
	if (min == 0) {
		return Math.floor((Math.random() * max) + 0);
	} else {
		return Math.floor(Math.random() * (max - min + 1)) + min;
	}
}

function removeFromCart_OC15(product_id_key, quantity) {
	$.ajax({
		url: 'index.php?route=extension/module/google_all/get_product_datas',
		data: { product_id_key: product_id_key, quantity: quantity },
		type: "POST",
		dataType: 'json',
		beforeSend: function () {
		},
		success: function (data) {
			if (data) {
				var object = googleMarketingTools_FormatJsonCart(data);
				_GoogleMarketingTools.trackRemoveFromCart(object);
				//eventDataLayerRemoveFromCart(data.atc_id, data.atc_price, data.atc_name, data.atc_category, data.atc_brand, data.atc_quantity, data.atc_variant);
			}
		},
		error: function (data) {
		},
	});
}

document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('a[href="#GMTCookiesSettings"]').forEach(function (link) {
		link.addEventListener('click', function (event) {
			event.preventDefault(); // Previene la acción por defecto del enlace
			_GoogleMarketingTools.googleConsentOpenConsentPopup();
		});
	});
});