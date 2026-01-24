function OCTgeturlparam(param) {
	var urlParams = new URLSearchParams(window.location.search);
	return urlParams.get(param) !== null ? param+'='+urlParams.get(param) : '';
}
var OCT4_GET_LANG = '';
var prdoptcol_ur1201 = 'index.php?route=module/prdoptcol/';
var prdoptcol_url230 = 'index.php?route=extension/module/prdoptcol/';
var prdoptcol_url401 = 'index.php?route=extension/prdoptcol/module/prdoptcol|';
var prdoptcol_url402 = 'index.php?route=extension/prdoptcol/module/prdoptcol.';

var prdoptcol = {	
	'atgrid': function() {
  		var product_ids = [];
 		$("[onclick*='cart.add'], [onclick*='addToCart'], .product-thumb form input[name='product_id']").each(function() {
			if($(this).closest('form').find("[name*='product_id']").length) {
				var target = $(this).closest('.product-thumb, .product-wrapper');
				var product_id = $(this).val(); 
			} else {
				var target = $(this).closest('.product-thumb, .product-wrapper');
				var product_id = $(this).attr('onclick').match(/[0-9]+/).toString(); 
			}
			if(target.length && target.hasClass('prdoptcol_atgrid') == false) {
				target.addClass('prdoptcol_atgrid').addClass('prdoptcol_atgrid'+product_id);
				product_ids.push(product_id);
 			}
		});
		if(product_ids.length) { 
			//console.log(product_ids.length);
 			$.ajax({
				url: prdoptcol_url230 + 'getcache' + OCT4_GET_LANG,
				type: 'post',
				data: {product_ids:product_ids},
				cache: true,
				complete: function() { 
				},
				success: function(json) {
					if(json && json['grid']) {
						//console.log(json);
						$("[onclick*='cart.add'], [onclick*='addToCart'], .product-thumb form input[name='product_id']").each(function() {
 							if($(this).closest('form').find("[name*='product_id']").length) {
								var target = $(this).closest('.product-thumb, .product-wrapper');
								var product_id = $(this).val(); 
							} else {
								var target = $(this).closest('.product-thumb, .product-wrapper');
								var product_id = $(this).attr('onclick').match(/[0-9]+/).toString(); 
							}
							if(json['grid'][product_id] && target.length && target.hasClass('prdoptcol') == false) {
								target.addClass('prdoptcol').find('.price').after(json['grid'][product_id].join(""));
 							}
						});
					}
				}
			});
		}		
  	},	
	'prodpage': function() {	
		var product_ids = [];
		var product_id = false;
		
		if($(".button-group-page input[name='product_id']").length) {
			product_id = $(".button-group-page input[name='product_id']").val();
			product_ids.push(product_id);
		} else if($('#input-product-id').length) {
			product_id = $('#input-product-id').val();
			product_ids.push(product_id);
		} else if($(".product-info input[name='product_id']").length) {
			product_id = $(".product-info input[name='product_id']").val();
			product_ids.push(product_id);
		} else if($("#product input[name='product_id']").length) {
			product_id = $("#product input[name='product_id']").val();
			product_ids.push(product_id);
		} 
			
		if (product_ids.length) {
			$.ajax({
				url: prdoptcol_url230 + 'getcache' + OCT4_GET_LANG,
				type: 'post',
				data: {product_ids:product_ids},
				cache: true,
				complete: function() { 
				},
				success: function(json) {
					if(json && json['prdpg']) {
						var target = $('#product .form-group').eq(0);
						var target_OC4 = $('#product .mb-3').eq(0);
						var target_J2 = $('#product .form-group').eq(0);
						var target_J3 = $('.button-group-page');
 												
						if(json['prdpg'][product_id]) {
							if(target.length && target.hasClass('prdoptcol') == false) {
								target.addClass('prdoptcol').before(json['prdpg'][product_id].join(""));	
							}
							if(target_J2.length && target_J2.hasClass('prdoptcol') == false) {
								target_J2.addClass('prdoptcol').before(json['prdpg'][product_id].join(""));	
							}
							if(target_J3.length && target_J3.hasClass('prdoptcol') == false) {
								target_J3.addClass('prdoptcol');
								if($('.product-options h3.options-title').length) {
									$('.product-options h3.options-title').after(json['prdpg'][product_id].join(""));
								} else {
									target_J3.before('<div class="product-options">' + json['prdpg'][product_id].join("") + '</div>');		
								}
							}
							if(target_OC4.length && target_OC4.hasClass('prdoptcol') == false) {
								target_OC4.addClass('prdoptcol').before(json['prdpg'][product_id].join("")); 
							}
						}
					}
				}
			});
 		}
  	},
 	'initjson': function() {
		prdoptcol.atgrid(); 
		$(document).ajaxStop(function() { prdoptcol.atgrid();  });
		prdoptcol.prodpage();
	}	
}
$(document).ready(function() {
prdoptcol.initjson();

// FOR GRID LIST
$(document).delegate('.prdoptcol_radio_griddiv', 'click', function() {
	if($(this).find('input[type="checkbox"]').length) {
		if($(this).find('input[type="checkbox"]').prop('checked')) {
			$(this).find('.prdoptcol_atgrid').removeClass('act');
			$(this).find('input[type="checkbox"]').prop("checked",false).trigger('change');
		} else {
			$(this).find('.prdoptcol_atgrid').addClass('act');
			$(this).find('input[type="checkbox"]').prop("checked",true).trigger('change');
		}
	} else {
		$(this).closest('.form-group, .mb-3').find('.act').removeClass('act');
		$(this).find('.prdoptcol_atgrid').addClass('act');
		$(this).find('input[type="radio"]').prop("checked",true).trigger('change');
	}
	
	var grdimg = $(this).find('.prdoptcol_swpimggrd').val();
	var target = $(this).closest('.product-thumb, .product-wrapper').find('.image'); 
	if(grdimg != '') {	
		target.find('img.img-responsive, img.img-fluid, img.first-image').attr('src', grdimg); 
		target.find('img.img-responsive, img.img-fluid, img.first-image').attr('srcset', grdimg); 
		target.find('img.img-responsive, img.img-fluid, img.first-image').parent().removeClass('has-second-image');
	}
});
$(document).delegate('.prdoptcol_gridbtnrst', 'click', function() {
	$(this).closest('.form-group, .mb-3').find('.act').removeClass('act');
	$(this).closest('.form-group, .mb-3').find('input.prdoptcol_radio_input').prop("checked",false).trigger('change');	
	
	var orgimg = $(this).closest('.form-group, .mb-3').find('.prdoptcol_orgimggrd').val();
	var target = $(this).closest('.product-thumb, .product-wrapper').find('.image'); 	
	target.find('img.img-responsive, img.img-fluid, img.first-image').attr('src', orgimg);
	target.find('img.img-responsive, img.img-fluid, img.first-image').attr('srcset', orgimg); 
	target.find('img.img-responsive, img.img-fluid, img.first-image').parent().removeClass('has-second-image');
});


// FOR Product Details Page
$(document).delegate('.prdoptcol_radio_prdpgdiv', 'click', function() {
	if($(this).find('input[type="checkbox"]').length) {
		if($(this).find('input[type="checkbox"]').prop('checked')) {
			$(this).find('.prdoptcol_atprd').removeClass('act');
			$(this).find('input[type="checkbox"]').prop("checked",false).trigger('change');
		} else {
			$(this).find('.prdoptcol_atprd').addClass('act');
			$(this).find('input[type="checkbox"]').prop("checked",true).trigger('change');
		}
	} else {
		$(this).closest('.form-group, .mb-3').find('.act').removeClass('act');
		$(this).find('.prdoptcol_atprd').addClass('act');
		$(this).find('input[type="radio"]').prop("checked",true).trigger('change');
	}
	
	if($(this).find('.prdoptcol_atprd').attr('data-original-title')) { 
		$(this).closest('.form-group, .mb-3').find('.prdoptcol_showtitle').remove();
		$(this).closest('.form-group, .mb-3').find('label').after('<div class="prdoptcol_showtitle">'+$(this).find('.prdoptcol_atprd').attr('data-original-title')+'</div>');
	}
	
	// Additional Image swapper
	var col_addimg = $(this).find('.prdoptcol_addimg').val();
	var col_addimg_pop = $(this).find('.prdoptcol_addimgpop').val();
	prdoptcol_imgswap_addimg(col_addimg, col_addimg_pop);

	// Main image swapper
	var prdimg = $(this).find('.prdoptcol_swpimgprd').val();
	var prdimgpop = $(this).find('.prdoptcol_swpimgpopprd').val();		
	if(prdimg != '') { prdoptcol_imgswapper(prdimg, prdimgpop); }	
});	
$(document).delegate('.prdoptcol_prdpg_btnrst', 'click', function() {
	$(this).closest('.form-group, .mb-3').find('.act').removeClass('act');
	$(this).closest('.form-group, .mb-3').find('input.prdoptcol_radio_input').prop("checked",false).trigger('change');			
	
	$(this).closest('.form-group, .mb-3').find('.prdoptcol_showtitle').remove();
	
	// Additional Image swapper
	$('li.image-additional, .div.image-additional, .div.additional-image, #product-info .magnific-popup div a').removeClass('prdoptcol_hide_addimg');
	
	$('.col_addimg').remove();
	
	// Main image swapper
	var prdimg = $(this).closest('.form-group, .mb-3').find('.prdoptcol_orgimgprd').val();
	var prdimgpop = $(this).closest('.form-group, .mb-3').find('.prdoptcol_orgimgpopprd').val();		
	if(prdimg != '') { prdoptcol_imgswapper(prdimg, prdimgpop); }	
});

});

