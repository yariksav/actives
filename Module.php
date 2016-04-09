<?php
namespace yariksav\actives;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'yariksav\actives\controllers';

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/sy/'.$category, $message, $params, $language);
    }

    function __construct($id, $parent = null, $config = []){
        parent::__construct($id, $parent = null, $config = []);

        Yii::$app->i18n->translations['modules/sy/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@yariksav/actives/messages',
            'fileMap' => [
                'modules/sy/app' => 'app.php',
            ],
        ];
    }
}1

?>