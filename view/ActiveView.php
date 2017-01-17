<?php
namespace yariksav\actives\view;

use yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\Exception;
use yariksav\actives\view\plugins\PluginMgr;

abstract class ActiveView extends ActiveObject
{
    public $key;

    public $baseModel;// base model data if grid linked to some model

    protected $_data;
    protected $_plugins;

    function __construct($config = []) {
        $this->_plugins = new PluginMgr($this);
        $this->name = get_called_class();
        parent::__construct($config);
    }

    public function getPlugins() {
        return $this->_plugins;
    }

    public function setPlugins($value) {
        $this->_plugins->load($value);
    }

    public function actionLoad(){
        $this->renderItems();
        $this->renderOptions();
    }

    public function actionData(){
        $this->renderItems();
    }

    public function renderItems() {
        $this->_response->data = $this->_data;
    }

    public function getData() {
        return $this->_data;
    }
    public function setData($value) {
        $this->_data = $value;
    }

    protected function renderOptions() {
        $this->_response->plugins = $this->_plugins->build();
        $this->_response->listens = $this->listens;
//        $this->_response->params = new \stdClass();
//        $this->_response->params->class = $this->className();
    }

    public function run($action = null) {
        if (!$this->visible || !$this->hasPermissions()) {
            if (Yii::$app->user->isGuest) {
                throw new HttpException(401, Yii::t('app.error', 'Please login for this request.'));
            } else {
                throw new HttpException(423, Yii::t('app.error', 'You are not authorized to perform this action.'));
            }
        }
        $action = 'action'.ucfirst($action ? : 'load');
        $this->$action();
        return $this->_response;
    }

}