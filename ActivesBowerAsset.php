<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yariksav\actives;

use yii\web\AssetBundle;

/**
 * Asset bundle for the Twitter bootstrap css files.
 *
 * @author Savaryn Yaroslav
 */
class ActivesBowerAsset extends AssetBundle
{
    public $sourcePath = '@bower';
    public $css = [
        'smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',
        'selectize/dist/css/selectize.bootstrap3.css',
    ];
    public $js = [
        'selectize/dist/js/standalone/selectize.min.js',
        //'hello/dist/hello.all.js',
    ];
}
