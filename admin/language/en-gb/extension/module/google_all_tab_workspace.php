<?php
$_['tab_workspace']                                 = 'GTM Workspace generator';
$_['workspace_explain']                             = '<p>In this zone, you will get a <b>workspace file</b> ready to work. The steps are next:</p><br>
<ol>
    <li>Enable and complete all tags/statistics that you want to use in your store.</li>
    <li>Press bottom button "<b>Generate Google Tag Manager Workspace</b>"</li>
    <li>The <b>API</b> will send your Workspace file <b>to your email</b>. (Check SPAM folder).</li>
    <li><b>Import</b> this file into your <b>Google Tag Manager account</b>.</li>
    <li>Finally, press the "<b>SUBMIT</b>" button in your <b>Google tag manager account</b> to publish your content.</li>
</ol>
<p>You can all information about this in section "<b>3.- Generate and import a workspace</b>" of <a href="https://devmanextensions.com/docs/en/opencart-extensions/google-marketing-tools" target="_blank">official documentation</a>.</p>
<p><b><u>VERY IMPORTANT</u></b>: When you want to add/remove tags or change your configuration, you will need to repeat these steps.</p>
';

// License Order ID
$_['license_id']                                    = 'License Order ID*';
$_['license_id_help']                               = 'Paste your Opencart Order ID here';

$_['google_consent']                                = 'Google consent';
$_['google_consent_help']                           = 'Consent mode (beta) allows you to adjust how your Google tags behave based on the consent status of your users. You can indicate whether consent has been granted for Analytics and Ads cookies. Google\'s tags will dynamically adapt, only utilizing measurement tools for the specified purposes when consent has been given by the user.';
$_['google_consent_help_bottom']                    = 'If you generated the workspace with Google Consent enabled, you need enable it in tab "<b><a href="javascript:{}" onclick="$(\'a.tab_google-consent\').click()">Google consent</a></b>"';

// Include shipping cost in total
$_['include_shipping_cost_in_total_label']          = 'Include shipping costs in total';

// GA4
$_['ga4_status']                                    = 'GA4';
$_['ga4_measurement_id']                            = 'MEASUREMENT ID';
$_['ga4_measurement_id_help']                       = 'Copy/paste <b>MEASUREMENT ID</b> from your GA4 account, example: G-H8Z2QHCET8.';
$_['ga4_measurement_id_measurement_id_tutorial']    = 'For get the <b>MEASUREMENT ID</b>, follow this <a href="https://www.youtube.com/watch?v=_wq5nfKQ3DM" target="_new">video tutorial</a>.';

// GA4 - Enhanced Ecommerce
$_['enhanced_ecommerce_status']                     = 'GA4 - Enhanced Ecommerce';
$_['enhanced_ecommerce_status_help']                = 'Measure product lists, product clicks, add/remove to cart, purchases, promotions...';

// Google Ads conversion (compatible with Enhanced Conversions)
$_['google_ads_conversion_status']                  = 'Google Ads conversion (compatible with Enhanced Conversions)';
$_['google_ads_conversion_id']                      = 'Ads conversion ID*';
$_['google_ads_conversion_id_help']                 = 'Paste your Ads Conversion ID here';
$_['google_ads_conversion_label']                   = 'Ads conversion label*';
$_['google_ads_conversion_label_help']              = 'Paste your Ads Conversion label here';

// Google Ads custom secondary conversion (compatible with Enhanced Conversions)
$_['google_ads_cs_conversion_status']               = 'Google Ads custom secondary conversion (compatible with Enhanced Conversions)';
$_['google_ads_cs_conversion_id']                   = 'Secondary conversion ID';
$_['google_ads_cs_conversion_id_help']              = 'Google Ads Conversion id Enter without AW-, only numbers';
$_['google_ads_cs_conversion_label']                = 'Secondary conversion label';
$_['google_ads_cs_conversion_label_help']           = 'Google Ads Conversion Label from the Conversion tracking code';
$_['google_ads_cs_conversion_route']                = 'Route to fire secondary conversion';
$_['google_ads_cs_conversion_route_help']           = 'Full URL where secondary conversion will be triggered';
$_['google_ads_cs_conversion_value']                = 'Value';
$_['google_ads_cs_conversion_value_help']           = 'Fixed Conversion value';

// Google Ads custom tertiary conversion (compatible with Enhanced Conversions)
$_['google_ads_ct_conversion_status']               = 'Google Ads custom tertiary conversion (compatible with Enhanced Conversions)';
$_['google_ads_ct_conversion_id']                   = 'Tertiary conversion ID';
$_['google_ads_ct_conversion_id_help']              = 'Google Ads Conversion id Enter without AW-, only numbers';
$_['google_ads_ct_conversion_label']                = 'Tertiary conversion label';
$_['google_ads_ct_conversion_label_help']           = 'Google Ads Conversion Label from the Conversion tracking code';
$_['google_ads_ct_conversion_route']                = 'Route to fire tertiary conversion';
$_['google_ads_ct_conversion_route_help']           = 'Full URL where tertiary conversion will be triggered';
$_['google_ads_ct_conversion_value']                = 'Value';
$_['google_ads_ct_conversion_value_help']           = 'Fixed Conversion value';

