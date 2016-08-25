<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 23.05.2016
 * Time: 16:37
 */

namespace yariksav\actives\controls;

use yii;

class GoogleMapsControl extends Control {

   // public $config = [];
    //public $requireModel = true;

    public function build() {
        return array_merge(parent::build(), [
            'language' => Yii::$app->language
        ]);
    }
}