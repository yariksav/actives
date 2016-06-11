<?php

namespace yariksav\actives\dialog;

use yii;
use yii\helpers\ArrayHelper;
use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\PermissionTrait;
use yariksav\actives\base\VisibleTrait;
use yariksav\actives\base\Exception;
use yii\web\HttpException;

class StepDialog extends ActiveObject {

    use PermissionTrait;
    use VisibleTrait;

    // TODO: set visibility behavior by this types
    const DIALOG_TYPE_BASE = 1;
    const DIALOG_TYPE_CONSTRUCTOR = 2;
    const DIALOG_TYPE_GRIDS = 3;

    protected $_steps;
    public $action;
    public $type = self::DIALOG_TYPE_BASE;
    public $response;
    public $id;
    public $width;
    protected $isNewRecord;

    protected $stepNumeraion = false;
    protected $stepRemember = false;
//    protected $stepNavigation =

    function __construct($config = []){
        $this->response = new \stdClass();
        $this->action =  ArrayHelper::getValue($config, 'action', 'load');
        $this->id =  ArrayHelper::getValue($config, 'id');
        $this->isNewRecord = !$this->id;
        $this->_steps = Yii::createObject(array_merge($config, ['class' => StepMgr::className()]), [$this]);
        $this->_init();
        $this->_steps->stepNumeration = $this->stepNumeraion;
        $this->_steps->stepRemember = $this->stepRemember;
    }

    protected function _init(){

    }

    public function run() {
        if (!$this->visible) {
            if (Yii::$app->user->isGuest) {
                throw new HttpException(401, Yii::t('app.error', 'Please login for this request.'));
            } else {
                throw new HttpException(423, Yii::t('app.error', 'You are not authorized to perform this action.'));
            }
        }
        $method = 'on'.ucfirst($this->action);
        if (method_exists($this, $method)) {
            $this->$method();
            $this->renderOptions();
        } else {
            $this->mergeToResponse($this->_steps->current->run());
        }
        return $this->response;
    }


    public function renderOptions(){
        $this->response->steps = $this->_steps->links();
        //$this->response->stepNavigation = $this->stepNavigation;
        $this->response->current = $this->_steps->current->name;
        //$this->response->stepNumeraion = $this->stepNumeraion;
    }

    public function setSteps(array $value) {
        $buttonNext = in_array($this->type, [self::DIALOG_TYPE_BASE, self::DIALOG_TYPE_CONSTRUCTOR]);
        $buttonPrev = in_array($this->type, [self::DIALOG_TYPE_BASE]);
        $this->_steps->load($value);
        $this->_steps->addPrevNext($buttonPrev, $buttonNext);
    }

    protected function onLoad(){
        $step = $this->_steps->current;
        if (!$step->id) {
            $step->id = $this->id;
        }
        if ($this->width) {
            $step->width = $this->width;
        }
        $step->action = 'load';
        $this->mergeToResponse($step->run());
        $this->renderOptions();
    }

    protected function onNavigation() {
        $this->saveCurrent();
        $this->_steps->navigate($this->_steps->next);
        $this->onLoad();
    }

    protected function onPrevious(){
        if ($this->_steps->previous()) {
            $this->onLoad();
        }
    }

    protected function onNext(){
        $this->saveCurrent();
        if ($this->_steps->next()) {
            $this->onLoad();
        }
    }

    protected function saveCurrent() {
        $step = $this->_steps->current;
        $step->action = 'save';
        $this->response = $step->run();
        $this->id = $step->id;
    }


    protected function mergeToResponse($obj){
        foreach ($obj as $k => $v) {
            $this->response->$k = $v;
        }
    }
}