<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yariksav\actives;

use yii;
use yii\web\AssetBundle;
use yii\web\View;

use yariksav\actives\dialog\Dialog;
use yariksav\actives\view\Grid;
/**
 * @author Savaryn Yaroslav <yariksav@gmail.com>
 * @since 1.0
 */
class ActivesAsset extends AssetBundle
{
    //public $basePath = '@webroot';
    //public $baseUrl = '@web';
    public $sourcePath = '@yariksav/actives/assets/';
    public $publishOptions = ['forceCopy'=>true];
    public $css = [
 //       '@bower/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',

        'css/dialog.css',
        'css/sy.alerts.css',
        'js/sy.toggler.css',
        'js/daterangepicker/daterangepicker.css',
        //'js/datepicker/bootstrap-datepicker.css',
        //'js/datetimepicker/css/bootstrap-datetimepicker.css',
        'js/sy.grid.css',
        'js/sy.tree.css'
    ];
    public $js = [
        //'js/common.js',
        'js/src/app.js',
        'js/templates.js',
        //'js/all.js',
        'js/selectize.js',
        'js/sy.toggler.js',
        'js/daterangepicker/moment.min.js',
        'js/daterangepicker/daterangepicker.js',
        'js/daterangepicker/daterangepicker.ru.js',
        //'js/datepicker/bootstrap-datepicker.js',
        //'js/datepicker/bootstrap-datepicker.ru.min.js',
        'js/datetimepicker/js/bootstrap-datetimepicker.js',
        //'js/datetimepicker/js/locales/bootstrap-datetimepicker.ru.js',
        //'js/sy.utils.js',
        //'js/sy.dialog.js',
        //'js/sy.html.js',
        'js/sy.alerts.js',
        'js/jquery.numeric.min.js',
        //'js/sy.grid.js',
        //'js/sy.context.js',
        'js/sy.tree.js',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yariksav\actives\ActivesBowerAsset',
        'rmrevin\yii\fontawesome\AssetBundle'
    ];

    public function init(){
        //$this->js[] = 'js/locale/sy.locale.'.substr(\Yii::$app->language, 0, strpos(\Yii::$app->language, '_')).'.js';
        parent::init();
    }

    public function registerAssetFiles($view) {
        parent::registerAssetFiles($view);
        //$view->registerJs(Dialog::prepareJsDefaults(), View::POS_END);
        //$view->registerJs(Grid::prepareJsDefaults(), View::POS_END);
    }
}
