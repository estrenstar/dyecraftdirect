<?php
use MatthiasMullie\Minify;
use voku\helper\HtmlMin;

class ModelToolBoost extends Model {

  public function dynamic_vars($output) {
    if (strpos($output, '[[[boostvar_username]]]') !== false) {
      $output = str_replace('[[[boostvar_username]]]', $this->customer->getFirstName(), $output);
    }
    
    if (strpos($output, '[[[boostvar_compare_count]]]') !== false) {
      $output = str_replace('[[[boostvar_compare_count]]]', isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0, $output);
    }
    
    if (strpos($output, '[[[boostvar_wishlist_count]]]') !== false || strpos($output, '[[[boostvar_opensource_wishlist]]]') !== false) {
      if (isset($this->session->data['wishlist'])) {
        $wishlistCount = count($this->session->data['wishlist']);
      } else if (!empty($this->session->data['customer_id'])){
        $wishlistCount = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "'")->row['total'];
      } else {
        $wishlistCount = 0;
      }
      
      if (strpos($output, '[[[boostvar_wishlist_count]]]') !== false) {
        $output = str_replace('[[[boostvar_wishlist_count]]]', $wishlistCount, $output);
      }
      
      if (strpos($output, '[[[boostvar_opensource_wishlist]]]') !== false) {
        $output = str_replace('[[[boostvar_opensource_wishlist]]]', ($wishlistCount ? '<style>.heart-icon::after{content:"'.$wishlistCount.'"; display:block;}</style>' : ''), $output);
      }
    }
    
