<?php
$extension_name = "Google Marketing Tools";
$extension_name_image = '<a href="https://devmanextensions.com/" target="_blank"><img src="https://devmanextensions.com/opencart_admin/common/img/devman_face.png"> DevmanExtensions.com</a> - '.$extension_name;

$_['extension_version'] = '13.12.1';
// Heading
$_['heading_title']    = $extension_name_image.' (V.'.$_['extension_version'].')';
$_['heading_title_2']  = $extension_name;

$_['text_buttom']      = 'Google Marketing Tools';
$_['text_license_info'] = '<h3>Where I can find Order ID (License ID)?</h3>
<p>After your purchase, you would have to receive all information about your license to email that you used for purchase license, check your <b>SPAM folder</b>.</p>
<br>
<p>Depends where your purchased license, the Order ID will be different:</p>
<ul>
<li>Purchased license in <a href="https://devmanextensions.com/extensions-shop" target="_blank">Devman Store</a>: <b>MLXXXXXX</b></li>
<li>Purchased license in Opencart marketplace: <b>XXXXXX</b> ("XXXXXX" is a numeric value).</li>
<li>Purchased license in Opencartforum: <b>of-XXXXXX</b> ("XXXXXX" is a numeric value).</li>
<li>Purchased license in IsenseLabs: <b>isenselabs-XXXXXX</b> ("XXXXXX" is a numeric value).</li>
</ul>
';
$_['curl_error'] = '<b>CURL ERROR NUMBER: %s</b><br><br>
<p>Your server didn\'t allow connect with our API for validate the license.</p>
<p><b>Put in contact with your hosting support team</b>, they have to solve this external problem.</p>
<p>This extensions is doing a simple CURL call to domain https://devmanextensions.com (213.239.217.148).</p>';