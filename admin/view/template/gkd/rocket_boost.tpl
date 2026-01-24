<?php 
$gkhtab = $gkhdiv = 0;
function fieldLabel($label, $_language) { ?>
  <?php if ($_language->get('entry_'.$label.'_i') != 'entry_'.$label.'_i') { ?>
    <label class="control-label"><span data-toggle="tooltip" title="<?php echo $_language->get('entry_'.$label.'_i'); ?>"><?php echo $_language->get('entry_'.$label); ?></span></label>
  <?php } else { ?>
    <label class="control-label"><?php echo $_language->get('entry_'.$label); ?></label>
  <?php } ?>
<?php } ?>
<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<?php if(!empty($style_scoped)) { ?><style scoped><?php echo $style_scoped; ?></style><?php } ?>
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>

  <div class="<?php if(version_compare(VERSION, '2', '>=')) echo 'container-fluid'; ?>">
    <?php if (isset($success) && $success) { ?><div class="alert alert-success success"><i class="fa fa-check-circle"></i> <?php echo $success; ?> <button type="button" class="close" data-dismiss="alert">&times;</button></div><script type="text/javascript">setTimeout("$('.alert-success').slideUp();",5000);</script><?php } ?>
    <?php if (isset($info) && $info) { ?><div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $info; ?> <button type="button" class="close" data-dismiss="alert">&times;</button></div><?php } ?>
    <?php if (isset($error) && $error) { ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error; ?> <button type="button" class="close" data-dismiss="alert">&times;</button></div><?php } ?>
    <?php if (isset($warning) && $warning) { ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $warning; ?> <button type="button" class="close" data-dismiss="alert">&times;</button></div><?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading module-heading">
        <div class="pull-right saveBtns">
          <button data-page="" type="button" class="btn btn-warning clearCache"><i class="fa fa-trash-o"></i> <?php echo $_language->get('text_clear_cache'); ?></button>
          <button type="submit" form="form" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_save; ?></button>
          <a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i> <?php echo $button_cancel; ?></a>
        </div>
        <h3 class="panel-title"><img src="<?php echo $_img_path; ?>icon_big.png" alt="" style="vertical-align:middle;"/>&nbsp;&nbsp;<?php echo $heading_title; ?></h3>
      </div>
      <div class="content panel-body">
      <?php /*
        <div id="stores" class="form-inline" <?php if (version_compare(VERSION, '2', '>=') && 0) echo 'class="v2"'; ?>>
          <?php echo $_language->get('text_store_select'); ?>
          <select name="store" class="form-control input-sm">
            <?php foreach ($stores as $store) { ?>
            <?php if ($store_id == $store['store_id']) { ?>
            <option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
            <?php } ?>
            <?php } ?>
          </select>
        </div>
      */ ?>
        
        <ul class="nav nav-tabs mainMenu">
          <li class="<?php echo empty($showTab) ? 'active' : ''; ?>"><a href="#tab-main" data-toggle="tab"><i class="fa fa-fw fa-bolt"></i><?php echo $_language->get('text_tab_main'); ?></a></li>
          <li><a href="#tab-cdn" data-toggle="tab"><i class="fa fa-dashboard"></i><?php echo $_language->get('text_tab_cdn'); ?></a></li>
          <li><a href="#tab-opt" data-toggle="tab"><i class="fa fa-magic"></i><?php echo $_language->get('text_tab_optimization'); ?></a></li>
          <?php /*<li><a href="#tab-cron" data-toggle="tab"><i class="fa fa-clock-o"></i><?php echo $_language->get('text_tab_cron'); ?></a></li> */ ?>
          <li><a href="#tab-manual" data-toggle="tab"><i class="fa fa-book"></i><?php echo $_language->get('text_tab_manual'); ?></a></li>
          <li><a href="#tab-about" data-toggle="tab"><i class="fa fa-info"></i><?php echo $_language->get('text_tab_about'); ?></a></li>
        </ul>
        
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
          <div class="tab-content container-fluid">
          
            <div class="tab-pane clearfix" id="tab-1" style="min-height:500px">
              <div class="">
                <div class="gkdwidget gkdwidget-color-blueLight clearfix">
                  <header role="heading">
                    <i class="icon fa fa-info-circle fa-2x pull-left" title="<?php echo $_language->get('info_help'); ?>"></i>
                    <ul class="nav nav-tabs pull-left in">
                      <li><a data-toggle="tab" href="#gkhelp<?php echo $gkhtab++; ?>"><span class="hidden-mobile hidden-tablet"><?php echo $_language->get('info_mass_tr_title'); ?></span></a></li>
                    </ul>
                  </header>
                  <div class="gkdwidget-container" style="display:none">
                    <div class="tab-content">
                      <div class="tab-pane" id="gkhelp<?php echo $gkhdiv++; ?>"><?php echo $_language->get('info_mass_tr'); ?></div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div id="update-form">
                <fieldset class="filters"><legend>
                  <!--div class="pull-right" style="font-size:13px; color:#666"><?php echo $_language->get('text_total_items'); ?> <span class="export_number badge clearblue"></span></div-->
                  <?php echo $_language->get('text_process_options'); ?>
                </legend>
                  
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <?php fieldLabel('type', $_language); ?>
                        <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-fw fa-caret-square-o-right"></i></span>
                        <select name="type" class="form-control big-select">
                          <?php $i=1; foreach ($process_types as $process_type) { ?>
                          <option value="<?php echo $process_type; ?>"><?php echo $_language->get('text_type_'.$process_type); ?></option>
                          <?php } ?>
                        </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <?php fieldLabel('overwrite', $_language); ?>
                        <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-fw fa-edit"></i></span>
                        <select class="form-control" name="overwrite">
                          <option value=""><?php echo $_language->get('text_empty_only'); ?></option>
                          <option value="1"><?php echo $_language->get('text_overwrite'); ?></option>
                        </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <?php fieldLabel('source_language', $_language); ?>
                        <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-fw fa-flag"></i></span>
                        <select class="form-control" name="source_language">
                          <option value="" <?php if (empty($profile['source_language'])) echo 'selected'; ?>><?php echo $_language->get('text_auto_detect'); ?></option>
                          <?php foreach ($languages as $language) { ?>
                            <option value="<?php echo $language['language_id']; ?>" <?php if (isset($profile['source_language']) && $profile['source_language'] !== '' && $profile['source_language'] == $language['language_id']) echo 'selected'; ?>><?php echo $language['name']; ?></option>
                          <?php } ?>
                        </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <?php fieldLabel('target_language', $_language); ?>
                        <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-fw fa-language"></i></span>
                        <select class="form-control" name="target_language">
                          <!--option value=""><?php echo $_language->get('text_all'); ?></option-->
                          <?php foreach ($languages as $language) { ?>
                            <option value="<?php echo $language['language_id']; ?>" <?php if (isset($profile['target_language']) && $profile['target_language'] !== '' && $profile['target_language'] == $language['language_id']) echo 'selected'; ?>><?php echo $language['name']; ?></option>
                          <?php } ?>
                        </select>
                        </div>
                      </div>
                    </div>
                  </div>

                  </fieldset>
                  
                  <fieldset class="filters"><legend><?php echo $_language->get('text_process_filters'); ?></legend>

                  <div class="row">
                    <div class="col-sm-6">
                      <!--div class="form-group">
                        <?php fieldLabel('filter_status', $_language); ?>
                        <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-fw fa-check"></i></span>
                        <select class="form-control" name="filter_status">
                          <option value=""><?php echo $_language->get('text_all'); ?></option>
                          <option value="1" <?php if (isset($profile['filter_status']) && $profile['filter_status'] !== '' && $profile['filter_status'] == 1) echo 'selected'; ?>><?php echo $_language->get('text_enabled'); ?></option>
                          <option value="0" <?php if (isset($profile['filter_status']) && $profile['filter_status'] !== '' && $profile['filter_status'] == 0) echo 'selected'; ?>><?php echo $_language->get('text_disabled'); ?></option>
                        </select>
                        </div>
                      </div-->
                      <div class="form-group">
                        <?php fieldLabel('limit_id', $_language); ?>
                        <div class="input-group">
                        <span class="input-group-addon"><?php echo $_language->get('text_limit_min'); ?></span>
                        <input type="text" class="form-control" name="filter-min-id" value="<?php echo isset($profile['filter-min-id']) ? $profile['filter-min-id'] : ''; ?>" placeholder="" />
                        <span class="input-group-addon"><?php echo $_language->get('text_limit_max'); ?></span>
                        <input type="text" class="form-control" name="filter-max-id" value="<?php echo isset($profile['filter-max-id']) ? $profile['filter-max-id'] : ''; ?>" placeholder=""/>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <?php fieldLabel('limit', $_language); ?>
                        <div class="input-group">
                        <span class="input-group-addon"><?php echo $_language->get('text_limit_start'); ?></span>
                        <input type="text" class="form-control" name="filter-start" value="<?php echo isset($profile['filter-start']) ? $profile['filter-start'] : ''; ?>" placeholder="" />
                        <span class="input-group-addon"><?php echo $_language->get('text_limit_limit'); ?></span>
                        <input type="text" class="form-control" name="filter-limit" value="<?php echo isset($profile['filter-limit']) ? $profile['filter-limit'] : ''; ?>" placeholder=""/>
                        </div>
                      </div>
                    </div>
                  </div>

                  <hr class="dotted" style="margin:0"/>

                  <div class="row show-mass-product">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <?php fieldLabel('category', $_language); ?>
                        <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-fw fa-folder-open-o"></i></span>
                        <select class="selectize_category" name="filter_category[]" multiple="multiple">
                          <option value=""><?php echo $_language->get('text_all'); ?></option>
                          <?php foreach ($categories as $category) { ?>
                            <option value="<?php echo $category['category_id']; ?>" <?php if (isset($profile['filter_category']) && in_array($category['category_id'], $profile['filter_category'])) echo 'selected'; ?>><?php echo $category['name']; ?></option>
                          <?php } ?>
                        </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  </fieldset>
                
                <div>
                  <button type="button" class="startProcess btn btn-success btn-lg btn-block"><i class="fa fa-play"></i> <?php echo $_language->get('text_start_process'); ?></button>
                  <button type="button" class="pauseProcess btn btn-default btn-lg btn-block" style="display:none"><i class="fa fa-gear fa-spin"></i> <?php echo $_language->get('text_pause_process'); ?></button>
                </div>

                <div class="spacer"></div>
                
                <div id="translateProgress" class="progress" style="display:none">
                  <div class="progress-bar progress-bar-striped progress-bar-success active"></div>
                </div>
                
              </div>
            </div>
            
            <div class="tab-pane clearfix form-horizontal <?php echo empty($showTab) ? 'active' : ''; ?>" id="tab-main" style="min-height:500px">
              
              <div class="col-md-offset-2">
                <div class="gkdwidget gkdwidget-color-blueLight clearfix">
                  <header role="heading">
                    <i class="icon fa fa-info-circle fa-2x pull-left" title="<?php echo $_language->get('info_help'); ?>"></i>
                    <ul class="nav nav-tabs pull-left in">
                      <li><a data-toggle="tab" href="#gkhelp<?php echo $gkhtab++; ?>"><span class="hidden-mobile hidden-tablet"><?php echo $_language->get('info_page_title'); ?></span></a></li>
                      <li><a data-toggle="tab" href="#gkhelp<?php echo $gkhtab++; ?>"><span class="hidden-mobile hidden-tablet"><?php echo $_language->get('info_custom_page_title'); ?></span></a></li>
                      <li><a data-toggle="tab" href="#gkhelp<?php echo $gkhtab++; ?>"><span class="hidden-mobile hidden-tablet"><?php echo $_language->get('info_widget_front_title'); ?></span></a></li>
                      <li><a data-toggle="tab" href="#gkhelp<?php echo $gkhtab++; ?>"><span class="hidden-mobile hidden-tablet"><?php echo $_language->get('info_widget_admin_title'); ?></span></a></li>
                    </ul>
                  </header>
                  <div class="gkdwidget-container" style="display:none">
                    <div class="tab-content">
                      <div class="tab-pane" id="gkhelp<?php echo $gkhdiv++; ?>"><?php echo $_language->get('info_page'); ?></div>
                      <div class="tab-pane" id="gkhelp<?php echo $gkhdiv++; ?>"><?php echo $_language->get('info_custom_page'); ?></div>
                      <div class="tab-pane" id="gkhelp<?php echo $gkhdiv++; ?>"><?php echo $_language->get('info_widget_front'); ?></div>
                      <div class="tab-pane" id="gkhelp<?php echo $gkhdiv++; ?>"><?php echo $_language->get('info_widget_admin'); ?></div>
                    </div>
                  </div>
                </div>
              </div>
                
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $_language->get('entry_status_i'); ?>"><?php echo $_language->get('entry_status'); ?></span></label>
                <div class="col-sm-10">
                  <div class="ckbx-style-8">
                    <input class="modStatus" type="checkbox" id="<?php echo $prefix; ?>status" value="1" name="<?php echo $prefix; ?>status" <?php if (!empty(${$prefix.'status'})) echo 'checked="checked"'; ?> >
                    <label for="<?php echo $prefix; ?>status"></label>
                  </div>
                </div>
              </div>
              
              <div class="form-group showStatus" <?php if (!empty(${$prefix.'status'})) echo 'style="display:none"'; ?>>
                <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $_language->get('entry_test_i'); ?>"><?php echo $_language->get('entry_test'); ?></span></label>
                <div class="col-sm-4">
                  <div class="ckbx-style-8">
                    <input type="checkbox" id="<?php echo $prefix; ?>test" value="1" name="<?php echo $prefix; ?>test" <?php if (!empty(${$prefix.'test'})) echo 'checked="checked"'; ?> >
                    <label for="<?php echo $prefix; ?>test"></label>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $_language->get('entry_cache_pages_i'); ?>"><?php echo $_language->get('entry_cache_pages'); ?></span></label>
                <div class="col-sm-10">
                  <table class="table">
                    <thead>
                      <tr>
                        <th><?php echo $_language->get('text_type'); ?></th>
                        <th><?php echo $_language->get('text_status'); ?></th>
                        <th><?php echo $_language->get('text_expire'); ?></th>
                        <th><?php echo $_language->get('text_action'); ?></th>
                      </tr>
                    </thead>
                    <tbody class="extraTable">
                      <?php foreach ($common_pages as $page => $exp) { ?>
                      <tr>
                        <td><?php echo $_language->get('text_'.$page); ?><br/><span style="color:#b5b5b5"><?php echo $page; ?></span></td>
                        <td>
                          <div class="ckbx-style-8">
                            <input class="pageSwitch" type="checkbox" id="<?php echo $prefix; ?>page<?php echo $page; ?>on" value="1" name="<?php echo $prefix; ?>page[<?php echo $page; ?>][on]" <?php if (!empty(${$prefix.'page'}[$page]['on'])) echo 'checked="checked"'; ?> >
                            <label for="<?php echo $prefix; ?>page<?php echo $page; ?>on"></label>
                          </div>
                        </td>
                        <td><input style="width:130px" class="form-control" type="text" name="<?php echo $prefix; ?>page[<?php echo $page; ?>][exp]" value="<?php if (isset(${$prefix.'page'}[$page]['exp'])) echo ${$prefix.'page'}[$page]['exp']; ?>" placeholder="<?php echo $exp; ?>"/></td>
                        <td style="width:10%; white-space:nowrap; text-align:right">
                          <button data-page="<?php echo $page; ?>" type="button" data-toggle="tooltip" title="<?php echo $_language->get('text_clear_cache'); ?>" class="btn btn-warning clearCache"><i class="fa fa-trash-o"></i></button>
                          <button data-page="<?php echo $page; ?>" type="button" data-toggle="tooltip" title="<?php echo $_language->get('text_preload_cache'); ?>" class="btn btn-primary startProcess"><i class="fa fa-refresh"></i></button>
                        </td>
                      </tr>
                      <?php 
                        unset(${$prefix.'page'}[$page]);
                      }
                      ${$prefix.'page'}['_extra_'] = array();
                      ?>
                      <?php foreach (${$prefix.'page'} as $page => $exp) { ?>
                      <tr<?php echo $page == '_extra_' ? ' class="extraPage" style="display:none"' : ''; ?>>
                        <?php if ($page == '_extra_') { ?>
                        <td><input type="text" class="form-control" placeholder="blog/blog" name="extra_page[_extra_]"/></td>
                        <?php } else { ?>
                        <td><i class="fa fa-pencil-square-o gkd-badge"></i> <?php echo $page; ?></td>
                        <?php } ?>
                        <td>
                          <div class="ckbx-style-8">
                            <input class="pageSwitch" type="checkbox" id="<?php echo $prefix; ?>page<?php echo $page; ?>on" value="1" name="<?php echo $page == '_extra_' ? 'extra_page_on[_extra_]' : $prefix.'page['.$page.'][on]'; ?>" <?php if (!empty(${$prefix.'page'}[$page]['on'])) echo 'checked="checked"'; ?> >
                            <label for="<?php echo $prefix; ?>page<?php echo $page; ?>on"></label>
                          </div>
                        </td>
                        <td><input style="width:130px" class="form-control" type="text" name="<?php echo $page == '_extra_' ? 'extra_page_exp[_extra_]' : $prefix.'page['.$page.'][exp]'; ?>" value="<?php if (isset(${$prefix.'page'}[$page]['exp'])) echo ${$prefix.'page'}[$page]['exp']; ?>" placeholder="7" <?php echo $page == '_extra_' ? 'disabled' : ''; ?>/></td>
                        <td style="width:10%; white-space:nowrap; text-align:right">
                          <button type="button" data-toggle="tooltip" title="<?php echo $_language->get('text_delete'); ?>" class="btn btn-danger remove-extra-page"><i class="fa fa-minus-circle"></i></button>
                          <?php if ($page != '_extra_') { ?>
                          <button type="button" data-toggle="tooltip" title="<?php echo $_language->get('text_clear_cache'); ?>" class="btn btn-warning"><i class="fa fa-trash-o"></i></button>
                          <button type="button" data-toggle="tooltip" title="<?php echo $_language->get('text_preload_cache'); ?>" class="btn btn-primary"><i class="fa fa-refresh"></i></button>
                          <?php } ?>
                        </td>
                      </tr>
                      <?php } ?>
                      <tr>
                        <td colspan="4">
                          <button type="button" class="btn btn-success btn-block add-extra-page"><i class="fa fa-plus"></i> <?php echo $_language->get('text_add_page'); ?></button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $_language->get('entry_widget_i'); ?>"><?php echo $_language->get('entry_widget'); ?></span></label>
                <div class="col-sm-10">
                  <div class="ckbx-style-8">
                    <input type="checkbox" id="<?php echo $prefix; ?>widget" value="1" name="<?php echo $prefix; ?>widget" <?php if (!empty(${$prefix.'widget'})) echo 'checked="checked"'; ?> >
                    <label for="<?php echo $prefix; ?>widget"></label>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $_language->get('entry_dyn_route_i'); ?>"><?php echo $_language->get('entry_dyn_route'); ?></span></label>
                <div class="col-sm-10">
                  <div>
                    <textarea rows="4" class="form-control" id="<?php echo $prefix; ?>dyn_route" name="<?php echo $prefix; ?>dyn_route" placeholder="common/column_right<?php echo PHP_EOL; ?>extension/module/special"><?php echo !empty(${$prefix.'dyn_route'}) ? ${$prefix.'dyn_route'} : ''; ?></textarea>
                    <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Do not set here these routes which are already handled correctly by the module: common/cart, common/language, common/currency</div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="tab-pane form-horizontal" id="tab-opt">
              
              <div class="col-md-offset-">
                <div class="gkdwidget gkdwidget-color-blueLight clearfix">
                  <header role="heading">
                    <i class="icon fa fa-info-circle fa-2x pull-left" title="<?php echo $_language->get('info_help'); ?>"></i>
                    <ul class="nav nav-tabs pull-left in">
                      <li><a data-toggle="tab" href="#gkhelp<?php echo $gkhtab++; ?>"><span class="hidden-mobile hidden-tablet"><?php echo $_language->get('info_optimization_title'); ?></span></a></li>
                    </ul>
                  </header>
                  <div class="gkdwidget-container" style="display:none">
                    <div class="tab-content">
                      <div class="tab-pane" id="gkhelp<?php echo $gkhdiv++; ?>"><?php echo $_language->get('info_optimization'); ?></div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <!--label class="col-sm-2 control-label"><?php echo $_language->get('entry_optimization'); ?></label-->
                <div class="col-sm-12">
                  <table class="table">
                    <thead>
                      <tr>
                        <th><?php echo $_language->get('text_opt_type'); ?></th>
                        <th><?php echo $_language->get('text_opt_desc'); ?></th>
                        <th></th>
                        <th><?php echo $_language->get('text_action'); ?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      foreach (array('missing_orders', 'sql_engine', 'sql_index') as $type) { ?>
                      <tr>
                        <td style="white-space:nowrap;font-weight:bold;vertical-align:top"><?php echo $_language->get('text_opt_'.$type); ?></td>
                        <td style="padding-bottom: 40px;"><?php echo $_language->get('text_opt_desc_'.$type); ?></td>
                        <td style="vertical-align:top">
                          <?php if ($type == 'missing_orders') { ?>
                            <select class="form-control missingOrdersDelay" name="<?php echo $prefix; ?>missing_orders_delay">
                              <?php foreach (array_reverse(array('1 day', '7 days', '1 month', '6 months', '1 year')) as $mode) { ?>
                                <option value="<?php echo $mode; ?>" <?php if (${$prefix.'missing_orders_delay'} == $mode) echo 'selected="selected"'; ?>><?php echo $_language->get('text_missing_orders_delay_'.$mode); ?></option>
                              <?php } ?>
                            </select>
                          <?php } ?>
                        </td>
                        <td style="width:10%; white-space:nowrap; text-align:right;vertical-align:top">
                          <?php if ($type != 'missing_orders') { ?>
                          <button style="width:60px" type="button" data-toggle="tooltip" title="<?php echo $_language->get('text_run_optimization_check'); ?>" class="btn btn-primary runOptimization" data-type="<?php echo $type; ?>" data-check="1"><i class="fa fa-search"></i></button>
                          <?php } ?>
                          <button style="width:60px" type="button" data-toggle="tooltip" title="<?php echo $_language->get('text_run_optimization'); ?>" class="btn btn-success runOptimization" data-type="<?php echo $type; ?>" data-check=""><i class="fa fa-magic"></i></button>
                        </td>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                  <div class="well optResult" style="display:none; height:350px; overflow:auto;line-height:21px;"></div>
                </div>
              </div>
              
            </div>
            
            <div class="tab-pane form-horizontal" id="tab-cdn">
              
              <div class="col-md-offset-2">
                <div class="gkdwidget gkdwidget-color-blueLight clearfix">
                  <header role="heading">
                    <i class="icon fa fa-info-circle fa-2x pull-left" title="<?php echo $_language->get('info_help'); ?>"></i>
                    <ul class="nav nav-tabs pull-left in">
                      <li><a data-toggle="tab" href="#gkhelp<?php echo $gkhtab++; ?>"><span class="hidden-mobile hidden-tablet"><?php echo $_language->get('info_opt_title'); ?></span></a></li>
                      <li><a data-toggle="tab" href="#gkhelp<?php echo $gkhtab++; ?>"><span class="hidden-mobile hidden-tablet"><?php echo $_language->get('info_cdn_title'); ?></span></a></li>
                    </ul>
                  </header>
                  <div class="gkdwidget-container" style="display:none">
                    <div class="tab-content">
                      <div class="tab-pane" id="gkhelp<?php echo $gkhdiv++; ?>"><?php echo $_language->get('info_opt'); ?></div>
                      <div class="tab-pane" id="gkhelp<?php echo $gkhdiv++; ?>"><?php echo $_language->get('info_cdn'); ?></div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $_language->get('entry_css_opt_i'); ?>"><?php echo $_language->get('entry_css_opt'); ?></span></label>
                <div class="col-sm-10">
                  <div class="ckbx-style-8">
                    <input type="checkbox" id="<?php echo $prefix; ?>css_opt" value="1" name="<?php echo $prefix; ?>css_opt" <?php if (!empty(${$prefix.'css_opt'})) echo 'checked="checked"'; ?> >
                    <label for="<?php echo $prefix; ?>css_opt"></label>
                  </div>
                  <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> This may not work well depending the theme, check if it works fine, if there is issues you can let this option disabled</div>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $_language->get('entry_js_opt_i'); ?>"><?php echo $_language->get('entry_js_opt'); ?></span></label>
                <div class="col-sm-10">
                  <div class="ckbx-style-8">
                    <input type="checkbox" id="<?php echo $prefix; ?>js_opt" value="1" name="<?php echo $prefix; ?>js_opt" <?php if (!empty(${$prefix.'js_opt'})) echo 'checked="checked"'; ?> >
                    <label for="<?php echo $prefix; ?>js_opt"></label>
                  </div>
                  <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> This may not work well depending the theme, check if it works fine, if there is issues you can let this option disabled</div>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $_language->get('entry_html_opt_i'); ?>"><?php echo $_language->get('entry_html_opt'); ?></span></label>
                <div class="col-sm-10">
                  <div class="ckbx-style-8">
                    <input type="checkbox" id="<?php echo $prefix; ?>html_opt" value="1" name="<?php echo $prefix; ?>html_opt" <?php if (!empty(${$prefix.'html_opt'})) echo 'checked="checked"'; ?> >
                    <label for="<?php echo $prefix; ?>html_opt"></label>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $_language->get('entry_opt_off_i'); ?>"><?php echo $_language->get('entry_opt_off'); ?></span></label>
                <div class="col-sm-10">
                  <div class="ckbx-style-8">
                    <textarea class="form-control" id="<?php echo $prefix; ?>opt_off" name="<?php echo $prefix; ?>opt_off" placeholder="view/javascript/script.js<?php echo PHP_EOL; ?>view/theme/default/style.css"><?php echo !empty(${$prefix.'opt_off'}) ? ${$prefix.'opt_off'} : ''; ?></textarea>
                  </div>
                </div>
              </div>
              
              <?php /*
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $_language->get('entry_defer_i'); ?>"><?php echo $_language->get('entry_defer'); ?></span></label>
                <div class="col-sm-10">
                  <div class="ckbx-style-8">
                    <input type="checkbox" id="<?php echo $prefix; ?>defer" value="1" name="<?php echo $prefix; ?>defer" <?php if (!empty(${$prefix.'defer'})) echo 'checked="checked"'; ?> >
                    <label for="<?php echo $prefix; ?>defer"></label>
                  </div>
                </div>
              </div>
              */ ?>
              
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $_language->get('entry_cdn_status_i'); ?>"><?php echo $_language->get('entry_cdn_status'); ?></span></label>
                <div class="col-sm-10">
                  <div class="ckbx-style-8">
                    <input type="checkbox" id="<?php echo $prefix; ?>cdn" value="1" name="<?php echo $prefix; ?>cdn" <?php if (!empty(${$prefix.'cdn'})) echo 'checked="checked"'; ?> >
                    <label for="<?php echo $prefix; ?>cdn"></label>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $_language->get('entry_cdn_mode'); ?></label>
                <div class="col-sm-10">
                  <select class="form-control cdnMode" name="<?php echo $prefix; ?>cdn_mode">
                    <?php foreach (array('', '1') as $mode) { ?>
                      <option value="<?php echo $mode; ?>" <?php if (${$prefix.'cdn_mode'} == $mode) echo 'selected="selected"'; ?>><?php echo $_language->get('text_cdn_mode_'.$mode); ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              
              <div class="form-group custCdnShow" <?php if (!${$prefix.'cdn_mode'}) echo 'style="display:none"'; ?>>
                <label class="col-sm-2 control-label"><?php echo $_language->get('entry_cust_cdn'); ?></label>
                <div class="col-sm-10">
                  <table class="table">
                    <thead>
                      <tr>
                        <th><?php echo $_language->get('text_cdn_source'); ?></th>
                        <th><?php echo $_language->get('text_cdn_target'); ?></th>
                        <th><?php echo $_language->get('text_action'); ?></th>
                      </tr>
                    </thead>
                    <tbody class="cdnTable">
                      <?php 
                      ${$prefix.'cust_cdn'}['_extra_'] = '';
                      foreach (${$prefix.'cust_cdn'} as $source => $target) { ?>
                      <tr<?php echo $source == '_extra_' ? ' class="extraCdn" style="display:none"' : ''; ?>>
                        <td><input class="form-control" type="text" name="<?php echo $prefix; ?>cust_cdn[source][]" value="<?php echo $source != '_extra_' ? $source : ''; ?>" placeholder="catalog/view/javascript/jquery/jquery-2.1.1.min.js" <?php echo $source == '_extra_' ? 'disabled' : ''; ?>/></td>
                        <td><input class="form-control" type="text" name="<?php echo $prefix; ?>cust_cdn[target][]" value="<?php echo $target; ?>" placeholder="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js" <?php echo $source == '_extra_' ? 'disabled' : ''; ?>/></td>
                        <td style="width:10%; white-space:nowrap; text-align:right">
                          <button type="button" data-toggle="tooltip" title="<?php echo $_language->get('text_delete'); ?>" class="btn btn-danger remove-extra-page"><i class="fa fa-minus-circle"></i></button>
                        </td>
                      </tr>
                      <?php } ?>
                      <tr>
                        <td colspan="3">
                          <button type="button" class="btn btn-success btn-block add-cdn"><i class="fa fa-plus"></i> <?php echo $_language->get('text_add_cdn'); ?></button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            
            <div class="tab-pane" id="tab-manual">
              <div class="well">
                <h4><i class="fa fa-info-circle"></i> What do the module do?</h4>
                <p>This extension improving your website speed by working on different optimizations fields, one of the most powerful being the smart caching system which is saving some generated page (or parts of the page) for further display without having to re-process all the logic behind. This way a lot of database call, server data computing and processing is spared, the page is loading lighning fast and your server will breath a lot more and be more responsive.</p>
                <p>Here are main benefits:
                <ul>
                  <li><b>Smart Cache:</b> Intelligent and safe cache system to gain speed without compromising user experience</li>
                  <li><b>Content Delivery Network:</b> Get JS and CSS scripts from external servers dedicated to that for fast load of ressources</li>
                  <li><b>Server ressources:</b> The optimizations are sparing your server ressources, so not only pages are loading fast, but server itself will be also more responsive and be able to handle other process quickier</li>
                  <li><b>Improve SEO:</b> Search engines are now also considering the speed of the website in their ranking, it's important to have fast response time</li>
                  <li><b>Optimizations:</b> Various opencart optimizations to improve overall performance of your website</li>
                </ul>
                </p>
              </div>
            </div>
            
    <div class="tab-pane" id="tab-about">
      <div class="row">
        <div class="col-md-4">
          <h3><i class="fa fa-info-circle"></i> Extension details</h3>
          <div class="table-responsive">
            <table class="table"><tbody>
              <tr>
                <td>Module</td>
                <td><?php echo strip_tags($heading_title); ?></td>
              </tr>
              <tr>
                <td>Version</td>
                <td><?php echo $module_version; ?> - <?php echo $module_type; ?></td>
              </tr>
            <?php if (!empty($license_info)) { ?>
              <tr>
                <td colspan="2" style="padding:0;">
                  <h3 style="padding-top:40px;margin-bottom:10px;"><i class="fa fa-check-square"></i> Your License</h3>
                </td>
              </tr>
              <tr>
                <td>License holder</td>
                <td><?php echo $license_info['email']; ?></td>
              </tr>
              <tr>
                <td>Registered domain</td>
                <td><?php echo $license_info['website']; ?></td>
              </tr>
              <tr>
                <td>Support expires on</td>
                <td><?php echo date('F d, Y', strtotime($license_info['support_date'])); ?></td>
              </tr>
              <tr>
                <td>Support status</td>
                <td style="vertical-align:middle">
                  <?php if (time() > strtotime($license_info['support_date'])) { ?>
                    <span class="label label-danger" style="font-size:12px">Expired</span>
                  <?php } else { ?>
                    <span class="label label-success" style="font-size:12px"><i class="fa fa-check"></i> Valid</span>
                  <?php } ?>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="padding:20px 0;">
                <?php if (time() > strtotime($license_info['support_date'])) { ?>
                  <a target="_blank" href="http://geekodev.com/renewal?module=<?php echo $module; ?>&license=<?php echo $license_info['license']; ?>" class="btn btn-success btn-block" style="font-size:14px;"><i class="fa fa-refresh"></i> Renew my license</a>
                  <?php } else { ?>
                  <a target="_blank" href="http://geekodev.com/login" class="btn btn-grey btn-block" style="font-size:14px;"><i class="fa fa-cog"></i> Manage my license</a>
                <?php } ?>
                </td>
              </tr>
            <?php } else { ?>
              <tr><td colspan="2"></td></tr>
            <?php } ?>
            </tbody></table>
          </div>
        </div>
        <div class="col-md-8">
          <h3><i class="fa fa-external-link"></i> Connect with us</h3>
          <div class="row">
            <div class="col-md-12">
              <div class="well text-center">
                <div style="height:80px"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAA6/NlyAAAACXBIWXMAAAsTAAALEwEAmpwYAAA5z2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS41LWMwMTQgNzkuMTUxNDgxLCAyMDEzLzAzLzEzLTEyOjA5OjE1ICAgICAgICAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgICAgICAgICB4bWxuczpzdEV2dD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlRXZlbnQjIgogICAgICAgICAgICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iCiAgICAgICAgICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgICAgICAgICAgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZmLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOmV4aWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vZXhpZi8xLjAvIj4KICAgICAgICAgPHhtcDpDcmVhdG9yVG9vbD5BZG9iZSBQaG90b3Nob3AgQ0MgKFdpbmRvd3MpPC94bXA6Q3JlYXRvclRvb2w+CiAgICAgICAgIDx4bXA6Q3JlYXRlRGF0ZT4yMDE2LTA2LTEzVDE4OjMyOjA2KzAyOjAwPC94bXA6Q3JlYXRlRGF0ZT4KICAgICAgICAgPHhtcDpNZXRhZGF0YURhdGU+MjAxNi0wNi0xM1QxODozMjowNiswMjowMDwveG1wOk1ldGFkYXRhRGF0ZT4KICAgICAgICAgPHhtcDpNb2RpZnlEYXRlPjIwMTYtMDYtMTNUMTg6MzI6MDYrMDI6MDA8L3htcDpNb2RpZnlEYXRlPgogICAgICAgICA8eG1wTU06SW5zdGFuY2VJRD54bXAuaWlkOjRlN2U2MGY0LTdiYzYtMDY0Ni04ZTJhLThhNWRhNWRlMjEyYjwveG1wTU06SW5zdGFuY2VJRD4KICAgICAgICAgPHhtcE1NOkRvY3VtZW50SUQ+eG1wLmRpZDowMjgzNzFmNC00MzJlLTY5NDctYjk4ZS1hZGJmNTBiZWViMDk8L3htcE1NOkRvY3VtZW50SUQ+CiAgICAgICAgIDx4bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ+eG1wLmRpZDowMjgzNzFmNC00MzJlLTY5NDctYjk4ZS1hZGJmNTBiZWViMDk8L3htcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD4KICAgICAgICAgPHhtcE1NOkhpc3Rvcnk+CiAgICAgICAgICAgIDxyZGY6U2VxPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5jcmVhdGVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6MDI4MzcxZjQtNDMyZS02OTQ3LWI5OGUtYWRiZjUwYmVlYjA5PC9zdEV2dDppbnN0YW5jZUlEPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6d2hlbj4yMDE2LTA2LTEzVDE4OjMyOjA2KzAyOjAwPC9zdEV2dDp3aGVuPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBQaG90b3Nob3AgQ0MgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDo0ZTdlNjBmNC03YmM2LTA2NDYtOGUyYS04YTVkYTVkZTIxMmI8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMTYtMDYtMTNUMTg6MzI6MDYrMDI6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpTZXE+CiAgICAgICAgIDwveG1wTU06SGlzdG9yeT4KICAgICAgICAgPGRjOmZvcm1hdD5pbWFnZS9wbmc8L2RjOmZvcm1hdD4KICAgICAgICAgPHBob3Rvc2hvcDpDb2xvck1vZGU+MzwvcGhvdG9zaG9wOkNvbG9yTW9kZT4KICAgICAgICAgPHRpZmY6T3JpZW50YXRpb24+MTwvdGlmZjpPcmllbnRhdGlvbj4KICAgICAgICAgPHRpZmY6WFJlc29sdXRpb24+NzIwMDAwLzEwMDAwPC90aWZmOlhSZXNvbHV0aW9uPgogICAgICAgICA8dGlmZjpZUmVzb2x1dGlvbj43MjAwMDAvMTAwMDA8L3RpZmY6WVJlc29sdXRpb24+CiAgICAgICAgIDx0aWZmOlJlc29sdXRpb25Vbml0PjI8L3RpZmY6UmVzb2x1dGlvblVuaXQ+CiAgICAgICAgIDxleGlmOkNvbG9yU3BhY2U+NjU1MzU8L2V4aWY6Q29sb3JTcGFjZT4KICAgICAgICAgPGV4aWY6UGl4ZWxYRGltZW5zaW9uPjYwPC9leGlmOlBpeGVsWERpbWVuc2lvbj4KICAgICAgICAgPGV4aWY6UGl4ZWxZRGltZW5zaW9uPjYwPC9leGlmOlBpeGVsWURpbWVuc2lvbj4KICAgICAgPC9yZGY6RGVzY3JpcHRpb24+CiAgIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgCjw/eHBhY2tldCBlbmQ9InciPz75HNQxAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAAeeSURBVHja5Jt7cBXVHcc/NyEIhA1gJO2oPAQhsUAplFbrM8cOKBWoLzoWaEdbbXOmlbZOp04ftrVlSuuMWgZ1o44tVTRTUSOotRlHFrFIW0FrgEKFxIRHjGBQcqCBhJj+sb9LN8t97I13N+v4/Yfd88r97u9xfr/fOSR6enpIIpFI0F+wHasA+DKwADgPOANIAAeAfwF/BdZoZXblunYvjnEgbDvWSGA18IUAwzcADwI1WpnOjyrhx4H5OU7bA9wJ2NmIx4qw7VjFwLvAoD4usRO4RSvzbBDCBfQ/5n8IsgATgGdsx3rYdqzh2Qb3q4RtxxoEbAHOztOSTcA8rcyW2EnYdqxC4P48kgUYC7xiO9aloUtYpHUuMA2oECIjgE6gWb5+I7AfKAVulO0nDHQAc7Qya/PutGzHOgu4GfgmUEJ8cBiYrpXZmRfCtmNNAX4FzIuJ80uFN4AZVZXtx5MNA/qourcBP+rL/HwikShk9KkzGVFcQUfnAXa31dHR9a53yFTg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" alt="" style="position:relative;top:6px;"/></div>
                <h3>Made by GeekoDev</h3>
                <p>Developed with love and skill.<br/>Check our other great extensions on our website.</p>
                <a href="https://geekodev.com" target="_blank" class="btn btn-lg btn-grey">Visit our website</a>
              </div>
            </div>
          
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="well text-center">
                <div style="height:80px"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD8AAABGCAYAAAB7ckzWAAAACXBIWXMAAAsTAAALEwEAmpwYAABCt2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS41LWMwMTQgNzkuMTUxNDgxLCAyMDEzLzAzLzEzLTEyOjA5OjE1ICAgICAgICAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIKICAgICAgICAgICAgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iCiAgICAgICAgICAgIHhtbG5zOnN0RXZ0PSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VFdmVudCMiCiAgICAgICAgICAgIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIgogICAgICAgICAgICB4bWxuczpwaG90b3Nob3A9Imh0dHA6Ly9ucy5hZG9iZS5jb20vcGhvdG9zaG9wLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOnRpZmY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vdGlmZi8xLjAvIgogICAgICAgICAgICB4bWxuczpleGlmPSJodHRwOi8vbnMuYWRvYmUuY29tL2V4aWYvMS4wLyI+CiAgICAgICAgIDx4bXA6Q3JlYXRvclRvb2w+QWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKTwveG1wOkNyZWF0b3JUb29sPgogICAgICAgICA8eG1wOkNyZWF0ZURhdGU+MjAxOC0xMi0xNFQxNDoyNjowMyswMTowMDwveG1wOkNyZWF0ZURhdGU+CiAgICAgICAgIDx4bXA6TWV0YWRhdGFEYXRlPjIwMTktMDUtMjFUMTE6NTA6MTMrMDI6MDA8L3htcDpNZXRhZGF0YURhdGU+CiAgICAgICAgIDx4bXA6TW9kaWZ5RGF0ZT4yMDE5LTA1LTIxVDExOjUwOjEzKzAyOjAwPC94bXA6TW9kaWZ5RGF0ZT4KICAgICAgICAgPGRjOmZvcm1hdD5pbWFnZS9wbmc8L2RjOmZvcm1hdD4KICAgICAgICAgPHhtcE1NOkluc3RhbmNlSUQ+eG1wLmlpZDphMmU2OTE0Yy01MGNkLTU5NDItYWE5OS1lNDhlYWM5YmU4MDU8L3htcE1NOkluc3RhbmNlSUQ+CiAgICAgICAgIDx4bXBNTTpEb2N1bWVudElEPmFkb2JlOmRvY2lkOnBob3Rvc2hvcDpmY2E3ZDJkYy0wNzNkLTRiNDgtOTc3Zi0zNzJiZWJiMzRmYjk8L3htcE1NOkRvY3VtZW50SUQ+CiAgICAgICAgIDx4bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ+eG1wLmRpZDpmZGE2ZjBiMC0xZWM4LTM5NDYtYjZiMy1lZjViNzgwNjNlZjk8L3htcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD4KICAgICAgICAgPHhtcE1NOkhpc3Rvcnk+CiAgICAgICAgICAgIDxyZGY6U2VxPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5jcmVhdGVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6ZmRhNmYwYjAtMWVjOC0zOTQ2LWI2YjMtZWY1Yjc4MDYzZWY5PC9zdEV2dDppbnN0YW5jZUlEPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6d2hlbj4yMDE4LTEyLTE0VDE0OjI2OjAzKzAxOjAwPC9zdEV2dDp3aGVuPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBQaG90b3Nob3AgQ0MgMjAxOSAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOjhmNDYyYTFiLTljZWYtNDI0Zi1iMGQ2LTE1OTZhYTJiMGZjYjwvc3RFdnQ6aW5zdGFuY2VJRD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OndoZW4+MjAxOC0xMi0xNFQxNToxOTozOSswMTowMDwvc3RFdnQ6d2hlbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgUGhvdG9zaG9wIENDIDIwMTkgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDo4NDY1OTc0ZS1iODJmLWZjNGEtODMyNy01Y2Q1YjQ3ODM1MjY8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMTgtMTItMTRUMTU6MjE6NDYrMDE6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE5IChXaW5kb3dzKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPmNvbnZlcnRlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6cGFyYW1ldGVycz5mcm9tIGFwcGxpY2F0aW9uL3ZuZC5hZG9iZS5waG90b3Nob3AgdG8gaW1hZ2UvcG5nPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+ZGVyaXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6cGFyYW1ldGVycz5jb252ZXJ0ZWQgZnJvbSBhcHBsaWNhdGlvbi92bmQuYWRvYmUucGhvdG9zaG9wIHRvIGltYWdlL3BuZzwvc3RFdnQ6cGFyYW1ldGVycz4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6ZjU3NWU5YTYtOGM3YS1iYjQwLWE5ZGUtYThjMjIzODQ4NTU3PC9zdEV2dDppbnN0YW5jZUlEPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6d2hlbj4yMDE4LTEyLTE0VDE1OjIxOjQ2KzAxOjAwPC9zdEV2dDp3aGVuPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBQaG90b3Nob3AgQ0MgMjAxOSAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOmEyZTY5MTRjLTUwY2QtNTk0Mi1hYTk5LWU0OGVhYzliZTgwNTwvc3RFdnQ6aW5zdGFuY2VJRD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OndoZW4+MjAxOS0wNS0yMVQxMTo1MDoxMyswMjowMDwvc3RFdnQ6d2hlbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgIDwvcmRmOlNlcT4KICAgICAgICAgPC94bXBNTTpIaXN0b3J5PgogICAgICAgICA8eG1wTU06RGVyaXZlZEZyb20gcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICA8c3RSZWY6aW5zdGFuY2VJRD54bXAuaWlkOjg0NjU5NzRlLWI4MmYtZmM0YS04MzI3LTVjZDViNDc4MzUyNjwvc3RSZWY6aW5zdGFuY2VJRD4KICAgICAgICAgICAgPHN0UmVmOmRvY3VtZW50SUQ+eG1wLmRpZDpmZGE2ZjBiMC0xZWM4LTM5NDYtYjZiMy1lZjViNzgwNjNlZjk8L3N0UmVmOmRvY3VtZW50SUQ+CiAgICAgICAgICAgIDxzdFJlZjpvcmlnaW5hbERvY3VtZW50SUQ+eG1wLmRpZDpmZGE2ZjBiMC0xZWM4LTM5NDYtYjZiMy1lZjViNzgwNjNlZjk8L3N0UmVmOm9yaWdpbmFsRG9jdW1lbnRJRD4KICAgICAgICAgPC94bXBNTTpEZXJpdmVkRnJvbT4KICAgICAgICAgPHBob3Rvc2hvcDpDb2xvck1vZGU+MzwvcGhvdG9zaG9wOkNvbG9yTW9kZT4KICAgICAgICAgPHRpZmY6T3JpZW50YXRpb24+MTwvdGlmZjpPcmllbnRhdGlvbj4KICAgICAgICAgPHRpZmY6WFJlc29sdXRpb24+NzIwMDAwLzEwMDAwPC90aWZmOlhSZXNvbHV0aW9uPgogICAgICAgICA8dGlmZjpZUmVzb2x1dGlvbj43MjAwMDAvMTAwMDA8L3RpZmY6WVJlc29sdXRpb24+CiAgICAgICAgIDx0aWZmOlJlc29sdXRpb25Vbml0PjI8L3RpZmY6UmVzb2x1dGlvblVuaXQ+CiAgICAgICAgIDxleGlmOkNvbG9yU3BhY2U+NjU1MzU8L2V4aWY6Q29sb3JTcGFjZT4KICAgICAgICAgPGV4aWY6UGl4ZWxYRGltZW5zaW9uPjYzPC9leGlmOlBpeGVsWERpbWVuc2lvbj4KICAgICAgICAgPGV4aWY6UGl4ZWxZRGltZW5zaW9uPjcwPC9leGlmOlBpeGVsWURpbWVuc2lvbj4KICAgICAgPC9yZGY6RGVzY3JpcHRpb24+CiAgIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAg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" alt=""/></div>
                <h3>Get support</h3>
                <p>You have an issue or need help about module usage?<br/>You have some idea or feature request?</p>
                <a href="https://support.geekodev.com" target="_blank" class="btn btn-lg btn-grey">Open a ticket</a>
              </div>
            </div>
            <div class="col-md-6">
              <div class="well text-center">
                <div style="height:80px"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEYAAABLCAYAAADNsPFaAAAACXBIWXMAAAsTAAALEwEAmpwYAABERGlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS41LWMwMTQgNzkuMTUxNDgxLCAyMDEzLzAzLzEzLTEyOjA5OjE1ICAgICAgICAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIKICAgICAgICAgICAgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIgogICAgICAgICAgICB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIKICAgICAgICAgICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgICAgICAgICAgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiCiAgICAgICAgICAgIHhtbG5zOnRpZmY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vdGlmZi8xLjAvIgogICAgICAgICAgICB4bWxuczpleGlmPSJodHRwOi8vbnMuYWRvYmUuY29tL2V4aWYvMS4wLyI+CiAgICAgICAgIDx4bXA6Q3JlYXRvclRvb2w+QWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKTwveG1wOkNyZWF0b3JUb29sPgogICAgICAgICA8eG1wOkNyZWF0ZURhdGU+MjAxNi0xMi0yMVQyMTowNSswMTowMDwveG1wOkNyZWF0ZURhdGU+CiAgICAgICAgIDx4bXA6TW9kaWZ5RGF0ZT4yMDE5LTA1LTE4VDIxOjA2OjA4KzAyOjAwPC94bXA6TW9kaWZ5RGF0ZT4KICAgICAgICAgPHhtcDpNZXRhZGF0YURhdGU+MjAxOS0wNS0xOFQyMTowNjowOCswMjowMDwveG1wOk1ldGFkYXRhRGF0ZT4KICAgICAgICAgPGRjOmZvcm1hdD5pbWFnZS9wbmc8L2RjOmZvcm1hdD4KICAgICAgICAgPHBob3Rvc2hvcDpDb2xvck1vZGU+MzwvcGhvdG9zaG9wOkNvbG9yTW9kZT4KICAgICAgICAgPHBob3Rvc2hvcDpEb2N1bWVudEFuY2VzdG9ycz4KICAgICAgICAgICAgPHJkZjpCYWc+CiAgICAgICAgICAgICAgIDxyZGY6bGk+eG1wLmRpZDo2NDI2N2M4Mi0wN2JmLWYwNDMtOTQyMS0yMzk3NzliODZjNTU8L3JkZjpsaT4KICAgICAgICAgICAgPC9yZGY6QmFnPgogICAgICAgICA8L3Bob3Rvc2hvcDpEb2N1bWVudEFuY2VzdG9ycz4KICAgICAgICAgPHhtcE1NOkluc3RhbmNlSUQ+eG1wLmlpZDpiNmNjZWQzYS1hZDQxLTY4NDMtODhhOC0xNjQyZWY2ZTljMTE8L3htcE1NOkluc3RhbmNlSUQ+CiAgICAgICAgIDx4bXBNTTpEb2N1bWVudElEPnhtcC5kaWQ6NjQyNjdjODItMDdiZi1mMDQzLTk0MjEtMjM5Nzc5Yjg2YzU1PC94bXBNTTpEb2N1bWVudElEPgogICAgICAgICA8eG1wTU06T3JpZ2luYWxEb2N1bWVudElEPnhtcC5kaWQ6NjQyNjdjODItMDdiZi1mMDQzLTk0MjEtMjM5Nzc5Yjg2YzU1PC94bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ+CiAgICAgICAgIDx4bXBNTTpIaXN0b3J5PgogICAgICAgICAgICA8cmRmOlNlcT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+Y3JlYXRlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOjY0MjY3YzgyLTA3YmYtZjA0My05NDIxLTIzOTc3OWI4NmM1NTwvc3RFdnQ6aW5zdGFuY2VJRD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OndoZW4+MjAxNi0xMi0yMVQyMTowNSswMTowMDwvc3RFdnQ6d2hlbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPmNvbnZlcnRlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6cGFyYW1ldGVycz5mcm9tIGltYWdlL3BuZyB0byBhcHBsaWNhdGlvbi92bmQuYWRvYmUucGhvdG9zaG9wPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDo3YzgwNzlhZi04Zjc5LTkzNGQtYjkwZi05ZWI1NjYwZTk5YjA8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMTYtMTItMjJUMDk6NDg6NTgrMDE6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOmQ4YTVkOGQ2LTc4ODctNDk0OC1hZTkzLWU5ZTc4YjI0Yzk2ODwvc3RFdnQ6aW5zdGFuY2VJRD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OndoZW4+MjAxOS0wNC0yM1QxNTo0NDo0MiswMjowMDwvc3RFdnQ6d2hlbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPmNvbnZlcnRlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6cGFyYW1ldGVycz5mcm9tIGFwcGxpY2F0aW9uL3ZuZC5hZG9iZS5waG90b3Nob3AgdG8gaW1hZ2UvcG5nPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+ZGVyaXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6cGFyYW1ldGVycz5jb252ZXJ0ZWQgZnJvbSBhcHBsaWNhdGlvbi92bmQuYWRvYmUucGhvdG9zaG9wIHRvIGltYWdlL3BuZzwvc3RFdnQ6cGFyYW1ldGVycz4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6ZjFhMTdkOTUtODQ1NC1lNTQwLThmMTItMDY4ZDBkNTE5NWRiPC9zdEV2dDppbnN0YW5jZUlEPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6d2hlbj4yMDE5LTA0LTIzVDE1OjQ0OjQyKzAyOjAwPC9zdEV2dDp3aGVuPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBQaG90b3Nob3AgQ0MgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDpiNmNjZWQzYS1hZDQxLTY4NDMtODhhOC0xNjQyZWY2ZTljMTE8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMTktMDUtMThUMjE6MDY6MDgrMDI6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpTZXE+CiAgICAgICAgIDwveG1wTU06SGlzdG9yeT4KICAgICAgICAgPHhtcE1NOkRlcml2ZWRGcm9tIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgPHN0UmVmOmluc3RhbmNlSUQ+eG1wLmlpZDpkOGE1ZDhkNi03ODg3LTQ5NDgtYWU5My1lOWU3OGIyNGM5Njg8L3N0UmVmOmluc3RhbmNlSUQ+CiAgICAgICAgICAgIDxzdFJlZjpkb2N1bWVudElEPnhtcC5kaWQ6NjQyNjdjODItMDdiZi1mMDQzLTk0MjEtMjM5Nzc5Yjg2YzU1PC9zdFJlZjpkb2N1bWVudElEPgogICAgICAgICAgICA8c3RSZWY6b3JpZ2luYWxEb2N1bWVudElEPnhtcC5kaWQ6NjQyNjdjODItMDdiZi1mMDQzLTk0MjEtMjM5Nzc5Yjg2YzU1PC9zdFJlZjpvcmlnaW5hbERvY3VtZW50SUQ+CiAgICAgICAgIDwveG1wTU06RGVyaXZlZEZyb20+CiAgICAgICAgIDx0aWZmOk9yaWVudGF0aW9uPjE8L3RpZmY6T3JpZW50YXRpb24+CiAgICAgICAgIDx0aWZmOlhSZXNvbHV0aW9uPjcyMDAwMC8xMDAwMDwvdGlmZjpYUmVzb2x1dGlvbj4KICAgICAgICAgPHRpZmY6WVJlc29sdXRpb24+NzIwMDAwLzEwMDAwPC90aWZmOllSZXNvbHV0aW9uPgogICAgICAgICA8dGlmZjpSZXNvbHV0aW9uVW5pdD4yPC90aWZmOlJlc29sdXRpb25Vbml0PgogICAgICAgICA8ZXhpZjpDb2xvclNwYWNlPjY1NTM1PC9leGlmOkNvbG9yU3BhY2U+CiAgICAgICAgIDxleGlmOlBpeGVsWERpbWVuc2lvbj43MDwvZXhpZjpQaXhlbFhEaW1lbnNpb24+CiAgICAgICAgIDxleGlmOlBpeGVsWURpbWVuc2lvbj43NTwvZXhpZjpQaXhlbFlEaW1lbnNpb24+CiAgICAgIDwvcmRmOkRlc2NyaXB0aW9uPgogICA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgIAo8P3hwYWNrZXQgZW5kPSJ3Ij8+F5l64gAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAAF5ElEQVR42uyba4hUVRzAf/fOax/q7K6PXR8rhj0RMbUtUyos7YMEmkUiERTRh0KLIgqiKCEhEIkkI0wi6ENE0AvrQ7uiBj7SzKSXEkGpWGrqXDVdZ2fu6cP5j3ud5525O87seP5wmLlnzj337O/+z/9xzllLKYWRXLENAgPGgDFgDBgDxoAxYAyYxpLw7B3OWOCEQZGrMeuBGw2KXDAK+A242eC4HMzDwDlgH/CSQTIIJgXMEs1ZDRwEVgLtxivB70APcB64HlgngNYDdwPW1QbGmrU94b2+CdgMjM9qdxjoA7YD3wKHgItXExiATmAjcF+R+44C3wF/imb9Ii7/BHCqUcFk5FngRQHlV04KnGMyLR3gtHyeAdJ5pqWSKd0BhAHX57NCwAXp3xuoWtLHKflMe17WWRnLSak7VwkYgFHAU8CTwOQGmin9npd2QDzyfmCTgCwJxvt2lgDLgLuAcQ1qWg4Ba4C3/YLxykhgHjBfPNm0BgS1wpq5PRHUF7cD14qbnw50A1OkjBO7MdzkYDhkQdrVVsrKsogWYJemdhrYI8UrUaBNAHWLvWoWjWuRz3QZg1UCud2TyijPy4lIfZdcj5TnV7KCkAiHbAjbELUHn5Jh0Z+CpAuWVVGElwSOS9ldg7ceB8aKNs8AHgRu8XnvBqtnV4KoDSNCg2AycBRwZgAupHxrT73LcuBd0d5C8j3QEwZQCtIqFwxAaxgiFpxP6TbDHM5H4qY3FUhz/gYWUWr+KcBVEA1BPAaxEKSUrhvGsq1A/TFgrgSouR6jb3a8YI8L9jpEbG17BkR7hqECrcijLfuBxcBf2dl1SSiZ35tCMDIKLWKT0sNLe+YBb2TVbQHmeKEAWD27Euy+LV72ExbudUi6gT3XlZRpwF4g5qn7FHgg73pMJVDI2B4b4lFojQzapDqVbmBrFpT3C0EBsIKcj5m728FCa0syDSm3LrWmDfgBuMZTtwZ4oeiyQ9CDQ7N3Olr1hIhSutSJRCUume6pex5YW+rGwHlMdlxjWbrUCaDeLCiPAR/4ubFqCZ7l0aAayefAnZ7rJcAXfm8ODCbtXh4Q5oNTA7uzUeISJNJdBOwop4PAYNwsILYXRG20ZTXwuHw/AixEr9KVp/FBjO/MHc6lv92mLjzSSvTWD8DPwL2S/5RvO4OMIiVUQvUBZbkHyhbg1kqhBNKYqdscwrJcYdU+uJsD7JTvnwAPBfa2QbSl2YaQVXMoY9CbgADvDAWUiozv5K0OKRdawhCzB6dTjcRGrw5GgFeA14eq47LBDLg6NmkN1UVuZANvicbsG9I4rLMvwT/3lE4kOzc7WBZcTEM8Al0xvSbTqGKnFIzpc4pP4j4HV0F/GsIWdET976MOVwlnXvpoD5yTC+K09TqWpd2wssTYphSMb9G25aLb2GdD8tqYjl4HF1TI44HSCiY1Q1tEFqYGmz8CLBXv0A98BbwH/BdwbLdL0neDBNh7ZA3lwJUAY3X0JnIqldI7YbaE9SkFE5v1FEq6l4zuGPQK2B15+v0DuB/4qcJxrQWeK/DbM55ArqpWvagkXehqgtExPX089ravABSAqRJwdVUwpleLQEG80NKagbGApNIeqCOmV+i4fPrMKNF3K/BymeMZD7zmo906qnx4u2DnaaU9UGeThpS1ybzMZ/+LJJXyK4t9tpsouVD1wXh3yJVMoc4YNIdzjC1lTJGx6A18vzKpjLbdVfdKrhjczBmtlEBpj+gplMctH/PZ/7/oI2d+5UgZbQ9XVWPSCkZHYUqL9jyTmmFKq55CbuGs+WOf/X9NeUc9vvTZ7ihVPkFhK3TANiKs859MUcX3qD8EfizR91lgVZnjOerT+K6gysG3DRpAKquUSIMU+mB0oXXUQ+iF6OMVjGkV8GaR5z4NfFb1AG/UNwkmNGm3PFDZO3gCfSZ4gtiULegzKGcCjm0+8ChwHTAg2fMG4NcrEvma/9SvMPI1YIwYMAaMAWPAGDAGjAFjwBgwBowBY8SAMWAMmCGV/wcAW96kU8ix6wIAAAAASUVORK5CYII=" alt=""/></div>
                <h3>Feedback</h3>
                <p>If you like this module, please consider to leave a comment on module page, or <a href="https://www.opencart.com/index.php?route=account/rating" target="_blank">a rating</a> from your account.</p>
                <a href="https://www.opencart.com/index.php?route=extension/extension/info&extension_id=<?php echo $OCID; ?>" target="_blank" class="btn btn-lg btn-grey">OC module page</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <style type="text/css">
        #tab-about .well p{font-size:13px; min-height:55px}
        #tab-about table td{font-size:13px; padding: 13px 5px;}
        #tab-about table tr td:first-child{font-weight:bold;}
        #tab-about .btn-grey{background: #818181; color: white; border-color: #6b6b6b;}
        #tab-about .btn-grey:hover{background: #646464; color: white; border-color: #727272;}
      </style>
		</div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$('body').on('click', '.gkdwidget .icon', function(){
  $(this).closest('.gkdwidget').find('.gkdwidget-container').slideUp(function(){
    $(this).closest('.gkdwidget').find('.active').removeClass('active');
  });
});

$('body').on('click', '.gkdwidget a[data-toggle="tab"]', function(){
  $(this).closest('.gkdwidget').find('.gkdwidget-container').fadeIn();
  $(this).closest('.gkdwidget').find('header').addClass('active');
});

$('body').tooltip({selector: '[data-toggle=tooltip]'});
/*
$('.toggler').toggler();
$('input.switch').iToggle({easing:'swing', speed:200});
$.each( jQuery('input.checkable'), function(){ jQuery(this).prettyCheckable(); });
*/

$('body').on('click', '.add-extra-page', function (e) {
  var element = $('.extraPage');
  var extra = element.clone();
  extra.html(function(i, oldHTML) {
    return oldHTML.replaceAll('_extra_', 'extra'+$('.extraTable > tr').length);
  });
  extra.find('[disabled]').removeAttr('disabled');
  extra.removeAttr('class');
  extra.insertBefore(element);
  extra.slideDown();
});

$('body').on('click', '.add-cdn', function (e) {
  var element = $('.extraCdn');
  var extra = element.clone();
  extra.find('[disabled]').removeAttr('disabled');
  extra.removeAttr('class');
  extra.insertBefore(element);
  extra.slideDown();
});

$('body').on('change', '.modStatus', function (e) {
  if ($(this).prop('checked')) {
    $('.showStatus').slideUp('fast');
  } else {
    $('.showStatus').slideDown('fast');
  }
});

$('body').on('change', '.cdnMode', function (e) {
  if ($(this).val()) {
    $('.custCdnShow').slideDown('fast');
  } else {
    $('.custCdnShow').slideUp('fast');
  }
});

$('body').on('click', '.remove-extra-page', function (e) {
  $(this).parent().parent().remove();
  $('.tooltip').remove();
});

$('body').on('change', '.pageSwitch', function (e) {
  if ($(this).prop('checked')) {
    $(this).parent().parent().parent().find('button').not('.remove-extra-page').fadeIn('fast');
  } else {
    $(this).parent().parent().parent().find('button').not('.remove-extra-page').fadeOut('fast');
  }
});

$('body').on('change', '.missingOrdersDelay', function (e) {
  $('.missingOrdersCount').load('index.php?route=extension/module/rocket_boost/count_missing_orders&<?php echo $token; ?>', $("#tab-opt").find('select, textarea, input').serialize());
});

$('.missingOrdersDelay').trigger('change');

$('body').on('click', '.clearCache', function (e) {
  $('button').attr('disabled', true);
  var currentBtn = $(this);
  var currentHtml = currentBtn.html();
  currentBtn.html('<i class="fa fa-cog fa-spin"></i>');
  $('.tooltip').remove();
  
   $.ajax({
		url: 'index.php?route=extension/module/rocket_boost/clear_cache&page='+currentBtn.data('page')+'&<?php echo $token; ?>',
    type: 'POST',
		dataType: 'json',
		success: function(data){
      if (!currentBtn.data('page')) {
        currentBtn.html('<i class="fa fa-check"></i> Cache cleared');
      } else {
        currentBtn.html('<i class="fa fa-check"></i>');
      }
      $('button').attr('disabled', false);
		},
    error: function(xhr) {
      alert(xhr.responseText);
      currentBtn.html(currentHtml);
      $('button').attr('disabled', false);
    }
	});
});

$('body').on('click', '.runOptimization', function (e) {
  $('.optResult').html('<p class="text-center"><img src="<?php echo $_img_path; ?>loader.gif" alt="Loading..."/></p>')
  $('.optResult').slideDown();
  $('button').attr('disabled', true);
  var currentBtn = $(this);
  var currentHtml = currentBtn.html();
  currentBtn.html('<i class="fa fa-cog fa-spin"></i>');
  $('.tooltip').remove();
  
   $.ajax({
		url: 'index.php?route=extension/module/rocket_boost/optimization&type='+currentBtn.data('type')+'&check='+currentBtn.data('check')+'&<?php echo $token; ?>',
    type: 'POST',
    data: $("#tab-opt").find('select, textarea, input').serialize(),
		dataType: 'html',
		success: function(data){
      $('.optResult').html(data);
      currentBtn.html('<i class="fa fa-check"></i>');
      $('button').attr('disabled', false);
		},
    error: function(xhr) {
      alert(xhr.responseText);
      currentBtn.html(currentHtml);
      $('button').attr('disabled', false);
    }
	});
});

var pauseProcess = 1;
var currentProgress = 0;
var processBtns = [];
var currentBtn = '';
var currentHtml = '';
  
function processQueue(start, type) {
  $('.tooltip').remove();
  currentBtn = processBtns[type];
  $.ajax({
		url: 'index.php?route=extension/module/rocket_boost/process&start='+start+'&type='+type+'&<?php echo $token; ?>',
    type: 'POST',
		//data: $('#update-form :input').serialize(),
		dataType: 'json',
		success: function(data){
      if(data.success) {
        $('.rbProgressBar').css('width',data.progress + '%').html(data.progress + ' %');
        $('.processProgress').html(data.progress + ' %');
        if (!pauseProcess && !data.finished) {
          processQueue(data.processed, type);
        } else {
          currentProgress = data.processed;
          $('.rbProgressBar').removeClass('active');
          currentBtn.removeClass('pauseProcess').addClass('startProcess');
          $('button').attr('disabled', false);
          
          if (data.finished) {
            currentBtn.html('<i class="fa fa-check"></i>');
            $('button').attr('disabled', false);
          }
        }
      }
		},
    error: function(xhr) {
      $('.rbProgressBar').css('width','100%').html('100 %');
      $('.rbProgressBar').removeClass('active');
      
      $('button').attr('disabled', false);
      currentBtn.html('<i class="fa fa-exclamation-triangle"></i>');
      currentBtn.removeClass('btn-success').addClass('btn-danger');
      currentBtn.attr('disabled', true);
      alert(xhr.responseText);
    }
	});
}

var first_run = true;
  
$('body').on('click', '.startProcess', function() {
  $('.tooltip').remove();
  $('button').attr('disabled', true);
  
  $('.fa-pause').parent().removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-refresh"></i>');
  
  currentBtn = processBtns[$(this).data('page')] = $(this);
  
  currentHtml = currentBtn.html();
  
  if (currentBtn.html().indexOf('fa-pause') < 0) {
    currentBtn.html('<i class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;<span class="processProgress">0 %</span>');
  } else {
    currentBtn.html(currentBtn.html().replace('fa-pause', 'fa-refresh fa-spin'));
  }
  currentBtn.removeClass('btn-primary').addClass('btn-success');
  currentBtn.attr('disabled', false);
  currentBtn.removeClass('startProcess').addClass('pauseProcess');
  $('.tooltip').remove();
  /*
  $('.startProcess').hide();
  $('.process-log').show();
  $('.pauseProcess').show();
  $('#translateProgress').show();
  $('#translateProgress .progress-bar').addClass('active');
  
  if (!currentProgress) {
    $('#translateProgress .progress-bar').css('min-width', '2em').html('0 %');
  }
  */
  
  pauseProcess = 0;
  if (!currentProgress) {
    processQueue('init', currentBtn.data('page'));
  } else {
    processQueue(currentProgress, currentBtn.data('page'));
  }
});

$('body').on('click', '.pauseProcess', function() {
  $('.tooltip').remove();
  pauseProcess = 1;
  currentBtn.html(currentBtn.html().replace('fa-refresh fa-spin', 'fa-pause'));
});

</script>
<?php echo $footer; ?>