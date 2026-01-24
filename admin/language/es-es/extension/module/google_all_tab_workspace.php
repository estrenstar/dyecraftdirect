<?php
$_['tab_workspace']                                 = 'GTM Workspace generator';
$_['workspace_explain']                             = '<p>En esta zona, obtendrá un <b>archivo de espacio de trabajo (workspace)</b> listo para trabajar. Los pasos son los siguientes:</p><br>
<ol>
    <li>Habilite y complete todas las etiquetas (tags)/estadísticas (statistics) que desee utilizar en su tienda.</li>
    <li>Pulse el botón inferior "<b>Generar Google Tag Manager Workspace</b>"</li>
    <li>La <b>API</b> le enviará su archivo Workspace <b>a su correo electrónico</b>. (Compruebe la carpeta SPAM).</li>
    <li><b>Importe</b> este archivo a su cuenta de <b>Google Tag Manager account</b>.</li>
    <li>Por último, presione el botón "<b>SUBMIT</b>" en su cuenta de <b>Google tag manager account</b> para publicar su contenido.</li>
</ol>
<p>Encontrará toda la información al respecto en la sección "<b>3.- Generate and import a workspace</b>" de <a href="https://devmanextensions.com/docs/en/opencart-extensions/google-marketing-tools" target="_blank">la documentación oficial</a>.</p>
<p><b><u>MUY IMPORTANTE</u></b>: Cuando quieras añadir/eliminar etiquetas o cambiar su configuración, tendrás que repetir estos pasos.</p>
';

// License Order ID
$_['license_id']                                    = 'License Order ID*';
$_['license_id_help']                               = 'Pegue aquí su ID de pedido de Opencart';

$_['google_consent']                                = 'Google consent';
$_['google_consent_help']                           = 'El modo de consentimiento (beta) permite ajustar el comportamiento de las etiquetas (tags) de Google en función del estado de consentimiento de los usuarios. Puede indicar si se ha otorgado el consentimiento para las cookies de Analytics y Ads. Las etiquetas (tags) de Google se adaptarán dinámicamente y sólo utilizarán las herramientas de medición para los fines especificados cuando el usuario haya dado su consentimiento.';
$_['google_consent_help_bottom']                    = 'Si generó el espacio de trabajo (workspace) con Google Consent activado, deberá activarlo en la pestaña "<b><a href="javascript:{}" onclick="$(\'a.tab_google-consent\').click()">Google consent</a></b>"';

// Include shipping cost in total
$_['include_shipping_cost_in_total_label']          = 'Incluir los costos de envío en el total';

// GA4
$_['ga4_status']                                    = 'GA4';
$_['ga4_measurement_id']                            = 'MEASUREMENT ID';
$_['ga4_measurement_id_help']                       = 'Copie/pegue <b>MEASUREMENT ID</b> desde su cuenta de GA4, ejemplo: G-H8Z2QHCET8.';
$_['ga4_measurement_id_measurement_id_tutorial']    = 'Para obtener el <b>MEASUREMENT ID</b>, siga este <a href="https://www.youtube.com/watch?v=_wq5nfKQ3DM" target="_new">video tutorial</a>.';

// GA4 - Enhanced Ecommerce
$_['enhanced_ecommerce_status']                     = 'Enhanced e-commerce';
$_['enhanced_ecommerce_status_help']                = 'Medir listas de productos, clics en productos, añadir/retirar al carrito, compras, promociones...';

// Google Ads conversion (compatible with Enhanced Conversions)
$_['google_ads_conversion_status']                  = 'Google Ads conversion (compatible with Enhanced Conversions)';
$_['google_ads_conversion_id']                      = 'Ads conversion ID*';
$_['google_ads_conversion_id_help']                 = 'Pegue su ID de Ads Conversion aquí';
$_['google_ads_conversion_label']                   = 'Ads conversion label*';
$_['google_ads_conversion_label_help']              = 'Pegue aquí su etiqueta (label) de Ads Conversion';