// Add to Cart conversion
$_['add_to_cart_conversion_status']                 = 'Add to Cart conversions';
$_['add_to_cart_conversion_id']                     = 'Conversion ID';
$_['add_to_cart_conversion_id_help']                = 'This conversion will be triggered when the user adds a product to the cart.';
$_['add_to_cart_conversion_label']                  = 'Conversion label';

// Google Reviews
$_['google_reviews_status']                         = 'Google Reviews';
$_['google_reviews_merchant_id']                    = 'Merchant ID';
$_['google_reviews_merchant_id_help']               = 'Your Merchant Center ID. You can get this from the Google Merchant Center';
$_['google_reviews_style']                          = 'Style';
$_['google_reviews_style_help']                     = 'Specifies how your opt-in module\'s dialog box is displayed';
$_['google_reviews_style_center_dialog']            = 'Displayed as a dialog box in the center of the view.';
$_['google_reviews_style_bottom_right_dialog']      = 'Displayed as a dialog box at the bottom right of the view.';
$_['google_reviews_style_bottom_left_dialog']       = 'Displayed as a dialog box at the bottom left of the view.';
$_['google_reviews_style_top_right_dialog']         = 'Displayed as a dialog box at the top right of the view.';
$_['google_reviews_style_top_left_dialog']          = 'Displayed as a dialog box at the top left of the view.';
$_['google_reviews_style_bottom_tray']              = 'Displayed in the bottom of the view.';
$_['google_reviews_delivery_days']                  = 'Estimated days';
$_['google_reviews_delivery_days_help']             = 'The estimated delivery days after purchase.';

// Google Ads Remarketing
$_['dynamic_remarketing_status']                    = 'Standard/Dynamic Remarketing';
$_['dynamic_remarketing_id']                        = 'Standard/Dynamic Remarketing conversion ID*';
$_['dynamic_remarketing_id_help']                   = 'Paste your Standard/Dynamic Remarketing Conversion ID here';
$_['dynamic_remarketing_label']                     = 'Standard/Dynamic Remarketing label';
$_['dynamic_remarketing_label_help']                = 'Paste your Standard/Dynamic Remarketing Conversion Label here';
$_['dynamic_remarketing_id_prefix']                 = 'Prefix ID';
$_['dynamic_remarketing_id_prefix_help']            = '<b>NOT REQUIRED</b><br><br>Google Merchant locales: <b>US UK AU DE FR NL IT ES</b><br><br>Example: <b>es_59,</b>';
$_['dynamic_remarketing_id_sufix']                  = 'Sufix ID';
$_['dynamic_remarketing_id_sufix_help']             = '<b>NOT REQUIRED</b><br><br>Google Merchant locales: <b>US UK AU DE FR NL IT ES</b><br><br>Example: <b>59_es,</b>';
$_['dynamic_remarketing_dynx']                      = 'Dynx compatibility';

// Google Reviews badge
$_['google_reviews_badge_code_status']              = 'Google Reviews badge status';
$_['google_reviews_badge_id']                       = 'Merchant ID';
$_['google_reviews_badge_id_help']                  = 'Your Merchant Center ID. You can get this value from the Google Merchant Center.';
$_['google_reviews_badge_code_style']               = 'Badge style';
$_['google_reviews_badge_code_style_bottom_right']  = 'Causes the badge to float in the bottom right of the page';
$_['google_reviews_badge_code_style_bottom_left']   = 'Causes the badge to float in the bottom left of the page';
$_['google_reviews_badge_code_style_bottom_inline'] = 'Renders the badge in the place in which the code appears';

// Rich Snippets
$_['rich_snippets_status']                          = 'Rich Snippets';

// Facebook API Conversions
$_['facebook_pixel_status']                         = 'Facebook Pixel';
$_['facebook_pixel_id']                             = 'Facebook Pixel ID *';
$_['facebook_pixel_id_help']                        = 'Create your facebook pixel and paste the PIXEL ID here
<p>
<p><b><u>VERY IMPORTANT</u></b>: Only paste the ID from Facebook not the complete string';

// TikTok Pixel
$_['tiktok_pixel_status']                           = 'TikTok Pixel';
$_['tiktok_pixel_id']                               = 'TikTok Pixel ID';
$_['tiktok_pixel_id_help']                          = 'Create TikTok pixel from your account and paste here the Pixel ID';

// Hotjar
$_['hotjar_status']                                 = 'Hotjar';
$_['hotjar_site_id']                                = 'Hotjar site ID';
$_['hotjar_site_id_help']                           = 'Find this under your pages insight dashboard on Hotjar page ';

// Pinterest Pixel
$_['pinterest_status']                              = 'Pinterest Pixel';
$_['pinterest_id']                                  = 'Pinterest ID';
$_['pinterest_id_help']                             = 'You can find this in your code shown on ads.pinterest.com under the conversion tag section';

// Crazyegg Tracking
$_['crazyegg_status']                               = 'Crazyegg Tracking';
$_['crazyegg_id']                                   = 'Crazyegg ID';
$_['crazyegg_id_help']                              = 'Paste your Crazyegg account number here';

