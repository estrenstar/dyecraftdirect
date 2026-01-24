<?php echo $header; ?><?php echo $column_left; ?>
<!--Main Content block start-->

<div id="content">
  <!--Header Start-->
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-language" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;<?php echo $button_save; ?></button>
		<a href="https://www.huntbee.com/documentation/docs/abandoned-cart-email/" title="Documentation" class="btn btn-default" target="_blank"><i class="fa fa-book"></i>&nbsp;Docs</a>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a> </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <!--Header End-->
  <!--Container 1 start -->
  <div class="container-fluid">
    <!--Start - Error / Success Message if any -->
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <!--End - Error / Success Message if any -->
	<div id="output-console"></div>
	
    <!--Panel Content Start-->
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-shopping-cart"></i> <?php echo $heading_title; ?></h3>
			<?php if ($stores) { ?>
			<div class="pull-right">
			<select id="store">
				<option value="0" <?php echo ($store_id == 0)?'selected':''; ?>>Default Store</option>
				<?php foreach ($stores as $store) { ?>
					<option value="<?php echo $store['store_id']; ?>" <?php echo ($store_id == $store['store_id'])?'selected':''; ?>><?php echo $store['name']; ?></option>
				<?php } ?>
			</select>
			</div>
			<?php } ?>
      </div>
      <div class="panel-body">
        
			<!--Tabs UL Starts-->
			<ul class="nav nav-tabs" id="tabs">
                <li class="active"><a href="#tab-dashboard" onclick="loadCartBlock('customer');" data-toggle="tab"><i class="fa fa-user-plus" aria-hidden="true"></i> <?php echo $tab_customer; ?></a></li>
				<li><a href="#tab-guest" data-toggle="tab" onclick="loadCartBlock('guest');"><i class="fa fa-user" aria-hidden="true"></i> <?php echo $tab_guest; ?></a></li>
				<li><a href="#tab-unidentified" data-toggle="tab" onclick="loadCartBlock('unidentified');"><i class="fa fa-user-secret" aria-hidden="true"></i> <?php echo $tab_unidentified; ?></a></li>
				<li><a href="#tab-graph" data-toggle="tab" onclick="loadBlock('cartlogs');"><i class="fa fa-pie-chart" aria-hidden="true"></i> <?php echo $tab_graph; ?></a></li>
				<li><a href="#tab-setting" data-toggle="tab"><i class="fa fa-cogs" aria-hidden="true"></i> <?php echo $tab_setup; ?></a></li>
				<li><a href="#tab-template" onclick="loadBlock('templates');" data-toggle="tab"><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;<?php echo $tab_template; ?></a></li>
				<li><a href="#tab-unsubscriber" data-toggle="tab" onclick="loadBlock('unsubscriber');"><i class="fa fa-thumbs-o-down" aria-hidden="true"></i> <?php echo $tab_unsubscriber; ?></a></li>
				<li><a href="#tab-logs" onclick="loadBlock('logs');" data-toggle="tab"><i class="fa fa-book"></i>&nbsp;<?php echo $tab_log; ?></a></li>
	          </ul>
			  <!--Tabs UL Ends-->
			  <div class="tab-content">
			  	<!--CUSTOMER CART-->
			  	<div class="tab-pane active" id="tab-dashboard">
					<div id="customer-cart-block"></div>
				</div>
				
				<!--GUEST CART-->
				<div class="tab-pane" id="tab-guest">
					<div id="guest-cart-block"></div>
				</div>
				
				<!--UNIDENTIFIED CART-->
				<div class="tab-pane" id="tab-unidentified">
					<div id="unidentified-cart-block"></div>
				</div>
				
				<!--STAT-->
				<div class="tab-pane" id="tab-graph">
					<div class="row">
						<div class="col-sm-8">
							<div class="panel panel-default">
							  <div class="panel-heading">
								<div class="pull-right"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-calendar"></i> <i class="caret"></i></a>
								  <ul id="range" class="dropdown-menu dropdown-menu-right">
									<li><a href="day">Today</a></li>
									<li class="active"><a href="month">Month</a></li>
									<li><a href="year">Year</a></li>
								  </ul>
								</div>
								<h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Abandoned Cart Items Statistics</h3>
							  </div>
							  <div class="panel-body">
								<div id="chart-cart" style="width: 100%; height: 260px;"></div>
							  </div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="panel panel-default">
							  <div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-pie-chart"></i> Recovered / Conversion Rate</h3>
							  </div>
							  <div class="panel-body">
								<div id="chart-pie" style="width: 100%; height: 260px;"></div>
							  </div>
							</div>
						</div>
					</div>

					<div id="cartlogs-block"></div>
				</div>
				
				<!--SETUP-->
				<div class="tab-pane" id="tab-setting">
					<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-language" class="form-horizontal">
						<ul class="nav nav-tabs" id="setting-tabs">
							<li class="active"><a href="#tab-setting-basic" data-toggle="tab"><i class="fa fa-wrench"></i>&nbsp;<?php echo $tab_setting_basic; ?></a></li>
							<li><a href="#tab-setting-template" data-toggle="tab"><i class="fa fa-envelope"></i>&nbsp;<?php echo $tab_setting_template; ?></a></li>
							<li><a href="#tab-setting-sms" data-toggle="tab"><i class="fa fa-comment"></i>&nbsp;<?php echo $tab_setting_sms; ?></a></li>
							<li><a href="#tab-setting-coupon" data-toggle="tab"><i class="fa fa-tag"></i>&nbsp;<?php echo $tab_setting_coupon; ?></a></li>		  
							<li><a href="#tab-setting-popup" data-toggle="tab"><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp;<?php echo $tab_setting_popup; ?></a></li>
							<li><a href="#tab-setting-cron" data-toggle="tab"><i class="fa fa-terminal"></i>&nbsp;<?php echo $tab_setting_cron; ?></a></li>
							<li><a href="#tab-setting-analytics" data-toggle="tab"><i class="fa fa-bar-chart"></i>&nbsp;<?php echo $tab_setting_analytics; ?></a></li>
							<li><a href="#tab-setting-admin" data-toggle="tab"><i class="fa fa-table"></i>&nbsp;<?php echo $tab_admin_setting; ?></a></li>
						</ul>

						<div class="tab-content">
							<div class="tab-pane active" id="tab-setting-basic">
								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_status; ?></label>
									<div class="col-sm-1">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_status" class="form-control" value="1" <?php echo ($hb_cart_status == 1)? 'checked':''; ?> />
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_enable_extension; ?></label>
									<div class="col-sm-1">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_enable" class="form-control" value="1" <?php echo ($hb_cart_enable == 1)? 'checked':''; ?> />
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-3 control-label">Script Trigger Mode</label>
									<div class="col-sm-2">
										<input type="checkbox" data-on="Web" data-off="Cron" data-toggle="toggle" data-onstyle="primary" data-offstyle="warning" name="hb_cart_webrun_enable" class="form-control" value="1" <?php echo ($hb_cart_webrun_enable == 1)? 'checked':''; ?> />
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_enable_log; ?></label>
									<div class="col-sm-1">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_log_enable" class="form-control" value="1" <?php echo ($hb_cart_log_enable == 1)? 'checked':''; ?> />
									</div>
							   </div>

							   <div class="form-group">
								<label class="col-sm-3 control-label"><?php echo $text_remove_outstock_products; ?></label>
								<div class="col-sm-1">
									<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_remove_oos_product" class="form-control" value="1" <?php echo ($hb_cart_remove_oos_product == 1)? 'checked':''; ?> />
								</div>
						   </div>

							   <div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_keep_guest_cart; ?></label>
									<div class="col-sm-3">
										<select name="hb_cart_clear_guest" class="form-control">
										<?php foreach ($interval as $i) { ?>
											<option value="<?php echo $i; ?>" <?php echo ($hb_cart_clear_guest == $i)? 'selected': ''; ?> ><?php echo $i; ?></option>
										<?php } ?>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label">Delete Inactive guest data after</label>
									<div class="col-sm-3">
										<div class="input-group">
											<input type="number" name="hb_cart_inactive_guest" class="form-control" value="<?php echo $hb_cart_inactive_guest; ?>"  />
											<span class="input-group-addon"> Months</span>
										</div>
									</div>
							    </div>

							    <div class="form-group">
									<label class="col-sm-3 control-label">Complete Order Status</label>
									<div class="col-sm-9">
									<div class="well well-sm" style="height: 150px; overflow: auto;">
										<?php foreach ($order_statuses as $order_status) { ?>
										<div class="checkbox">
										<label>
											<?php if (in_array($order_status['order_status_id'], $hb_cart_complete_status)) { ?>
											<input type="checkbox" name="hb_cart_complete_status[]" value="<?php echo $order_status['order_status_id']; ?>" checked="checked" />
											<?php echo $order_status['name']; ?>
											<?php } else { ?>
											<input type="checkbox" name="hb_cart_complete_status[]" value="<?php echo $order_status['order_status_id']; ?>" />
											<?php echo $order_status['name']; ?>
											<?php } ?>
										</label>
										</div>
										<?php } ?>
									</div>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label">Guest Cart IP Address Matching</label>
									<div class="col-sm-9">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_ip_matching" class="form-control" value="1" <?php echo ($hb_cart_ip_matching == 1)? 'checked':''; ?> />
									</div>
							    </div>
							</div> <!--BASIC -->

							<div class="tab-pane" id="tab-setting-template">
								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_first_template; ?></label>
									<div class="col-sm-4">
										<div class="row">
											<?php foreach ($languages as $language) { ?>
												<div class="col-sm-12">
													<div class="input-group" style="margin-bottom:5px;">
														<span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
														<select name="hb_cart_email_first<?php echo $language['language_id']; ?>" class="form-control">
															<option value="0">Select a template</option>
															<?php foreach ($email_templates as $template) { ?>
																<option value="<?php echo $template['id']; ?>" <?php echo ($hb_cart_email_first[$language['language_id']] == $template['id'])? 'selected':''; ?>><?php echo $template['template_label']; ?> (<?php echo $template['id']; ?>)</option>
															<?php } ?>
														</select>
													</div>
												</div>
											<?php } ?>
										</div>
									</div>		
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_image_dimension; ?></label>
									<div class="col-sm-2">
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-arrows-h"></i></span>
											<input type="number" name="hb_cart_img_width_first" class="form-control" value="<?php echo $hb_cart_img_width_first; ?>"  />
											<span class="input-group-addon"> px</span>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-arrows-v"></i></span>
											<input type="number" name="hb_cart_img_height_first" class="form-control" value="<?php echo $hb_cart_img_height_first; ?>"  />
											<span class="input-group-addon"> px</span>
										</div>
									</div>
							    </div>

								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_trigger; ?></label>
									<div class="col-sm-4">
										<div class="input-group">
											<input type="number" name="hb_cart_trigger" class="form-control" value="<?php echo $hb_cart_trigger; ?>"  />
											<span class="input-group-addon"> HOURS</span>
										</div>
									</div>
							    </div>
								
							    <div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_enable_reminder; ?></label>
									<div class="col-sm-4">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_enable_reminder" class="form-control" value="1" <?php echo ($hb_cart_enable_reminder == 1)? 'checked':''; ?> />
									</div>
							    </div>

								<div class="form-group reminder-settings">
									<label class="col-sm-3 control-label"><?php echo $text_reminder_template; ?></label>
									<div class="col-sm-4">
										<div class="row">
											<?php foreach ($languages as $language) { ?>
												<div class="col-sm-12">
													<div class="input-group" style="margin-bottom:5px;">
														<span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
														<select name="hb_cart_email_reminder<?php echo $language['language_id']; ?>" class="form-control">
															<option value="0">Select a template</option>
															<?php foreach ($email_templates as $template) { ?>
																<option value="<?php echo $template['id']; ?>" <?php echo ($hb_cart_email_reminder[$language['language_id']] == $template['id'])? 'selected':''; ?>><?php echo $template['template_label']; ?> (<?php echo $template['id']; ?>)</option>
															<?php } ?>
														</select>
													</div>
												</div>
											<?php } ?>
										</div>
									</div>		
								</div>
								
								<div class="form-group reminder-settings">
									<label class="col-sm-3 control-label"><?php echo $text_image_dimension; ?></label>
									<div class="col-sm-2">
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-arrows-h"></i></span>
											<input type="number" name="hb_cart_img_width_reminder" class="form-control" value="<?php echo $hb_cart_img_width_reminder; ?>"  />
											<span class="input-group-addon"> px</span>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-arrows-v"></i></span>
											<input type="number" name="hb_cart_img_height_reminder" class="form-control" value="<?php echo $hb_cart_img_height_reminder; ?>"  />
											<span class="input-group-addon"> px</span>
										</div>
									</div>
							    </div>
			
								<div class="form-group reminder-settings">
									<label class="col-sm-3 control-label"><?php echo $text_number_reminder; ?></label>
									<div class="col-sm-4">
										<input type="number" name="hb_cart_reminder_count" class="form-control" value="<?php echo $hb_cart_reminder_count; ?>"  />
									</div>
								</div>
								
								<div class="form-group reminder-settings">
									<label class="col-sm-3 control-label"><?php echo $text_reminder_gap; ?></label>
									<div class="col-sm-4">
										<div class="input-group">
											<input type="number" name="hb_cart_reminder_gap" class="form-control" value="<?php echo $hb_cart_reminder_gap; ?>"  />
											<span class="input-group-addon"> DAYS</span>
										</div>
									</div>
							   </div>

							    <div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_email_guest; ?></label>
									<div class="col-sm-4">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_email_guest" class="form-control" value="1" <?php echo ($hb_cart_email_guest == 1)? 'checked':''; ?> />
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-sm-3">Select template for short-code {cart_items}</label>
									<div class="col-sm-4">
										<div class="input-group">
											<label class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></label>
											<select name="hb_cart_items_template" id="hb_cart_items_template" class="form-control">
												<?php foreach ($cartitems_template_lists as $list) { ?>
													<option value="<?php echo $list['value']; ?>" <?php echo ($hb_cart_items_template == $list['value'])? 'selected="selected"':''; ?>><?php echo $list['label']; ?></option>
												<?php } ?>
											</select>
										</div>		
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-sm-3">Select template for short-code {customer_reviews}</label>
									<div class="col-sm-4">
										<div class="input-group">
											<label class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></label>
											<select name="hb_cart_reviews_template" id="hb_cart_reviews_template" class="form-control">
												<?php foreach ($reviews_template_lists as $list) { ?>
													<option value="<?php echo $list['value']; ?>" <?php echo ($hb_cart_reviews_template == $list['value'])? 'selected="selected"':''; ?>><?php echo $list['label']; ?></option>
												<?php } ?>
											</select>
										</div>		
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_optional_apps; ?></label>
									<div class="col-sm-9">
										<?php if ($et_extn_linked) { ?>
											<div class="alert alert-success"><i class="fa fa-check"></i>&nbsp;<?php echo $text_et_connected; ?></div><br>
										<?php } else { ?>
											<a class="btn btn-default" href="https://www.huntbee.com/email-template-designer-pro-for-opencart&utm_medium=extension&utm_campaign=add-feature" target="_blank"><i class="fa fa-download"></i>&nbsp;<?php echo $text_install_et; ?></a>
										<?php } ?>
									</div>
								</div>
							</div><!---setting template-->

							<div class="tab-pane" id="tab-setting-sms">
								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_sms_template1; ?></label>
									<div class="col-sm-6">
										<div class="row">
											<?php foreach ($languages as $language) { ?>
												<div class="col-sm-12">
													<div class="input-group" style="margin-bottom:5px;">
														<span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
														<textarea name="hb_cart_sms_first<?php echo $language['language_id']; ?>" class="form-control"><?php echo $hb_cart_sms_first[$language['language_id']]; ?></textarea>
													</div>
												</div>
											<?php } ?>
										</div>
									</div>	
									<div class="col-sm-3">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_sms_first_status" class="form-control" value="1" <?php echo ($hb_cart_sms_first_status == 1)? 'checked':''; ?> />
									</div>		
								</div>
			
								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_sms_template2; ?></label>
									<div class="col-sm-6">
										<div class="row">
											<?php foreach ($languages as $language) { ?>
												<div class="col-sm-12">
													<div class="input-group" style="margin-bottom:5px;">
														<span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
														<textarea name="hb_cart_sms_reminder<?php echo $language['language_id']; ?>" class="form-control"><?php echo $hb_cart_sms_reminder[$language['language_id']]; ?></textarea>
													</div>
												</div>
											<?php } ?>
										</div>
									</div>	
			
									<div class="col-sm-3">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_sms_reminder_status" class="form-control" value="1" <?php echo ($hb_cart_sms_reminder_status == 1)? 'checked':''; ?> />
									</div>		
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_optional_apps; ?></label>
									<div class="col-sm-9">
										<?php if ($sms_extn_linked) { ?>
											<div class="alert alert-success"><i class="fa fa-check"></i>&nbsp;<?php echo $text_sms_connected; ?></div><br>
										<?php } else { ?>
											<a class="btn btn-default" href="https://www.huntbee.com/opencart-sms&utm_medium=extension&utm_campaign=add-feature" target="_blank"><i class="fa fa-download"></i>&nbsp;<?php echo $text_install_sms; ?></a>
										<?php } ?>
									</div>
								</div>
							</div>

							<div class="tab-pane" id="tab-setting-coupon">
								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_auto_coupon; ?></label>
									<div class="col-sm-3">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_coupon" class="form-control" value="1" <?php echo ($hb_cart_coupon == 1)? 'checked':''; ?> />
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_delete_coupon; ?></label>
									<div class="col-sm-3">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_coupon_delete" class="form-control" value="1" <?php echo ($hb_cart_coupon_delete == 1)? 'checked':''; ?> />
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_coupon_products; ?></label>
									<div class="col-sm-3">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_coupon_products" class="form-control" value="1" <?php echo ($hb_cart_coupon_products == 1)? 'checked':''; ?> />
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_coupon_freeshipping; ?></label>
									<div class="col-sm-3">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_coupon_freeshipping" class="form-control" value="1" <?php echo ($hb_cart_coupon_freeshipping == 1)? 'checked':''; ?> />
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_coupon_rule; ?> - First Abandoned Cart Email</label>
									<div class="col-sm-9">
										<textarea name="hb_cart_coupon_rules" class="form-control" rows="5"><?php echo $hb_cart_coupon_rules; ?></textarea>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_coupon_validity; ?> - First Abandoned Cart Email</label>
									<div class="col-sm-3">
										<div class="input-group">
											<input type="number" name="hb_cart_coupon_validity" class="form-control" value="<?php echo $hb_cart_coupon_validity; ?>"  />
											<span class="input-group-addon"> DAYS</span>
										</div>
									</div>
							    </div>

								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_coupon_rule; ?> - Reminder Abandoned Cart Emails</label>
									<div class="col-sm-9">
										<textarea name="hb_cart_coupon_2_rules" class="form-control" rows="5"><?php echo $hb_cart_coupon_2_rules; ?></textarea>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_coupon_validity; ?> - Reminder Abandoned Cart Emails</label>
									<div class="col-sm-3">
										<div class="input-group">
											<input type="number" name="hb_cart_coupon2_validity" class="form-control" value="<?php echo $hb_cart_coupon2_validity; ?>"  />
											<span class="input-group-addon"> DAYS</span>
										</div>
									</div>
							    </div>

							</div><!---setting coupon-->

							<div class="tab-pane" id="tab-setting-popup">
								<div class="form-group">
									<label class="col-sm-3 control-label">Exit Intent Popup</label>
									<div class="col-sm-3">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_exit_pop" class="form-control" value="1" <?php echo ($hb_cart_exit_pop == 1)? 'checked':''; ?> />
									</div>
									<label class="col-sm-4 control-label">Include Magnific Popup Libraries (magnific-popup.css and jquery.magnific-popup.min.js) ?</label>
									<div class="col-sm-2">
										<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_cart_magnific_lib" class="form-control" value="1" <?php echo ($hb_cart_magnific_lib == 1)? 'checked':''; ?> />
									</div>
							   </div>

							   	<div class="form-group">
									<label class="control-label col-sm-3">Popup Content</label>
									<div class="col-sm-9"><!--POPUP CONTENT-->
										<ul class="nav nav-tabs" id="popup_languages">
											<?php foreach ($languages as $language) { ?>
											<li><a href="#popup-language<?php echo $language['language_id']; ?>" data-toggle="tab"><?php echo $language['name']; ?></a></li>
											<?php } ?>
										</ul>
										<div class="tab-content">
										<?php foreach ($languages as $language) { ?>
											<div class="tab-pane" id="popup-language<?php echo $language['language_id']; ?>">
												<textarea id="popup-content<?php echo $language['language_id']; ?>" name="hb_cart_exit_intent_block<?php echo $language['language_id']; ?>" class="form-control summernote"><?php echo $hb_cart_exit_intent_block[$language['language_id']]; ?></textarea>	
											</div>
										<?php } ?>
										</div>
									</div><!--POPUP CONTENT-->
								</div>
							</div><!---setting popup-->

							<div class="tab-pane" id="tab-setting-cron">
									<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_cron_command; ?></label>
									<div class="col-sm-9">
										<input type="text" name="hb_cart_pass_key" class="form-control" value="<?php echo $hb_cart_pass_key; ?>" /><br>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
											<textarea class="form-control" readonly><?php echo $hb_cart_cron; ?></textarea>
										</div>
										<br>
										<div class="alert alert-info"><?php echo $text_cron_command_instruction; ?></div>
									</div>
								</div>
							</div><!---setting cron-->

							<div class="tab-pane" id="tab-setting-analytics">
								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_campaign; ?></label>
									<div class="col-sm-9">
										<input type="text" name="hb_cart_campaign" class="form-control" value="<?php echo $hb_cart_campaign; ?>"  />
									</div>
							   	</div>
							</div><!---setting analytics-->

							<div class="tab-pane" id="tab-setting-admin"><!--admin setting-->
								<div class="form-group">
									<label class="col-sm-3 control-label"><?php echo $text_list_columns; ?></label>
									<div class="col-sm-3">
										<table class="table table-bordered table-hover">
											<?php foreach ($table_columns as $column) { ?>
												<tr>
													<td><?php echo $column['column_name']; ?></td>
													<td><input type="checkbox" data-size="small" data-style="android" data-toggle="toggle" data-onstyle="success" name="hb_cart_col_<?php echo $column['column_id']; ?>" class="form-control" value="1" <?php echo ($hb_cart_col[$column['column_id']] == 1)? 'checked':''; ?> /></td>
												</tr>
											<?php } ?>
										</table>
									</div>
								</div>
							</div><!--admin setting-->
						</div>
					</form>
				</div>
				
				<div class="tab-pane" id="tab-template">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-8">
								<div class="input-group">
									<input type="text" id="search-value" onkeyup="searchTemplate();" class="form-control" placeholder="<?php echo $text_search_label; ?>">
									<span class="input-group-addon btn" id="search-template-button" onclick="searchTemplate();"><i class="fa fa-search"></i></span>
								</div>
							</div>
							<div class="col-sm-4">
									<a href="<?php echo $create_template; ?>" class="btn btn-success col-sm-12"><i class="fa fa-plus"></i>&nbsp;<?php echo $button_create_template; ?></a>
							</div>
						</div>
						
						<div class="row" style="margin-top:10px;">
							<div class="col-sm-12">
								<div id="templates-block">LIST</div>
							</div>
						</div>
					</div>
					
					<div class="col-sm-6">
						<div id="template-preview">
							<center><iframe id="iframe" width="100%" height="800px"></iframe></center>
						</div>
					</div>
					 
				</div><!--end tab template div-->
				
				<!--UNSUBSCRIBER-->
				<div class="tab-pane" id="tab-unsubscriber">
					<div id="unsubscriber-block"></div>
				</div>
				
				<!--LOGS TAB-->
				<div class="tab-pane" id="tab-logs">
					<div id="logs-block"></div>
		
					<div class="pull-right" style="margin-top:10px;">
						<a onclick="confirm('Are you sure?') ? location.href='<?php echo $clear; ?>' : false;" data-toggle="tooltip" title="Clear Logs" class="btn btn-danger"><i class="fa fa-eraser"></i>&nbsp;<?php echo $button_clear_logs; ?></a>
					</div>
				</div>
				<!--end tab logs-->
				
			  </div><!--tab-content block end-->
		
		<!--MODAL EMAIL PREVIEW-->
		<div class="modal fade" id="modal-mail-preview" tabindex="-1" role="dialog">
		  <div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
			  <div class="modal-header" style="background-color:#c2dde6;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Email Preview <span class="mail-action-button"></span></h4>
				
			  </div>
			  <div class="modal-body" id="mail-preview">
		
			  </div>
			  <div class="modal-footer">
				 <div class="mail-action-button"></div>
				 <div id="mail-result" style="text-align:center;"></div>
			  </div>
			</div>
		  </div>
		</div>
		<!--END - MODAL EMAIL PREVIEW-->

      </div>
    </div>
    <!--Panel Content End-->
    <!--Huntbee copyrights-->
    <div class="container-fluid">
      <center>
        <span class="help"><?php echo $heading_title; ?> - <?php echo $extension_version;?> &copy; <a href="https://www.huntbee.com/">WWW.HUNTBEE.COM</a> | <a href="https://www.huntbee.com/get-support">SUPPORT</a></span>
      </center>
    </div>
    <!--Huntbee copyrights end-->
  </div>
  <!--Container 1 start -->