// Google Ads custom secondary conversion (compatible with Enhanced Conversions)
$_['google_ads_cs_conversion_status']               = 'Conversión secundaria personalizada de Google Ads (compatible con Enhanced Conversions)';
$_['google_ads_cs_conversion_id']                   = 'ID de conversión secundaria';
$_['google_ads_cs_conversion_id_help']              = 'ID de conversión de Google Ads. Ingrese sin AW-, solo números';
$_['google_ads_cs_conversion_label']                = 'Etiqueta de conversión secundaria';
$_['google_ads_cs_conversion_label_help']           = 'Etiqueta de conversión de Google Ads del código de seguimiento de conversiones';
$_['google_ads_cs_conversion_route']                = 'Ruta para disparar la conversión secundaria';
$_['google_ads_cs_conversion_route_help']           = 'URL completa donde se activará la conversión secundaria';
$_['google_ads_cs_conversion_value']                = 'Valor';
$_['google_ads_cs_conversion_value_help']           = 'Valor de conversión fijo';

// Google Ads custom tertiary conversion (compatible with Enhanced Conversions)
$_['google_ads_ct_conversion_status']               = 'Conversión terciaria personalizada de Google Ads (compatible con Enhanced Conversions)';
$_['google_ads_ct_conversion_id']                   = 'ID de conversión terciaria';
$_['google_ads_ct_conversion_id_help']              = 'ID de conversión de Google Ads. Ingrese sin AW-, solo números';
$_['google_ads_ct_conversion_label']                = 'Etiqueta de conversión terciaria';
$_['google_ads_ct_conversion_label_help']           = 'Etiqueta de conversión de Google Ads del código de seguimiento de conversiones';
$_['google_ads_ct_conversion_route']                = 'Ruta para disparar la conversión terciaria';
$_['google_ads_ct_conversion_route_help']           = 'URL completa donde se activará la conversión terciaria';
$_['google_ads_ct_conversion_value']                = 'Valor';
$_['google_ads_ct_conversion_value_help']           = 'Valor de conversión fijo';

// Add to Cart conversion
$_['add_to_cart_conversion_status']                 = 'Conversión de Add to Cart';
$_['add_to_cart_conversion_id']                     = 'ID de la conversión';
$_['add_to_cart_conversion_id_help']                = 'Esta conversión se va a lanzar cuando el usuario añada un producto al carrito.';
$_['add_to_cart_conversion_label']                  = 'Etiqueta de la conversión';

// Google Reviews
$_['google_reviews_status']                         = 'Google Reviews';
$_['google_reviews_merchant_id']                    = 'Merchant ID';
$_['google_reviews_merchant_id_help']               = 'Su Merchant Center ID. Puedes obtener esto en Google Merchant Center.';
$_['google_reviews_style']                          = 'Estilo';
$_['google_reviews_style_help']                     = 'Especifica cómo se muestra el cuadro de diálogo de su módulo de suscripción';
$_['google_reviews_style_center_dialog']            = 'Se muestra como un cuadro de diálogo en el centro de la vista.';
$_['google_reviews_style_bottom_right_dialog']      = 'Se muestra como un cuadro de diálogo en el inferior derecho de la vista.';
$_['google_reviews_style_bottom_left_dialog']       = 'Se muestra como un cuadro de diálogo en el inferior izquierdo de la vista.';
$_['google_reviews_style_top_right_dialog']         = 'Se muestra como un cuadro de diálogo en el superior derecho de la vista.';
$_['google_reviews_style_top_left_dialog']          = 'Se muestra como un cuadro de diálogo en el superior izquierdo de la vista.';
$_['google_reviews_style_bottom_tray']              = 'Mostrado en la parte inferior de la vista.';
$_['google_reviews_delivery_days']                  = 'Días estimados';
$_['google_reviews_delivery_days_help']             = 'Los días de entrega estimados después de la compra.';

// Google Ads Remarketing
$_['dynamic_remarketing_status']                    = 'Standard/Dynamic Remarketing';
$_['dynamic_remarketing_id']                        = 'Standard/Dynamic Remarketing conversion ID*';
$_['dynamic_remarketing_id_help']                   = 'Pegue aquí su Standard/Dynamic Remarketing Conversion ID';
$_['dynamic_remarketing_label']                     = 'Standard/Dynamic Remarketing label';
$_['dynamic_remarketing_label_help']                = 'Pegue aquí su Standard/Dynamic Remarketing Conversion Label';
$_['dynamic_remarketing_id_prefix']                 = 'Prefix ID';
$_['dynamic_remarketing_id_prefix_help']            = '<b>NO REQUERIDO</b><br><br>Localizaciones de Google Merchant: <b>US UK AU DE FR NL IT ES</b><br><br>Ejemplo: <b>es_59,</b>';
$_['dynamic_remarketing_id_sufix']                  = 'Sufix ID';
$_['dynamic_remarketing_id_sufix_help']             = '<b>NO REQUERIDO</b><br><br>Localizaciones de Google Merchant: <b>US UK AU DE FR NL IT ES</b><br><br>Ejemplo: <b>59_es,</b>';
$_['dynamic_remarketing_dynx']                      = 'Compatibilidad Dynx';

