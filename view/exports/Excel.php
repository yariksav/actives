<?php
namespace yariksav\actives\view\exports;

use yariksav\actives\base\ProtectedObject;
use yii\helpers\ArrayHelper;

class Excel extends Export
{
    function init() {
        $this->icon = 'fa fa-file-excel-o';
        $this->text = 'Excel';
    }
}