// Criteo OneTag
$_['criteo_status']                                 = 'Criteo OneTag';
$_['criteo_id']                                     = 'Partner ID';
$_['criteo_id_help']                                = 'Paste here from your Criteo account, it will be a number string';

// Bing Ads
$_['bing_ads_status']                               = 'Bing Ads';
$_['bing_ads_tag_id']                               = 'Bing Ads tag ID*';
$_['bing_ads_tag_id_help']                          = 'Paste your Bing Ads tag ID from your account here';

// Dognet
$_['dognet_status']                                 = 'Dognet';
$_['dognet_account_id']                             = 'Account ID';
$_['dognet_campaign_id']                             = 'Campaign ID';

// Yahoo - Native & Search Dot
$_['yahoo_status']                                  = 'Yahoo - Native & Search Dot';
$_['yahoo_project_id']                              = 'Project ID';
$_['yahoo_project_id_help']                         = 'Paste your Project ID provided by Native & Search.';
$_['yahoo_pixel_id']                                = 'Pixel ID';
$_['yahoo_pixel_id_help']                           = 'Paste your Pixel ID provided by Native & Search.';

// Domodi Pixel
$_['domodi_pixel_status']                           = 'Domodi Pixel';
$_['domodi_pixel_id']                               = 'Shop key';

// Stileo Pixel 2.0
$_['stileo_pixel_status']                           = 'Stileo Pixel 2.0';
$_['stileo_pixel_id']                               = 'API key';

// Skroutz Analytics
$_['skroutz_analytics_status']                      = 'Skroutz Analytics';
$_['skroutz_analytics_shop_id']                     = 'Shop Account ID';

// Klaviyo
$_['klaviyo_status']                                = 'Klaviyo';
$_['klaviyo_public_api_key']                        = 'Public API Key';
$_['klaviyo_public_api_key_help']                   = 'In Klaviyo dashboard, click on your account menu and select Settings. Then click on API Keys. Once there, go tu section <strong>Public API Key / site ID</strong> and copy your Public API Key.';

// LinkedIn Insights
$_['linkedin_status']                               = 'LinkedIn Insights';
$_['linkedin_partner_id']                           = 'Partner ID';
$_['linkedin_purcharse_conversion_id']              = 'Purcharse - coversion_id';
$_['linkedin_add_to_cart_conversion_id']            = 'Add to cart - coversion_id';
$_['linkedin_wishlist_conversion_id']               = 'Add to Wishlist - coversion_id';
$_['linkedin_promotion_click_conversion_id']        = 'Promotion click - coversion_id';
$_['linkedin_search_conversion_id']                 = 'Search - coversion_id';
$_['linkedin_start_checkout_conversion_id']         = 'Start checkout - coversion_id';
$_['linkedin_conversion_id_placeholder']            = 'conversion_id';

// Twitter Pixel
$_['twitter_status']                                = 'Twitter Pixel';
$_['twitter_event_id_placeholder']                  = 'Event ID';
$_['twitter_add_to_cart_event_id']                  = 'Add to Cart - Event ID';
$_['twitter_add_to_cart_event_id_help']             = '<b>Unique Event ID setup in your Twitter Analytics for Add to Cart Event</b>';
$_['twitter_wishlist_event_id']                     = 'Add to Wishlist - Event ID';
$_['twitter_wishlist_event_id_help']                = '<b>Unique Event ID setup in your Twitter Analytics for Add to Wishlist Event</b>';
$_['twitter_checkout_initiated_event_id']           = 'Checkout initiated - Event ID';
$_['twitter_checkout_initiated_event_id_help']      = '<b>Unique Event ID setup in your Twitter Analytics for Initiate Checkout Event</b>';
$_['twitter_content_view_event_id']                 = 'Content view - Event ID';
$_['twitter_content_view_event_id_help']            = '<b>Unique Event ID setup in your Twitter Analytics for View Content Event</b>';
$_['twitter_purcharse_event_id']                    = 'Purcharse - Event ID';
$_['twitter_purcharse_event_id_help']               = '</b>Unique Event ID setup in your Twitter Analytics for Purchase Event</b>';
$_['twitter_search_event_id']                       = 'Search - Event ID';
$_['twitter_search_event_id_help']                  = 'Unique Event ID setup in your Twitter Analytics for Search Event';
$_['twitter_pixel_id']                              = 'Pixel ID';

// Mailchimp
$_['mailchimp_status']                              = 'Mailchimp';
$_['mailchimp_dc_or_server']                        = 'DC or Server';
$_['mailchimp_dc_or_server_help']                   = "To find the value for the server parameter used in mailchimp.setConfig, log into your Mailchimp account and look at the URL in your browser. You'll see something like <b>https://us19.admin.mailchimp.com</b>; the <b> us19 </b> part is the server prefix. Note that your specific value may be different";
$_['mailchimp_dc_placeholder']                      = 'dc';
$_['mailchimp_api_key']                             = 'API Key';
$_['mailchimp_list_id']                             = 'List ID';

// Generate Workspace
$_['generate_workspace']                            = 'Generate Google Tag Manager Workspace';