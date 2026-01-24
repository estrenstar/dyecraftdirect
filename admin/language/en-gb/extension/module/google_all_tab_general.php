<?php
$_['tab_general']                                   = 'General';
$_['legend_general']                                = 'General status';
$_['status']                                        = 'Google Marketing Tools status';
$_['help']                                          = 'Click to enable this plugin and allow functionality of the below configurations.<br><b>IMPORTANT:</b> Remember to generate a workspace in tab "<b><a href="javascript:{}" onclick="open_tab_gtm_workspace();">GTM Workspace Generator</a></b>" before enabling this.';
$_['legend_google_tag_manager']                     = 'Google Tag Manager - Configuration <b>REQUIRED</b>';
$_['container_id']                                  = 'GTM Container ID';
$_['container_id_help']                             = '<b>REQUIRED</b>. Login into Tag Manager panel and complete the following <b>GTM-XXXXXXX</b>';
$_['google_analytics_ee_legend']                    = 'GA4 Enhanced Ecommerce - Configuration';
$_['google_ee_product_id_like']                     = 'Product ID';
$_['google_ee_product_id_like_help']                = 'The product identificator selected will be sent to <b>GA4 Enhanced Ecommerce</b>. If the identificator is not found, the <b>Product ID</b> is set by default.';
$_['google_ads_legend']                             = 'Google Ads / Dynamic remarketing - Configuration';
$_['google_product_id_like']                        = 'Product Identification';
$_['google_product_id_like_help']                   = 'Select what unique value is used to identify your products for tracking <b>Most commonly product ID</b>. If identificator not found, <b>Product ID</b> is set by default.';
$_['dynamic_remarketing_dynx2']                     = 'Secondary Product Identification';
$_['dynamic_remarketing_dynx2_product_id']          = 'Product ID';
$_['dynamic_remarketing_dynx2_model']               = 'Model';
$_['dynamic_remarketing_dynx2_sku']                 = 'SKU';
$_['dynamic_remarketing_dynx2_upc']                 = 'UPC';
$_['dynamic_remarketing_dynx2_ean']                 = 'EAN';
$_['dynamic_remarketing_dynx2_jan']                 = 'JAN';
$_['dynamic_remarketing_dynx2_isbn']                = 'ISBN';
$_['dynamic_remarketing_dynx2_mpn']                 = 'MPN';
$_['dynamic_remarketing_dynx2_location']            = 'Location';
$_['fb_pixel_legend']                               = 'Facebook Pixel / API conversions - Configuration';
$_['google_reviews_legend']                         = 'Google Reviews - Configuration';
$_['google_reviews_gtin']                           = 'Product GTIN';
$_['google_reviews_gtin_help']                      = 'Required from some countries/feeds. Choose GTIN used in your feed.';
$_['google_reviews_gtin_product_id']                = 'Product ID';
$_['google_reviews_gtin_mpn']                       = 'MPN';
$_['google_reviews_gtin_model']                     = 'Model';
$_['google_reviews_gtin_ean']                       = 'EAN';
$_['google_reviews_gtin_upc']                       = 'UPC';
$_['2performant_legend']                            = '2Performant - Conversion pixel';
$_['2performant_status']                            = '2Performant status';
$_['2performant_campaign_id']                       = 'Campaign ID';
$_['2performant_campaign_id_help']                  = 'You received it by email.';
$_['2performant_confirm_id']                        = 'Confirm ID';
$_['2performant_confirm_id_help']                   = 'You received it by email.';
$_['domodi_legend']                                 = 'Domodi - Pixel settings';
$_['domodi_size_options']                           = 'Get Size from';
$_['domodi_size_options_help']                      = 'The event <b>Add to cart</b> expects a list of sizes: s,m,xl... mark the options where you are saving product sizes.';
$_['another_configuration_legend']                  = 'Generic configuration';
$_['debug_mode']                                    = 'Debug mode';
$_['debug_mode_help']                               = 'When you are logged like admin user, a popoup will appear in frontend zone, which show dataLayer sent to Google tag manager and Facebook conversions API queries';
$_['cache_compatibility']                           = 'Cache Compatibility';
$_['cache_compatibility_help']                      = "If you think you are using any cache system in your Opencart store or server, check this box to avoid potential issues. If you are not currently noticing any problems, there is no need to enable this option, as it might slightly reduce your site's performance.";
$_['positive_conversion_status_id']                 = 'Conversions: Order status for tracking';
$_['positive_conversion_status_id_help']            = 'When a customer finishes their order, the conversion will be triggered if their order status marked in this list (if empty, all order statuses will be considered valid). <b>IF YOU DON\'T KNOW DEFAULT ORDER STATUS AFTER ORDER IS CREATED, DO NOT SELECT ANY OPTION, CONVERSIONS WILL NOT BE TRACKED.</b>';
$_['custom_url_checkout_cart']                      = 'Custom checkout/cart url';
$_['custom_url_checkout_cart_help']                 = 'If your checkout/cart url is not <b>'.HTTPS_CATALOG.'index.php?route=checkout/cart</b>, you can set in this field your custom urls separated by comma, examples:<br>
    - '.HTTPS_CATALOG.'<b>view-cart</b><br><br>
    In this example, you would have to set:  "<b>view-cart</b>"
    ';

$_['custom_url_checkout_checkout']                  = 'Custom checkout/checkout url';
$_['custom_url_checkout_checkout_help']             = 'If your checkout/checkout url is not <b>'.HTTPS_CATALOG.'index.php?route=checkout/checkout</b>, you can set in this field your custom urls separated by comma, examples:<br>
    - '.HTTPS_CATALOG.'<b>finish-checkout</b><br>
    In this example, you would have to set:  "<b>finish-checkout</b>"
    ';

$_['custom_url_checkout_success']                   = 'Custom checkout/success url';
$_['custom_url_checkout_success_help']              = 'If your checkout/success url is not <b>'.HTTPS_CATALOG.'index.php?route=checkout/success</b>, you can set in this field your custom urls separated by comma, examples:<br>
    - '.HTTPS_CATALOG.'index.php?route=<b>visa/success</b><br>
    - '.HTTPS_CATALOG.'<b>order-success</b><br><br>
    
    In this example, you would have to set:  "<b>visa/success,order-success</b>"
    ';
?>