<?php
//==============================================================================
// UPS Pro v2025-6-25
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

$_['version'] = 'v2025-6-25';

//------------------------------------------------------------------------------
// Heading
//------------------------------------------------------------------------------
$_['heading_title']						= 'UPS Pro';
$_['button_create_ups_shipping_label']	= 'Create UPS Shipping Label';

//------------------------------------------------------------------------------
// Extension Settings
//------------------------------------------------------------------------------
$_['tab_extension_settings']			= 'Extension Settings';
$_['heading_extension_settings']		= 'Extension Settings';

$_['entry_status']						= 'Status: <div class="help-text">Set the status for the extension as a whole.</div>';
$_['entry_check_for_updates']			= 'Check For Updates: <div class="help-text">Choose whether to automatically check for updates when the extension admin panel is loaded.</div>';
$_['entry_heading']						= 'Heading: <div class="help-text">The heading under which these shipping options will appear. HTML is supported.</div>';
$_['entry_sort_order']					= 'Sort Order: <div class="help-text">Set the Sort Order for the extension, relative to other shipping methods.</div>';
$_['entry_tax_class_id']				= 'Tax Class: <div class="help-text">If you want to tax your shipping costs, set a tax class.</div>';

$_['entry_rate_sorting']				= 'Rate Sorting: <div class="help-text">Choose how to sort rates returned by UPS.</div>';
$_['text_sort_by_name']					= 'Sort by Name';
$_['text_sort_by_price_ascending']		= 'Sort by Price Ascending';
$_['text_sort_by_price_descending']		= 'Sort by Price Descending';

$_['entry_boxes_text']					= 'Number of Boxes Text: <div class="help-text">If you want the number of boxes to show in the shipping method heading, enter the text here. Use [boxes] in place of the number of calculated boxes. Leave this field blank to hide the text.</div>';
$_['entry_weight_text']					= 'Weight Text: <div class="help-text">If you want the weight of each box to show in the shipping method heading, enter the text here. Use [weight] in place of the calculated weight. The weight will be converted to your default weight class, and will automatically show the correct weight class. Leave this field blank to hide the box weights.</div>';
$_['entry_signature_required_text']		= 'Signature Required Text: <div class="help-text">If requiring a signature, enter the text appended to the rate title. Leave this field blank to hide the "signature required" text.</div>';

// Estimated Delivery Settings
$_['heading_estimated_delivery']		= 'Estimated Delivery Settings';

$_['entry_shipping_days']				= 'Shipping Days: <div class="help-text">Choose the days on which your store ships packages.</div>';
$_['text_sunday']						= 'Sunday';
$_['text_monday']						= 'Monday';
$_['text_tuesday']						= 'Tuesday';
$_['text_wednesday']					= 'Wednesday';
$_['text_thursday']						= 'Thursday';
$_['text_friday']						= 'Friday';
$_['text_saturday']						= 'Saturday';

$_['entry_estimated_delivery_text']		= 'Estimated Delivery Text: <div class="help-text">For rates that offer an estimated delivery, enter the text appended to the rate title. Use [date] in place of the estimated date. Leave this field blank to hide estimated delivery dates.</div>';
$_['entry_estimated_delivery_format']	= 'Estimated Delivery Date Format: <div class="help-text">Set the format used for the date in the Estimated Delivery Text. The formatting details can be found <a target="_blank" href="https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters">on this page</a>.</div>';
$_['entry_estimated_delivery_padding']	= 'Estimated Delivery Padding: <div class="help-text">Enter the number of business days to pad the delivery date.</div>';
$_['entry_cutoff_time']					= 'Cutoff Time: <div class="help-text">Enter the cutoff time (in HH:MM format) for delivery estimates. Orders placed after this time will start calculating based on the following eligible day.</div>';
$_['entry_holiday_dates']				= 'Holiday Dates: <div class="help-text">Optionally enter a list of holiday dates that will be skipped when calculating delivery date estimates. If you want the same holiday dates every year, you can use <code>20XX</code> for the year. Enter one date per line using the format <code>YYYY-MM-DD</code>. Enter date ranges in the format<br><code>YYYY-MM-DD::YYYY-MM-DD</code></div>';

