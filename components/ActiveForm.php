<?php

namespace yariksav\actives\components;

use yii;
use yariksav\actives\controls\ControlMgr;
use yariksav\actives\exceptions\ConfirmException;
use yariksav\actives\exceptions\ValidationException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\PermissionTrait;
use yariksav\actives\base\VisibleTrait;
use yii\web\HttpException;

class ActiveForm extends ActiveObject {

    public $id;
    public $name;
    public $title;
    public $template;
    public $filter;
    public $confirm;

    protected $_actions;
    protected $_controls;
    protected $_data;
    protected $_values;
    protected $_key;
    protected $_validation = [];
    protected $_emits = [];

    public $cmp = 'Form';


    public function __construct($config = []) {
        $this->_key =  ArrayHelper::getValue($config, 'key');
        $this->_controls = new ControlMgr($this);
        $this->_actions = new FormActionMgr($this);
        parent::__construct($config);
    }

    public function run($action = null) {
        if ($action) {
            $action = $this->_actions->get($action);
            if (!$action) {
                throw new HttpException(404, 'Unknown Action');
            }
            $action->run();
        } else {
            $this->load();
        }

        if ($this->key) {
            $this->response->key = $this->key;
        }
        if ($this->_emits) {
            $this->response->emits = $this->_emits;
        }
        return $this->response;
    }


    public function getIsNewRecord() {
        return !$this->key;
    }

    public function getKey() {
        return $this->_key;
    }

    public function setKey($value) {
        $this->_key = $value;
    }

    public function getAction() {
        return $this->_action;
    }

    public function setAction($value) {
        $this->_actions->current = $value;
    }

    public function setValues($value) {
        $this->_values = $value;
        $this->_controls->values = $value;
    }


    /**
     * Setting particular actions from current dialog actions.
     * @param array $value Actions
     */
    public function setActions(array $value) {
        $this->_actions->load($value);
    }

    public function setControls($value) {
        $this->_controls->load($value);
    }

    public function getData() {
        return $this->_data;
    }

    public function setData($value) {
        $this->_data = $value;

        if (!$this->_data && $this->_actions->current->data) {  //????????????
            $this->_data = $this->_actions->current->data;
        }

        if (is_string($this->_data) && class_exists($this->_data)) {
            $class = $this->_data;
            $this->_data = $this->isNewRecord ? new $class : $class::findOne($this->key);
        } else if (is_callable($this->_data)) {
            $this->_data = call_user_func($this->_data);
        }

        if ($this->_data instanceof Model) {
            $this->registerModel($this->_data);
        }

        $this->_controls->model = $this->data;
        $this->_controls->values = $this->_values;
    }

    protected function registerModel($model) {
        $model->on(yii\db\ActiveRecord::EVENT_AFTER_UPDATE, function($event) {
            //$this->key = $this->data->getP;
            $this->emit($model->name);
        });
//        $model->on(Model::EVENT_BEFORE_VALIDATE, function($event) {
//            $event->sender;
//        });
        $model->on(Model::EVENT_AFTER_VALIDATE, function($event) {
            $this->_validation = array_merge(
                $this->_validation,
                $event->sender->errors
            );
            if ($this->_validation) {
                throw new ValidationException($this->_validation);
            }
            return true;
        });
        
        
        
        //$model->on(
    }

    protected function load() {
        $data = $this->data;
//        if ($data === null) {
//            throw new HttpException(500, Yii::t('actives', 'Record not found'));
//        }

        $this->_controls->model = $data;
        $this->response->controls = $this->_controls->build();

        if ($this->template) {
            $this->response->template = $this->renderTemplate();
        }
        $this->response->actions = $this->_actions->links();
        //$this->response->options = $this->options;
        $this->response->title = $this->title;
        $this->response->id = $this->id;
        
        //$this->response->v = $this->_data->value
    }

    protected function control() {
        $name = ArrayHelper::remove($this->control, 'name');

        $control = $this->_controls->get($name);

        if (!$control || !$control->visible || !$control->hasPermissions()) {
            throw new yii\base\Exception(Yii::t('actives', 'Control "{0}" was not found', [$name]));
        }
        $control->config = array_merge(/*['method'=>''],*/ $control->config ?:[], $this->control, ['class' => $control->config['class']]);
        if ($control->requireModel) {
            $control->model = $this->getModel();
        }
        $this->response->control = $control->load();
    }

   /* protected function validate() {
        if ($this->_data instanceof Model) {
            $this->_data->validate();
            $this->_validation = array_merge(
                $this->_validation,
                $this->_data->errors
            );
        }

        ////,
        //$this->_controls->validate()

        if ($this->_validation) {
            throw new ValidationException($this->_validation);
        }
    }*/

    public function emit($name, $key = null, $action = null) {
        if (!$key) {
            $key = $this->key;
        }
        if (!$action) {
            if ($this->_actions->getCurrent()->name === 'save') {
                $action = $this->isNewRecord ? 'insert' : 'update';
            } else {
                $action = $this->_actions->getCurrent();
            }
        }
        $this->_emits[] = ['name'=>$name, 'key'=>$key, 'action'=>$action];
    }

    protected function confirm($message, $buttons = null){
        $id = md5($message);
        if (empty($this->confirm[$id])){
            throw new ConfirmException($message, $id, $buttons);
        }
        $result = ArrayHelper::getValue($this->confirm[$id], 'result');
        return $buttons ? $result : (bool)$result;
    }

    protected function message($message){
        if (!$this->response->messages)
            $this->response->messages = [];
        $this->response->messages[] = $message;
    }


    protected function renderTemplate() {
        $content = '';
        if (is_string($this->template)) {
            $content = $this->getView()->render($this->template, array_merge([
                'model' => $this->getData(),
                'owner' => $this,
            ]), $this);
        } else {
            $content = is_callable($this->template) ? call_user_func($this->template, $model, $this) : '';
        }
        return $content;
    }
}
