<?php
$extension_name = "Google Marketing Tools";
$extension_name_image = '<a href="https://devmanextensions.com/" target="_blank"><img src="https://devmanextensions.com/opencart_admin/common/img/devman_face.png"> DevmanExtensions.com</a> - '.$extension_name;

$_['extension_version'] = '13.12.1';
// Heading
$_['heading_title']    = $extension_name_image.' (V.'.$_['extension_version'].')';
$_['heading_title_2']  = $extension_name;

$_['text_buttom']      = 'Google Marketing Tools';
$_['text_license_info'] = '<h3>Dónde puedo encontrar el ID de compra (License ID)?</h3>
<p>Después de comprar, debería recibir toda la información sobre su licencia, en el mismo correo electrónico que utilizó para comprar la licencia, revisar su carpeta de <b>SPAM</b>.</p>
<br>
<p>Dependiendo de dónde compró la licencia, el ID del pedido será diferente:</p>
<ul>
<li>Licencia comprada en <a href="https://devmanextensions.com/extensions-shop" target="_blank">Devman Store</a>: <b>MLXXXXXX</b></li>
<li>Licencia comprada en Opencart marketplace: <b>XXXXXX</b> (es un valor numérico "XXXXXX").</li>
<li>Licencia comprada en Opencartforum: <b>of-XXXXXX</b> (es un valor numérico "XXXXXX").</li>
<li>Licencia comprada en IsenseLabs: <b>isenselabs-XXXXXX</b> (es un valor numérico "XXXXXX").</li>
</ul>
';
$_['curl_error'] = '<b>NÚMERO DE ERROR DE CURL: %s</b><br><br>
<p>Su servidor no permitió conectarse con nuestra API para validar la licencia.</p>
<p><b>Póngase en contacto con su equipo de soporte de hosting</b>, tienen que solucionar este problema externo.</p>
<p>Esta extensión está haciendo un simple CURL llamando al dominio https://devmanextensions.com (213.239.217.148).</p>';