//------------------------------------------------------------------------------
// Restrictions
//------------------------------------------------------------------------------
$_['tab_restrictions']					= 'Restrictions';
$_['heading_restrictions']				= 'Restrictions';

$_['entry_stores']						= 'Store(s): <div class="help-text">Select the stores that can use this shipping method.</div>';

$_['entry_geo_zones']					= 'Geo Zone(s): <div class="help-text">Select the geo zones that can use this shipping method. The "Everywhere Else" checkbox applies to any locations not within a geo zone.</div>';
$_['text_everywhere_else']				= '<em>Everywhere Else</em>';

$_['entry_customer_groups']				= 'Customer Group(s): <div class="help-text">Select the customer groups that can use this shipping method. The "Guests" checkbox applies to all customers not logged in to an account.</div>';
$_['text_guests']						= '<em>Guests</em>';

//------------------------------------------------------------------------------
// UPS Settings
//------------------------------------------------------------------------------
$_['tab_ups_settings']					= 'UPS Settings';
$_['heading_ups_settings']				= 'UPS Settings';

$_['entry_mode']						= 'Mode: <div class="help-text">Rates and shipping labels can be tested in "Test" mode. Once your store is live you should use "Live" mode.</div>';
$_['text_test']							= 'Test';
$_['text_live']							= 'Live';

$_['help_ups_settings']					= '
	<div class="text-info text-center">
		Enter your Client ID and Client Secret below from your <a target="_blank" href="https://developer.ups.com/apps">UPS App</a>.
		For directions on how to create a UPS App, <a style="color: #428bca" onclick="$(\'#help-creating-app\').slideToggle()">follow these steps</a>.
	</div>
	<div id="help-creating-app" class="alert alert-info" style="display: none; margin-top: 15px;">
		<ol style="line-height: 2; margin: 0; padding: 5px 0 0 20px;">
			<li>Navigate to <a target="_blank" href="https://developer.ups.com/apps">https://developer.ups.com/apps</a></li>
			<li>Log in using your UPS credentials.</li>
			<li>Click the "Add App" link.</li>
			<li>In the pop-up that appears, choose "I want to integrate UPS shipping capabilities to my business."</li>
			<li>Follow the prompts to fill in your contact info.</li>
			<li>When you get to the "Add App" page, fill in the "App name" with whatever you want (e.g. OpenCart).</li>
			<li>Click the + button for the following APIs:
				<ul>
					<li>Authorization (O Auth)</li>
					<li>Address Validation</li>
					<li>Rating</li>
					<li>Shipping</li>
				</ul>
			</li>
			<li>Leave the "Callback URL" field blank.</li>
			<li>Click "Save".</li>
			<li>You should now be shown a page with your Client ID and Client Secret.</li>
		</ol>
	</div>
';

$_['entry_client_id']					= 'Client ID:';
$_['entry_client_secret']				= 'Client Secret:';
$_['entry_account_number']				= 'Account Number: <div class="help-text">If you want to use UPS Negotiated Rates or print shipping labels, enter your account number in this field.</div>';

$_['entry_store_address']				= 'Store Address: <div class="help-text">Enter your store\'s physical address that is used for the "Ship From" address.</div>';
$_['placeholder_street_address']		= 'Street Address';
$_['placeholder_city']					= 'City';
$_['placeholder_state']					= 'State';
$_['placeholder_postcode']				= 'Postcode';
$_['placeholder_country']				= 'Country';

// Rate Settings
$_['heading_rate_settings']				= 'Rate Settings';