function prdoptcol_imgswapper(prdimg, prdimgpop) {
	 // J3
	if($('.product-left .main-image .swiper-slide-active').length) {
		target = $('.product-left .main-image .swiper-slide-active');
		
		target.find('img').attr('src', prdimg);
		target.find('img').attr('srcset', prdimgpop);
		target.find('img').attr('data-zoom-image', prdimgpop);
		target.find('img').attr('data-largeimg', prdimgpop);
		$('.zoomWindowContainer div').stop().css("background-image","url("+ JSON.stringify(String(prdimgpop)) +")");		
		
		$(document).delegate('.additional-image', 'click', function () {
			$('.additional-image').removeClass('swiper-slide-active');
			$(this).addClass('swiper-slide-active');

			var $s = $('.main-image').data('swiper');

			if ($s.params.loop) {
				$s.slideToLoop($(this).data('index'), 0);
			} else {
				$s.slideTo($(this).data('index'), 0);
			}
		});
		$('.main-image img').each(function () {
			var $this = $(this);

			$this.ImageZoom({
				type: Journal['isPopup'] ? Journal['quickviewPageStyleCloudZoomPosition'] : Journal['productPageStyleCloudZoomPosition'],
				showDescription: false,
				offset: [0, 0],
				zoomSize: [$this.width(), $this.height()],
				bigImageSrc: $this.data('largeimg')
			});
		});
	}
	
	target = $('ul.thumbnails li:first-child a');
	
	if($('.product-info #image').length) { // J2
		target = $('.product-info #image').parent();
	}
	
	if($('#product-info .magnific-popup').length) { // OC4
		target = $('#product-info .magnific-popup a:eq(0)');
	}
	
	target.find('img').attr('src', prdimg);
	target.attr('href', prdimgpop);
	target.find('img').attr('data-zoom-image', prdimgpop);
	$('.zoomWindowContainer div').stop().css("background-image","url("+ JSON.stringify(String(prdimgpop)) +")");
	$('.zm-viewer img').attr('src', prdimgpop);					
	setTimeout(function(){ 
		$('.zm-viewer img').attr('src', prdimgpop);
	}, 1000);
}
function prdoptcol_imgswap_addimg(col_addimg, col_addimg_pop) {
	if(col_addimg) {					
		col_addimg = col_addimg.split(",");
		col_addimg_pop = col_addimg_pop.split(",");
		
		if($('li.image-additional').length) { 
			$('li.image-additional').addClass('prdoptcol_hide_addimg');
			$('.col_addimg').remove();
		
			for (var cnt = 0; cnt < col_addimg.length; cnt++) {
				if(col_addimg[cnt] != '') { 
					$('ul.thumbnails li:first-child a').closest('ul').append('<li class="image-additional col_addimg"><a class="thumbnail" href="'+col_addimg_pop[cnt]+'"> <img src="'+col_addimg[cnt]+'"></a></li>');
				}
			}
		}
		
		// OC4
		if($('#product-info .magnific-popup div').length) {
			$('#product-info .magnific-popup div a').addClass('prdoptcol_hide_addimg');
			$('.col_addimg').remove();
		
			for (var cnt = 0; cnt < col_addimg.length; cnt++) {
				if(col_addimg[cnt] != '') { 
				$('#product-info .magnific-popup div').append('<a class="col_addimg" href="'+col_addimg_pop[cnt]+'"> <img src="'+col_addimg[cnt]+'"></a>');
				}
			}
		}
		
		// J2
		if($('.product-info #image').length) { 			
			$('div.image-additional .swiper-slide').addClass('prdoptcol_hide_addimg');
			$('.col_addimg').remove();

			for (var cnt = 0; cnt < col_addimg.length; cnt++) {
				if(col_addimg[cnt] != '') { 
					$('div.image-additional .swiper-wrapper').append('<a class="swiper-slide col_addimg" style="width: 88px; margin-right: 10px;" href="'+col_addimg_pop[cnt]+'"> <img src="'+col_addimg[cnt]+'"></a>');
				}
			}
		}
		
		// J3
		if($('.product-left .main-image .swiper-slide-active').length) { 
			$('div.additional-image').addClass('prdoptcol_hide_addimg');
			$('.col_addimg').remove();
			
			var addimg_html = '';
			var addimg_html_slider = '';
			for (var cnt = 0; cnt < col_addimg.length; cnt++) {
				if(col_addimg[cnt] != '') { 
					random_index = cnt + (parseInt($(".additional-image").length));//Math.floor(Math.random() * 100 * 100);
					addimg_html += '<div class="swiper-slide additional-image col_addimg" data-index="'+(random_index)+'"><img src="'+col_addimg[cnt]+'" srcset="'+col_addimg_pop[cnt]+'" data-largeimg="'+col_addimg_pop[cnt]+'"></div>';
					addimg_html_slider += '<div class="swiper-slide col_addimg" data-gallery=".lightgallery-product-images" data-index="'+(random_index)+'"> <img src="'+col_addimg_pop[cnt]+'" srcset="'+col_addimg_pop[cnt]+'" data-largeimg="'+col_addimg_pop[cnt]+'" loading="lazy"> </div>';
				}
			}
			$('.additional-images .swiper-container .swiper-wrapper').append(addimg_html);		
			$('.product-left .main-image .swiper-wrapper').append(addimg_html_slider);	
			$('.product-left .main-image .swiper-wrapper').removeClass('swiper-slide-visible').removeClass('swiper-slide-active');			
		}
	}
}