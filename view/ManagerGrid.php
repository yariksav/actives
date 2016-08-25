<?php
namespace yariksav\actives\view;

use yii;

class ManagerGrid extends Grid
{

    function beforeInit() {
        parent::beforeInit();
        $this->plugins = [
            'sort'=>[
                'remember'=>true
            ],
            'pagination',
            'loader',
            'refresh',
        ];
    }
}
