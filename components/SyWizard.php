<?php

namespace yariksav\actives\components;

use yii\helpers\ArrayHelper;

class SyWizard extends SyDialog{
    protected $step = 0;
    protected $stepNumeraion = true;
    protected $stepRemember = false;
    protected $nextStep;
    protected $allSteps;
    protected $steps;
    protected $visibleSteps;
    protected $showFinishButtonAlways = false;
    protected $stepNavigation = ['class'=>'nav nav-pills nav-justified'];

    function __construct($request){
        $this->step = ArrayHelper::getValue($request, 'step', 0);
        $this->nextStep = ArrayHelper::getValue($request, 'nextStep');
        parent::__construct($request);
        $this->data();
        $this->allSteps = $this->steps();
        $this->prepareSteps();
    }


    protected function getObjectProperty($step, $name, $default = null){
        if (is_array($step))
            if (isset($step[$name])) //$step instanceof SyDialog
                return is_callable($step[$name]) ? $step[$name]($this) : $step[$name];
            else return $default;
        else
            return method_exists($step, $name) ? $step->$name($this) : $step->$name;
    }

    protected function execute($step, $name){
        if (is_array($step))
            isset($step[$name]) ? $step[$name]($this) : $this->$name();
        else
            return $step->$name($this);
    }

    protected function loadDialog(){
        $this->beforeLoad();

        $this->renderOptions();
        $step = $this->getCurrentStep();
        //$this->getObjectProperty($step, 'beforeLoad');
        $this->getObjectProperty($step, 'data');

        //if (!$this->model)
            //	throw new Exception(Yii::t('error', 'Record not found'));

        $controls = $this->getObjectProperty($step, 'controls');

        $helper = new SyDialogConstructor($this->model);
        $controls = $helper->buildControls($controls);

        $this->response->controls = $controls;
        $this->response->buttons = $this->prepareButtons();
        $this->response->width = $this->getObjectProperty($step, 'width', $this->width); //isset($step['width']) ? $step['width'] : $this->width;
        $this->response->step = $this->step;
    }


    protected function prepareSteps(){
        if (!$this->allSteps)
            throw new Exception('Steps not found!');
        $ret = array();
        $this->request['parent']= $this->model;
        if ($this->allSteps) foreach($this->allSteps as $index=>$step){
            $visible = isset($step['visible']) ? $this->evaluateExpression($step['visible'], ['data'=>$this->model]) : true;
            if (!$visible)
                continue;

            if (isset($step['class'])){
                $this->request['class'] = $step['class'];
                $step = SyDialog::createInstance($this->request);
            }
            $ret[]=$step;
        }
        $this->steps = $ret;
        return $ret;
    }

    public function getCurrentStep(){

        if ($this->nextStep===null && $this->stepRemember){
            $steptitle = $this->getState('step');
            foreach($this->steps as $index=>$step){
                if ($step['title'] == $steptitle) {
                    $this->step = $index;
                    return $step;
                }
            }
        }

        $step = isset($this->steps[$this->step]) ? $this->steps[$this->step] : null;
        if (!$step)
            throw new \Exception('Step not found');

        if ($this->stepRemember)
            $this->setState('step', $step['title']);

        return $step;
    }

    public function renderOptions(){
        $this->response->steps = [];
        foreach($this->steps as $index=>$step){
            $this->response->steps[$index] = array('title'=>$step instanceof SyDialog ? $step->title : ArrayHelper::getValue($step, 'title', 'Step '.$index));
        }
        $this->response->stepNavigation = $this->stepNavigation;
        $this->response->closeOnEscape = $this->closeOnEscape;
        $this->response->stepNumeraion = $this->stepNumeraion;
        $this->response->isModal = $this->isModal;
        $this->response->showFinishButtonAlways = $this->showFinishButtonAlways;

    }

    protected function saveDialog(){
        $this->response->output = new \stdClass();
        $this->button = ArrayHelper::getValue($this->request, 'button');

        //	throw new Exception(Yii::t('error', 'You are not authorized to perform this action.'));
        if ($this->nextStep!== null && $this->step > $this->nextStep) {

            $this->step = $this->nextStep;
            $this->loadDialog();
            return;
        }

        $step = $this->getCurrentStep();


        $this->getObjectProperty($step, 'data');
        $this->execute($step, 'inputs');
        $this->execute($step, 'validate');
        if ($this->validation){
            $this->response->validation = $this->parseValidation($this->validation);
        }
        else{
            $this->execute($step, 'save');
            if ($this->validation){
                $this->response->validation = $this->parseValidation($this->validation);
            }
            else{
                $this->response->button = $this->button;
                if ($this->nextStep!== null) {
                    $this->response->output->id = $this->id;
                    $this->response->output->method = $this->method;
                    $this->step = $this->nextStep;
                    $this->loadDialog();
                }
            }
        }
    }

    public function buttons(){
    }

    protected function onControlData(){
        $step = $this->getCurrentStep();
        if (!$step)
            return;
        $controls = $this->getObjectProperty($step, 'controls');
        if (!$controls)
            throw new Exception('tag Controls was not found');
        $control = $this->findControlByField($controls, $this->request['control']);
        if (!$control)
            $control = $this->findControlByField($controls, $this->request['control'], 'type');
        if (!$control)
            throw new Exception('Control was not found');

        $this->response = $this->getControlData($control, ArrayHelper::getValue($this->request, 'data'));
    }
}