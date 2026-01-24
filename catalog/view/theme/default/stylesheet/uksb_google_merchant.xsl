<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
                xmlns:html="http://www.w3.org/TR/REC-html40"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:g="http://base.google.com/ns/1.0">
	<xsl:output method="html" omit-xml-declaration="yes" version="1.0" encoding="UTF-8" indent="yes"/>
 	<xsl:strip-space elements="*"/>
	<xsl:template match="/rss/channel">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<title><xsl:value-of select="title"/></title>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<style type="text/css">
					body {
						font-family: Verdana, Arial, Helvetica, sans-serif;
						font-size: 12px;
					}

					#header, #footer {
						background-color: #F0F7FC;
						border: 1px #50A6E0 solid;
						border-right: none; border-left: none;
						padding: 5px 10px;
						margin: 10px 0px;
						line-height: 1.7;
					}

					#header a, #footer a {
						color: #2266bb;
					}

					#footer {
						margin-top: 15px;
					}

					table {
						width: 100%;
					}

					th {
						text-align: left;
						border-bottom: 1px solid #aaaaaa;
						padding-bottom: 10px; padding-left: 5px;
					}

					tr.odd {
						background-color: #f7f7f7;
					}

					td {
						padding: 5px;
						margin: 0px;
					}
				</style>
			</head>
			<body>
				<xsl:variable name="siteURL">
										<xsl:value-of select="link" disable-output-escaping="yes"/>
									</xsl:variable>
									<h1><a href="{$siteURL}" target="_blank">
										<xsl:value-of select="title"/>
									</a></h1>
				<div id="header">
					<p>
						<xsl:value-of select="description"/>
					</p>
					<p>
						This feed was generated using <a href="https://www.opencart-extensions.co.uk/?source=ext" title="OpenCart Extensions" target="_blank">UKSB Google Merchant Feeds</a>, an OpenCart extension.
					</p>
				</div>
				<div id="content">
					<table cellpadding="0" cellspacing="0">
						<xsl:for-each select="item">
						<tr>
							<xsl:if test="position() mod 2 != 0">
								<xsl:attribute name="class">odd</xsl:attribute>
							</xsl:if>
							<td>
								<br />
								<table cellpadding="0" cellspacing="0">
									<tr>
										<th style="width: 60%;"><b>Item Title: </b> <xsl:variable name="itemURL">
												<xsl:value-of select="link" disable-output-escaping="yes"/>
											</xsl:variable>
											<a href="{$itemURL}" target="_blank">
												<xsl:value-of select="title"/>
											</a>
										</th>
									</tr>
									<xsl:if test="description">
										<tr>
											<td>
												<b>Item Description: </b> <xsl:value-of select="description"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:item_group_id">
										<tr>
											<td>
												<b>Item Group ID: </b> <xsl:value-of select="g:item_group_id"/>
											</td>
										</tr>
									</xsl:if>
									<tr>
										<td>
											<b>Item ID: </b> <xsl:value-of select="g:id"/>
										</td>
									</tr>
									<xsl:if test="g:promotion_id">
										<tr>
											<td>
												<b>Promotion ID: </b> <xsl:value-of select="g:promotion_id"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:expiry_date">
										<tr>
											<td>
												<b>Expiry Date: </b> <xsl:value-of select="g:expiry_date"/>
											</td>
										</tr>
									</xsl:if>
									<tr>
										<td>
											<b>Condition: </b> <xsl:value-of select="g:condition"/>
										</td>
									</tr>
									<tr>
										<td>
											<b>Availability: </b> <xsl:value-of select="g:availability"/>
										</td>
									</tr>
									<tr>
										<td>
											<b>Price: </b> <xsl:value-of select="g:price"/>
										</td>
									</tr>
									<xsl:if test="g:sale_price">
										<tr>
											<td>
												<b>Sale Price: </b> <xsl:value-of select="g:sale_price"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:sale_price_effective_date">
										<tr>
											<td>
												<b>Sale Date From/To: </b> <xsl:value-of select="g:sale_price_effective_date"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:unit_pricing_measure">
										<tr>
											<td>
												<b>Unit Pricing Measure: </b> <xsl:value-of select="g:unit_pricing_measure"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:unit_pricing_base_measure">
										<tr>
											<td>
												<b>Unit Pricing Base Measure: </b> <xsl:value-of select="g:unit_pricing_base_measure"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:google_product_category">
										<tr>
											<td>
												<b>Google Product Category: </b> <xsl:value-of select="g:google_product_category"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:product_type">
										<xsl:for-each select="g:product_type">
											<tr>
												<td>
													<b>Product Type: </b> <xsl:value-of select="."/>
												</td>
											</tr>
										</xsl:for-each>
									</xsl:if>
									<tr>
										<td>
											<b>Has Product Identifiers: </b> <xsl:value-of select="g:identifier_exists"/>
										</td>
									</tr>
									<xsl:if test="g:mpn">
										<tr>
											<td>
												<b>MPN: </b> <xsl:value-of select="g:mpn"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:brand">
										<tr>
											<td>
												<b>Brand: </b> <xsl:value-of select="g:brand"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:gtin">
										<tr>
											<td>
												<b>GTIN: </b> <xsl:value-of select="g:gtin"/>
											</td>
										</tr>
									</xsl:if>
									<tr>
										<td>
											<b>Main Image: </b> <xsl:variable name="imageURL">
												<xsl:value-of select="g:image_link" disable-output-escaping="yes"/>
											</xsl:variable>
											<a href="{$imageURL}" target="_blank">
												<xsl:value-of select="g:image_link"/>
											</a>
										</td>
									</tr>
									<xsl:if test="g:additional_image_link">
										<xsl:for-each select="g:additional_image_link">
											<tr>
												<td>
													<b>Additional Image: </b> <xsl:variable name="addimageURL">
														<xsl:value-of select="." disable-output-escaping="yes"/>
													</xsl:variable>
													<a href="{$addimageURL}" target="_blank">
														<xsl:value-of select="."/>
													</a>
												</td>
											</tr>
										</xsl:for-each>
									</xsl:if>
									<xsl:if test="g:multipack">
										<tr>
											<td>
												<b>Multipack: </b> <xsl:value-of select="g:multipack"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:is_bundle">
										<tr>
											<td>
												<b>Is Bundle: </b> <xsl:value-of select="g:is_bundle"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:shipping_weight">
										<tr>
											<td>
												<b>Shipping Weight: </b> <xsl:value-of select="g:shipping_weight"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:delivery_label">
										<tr>
											<td>
												<b>Shipping / Delivery Label: </b> <xsl:value-of select="g:delivery_label"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:size_type">
										<tr>
											<td>
												<b>Size Type: </b> <xsl:value-of select="g:size_type"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:size_system">
										<tr>
											<td>
												<b>Size System: </b> <xsl:value-of select="g:size_system"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:gender">
										<tr>
											<td>
												<b>Gender: </b> <xsl:value-of select="g:gender"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:age_group">
										<tr>
											<td>
												<b>Age Group: </b> <xsl:value-of select="g:age_group"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:energy_efficiency_class">
										<tr>
											<td>
												<b>Energy Efficiency Class: </b> <xsl:value-of select="g:energy_efficiency_class"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:size">
										<tr>
											<td>
												<b>Size: </b> <xsl:value-of select="g:size"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:color">
										<tr>
											<td>
												<b>Color: </b> <xsl:value-of select="g:color"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:colour">
										<tr>
											<td>
												<b>Colour: </b> <xsl:value-of select="g:colour"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:material">
										<tr>
											<td>
												<b>Material: </b> <xsl:value-of select="g:material"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:pattern">
										<tr>
											<td>
												<b>Pattern: </b> <xsl:value-of select="g:pattern"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:product_review_count">
										<tr>
											<td>
												<b>Product Reviews: </b> <xsl:value-of select="g:product_review_count"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:product_review_average">
										<tr>
											<td>
												<b>Product Review Average: </b> <xsl:value-of select="g:product_review_average"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:custom_label_0">
										<tr>
											<td>
												<b>Custom Label 0: </b> <xsl:value-of select="g:custom_label_0"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:custom_label_1">
										<tr>
											<td>
												<b>Custom Label 1: </b> <xsl:value-of select="g:custom_label_1"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:custom_label_2">
										<tr>
											<td>
												<b>Custom Label 2: </b> <xsl:value-of select="g:custom_label_2"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:custom_label_3">
										<tr>
											<td>
												<b>Custom Label 3: </b> <xsl:value-of select="g:custom_label_3"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:custom_label_4">
										<tr>
											<td>
												<b>Custom Label 4: </b> <xsl:value-of select="g:custom_label_4"/>
											</td>
										</tr>
									</xsl:if>
									<xsl:if test="g:adwords_redirect">
										<tr>
											<td>
												<b>Adwords Redirect: </b> <xsl:variable name="redirectURL">
												<xsl:value-of select="g:adwords_redirect" disable-output-escaping="yes"/>
											</xsl:variable>
											<a href="{$redirectURL}" target="_blank">
												<xsl:value-of select="g:adwords_redirect"/>
											</a>
											</td>
										</tr>
									</xsl:if>
								</table><br />
							</td>
						</tr>
						</xsl:for-each>
					</table>
				</div>
				<div id="footer">
					(c) UK Site Builder Ltd - For more great OpenCart Extensions, please visit <a href="https://www.opencart-extensions.co.uk/?source=ext" title="Visit OpenCart-Extensions.co.uk" target="_blank">UKSB's OpenCart Extensions</a>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
