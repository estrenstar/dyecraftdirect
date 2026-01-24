<?php
//==============================================================================
// Google reCAPTCHA v3 v2024-10-09
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

$_['version'] = 'v2024-10-09';

//------------------------------------------------------------------------------
// Extension Settings
//------------------------------------------------------------------------------
$_['heading_title']						= 'Google reCAPTCHA v3';
$_['standard_captcha']					= 'Captchas';

// Extension Settings
$_['heading_extension_settings']		= 'Extension Settings';
$_['help_signup']						= 'You can register for Google reCAPTCHA <a target="_blank" href="https://www.google.com/recaptcha/admin">on this page</a>. Make sure you choose "v3" when creating your captcha.';

$_['entry_status']						= 'Status: <div class="help-text">Set the status for the extension as a whole. If using Journal 3, make sure you read the instructions.txt file and make the edit detailed in that file.</div>';
$_['entry_check_for_updates']			= 'Check For Updates: <div class="help-text">Choose whether to automatically check for updates when the extension admin panel is loaded.</div>';
$_['entry_site_key']					= 'Site Key:';
$_['entry_secret_key']					= 'Secret Key:';
$_['entry_error_message']				= 'Error Message:';
$_['entry_customer_groups']				= 'Customer Groups: <div class="help-text">Choose the customer groups that will require a captcha. Unchecked customer groups will not be checked against reCAPTCHA v3.</div>';
$_['text_guests']						= 'Guests';
$_['entry_ips_to_ignore']				= 'IP Addresses to Ignore: <div class="help-text">Optionally enter a list of IP addresses that will not load the captcha. Enter one IP per line.</div>';
$_['entry_testing_mode']				= 'Testing Mode: <div class="help-text">Enabling this will write the reCAPTCHA results to the browser error console. Only use this for testing, do not leave on for a live store.</div>';

// Score Thresholds
$_['heading_score_thresholds']			= 'Score Thresholds';
$_['help_score_thresholds']				= 'When a protected page is visited, reCAPTCHA v3 returns a score (1.0 is very likely a good interaction, 0.0 is very likely a bot). Anything <b>equal to or above</b> the score you enter in this setting will <b>pass the check</b>. Anything <b>below</b> the score will <b>display an error</b>.<br><br>reCAPTCHA v3 learns by seeing real traffic on your site, so scores in a staging environment or soon after implementing may differ from production. If this is your first time using reCAPTCHA v3, it is recommended to use a value of 0.0 in these settings for a while, and then decide on your actual thresholds by looking at your traffic in the <a target="_blank" href="https://www.google.com/recaptcha/admin">Google admin console</a>. Alternately, you can set them all to 1.0 so it blocks everyone, and enable the v2 Fallback so all customers have to complete a captcha during the learning period.';

$_['entry_account_registration_score']	= 'Account Registration: <div class="help-text">account/register<br>checkout/register<br>checkout/checkout</div>';
$_['entry_contact_page_score']			= 'Contact Page: <div class="help-text">information/contact</div>';
$_['entry_guest_checkout_score']		= 'Guest Checkout: <div class="help-text">checkout/guest/save</div>';
$_['entry_product_reviews_score']		= 'Product Reviews: <div class="help-text">product/product/write</div>';
$_['entry_returns_score']				= 'Returns: <div class="help-text">account/' . (version_compare(VERSION, '4.0', '<') ? 'return' : 'returns') . '/add</div>';
$_['entry_default_score']				= 'All Other Pages: <div class="help-text">any route not listed above</div>';

// reCAPTCHA v2 Fallback
$_['heading_recaptcha_v2_fallback']		= 'reCAPTCHA v2 Fallback';
$_['help_recaptcha_v2_fallback']		= 'If you want to fall back to the v2 version of reCAPTCHA when the customer fails the v3 check, enter your v2 Site and Secret Keys here. If you do not want to fall back and only want to show the error message, leave these fields blank.';
$_['entry_v2_site_key']					= 'Site Key:';
$_['entry_v2_secret_key']				= 'Secret Key:';
$_['entry_v2_heading']					= 'Heading:';

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