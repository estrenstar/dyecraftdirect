<?php
    //Devman Extensions - info@devmanextensions.com - 2017-01-20 16:33:18 - Excel library
        if ((VERSION === '3.0.3.8' || VERSION === '3.0.3.9') && version_compare(PHP_VERSION, '8.0.0', '>=')) {
            require_once DIR_SYSTEM . 'library/Spout8/Autoloader/autoload.php';
        } else {
            require_once DIR_SYSTEM . 'library/Spout/Autoloader/autoload.php';
        }
    //END

    class ModelExtensionModuleGoogleAllFileCsv extends ModelExtensionModuleGoogleAllFile 
    {
        public function __construct( $registry ) {
            parent::__construct($registry);

            if ((VERSION === '3.0.3.8' || VERSION === '3.0.3.9') && version_compare(PHP_VERSION, '8.0.0', '>=')) {
                $this->use_spout_8 = true;

                if (!$this->registry->has('writer') ) {
                    $this->writer = Box\Spout\Writer\Common\Creator\WriterEntityFactory::createCSVWriter();
                }
            } else {
                $this->use_spout_8 = false;

                if (!$this->registry->has('writer') ) {
                    $this->writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::CSV);
                }
            }
        }

        public function create_file() {
            $this->filename = $this->get_filename();
            $this->filename_path = $this->path_tmp.$this->filename;
            
            $this->writer->openToFile($this->filename_path);
        }

        public function insert_columns($columns) {
            foreach ($columns as $col) {
                $final_column_names[] = $col['custom_name'];
            }

            if ($this->use_spout_8) {
                $row = Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray($final_column_names);
                $this->writer->addRow($row);
            } else {
                $this->writer->addRow($final_column_names);
            }
        }

        public function insert_data( $feed_elements ) {
            $elements = $feed_elements['elements'];
            $columns = $feed_elements['columns'];

            if ($this->use_spout_8) {
                $this->writer->addRow(Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray($columns));
            } else {
                $this->writer->addRow($columns);
            }
            
            foreach ($elements as $element_id => $element) {
                if ($this->use_spout_8) {
                    $this->writer->addRow(Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray($element));
                } else {
                    $this->writer->addRow($element);
                }
            }

            $this->writer->close();
        }
    }
?>