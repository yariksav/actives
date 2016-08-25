<?php
namespace yariksav\actives\view;

use yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\Exception;
use yariksav\actives\view\plugins\PluginMgr;
use yariksav\actives\view\buttons\ButtonMgr;

abstract class ActiveView extends ActiveObject
{
    protected $_plugins;
    protected $_buttons;


    public $baseModel;// base model data if grid linked to some model

    public $componentName;
    public $method = 'init'; //!!!!!!!!!!!!!!!!

    protected $request;
    protected $response;
    //protected $data;

    public $visible = true;

    function __construct($config = []) {
        $this->response = new \stdClass();
        $this->_plugins = new PluginMgr($this);
        $this->_buttons = new ButtonMgr($this);
        $this->name = get_called_class();
        $this->beforeInit();
        parent::__construct($config);
    }

    function init() {
        if ($this->_provider && $this->_plugins) {
            $this->_plugins->setProvider($this->_provider);
        }
    }
    public function actionInit(){
        $this->renderItems();
        $this->renderOptions();
    }

    public function actionLoad(){
        $this->renderItems();
    }

    public function renderItems() {

    }

    public function setPlugins($value) {
        $this->_plugins->load($value);
    }

    public function setButtons($value) {
        $this->_buttons->load($value);
    }

    protected function renderOptions() {
        $this->response->plugins = $this->_plugins->build();

        if ($this->title) {
            $this->response->title = $this->title;
        }
        $this->response->name = $this->name;
        $this->response->url = Url::toRoute('/actives/api/grid');

        $this->response->buttons = $this->_buttons->build();
        $this->response->listens = $this->listens;

        $this->response->params = new \stdClass();
        $this->response->params->class = $this->className();
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

    public static function widget($config){
        $instance = self::createObject($config);
        $response = $instance->run();
        return Html::tag('div', '', [
            'class' => $instance->name,
            'data-cmp' => $instance->componentName,
            'data-json-config' => json_encode($response),
            //'data-gzip-config' => base64_encode(gzencode(json_encode($response)))
        ]);
        return $response;
    }

    protected function renderAction(){
        $action = 'action'.$this->method;

        if (!method_exists($this, $action))
            throw new Exception('Method '.$action.' not exists');

        if ($this->method == 'export'){
            $this->$action();
            return null;
        } else {
            if (\Yii::$app->request->isAjax) {
                header('Content-type: application/json');
            }
            $this->$action();
        }
    }

//    protected function _wrap($data, $view){
//        return json_encode($data);
//    }


}