$_['entry_insurance']					= 'Add Insurance: <div class="help-text">Choose whether to include the insurance cost in the rate calculation.</div>';

$_['entry_customer_classification']		= 'Customer Classification: <div class="help-text">If you use negotiated rates you may need to set a particular value for this setting. Talk to your UPS Representative if you do not know what value to use.</div>';
$_['text_rates_associated']				= '00 - Rates Associated with Shipper Number';
$_['text_daily_rates']					= '01 - Daily Rates';
$_['text_retail_rates']					= '04 - Retail Rates';
$_['text_regional_rates']				= '05 - Regional Rates';
$_['text_general_list_rates']			= '06 - General List Rates';
$_['text_standard_list_rates']			= '53 - Standard List Rates';

$_['entry_pickup_type']					= 'Pickup Type: <div class="help-text">Choose the pickup type for the rate request. If you use negotiated rates you may need to use "Customer Counter" or "None" to get the correct rates from your account.</div>';
$_['text_no_pickup_type']				= 'None';
$_['text_daily_pickup']					= 'Daily Pickup';
$_['text_customer_counter']				= 'Customer Counter';

$_['entry_signature_type']				= 'Signature Type:';
$_['text_no_signature_required']		= 'No Signature Required';
$_['text_signature_required']			= 'Signature Required';
$_['text_adult_signature_required']		= 'Adult Signature Required';
$_['text_signature_type_help']			= '
	<table class="help-text table-bordered">
		<tr><td>No Signature Required</td><td>Do not require a signature upon delivery</td></tr>
		<tr><td>Signature Required</td><td>Someone at the delivery address must sign upon delivery</td></tr>
		<tr><td>Adult Signature Required</td><td>Someone 21 or older at the delivery address must sign upon delivery</td></tr>
	</table>
';

$_['entry_signature_choice']			= 'Signature Choice: <div class="help-text">If a signature is required for delivery, choose whether the customer is allowed to choose between a "signature not required" option and a "signature required" option.</div>';
$_['entry_negotiated_rates']			= 'Use Negotiated Rates: <div class="help-text">Choose whether to retrieve UPS Negotiated Rates for your account.</div>';

// Package Settings
$_['heading_package_settings']			= 'Package Settings';

$_['entry_weight_units']				= 'Weight Units: <div class="help-text">Choose whether to send the weight to UPS in pounds or kilograms.</div>';
$_['text_pounds']						= 'Pounds';
$_['text_kilograms']					= 'Kilograms';

$_['entry_dimension_units']				= 'Dimension Units: <div class="help-text">Choose whether to send the dimensions to UPS in inches or centimeters.</div>';
$_['text_inches']						= 'Inches';
$_['text_centimeters']					= 'Centimeters';

$_['entry_avoid_long_boxes']			= 'Avoid Long Boxes: <div class="help-text">Usually "Yes" is preferable for this setting, but if you notice irregular package sizes, try using "No".</div>';
$_['entry_avoid_oversize_fees']			= 'Avoid Oversize Fees: <div class="help-text">Usually "Yes" is preferable for this setting, but if you notice large or long items being divided into multiple packages, try using "No".</div>';
$_['entry_weight_limit']				= 'Box Weight Limit: <div class="help-text">Set the weight limit used for box calculations. The default value is 150, which is the standard UPS limit for pounds. If you use kilograms you want to set this to 68.</div>';
$_['entry_divide_heavy_products']		= 'Divide Heavy Products: <div class="help-text">Choose whether to divide up products that weigh more than the Box Weight Limit. For example, if a product weighs 100 lbs and your limit is 70 lbs, it would be divided into 2 items weighing 50 lbs for the purpose of packaging. If you do not enable this and a product weighs more than the Box Weight Limit, the extension will be disabled.</div>';


