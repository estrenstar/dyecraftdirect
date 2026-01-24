<?php
class ModelToolRocketBoost extends Model {
  
	public function getPageList($type, $count = false) {
    $list = array();
    $toProcess = array();
    
		if (empty($type)) {
      foreach ($this->config->get('boost_page') as $page => $data) {
        if (!empty($data['on'])) {
          $toProcess[] = $page;
        }
      }
    } else {
      $toProcess[] = $type;
    }
		//$this->db->query("INSERT INTO `" . DB_PREFIX . "upload` SET `name` = '" . $this->db->escape($name) . "', `filename` = '" . $this->db->escape($filename) . "', `code` = '" . $this->db->escape($code) . "', `date_added` = NOW()");

    foreach ($toProcess as $type) {
      if ($type == 'product/product') {
        $items = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE status = 1")->rows;
        
        foreach ($items as $item) {
          $list[] = HTTP_CATALOG.'index.php?route='.$type.'&product_id='.$item['product_id'];
        }
      } else if ($type == 'product/manufacturer') {
        $items = $this->db->query("SELECT manufacturer_id FROM `" . DB_PREFIX . "manufacturer`")->rows;
        
        $list[] = HTTP_CATALOG.'index.php?route='.$type;
        
        foreach ($items as $item) {
          $list[] = HTTP_CATALOG.'index.php?route='.$type.'/info&manufacturer_id='.$item['manufacturer_id'];
        }
      } else if ($type == 'information/information') {
        $items = $this->db->query("SELECT information_id FROM `" . DB_PREFIX . "information` WHERE status = 1")->rows;
        
        foreach ($items as $item) {
          $list[] = HTTP_CATALOG.'index.php?route='.$type.'&information_id='.$item['information_id'];
        }
      } else if ($type == 'product/category') {
        $items = $this->db->query("SELECT category_id, parent_id FROM `" . DB_PREFIX . "category` WHERE status = 1")->rows;
        
        foreach ($items as $item) {
          $path = $this->fullCategoryPath($item, true);
          $list[] = HTTP_CATALOG.'index.php?route='.$type.'&path='.$path;
        }
      } else if ($type == 'common/home') {
        $list[] = HTTP_CATALOG;
      } else {
        $list[] = HTTP_CATALOG.'index.php?route='.$type;
      }
    }
    
		return $list;
	}
 
  private function fullCategoryPath($category, $include_current = false) {
    if ($include_current) {
      $path = $category['category_id'];
    } else {
      $path = '';
    }
    
    while (!empty($category['parent_id'])) {
      $path = $category['parent_id'] . '_' . $path;
      $category = $this->db->query("SELECT parent_id FROM " . DB_PREFIX . "category WHERE category_id = '" . $category['parent_id']. "' AND status = 1")->row;
    }
    
    return rtrim($path, '_');
  }
  
  public function optimize_missing_orders($check = true, $count = false) {
    $log = array();
    
    if (!empty($this->request->post['boost_missing_orders_delay'])) {
      $delay = date('Y-m-d', strtotime('-'.$this->request->post['boost_missing_orders_delay']));
    } else {
      $delay = date('Y-m-d', strtotime('-1 day'));
    }
    
    $orders = $this->getOrders($delay);
    
    $totalDeleted = count($orders);
    
    if ($count) {
      return $totalDeleted;
    }
    
    foreach ($orders as $order) {
      $this->deleteOrder($order['order_id']);
    }
    
    if ($check) {
      $log[] = '<span class="label label-info"><i class="fa fa-fw fa-exclamation-circle"></i> Simulation</span> No data will be removed from database';
    }
    
    if ($totalDeleted) {
      $log[] = '<span class="label label-success"><i class="fa fa-fw fa-check"></i> Done</span> Deleted '.$totalDeleted.' missing orders.';
    } else {
      $log[] = '<span class="label label-default"><i class="fa fa-fw fa-check"></i> OK</span> No missing orders to delete.';
    }
    
    return($log);
  }
  
