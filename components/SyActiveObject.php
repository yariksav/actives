<?php

namespace yariksav\actives\components;

use yii;

class SyActiveObject extends SyComponent //abstract
{
    protected $name;

    public static function createInstance($options){
        if (is_string($options))
            $options = ['class' => $options];

        if (empty($options['class']))
            throw new \Exception('Object params is not defined');

        $class = $options['class'];
        /*if (strpos($class, '.') !== false) {
            Yii::import('application.classes.'.$class);
            $class = str_replace('.', '', strrchr($class, '.'));
        }*/
        $class = 'app\classes\\'.str_replace('.', '\\', $options['class']);
        if (!class_exists($class))
            $class = __NAMESPACE__ .'\\'.$options['class'];
        if (!class_exists($class))
            throw new \Exception('Class '.$class.' not found');

        $object = new $class($options);
        if (!$object instanceof self)
            throw new \Exception($class.' has incorrect instance');
        return $object;
    }

    protected function setState($key, $value){
        $name = $this->name ? $this->name : get_called_class();
        Yii::$app->session->set($name.'-'.$key, $value);
    }

    protected function getState($key, $default = null){
        $name = $this->name ? $this->name : get_called_class();
        return Yii::$app->session->get($name.'-'.$key, $default);
    }

}