$_['entry_packaging_method']			= 'Packaging Method:';
$_['text_individual_per_item']			= 'Individual Per Item';
$_['text_use_packing_algorithm']		= 'Use Packing Algorithm';
$_['text_packaging_method_help']		= '
	<table class="help-text table-bordered">
		<tr><td style="white-space: nowrap">Individual Per Item</td><td>Each item in the cart will be requested as its own package, with multiple quantities of the same product requested as individual packages.</td></tr>
		<tr><td style="white-space: nowrap">Use Packing Algorithm</td><td>When the cart weight is over the Weight Limit Per Box, the extension will request rates for multiple packages. It will fill the first box as close to the weight limit as possible, then proceed on to the next box(es). All boxes will be packed using a 3-dimensional packing algorithm, with the dimensions of the box determined by this algorithm, unless using Set Box Dimensions.</td></tr>
	</table>
';

//------------------------------------------------------------------------------
// UPS Services
//------------------------------------------------------------------------------
$_['tab_ups_services']					= 'UPS Services';
$_['heading_ups_services']				= 'UPS Services';

$_['help_ups_services']					= '<b>Service Name:</b> Enter the name for the service displayed to the customer on the front-end. Leave the name blank to hide that service.<br><br><b>Total Limit:</b> Enter a single value like <code>100.00</code> or a range like <code>50-99.99</code>. If the cart total is more than this amount, or is outside the range, this rate will be disabled.<br><br><b>Weight Limit:</b> Enter a single value like <code>10</code> or a range like <code>0-20</code>. If the package weighs more than this amount, or is outside the range, this rate will be disabled.<br><br><b>Rate Adjustment:</b> Enter a flat or percentage rate adjustment. For example, to increase the cost by 10%, enter <code>10%</code>. To decrease the cost by 5.00, enter <code>-5.00</code>';

$_['entry_service_name']				= 'Service Name:';
$_['entry_service_geo_zone']			= 'Geo Zone:';
$_['text_default_geo_zones']			= '--- Default Geo Zones ---';
$_['entry_service_total_limit']			= 'Total Limit:';
$_['entry_service_weight_limit']		= 'Weight Limit:';
$_['entry_rate_adjustment']				= 'Rate Adjustment:';

$_['text_service_03']					= 'UPS Ground';
$_['text_service_12']					= 'UPS 3 Day Select';
$_['text_service_02']					= 'UPS 2nd Day Air';
$_['text_service_59']					= 'UPS 2nd Day Air A.M.';
$_['text_service_01']					= 'UPS Next Day Air';
$_['text_service_13']					= 'UPS Next Day Air Saver';
$_['text_service_14']					= 'UPS Next Day Air Early A.M.';
$_['text_service_92']					= 'UPS SurePost (Under 1 lb)';
$_['text_service_93']					= 'UPS SurePost (1+ lb)';
$_['text_service_11']					= 'UPS Standard';
$_['text_service_65']					= 'UPS Worldwide Saver';
$_['text_service_07']					= 'UPS Worldwide Express';
$_['text_service_08']					= 'UPS Worldwide Expedited';
$_['text_service_54']					= 'UPS Worldwide Express Plus';

//------------------------------------------------------------------------------
// Adjustments
//------------------------------------------------------------------------------
$_['tab_adjustments']					= 'Adjustments';

$_['heading_domestic_adjustments']		= 'Domestic Adjustments';
$_['help_domestic']						= 'These adjustments are used for your store\'s default country, which is set to';

