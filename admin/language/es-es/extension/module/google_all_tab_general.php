<?php
$_['tab_general']                                   = 'General';
$_['legend_general']                                = 'Estado General';
$_['status']                                        = 'Estado - Google Marketing Tools';
$_['help']                                          = 'Haga clic para habilitar este complemento y permitir la funcionalidad de las siguientes configuraciones.<br><b>IMPORTANTE:</b> Recuerde generar un espacio de trabajo en la pestaña "<b><a href="javascript:{}" onclick="open_tab_gtm_workspace();">GTM Workspace Generator</a></b>" antes de habilitar esta área.';
$_['legend_google_tag_manager']                     = 'Configuración - Google Tag Manager <b>REQUERIDO</b>';
$_['container_id']                                  = 'GTM Container ID';
$_['container_id_help']                             = '<b>REQUERIDO</b>. Inicie sesión en el panel de Tag Manager y complete el siguiente campo <b>GTM-XXXXXXX</b>';
$_['google_analytics_ee_legend']                    = 'Configuración - GA4 Enhanced Ecommerce';
$_['google_ee_product_id_like']                     = 'ID de producto';
$_['google_ee_product_id_like_help']                = 'El identificador del producto seleccionado se enviará para <b>GA4 Enhanced Ecommerce</b>. Si no se encuentra el identificador, el <b>ID de Producto</b> está configurado por defecto.';
$_['google_ads_legend']                             = 'Configuración - Google Ads / Dynamic remarketing';
$_['google_product_id_like']                        = 'Identificador de producto';
$_['google_product_id_like_help']                   = 'Seleccione qué valor único se utiliza para identificar sus productos para el seguimiento <b>ID de producto más común</b>. Si no se encuentra el identificador, el <b>ID de Producto</b> está configurado por defecto.';
$_['dynamic_remarketing_dynx2']                     = 'Identificación secundario del producto';
$_['dynamic_remarketing_dynx2_product_id']          = 'ID de producto';
$_['dynamic_remarketing_dynx2_model']               = 'Referencia';
$_['dynamic_remarketing_dynx2_sku']                 = 'SKU';
$_['dynamic_remarketing_dynx2_upc']                 = 'UPC';
$_['dynamic_remarketing_dynx2_ean']                 = 'EAN';
$_['dynamic_remarketing_dynx2_jan']                 = 'JAN';
$_['dynamic_remarketing_dynx2_isbn']                = 'ISBN';
$_['dynamic_remarketing_dynx2_mpn']                 = 'MPN';
$_['dynamic_remarketing_dynx2_location']            = 'Localización';
$_['fb_pixel_legend']                               = 'Facebook Pixel / API conversions - Configuración';
$_['google_reviews_legend']                         = 'Configuración - Google Reviews';
$_['google_reviews_gtin']                           = 'Producto GTIN';
$_['google_reviews_gtin_help']                      = 'Obligatorio en algunos países / feeds. Seleccionar GTIN para ser usado en su feed.';
$_['google_reviews_gtin_product_id']                = 'ID del producto';
$_['google_reviews_gtin_mpn']                       = 'MPN';
$_['google_reviews_gtin_model']                     = 'Referencia';
$_['google_reviews_gtin_ean']                       = 'EAN';
$_['google_reviews_gtin_upc']                       = 'UPC';
$_['2performant_legend']                            = '2Performant - Conversión pixel';
$_['2performant_status']                            = 'Estado de 2Performant';
$_['2performant_campaign_id']                       = 'ID de campaña';
$_['2performant_campaign_id_help']                  = 'Lo recibiste por correo electrónico.';
$_['2performant_confirm_id']                        = 'Confirmar ID';
$_['2performant_confirm_id_help']                   = 'Lo recibiste por correo electrónico.';
$_['domodi_legend']                                 = 'Domodi - Configuración de Pixel';
$_['domodi_size_options']                           = 'Obtener tamaño de';
$_['domodi_size_options_help']                      = 'El evento <b>Añadir a la cesta</b> espera una lista de tamaños: s,m,xl... marque las opciones en las que está guardando tamaños de productos.';
$_['another_configuration_legend']                  = 'Configuración genérica';
$_['debug_mode']                                    = 'Modo Debug';
$_['debug_mode_help']                               = 'Al iniciar sesión como usuario administrador, aparecerá un popoup en la zona del frontend, que muestra el dataLayer enviado a Google tag manager y las consultas de la API de Facebook conversions';
$_['cache_compatibility']                           = 'Compatibilidad de Caché';
$_['cache_compatibility_help']                      = "Si crees que estás utilizando algún sistema de caché en tu tienda Opencart o servidor, marca esta casilla para evitar posibles problemas. Si actualmente no estás notando ningún problema, no hay necesidad de habilitar esta opción, ya que podría reducir ligeramente el rendimiento de tu sitio.";
$_['positive_conversion_status_id']                 = 'Conversiones: Estado de pedido para tracking';
$_['positive_conversion_status_id_help']            = 'Cuando un cliente finaliza su pedido, la conversión se activará, si el estado de su pedido está marcado en esta lista (si está vacío, todos los estados de los pedidos se considerarán válidos).<br><b>SI NO CONOCE EL ESTADO DEL PEDIDO POR DEFECTO DESPUÉS DE CREAR EL PEDIDO, NO SELECCIONE NINGUNA OPCIÓN, NO SE SEGUIRÁ LAS CONVERSIONES.</b>';
$_['custom_url_checkout_cart']                      = 'URL personalizada de checkout/cart';
$_['custom_url_checkout_cart_help']                 = 'Si la URL de checkout/cart no es <b>'.HTTPS_CATALOG.'index.php?route=checkout/cart</b>, puede establecer en este campo sus URLs personalizadas separadas por comas, ejemplo:<br>
    - '.HTTPS_CATALOG.'<b>view-cart</b><br><br>
    En este ejemplo, tendría que configurar:  "<b>view-cart</b>"
    ';

$_['custom_url_checkout_checkout']                  = 'URL personalizada de checkout/checkout';
$_['custom_url_checkout_checkout_help']             = 'Si la URL de checkout/checkout no es <b>'.HTTPS_CATALOG.'index.php?route=checkout/checkout</b>, puede establecer en este campo sus URLs personalizadas separadas por comas, ejemplo:<br>
    - '.HTTPS_CATALOG.'<b>finish-checkout</b><br>
    En este ejemplo, tendría que configurar:  "<b>finish-checkout</b>"
    ';

$_['custom_url_checkout_success']                   = 'URL personalizada de checkout/success';
$_['custom_url_checkout_success_help']              = 'Si la URL de checkout/success no es <b>'.HTTPS_CATALOG.'index.php?route=checkout/success</b>, puede establecer en este campo sus URLs personalizadas separadas por comas, ejemplo:<br>
    - '.HTTPS_CATALOG.'index.php?route=<b>visa/success</b><br>
    - '.HTTPS_CATALOG.'<b>order-success</b><br><br>
    
    En este ejemplo, tendría que configurar:  "<b>visa/success,order-success</b>"
    ';
?>