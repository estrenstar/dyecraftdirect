<?php
// Heading
$_['heading_title'] = '<img src="view/gkd/rocket_boost/img/icon.png" style="vertical-align:middle;padding-right:6px;position:relative;bottom:2px;"/><b style="color:#555">Rocket</b><b style="color:#FFB300">Boost</b>';
$_['module_title'] = '<span>Rocket</span>Boost';

// Text
$_['text_module'] = 'Modules';
$_['text_success'] = 'Success: Your modifications have been saved';
$_['text_store_select'] = 'Store:';
$_['text_clear_cache'] = 'Clear cache';
$_['text_preload_cache'] = 'Preload cache';
$_['text_delete'] = 'Delete';

// Page types
$_['text_common/home'] = '<i class="fa fa-home gkd-badge cyan"></i> Home';
$_['text_product/category'] = '<i class="fa fa-folder-open-o gkd-badge green"></i> Category';
$_['text_product/manufacturer'] = '<i class="fa fa-industry gkd-badge purple"></i> Manufacturer';
$_['text_product/product'] = '<i class="fa fa-tags gkd-badge blue"></i> Product';
$_['text_product/special'] = '<i class="fa fa-usd gkd-badge blue"></i> Special';
$_['text_information/information'] = '<i class="fa fa-file-text-o gkd-badge orange"></i> Information';
$_['text_information/sitemap'] = '<i class="fa fa-sitemap gkd-badge orange"></i> Sitemap';

