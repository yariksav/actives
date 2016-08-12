<?php
namespace yariksav\actives\view;

use yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\Exception;
use yariksav\actives\view\plugins\PluginMgr;

abstract class ActiveView extends ActiveObject
{
    protected $_plugins;

    public $method = 'init'; //!!!!!!!!!!!!!!!!

    protected $request;
    protected $response;
    protected $data;

    public $visible = true;

    function __construct($config = []) {
        $this->response = new \stdClass();
        $this->_plugins = new PluginMgr($this);

        $this->beforeInit();
        parent::__construct($config);
    }

    public function setPlugins($value) {
        $this->_plugins->load($value);
        //$this->_plugins->values($value);
    }

//    public function setPluginsInit($value) {
//        return $this->_plugins;
//        $this->_plugins->values($value);
//    }


    protected function renderOptions() {
        $this->response->plugins = $this->_plugins->build();
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