$_['entry_domestic_set_box_dimensions']	= 'Set Box Dimensions: <div class="help-text">Optionally enter the dimensions of set box sizes you use, in order from smallest to largest. Leave this setting blank to have the algorithm determine the necessary box size, based on the items in the cart.<br><br>When entering dimensions, use the following format:<br><br><code>L x W x H, Weight Limit</code><br><br>The Weight Limit is optional. For example:<br><br><code>8.5 x 5 x 1.75, 5</code><br><code>11 x 9 x 7, 10</code><br><code>12 x 12 x 6</code><br><code>15 x 10 x 8</code></div>';
$_['entry_domestic_item_dimension']		= 'Item Dimension Adjustment: <div class="help-text">Enter a flat value (such as 0.5) by which to adjust each item dimension. You can use this to simulate packaging materials. For example, if you enter 0.5 then the length, width, and height will <b>ALL</b> be increased by 0.5 for every item in the cart.</div>';
$_['entry_domestic_item_weight']		= 'Item Weight Adjustment: <div class="help-text">Enter a flat value (such as 1.00) or a percentage (such as 10%) by which to adjust each item weight. You can use this to simulate packaging materials.</div>';
$_['entry_domestic_box_weight']			= 'Box Weight Adjustment: <div class="help-text">Enter a flat value (such as 2.50) or a percentage (such as 10%) by which to adjust each box weight. This will apply after the Item Weight Adjustment is calculated. You can use this to add the weight of the box to the calculation.</div>';
$_['entry_domestic_rate_adjustment']	= 'Rate Adjustment for Domestic Services: <div class="help-text">Enter a flat or percentage rate adjustment that applies to all domestic services, which will be added <b>before</b> any individual rate adjustments. For example, to increase the cost by 10%, enter <code>10%</code>. To decrease the cost by 5.00, enter <code>-5.00</code>.</div>';

$_['heading_international_adjustments']	= 'International Adjustments';
$_['help_international']				= 'These adjustments are used for all other countries.';

$_['entry_international_set_box_dimensions']	= 'Set Box Dimensions: <div class="help-text">Optionally enter the dimensions of set box sizes you use, in order from smallest to largest. Leave this setting blank to have the algorithm determine the necessary box size, based on the items in the cart.<br><br>When entering dimensions, use the following format:<br><br><code>L x W x H, Weight Limit</code><br><br>The Weight Limit is optional. For example:<br><br><code>8.5 x 5 x 1.75, 5</code><br><code>11 x 9 x 7, 10</code><br><code>12 x 12 x 6</code><br><code>15 x 10 x 8</code></div>';
$_['entry_international_item_dimension']	= 'Item Dimension Adjustment: <div class="help-text">Enter a flat value (such as 0.5) by which to adjust each item dimension. You can use this to simulate packaging materials. For example, if you enter 0.5 then the length, width, and height will <b>ALL</b> be increased by 0.5 for every item in the cart.</div>';
$_['entry_international_item_weight']	= 'Item Weight Adjustment: <div class="help-text">Enter a flat value (such as 1.00) or a percentage (such as 10%) by which to adjust each item weight. You can use this to simulate packaging materials.</div>';
$_['entry_international_box_weight']	= 'Box Weight Adjustment: <div class="help-text">Enter a flat value (such as 2.50) by which to adjust each box weight. This will apply after the Item Weight Adjustment is calculated.</div>';
$_['entry_international_rate_adjustment']	= 'Rate Adjustment for International Services: <div class="help-text">Enter a flat or percentage rate adjustment that applies to all international services, which will be added <b>before</b> any individual rate adjustments. For example, to increase the cost by 10%, enter <code>10%</code>. To decrease the cost by 5.00, enter <code>-5.00</code>.</div>';

//------------------------------------------------------------------------------
// Shipping Labels
//------------------------------------------------------------------------------
$_['tab_shipping_labels']				= 'Shipping Labels';
$_['help_for_shipping_labels']			= 'Shipping labels will always display the retail price on them, even if your UPS account is actually charged a discounted price.<br>Labels will include a delivery confirmation signature if: (1) UPS Pro is set to always require a signature, or<br>(2) UPS Pro is set to give the customer a choice and the shipping rate title on the order has the "Signature Required Text" in it.';
$_['heading_shipping_labels']			= 'Shipping Labels';

