<?php

namespace yariksav\actives\components;

use yariksav\actives\controls\ControlMgr;
use yii\helpers\ArrayHelper;
use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\PermissionTrait;
use yariksav\actives\base\VisibleTrait;

class ActiveForm extends ActiveObject {

    use PermissionTrait;
    use VisibleTrait;

    public $id;
    public $name;
    public $title;
    public $template;
    public $filter;

    protected $_actions;
    protected $_fields;
    protected $_data;
    protected $_key;
    protected $_validation = [];
    public $componentName = 'Form';

    //public $componentName = 'Dialog'; //????

    public function __construct($config = []) {
        $this->_key =  ArrayHelper::getValue($config, 'key');
        $this->_fields = new ControlMgr($this);
        $this->_actions = new FormActionMgr($this);
        $config = array_merge($this->defaults(), $config);
        parent::__construct($config);
    }

    public function run() {
        //parent::run();
        if ($this->_actions->current) {
            if (!$this->_actions->current->visible || !$this->_actions->current->hasPermissions()) {
                throw new HttpException(423, Yii::t('app.error', 'You are not authorized to perform this action.'));
            }
            $this->_actions->current->run();
//            $this->trigger('before' . $this->_actions->current->name);
//            $this->trigger($this->_actions->current->name);
//            $this->trigger('after' . $this->_actions->current->name);
        } else {
            $this->load();
        }

        if ($this->key) {
            $this->response->key = $this->key;
        }
        if ($this->emits) {
            $this->response->emits = $this->emits;
        }
        return $this->response;
    }
//    public function run() {
//        if (!$this->visible) {
//            if (Yii::$app->user->isGuest) {
//                throw new HttpException(401, Yii::t('app.error', 'Please login for this request.'));
//            } else {
//                throw new HttpException(423, Yii::t('app.error', 'You are not authorized to perform this action.'));
//            }
//        }
//        // todo Add privilege
//    }

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
        //$this->_action = $value;
        $this->_actions->current = $value;
    }

    /**
     * Setting particular actions from current dialog actions.
     * @param array $value Actions
     */
    public function setActions(array $value) {
        $this->_actions->load($value);
    }

    public function setFields($value) {
        $this->_fields->load($value);
    }

    public function setData($value) {
        $this->_data = $value;
    }

    public function getData() {
        return $this->_data;
    }

    public function getModel() {
        if (!$this->_data && $this->_actions->current->data) {
            $this->_data = $this->_actions->current->data;
        }
        if (is_callable($this->_data)) {
            $this->_data = call_user_func_array($this->_data, []);
        }
        return $this->_data;
    }

    protected function load() {
        if ($this->getModel() === null) {
            throw new Exception(Yii::t('actives', 'Record not found'));
        }
        $this->_fields->model = $this->getModel();
        $this->response->fields = $this->_fields->build();
        if ($this->template) {
            $this->response->template = $this->renderTemplate();
        }
        $this->response->actions = $this->_actions->links();
        //$this->response->options = $this->options;
        $this->response->title = $this->title;
        $this->response->id = $this->id;
    }

    protected function renderTemplate() {
        if (is_string($this->template)) {
            $content = $this->getView()->render($this->template, array_merge([
                'model' => $this->getModel(),
                'owner' => $this,
            ]), $this);
        } else {
            $content = call_user_func($this->template, $model, $this);
        }
        return $content;
    }
}
