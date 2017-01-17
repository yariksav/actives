<?php

namespace yariksav\actives\base;

use yii;
use yii\helpers\Html;

abstract class ActiveObject extends Component implements yii\base\ViewContextInterface, RunnableInterface
{
    use PermissionTrait;
    use ViewerTrait;
    use VisibleTrait;

    protected $name;
    protected $emits = [];
    public $listens = [];
    public $cmp;
    protected $_response;

    function __construct($config = []) {
        $this->_response = new \stdClass();
        parent::__construct($config);
    }

    public function getResponse() {
        return $this->_response;
    }

    public function setResponse($value) {
        $this->_response = $value;
    }

    public static function widget($config = []){
        if (empty($config['class'])) {
            $config['class'] = get_called_class();
        }
        $instance = Yii::createObject($config, []);
        $instance->run();
        return Html::tag('div', '', [
            'data'=>[
                'api-url' => $instance::apiUrl(),
                'cmp' => $instance->cmp,
                'cmp-config' => json_encode($instance->response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ],
        ]);
    }

    public static function apiUrl($action = null) {
        $className = \yii\helpers\StringHelper::basename(get_called_class());
        $namespace = implode('\\', explode('\\', get_called_class(), -1));
        $alias = array_search($namespace, Yii::$app->params['alias']);
        if ($alias !== false) {
            return $alias.'/'.lcfirst($className) . ($action ? '/'.$action : '');
        }
    }


/*    public static function createObject($config=[]){
        if (empty($config['class'])) {
            throw new \Exception('Object class is not defined');
        }

        $class = $config['class'];
        if (!class_exists($class)) {
            $class = Yii::getAlias('@actives') . '\\' . str_replace('.', '\\', $config['class']);
        }

        if (!class_exists($class)) {
            throw new \Exception('Class ' . $class . ' not found');
        }
        $config['class'] = $class;
        $object = Yii::createObject($config, []);

        if (!$object instanceof self) {
            throw new \Exception($class . ' has incorrect instance');
        }
        return $object;
    }*/

    public function setState($key, $value){
        $name = $this->name ? $this->name : get_called_class();
        Yii::$app->session->set($name.'-'.$key, $value);
    }

    public function getState($key, $default = null){
        $name = $this->name ? $this->name : get_called_class();
        return Yii::$app->session->get($name.'-'.$key, $default);
    }

}