<div style='background-color:#AFF'><h3>Encryption</h3><p><strong>PHP Version</strong></p><pre>8.2.29</pre><hr /><p><strong>Cryptor</strong></p><pre>OpenSSL</pre><hr /><p><strong>Default Cipher</strong></p><pre>aes-256-cbc</pre><hr /><p><strong>mb_internal_encoding</strong></p><pre>UTF-8</pre><hr /><div style='background-color:#AAA'><h3>IP Validation</h3><p><strong>$headers from sl_get_ip()</strong></p><pre>Array
(
    [X-Https] => 1
    [Incap-Client-Ip] => 184.154.76.7
    [X-Forwarded-For] => 184.154.76.7
    [Cdn-Loop] => IncapCDN; id="329_3142932_0"
    [User-Agent] => SiteLock (Module: SmartDB; Source: https://www.sitelock.com/; Version: 1.0)
    [Host] => dyecraftdirect.com
    [Connection] => TE, close
    [Te] => deflate,gzip;q=0.3
)
</pre><hr /><p><strong>IP Check started in</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/78266ea8d0226e759697b06375373fc4.php</pre><hr /><p><strong>IP Check started at</strong></p><pre>2025-10-27T09:05:16-04:00</pre><hr /><p><strong>The following IPs will be tested</strong></p><pre>Array
(
    [0] => 184.154.76.7
    [1] => 107.154.186.4
)
</pre><hr /><p><strong>mapi_post URL</strong></p><pre>https://mapi.sitelock.com/v3/connect/</pre><hr /><p><strong>mapi_post_request</strong></p><pre>Array
(
    [pluginVersion] => 100.0.0
    [apiTargetVersion] => 3.0.0
    [token] => a21***
    [requests] => Array
        (
            [id] => 0933df7eb5bb499c0e1837408ca25b76-17615703161189
            [action] => validate_ip
            [params] => Array
                (
                    [site_id] => 40186158
                    [ip] => 184.154.76.7
                )

        )

)
</pre><hr /><p><strong>curl_getinfo()</strong></p><pre>Array
(
    [url] => https://mapi.sitelock.com/v3/connect/
    [content_type] => text/html; charset=UTF-8
    [http_code] => 200
    [header_size] => 765
    [request_size] => 506
    [filetime] => -1
    [ssl_verify_result] => 0
    [redirect_count] => 0
    [total_time] => 0.263057
    [namelookup_time] => 3.5E-5
    [connect_time] => 0.005892
    [pretransfer_time] => 0.04089
    [size_upload] => 320
    [size_download] => 524
    [speed_download] => 1992
    [speed_upload] => 1216
    [download_content_length] => -1
    [upload_content_length] => 320
    [starttransfer_time] => 0.25684
    [redirect_time] => 0
    [redirect_url] => 
    [primary_ip] => 45.60.12.54
    [certinfo] => Array
        (
        )

    [primary_port] => 443
    [local_ip] => 213.165.236.14
    [local_port] => 57314
    [http_version] => 2
    [protocol] => 2
    [ssl_verifyresult] => 0
    [scheme] => HTTPS
    [appconnect_time_us] => 40806
    [connect_time_us] => 5892
    [namelookup_time_us] => 35
    [pretransfer_time_us] => 40890
    [redirect_time_us] => 0
    [starttransfer_time_us] => 256840
    [total_time_us] => 263057
)
</pre><hr /><p><strong>mapi_response</strong></p><pre><textarea style="width:99%;height:100px;">{"apiVersion":"3.0.1","status":"ok","globalResponse":null,"banner":null,"forceLogout":false,"newToken":null,"now":1761570316,"responses":[{"id":"0933df7eb5bb499c0e1837408ca25b76-17615703161189","data":{"ip_address":"184.154.76.7","valid":true},"raw_api_url":"https:\/\/api.sitelock.com\/v1\/dbscan\/checkip","raw_response":{"@attributes":{"version":"1.1","encoding":"UTF-8"},"checkIP":{"status":"1"}},"raw_request":{"site_id":"40186158","ip":"184.154.76.7"},"user_agent":"SiteLock Bullet for DBScan (other)","status":"ok"}]}</textarea></pre><hr /><div style='background-color:#AFA'><h3>GrabAndZip</h3><p><strong>_POST</strong></p><pre>Array
(
)
</pre><hr /><p><strong>_GET (raw)</strong></p><pre>cmd=db_creds_ready&enc_db_creds=%2FW3TFvKTc%2B4ro%2FYVmQAnYLvu5F2bwAadMFDobuCKTHamg96UjY2h9wobeTJIMtxQ5dUEB5BHjV3SohZoivXjFrOpv4axoSbH7GDrkvSPBLr6a4tk%2BLXXBd8bHNzDYyhXk6%2BsRPiIUlInEqbz98bKIb%2BvBxSJD8FnbBEjbG4cXVg%2FxS5hANwMqQPIhvAm2Y4E&smart_single_download_id=5528611</pre><hr /><p><strong>Detected memory_limit</strong></p><pre>1024M</pre><hr /><p><strong>Chunk Size</strong></p><pre>10485760</pre><hr /><p><strong>mapi_post URL</strong></p><pre>https://mapi.sitelock.com/v3/connect/</pre><hr /><p><strong>mapi_post_request</strong></p><pre>Array
(
    [pluginVersion] => 100.0.0
    [apiTargetVersion] => 3.0.0
    [token] => a21***
    [requests] => Array
        (
            [id] => 5cf5d30f0b29eac701d8746b41513446-17615703163841
            [action] => s3_get_enc_info
            [params] => Array
                (
                    [site_id] => 40186158
                    [queue_id] => 5528611
                )

        )

)
</pre><hr /><p><strong>curl_getinfo()</strong></p><pre>Array
(
    [url] => https://mapi.sitelock.com/v3/connect/
    [content_type] => text/html; charset=UTF-8
    [http_code] => 200
    [header_size] => 762
    [request_size] => 514
    [filetime] => -1
    [ssl_verify_result] => 0
    [redirect_count] => 0
    [total_time] => 0.225404
    [namelookup_time] => 3.5E-5
    [connect_time] => 0.005487
    [pretransfer_time] => 0.026439
    [size_upload] => 328
    [size_download] => 833
    [speed_download] => 3702
    [speed_upload] => 1457
    [download_content_length] => -1
    [upload_content_length] => 328
    [starttransfer_time] => 0.225347
    [redirect_time] => 0
    [redirect_url] => 
    [primary_ip] => 45.60.12.54
    [certinfo] => Array
        (
        )

    [primary_port] => 443
    [local_ip] => 213.165.236.14
    [local_port] => 57368
    [http_version] => 2
    [protocol] => 2
    [ssl_verifyresult] => 0
    [scheme] => HTTPS
    [appconnect_time_us] => 26353
    [connect_time_us] => 5487
    [namelookup_time_us] => 35
    [pretransfer_time_us] => 26439
    [redirect_time_us] => 0
    [starttransfer_time_us] => 225347
    [total_time_us] => 225404
)
</pre><hr /><p><strong>Received encryption details</strong></p><pre>Array
(
    [cipher] => aes-256-cbc
    [key] => SXc***
    [iv] => kGD***
)
</pre><hr /><p><strong>_TABLES</strong></p><pre>Array
(
    [oc_gs_languages] => Array
        (
            [last_id] => 0
        )

    [oc_hittech_fedex_details_new] => Array
        (
            [last_id] => 0
        )

    [oc_theme] => Array
        (
            [last_id] => 0
        )

    [oc_module] => Array
        (
            [last_id] => 0
        )

    [oc_download] => Array
        (
            [last_id] => 0
        )

    [oc_url_redirect] => Array
        (
            [last_id] => 0
        )

    [oc_webservice_cms_page_data] => Array
        (
            [last_id] => 0
        )

    [oc_user_group] => Array
        (
            [last_id] => 0
        )

    [oc_product_description] => Array
        (
            [last_id] => 0
        )

    [oc_option] => Array
        (
            [last_id] => 0
        )

    [oc_return_action] => Array
        (
            [last_id] => 0
        )

    [oc_url_absolute] => Array
        (
            [last_id] => 0
        )

    [oc_length_class_description] => Array
        (
            [last_id] => 0
        )

    [oc_tmdpolicy_request] => Array
        (
            [last_id] => 0
        )

    [oc_kbsociallogin_users] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_blog_post_description] => Array
        (
            [last_id] => 0
        )

    [oc_order_product] => Array
        (
            [last_id] => 0
        )

    [oc_option_value] => Array
        (
            [last_id] => 0
        )

    [oc_order_option] => Array
        (
            [last_id] => 0
        )

    [oc_abandonedcarts_settings] => Array
        (
            [last_id] => 0
        )

    [oc_api] => Array
        (
            [last_id] => 0
        )

    [oc_order_shipment] => Array
        (
            [last_id] => 0
        )

    [oc_imiw_admin_product_minifier] => Array
        (
            [last_id] => 0
        )

    [oc_order_voucher] => Array
        (
            [last_id] => 0
        )

    [oc_google_feed_product_related_data] => Array
        (
            [last_id] => 0
        )

    [oc_api_session] => Array
        (
            [last_id] => 0
        )

    [oc_layout] => Array
        (
            [last_id] => 0
        )

    [oc_tax_rule] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_layout] => Array
        (
            [last_id] => 0
        )

    [oc_google_base_category] => Array
        (
            [last_id] => 0
        )

    [oc_notify_when_available] => Array
        (
            [last_id] => 0
        )

    [oc_custom_field_value_description] => Array
        (
            [last_id] => 0
        )

    [oc_information_description] => Array
        (
            [last_id] => 0
        )

    [oc_order_recurring_transaction] => Array
        (
            [last_id] => 0
        )

    [oc_customer_login] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_variable] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_blog_comments] => Array
        (
            [last_id] => 0
        )

    [oc_review] => Array
        (
            [last_id] => 0
        )

    [oc_filter_group_description] => Array
        (
            [last_id] => 0
        )

    [oc_tmd_deleterequest] => Array
        (
            [last_id] => 0
        )

    [oc_prodinfo] => Array
        (
            [last_id] => 0
        )

    [oc_attribute_description] => Array
        (
            [last_id] => 0
        )

    [oc_email_template_description] => Array
        (
            [last_id] => 0
        )

    [oc_gs_queue] => Array
        (
            [last_id] => 0
        )

    [oc_return_reason] => Array
        (
            [last_id] => 0
        )

    [oc_filter_description] => Array
        (
            [last_id] => 0
        )

    [oc_seo_manufacturer_description] => Array
        (
            [last_id] => 0
        )

    [oc_upload] => Array
        (
            [last_id] => 0
        )

    [oc_gs_feeds] => Array
        (
            [last_id] => 0
        )

    [oc_option_value_description] => Array
        (
            [last_id] => 0
        )

    [oc_product] => Array
        (
            [last_id] => 0
        )

    [oc_order_history] => Array
        (
            [last_id] => 0
        )

    [oc_product_image] => Array
        (
            [last_id] => 0
        )

    [oc_kbmobileapp_top_category] => Array
        (
            [last_id] => 0
        )

    [oc_layout_route] => Array
        (
            [last_id] => 0
        )

    [oc_video_gallery] => Array
        (
            [last_id] => 0
        )

    [oc_order_status] => Array
        (
            [last_id] => 0
        )

    [oc_address] => Array
        (
            [last_id] => 0
        )

    [oc_user] => Array
        (
            [last_id] => 0
        )

    [oc_voucher_theme] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_setting] => Array
        (
            [last_id] => 0
        )

    [oc_abandonedcarts_coupon] => Array
        (
            [last_id] => 0
        )

    [oc_abandonedcarts] => Array
        (
            [last_id] => 0
        )

    [oc_google_feed_category_mapping] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_blog_post] => Array
        (
            [last_id] => 0
        )

    [oc_session] => Array
        (
            [last_id] => 0
        )

    [oc_weight_class_description] => Array
        (
            [last_id] => 0
        )

    [oc_logger] => Array
        (
            [last_id] => 0
        )

    [oc_gs_profiles] => Array
        (
            [last_id] => 0
        )

    [oc_kb_mobileapp_ori_translations] => Array
        (
            [last_id] => 0
        )

    [oc_kbmobileapp_component_types] => Array
        (
            [last_id] => 0
        )

    [oc_customer_reward] => Array
        (
            [last_id] => 0
        )

    [oc_webservice_cms_page_title] => Array
        (
            [last_id] => 0
        )

    [oc_hittech_ups_details] => Array
        (
            [last_id] => 0
        )

    [oc_customer_group_description] => Array
        (
            [last_id] => 0
        )

    [oc_recurring_description] => Array
        (
            [last_id] => 0
        )

    [oc_gs_countries] => Array
        (
            [last_id] => 0
        )

    [oc_url_404] => Array
        (
            [last_id] => 0
        )

    [oc_language] => Array
        (
            [last_id] => 0
        )

    [oc_translation] => Array
        (
            [last_id] => 0
        )

    [oc_download_description] => Array
        (
            [last_id] => 0
        )

    [oc_order_total] => Array
        (
            [last_id] => 0
        )

    [oc_fcm_details] => Array
        (
            [last_id] => 0
        )

    [oc_modification] => Array
        (
            [last_id] => 0
        )

    [oc_attribute_group_description] => Array
        (
            [last_id] => 0
        )

    [oc_shipping_courier] => Array
        (
            [last_id] => 0
        )

    [oc_email_template] => Array
        (
            [last_id] => 0
        )

    [oc_abandoned_template_description] => Array
        (
            [last_id] => 0
        )

    [oc_event] => Array
        (
            [last_id] => 0
        )

    [oc_abandonedcarts_mail_description] => Array
        (
            [last_id] => 0
        )

    [oc_prodinfo_description] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_blog_category] => Array
        (
            [last_id] => 0
        )

    [oc_webservice_banner] => Array
        (
            [last_id] => 0
        )

    [oc_stripe_customer] => Array
        (
            [last_id] => 0
        )

    [oc_kb_mobileapp_layouts] => Array
        (
            [last_id] => 0
        )

    [oc_extension_path] => Array
        (
            [last_id] => 0
        )

    [oc_extension] => Array
        (
            [last_id] => 0
        )

    [oc_voucher_theme_description] => Array
        (
            [last_id] => 0
        )

    [oc_kbsociallogin_layout] => Array
        (
            [last_id] => 0
        )

    [oc_zone] => Array
        (
            [last_id] => 0
        )

    [oc_dam_setting] => Array
        (
            [last_id] => 0
        )

    [oc_webservice_banner_image] => Array
        (
            [last_id] => 0
        )

    [oc_kbmobileapp_unique_verification] => Array
        (
            [last_id] => 0
        )

    [oc_return_status] => Array
        (
            [last_id] => 0
        )

    [oc_seo_url] => Array
        (
            [last_id] => 0
        )

    [oc_product_option] => Array
        (
            [last_id] => 0
        )

    [oc_location] => Array
        (
            [last_id] => 0
        )

    [oc_product_video] => Array
        (
            [last_id] => 0
        )

    [oc_layout_module] => Array
        (
            [last_id] => 0
        )

    [oc_gs_category_mapping] => Array
        (
            [last_id] => 0
        )

    [oc_setting] => Array
        (
            [last_id] => 0
        )

    [oc_manufacturer] => Array
        (
            [last_id] => 0
        )

    [oc_custom_field] => Array
        (
            [last_id] => 0
        )

    [oc_kbsociallogin_login_history] => Array
        (
            [last_id] => 0
        )

    [oc_banner] => Array
        (
            [last_id] => 0
        )

    [oc_url_autolink] => Array
        (
            [last_id] => 0
        )

    [oc_imiw_categories] => Array
        (
            [last_id] => 0
        )

    [oc_google_feed_inserted_product_mapping] => Array
        (
            [last_id] => 0
        )

    [oc_customer_approval] => Array
        (
            [last_id] => 0
        )

    [oc_customer_transaction] => Array
        (
            [last_id] => 0
        )

    [oc_order] => Array
        (
            [last_id] => 0
        )

    [oc_bulkgate_module] => Array
        (
            [last_id] => 0
        )

    [oc_order_recurring] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_product_attribute] => Array
        (
            [last_id] => 0
        )

    [oc_tmd_persnaldatareport] => Array
        (
            [last_id] => 0
        )

    [oc_kbb_components_heading] => Array
        (
            [last_id] => 0
        )

    [oc_customer_ip] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_message] => Array
        (
            [last_id] => 0
        )

    [oc_module_unused_image] => Array
        (
            [last_id] => 0
        )

    [oc_information] => Array
        (
            [last_id] => 0
        )

    [oc_kbmobileapp_product_data] => Array
        (
            [last_id] => 0
        )

    [google_language_translator] => Array
        (
            [last_id] => 0
        )

    [oc_gs_categories] => Array
        (
            [last_id] => 0
        )

    [oc_extension_install] => Array
        (
            [last_id] => 0
        )

    [oc_gs_currencies] => Array
        (
            [last_id] => 0
        )

    [oc_option_description] => Array
        (
            [last_id] => 0
        )

    [oc_shipinschk] => Array
        (
            [last_id] => 0
        )

    [oc_gs_audit_log] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_blog_category_description] => Array
        (
            [last_id] => 0
        )

    [oc_abandonedcart_history] => Array
        (
            [last_id] => 0
        )

    [oc_country] => Array
        (
            [last_id] => 0
        )

    [oc_return] => Array
        (
            [last_id] => 0
        )

    [oc_geo_zone] => Array
        (
            [last_id] => 0
        )

    [oc_coupon_history] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_module] => Array
        (
            [last_id] => 0
        )

    [oc_kbmobileapp_banners] => Array
        (
            [last_id] => 0
        )

    [oc_imiw_links] => Array
        (
            [last_id] => 0
        )

    [oc_category] => Array
        (
            [last_id] => 0
        )

    [oc_push_notification_history] => Array
        (
            [last_id] => 0
        )

    [oc_gs_products_list] => Array
        (
            [last_id] => 0
        )

    [oc_customer_history] => Array
        (
            [last_id] => 0
        )

    [oc_customer_search] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_skin] => Array
        (
            [last_id] => 0
        )

    [oc_webservice_banner_image_description] => Array
        (
            [last_id] => 0
        )

    [oc_customer_online] => Array
        (
            [last_id] => 0
        )

    [oc_abandonedcart] => Array
        (
            [last_id] => 0
        )

    [oc_video_gallery_video] => Array
        (
            [last_id] => 0
        )

    [oc_customer] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_style] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_newsletter] => Array
        (
            [last_id] => 0
        )

    [oc_banner_image] => Array
        (
            [last_id] => 0
        )

    [oc_voucher] => Array
        (
            [last_id] => 0
        )

    [oc_tax_class] => Array
        (
            [last_id] => 0
        )

    [oc_customer_activity] => Array
        (
            [last_id] => 0
        )

    [oc_abandonedcarts_mail] => Array
        (
            [last_id] => 0
        )

    [oc_marketing] => Array
        (
            [last_id] => 0
        )

    [oc_notify_when_available_email] => Array
        (
            [last_id] => 0
        )

    [oc_product_attribute] => Array
        (
            [last_id] => 0
        )

    [oc_return_history] => Array
        (
            [last_id] => 0
        )

    [oc_category_description] => Array
        (
            [last_id] => 0
        )

    [oc_stock_status] => Array
        (
            [last_id] => 0
        )

    [oc_coupon] => Array
        (
            [last_id] => 0
        )

    [oc_customer_affiliate] => Array
        (
            [last_id] => 0
        )

    [oc_cart] => Array
        (
            [last_id] => 0
        )

    [oc_tax_rate] => Array
        (
            [last_id] => 0
        )

    [oc_api_ip] => Array
        (
            [last_id] => 0
        )

    [oc_statistics] => Array
        (
            [last_id] => 0
        )

    [oc_journal3_skin_setting] => Array
        (
            [last_id] => 0
        )

    [oc_kb_mobileapp_browser_orders] => Array
        (
            [last_id] => 0
        )

    [oc_currency] => Array
        (
            [last_id] => 0
        )

    [oc_custom_field_description] => Array
        (
            [last_id] => 0
        )

    [oc_kb_mobileapp_translations] => Array
        (
            [last_id] => 0
        )

    [oc_store] => Array
        (
            [last_id] => 0
        )

    [oc_product_option_value] => Array
        (
            [last_id] => 0
        )

)
</pre><hr /><p><strong>_QUOTA</strong></p><pre>0</pre><hr /><p><strong>_SCANDATE</strong></p><pre>1761570316</pre><hr /><p><strong>sl_get_bullet_lock_path:</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/78266ea8d0226e759697b06375373fc4.php.lock</pre><hr /><p><strong>sl_is_bullet_locked check:</strong></p><pre>not locked (no lock file)</pre><hr /><p><strong>sl_get_bullet_lock_path:</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/78266ea8d0226e759697b06375373fc4.php.lock</pre><hr /><p><strong>sl_lock_the_bullet: bytes written</strong></p><pre>10</pre><hr /><p><strong>Starting MySQLi constructor</strong></p><pre></pre><hr /><p><strong>MySQL Version Detected</strong></p><pre>10.6.23-MariaDB</pre><hr /><p><strong>Tables Summary</strong></p><pre></pre><hr /><pre>oc_gs_languages: 174 records pulled; 174 running total.</pre><hr /><pre>oc_hittech_fedex_details_new: 0 records pulled; 174 running total.</pre><hr /><pre>oc_theme: 0 records pulled; 174 running total.</pre><hr /><pre>oc_module: 5 records pulled; 179 running total.</pre><hr /><pre>oc_download: 74 records pulled; 253 running total.</pre><hr /><pre>oc_url_redirect: 5 records pulled; 258 running total.</pre><hr /><pre>oc_webservice_cms_page_data: 9 records pulled; 267 running total.</pre><hr /><pre>oc_user_group: 3 records pulled; 270 running total.</pre><hr /><pre>oc_product_description: 1315 records pulled; 1585 running total.</pre><hr /><pre>oc_option: 30 records pulled; 1615 running total.</pre><hr /><pre>oc_return_action: 3 records pulled; 1618 running total.</pre><hr /><pre>oc_url_absolute: 0 records pulled; 1618 running total.</pre><hr /><pre>oc_length_class_description: 3 records pulled; 1621 running total.</pre><hr /><pre>oc_tmdpolicy_request: 0 records pulled; 1621 running total.</pre><hr /><pre>oc_kbsociallogin_users: 0 records pulled; 1621 running total.</pre><hr /><pre>oc_journal3_blog_post_description: 16 records pulled; 1637 running total.</pre><hr /><pre>oc_order_product: 2500 records pulled; 4137 running total.</pre><hr /><pre>oc_option_value: 151 records pulled; 4288 running total.</pre><hr /><pre>oc_order_option: 189 records pulled; 4477 running total.</pre><hr /><pre>oc_abandonedcarts_settings: 1 records pulled; 4478 running total.</pre><hr /><pre>oc_api: 1 records pulled; 4479 running total.</pre><hr /><pre>oc_order_shipment: 0 records pulled; 4479 running total.</pre><hr /><pre>oc_imiw_admin_product_minifier: 16 records pulled; 4495 running total.</pre><hr /><pre>oc_order_voucher: 74 records pulled; 4569 running total.</pre><hr /><pre>oc_google_feed_product_related_data: 0 records pulled; 4569 running total.</pre><hr /><pre>oc_api_session: 42 records pulled; 4611 running total.</pre><hr /><pre>oc_layout: 23 records pulled; 4634 running total.</pre><hr /><pre>oc_tax_rule: 4 records pulled; 4638 running total.</pre><hr /><pre>oc_journal3_layout: 24 records pulled; 4662 running total.</pre><hr /><pre>oc_google_base_category: 0 records pulled; 4662 running total.</pre><hr /><pre>oc_notify_when_available: 4 records pulled; 4666 running total.</pre><hr /><pre>oc_custom_field_value_description: 2 records pulled; 4668 running total.</pre><hr /><pre>oc_information_description: 10 records pulled; 4678 running total.</pre><hr /><pre>oc_order_recurring_transaction: 0 records pulled; 4678 running total.</pre><hr /><pre>oc_customer_login: 2500 records pulled; 7178 running total.</pre><hr /><pre>oc_journal3_variable: 73 records pulled; 7251 running total.</pre><hr /><pre>oc_journal3_blog_comments: 203 records pulled; 7454 running total.</pre><hr /><pre>oc_review: 108 records pulled; 7562 running total.</pre><hr /><pre>oc_filter_group_description: 2 records pulled; 7564 running total.</pre><hr /><pre>oc_tmd_deleterequest: 0 records pulled; 7564 running total.</pre><hr /><pre>oc_prodinfo: 1 records pulled; 7565 running total.</pre><hr /><pre>oc_attribute_description: 16 records pulled; 7581 running total.</pre><hr /><pre>oc_email_template_description: 12 records pulled; 7593 running total.</pre><hr /><pre>oc_gs_queue: 0 records pulled; 7593 running total.</pre><hr /><pre>oc_return_reason: 5 records pulled; 7598 running total.</pre><hr /><pre>oc_filter_description: 10 records pulled; 7608 running total.</pre><hr /><pre>oc_seo_manufacturer_description: 13 records pulled; 7621 running total.</pre><hr /><pre>oc_upload: 0 records pulled; 7621 running total.</pre><hr /><pre>oc_gs_feeds: 1 records pulled; 7622 running total.</pre><hr /><pre>oc_option_value_description: 151 records pulled; 7773 running total.</pre><hr /><pre>oc_product: 1315 records pulled; 9088 running total.</pre><hr /><pre>oc_order_history: 2500 records pulled; 11588 running total.</pre><hr /><pre>oc_product_image: 1130 records pulled; 12718 running total.</pre><hr /><pre>oc_kbmobileapp_top_category: 14 records pulled; 12732 running total.</pre><hr /><pre>oc_layout_route: 19 records pulled; 12751 running total.</pre><hr /><pre>oc_video_gallery: 6 records pulled; 12757 running total.</pre><hr /><pre>oc_order_status: 15 records pulled; 12772 running total.</pre><hr /><pre>oc_address: 2500 records pulled; 15272 running total.</pre><hr /><pre>oc_user: 5 records pulled; 15277 running total.</pre><hr /><pre>oc_voucher_theme: 11 records pulled; 15288 running total.</pre><hr /><pre>oc_journal3_setting: 71 records pulled; 15359 running total.</pre><hr /><pre>oc_abandonedcarts_coupon: 0 records pulled; 15359 running total.</pre><hr /><pre>oc_abandonedcarts: 2500 records pulled; 17859 running total.</pre><hr /><pre>oc_google_feed_category_mapping: 0 records pulled; 17859 running total.</pre><hr /><pre>oc_journal3_blog_post: 16 records pulled; 17875 running total.</pre><hr /><pre>oc_session: 4 records pulled; 17879 running total.</pre><hr /><pre>oc_weight_class_description: 4 records pulled; 17883 running total.</pre><hr /><pre>oc_logger: 2500 records pulled; 20383 running total.</pre><hr /><pre>oc_gs_profiles: 1 records pulled; 20384 running total.</pre><hr /><pre>oc_kb_mobileapp_ori_translations: 440 records pulled; 20824 running total.</pre><hr /><pre>oc_kbmobileapp_component_types: 9 records pulled; 20833 running total.</pre><hr /><pre>oc_customer_reward: 0 records pulled; 20833 running total.</pre><hr /><pre>oc_webservice_cms_page_title: 9 records pulled; 20842 running total.</pre><hr /><pre>oc_hittech_ups_details: 0 records pulled; 20842 running total.</pre><hr /><pre>oc_customer_group_description: 1 records pulled; 20843 running total.</pre><hr /><pre>oc_recurring_description: 0 records pulled; 20843 running total.</pre><hr /><pre>oc_gs_countries: 94 records pulled; 20937 running total.</pre><hr /><pre>oc_url_404: 206 records pulled; 21143 running total.</pre><hr /><pre>oc_language: 1 records pulled; 21144 running total.</pre><hr /><pre>oc_translation: 0 records pulled; 21144 running total.</pre><hr /><pre>oc_download_description: 74 records pulled; 21218 running total.</pre><hr /><pre>oc_order_total: 2500 records pulled; 23718 running total.</pre><hr /><pre>oc_fcm_details: 213 records pulled; 23931 running total.</pre><hr /><pre>oc_modification: 53 records pulled; 23984 running total.</pre><hr /><pre>oc_attribute_group_description: 6 records pulled; 23990 running total.</pre><hr /><pre>oc_shipping_courier: 6 records pulled; 23996 running total.</pre><hr /><pre>oc_email_template: 12 records pulled; 24008 running total.</pre><hr /><pre>oc_abandoned_template_description: 1 records pulled; 24009 running total.</pre><hr /><pre>oc_event: 301 records pulled; 24310 running total.</pre><hr /><pre>oc_abandonedcarts_mail_description: 1 records pulled; 24311 running total.</pre><hr /><pre>oc_prodinfo_description: 1 records pulled; 24312 running total.</pre><hr /><pre>oc_journal3_blog_category: 0 records pulled; 24312 running total.</pre><hr /><pre>oc_webservice_banner: 2 records pulled; 24314 running total.</pre><hr /><pre>oc_stripe_customer: 0 records pulled; 24314 running total.</pre><hr /><pre>oc_kb_mobileapp_layouts: 3 records pulled; 24317 running total.</pre><hr /><pre>oc_extension_path: 2500 records pulled; 26817 running total.</pre><hr /><pre>oc_extension: 89 records pulled; 26906 running total.</pre><hr /><pre>oc_voucher_theme_description: 11 records pulled; 26917 running total.</pre><hr /><pre>oc_kbsociallogin_layout: 0 records pulled; 26917 running total.</pre><hr /><pre>oc_zone: 2500 records pulled; 29417 running total.</pre><hr /><pre>oc_dam_setting: 1 records pulled; 29418 running total.</pre><hr /><pre>oc_webservice_banner_image: 0 records pulled; 29418 running total.</pre><hr /><pre>oc_kbmobileapp_unique_verification: 4 records pulled; 29422 running total.</pre><hr /><pre>oc_return_status: 3 records pulled; 29425 running total.</pre><hr /><pre>oc_seo_url: 1544 records pulled; 30969 running total.</pre><hr /><pre>oc_product_option: 30 records pulled; 30999 running total.</pre><hr /><pre>oc_location: 0 records pulled; 30999 running total.</pre><hr /><pre>oc_product_video: 121 records pulled; 31120 running total.</pre><hr /><pre>oc_layout_module: 12 records pulled; 31132 running total.</pre><hr /><pre>oc_gs_category_mapping: 1 records pulled; 31133 running total.</pre><hr /><pre>oc_setting: 1271 records pulled; 32404 running total.</pre><hr /><pre>oc_manufacturer: 13 records pulled; 32417 running total.</pre><hr /><pre>oc_custom_field: 2 records pulled; 32419 running total.</pre><hr /><pre>oc_kbsociallogin_login_history: 0 records pulled; 32419 running total.</pre><hr /><pre>oc_banner: 6 records pulled; 32425 running total.</pre><hr /><pre>oc_url_autolink: 1 records pulled; 32426 running total.</pre><hr /><pre>oc_imiw_categories: 1 records pulled; 32427 running total.</pre><hr /><pre>oc_google_feed_inserted_product_mapping: 0 records pulled; 32427 running total.</pre><hr /><pre>oc_customer_approval: 9 records pulled; 32436 running total.</pre><hr /><pre>oc_customer_transaction: 0 records pulled; 32436 running total.</pre><hr /><pre>oc_order: 2500 records pulled; 34936 running total.</pre><hr /><pre>oc_bulkgate_module: 2 records pulled; 34938 running total.</pre><hr /><pre>oc_order_recurring: 0 records pulled; 34938 running total.</pre><hr /><pre>oc_journal3_product_attribute: 0 records pulled; 34938 running total.</pre><hr /><pre>oc_tmd_persnaldatareport: 0 records pulled; 34938 running total.</pre><hr /><pre>oc_kbb_components_heading: 7 records pulled; 34945 running total.</pre><hr /><pre>oc_customer_ip: 2500 records pulled; 37445 running total.</pre><hr /><pre>oc_journal3_message: 336 records pulled; 37781 running total.</pre><hr /><pre>oc_module_unused_image: 2500 records pulled; 40281 running total.</pre><hr /><pre>oc_information: 10 records pulled; 40291 running total.</pre><hr /><pre>oc_kbmobileapp_product_data: 4 records pulled; 40295 running total.</pre><hr /><pre>google_language_translator: 1 records pulled; 40296 running total.</pre><hr /><pre>oc_gs_categories: 2500 records pulled; 42796 running total.</pre><hr /><pre>oc_extension_install: 88 records pulled; 42884 running total.</pre><hr /><pre>oc_gs_currencies: 94 records pulled; 42978 running total.</pre><hr /><pre>oc_option_description: 30 records pulled; 43008 running total.</pre><hr /><pre>oc_shipinschk: 1 records pulled; 43009 running total.</pre><hr /><pre>oc_gs_audit_log: 0 records pulled; 43009 running total.</pre><hr /><pre>oc_journal3_blog_category_description: 0 records pulled; 43009 running total.</pre><hr /><pre>oc_abandonedcart_history: 0 records pulled; 43009 running total.</pre><hr /><pre>oc_country: 253 records pulled; 43262 running total.</pre><hr /><pre>oc_return: 14 records pulled; 43276 running total.</pre><hr /><pre>oc_geo_zone: 3 records pulled; 43279 running total.</pre><hr /><pre>oc_coupon_history: 1945 records pulled; 45224 running total.</pre><hr /><pre>oc_journal3_module: 108 records pulled; 45332 running total.</pre><hr /><pre>oc_kbmobileapp_banners: 27 records pulled; 45359 running total.</pre><hr /><pre>oc_imiw_links: 1 records pulled; 45360 running total.</pre><hr /><pre>oc_category: 177 records pulled; 45537 running total.</pre><hr /><pre>oc_push_notification_history: 0 records pulled; 45537 running total.</pre><hr /><pre>oc_gs_products_list: 0 records pulled; 45537 running total.</pre><hr /><pre>oc_customer_history: 3 records pulled; 45540 running total.</pre><hr /><pre>oc_customer_search: 2500 records pulled; 48040 running total.</pre><hr /><pre>oc_journal3_skin: 1 records pulled; 48041 running total.</pre><hr /><pre>oc_webservice_banner_image_description: 0 records pulled; 48041 running total.</pre><hr /><pre>oc_customer_online: 2 records pulled; 48043 running total.</pre><hr /><pre>oc_abandonedcart: 0 records pulled; 48043 running total.</pre><hr /><pre>oc_video_gallery_video: 10 records pulled; 48053 running total.</pre><hr /><pre>oc_customer: 2500 records pulled; 50553 running total.</pre><hr /><pre>oc_journal3_style: 102 records pulled; 50655 running total.</pre><hr /><pre>oc_journal3_newsletter: 1021 records pulled; 51676 running total.</pre><hr /><pre>oc_banner_image: 20 records pulled; 51696 running total.</pre><hr /><pre>oc_voucher: 180 records pulled; 51876 running total.</pre><hr /><pre>oc_tax_class: 2 records pulled; 51878 running total.</pre><hr /><pre>oc_customer_activity: 2500 records pulled; 54378 running total.</pre><hr /><pre>oc_abandonedcarts_mail: 1 records pulled; 54379 running total.</pre><hr /><pre>oc_marketing: 0 records pulled; 54379 running total.</pre><hr /><pre>oc_notify_when_available_email: 0 records pulled; 54379 running total.</pre><hr /><pre>oc_product_attribute: 0 records pulled; 54379 running total.</pre><hr /><pre>oc_return_history: 0 records pulled; 54379 running total.</pre><hr /><pre>oc_category_description: 177 records pulled; 54556 running total.</pre><hr /><pre>oc_stock_status: 4 records pulled; 54560 running total.</pre><hr /><pre>oc_coupon: 6 records pulled; 54566 running total.</pre><hr /><pre>oc_customer_affiliate: 22 records pulled; 54588 running total.</pre><hr /><pre>oc_cart: 2500 records pulled; 57088 running total.</pre><hr /><pre>oc_tax_rate: 4 records pulled; 57092 running total.</pre><hr /><pre>oc_api_ip: 91 records pulled; 57183 running total.</pre><hr /><pre>oc_statistics: 0 records pulled; 57183 running total.</pre><hr /><pre>oc_journal3_skin_setting: 1202 records pulled; 58385 running total.</pre><hr /><pre>oc_kb_mobileapp_browser_orders: 0 records pulled; 58385 running total.</pre><hr /><pre>oc_currency: 1 records pulled; 58386 running total.</pre><hr /><pre>oc_custom_field_description: 2 records pulled; 58388 running total.</pre><hr /><pre>oc_kb_mobileapp_translations: 0 records pulled; 58388 running total.</pre><hr /><pre>oc_store: 0 records pulled; 58388 running total.</pre><hr /><pre>oc_product_option_value: 110 records pulled; 58498 running total.</pre><hr /><p><strong>Total time to query and write tables</strong></p><pre>3.2692060470581</pre><hr /><p><strong>sl_archive_files</strong></p><pre>start</pre><hr /><p><strong>sl_archive_files 5</strong></p><pre>Send to CLI0</pre><hr /><p><strong>Failed shell_exec for /usr/bin/zip.</strong></p><pre>shell_exec_not_available</pre><hr /><p><strong>Attempting to use ZipArchive.</strong></p><pre>good luck!</pre><hr /><p><strong>archived file</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.0430ef19c8e8f00d543076064009cea3/0892745001761570319.zip</pre><hr /><p><strong>Original ZIP size</strong></p><pre>9932837</pre><hr /><p><strong>bytes written to /home/ginger101/public_html/dyecraftdirect.com/tmp/.0430ef19c8e8f00d543076064009cea3/0892745001761570319.zip.0</strong></p><pre>9932848</pre><hr /><p><strong>sl_wrap_up_the_scan with status ok</strong></p><pre></pre><hr /><p><strong>Returned technical error details, if any</strong></p><pre></pre><hr /><p><strong>mapi_post URL</strong></p><pre>https://mapi.sitelock.com/v3/connect/</pre><hr /><p><strong>mapi_post_request</strong></p><pre>Array
(
    [pluginVersion] => 100.0.0
    [apiTargetVersion] => 3.0.0
    [token] => a21***
    [requests] => Array
        (
            [id] => 83f08aabad96fc11340ce70b7e0561dd-17615703224287
            [action] => s3_queue
            [params] => Array
                (
                    [site_id] => 40186158
                    [queue_id] => 5528611
                    [client_id] => 9816
                    [feature_code] => db_scan
                    [status] => ok
                    [url] => .0430ef19c8e8f00d543076064009cea3/0892745001761570319.zip
                    [zip_file_info] => .0430ef19c8e8f00d543076064009cea3/0892745001761570319.zip-descriptor
                )

        )

)
</pre><hr /><p><strong>curl_getinfo()</strong></p><pre>Array
(
    [url] => https://mapi.sitelock.com/v3/connect/
    [content_type] => text/html; charset=UTF-8
    [http_code] => 200
    [header_size] => 767
    [request_size] => 790
    [filetime] => -1
    [ssl_verify_result] => 0
    [redirect_count] => 0
    [total_time] => 0.265624
    [namelookup_time] => 3.0E-5
    [connect_time] => 0.006807
    [pretransfer_time] => 0.026691
    [size_upload] => 604
    [size_download] => 770
    [speed_download] => 2905
    [speed_upload] => 2279
    [download_content_length] => -1
    [upload_content_length] => 604
    [starttransfer_time] => 0.264877
    [redirect_time] => 0
    [redirect_url] => 
    [primary_ip] => 45.60.12.54
    [certinfo] => Array
        (
        )

    [primary_port] => 443
    [local_ip] => 213.165.236.14
    [local_port] => 58270
    [http_version] => 2
    [protocol] => 2
    [ssl_verifyresult] => 0
    [scheme] => HTTPS
    [appconnect_time_us] => 26611
    [connect_time_us] => 6807
    [namelookup_time_us] => 30
    [pretransfer_time_us] => 26691
    [redirect_time_us] => 0
    [starttransfer_time_us] => 264877
    [total_time_us] => 265624
)
</pre><hr /><p><strong>mapi_response</strong></p><pre><textarea style="width:99%;height:100px;">{"apiVersion":"3.0.1","status":"ok","globalResponse":null,"banner":null,"forceLogout":false,"newToken":null,"now":1761570322,"responses":[{"id":"83f08aabad96fc11340ce70b7e0561dd-17615703224287","data":{"s3_status":"approved","queue_id":"5528611"},"raw_api_url":"https:\/\/api.sitelock.com\/v1\/dbscan\/queue","raw_response":{"@attributes":{"version":"1.1","encoding":"UTF-8"},"dbscan":{"queue_id":"5528611","status":"approved"}},"raw_request":"<xml>\n  <dbscan site_id=\"40186158\" queue_id=\"5528611\" url=\".0430ef19c8e8f00d543076064009cea3\/0892745001761570319.zip\" status=\"ok\" zip_file_info=\".0430ef19c8e8f00d543076064009cea3\/0892745001761570319.zip-descriptor\" client_id=\"9816\"\/>\n<\/xml>","user_agent":"SiteLock Bullet for DBScan (other)","status":"ok"}]}</textarea></pre><hr /><p><strong>sl_get_bullet_lock_path:</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/78266ea8d0226e759697b06375373fc4.php.lock</pre><hr /><p><strong>sl_is_bullet_locked check time:</strong></p><pre>current: 1761570322, locked at: 1761570316, diff: 6, still locked: Yes</pre><hr /><p><strong>sl_get_bullet_lock_path:</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/78266ea8d0226e759697b06375373fc4.php.lock</pre><hr /><p><strong>sl_unlock_the_bullet: status</strong></p><pre>success</pre><hr /><p><strong>sl_delete_unique_directory - unlink( $descriptor_file[0] );</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.68c7c7d6f3c25fe08b86edaee5d7669d/0873313001758710128.zip-descriptor</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.68c7c7d6f3c25fe08b86edaee5d7669d/0873313001758710128.zip.0</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.68c7c7d6f3c25fe08b86edaee5d7669d/0873313001758710128.zip.1</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.68c7c7d6f3c25fe08b86edaee5d7669d/0873313001758710128.zip.2</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.68c7c7d6f3c25fe08b86edaee5d7669d/0873313001758710128.zip.3</pre><hr /><p><strong>sl_delete_unique_directory - rmdir( $path )</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.68c7c7d6f3c25fe08b86edaee5d7669d</pre><hr /><p><strong>Bullet run time, seconds.</strong></p><pre>6.59</pre><hr />