// Google Reviews badge
$_['google_reviews_badge_code_status']              = 'Estado de Insignia de Reseñas de Google (Google Reviews badge)';
$_['google_reviews_badge_id']                       = 'Merchant ID';
$_['google_reviews_badge_id_help']                  = 'Su Merchant Center ID. Puede obtener este valor en Google Merchant Center.';
$_['google_reviews_badge_code_style']               = 'Estilo de insignia';
$_['google_reviews_badge_code_style_bottom_right']  = 'Hace que la insignia flote en la parte inferior derecha de la página.';
$_['google_reviews_badge_code_style_bottom_left']   = 'Hace que la insignia flote en la parte inferior izquierda de la página.';
$_['google_reviews_badge_code_style_bottom_inline'] = 'Muestra la insignia en el lugar en el que aparece el código.';

// Rich Snippets
$_['rich_snippets']                                 = 'Rich Snippets';

// Facebook API Conversions
$_['facebook_pixel_status']                         = 'Facebook API Conversions';
$_['facebook_pixel_id']                             = 'ID de Facebook Pixel*';
$_['facebook_pixel_id_help']                        = 'Crea tu píxel de Facebook y pega aquí el PIXEL ID
<p>
<p><b><u>MUY IMPORTANTE</u></b>: Pega sólo el ID de Facebook, no la cadena completa';

// TikTok Pixel
$_['tiktok_pixel_status']                           = 'TikTok Pixel';
$_['tiktok_pixel_id']                               = 'ID del TikTok Pixel';
$_['tiktok_pixel_id_help']                          = 'Crea el píxel de TikTok desde tu cuenta y pega aquí el ID del píxel';

// Hotjar
$_['hotjar_status']                                 = 'Hotjar';
$_['hotjar_site_id']                                = 'ID del sitio Hotjar';
$_['hotjar_site_id_help']                           = 'Encuéntralo en el panel de control de tus páginas en la página de Hotjar';

// Pinterest Pixel
$_['pinterest_status']                              = 'Pinterest Pixel';
$_['pinterest_id']                                  = 'ID de Pinterest';
$_['pinterest_id_help']                             = 'Puedes encontrarlo en el código que se muestra en ads.pinterest.com en la sección de etiquetas de conversión (conversion tag)';

// Crazyegg Tracking
$_['crazyegg_status']                               = 'Crazyegg Tracking';
$_['crazyegg_id']                                   = 'ID de Crazyegg';
$_['crazyegg_id_help']                              = 'Pega aquí tu número de cuenta Crazyegg';

// Criteo OneTag
$_['criteo_status']                                 = 'Criteo OneTag';
$_['criteo_id']                                     = 'Partner ID';
$_['criteo_id_help']                                = 'Pega aquí desde tu cuenta Criteo, será una cadena de números';

// Bing Ads
$_['bing_ads_status']                               = 'Bing Ads';
$_['bing_ads_tag_id']                               = 'ID de etiqueta de Bing Ads*';
$_['bing_ads_tag_id_help']                          = 'Pegue aquí el ID de etiqueta de Bing Ads de su cuenta';

// Yahoo - Native & Search Dot
$_['yahoo_status']                                  = 'Yahoo - Native & Search Dot';
$_['yahoo_project_id']                              = 'ID del proyecto';
$_['yahoo_project_id_help']                         = 'Pegue su ID de proyecto proporcionado por Native & Search.';
$_['yahoo_pixel_id']                                = 'ID del Pixel';
$_['yahoo_pixel_id_help']                           = 'Pega tu Pixel ID proporcionado por Native & Search.';

// Domodi Pixel
$_['domodi_pixel_status']                           = 'Domodi Pixel';
$_['domodi_pixel_id']                               = 'Clave de la Tienda (Shop key)';

