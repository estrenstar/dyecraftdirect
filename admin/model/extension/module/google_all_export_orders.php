<?php
//Devman Extensions - info@devmanextensions.com - 2017-01-20 16:33:18 - Excel library
if ((VERSION === '3.0.3.8' || VERSION === '3.0.3.9') && version_compare(PHP_VERSION, '8.0.0', '>=')) {
    require_once DIR_SYSTEM . 'library/Spout8/Autoloader/autoload.php';
} else {
    require_once DIR_SYSTEM . 'library/Spout/Autoloader/autoload.php';
}
//END

class ModelExtensionModuleGoogleAllExportOrders extends ModelExtensionModuleGoogleAll {
    public function __construct($registry) {
        parent::__construct($registry);

        if (!is_dir($this->path_tmp)) {
            mkdir($this->path_tmp, 0755);
        }

        if ((VERSION === '3.0.3.8' || VERSION === '3.0.3.9') && version_compare(PHP_VERSION, '8.0.0', '>=')) {
            $this->use_spout_8 = true;

            if (!$this->registry->has('writer') ) {
                $this->writer = Box\Spout\Writer\Common\Creator\WriterEntityFactory::createXLSXWriter();
            }
        } else {
            $this->use_spout_8 = false;

            if (!$this->registry->has('writer') ) {
                $this->writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX);
            }
        }
    }

    public function create_file() {
        $this->filename = ucfirst("export_orders_" . date('Y-m-d-His') . ".xlsx");
        $this->filename_path = $this->path_tmp . $this->filename;
        
        $this->writer->openToFile($this->filename_path);
    }

    public function insert_columns($columns) {
        $firstSheet = $this->writer->getCurrentSheet();
        $firstSheet->setName("xlsx_sheet_name_export_orders");
        
        $styles_array = array();
        $final_column_names = array();

        foreach ($columns as $col) {
            if(!array_key_exists($col['bg_color'], $styles_array)) {
                $styles_array[$col['bg_color']] = $this->get_style_cell($col['bg_color']);
            }

            if ($this->use_spout_8) {
                $final_column_names[] = Box\Spout\Writer\Common\Creator\WriterEntityFactory::createCell($col['custom_name'], $styles_array[$col['bg_color']]);
            } else {
                $final_column_names[] = $col['custom_name'];
            }
        }


        if ($this->use_spout_8) {
            $row = Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRow($final_column_names, $this->get_style_cell());
            $this->writer->addRow($row);
        } else {
            $this->writer->addRowWithStyle($final_column_names, $this->get_style_cell());
        }
    }

    public function insert_data($columns, $elements) {
        $style = $this->get_style_cell_simple();

        foreach ($elements as $element) {
            $temp = array();

            foreach ($columns as $col_info) {
                $custom_name = $col_info['custom_name'];
                $temp[] = array_key_exists($custom_name, $element) ? $element[$custom_name] : '';
            }

            if ($this->use_spout_8) {
                $this->writer->addRow(Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray($temp, $style));
            } else {
                $this->writer->addRowWithStyle($temp, $style);
            }
        }

        $this->writer->close();
    }

    public function get_style_cell($background_color = '55acee') {
        $border = $this->get_border_cell();

        if ($this->use_spout_8) {
            return (new Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
                ->setBorder($border)
                ->setFontBold()
                ->setFontSize(11)
                ->setFontColor('ffffff')
                ->setShouldWrapText(false)
                ->setBackgroundColor($background_color)
                ->build();
        }
        
        return (new Box\Spout\Writer\Style\StyleBuilder())
            ->setBorder($border)
            ->setFontBold()
            ->setFontSize(11)
            ->setFontColor('ffffff')
            ->setShouldWrapText(false)
            ->setBackgroundColor($background_color)
            ->build();
    }

    function get_border_cell() {
        if ($this->use_spout_8) {
            return (new Box\Spout\Writer\Common\Creator\Style\BorderBuilder())
                ->setBorderTop('000000', Box\Spout\Common\Entity\Style\Border::WIDTH_THIN)
                ->setBorderBottom('000000', Box\Spout\Common\Entity\Style\Border::WIDTH_THIN)
                ->setBorderLeft('000000', Box\Spout\Common\Entity\Style\Border::WIDTH_THIN)
                ->setBorderRight('000000', Box\Spout\Common\Entity\Style\Border::WIDTH_THIN)
                ->build();
        }

        return (new Box\Spout\Writer\Style\BorderBuilder())
            ->setBorderTop('000000', Box\Spout\Writer\Style\Border::WIDTH_THIN)
            ->setBorderBottom('000000', Box\Spout\Writer\Style\Border::WIDTH_THIN)
            ->setBorderLeft('000000', Box\Spout\Writer\Style\Border::WIDTH_THIN)
            ->setBorderRight('000000', Box\Spout\Writer\Style\Border::WIDTH_THIN)
            ->build();
    }

    function get_style_cell_simple() {
        $border = $this->get_border_cell();

        if ($this->use_spout_8) {
            return (new Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
                ->setBorder($border)
                ->setShouldWrapText(false)
                ->build();
        }
        
        return (new Box\Spout\Writer\Style\StyleBuilder())
            ->setBorder($border)
            ->setShouldWrapText(false)
            ->build();
    }
}
?>