    return $output;
  }
  
  public function optimize_html($output) {
    preg_match('~<base href="(.+?)"~', $output, $base);
    
    if (!empty($base[1])) {
      $base = $base[1];
      
      $output = preg_replace('~(<a .*?href=")'.$this->config->get('config_ssl').'(.+?)(".*?\/?>)~', '$1$2$3', $output);
      $output = preg_replace('~(<img .*?src=")'.$this->config->get('config_ssl').'(.+?)(".*?\/?>)~', '$1$2$3', $output);
    }
    
    $minifier = new HtmlMin();
    
    if (substr_count($output, '</body>') > 1) {
      $minifier->doOptimizeViaHtmlDomParser(false);
    }
    
    $output = $minifier->minify($output); 
    
    return $output;
  }
  
  public function optimize_js($output, $route = '') {
    $minifyInline = false;
    $html = $output;
    
    $html = str_replace('<script type="text/javascript"><!--', '#BOOST_JS_OPEN#', $html);
    $html = str_replace('--></script>', '#BOOST_JS_CLOSE#', $html);
    
    $html = preg_replace('/<!--(.|\s)*?-->/', '', $html);
    
    $html = str_replace('#BOOST_JS_OPEN#', '<script type="text/javascript"><!--', $html);
    $html = str_replace('#BOOST_JS_CLOSE#', '--></script>', $html);
    
    $rbDelayJs = '';
    
    preg_match_all('~<script.*?>(.*?)</script>~s', $html, $rbScripts);
    
    $cachedFile = str_replace('=', '', base64_encode($route));
    $cachedFileRelPathName = 'catalog/view/javascript/rb/'.$cachedFile.'#i#.js';
    $cachedFileFullPathName = DIR_APPLICATION.'view/javascript/rb/'.$cachedFile.'#i#.js';
    $cachedFileRelPath = 'catalog/view/javascript/rb/'.$cachedFile.'1.js';
    $cachedFileFullPath = DIR_APPLICATION.'view/javascript/rb/'.$cachedFile.'1.js';
    $currentIteration = 1;
    
    $cacheDelay = 1 * 24 * 3600; // 1 day
    
    $disabledLinks = array_filter(explode("\n", $this->config->get('boost_opt_off')));
    
    
    if (!empty($rbScripts[0])) {
      $output = $this->str_lreplace('</body>', '[[[boost_scripts]]]</body>', $output);
      
      foreach ($rbScripts[0] as $k => $rbScript) {
        if (!empty($rbScripts[1][$k])) {
          if (strpos($rbScript, 'ld+json')) continue;
          
          if (isset($minifier)) {
            if (!file_exists($cachedFileFullPath) || (time() - filemtime($cachedFileFullPath) > $cacheDelay)) {
              $minifier->minify($cachedFileFullPath);
            }
            
            unset($minifier);
            
            $rbDelayJs .= '<script src="'.$cachedFileRelPath.'"></script>';
            
            $currentIteration++;
            $cachedFileFullPath = str_replace('#i#', $currentIteration, $cachedFileFullPathName);
            $cachedFileRelPath = str_replace('#i#', $currentIteration, $cachedFileRelPathName);
          }
          
          if ($minifyInline) {
            $tempMinifier = new Minify\JS($rbScripts[1][$k]);
            
            $rbDelayJs .= '<script>'.$tempMinifier->minify().'</script>';
          } else {
            $rbDelayJs .= $rbScript;
          }
          
          $output = str_replace($rbScript, '', $output);
        } else if (preg_match('~src="(.+?)"~', $rbScript, $url)) {
          $script = $url[1];
          $isUrl = false;
          
          foreach ($disabledLinks as $rbScriptOff) {
            if (strpos($rbScript, trim($rbScriptOff)) !== false) continue 2;
          }
          
          if (false /*substr($script, 0, 2) == '//' || substr($script, 0, 6) == 'https:' || substr($script, 0, 6) == 'http:/'*/) {
            if (substr($script, 0, 2) == '//') {
              $url[1] = 'https:'.$script;
            }
            
            $script = file_get_contents($url[1]);
            $isUrl = true;
          } else if (substr($script, 0, 2) == '//' || substr($script, 0, 6) == 'https:' || substr($script, 0, 6) == 'http:/') {
            if (isset($minifier)) {
              if (!file_exists($cachedFileFullPath) || (time() - filemtime($cachedFileFullPath) > $cacheDelay)) {
                $minifier->minify($cachedFileFullPath);
              }
              
              unset($minifier);
              
              $rbDelayJs .= '<script src="'.$cachedFileRelPath.'"></script>';
              
              $currentIteration++;
              $cachedFileFullPath = str_replace('#i#', $currentIteration, $cachedFileFullPathName);
              $cachedFileRelPath = str_replace('#i#', $currentIteration, $cachedFileRelPathName);
            }
            
            $rbDelayJs .= $rbScript;
          
            $output = str_replace($rbScript, '', $output);
            
            continue;
          } else if (substr($script, 0, 8) == 'catalog/') {
            if (strstr($script, '?', true)) {
              $script = strstr($script, '?', true);
            }
            
            $script = DIR_APPLICATION . substr($script, 8);
          } else {
            if (strstr($script, '?', true)) {
              $script = strstr($script, '?', true);
            }
            
            if (file_exists(DIR_SYSTEM . '../' . $script)) {
              $script = DIR_SYSTEM . '../' . $script;
            }
          }
          
          if (!$isUrl && !file_exists($script)) continue;
          
          if (!isset($minifier)) {
            $minifier = new Minify\JS($script);
          } else {
            $minifier->add("\n\n/*!".$url[1]."*/\n\n");
            $minifier->add($script);
          }
          
          $output = str_replace($rbScript, '', $output);
        }
      }
      
      if (!is_dir(DIR_APPLICATION.'view/javascript/rb')) {
        mkdir(DIR_APPLICATION.'view/javascript/rb',  0777, true);
      }
      
      if (isset($minifier)) {
        if (!file_exists($cachedFileFullPath) || (time() - filemtime($cachedFileFullPath) > $cacheDelay)) {
          $minifier->minify($cachedFileFullPath);
        }
        
        $rbDelayJs .= '<script src="'.$cachedFileRelPath.'"></script>';
      }
      
      $output = $this->str_lreplace('</body>', $rbDelayJs.'</body>', $output);
    }
    
    return $output;
  }
  
  public function delay_js($output) {
    $minifyInline = false;
    
    $html = $output;
    
    if (strpos($html, '[[[boost_scripts]]]')) {
      $html = strstr($html, '[[[boost_scripts]]]', true);
      $output = str_replace('[[[boost_scripts]]]', '', $output);
    }
    
    $html = str_replace('<script type="text/javascript"><!--', '#BOOST_JS_OPEN#', $html);
    $html = str_replace('--></script>', '#BOOST_JS_CLOSE#', $html);
    
    $html = preg_replace('/<!--(.|\s)*?-->/', '', $html);
    
    $html = str_replace('#BOOST_JS_OPEN#', '<script type="text/javascript"><!--', $html);
    $html = str_replace('#BOOST_JS_CLOSE#', '--></script>', $html);
    
    preg_match_all('~<script.*?>(.*?)</script>~s', $html, $rbScripts);
    
    if (!empty($rbScripts[0])) {
      $rbDelayJs = '';
      $rbDelayJsOff = array_filter(explode("\n", $this->config->get('boost_opt_off')));
      foreach ($rbScripts[0] as $k => $rbScript) {
        if (strpos($rbScript, 'ld+json')) continue;
        
        foreach ($rbDelayJsOff as $rbScriptOff) {
          if (strpos($rbScript, trim($rbScriptOff)) !== false) continue 2;
        }
        
        $output = str_replace($rbScript, '', $output);
        
        if (!empty($rbScripts[1][$k])) {
          if ($minifyInline) {
            $tempMinifier = new Minify\JS($rbScripts[1][$k]);
            $rbDelayJs .= '<script>'.$tempMinifier->minify().'</script>';
          } else {
            $rbDelayJs .= '<script>'.$rbScripts[1][$k].'</script>';
          }
        } else {
          $rbDelayJs .= $rbScript;
        }
      }
      
      $output = $this->str_lreplace('</body>', $rbDelayJs.'</body>', $output);
    }
    
    return $output;
  }
  
  public function optimize_css($output, $route = '') {
    $inlineMode = false;
    
    $html = preg_replace('/<!--(.|\s)*?-->/', '', $output);
    
    preg_match_all('~<link .*?href="(.+?)".*?\/?>~i', $html, $rbScripts);
    
    $cachedFile = str_replace('=', '', base64_encode($route));
    $cachedFileRelPath = 'catalog/view/javascript/rb/'.$cachedFile.'.css';
    $cachedFileFullPath = DIR_APPLICATION.'view/javascript/rb/'.$cachedFile.'.css';
    
    $cacheDelay = 1 * 24 * 3600; // 1 day
    
    $disabledLinks = array_filter(explode("\n", $this->config->get('boost_opt_off')));
    
    if (!empty($rbScripts[0])) {
      $toRemove = [];
      
      if (!$inlineMode && file_exists($cachedFileFullPath) && (time() - filemtime($cachedFileFullPath) < $cacheDelay)) {
        foreach ($rbScripts[0] as $rbScript) {
          if (strpos($rbScript, ' rel="stylesheet"') && preg_match('~href="(.+?)"~', $rbScript, $url)) {
            foreach ($disabledLinks as $rbScriptOff) {
              if (strpos($rbScript, trim($rbScriptOff)) !== false) continue 2;
            }
            
            $toRemove[] = $rbScript;
          }
        }
        
        $lastScript = array_pop($toRemove);
        
        foreach ($toRemove as $rem) {
          $output = str_replace($rem, '', $output);
        }
        
        // $output = str_replace($lastScript, '<link href="'.$cachedFileRelPath.'" rel="stylesheet">', $output);
        $output = str_replace('<base href', "\n".'<link href="'.$cachedFileRelPath.'" rel="preload" as="style" id="rbpreload"><script>document.getElementById(\'rbpreload\').rel=\'stylesheet\'</script><base href', $output);
      } else {
        foreach ($rbScripts[0] as $rbScript) {
          if (strpos($rbScript, ' rel="stylesheet"') && preg_match('~href="(.+?)"~', $rbScript, $url)) {
            $script = $url[1];
            $isUrl = false;
            
            foreach ($disabledLinks as $rbScriptOff) {
              if (strpos($rbScript, trim($rbScriptOff)) !== false) continue 2;
            }
            
            if (substr($script, 0, 2) == '//' || substr($script, 0, 6) == 'https:' || substr($script, 0, 6) == 'http:/') {
              if (substr($script, 0, 2) == '//') {
                $url[1] = 'https:'.$script;
              }
              
              $script = file_get_contents($url[1]);
              $isUrl = true;
            } else if (substr($script, 0, 8) == 'catalog/') {
              $script = DIR_APPLICATION . substr($script, 8);
            }
            
            if (!$isUrl && !file_exists($script)) continue;
            
            if (!isset($minifier)) {
              $minifier = new Minify\CSS($script);
            } else {
              $minifier->add("\n\n/*!".$url[1]."*/\n\n");
              $minifier->add($script);
            }
            
            //$output = str_replace($rbScript, '', $output);
            $toRemove[] = $rbScript;
          }
        }
        
        if (!is_dir(DIR_APPLICATION.'view/javascript/rb')) {
          mkdir(DIR_APPLICATION.'view/javascript/rb',  0777, true);
        }
        
        // if (!is_dir(DIR_IMAGE.'cache/boost')) {
          // mkdir(DIR_IMAGE.'cache/boost',  0777, true);
        // }
        
        if (isset($minifier)) {
          $lastScript = array_pop($toRemove);
          
          foreach ($toRemove as $rem) {
            $output = str_replace($rem, '', $output);
          }
          
          if ($inlineMode) {
            // inline mode
            $minifier->setTargetPath('/');
            $minified = $minifier->minify();
            
            if (preg_match_all('~@font-face\{.+?\}~', $minified, $searchFonts)) {
              foreach ($searchFonts[0] as $fontCss) {
                if (!strpos($fontCss, 'font-display:swap')) {
                  $newFontCss = str_replace('@font-face{', '@font-face{font-display:swap;', $fontCss);
                  $minified = str_replace( $fontCss, $newFontCss, $minified);
                }
              }
            }
            
            $output = str_replace($lastScript, '<style>'.$minified.'</style>', $output);
          } else {
            //file mode
            $minifier->minify($cachedFileFullPath);
            
            // $output = str_replace($lastScript, '<link href="'.$cachedFileRelPath.'" rel="stylesheet">', $output);
            $output = str_replace('<base href', "\n".'<link href="'.$cachedFileRelPath.'" rel="preload" as="style" id="rbpreload"><script>document.getElementById(\'rbpreload\').rel=\'stylesheet\'</script><base href', $output);
          }
        }
      }
    }
    
    return $output;
  }
  
  public function journal3_blocks($output) {
    // load footer to initialise j3 vars
    $this->load->controller('common/footer');
    
    $j3 = \Journal3::getInstance()->settings;
    
    preg_match_all("/\[\[\[ j3\.settings\.get\('(.+?)'\) \]\]\]/", $output, $matches, PREG_SET_ORDER);
    
    if (!empty($matches)) {
      foreach ($matches as $match) {
        if ($this->config->get('boost_debug')) {
          file_put_contents(DIR_LOGS . 'boost.log', 'GET J3 block: '.$match[1] . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
      
        $output = str_replace($match[0], $j3->get('boost.'.$match[1]), $output);
      }
    }
    
    return $output;
  }
  
  private function str_lreplace($search, $replace, $subject) {
    $pos = strrpos($subject, $search);

    if($pos !== false) {
      $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
  }
}