<?php
namespace yariksav\actives\view;

use yii;

class ManagerGrid extends Grid
{

    function beforeInit() {
        parent::beforeInit();
        $this->plugins = [
            'sort'=>[
                'saveState'=>true
            ],
//            'manage',
//            'contextMenu',
//            'columnMenu',
            'pagination',
            'loader',
            'refresh',
        ];
    }

}

//
//'export' => [
//    'type' => 'export',
//    'collection'=> [
//        'xls'=>[
//            'type'=>'excel',
//            'text'=>'Excel',
//            'file'=>'report.xls'
//        ],
//        'csv'=>[
//            'type'=>'csv',
//            'text'=>'Csv',
//            'file'=>'report.csv'
//        ]
//    ]
//],