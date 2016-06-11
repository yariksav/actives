<?php
namespace yariksav\actives;

use Yii;

class Module extends \yii\base\Module
{
    //public $controllerNamespace = 'yariksav\actives\controllers';
    public static function t($category, $message, $params = [], $language = null) {
        return Yii::t('actives/'.$category, $message, $params, $language);
    }

    public static function registerTranslationMessages(){
        if (!isset(Yii::$app->get('i18n')->translations['actives*'])) {
            Yii::$app->get('i18n')->translations['actives*'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => __DIR__ . '\\messages',
                'fileMap' => [
                    'actives/app' => 'app.php',
                ],
            ];
        }
    }
    function __construct($id, $parent = null, $config = []) {
        parent::__construct($id, $parent = null, $config = []);
        self::registerTranslationMessages();
    }
}
?>