// Tab main
$_['text_tab_main'] = 'Page cache';
$_['entry_status'] = 'General status';
$_['entry_status_i'] = 'Enable or disable the module';
$_['entry_test'] = 'Test mode';
$_['entry_test_i'] = 'The module will be enabled only when admin is logged in, so you can test if module is working fine before enabling for all customers';
$_['entry_widget'] = 'Front widget';
$_['entry_widget_i'] = 'Display the front widget?';
$_['entry_dyn_route'] = 'Preserve elements';
$_['entry_dyn_route_i'] = 'In some case it could be necessary to set some element to not include in cache, just insert its route here to do so';
$_['entry_cache_pages'] = 'Pages to cache';
$_['entry_cache_pages_i'] = 'Which pages types to save in cache?';
$_['text_add_page'] = 'Add custom page';
$_['text_type'] = 'Page';
$_['text_status'] = 'Status';
$_['text_expire'] = 'Expiry <span style="font-weight:normal">(days)</span>';
$_['text_action'] = 'Action';
$_['info_page_title'] = 'Page cache';
$_['info_page'] = '<h4>Page cache</h4>
<p>This is the core of the module, and the most powerful way to improve speed of a website, when openeing a page there will be a lot number of operations done in order to display the final page, hundreds of connections to database to retrieve all the data and computing all this data, this takes times and is sometimes could be avoided if the content is the same for all, for example a product page that would be exactly the same for one user and another.</p>
<p>This is what the module do, it will save a computed page (or part of a page) and then when another user request same page the saved version (cached version) will be displayed, and this is very fast as it bypass all database connexions and server computing. Depending on your server and how much data you have to compute this can lead to performance increase speed in considerable amount.</p>
<p class="text-center"><img class="pull-right_ img-thumbnail" style="padding:5px;" src="view/gkd/rocket_boost/img/cache.png" alt=""/></p>
<p>In this section you can handle which pages you want to cache on your website, generally you would just let all enabled, it is also possible to cache page that comes from 3rd party modules with the custom page feature (check dedicated help section).</p>
<table class="table">
<thead><tr><th>Element</th><th>Detail</th></tr></thead>
<tbody>
<tr><td><b class="badge">Status</b></td><td>Enable/Disable cache for this page type</td></tr>
<tr><td><b class="badge">Expiry</b></td><td>How long (in days) you want that your page remains in cache, after this delay the page will be automatically re-generated.<br/>Normally the cache is automatically cleared if there is some change in the product, but if for some reason it does not happens (could be because the modification is done in other way than admin edit) then there this setting will auto clear the cache after some delay so all is renewed time to time. It is good practice to have a cache expiry so we are sure to not keep for unlimited time some incorrect data on the website.</td></tr>
<tr><td><b class="badge"><i class="fa fa-trash"></i> Clear</b></td><td>Clear current type cache</td></tr>
<tr><td><b class="badge"><i class="fa fa-refresh"></i> Preload</b></td><td>Re-generate cache for pages of current type</td></tr>
</tbody>
</table>
';
$_['info_custom_page_title'] = 'Set up custom page';
$_['info_custom_page'] = '<h4>Set up custom page</h4>
<p>You can easily set up the module to cache page from 3rd party modules, you just need to do the following:</p>
<ul>
<li>Click on button <b class="badge"><i class="fa fa-plus"></i> Add custom page</b></li>
<li>Set the route path of your page, this is what appears when you open the page in /index.php?route=<b>blog/blog</b></li>
<li>If you have an seo url instead of route= parameter just temporarily disable seo url in System > Settings > Server, you can can find what is the corresponding route</li>
<li>That\'s all, now your custom module can also be processed into cache</li>
</ul>
';
$_['info_widget_front_title'] = 'Front widget';
$_['info_widget_front'] = '<h4>Front widget</h4>
<img class="pull-right img-thumbnail" style="margin-left:25px;padding:5px;" src="view/gkd/rocket_boost/img/front_widget.png" alt=""/>
<p>If you\'re logged in admin and connect to front then the front widget will appear at the bottom of any page on the client side, it can give you some information about how the current page.</p>
<div class="alert alert-info"><i class="fa fa-info-circle"></i> Note that the widget is only accessible on default store, it is not possible to see it on substores</div>
<p><b>Benchmark</b><br/>The benchmark function is useful to know how good the RocketBoost is performing on your website, when you run the benchmark there will be measurment of cached and not cached versions of the current page to see the different load times.<br/><br/>Note that this is comparing the load of the page itself without external ressources, so basically it measure the performance of page cache alone, CDN have no impact here because they are loaded just after this current page load, so it means this part is great already but there is probably even more overall performance gained by the CDN which is not appearing in this benchmark result.</p>
<table class="table">
<thead><tr><th>Element</th><th>Detail</th></tr></thead>
<tbody>
<tr><td><b class="badge"><i class="fa fa-circle" style="color:#fff"></i> Color dot</b></td><td>Color in the top left dot is indicating the cache status of the current page:<br/><i class="fa fa-circle" style="color:#4cb725"></i> The page was in cache, so the displayed version is the cached one<br/><i class="fa fa-circle" style="color:#f98937"></i> The page was not in cache, cache have been generated<br/><i class="fa fa-circle" style="color:#d93131"></i> This page is excluded from cache, but still some page parts can be cached which also improves performance anyway</td></tr>
<tr><td><b class="badge"><i class="fa fa-trash"></i> Clear</b></td><td>Clear current page cache, only for this page, new cache will be automatically generated when accessing again to this page</td></tr>
<tr><td><b class="badge"><i class="fa fa-area-chart"></i> Benchmark</b></td><td>Compare load time between chached version and not cached version</td></tr>
</tbody>
</table>
';
$_['info_widget_admin_title'] = 'Admin widget';
$_['info_widget_admin'] = '<h4>Admin widget</h4>
<img class="pull-right img-thumbnail" style="margin-left:25px;padding:5px;" src="view/gkd/rocket_boost/img/admin_widget.png" alt=""/>
<p>The admin widget appears at the top left of the admin panel, in the header bar, in one click you can quickly see the module status and run some operations without having to go into the module options.</p>
<table class="table">
<thead><tr><th>Button</th><th>Action</th></tr></thead>
<tbody>
<tr><td><b class="badge"><i class="fa fa-power-off"></i> Active/Inactive</b></td><td>Current general module status, click on the button to change the status</td></tr>
<tr><td><b class="badge"><i class="fa fa-trash"></i> Clear</b></td><td>Clear current full cache, new cache will be automatically generated when accessing to some page</td></tr>
<tr><td><b class="badge"><i class="fa fa-refresh"></i> Preload</b></td><td>Re-generate full website cache</td></tr>
</tbody>
</table>
';

