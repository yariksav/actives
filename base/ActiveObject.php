<?php

namespace yariksav\actives\base;

use yii;

class ActiveObject extends Component
{
    protected $name;
    protected $affect = [];

    protected function beforeInit() {}

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