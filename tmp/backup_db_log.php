<div style='background-color:#AFF'><h3>Encryption</h3><p><strong>PHP Version</strong></p><pre>8.2.29</pre><hr /><p><strong>Cryptor</strong></p><pre>OpenSSL</pre><hr /><p><strong>Default Cipher</strong></p><pre>aes-256-cbc</pre><hr /><p><strong>mb_internal_encoding</strong></p><pre>UTF-8</pre><hr /><div style='background-color:#AAA'><h3>IP Validation</h3><p><strong>$headers from sl_get_ip()</strong></p><pre>Array
(
    [X-Https] => 1
    [Incap-Client-Ip] => 184.154.76.7
    [X-Forwarded-For] => 184.154.76.7
    [Cdn-Loop] => IncapCDN; id="304_3142932_0"
    [User-Agent] => SiteLock (Module: SmartDB; Source: https://www.sitelock.com/; Version: 1.0)
    [Host] => dyecraftdirect.com
    [Connection] => TE, close
    [Te] => deflate,gzip;q=0.3
)
</pre><hr /><p><strong>IP Check started in</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/310468803f6c1f82608b1ef8134ed75c.php</pre><hr /><p><strong>IP Check started at</strong></p><pre>2025-09-24T06:35:16-04:00</pre><hr /><p><strong>The following IPs will be tested</strong></p><pre>Array
(
    [0] => 184.154.76.7
    [1] => 198.143.60.2
)
</pre><hr /><p><strong>mapi_post URL</strong></p><pre>https://mapi.sitelock.com/v3/connect/</pre><hr /><p><strong>mapi_post_request</strong></p><pre>Array
(
    [pluginVersion] => 100.0.0
    [apiTargetVersion] => 3.0.0
    [token] => 66c***
    [requests] => Array
        (
            [id] => 2a05bb05b00b2b5d3f417b1e5337d28a-17587101168199
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
    [header_size] => 766
    [request_size] => 498
    [filetime] => -1
    [ssl_verify_result] => 0
    [redirect_count] => 0
    [total_time] => 0.254903
    [namelookup_time] => 0.000244
    [connect_time] => 0.005485
    [pretransfer_time] => 0.02075
    [size_upload] => 320
    [size_download] => 550
    [speed_download] => 2165
    [speed_upload] => 1259
    [download_content_length] => -1
    [upload_content_length] => 320
    [starttransfer_time] => 0.25478
    [redirect_time] => 0
    [redirect_url] => 
    [primary_ip] => 45.60.12.54
    [certinfo] => Array
        (
        )

    [primary_port] => 443
    [local_ip] => 213.165.236.14
    [local_port] => 57154
    [http_version] => 2
    [protocol] => 2
    [ssl_verifyresult] => 0
    [scheme] => HTTPS
    [appconnect_time_us] => 20694
    [connect_time_us] => 5485
    [namelookup_time_us] => 244
    [pretransfer_time_us] => 20750
    [redirect_time_us] => 0
    [starttransfer_time_us] => 254780
    [total_time_us] => 254903
)
</pre><hr /><p><strong>mapi_response</strong></p><pre><textarea style="width:99%;height:100px;">{"apiVersion":"3.0.1","status":"ok","globalResponse":null,"banner":null,"forceLogout":false,"newToken":null,"now":1758710117,"responses":[{"id":"2a05bb05b00b2b5d3f417b1e5337d28a-17587101168199","data":{"ip_address":"184.154.76.7","valid":true},"raw_api_url":"https:\/\/api.sitelock.com\/v1\/66c***\/dbscan\/checkip","raw_response":{"@attributes":{"version":"1.1","encoding":"UTF-8"},"checkIP":{"status":"1"}},"raw_request":{"site_id":"40186158","ip":"184.154.76.7"},"user_agent":"SiteLock Bullet for Backup","status":"ok"}]}</textarea></pre><hr /><pre>Ifsnop\Mysqldump is loaded into memory.</pre><hr /><div style='background-color:#AFA'><h3>BackupGrabAndZip</h3><p><strong>_POST</strong></p><pre>Array
(
)
</pre><hr /><p><strong>_GET (raw)</strong></p><pre>cmd=db_creds_ready&enc_db_creds=F6NsZateTovHRg7WxV4zHHkGZ1HzmaToTaDgPZDBpwZd4W9yMITyp1w%2Fmaq2u4L3VWjDgHdcLE0OQk3xnctHw7bi7iTJszgMLhBmQ9aIqXEKJQ03W%2BG%2F5r%2FdrDMubgyP8HefB%2F2NxJR5KCB1jnqHsg%3D%3D&smart_single_download_id=5500565</pre><hr /><p><strong>Detected memory_limit</strong></p><pre>1024M</pre><hr /><p><strong>Chunk Size</strong></p><pre>10485760</pre><hr /><p><strong>mapi_post URL</strong></p><pre>https://mapi.sitelock.com/v3/connect/</pre><hr /><p><strong>mapi_post_request</strong></p><pre>Array
(
    [pluginVersion] => 100.0.0
    [apiTargetVersion] => 3.0.0
    [token] => 66c***
    [requests] => Array
        (
            [id] => 5354e1f8d7db24b3eb2f790fbfc24991-17587101170764
            [action] => s3_get_enc_info
            [params] => Array
                (
                    [site_id] => 40186158
                    [queue_id] => 5500565
                )

        )

)
</pre><hr /><p><strong>curl_getinfo()</strong></p><pre>Array
(
    [url] => https://mapi.sitelock.com/v3/connect/
    [content_type] => text/html; charset=UTF-8
    [http_code] => 200
    [header_size] => 766
    [request_size] => 506
    [filetime] => -1
    [ssl_verify_result] => 0
    [redirect_count] => 0
    [total_time] => 0.269852
    [namelookup_time] => 1.8E-5
    [connect_time] => 0.005266
    [pretransfer_time] => 0.022625
    [size_upload] => 328
    [size_download] => 861
    [speed_download] => 3200
    [speed_upload] => 1219
    [download_content_length] => -1
    [upload_content_length] => 328
    [starttransfer_time] => 0.269816
    [redirect_time] => 0
    [redirect_url] => 
    [primary_ip] => 45.60.12.54
    [certinfo] => Array
        (
        )

    [primary_port] => 443
    [local_ip] => 213.165.236.14
    [local_port] => 57174
    [http_version] => 2
    [protocol] => 2
    [ssl_verifyresult] => 0
    [scheme] => HTTPS
    [appconnect_time_us] => 22573
    [connect_time_us] => 5266
    [namelookup_time_us] => 18
    [pretransfer_time_us] => 22625
    [redirect_time_us] => 0
    [starttransfer_time_us] => 269816
    [total_time_us] => 269852
)
</pre><hr /><p><strong>Received encryption details</strong></p><pre>Array
(
    [cipher] => aes-256-cbc
    [key] => Yd9***
    [iv] => AFN***
)
</pre><hr /><p><strong>sl_get_bullet_lock_path:</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/310468803f6c1f82608b1ef8134ed75c.php.lock</pre><hr /><p><strong>sl_is_bullet_locked check:</strong></p><pre>not locked (no lock file)</pre><hr /><p><strong>sl_get_bullet_lock_path:</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/310468803f6c1f82608b1ef8134ed75c.php.lock</pre><hr /><p><strong>sl_lock_the_bullet: bytes written</strong></p><pre>10</pre><hr /><p><strong>Starting MySQLi constructor</strong></p><pre></pre><hr /><p><strong>MySQL Version Detected</strong></p><pre>10.6.23-MariaDB</pre><hr /><p><strong>$SL_SCHEMAS received</strong></p><pre><textarea style="width:99%;height:100px;">["martha_demo-site"]</textarea></pre><hr /><p><strong>Attempted to setlocale() with UTF-8</strong></p><pre>Success</pre><hr /><p><strong>ini_get('disable_functions')</strong></p><pre>exec,passthru,shell_exec,system</pre><hr /><p><strong>NO SHELL AVAILABLE</strong></p><pre>Will try using the PHP library</pre><hr /><p><strong>Starting execution using PHP library</strong></p><pre></pre><hr /><pre>Ifsnop\Mysqldump is initialized.</pre><hr /><p><strong>Used PHP library to add martha_demo-site to ./.68c7c7d6f3c25fe08b86edaee5d7669d/database_backup.sql.</strong></p><pre></pre><hr /><p><strong>sl_archive_files</strong></p><pre>start</pre><hr /><p><strong>sl_archive_files 5</strong></p><pre>Send to CLI0</pre><hr /><p><strong>Failed shell_exec for /usr/bin/zip.</strong></p><pre>shell_exec_not_available</pre><hr /><p><strong>Attempting to use ZipArchive.</strong></p><pre>good luck!</pre><hr /><p><strong>archived file</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.68c7c7d6f3c25fe08b86edaee5d7669d/0873313001758710128.zip</pre><hr /><p><strong>Original ZIP size</strong></p><pre>32879986</pre><hr /><p><strong>Encoded chunks written</strong></p><pre>4</pre><hr /><p><strong>sl_wrap_up_the_backup with status ok</strong></p><pre></pre><hr /><p><strong>Returned technical error details, if any</strong></p><pre></pre><hr /><p><strong>mapi_post URL</strong></p><pre>https://mapi.sitelock.com/v3/connect/</pre><hr /><p><strong>mapi_post_request</strong></p><pre>Array
(
    [pluginVersion] => 100.0.0
    [apiTargetVersion] => 3.0.0
    [token] => 66c***
    [requests] => Array
        (
            [id] => fc25316eb4d0865ab22aa48ff28e65ac-17587101507863
            [action] => s3_queue
            [params] => Array
                (
                    [site_id] => 40186158
                    [queue_id] => 5500565
                    [client_id] => 2140
                    [feature_code] => backup_db
                    [status] => ok
                    [url] => .68c7c7d6f3c25fe08b86edaee5d7669d/0873313001758710128.zip
                    [zip_file_info] => .68c7c7d6f3c25fe08b86edaee5d7669d/0873313001758710128.zip-descriptor
                )

        )

)
</pre><hr /><p><strong>curl_getinfo()</strong></p><pre>Array
(
    [url] => https://mapi.sitelock.com/v3/connect/
    [content_type] => text/html; charset=UTF-8
    [http_code] => 200
    [header_size] => 766
    [request_size] => 782
    [filetime] => -1
    [ssl_verify_result] => 0
    [redirect_count] => 0
    [total_time] => 2.202607
    [namelookup_time] => 2.2E-5
    [connect_time] => 0.006663
    [pretransfer_time] => 0.024348
    [size_upload] => 604
    [size_download] => 780
    [speed_download] => 354
    [speed_upload] => 274
    [download_content_length] => -1
    [upload_content_length] => 604
    [starttransfer_time] => 2.202567
    [redirect_time] => 0
    [redirect_url] => 
    [primary_ip] => 45.60.12.54
    [certinfo] => Array
        (
        )

    [primary_port] => 443
    [local_ip] => 213.165.236.14
    [local_port] => 60618
    [http_version] => 2
    [protocol] => 2
    [ssl_verifyresult] => 0
    [scheme] => HTTPS
    [appconnect_time_us] => 24295
    [connect_time_us] => 6663
    [namelookup_time_us] => 22
    [pretransfer_time_us] => 24348
    [redirect_time_us] => 0
    [starttransfer_time_us] => 2202567
    [total_time_us] => 2202607
)
</pre><hr /><p><strong>mapi_response</strong></p><pre><textarea style="width:99%;height:100px;">{"apiVersion":"3.0.1","status":"ok","globalResponse":null,"banner":null,"forceLogout":false,"newToken":null,"now":1758710152,"responses":[{"id":"fc25316eb4d0865ab22aa48ff28e65ac-17587101507863","data":{"s3_status":"ok","queue_id":""},"raw_api_url":"https:\/\/api.sitelock.com\/v1\/66c***\/backup\/queue","raw_response":{"@attributes":{"version":"1.1","encoding":"UTF-8"},"backup_db":{"message":"OK","status":"ok"}},"raw_request":"<xml>\n  <backup_db site_id=\"40186158\" queue_id=\"5500565\" url=\".68c7c7d6f3c25fe08b86edaee5d7669d\/0873313001758710128.zip\" status=\"ok\" zip_file_info=\".68c7c7d6f3c25fe08b86edaee5d7669d\/0873313001758710128.zip-descriptor\" backup_db_id=\"2140\"\/>\n<\/xml>","user_agent":"SiteLock Bullet for Backup","status":"ok"}]}</textarea></pre><hr /><p><strong>sl_get_bullet_lock_path:</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/310468803f6c1f82608b1ef8134ed75c.php.lock</pre><hr /><p><strong>sl_unlock_the_bullet: status</strong></p><pre>success</pre><hr /><p><strong>sl_delete_unique_directory - unlink( $descriptor_file[0] );</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8/everything.sql.zip-descriptor</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8/everything.sql.zip.0</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8/everything.sql.zip.1</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8/everything.sql.zip.10</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8/everything.sql.zip.11</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8/everything.sql.zip.2</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8/everything.sql.zip.3</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8/everything.sql.zip.4</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8/everything.sql.zip.5</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8/everything.sql.zip.6</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8/everything.sql.zip.7</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8/everything.sql.zip.8</pre><hr /><p><strong>sl_delete_unique_directory - file chunk</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8/everything.sql.zip.9</pre><hr /><p><strong>sl_delete_unique_directory - rmdir( $path )</strong></p><pre>/home/ginger101/public_html/dyecraftdirect.com/tmp/.67a588c81ca3acf48a6359493df30cd8</pre><hr /><p><strong>Bullet run time, seconds.</strong></p><pre>36.18</pre><hr />