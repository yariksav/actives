<?php
namespace yariksav\actives;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'yariksav\actives\controllers';

    function __construct($id, $parent = null, $config = []) {
        parent::__construct($id, $parent = null, $config = []);
    }
}
?>