// Tab CDN
$_['text_tab_cdn'] = 'Performance';
$_['entry_cdn_status'] = 'CDN status';
$_['entry_cdn_status_i'] = 'Enable or disable CDN component';
$_['entry_css_opt'] = 'CSS optimization';
$_['entry_css_opt_i'] = 'Optimize CSS size and loading';
$_['entry_js_opt'] = 'JS optimization';
$_['entry_js_opt_i'] = 'Optimize javascript size and loading';
$_['entry_html_opt'] = 'HTML optimization';
$_['entry_html_opt_i'] = 'Optimize HTML page size';
$_['entry_opt_off'] = 'Optimization disabled';
$_['entry_opt_off_i'] = 'Set some css/js file for which to not apply boost optimization, one per line';
$_['entry_defer'] = 'Apply defer';
$_['entry_defer_i'] = 'Apply defer parameter on CSS and JS scripts to improve the First Paintful Content';
$_['entry_cdn_mode'] = 'CDN mode';
$_['text_cdn_mode_'] = 'Automatic - Easy mode, the module will try to auto detect your scripts to call corresponding CDN';
$_['text_cdn_mode_1'] = 'Advanced - Manually set each CDN urls, for experienced users';
$_['entry_cust_cdn'] = 'Custom CDN';
$_['text_cdn_source'] = 'Source';
$_['text_cdn_target'] = 'Target';
$_['text_add_cdn'] = 'Add new custom CDN';
$_['info_cdn_title'] = 'Use of CDN';
$_['info_cdn'] = '<h4>What is CDN</h4>
<img class="pull-right img-thumbnail" style="margin-left:25px;padding: 15px;" src="view/gkd/rocket_boost/img/cdn.png" alt=""/>
<p>CDN means Content Delivery Network, it is basically an external server specifically dedicated to serve some ressources very fast and from server located the closest to the user, there is 2 advantages of using CDN, first the loading of the script is faster than if it was loaded from your server, and second your server is using less ressources because it does not have to serve the scripts, so it will be overall faster to handle the other processes.</p>
<h5>Automatic mode</h5>
<p>If you\'re not familiar with CDNs then you can just choose this mode that will try to detect and serve from CDN most commonly used scripts of your website, there is nothing to configure just enable it and it will work directly.<br/>When you enable this mode make sure that your website is still working normally, specifically if there is no design issue, and if functions like add to cart, search bar, etc are still working fine, because there could be some incorrect auto-detect, if it happens just disable this option, you will rather need to use advanced mode.</p>
<h5>Advanced mode</h5>
<p>For experimented users, you can use this mode to fine tune each script to be loaded in case not all scripts are detected in automatic mode.</p>
<p>You can find CDN urls for your scripts here:<ul>
<li><a href="https://www.jsdelivr.com" target="_blank">JSdelivr</a></li>
<li><a href="https://cdnjs.com" target="_blank">Cdnjs</a></li>
<li><a href="https://developers.google.com/speed/libraries" target="_blank">Google CDN</a></li>
<li><a href="https://docs.microsoft.com/en-us/aspnet/ajax/cdn/overview" target="_blank">Microsoft CDN</a></li>
</ul></p>
';
$_['info_opt_title'] = 'CSS/JS Boost';
$_['info_opt'] = '<h4>CSS/JS Boost optimization</h4>
<p>These options are optimizing style and script files in various ways to reduce the size, the number of queries, and also the way to load them in order improve the general loading time.<br>This is allowing your website to be displayed in quicker way and will improve <b>First Paintful Content</b> and <b>Render Blocking Ressource</b>.</p>
<p>It is highly recommended to enable these options.</p>
<h5>Optimization disabled</h5>
<p>In case this function is making the website to not work well you may need to disable it for specific script or css, in this case just set the list of scripts for which to disable delayed loading there.</p>
';