</div>
<!--Main Content block end-->
<style type="text/css">

<?php foreach ($show as $key => $value) { ?>
	<?php if ($value == false ) { ?>
	.hb_<?php echo $key; ?>{display: none;}
	<?php } ?>
<?php } ?>

</style>
<script type="text/javascript" src="view/javascript/jscolor/jscolor.js"></script>
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<?php if (version_compare(VERSION,'2.2.0.0', '>'))   { ?>
	<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
	<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
	<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<?php } ?>




<script type="text/javascript"><!--
	$('#popup_languages a:first').tab('show');
//--></script>

<script type="text/javascript">
$(document).ready(function() {
	loadCartBlock('customer');
	
	if ($('input[name="hb_cart_enable_reminder"]').is(':checked')){
		$('.reminder-settings').show();
	}else{
		$('.reminder-settings').hide();
	}
	
});
</script>

<script type="text/javascript">
function loadBlock(name){
	$('#'+name+'-block').html('<center><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></center>');
	$('#'+name+'-block').load('index.php?route=<?php echo $base_route; ?>/hb_cart/'+name+'&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');
}

function loadCartBlock(blocktype){
	$('#'+blocktype+'-cart-block').html('<center><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></center>');
	$('#'+blocktype+'-cart-block').load('index.php?route=<?php echo $base_route; ?>/hb_cart/cartrecords&token=<?php echo $token; ?>&type='+blocktype+'&store_id=<?php echo $store_id; ?>');
}

