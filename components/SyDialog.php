<?php

namespace yariksav\actives\components;

use yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yariksav\actives\Module;

class SyDialog extends SyActiveObject {

    public $validation = array();

    protected $method;
    protected $current_id;
    protected $id;
    protected $data;
    protected $inputs;
    protected $filter;
    protected $request;
    protected $response;
    protected $roles;
    protected $button;

    protected $model;
    public $title;
    public $width = 500;
    protected $closeOnEscape = true;
    protected $isModal = true;
//	protected $options = array();
    protected $allowGuest = false;
    public $parent;

    /*public static function createInstance($options){
        //var_export($options);
        $class = isset($options['class']) ? $options['class'] : null;
        if (!$class)
            throw new Exception('Dialog params is not defined');

        if (strpos($class, '.') !== false) {
            Yii::import('application.classes.'.$class);
            $class = str_replace('.', '', strrchr($class, '.'));
        }

        $object = new $class($options);
        if (!$object instanceof self)
            throw new Exception($class.' has incorrect instance');
        return $object;
    }*/

    function __construct($request) {
        //parent::__construct($action);
        $this->request = $request;
        $this->filter = isset($request['filter']) ? $request['filter'] : [];
        $this->response = new \stdClass();

        $this->id = ArrayHelper::getValue($request, 'id', -1);
        $this->current_id = $this->id;
        $this->method = ArrayHelper::getValue($request, 'method');
        $this->parent = ArrayHelper::getValue($request, 'parent');
        $this->inputs = ArrayHelper::getValue($request, 'inputs');
        $this->_init();
    }

    protected function _init(){}
    protected function beforeLoad(){}

    public function build(){
        if (!$this->allowGuest)
            $this->raiseExceptionsIfGuest();

        $method = 'on'.ucfirst($this->method);
        if ($this->method && method_exists($this, $method)){
            $this->$method();
        }
        else
            throw new Exception("Error! Unknown method '$this->method'", 222);

        return json_encode($this->response);
    }


    public function buttons(){
        return [
            'ok',
        ];
    }

    public function prepareButtons(){
        $ret = array();
        $buttons = $this->buttons();
        if ($buttons) foreach ($buttons as $key=>$button){
            if (is_string($button)){
                $ret[$button] = array(
                    'text'=>Yii::t('app', ucfirst($button)),
                );
            }
            else if ($button === false){
                $ret[$key] = false;
            }
            else {
                if (ArrayHelper::getValue($button, 'visible', true) === false)
                    continue;
                unset($button['click']);
                $ret[$key] = $button;
            }
        }
        return $ret ? $ret : null;
    }

    public function checkButton($btn){
        $buttons = $this->prepareButtons();
        if ($buttons && isset($buttons[$btn]) && ArrayHelper::getValue($buttons[$btn], 'enabled', true) !== false)
            return $buttons[$btn];
        return false;
    }

    public function getValue($value){
        if (isset($this->request[$value]))
            return $this->request[$value];
        if (isset($this->filter[$value]))
            return $this->filter[$value];
    }

    protected function onInsert(){
        $this->inputs===null ? $this->loadDialog() : $this->saveDialog();
    }

    protected function onUpdate(){
        $this->inputs===null ? $this->loadDialog() : $this->saveDialog();
    }

    protected function loadDialog(){
        $this->beforeLoad();
        $this->models();
        //if (!$this->model)
        //	throw new Exception(Yii::t('error', 'Record not found'));

        $helper = new SyDialogConstructor($this->model);
        $controls = $helper->buildControls($this->controls());

        $this->response->controls = $controls;
        $this->response->buttons = $this->prepareButtons();
        $this->response->closeOnEscape = $this->closeOnEscape;
        $this->response->isModal = $this->isModal;
        $this->response->title = $this->title;
        $this->response->width = $this->width;
    }

    protected function saveDialog(){
        $this->button = $this->checkButton(ArrayHelper::getValue($this->request, 'button'));
        if (!$this->button)
            throw new Exception(Yii::t('app.error', 'You are not authorized to perform this action.'));

        $this->models();
        $this->inputs();
        $this->validate();
        if ($this->validation){
            $this->response->validation = $this->cleanRequestData($this->validation);
        }
        else{
            $this->save();
            if ($this->validation){
                //$this->response->error = Yii::t('app', 'Save error');
                $this->response->validation = $this->validation;
            }
        }
//		if ($this->options)
//			$this->response->options = $this->options;
    }

    protected function onDelete(){
        $this->models();
        if (!$this->model)
            throw new Exception('Object not found in database');
        $this->save();
    }

    protected function onControlData(){
        $name = isset($this->request['name']) ? $this->request['name'] : $this->request['control'];
        $controls = $this->controls();
        $control = $this->findControlByField($controls, $name, 'name');

        if (!$control)
            $control = $this->findControlByField($controls, $this->request['name'], 'type');

        if (!$control)
            throw new \Exception(Yii::t('app', 'Control "{0}" was not found', [$this->request['name']]));
        $this->response = $this->getControlData($control, ArrayHelper::getValue($this->request,'query'));
    }

