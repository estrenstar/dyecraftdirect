<?php
$_['tab_workspace']                                 = 'Генератор рабочего пространства GTM';
$_['workspace_explain']                             = '<p>Пошаговая настройка готовой <b>рабочей области</b>.</p><br>
<ol>
    <li>Включите и заполните все опции / поля, которые вы хотите использовать в своем магазине.</li>
    <li>Нажмите кнопку "<b>Создание рабочей области Google Tag Manager</b>"</li>
    <li><b>API</b> отправит файл рабочей области <b>на Ваш  email</b>. (Проверьте папку СПАМ).</li>
    <li><b>Загрузите</b> этот файл в Ваш аккаунт <b>Google Tag Manager</b>.</li>
    <li>В конце нажмите кнопку "<b>SUBMIT</b>" в Вашем аккаунте  <b>Google tag manager</b> для публикации информации.</li>
</ol>
<p>Всю дополнительную информацию для пункта "<b>3.- Создание и импорт рабочей области</b>" можно получить из <a href="https://devmanextensions.com/docs/en/opencart-extensions/google-marketing-tools" target="_blank">официальной документации</a>.</p>
<p><b><u>ОЧЕНЬ ВАЖНО</u></b>: Если Вы захотите изменить настройки, Вам необходимо повторить шаги указанные выше.</p>
';

// License Order ID
$_['license_id']                                    = 'ID порядка лицензирования*';
$_['license_id_help']                               = 'Вставьте сюда Ваш ID заказа Opencart';

$_['google_consent']                                = 'Согласие Google';
$_['google_consent_help']                           = 'Режим согласия (бета-версия) позволяет настраивать поведение тегов Google в зависимости от статуса согласия пользователей. Вы можете указать, было ли получено согласие на использование файлов cookie, Аналитики и рекламы. Теги Google будут динамически адаптироваться, используя инструменты измерения только для указанных целей, когда пользователь дал свое согласие.';
$_['google_consent_help_bottom']                    = 'Если вы создали рабочую область с включенным согласием Google, вам необходимо включить его на вкладке "<b><a href="javascript:{}" onclick="$(\'a.tab_google-consent\').click()">Согласие Google</a></b>"';

// Include shipping cost in total
$_['include_shipping_cost_in_total_label']          = 'Включить стоимость доставки в общую сумму';

// GA4
$_['ga4_status']                                    = 'GA4';
$_['ga4_measurement_id']                            = 'MEASUREMENT ID';
$_['ga4_measurement_id_help']                       = 'Копировать/Вставить <b>MEASUREMENT ID</b> для Вашего аккаунта GA4, например: G-H8Z2QHCET8.';
$_['ga4_measurement_id_measurement_id_tutorial']    = 'Для получения <b>MEASUREMENT ID</b>, следуйте <a href="https://www.youtube.com/watch?v=_wq5nfKQ3DM" target="_new">видеоинструкции</a>.';

// GA4 - Enhanced Ecommerce
$_['enhanced_ecommerce_status']                     = 'Расширенные возможности электронной коммерции';
$_['enhanced_ecommerce_status_help']                = 'Измерение списков товаров, клики по товарам, добавление/удаление в корзину, покупки, акции...';

// Google Ads conversion (compatible with Enhanced Conversions)
$_['google_ads_conversion_status']                  = 'Конверсия Google Ads (совместима с Расширенными конверсиями)';
$_['google_ads_conversion_id']                      = 'ID конверсии Ads*';
$_['google_ads_conversion_id_help']                 = 'Вставьте Ваш ID конверсии Ads';
$_['google_ads_conversion_label']                   = 'Заголовок конверсии Ads*';
$_['google_ads_conversion_label_help']              = 'Вставьте Ваш заголовок ID конверсии Ads';

