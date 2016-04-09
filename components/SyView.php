<?php
namespace yariksav\actives\components;

use yii\helpers\ArrayHelper;

abstract class SyView extends SyActiveObject
{
    protected $response;
    protected $request;
    protected $system = array();
    protected $data;
    protected $scripts;

    /*public static function getInstance($options){
        if (is_string($options)){
            $class = $options;
            $options = ['class'=>$class];
        }
        else if (is_array($options)){
            $class = isset($options['class']) ? $options['class'] : 'SyGrid';
        }
        else
            throw new Exception('Object params is not defined');

        if (strpos($class, '.') !== false) {
            Yii::import('application.classes.'.$class);
            $class = str_replace('.', '', strrchr($class, '.'));
        }

        $object = new $class($options);
        if (!$object instanceof self)
            throw new Exception($class.' has incorrect instance');
        return $object;
    }*/

    public static function widget($options, $view){
        $instance = self::createInstance($options);
        $response = $instance->getResponse();
        if ($view)
            $response = $instance->_wrap($response, $view);
        return $response;
    }

    protected function renderAction(){
        $action = 'action'.$this->request['method'];

        if (!method_exists($this, $action))
            throw new Exception('Method '.$action.' not exists');

        if ($action == 'actionExport'){
            $this->$action();
            return null;
        }
        else {
            if (\Yii::$app->request->isAjax)
                header('Content-type: application/json');
            $this->$action();
        }
    }

    protected function _wrap($data, $view){
        return json_encode($data);
    }

    function __construct($request){
        $this->response = new \stdClass();
        if (!isset($request['method']))
            $request['method'] = 'init';

        $this->name = str_replace('\\', '_', get_called_class());
        $this->response->class = $request['class'];

        $this->request = $request;
        $class = get_called_class();
        if ($this->request) foreach($this->request as $name=>$value){
            if (property_exists($class, $name) && !in_array($name, ['system']))
                $this->$name=$value;
        }
        $this->_init();
        $this->renderAction();
    }

    abstract protected function _init();
    abstract protected function data();

    public function getResponse(){
        $response = $this->response;
        if (isset($this->system)){
            $response->system = base64_encode(json_encode($this->system));
        }
        return $response;
    }


    protected function renderButtons($buttons, $data = null){
        $result = array();
        if ($buttons) foreach ($buttons as $button){
            if (($data && ArrayHelper::getValue($button, 'for') !== 'row') || (!$data && ArrayHelper::getValue($button, 'for') == 'row'))
                continue;
            unset($button['for']);

            if (isset($button['visible']) && !$this->evaluateExpression($button['visible'], ['data'=>$data]))
                continue;

            if (isset($button['data']) && is_callable($button['data']))
                $button['data'] = $this->evaluateExpression($button['data'], ['data'=>$data]);

            if (isset($button['buttons'])){
                $button['buttons'] = $this->renderButtons($button['buttons'], $data);
            }
            $result[] = $button;
        }
        return $result;
    }

}