// Stileo Pixel 2.0
$_['stileo_pixel_status']                           = 'Stileo Pixel 2.0';
$_['stileo_pixel_id']                               = 'Clave de la API (API key)';

// Skroutz Analytics
$_['skroutz_analytics_status']                      = 'Skroutz Analytics';
$_['skroutz_analytics_shop_id']                     = 'ID de Cuenta de la Tienda (Shop Account ID)';

// Klaviyo
$_['klaviyo_status']                                = 'Klaviyo';
$_['klaviyo_public_api_key']                        = 'Clave API Pública';
$_['klaviyo_public_api_key_help']                   = 'En el panel de Klaviyo, haz clic en el menú de tu cuenta y selecciona Configuración. Luego haz clic en Claves API. Una vez allí, ve a la sección <strong>Clave API Pública / ID del sitio</strong> y copia tu Clave API Pública.';

// LinkedIn Insights
$_['linkedin_status']                               = 'LinkedIn Insights';
$_['linkedin_partner_id']                           = 'Partner ID';
$_['linkedin_purcharse_conversion_id']              = 'Compra - coversion_id';
$_['linkedin_add_to_cart_conversion_id']            = 'Añadir al carrito - coversion_id';
$_['linkedin_wishlist_conversion_id']               = 'Añadir a la lista de deseos - coversion_id';
$_['linkedin_promotion_click_conversion_id']        = 'Clic en promoción - coversion_id';
$_['linkedin_search_conversion_id']                 = 'Búsqueda - coversion_id';
$_['linkedin_start_checkout_conversion_id']         = 'Inicio de pago - coversion_id';
$_['linkedin_conversion_id_placeholder']            = 'conversion_id';

// Twitter Pixel
$_['twitter_status']                                = 'Twitter Pixel';
$_['twitter_event_id_placeholder']                  = 'ID del Evento';
$_['twitter_add_to_cart_event_id']                  = 'Añadir al Carrito - ID del Evento';
$_['twitter_add_to_cart_event_id_help']             = '<b>ID de Evento Único configurado en tus Analíticas de Twitter para el Evento de Añadir al Carrito</b>';
$_['twitter_wishlist_event_id']                     = 'Añadir a la Lista de Deseos - ID del Evento';
$_['twitter_wishlist_event_id_help']                = '<b>ID de Evento Único configurado en tus Analíticas de Twitter para el Evento de Añadir a la Lista de Deseos</b>';
$_['twitter_checkout_initiated_event_id']           = 'Inicio de Pago - ID del Evento';
$_['twitter_checkout_initiated_event_id_help']      = '<b>ID de Evento Único configurado en tus Analíticas de Twitter para el Evento de Inicio de Pago</b>';
$_['twitter_content_view_event_id']                 = 'Visualización de Contenido - ID del Evento';
$_['twitter_content_view_event_id_help']            = '<b>ID de Evento Único configurado en tus Analíticas de Twitter para el Evento de Visualización de Contenido</b>';
$_['twitter_purcharse_event_id']                    = 'Compra - ID del Evento';
$_['twitter_purcharse_event_id_help']               = '<b>ID de Evento Único configurado en tus Analíticas de Twitter para el Evento de Compra</b>';
$_['twitter_search_event_id']                       = 'Búsqueda - ID del Evento';
$_['twitter_search_event_id_help']                  = 'ID de Evento Único configurado en tus Analíticas de Twitter para el Evento de Búsqueda';
$_['twitter_pixel_id']                              = 'ID del Pixel';

// Mailchimp
$_['mailchimp_status']                              = 'Mailchimp';
$_['mailchimp_dc_or_server']                        = 'DC o Servidor';
$_['mailchimp_dc_or_server_help']                   = "Para encontrar el valor para el parámetro del servidor utilizado en mailchimp.setConfig, inicia sesión en tu cuenta de Mailchimp y mira la URL en tu navegador. Verás algo como <b>https://us19.admin.mailchimp.com</b>; la parte de <b>us19</b> es el prefijo del servidor. Ten en cuenta que tu valor específico puede ser diferente";
$_['mailchimp_dc_placeholder']                      = 'dc';
$_['mailchimp_api_key']                             = 'Clave API';
$_['mailchimp_list_id']                             = 'ID de la Lista';

// Generate Workspace
$_['generate_workspace']                            = 'Generar Google Tag Manager Workspace';