// Google Ads custom secondary conversion (compatible with Enhanced Conversions)
$_['google_ads_cs_conversion_status']               = 'Пользовательское вторичное преобразование Google Ads (совместимо с Улучшенными преобразованиями)';
$_['google_ads_cs_conversion_id']                   = 'ID вторичного преобразования';
$_['google_ads_cs_conversion_id_help']              = 'ID преобразования Google Ads. Введите без AW-, только числа';
$_['google_ads_cs_conversion_label']                = 'Метка вторичного преобразования';
$_['google_ads_cs_conversion_label_help']           = 'Метка преобразования Google Ads из кода отслеживания преобразований';
$_['google_ads_cs_conversion_route']                = 'Маршрут для запуска вторичного преобразования';
$_['google_ads_cs_conversion_route_help']           = 'Полный URL, где будет запущено вторичное преобразование';
$_['google_ads_cs_conversion_value']                = 'Значение';
$_['google_ads_cs_conversion_value_help']           = 'Фиксированное значение преобразования';

// Google Ads custom tertiary conversion (compatible with Enhanced Conversions)
$_['google_ads_ct_conversion_status']               = 'Пользовательское третичное преобразование Google Ads (совместимо с Улучшенными преобразованиями)';
$_['google_ads_ct_conversion_id']                   = 'ID третичного преобразования';
$_['google_ads_ct_conversion_id_help']              = 'ID преобразования Google Ads. Введите без AW-, только числа';
$_['google_ads_ct_conversion_label']                = 'Метка третичного преобразования';
$_['google_ads_ct_conversion_label_help']           = 'Метка преобразования Google Ads из кода отслеживания преобразований';
$_['google_ads_ct_conversion_route']                = 'Маршрут для запуска третичного преобразования';
$_['google_ads_ct_conversion_route_help']           = 'Полный URL, где будет запущено третичное преобразование';
$_['google_ads_ct_conversion_value']                = 'Значение';
$_['google_ads_ct_conversion_value_help']           = 'Фиксированное значение преобразования';

// Add to Cart conversion
$_['add_to_cart_conversion_status']                 = 'Конверсия добавления в корзину';
$_['add_to_cart_conversion_id']                     = 'ID конверсии';
$_['add_to_cart_conversion_id_help']                = 'Эта конверсия будет запущена, когда пользователь добавит товар в корзину.';
$_['add_to_cart_conversion_label']                  = 'Метка конверсии';

// Google Reviews
$_['google_reviews_status']                         = 'Google Reviews';
$_['google_reviews_merchant_id']                    = 'ID торговой площадки';
$_['google_reviews_merchant_id_help']               = 'ID Вашей торговой площадки. Вы можете получить это значение из Google Merchant Center.';
$_['google_reviews_style']                          = 'Стиль';
$_['google_reviews_style_help']                     = 'Показывает, как отображается диалоговое окно модуля выбора товара';
$_['google_reviews_style_center_dialog']            = 'Отображение диалогового окна в центре.';
$_['google_reviews_style_bottom_right_dialog']      = 'Отображение диалогового окна внизу справа.';
$_['google_reviews_style_bottom_left_dialog']       = 'Отображение диалогового окна внизу слева.';
$_['google_reviews_style_top_right_dialog']         = 'Отображение диалогового окна вверху справа.';
$_['google_reviews_style_top_left_dialog']          = 'Отображение диалогового окна вверху слева.';
$_['google_reviews_style_bottom_tray']              = 'Отображение внизу.';
$_['google_reviews_delivery_days']                  = 'Кол-во дней для доставки';
$_['google_reviews_delivery_days_help']             = 'Расчетное количество дней для доставки после оплаты.';

// Google Ads Remarketing
$_['dynamic_remarketing_status']                    = 'Стандартный/Динамический Ремаркетинг';
$_['dynamic_remarketing_id']                        = 'ID конверсии Стандартного/Динамического Ремаркетинга*';
$_['dynamic_remarketing_id_help']                   = 'Вставьте сюда Ваш ID конверсии Стандарт/Динамик Ремаркетинг';
$_['dynamic_remarketing_label']                     = 'Стандартный/Динамический заголовок Ремаркетинга';
$_['dynamic_remarketing_label_help']                = 'Вставьте сюда Ваш заголовок ID конверсии Стандарт/Динамик Ремаркетингe';
$_['dynamic_remarketing_id_prefix']                 = 'ID префикс';
$_['dynamic_remarketing_id_prefix_help']            = '<b>НЕ ОБЯЗАТЕЛНО</b><br><br>Торговые локации Google: <b>US UK AU DE FR NL IT ES</b><br><br>Пример: <b>es_59,</b>';
$_['dynamic_remarketing_id_sufix']                  = 'ID суффикс';
$_['dynamic_remarketing_id_sufix_help']             = '<b>НЕ ОБЯЗАТЕЛНО</b><br><br>Торговые локации Google: <b>US UK AU DE FR NL IT ES</b><br><br>Пример: <b>es_59,</b>';
$_['dynamic_remarketing_dynx']                      = 'Совместимость Dynx';

