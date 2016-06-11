<?php
namespace yariksav\actives\view;

use yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\Exception;

abstract class ActiveView extends ActiveObject
{
    public $method = 'init'; //!!!!!!!!!!!!!!!!

    protected $request;
    public $system = [];
    protected $data;
    protected $scripts;
    public $searchPhrase = false;

    public $name;
    public $visible = true;
    protected $response;

/*    function __construct($request, $config=[]){
        parent::__construct($config);

        $this->response = new \stdClass();
        if (!isset($request['method']))
            $request['method'] = $this->method ? $this->method : 'init';

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
    }*/

    function __construct($config = []) {
        $this->response = new \stdClass();
        $this->_init();
        parent::__construct($config);
    }

    public function run() {
        if (!$this->visible) {
            if (Yii::$app->user->isGuest) {
                throw new HttpException(401, Yii::t('app.error', 'Please login for this request.'));
            } else {
                throw new HttpException(423, Yii::t('app.error', 'You are not authorized to perform this action.'));
            }
        }
        $this->renderAction();
        return $this->response;
    }

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
    }

    public static function widget($options, $view){
        $instance = self::createInstance($options);
        $response = $instance->getResponse();
        if ($view)
            $response = $instance->_wrap($response, $view);
        return $response;
    }*/

    public static function widget($config){
        $instance = self::createObject($config);
        $response = $instance->run();
        return Html::tag('div', '', [
            'class' => $instance->name.' grid-view clear-top',
            'data-cmp' => 'Grid',
            'data-json-config' => json_encode($response)
        ]);
        return $response;
    }

    protected function renderAction(){
        $action = 'action'.$this->method;

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



    abstract protected function _init();
    abstract protected function data();

    public function getResponse(){
        $response = $this->response;
        if (isset($this->system)){
            $response->system = base64_encode(json_encode($this->system));
        }
        return $response;
    }



}