// Tab optimization
$_['text_tab_optimization'] = 'Optimization';
$_['entry_optimization'] = 'Optimizations';
$_['text_opt_type'] = 'Type';
$_['text_opt_desc'] = 'Description';
$_['text_run_optimization_check'] = 'Check if optimization is necessary';
$_['text_run_optimization'] = 'Run optimization';
$_['text_opt_missing_orders'] = '<i class="fa fa-fw fa-shopping-cart"></i> Missing orders';
$_['text_opt_desc_missing_orders'] = 'Missing orders are making the database to grow with some unnecessary data, you can use this function to remove these and make database faster.<br/><br/>Choose corresponding delay to remove the missing orders older than this delay.<br/><br/>If you have a module for abandoned cart make sure to choose long enough delay because abandoned cart will for example check in missing orders from 24 or 24h, so in such case it is recommended to use at least 1 month delay in case the customer come back after some time.<br/><br/>Total missing orders to delete: <b class="missingOrdersCount"></b>';
$_['text_opt_sql_engine'] = '<i class="fa fa-fw fa-database"></i> SQL Engine';
$_['text_opt_desc_sql_engine'] = 'Change to best SQL engine available to improve performance and integrity of your data.<br/><br/><i class="fa fa-info-circle"></i> Although it is normally not generating any issue to run this, better make sure to create a DB backup.';
$_['text_opt_sql_index'] = '<i class="fa fa-fw fa-database"></i> SQL Indexes';
$_['text_opt_desc_sql_index'] = 'Create the SQL indexes on your database, this is kind of dictionary index that will help your database to find your data in quickier way, this is having high impact on performance in case some indexes are missing.<br/><br/><i class="fa fa-info-circle"></i> Although it is normally not generating any issue to run this, better make sure to create a DB backup before.';
$_['info_optimization_title'] = 'Optimizations';
$_['info_optimization'] = '<h4>Optimizations</h4>
<p>In this section you can find various functions to optimize your opencart performance, most of operations done here are permanent so they are required to apply just once (or eventually after you install new module or upgrade your opencart version).</p>
<p>These will boost your overall opencart performance in normal operations (both front and admin) and also for not cached pages.</p>
<p>As these optimizations are permanent take note that the performance gain will not appear in the benchmark of the front widget, at least not in real time, to have an idea of performance difference you can do few benchmarks on front website before applying the optimizations, write somewhere the delays of not cached pages, and then apply the optimizations and run again a serie of benchmark to see the difference when optimizations were not yet applied.</p>
';
$_['text_missing_orders_delay_1 day'] = '1 day';
$_['text_missing_orders_delay_7 days'] = '7 days';
$_['text_missing_orders_delay_1 month'] = '1 month';
$_['text_missing_orders_delay_6 months'] = '6 months';
$_['text_missing_orders_delay_1 year'] = '1 year';

// Tab cron
$_['text_tab_cron'] = 'Cron';

// Common
$_['text_all'] = '- All -';
$_['text_auto_detect'] = '- Auto detect -';
$_['text_tab_about'] = 'About';
$_['text_tab_manual'] = 'Information';

// Buttons
$_['button_save'] = 'Save';
$_['button_save_close'] = 'Save & close';
$_['button_cancel'] = 'Cancel';

// Error
$_['text_demo_mode'] = 'DEMO MODE: Some features may be inactive';
$_['error_permission'] = 'Warning: You do not have permission to modify this module!';