  public function getOrders($delay) {
		$sql = "SELECT o.order_id FROM `" . DB_PREFIX . "order` o";
    
		$sql .= " WHERE o.order_status_id = '0'";
    
		if (!empty($delay)) {
			$sql .= " AND DATE(o.date_added) < DATE('" . $this->db->escape($delay) . "')";
		}
    
		$query = $this->db->query($sql);

		return $query->rows;
	}
  
  public function deleteOrder($order_id) {
    $this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE `order_id` = '" . (int)$order_id . "'");
    $this->db->query("DELETE FROM `" . DB_PREFIX . "order_history` WHERE `order_id` = '" . (int)$order_id . "'");
    $this->db->query("DELETE FROM `" . DB_PREFIX . "order_option` WHERE `order_id` = '" . (int)$order_id . "'");
    $this->db->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int)$order_id . "'");
    if ($this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "order_shipment'")->row) {
      $this->db->query("DELETE FROM `" . DB_PREFIX . "order_shipment` WHERE `order_id` = '" . (int)$order_id . "'");
    }
    $this->db->query("DELETE FROM `" . DB_PREFIX . "order_total` WHERE `order_id` = '" . (int)$order_id . "'");
    $this->db->query("DELETE FROM `" . DB_PREFIX . "order_voucher` WHERE `order_id` = '" . (int)$order_id . "'");
  }
  
  public function optimize_sql_engine($check = true) {
    $log = array();
    $changed = false;
    
    $tables = $this->getTables();
    
    if (!empty($tables)) {
      foreach ($tables as $table) {
        if ($table['engine'] != 'InnoDB') {
          if ($check) {
            $log[] = '<span class="label label-warning"><i class="fa fa-fw fa-magic"></i> Optimize</span> Table '.$table['name'].' require convertion from ' . $table['engine'] . ' to InnoDB';
            $changed = true;
          } else {
            try {
              if ($this->db->query("ALTER TABLE `".$table['name']."` ENGINE = INNODB")) {
                $log[] = '<span class="label label-success"><i class="fa fa-fw fa-check"></i> OK</span> Convert table '.$table['name'].' from ' . $table['engine'] . ' to InnoDB';
                $changed = true;
              } else {
                $log[] = '<span class="label label-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> Error</span> Failed to convert table '.$table['name'].' from ' . $table['engine'] . ' to InnoDB - Make sure that current db user have rights to ALTER table';
              }
            } catch (Exception $e) {
              $log[] = '<span class="label label-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> Skipped</span> Not possible to convert table '.$table['name'].' - '.str_replace(array('<br>', '<br />', '<br/>'), ' - ', $e->getMessage());
            }
          }
        } else {
          $log[] = '<span class="label label-default"><i class="fa fa-fw fa-check"></i> OK</span> Table '.$table['name'].' is already in InnoDB';
        }
      }
    }
    
    if (!$changed) {
      if (!empty($log)) {
        $log[] = '<hr style="margin-bottom: 0; border: 1px dashed #ddd;"/>';
      }
      $log[] = '<span class="label label-success"><i class="fa fa-fw fa-check"></i> OK</span> Your database is already fully running on InnoDB';
    }
    
    return($log);
  }
  
  public function optimize_sql_index($check = true) {
    $commonIndexes = array(
      'status',
      'sort_order',
    );
    
    $extraIndexes = array(
      'product.model',
      'product.date_available',
      'product.subtract',
      'product.quantity',
      'seo_url.query',
      'seo_url.keyword',
      'setting.group',
      'setting.key',
    );
    
    $excludedTables = array(
      'oc_ebabandonedcart',
      'oc_ebabandonedcart_description',
      //'oc_journal3_product_attribute',
    );
    
    $log = array();
    $changed = false;
    
    $tables = $this->getTables(true);
    
    if (!empty($tables)) {
      foreach ($tables as $table) {
        if (in_array($table['name'], $excludedTables)) continue;
        
        foreach($table['columns'] as $column) {
          $indexExists = $addIndex = false;
          
          if (substr($column['name'], -3) == '_id' || in_array($column['name'], $commonIndexes) || in_array($table['name'].'.'.$column['name'], $extraIndexes)) {
            $addIndex = true;
          }
          
          if (!empty($column['index'])) {
            foreach ($column['index'] as $index) {
              if ($index['seq'] == 1) {
                $indexExists = true;
              }
            }
          }
          
          if (!$indexExists && $addIndex) {
            $changed = true;
            
            if ($check) {
              $log[] = '<span class="label label-warning"><i class="fa fa-fw fa-magic"></i> Optimize</span> Index required for '.$table['name'].'.'.$column['name'];
            } else {
              try{
                if ($this->db->query("ALTER TABLE `".$table['name']."` ADD INDEX (`".$column['name']."`)")) {
                  $log[] = '<span class="label label-success"><i class="fa fa-fw fa-check"></i> OK</span> Index added for '.$table['name'].'.'.$column['name'];
                } else {
                  $log[] = '<span class="label label-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> Error</span> Failed to add index for '.$table['name'].'.'.$column['name']. ' - Make sure that current db user have rights to ALTER table';
                }
              } catch (Exception $e) {
                $log[] = '<span class="label label-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> Error</span> Failed to add index for '.$table['name'].'.'.$column['name']. ' - '.$e->getMessage();
              }
            }
          } else if ($addIndex) {
            $log[] = '<span class="label label-default"><i class="fa fa-fw fa-check"></i> OK</span> Index already exists for '.$table['name'].'.'.$column['name'];
          }
        }
      }
    }
    
    if (!$changed) {
      if (!empty($log)) {
        $log[] = '<hr style="margin-bottom: 0; border: 1px dashed #ddd;"/>';
      }
      $log[] = '<span class="label label-success"><i class="fa fa-fw fa-check"></i> OK</span> Your indexes are correctly set up';
    }
    
    return($log);
  }
  
  private function getTables($fullData = false) {
    $limit = false;
    
    if (!$this->user->hasPermission('modify', 'extension/module/rocket_boost')) {
      $limit = 10;
    }
    
    $tables = array();
    
    $tableQuery = $this->db->query("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA LIKE '".DB_DATABASE."'")->rows;
    
    if (!empty($tableQuery)) {
      foreach ($tableQuery as $tableRow) {
        $tables[] = array(
          'name' => $tableRow['TABLE_NAME'],
          'engine' => $tableRow['ENGINE'],
        );
      }
    }
    
    if ($fullData) {
      foreach($tables as $i => &$table) {
        if ($limit && $i > $limit) continue;
        
        $indexQuery = $this->db->query("SELECT * FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA LIKE '".DB_DATABASE."' AND TABLE_NAME LIKE '".$table['name']."'")->rows;
        
        if (!empty($indexQuery)) {
          foreach ($indexQuery as $indexRow) {
            $table['index'][$indexRow['COLUMN_NAME']][] = array(
              'name' => $indexRow['COLUMN_NAME'],
              'key' => $indexRow['INDEX_NAME'],
              'seq' => $indexRow['SEQ_IN_INDEX'],
              'unique' => !empty($indexRow['NON_UNIQUE']) ? false : true,
            );
          }
        }
        
        $columnQuery = $this->db->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA LIKE '".DB_DATABASE."' AND TABLE_NAME LIKE '".$table['name']."'")->rows;
        
        if (!empty($columnQuery)) {
          foreach ($columnQuery as $columnRow) {
            $table['columns'][$columnRow['COLUMN_NAME']] = array(
              'name' => $columnRow['COLUMN_NAME'],
              'type' => $columnRow['DATA_TYPE'],
            );
            
            if (isset($table['index'][$columnRow['COLUMN_NAME']])) {
              $table['columns'][$columnRow['COLUMN_NAME']]['index'] = $table['index'][$columnRow['COLUMN_NAME']];
            }
          }
        }
      }
    }
    
    return $tables;
  }
}