$_['entry_shipping_labels']				= 'Show "Create UPS Shipping Label" Button: <div class="help-text">Choose where the "Create UPS Shipping Label" button is shown.</div>';
$_['text_yes_order_info_page']			= 'Yes, on the order info page';
$_['text_yes_both_pages']				= 'Yes, on both the order info and order list pages';

$_['entry_default_service']				= 'Service to Use For Non-UPS Pro Orders: <div class="help-text">Choose the service that will be used for labels generated for orders that do not have a UPS Pro shipping rate on them.</div>';
$_['text_do_not_allow']					= '--- Do Not Allow ---';

$_['entry_shipfrom_name']				= '"Ship From" Name: <div class="help-text">Enter the "Ship From" name displayed on the shipping label and international invoice document. Maximum length is 35 characters,  but only 30 characters will be printed on the label.</div>';
$_['entry_attention_name']				= 'Attention Name: <div class="help-text">Enter the "Ship From" attention name displayed on the shipping label and international invoice document. Maximum length is 35 characters,  but only 30 characters will be printed on the label. Required if destination is international. </div>';
$_['entry_phone_number']				= 'Phone Number: <div class="help-text">Enter the "Ship From" phone number displayed on the shipping label and international invoice document. Maximum length is 15 numbers including the country code. Leave out the beginning "+" for country codes.</div>';
$_['entry_shipping_order_status']		= 'Set Order Status To: <div class="help-text">Choose the order status that the extension will set for the order when generating a shipping label.</div>';
$_['text_no_change']					= '--- No Change ---';

// International Invoice Settings
$_['heading_international_invoice_settings']	= 'International Invoice Settings';
$_['entry_invoice_tax_id']						= 'Tax ID/EIN/VAT No: <div class="help-text">Enter your business Tax ID/EIN/VAT No. to include on the invoice. Maximum length is 15 characters.</div>';
$_['entry_invoice_commodity_code']				= 'Default Commodity Code: <div class="help-text">Enter the default Commodity Code / Harmonized System (HS) Code to send for products if the product-specific Commodity Code field is blank.</div>';
$_['entry_invoice_commodity_code_field']		= 'Commodity Code Field: <div class="help-text">Select the column from the "product" database table that holds the product\'s commodity code. Choose "--- None ---" if you do not have commodity codes entered for products.</div>';
$_['entry_invoice_country_code_field']			= 'Origin Country Code Field: <div class="help-text">Select the column from the "product" database table that holds the product\'s two-character origin country code. Choose "--- None ---" if you do not have country codes entered for products.</div>';
$_['entry_invoice_comments']					= 'Comments: <div class="help-text">Optionally enter any additional information to include on the invoice in this field, such as EORI number. Maximum length is 150 characters.</div>';
$_['entry_invoice_declaration_statement']		= 'Declaration Statement: <div class="help-text">Optionally enter a declaration statement included on the invoice. Maximum length is 550 characters.</div>';

// E-mail Settings
$_['heading_email_settings']			= 'E-mail Settings';
$_['entry_copy_emails']					= 'Send a Copy To: <div class="help-text">Optionally enter a list of e-mail addresses that will receive a copy of the shipping labels when they are generated. Separate multiple addresses by commas.</div>';
$_['entry_customer_email']				= 'Automatically E-mail Customer: <div class="help-text">Select whether to automatically e-mail the customer their tracking number when a shipping label is created. If you choose not to e-mail the customer automatically, you can do it manually later on from the order history area of the order.</div>';
$_['entry_customer_subject']			= 'Customer E-mail Subject: <div class="help-text">Enter the subject for the e-mail sent to the customer. You can add any order information by entering the column name from the "order" database table in [ and ] brackets, like <code>[store_name]</code> or <code>[order_id]</code>.</div>';
$_['entry_customer_message']			= 'Customer E-mail Message: <div class="help-text">Enter the body for the e-mail sent to the customer. Use <code>[tracking_numbers]</code> in place of the UPS tracking numbers, and <code>[tracking_url]</code> in place of the tracking page URL. You can add any other order information by entering the column name from the "order" database table in [ and ] brackets, like <code>[order_id]</code> or <code>[shipping_firstname]</code>. You can use <code>[products]</code> to output the list of products from the order.</div>';

