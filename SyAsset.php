<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yariksav\actives;

use yii\web\AssetBundle;
use yii\web\View;

use yariksav\actives\components\SyDialog;
use yariksav\actives\components\SyGrid;
/**
 * @author Savaryn Yaroslav <yariksav@gmail.com>
 * @since 1.0
 */
class SyAsset extends AssetBundle
{
    //public $basePath = '@webroot';
    //public $baseUrl = '@web';
    public $sourcePath ='@app/modules/sy/assets/';
    public $publishOptions = ['forceCopy'=>true];
    public $css = [
        'js/selectize.bootstrap3.css',
        'css/dialog.css',
        'css/sy.alerts.css',
        'js/sy.toggler.css',
        'js/daterangepicker/daterangepicker.css',
        //'js/datepicker/bootstrap-datepicker.css',
        'js/datetimepicker/css/bootstrap-datetimepicker.css',
        'js/sy.grid.css',
        'js/sy.tree.css'
    ];
    public $js = [   
        'js/selectize.js',
        'js/sy.toggler.js',
        'js/daterangepicker/moment.min.js',
        'js/daterangepicker/daterangepicker.js',
        'js/daterangepicker/daterangepicker.ru.js',
        //'js/datepicker/bootstrap-datepicker.js',
        //'js/datepicker/bootstrap-datepicker.ru.min.js',
        'js/datetimepicker/js/bootstrap-datetimepicker.js',
        'js/datetimepicker/js/locales/bootstrap-datetimepicker.ru.js',
        'js/sy.utils.js',
        'js/sy.dialog.js',
        'js/sy.html.js',
        'js/sy.alerts.js',
        'js/jquery.numeric.min.js',
        'js/sy.grid.js',
        'js/sy.context.js',
        'js/sy.tree.js',
    ];
    public $depends = [
    ];

    public function init(){
        //$this->js[] = 'js/locale/sy.locale.'.substr(\Yii::$app->language, 0, strpos(\Yii::$app->language, '_')).'.js';
        parent::init();
    }

    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);
        \Yii::$app->i18n->translations['modules/sy/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            //'sourceLanguage' => 'ru',
            'basePath' => '@yariksav/actives/messages',
            'fileMap' => [
                'yariksav/actives/app' => 'app.php',
            ],
        ];
        $view->registerJs(SyDialog::prepareJsDefaults(), View::POS_END);
        $view->registerJs(SyGrid::prepareJsDefaults(), View::POS_END);
    }

}
