<?php

namespace yariksav\actives\dialog;

use yii;
use yii\helpers\ArrayHelper;
use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\PermissionTrait;
use yariksav\actives\base\VisibleTrait;
use yariksav\actives\base\Exception;
use yii\web\HttpException;

class StepDialog extends BaseDialog {

    // TODO: set visibility behavior by this types
    const DIALOG_TYPE_BASE = 1;
    const DIALOG_TYPE_CONSTRUCTOR = 2;
    const DIALOG_TYPE_GRIDS = 3;

    protected $_steps;
    public $type = self::DIALOG_TYPE_BASE;

    function __construct($config = []) {
        $this->_steps = Yii::createObject(array_merge($config, [
            'class' => StepMgr::className()
        ]), [$this]);

        parent::__construct([
            'key' => ArrayHelper::getValue($config, 'key'),
            'action' => ArrayHelper::getValue($config, 'action', 'load')
        ]);
    }

    public function run() {
        parent::run();

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
        $this->response->current = $this->_steps->current->name;
        $this->response->stepNavigation = $this->_steps->navigation;
        $this->response->stepNumeration = $this->_steps->numeration;
    }

    public function getSteps() {
        return $this->_steps;
    }

    public function setSteps(array $value) {
        $buttonNext = in_array($this->type, [self::DIALOG_TYPE_BASE, self::DIALOG_TYPE_CONSTRUCTOR]);
        $buttonPrev = in_array($this->type, [self::DIALOG_TYPE_BASE]);
        $this->_steps->load($value);
        $this->_steps->addPrevNext($buttonPrev, $buttonNext);
    }

    protected function onLoad(){
        $step = $this->_steps->current;
        if (!$step->key) {
            $step->key = $this->key;
        }
        if (!$step->width && $this->width) {
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
        $this->key = $step->key;
    }


    protected function mergeToResponse($obj){
        foreach ($obj as $k => $v) {
            $this->response->$k = $v;
        }
    }
}