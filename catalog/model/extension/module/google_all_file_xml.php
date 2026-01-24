<?php
    class ModelExtensionModuleGoogleAllFileXml extends ModelExtensionModuleGoogleAllFile {
        public function __construct($registry){
            parent::__construct($registry);
        }
        function create_file() {
            $this->filename = $this->get_filename();
            $this->filename_path = $this->path_tmp.$this->filename;

            $this->xw = xmlwriter_open_memory();
            xmlwriter_set_indent($this->xw, 1);
            $res = xmlwriter_set_indent_string($this->xw, ' ');

            xmlwriter_start_document($this->xw, '1.0', 'UTF-8');
        }

        function insert_data($elements) {
            $xml_content = $elements['header']."\n";
            foreach ($elements['elements'] as $elements_inside) {
                $xml_content .= '<'.$elements['node'].'>'."\n";
                    foreach ($elements_inside as $tag_name => $element) {
                        if (!is_array($element))
                            $xml_content .= $this->get_tag($tag_name, $element)."\n";
                        else {
                            if($this->array_depth($element) == 1) {
                                foreach ($element as $el) {
                                    $xml_content .= $this->get_tag($tag_name, $this->format_xml_value($el)) . "\n";
                                }
                            } else {
                                if(array_key_exists('attributes', $element)) {
                                    $xml_content .= '<' . $tag_name;
                                    foreach ($element['attributes'] as $attri_name => $attri_value) {
                                        $xml_content .= ' '.$attri_name.'="'.$attri_value.'" ';
                                    }
                                    $xml_content = trim($xml_content);
                                    $xml_content .= '>';

                                    $xml_content .= $this->format_xml_value($element['value']);
                                    $xml_content .= '</' . $tag_name.'>' . "\n";

                                } else {
                                    $exist_tag_children = array_key_exists('tag_children', $element);
                                    $values_in_tag = array_key_exists('values_in_tag', $element) && $element['values_in_tag'];

                                    if (!$values_in_tag)
                                        $xml_content .= $exist_tag_children ? '<' . $tag_name . '>' . "\n" : '';
                                    else
                                        $xml_content .= '<' . $tag_name;

                                    $tag_children = $exist_tag_children ? $element['tag_children'] : (!$values_in_tag ? $tag_name : '');
                                    foreach ($element['values'] as $tag_name2 => $val) {
                                        $xml_content .= $this->get_tag($tag_children, $this->format_xml_value($val), $tag_name2) . (!$values_in_tag ? "\n" : '');
                                        if (empty($values_in_tag))
                                            $xml_content .= ' ';
                                    }
                                    if (empty($values_in_tag))
                                        $xml_content = rtrim($xml_content);

                                    if (!$values_in_tag)
                                        $xml_content .= $exist_tag_children ? '</' . $tag_name . '>' . "\n" : '';
                                    else
                                        $xml_content .= ' />' . "\n";
                                }
                            }
                        }
                    }
                $xml_content .= '</'.$elements['node'].'>'."\n";
            }
            $xml_content .= $elements['footer'];

            file_put_contents($this->filename_path, $xml_content);
        }

        function get_tag($tag_name, $value, $tag_attributes = '') {
            $attributes = '';

            if(!empty($tag_attributes)) {
                if (strpos($tag_attributes, '@') !== false) {
                    $attributes = explode('@', $tag_attributes);
                    $attributes = ' '.$attributes[0].'="'.$attributes[1].'" ';
                }
            }

            $encodingTable = [
                '®' => '&#x00AE;'
            ];
            $value = strtr($value, $encodingTable);


            return !empty($tag_name) ? '<'.$tag_name.$attributes.'>'.$value.'</'.$tag_name.'>' : rtrim($attributes);
        }

        function array_depth(array $array) {
            $max_depth = 1;
            foreach ($array as $value) {
                if (is_array($value)) {
                    $depth = $this->array_depth($value) + 1;

                    if ($depth > $max_depth) {
                        $max_depth = $depth;
                    }
                }
            }
            return $max_depth;
        }
    }
?>