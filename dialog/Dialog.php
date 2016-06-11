<?php

namespace yariksav\actives\dialog;


use yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yariksav\actives\Module;
use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\Exception;
use yariksav\actives\base\PermissionTrait;
use yariksav\actives\base\VisibleTrait;
use yariksav\actives\controls\ControlMgr;
use yariksav\actives\controls\Control;
use yariksav\actives\action\ActionMgr;


class Dialog extends ActiveObject {

    use PermissionTrait;
    use VisibleTrait;

    public $confirm = [];
    public $data = false;
    public $filter;
    public $id;
    public $inputs;
    public $title;
    public $width = 500;
    public $name;
    public $control;

    protected $_actions = [];
    protected $_action;
    protected $_config;
    protected $_controls = [];
    protected $_validation = [];

    protected $response;
    protected $options;
    protected $isNewRecord = false;

    function __construct($config = []) {
        $this->id = ArrayHelper::getValue($config, 'id');
        $this->isNewRecord = !$this->id;

        $this->response = new \stdClass();
        $this->response->params = new \stdClass();

        $this->_config = $config;
        $this->_controls = new ControlMgr($this);
        $this->_actions = new ActionMgr($this);
        $this->loadSystemActions();
        $this->options = [
            'closeOnEscape' => true,
            'isModal' => true,
        ];

        $this->_init();
        parent::__construct($config);


        $this->_actions->current = $this->action ? : 'load';
    }

    protected function _init(){

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
        if (!$this->visible) {
            if (Yii::$app->user->isGuest) {
                throw new HttpException(401, Yii::t('app.error', 'Please login for this request.'));
            } else {
                throw new HttpException(423, Yii::t('app.error', 'You are not authorized to perform this action.'));
            }
        }
        if ($this->_actions->current) {
            if (!$this->_actions->current->visible || !$this->_actions->current->hasPermissions()) {
                throw new HttpException(423, Yii::t('app.error', 'You are not authorized to perform this action.'));
            }
            $this->trigger('before' . $this->_actions->current->name);
            $this->trigger($this->_actions->current->name);
            $this->trigger('after' . $this->_actions->current->name);
        }

        if ($this->id) {
            $this->response->params->id = $this->id;
        }
        if ($this->affect) {
            $this->response->affect = $this->affect;
        }
        return $this->response;
    }

    /**
     * Setting particular actions from current dialog actions.
     * @param array $value Actions
     */
    public function setActions(array $value) {
        $this->_actions->load($value);
        $this->loadSystemActions();
    }

    protected function loadSystemActions() {
        $this->_actions->loadIfNotExists(
            [
                'load' => [
                    'on' => [$this, 'onLoad']
                ],
                'control' => [
                    'on' => [$this, 'onControl']
                ]
            ]
        );
    }

    public function setAffect($name, $id, $action) {
        $this->affect[] = ['name'=>$name, 'id'=>$id, 'type'=>$action];
    }
    public function setControls($value) {
        $this->_controls->load($value);
    }

    protected function onLoad(){
        if ($this->model === null)
            throw new Exception(Module::t('app', 'Record not found'));
        $this->_controls->model = $this->model;
        $this->response->controls = $this->_controls->build();
        $this->addResponseOptions();
    }

    protected function addResponseOptions(){
        $this->response->actions = $this->_actions->links();
        $this->response->options = $this->options;
        $this->response->title = $this->title;
        $this->response->width = $this->width;

    }

    protected function prepare(){
        $this->_controls->model = $this->model;
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
        //if (!$buttons)
        //    $buttons= ['yes'=>'Yes'];
        if (empty($this->confirm[$id]['result'])){
            throw new ConfirmException($message, $id, $buttons);
        }
        return (bool)$this->confirm[$id]['result'];
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
        if (empty($config['id'])) {
            $config['id'] = $this->id;
        }
        if (empty($config['action'])) {
            $config['filter'] = $this->filter;
        }
        $dialog = ActiveObject::createObject($config);
        $this->response = $dialog->run();
        $this->response->class = $config['class'];
    }

    public function getModel() {
        if (!$this->data && $this->_actions->current->data) {
            $this->data = $this->_actions->current->data;
        }
        if (is_callable($this->data)) {
            $this->data = call_user_func_array($this->data, []);
        }
        return $this->data;
    }

    protected function onControl() {
        $control = $this->_controls->get($this->control);
        if (!$control || !$control->visible || !$control->hasPermissions()) {
            throw new Exception(Yii::t('app', 'Control "{0}" was not found', [$this->control]));
        }
        $control->config = array_merge($control->config, $this->data, ['class' => $control->config['class']]);
        if ($control->requireModel) {
            $control->model = $this->model;
        }
        $this->response = $control->build();
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
        $this->_actions->current = $value;
        $this->_action = $value;
    }

    public static function prepareJsDefaults($scriptWrap = true){
        $defaults = [
            'labels'=>[
                'close' => Module::t('app', 'Close'),
                'cancel'=> Module::t('app', 'Cancel'),
                'yes'   => Module::t('app', 'Yes'),
                'no'    => Module::t('app', 'No'),
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