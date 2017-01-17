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
            'SummaryInfo',
            //'Pagination',
            'loader',
            'Refresh',
        ];
    }
}
