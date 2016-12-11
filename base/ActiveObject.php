<?php

namespace yariksav\actives\base;

use yii;
use yii\helpers\Html;

abstract class ActiveObject extends Component implements yii\base\ViewContextInterface, RunnableInterface
{
    use ViewerTrait;

    protected $name;
    protected $emits = [];
    protected $listens = [];
    public $componentName;
    protected $_response;

    function __construct($config = []) {
        $this->_response = new \stdClass();
        parent::__construct($config);
    }

//    public function className() {
//        return get_called_class();
//    }
    protected function beforeInit() {} // todo ????

    
    public function getResponse() {
        return $this->_response;
        // todo: check is this necessary
        //        if (isset($response->data)){
        //            $response->system = base64_encode(json_encode($this->system));
        //        }
    }

    public static function widget($config){
        $instance = self::createObject($config);
        $instance->run();
        return Html::tag('div', '', [
            'data'=>[
                'class' => $instance->className(),
                'cmp' => $instance->componentName,
                'cmp-config' => json_encode($instance->response)
            ],
        ]);
    }

    public function setResponse($value) {
        $this->_response = $value;
    }

    public static function createObject($config=[]){
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
    }

    public function setState($key, $value){
        $name = $this->name ? $this->name : get_called_class();
        Yii::$app->session->set($name.'-'.$key, $value);
    }

    public function getState($key, $default = null){
        $name = $this->name ? $this->name : get_called_class();
        return Yii::$app->session->get($name.'-'.$key, $default);
    }

}