// Google Reviews badge
$_['google_reviews_badge_code_status']              = 'Google Reviews badge status';
$_['google_reviews_badge_id']                       = 'Merchant ID';
$_['google_reviews_badge_id_help']                  = 'Your Merchant Center ID. You can get this value from the Google Merchant Center.';
$_['google_reviews_badge_code_style']               = 'Стиль значка';
$_['google_reviews_badge_code_style_bottom_right']  = 'Плавающий значок в правом нижнем углу страницы';
$_['google_reviews_badge_code_style_bottom_left']   = 'Плавающий значок в левом нижнем углу страницы';
$_['google_reviews_badge_code_style_bottom_inline'] = 'Отображает значок в том месте, где отображается код';

// Rich Snippets
$_['rich_snippets']                                 = 'Сниппеты';

// Facebook API Conversions
$_['facebook_pixel_status']                         = 'Facebook Pixel';
$_['facebook_pixel_id']                             = 'Facebook Pixel ID*';
$_['facebook_pixel_id_help']                        = 'Создайте Ваш facebook pixel и вставьте сюда PIXEL ID
<p>
<p><b><u>ОЧЕНЬ ВАЖНОNT</u></b>: Вставить необходимо не полную строку из Facebook';

// TikTok Pixel
$_['tiktok_pixel_status']                           = 'TikTok Pixel';
$_['tiktok_pixel_id']                               = 'TikTok Pixel ID';
$_['tiktok_pixel_id_help']                          = 'Создайте TikTok pixel для своего аккаунта и вставьте сюда Pixel ID';

// Hotjar
$_['hotjar_status']                                 = 'Hotjar';
$_['hotjar_site_id']                                = 'Hotjar сайт ID';
$_['hotjar_site_id_help']                           = 'Найдите это в своей панели инструментов pages insight на странице Hotjar';

// Pinterest Pixel
$_['pinterest_status']                              = 'Pinterest Pixel';
$_['pinterest_id']                                  = 'Pinterest ID';
$_['pinterest_id_help']                             = 'Вы можете найти это в своем коде, показанном на ads.pinterest.com в разделе тег преобразования';

// Crazyegg Tracking
$_['crazyegg_status']                               = 'Отслеживание Crazyegg';
$_['crazyegg_id']                                   = 'Crazyegg ID';
$_['crazyegg_id_help']                              = 'Вставьте сюда значение из аккаунта Crazyegg';

// Criteo OneTag
$_['criteo_status']                                 = 'Criteo OneTag';
$_['criteo_id']                                     = 'ID партнера';
$_['criteo_id_help']                                = 'Вставьте сюда значение из аккаунта Criteo, это может быть последовательность цифр';

// Bing Ads
$_['bing_ads_status']                               = 'Bing Ads';
$_['bing_ads_tag_id']                               = 'Bing Ads tag ID*';
$_['bing_ads_tag_id_help']                          = 'Вставьте сюда Ваш ID из аккаунта Bing Ads';

// Yahoo - Native & Search Dot
$_['yahoo_status']                                  = 'Yahoo - Native & Search Dot';
$_['yahoo_project_id']                              = 'Project ID';
$_['yahoo_project_id_help']                         = 'Вставьте свой Project ID предоставленный Native & Search.';
$_['yahoo_pixel_id']                                = 'Pixel ID';
$_['yahoo_pixel_id_help']                           = 'Вставьте свой Pixel ID предоставленный Native & Search.';

// Domodi Pixel
$_['domodi_pixel_status']                           = 'Domodi Pixel';
$_['domodi_pixel_id']                               = 'Ключ магазина (Shop key)';

