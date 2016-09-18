<?php

namespace yariksav\actives\dialog;


use yariksav\actives\base\ViewerTrait;
use yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yariksav\actives\Module;
use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\Exception;
use yariksav\actives\controls\ControlMgr;
use yariksav\actives\controls\Control;
use yariksav\actives\action\ActionMgr;


class Dialog extends BaseDialog {

    public $confirm = [];

    public $filter;
    public $inputs;
    public $title;
    public $name;
    public $control;
    public $template;

    protected $_data = false;
    protected $_actions = [];

    protected $_controls = [];
    protected $_validation = [];

    protected $options;

    function __construct($config = []) {

        $this->_controls = new ControlMgr($this);

        $this->_actions = new ActionMgr($this);
        $this->_actions->load([
            'load' => [
                'on' => [$this, 'onLoad']
            ],
            'control' => [
                'on' => [$this, 'onControl']
            ]
        ]);

        $this->options = [
            'closeOnEscape' => true,
            'isModal' => true,
        ];


        parent::__construct($config);

        //$this->_actions->current = $this->action ? : 'load';
    }

    protected function _init(){
        ;
    }

    public function getIsNewRecord(){
        return $this->isNewRecord;
    }

    /**
     * Build dialog.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function run() {
        parent::run();

        if ($this->_actions->current) {
            if (!$this->_actions->current->visible || !$this->_actions->current->hasPermissions()) {
                throw new HttpException(423, Yii::t('app.error', 'You are not authorized to perform this action.'));
            }
            $this->trigger('before' . $this->_actions->current->name);
            $this->trigger($this->_actions->current->name);
            $this->trigger('after' . $this->_actions->current->name);
        }

        if ($this->key) {
            $this->response->key = $this->key;
        }
        if ($this->emits) {
            $this->response->emits = $this->emits;
        }
        return $this->response;
    }

    /**
     * Setting particular actions from current dialog actions.
     * @param array $value Actions
     */
    public function setActions(array $value) {
        $this->_actions->load($value);
    }

    public function emit($name, $key = null, $action = null) {
        if (!$key) {
            $key = $this->key;
        }
        if (!$action) {
            if ($this->_actions->getCurrent()->name === 'save') {
                $action = $this->isNewRecord ? 'insert' : 'update';
            } else {
                $action = $this->_action;
            }
        }
        $this->emits[] = ['name'=>$name, 'key'=>$key, 'action'=>$action];
    }

    public function setControls($value) {
        $this->_controls->load($value);
    }

    protected function onLoad(){
        if ($this->getModel() === null) {
            throw new Exception(Yii::t('actives', 'Record not found'));
        }
        $this->_controls->model = $this->getModel();
        $this->response->controls = $this->_controls->build();
        if ($this->template) {
            $this->response->template = $this->renderTemplate();
        }
        $this->addResponseOptions();
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

    protected function addResponseOptions(){
        $this->response->actions = $this->_actions->links();
        $this->response->options = $this->options;
        $this->response->title = $this->title;
        $this->response->width = $this->width ? : 500;
    }

    protected function prepare(){
        $this->_controls->model = $this->getModel();
        $this->_controls->update($this->inputs);
        $this->verify();
    }

    protected function verify() {
        if ($this->model instanceof Model) {
            $this->model->validate();
            $this->_validation = array_merge($this->_validation, $this->model->errors);
        }
        if ($this->_validation) {
            throw new ValidationException($this->_validation);
        }
    }

    protected function confirm($message, $buttons = null){
        $id = md5($message);
        if (empty($this->confirm[$id])){
            throw new ConfirmException($message, $id, $buttons);
        }
        return (bool)ArrayHelper::getValue($this->confirm[$id], 'result');
    }

    protected function message($message){
        if (!$this->response->messages)
            $this->response->messages = [];
        $this->response->messages[] = $message;
    }

    protected function redirect($config) {
        if (empty($config['action'])) {
            $config['action'] = 'load';
        }
        if (empty($config['key'])) {
            $config['key'] = $this->key;
        }
        if (empty($config['filter'])) {
            $config['filter'] = $this->filter;
        }
        $dialog = ActiveObject::createObject($config);
        $this->response = $dialog->run();
        if (!$this->response->params) {
            $this->response->params = new \stdClass();
        }
        $this->response->params->class = $config['class'];
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

    protected function onControl() {
        $name = ArrayHelper::remove($this->control, 'name');

        $control = $this->_controls->get($name);

        if (!$control || !$control->visible || !$control->hasPermissions()) {
            throw new Exception(Yii::t('actives', 'Control "{0}" was not found', [$name]));
        }
        $control->config = array_merge(['method'=>''], $control->config, $this->control, ['class' => $control->config['class']]);
        if ($control->requireModel) {
            $control->model = $this->getModel();
        }
        $this->response->control = $control->load();
    }

    protected function deleteInTransaction($model) {
        if (!$model) {
            $model = $this->model;
        }
        $tran = $model->db->beginTransaction();
        $model->delete();
        if ($model->errors) {
            $this->validation = $model->errors;
        } else {
            $tran->commit();
        }
    }

    protected function saveModelInTransaction($model, $callback = null){
        $tran = $model->db->beginTransaction();

        $model->save();

        if ($model->errors) {
            $this->validation = $model->errors;
        } else {
            if (is_callable($callback)) {
                $callback($model);
            }
            $tran->commit();
        }
        return !$model->errors;
    }

    public function getAction() {
        return $this->_action;
    }

    public function setAction($value) {
        parent::setAction($value);
        $this->_actions->current = $value;
    }

    public static function prepareJsDefaults($scriptWrap = true){
        $defaults = [
            'labels'=>[
                'close' => Yii::t('actives', 'Close'),
                'cancel'=> Yii::t('actives', 'Cancel'),
                'yes'   => Yii::t('actives', 'Yes'),
                'no'    => Yii::t('actives', 'No'),
            ],
            'ajax'=>[
                'url'=>Url::toRoute('actives/api')
            ],
            'login'=>['class'=>'system.LoginDialog']
        ];
        return ';$.fn.sydialog.defaults('.json_encode($defaults).');';
    }
}
?>