    protected function getControlData($control, $params = null){
        switch($control['type']) {
            case 'grid':
                if (empty($control['class']))
                    $control['class'] = 'SyGrid';

                $this->data();
                $control['model'] = $this->model;

                $params = ArrayHelper::getValue($this->request, 'data');
                //if (!$params || !is_array($params))
                //	$params = [$params];
                //var_export(array_merge($control, $params));
                $grid = SyActiveObject::createInstance(array_merge($control, $params));
                return $grid->response;
            default:
                $data = isset($control['data']) ? $control['data'] : '';
                if (is_callable($data))
                    $data = $data($params);

                if (isset($control['fields']))
                    $data = SyDialogConstructor::prepareDataByFields($data, $control['fields']);

                return ['data'=>$data];
        }
    }

    public function getIsLoad(){
        return $this->method == 'load';
    }

    public function getIsDelete(){
        return $this->method == 'delete';
    }

    public function getIsNewRecord(){
        return $this->method == 'insert';// || !($this->id > 0);
    }

    public function controls() {
        return array();
    }


    public function findControlByField($controls, $name, $field = 'name'){
        foreach ($controls as $control){
            if ((isset($control[$field]) and $control[$field] == $name))
                return $control;
            if (isset($control['controls'])) {
                $found = $this->findControlByField($control['controls'], $name, $field);
                if ($found)
                        return $found;
            }
        }
    }


    public function models(){
        return $this->data();
    }

    public function data(){
        // stub;
    }

    protected function inputs(){
        if ($this->model && $this->model instanceof Model){
            $this->model->attributes = $this->inputs;
        }
    }


    public function validate() {
        if ($this->model instanceof Model) {

            $this->model->validate();
            $this->validation = $this->model->errors;
        }
    }

    public function save() {
        // stub;
    }



    protected function checkErrors($errors){
        if ($errors)
            $this->validation = $errors;
        return !$errors;
    }

    protected function saveModelInTransaction($model, $callback = null){
        $tran = $model->db->beginTransaction();

        if ($this->isDelete)
            $model->delete();
        else
            $model->save();

        if ($model->errors)
            $this->validation = $model->errors;
        else {
            if (is_callable($callback))
                $callback($model);
            $tran->commit();
        }
        return !$model->errors;
    }


    protected function throwValidationException($control, $msg, $params=array()){
        $msg = Yii::t('app.error', $msg, $params);
        $this->validation[$control] = $msg;
        throw new \SyException($msg);
    }

    protected function confirm($message, $buttons = null){
        $id = md5($message);
        if (!$buttons)
            $buttons= ['yes'=>'Yes'];
        $confirm = ArrayHelper::getValue($this->request, 'confirm', []);
        if (empty($confirm[$id])){
            throw new SyConfirmException($message, $id, $buttons);
        }
        return (string)$confirm[$id]['result'];
    }

    protected  function message($message){
        if (!$this->response->messages)
            $this->response->messages = [];
        $this->response->messages[] = $message;
    }

    // TODO! доделать роли

    //protected function checkAccess(){
        //return !Yii::app()->user->isGuest;
        //if (!$this->options['guest'] === false && Yii::app()->user->isGuest)
        //	throw new MsgException('Access error. Please login again');
        //if (!Yii::app()->user->checkAccess($action, array($actiontype))))
        //   throw new MsgException("Access error. You do not have permission to access this page");
    //}

    protected function raiseExceptionsIfGuest(){
        if (Yii::$app->user->isGuest)
            throw new HttpException(401, Yii::t('app.error', 'Please login for this request.'));
    }

    public function checkPermission($permission){
        $this->raiseExceptionsIfGuest();
        if ($permission && !Yii::$app->user->can($permission))
            throw new HttpException(423, Yii::t('app.error', 'You are not authorized to perform this action.'));
    }

    public function can($role){
        return Yii::$app->user->hasRole($role);
    }

    public function checkIsAdmin(){
        $this->raiseExceptionsIfGuest();
        //if (!Yii::app()->user->isAdmin())
        //	throw new HttpException(423, Yii::t('app.error', 'You are not authorized to perform this action.'));
    }

    protected function parseValidation($validation){
        $validations = $this->cleanRequestData($validation);
        $new = array();
        if ($validations) foreach ($validations as $key=>$value){
            $new[$key]=implode('',$value);
        }
        return $new;
    }
    protected function request($param, $default = null){
        return isset($this->request[$param]) ? $this->request[$param] : $default;
    }

    public function cleanRequestData($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$this->cleanRequestData($key)] = $this->cleanRequestData($value);
            }
        }
        else {
            $data = htmlspecialchars(stripslashes(trim($data)));
        }
        return $data;
    }

    public static function prepareJsDefaults($scriptWrap = true){
        $defaults = array(
            'labels'=>array(
                'close'=>Module::t('app', 'Close'),
                'cancel'=>Module::t('app', 'Cancel'),
                'next'=>Module::t('app', 'Next'),
                'prev'=>Module::t('app', 'Previous'),
                'finish'=>Module::t('app', 'Save'),
                'yes'=>Module::t('app', 'Yes'),
                'no'=>Module::t('app', 'No'),
            ),
            'ajax'=>array(
                'url'=>Url::toRoute('sy/api')
            ),
            'login'=>['class'=>'system.LoginDialog']
        );
        return ';$.fn.sydialog.defaults('.json_encode($defaults).');';
    }
}

?>