// Stileo Pixel 2.0
$_['stileo_pixel_status']                           = 'Stileo Pixel 2.0';
$_['stileo_pixel_id']                               = 'Ключ API (API key)';

// Skroutz Analytics
$_['skroutz_analytics_status']                      = 'Аналитика Skroutz';
$_['skroutz_analytics_shop_id']                     = 'Аккаунт магазина (Shop Account ID)';

// Klaviyo
$_['klaviyo_status']                                = 'Klaviyo';
$_['klaviyo_public_api_key']                        = 'Публичный API-ключ';
$_['klaviyo_public_api_key_help']                   = 'В панели Klaviyo нажмите на меню вашего аккаунта и выберите Настройки. Затем нажмите на API-ключи. После этого перейдите в раздел <strong>Публичный API-ключ / ID сайта</strong> и скопируйте ваш публичный API-ключ.';

// LinkedIn Insights
$_['linkedin_status']                               = 'LinkedIn Insights';
$_['linkedin_partner_id']                           = 'Partner ID';
$_['linkedin_purcharse_conversion_id']              = 'Покупка - coversion_id';
$_['linkedin_add_to_cart_conversion_id']            = 'Добавить в корзину - coversion_id';
$_['linkedin_wishlist_conversion_id']               = 'Добавить в список желаний - coversion_id';
$_['linkedin_promotion_click_conversion_id']        = 'Клик по рекламе - coversion_id';
$_['linkedin_search_conversion_id']                 = 'Поиск - coversion_id';
$_['linkedin_start_checkout_conversion_id']         = 'Начало оформления заказа - coversion_id';
$_['linkedin_conversion_id_placeholder']            = 'conversion_id';

// Twitter Pixel
$_['twitter_status']                                = 'Twitter Pixel';
$_['twitter_event_id_placeholder']                  = 'ID события';
$_['twitter_add_to_cart_event_id']                  = 'Добавить в корзину - ID события';
$_['twitter_add_to_cart_event_id_help']             = '<b>Уникальный ID события, настроенный в вашей аналитике Twitter для события "Добавить в корзину"</b>';
$_['twitter_wishlist_event_id']                     = 'Добавить в список желаний - ID события';
$_['twitter_wishlist_event_id_help']                = '<b>Уникальный ID события, настроенный в вашей аналитике Twitter для события "Добавить в список желаний"</b>';
$_['twitter_checkout_initiated_event_id']           = 'Начало оформления заказа - ID события';
$_['twitter_checkout_initiated_event_id_help']      = '<b>Уникальный ID события, настроенный в вашей аналитике Twitter для события "Начало оформления заказа"</b>';
$_['twitter_content_view_event_id']                 = 'Просмотр контента - ID события';
$_['twitter_content_view_event_id_help']            = '<b>Уникальный ID события, настроенный в вашей аналитике Twitter для события "Просмотр контента"</b>';
$_['twitter_purcharse_event_id']                    = 'Покупка - ID события';
$_['twitter_purcharse_event_id_help']               = '<b>Уникальный ID события, настроенный в вашей аналитике Twitter для события "Покупка"</b>';
$_['twitter_search_event_id']                       = 'Поиск - ID события';
$_['twitter_search_event_id_help']                  = 'Уникальный ID события, настроенный в вашей аналитике Twitter для события "Поиск"';
$_['twitter_pixel_id']                              = 'ID пикселя';

// Mailchimp
$_['mailchimp_status']                              = 'Mailchimp';
$_['mailchimp_dc_or_server']                        = 'DC или сервер';
$_['mailchimp_dc_or_server_help']                   = "Чтобы найти значение для параметра сервера, используемого в mailchimp.setConfig, войдите в свою учетную запись Mailchimp и посмотрите URL в вашем браузере. Вы увидите что-то вроде <b>https://us19.admin.mailchimp.com</b>; часть <b>us19</b> - это префикс сервера. Обратите внимание, что ваше конкретное значение может отличаться";
$_['mailchimp_dc_placeholder']                      = 'dc';
$_['mailchimp_api_key']                             = 'API ключ';
$_['mailchimp_list_id']                             = 'ID списка';

// Generate Workspace
$_['generate_workspace']                            = 'Создание рабочей области Google Tag Manager';