//------------------------------------------------------------------------------
// Testing Mode
//------------------------------------------------------------------------------
$_['tab_testing_mode']					= 'Testing Mode';
$_['testing_mode_help']					= 'Enable testing mode if things are not working as expected on the front end. Messages logged during testing can be viewed below.';
$_['heading_testing_mode']				= 'Testing Mode';

$_['entry_testing_mode']				= 'Testing Mode:';
$_['entry_testing_messages']			= 'Messages:';
$_['button_refresh_log']				= 'Refresh Log';
$_['button_download_log']				= 'Download Log';
$_['button_clear_log']					= 'Clear Log';

//------------------------------------------------------------------------------
// Standard Text
//------------------------------------------------------------------------------
$_['contact_url']						= 'https://www.getclearthinking.com/contact?storeurl=' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . '&version=' . VERSION;
$_['copyright']							= '<hr><div class="text-center" style="margin: 15px">' . $_['heading_title'] . ' (' . $_['version'] . ') &copy; <a target="_blank" href="' . $_['contact_url'] . '">Clear Thinking, LLC</a></div>';

$_['standard_autosaving_enabled']		= 'Auto-Saving Enabled';
$_['standard_confirm']					= 'This operation cannot be undone. Continue?';
$_['standard_error']					= '<strong>Error:</strong> You do not have permission to modify ' . $_['heading_title'] . '!';
$_['standard_max_input_vars']			= '<strong>Warning:</strong> The number of settings is close to your <code>max_input_vars</code> server value. You should enable auto-saving to avoid losing any data.';
$_['standard_please_wait']				= 'Please wait...';
$_['standard_saved']					= 'Saved!';
$_['standard_saving']					= 'Saving...';
$_['standard_select']					= '--- Select ---';
$_['standard_success']					= 'Success!';
$_['standard_testing_mode']				= "Your log is too large to open! If you need to archive it, you can download it using the button above.\n\nTo start a new log, (1) click the Clear Log button, (2) reload the admin panel page, then (3) run your test again.";

$_['standard_check_for_updates']		= 'Check For Updates';
$_['standard_contact_clear_thinking']	= 'Contact Clear Thinking';
$_['standard_error_checking']			= 'There was an error checking for the latest version.';
$_['standard_using_latest']				= 'You are using the latest version';
$_['standard_new_version']				= 'A new version is available!';
$_['standard_your_version']				= 'Your Version:';
$_['standard_latest_version']			= 'Latest Version:';
$_['standard_release_notes']			= 'View release notes';
$_['standard_continue']					= 'Continue';
$_['standard_update_warning']			= '<ul><li>Before updating, it is highly recommended to <b>back up your website files</b>.</li><br><li>To update, enter your license key below and click "Update". A license comes with 1 year of free updates, so you may not qualify for the latest version if you are beyond that period. If that is the case, you will be notified after attempting to update.</li><br><li>Updating the extension will <b>overwrite all current extension files</b>. If you have made modifications to any files, make sure you back up your edits before updating.</li><br><li>If any issues occur during or after updating, download and reinstall the extension manually.</li><br><li>If you have lost your license key or download link, you can retrieve them <a target="_blank" href="https://www.getclearthinking.com/downloads/license">on this page</a>.</li></ul><br>';
$_['standard_update']					= 'Update';
$_['standard_updating']					= 'Updating...';
$_['standard_license_key']				= 'License Key:';

$_['standard_module']					= 'Modules';
$_['standard_shipping']					= 'Shipping';
$_['standard_payment']					= 'Payments';
$_['standard_total']					= 'Order Totals';
$_['standard_feed']						= 'Feeds';
?>