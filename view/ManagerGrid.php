<?php
namespace yariksav\actives\view;

use yii;

class ManagerGrid extends Grid
{

    function init() {
        parent::init();
        $this->plugins = [
            'manage',
            'sort',
            'search',
            'pager',
            'pageSize',
            'contextMenu',
            'columnMenu',
            'loader',
            'refresh',
            'filter'
        ];
    }

}