<?php
namespace yariksav\actives\view\exports;

use yariksav\actives\base\ProtectedObject;
use yii\helpers\ArrayHelper;

class Csv extends Export
{

    function init() {
        $this->icon = 'fa fa-file-excel-o';
        $this->text = 'Csv';
    }

    public function export($data) {
        $this->convert_to_csv($data, $this->file);
    }

    function convert_to_csv($input_array, $output_file_name, $delimiter = ';')
    {
        /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
        $f = fopen('php://memory', 'w');
        /** loop through array  */
        foreach ($input_array as $line) {
            /** default php csv handler **/
            fputcsv($f, $line, $delimiter);
        }
        /** rewrind the "file" with the csv lines **/
        fseek($f, 0);
        /** modify header to be downloadable csv file **/
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
        /** Send file to browser for download */
        fpassthru($f);
    }
}