$('#customer-cart-block').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#customer-cart-block').load(this.href);
});

$('#guest-cart-block').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#guest-cart-block').load(this.href);
});

$('#unidentified-cart-block').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#unidentified-cart-block').load(this.href);
});

$('#cartlogs-block').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#cartlogs-block').load(this.href);
});

$('#unsubscriber-block').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#unsubscriber-block').load(this.href);
});

$('input[name="hb_cart_enable_reminder"]').on('change', function() {
	 if(this.checked) {
        $('.reminder-settings').show();
    }else{
		$('.reminder-settings').hide();
	}
});

</script>

<script type="text/javascript">
//This event is fired after the modal is closed
$("#modal-mail-preview").on('hidden.bs.modal', function(){
	loadCartBlock('customer');
	loadCartBlock('guest');
});

function searchTemplate() {
	$('#templates-block').html('<center><div class="loaddiv"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></div></center>');
	var search_value = $('#search-value').val();
	$('#templates-block').load('index.php?route=<?php echo $base_route; ?>/hb_cart/templates&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>&search='+encodeURIComponent(search_value));
};

function delete_template(id){
	$('#output-console').html('<center><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></center>');
	$.ajax({
		type: 'post',
		url: 'index.php?route=<?php echo $base_route; ?>/hb_cart/delete_template&token=<?php echo $token; ?>',
		data: {id : id},
		dataType: 'json',
		success: function(json) {
			if (json['success']) {
				  $('#output-console').html('<div class="alert alert-success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				  loadBlock('templates');
			}
		},			
		error: function(xhr, ajaxOptions, thrownError) { alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
	 });
}

function preview(id) {
	document.getElementById('iframe').src='<?php echo $preview_link ?>&template_id='+id;
}

function deleteCart(session_id){
	$('#output-console').html('<center><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></center>');
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=<?php echo $base_route; ?>/hb_cart/deletecart&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {session_id: session_id},
		  dataType: 'json',	
		  success: function(json) {
				if (json['success']) {
					  $('#output-console').html('<div class="alert alert-success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
				if (json['warning']) {
					  $('#output-console').html('<div class="alert alert-danger"><i class="fa fa-warning"></i> '+json['warning']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
				loadCartBlock('customer');
				loadCartBlock('guest');
				loadCartBlock('unidentified');
		  },			
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}

	 });
}

function deleteUnsubscriber(id){
	$('#output-console').html('<center><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></center>');
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=<?php echo $base_route; ?>/hb_cart/delete_unsubscriber&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {id: id},
		  dataType: 'json',		
		  success: function(json) {
				if (json['success']) {
					  $('#output-console').html('<div class="alert alert-success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
				if (json['warning']) {
					  $('#output-console').html('<div class="alert alert-danger"><i class="fa fa-warning"></i> '+json['warning']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
				$('#unsubscriber-block').html('<center><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></center>');
				$('#unsubscriber-block').load('index.php?route=<?php echo $base_route; ?>/hb_cart/unsubscriber&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');
		  },			
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}

	 });
}
</script>
<script type="text/javascript"> 
function emailPreview(customer_id, session_id, reminder){
	$('#mail-result').html('');
	$('#mail-preview').html('<center><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></center>');
	$('#mail-preview').load('<?php echo $store_url; ?>index.php?route=extension/module/hb_cart/admin_send&preview=true&passkey=<?php echo $hb_cart_pass_key; ?>&store_id=<?php echo $store_id; ?>&customer_id='+customer_id+'&session_id='+session_id+'&reminder='+reminder);
	$('.mail-action-button').html('<a onclick="sendAbandonedEmail(' + customer_id + ', \''+session_id+'\',' + reminder + ');" class="btn btn-sm btn-success">Confirm & Send</a>');
	$('#modal-mail-preview').modal('show');
}

function sendAbandonedEmail(customer_id, session_id, reminder){
	$('#mail-result').html('<center><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></center>');
	$.ajax({
		url: '<?php echo $store_url; ?>index.php?route=extension/module/hb_cart/admin_send&passkey=<?php echo $hb_cart_pass_key; ?>&store_id=<?php echo $store_id; ?>&customer_id='+customer_id+'&session_id='+session_id+'&reminder='+reminder,
		dataType: 'json',
		success: function(json) {
			if (json['success']) {
				  $('#mail-result').html('<div class="alert alert-success"><i class="fa fa-check"></i> '+json['success']+'</div>');
				  $('.mail-action-button').html('<a class="btn btn-sm btn-success"><i class="fa fa-check"></i> Email Sent</a>');
			}
			if (json['warning']) {
				  $('#mail-result').html('<div class="alert pr_warning"><i class="fa fa-exclamation"></i> '+json['warning']+'</div>');
				  $('.mail-action-button').html('<a class="btn btn-sm btn-warning"><i class="fa fa-exclamation"></i> Skipped</a>');
			}
		},			
		error: function(xhr, ajaxOptions, thrownError) { alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
	 });
}

</script>

<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.js"></script>
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.resize.min.js"></script>
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.pie.min.js"></script>
<!--<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.orderBars.js"></script> -->
<script type="text/javascript"><!--
$('#range a').on('click', function(e) {
	e.preventDefault();
	
	$(this).parent().parent().find('li').removeClass('active');
	
	$(this).parent().addClass('active');
	
	$.ajax({
		type: 'get',
		url: 'index.php?route=<?php echo $base_route; ?>/hb_cart/chart&token=<?php echo $token; ?>&range=' + $(this).attr('href'),
		dataType: 'json',
		success: function(json) {
				if (typeof json['carts'] == 'undefined') { return false; }
				
				var option = {
							series: {
								bars: {
									show: true,
									fill: true,
									barWidth: 0.5,
									align: "center",
									order: 1
								}
							},
							xaxis: {
								show: true,
								ticks: json['xaxis']
							},
							grid: {
								backgroundColor: '#FFFFFF',
								hoverable: true,
							},
							colors: ['#3AD9D9', '#E4E644'],
							yAxis: {
								allowDecimals: false,
								tickDecimals: 0
							}
						}
			
			$.plot('#chart-cart', [json['carts'], json['products']], option);	
					
			$('#chart-cart').bind('plothover', function(event, pos, item) {
				$('.tooltip').remove();
			  
				if (item) {
					$('<div id="tooltip" class="tooltip top in"><div class="tooltip-arrow"></div><div class="tooltip-inner">' + item.datapoint[1].toFixed(2) + '</div></div>').prependTo('body');
					
					$('#tooltip').css({
						position: 'absolute',
						left: item.pageX - ($('#tooltip').outerWidth() / 2),
						top: item.pageY - $('#tooltip').outerHeight(),
						pointer: 'cusror'
					}).fadeIn('slow');	
					
					$('#chart-cart').css('cursor', 'pointer');		
			  	} else {
					$('#chart-cart').css('cursor', 'auto');
				}
			});			
		},
        error: function(xhr, ajaxOptions, thrownError) {
           alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
	});
});

$('#range .active a').trigger('click');


//--></script> 
<script type="text/javascript">
var data = [
{label: " Customer Not Responded ", data: <?php echo $total_not_responded; ?> },
{label: " Customer Responded ", data: <?php echo $total_responded; ?> }
];

$.plot('#chart-pie', data, {
    series: {
        pie: {
            show: true
        }
    },
	colors: ['#278E72', '#87C636'],
	grid: {
		hoverable: true
	},
    legend: {
        show: false
    }
});
</script>

<script type="text/javascript">
$('#store').on('change', function() {
	window.location.href = 'index.php?route=<?php echo $base_route; ?>/hb_cart&token=<?php echo $token; ?>&store_id='+$('#store').val();
});
</script>
<?php echo $footer; ?>