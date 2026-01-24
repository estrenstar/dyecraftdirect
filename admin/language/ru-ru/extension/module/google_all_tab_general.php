<?php
$_['tab_general']                                   = 'Основные настройки';
$_['legend_general']                                = 'Основные настройки';
$_['status']                                        = 'Статус Google Marketing Tools';
$_['help']                                          = 'Щелкните для активации модуля и разрешения для работы текущих конфигураций.<br><b>ВАЖНО:</b> Не забудьте создать рабочую область в таблице "<b><a href="javascript:{}" onclick="open_tab_gtm_workspace();">Генератор рабочего пространства GTM</a></b>" перед тем как включить модуль.';
$_['legend_google_tag_manager']                     = 'Настройка Google Tag Manager <b>ОБЯЗАТЕЛЬНО</b>';
$_['container_id']                                  = 'ID контейнера GTM';
$_['container_id_help']                             = '<b>Обязательно</b>. Авторизуйтесь в панели Tag Manager и введите <b>GTM-XXXXXXX</b>';
$_['google_analytics_ee_legend']                    = 'Настройка GA4 Enhanced Ecommerce';
$_['google_ee_product_id_like']                     = 'ID товара';
$_['google_ee_product_id_like_help']                = 'Выбранный идентификатор товара будет отправлен в <b>GA4 Enhanced Ecommerce</b>. Если идентификатор не будет найден, то значение <b>ID товара</b> будет установлено по-умолчанию.';
$_['google_ads_legend']                             = 'Настройка - Google Ads / Dynamic remarketing';
$_['google_product_id_like']                        = 'Идентификатор товара';
$_['google_product_id_like_help']                   = 'Выберете уникальное значение, которое будет использоваться для отслеживания Ваших товаров, <b>обычно это ID</b>. Если идентификатор не будет найден, то значение <b>ID товара</b> будет установлено по-умолчанию.';
$_['dynamic_remarketing_dynx2']                     = 'Вторичная идентификация товара';
$_['dynamic_remarketing_dynx2_product_id']          = 'ID товара';
$_['dynamic_remarketing_dynx2_model']               = 'Модель';
$_['dynamic_remarketing_dynx2_sku']                 = 'SKU';
$_['dynamic_remarketing_dynx2_upc']                 = 'UPC';
$_['dynamic_remarketing_dynx2_ean']                 = 'EAN';
$_['dynamic_remarketing_dynx2_jan']                 = 'JAN';
$_['dynamic_remarketing_dynx2_isbn']                = 'ISBN';
$_['dynamic_remarketing_dynx2_mpn']                 = 'MPN';
$_['dynamic_remarketing_dynx2_location']            = 'Расположение';
$_['google_reviews_legend']                         = 'Настройки Google Reviews';
$_['google_reviews_gtin']                           = 'Продукт GTIN';
$_['google_reviews_gtin_help']                      = 'Нужно для некоторых стран/каналов. Выберете GTIN используемый в канале.';
$_['google_reviews_gtin_product_id']                = 'ID продукта';
$_['google_reviews_gtin_mpn']                       = 'MPN';
$_['google_reviews_gtin_model']                     = 'Модель';
$_['google_reviews_gtin_ean']                       = 'EAN';
$_['google_reviews_gtin_upc']                       = 'UPC';
$_['domodi_legend']                                 = 'Domodi - Настройки Pixel';
$_['domodi_size_options']                           = 'Получить размер от';
$_['domodi_size_options_help']                      = 'TСобытие <b>Добавить в корзину</b> ожидает список размеров: s,m,xl... отметьте параметры, в которых вы сохраняете размеры продукта.';

$_['cache_compatibility']                           = 'Совместимость кэша';
$_['cache_compatibility_help']                      = "Если вы думаете, что используете какую-либо систему кэширования в вашем магазине Opencart или на сервере, отметьте этот пункт, чтобы избежать потенциальных проблем. Если вы в настоящее время не замечаете никаких проблем, нет необходимости включать эту опцию, так как это может немного снизить производительность вашего сайта.";

$_['another_configuration_legend']                  = 'Универсальные настройки';
$_['positive_conversion_status_id']                 = 'Конверсия: Статус заказа для отслеживания';
$_['positive_conversion_status_id_help']            = 'Когда клиент завершает свой заказ, статистика конверсии будет обновлена если его статус указан в этом поле (если поле пустое, все статусы заказа будут считаться действительными). <b>ЕСЛИ ВЫ НЕ ЗНАЕТЕ СТАТУС СОЗДАВАЕМОГО ЗАКАЗА ПО-УМОЛЧАНИЮ, НЕ ИСПОЛЬЗУЙТЕ ДАННУЮ ОПЦИЮ, ТАК КАК ОБНОВЛЕНИЕ СТАТИСТИКИ НЕ БУДЕТ РАБОТАТЬ.</b>';
$_['custom_url_checkout_cart']                      = 'Пользовательский URL для оформления checkout/cart';
$_['custom_url_checkout_cart_help']                 = 'Если URL-адрес checkout/cart не является <b>'.HTTPS_CATALOG.'index.php?route=checkout/cart</b>, Вы можете задать в этом поле свои пользовательские урлы, разделенные запятыми, например:<br>
    - '.HTTPS_CATALOG.'<b>view-cart</b><br><br>
    В этом примере вам нужно установить:   "<b>view-cart</b>"
    ';

$_['custom_url_checkout_checkout']                  = 'Пользовательский URL для оформления checkout/checkout';
$_['custom_url_checkout_checkout_help']             = 'Если URL-адрес checkout/checkout не является <b>'.HTTPS_CATALOG.'index.php?route=checkout/checkout</b>, Вы можете задать в этом поле свои пользовательские урлы, разделенные запятыми, например:<br>
    - '.HTTPS_CATALOG.'<b>finish-checkout</b><br>
    В этом примере вам нужно установить:   "<b>finish-checkout</b>"
    ';

$_['custom_url_checkout_success']                   = 'Пользовательский URL для оформления checkout/success';
$_['custom_url_checkout_success_help']              = 'Если URL-адрес checkout/success не является <b>'.HTTPS_CATALOG.'index.php?route=checkout/success</b>, Вы можете задать в этом поле свои пользовательские урлы, разделенные запятыми, например:<br>
    - '.HTTPS_CATALOG.'index.php?route=<b>visa/success</b><br>
    - '.HTTPS_CATALOG.'<b>order-success</b><br><br>
    
    В этом примере вам нужно установить:   "<b>visa/success,order-success</b>"
    ';
?>