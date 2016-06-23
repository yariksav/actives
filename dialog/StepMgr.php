<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 23.05.2016
 * Time: 21:12
 */

namespace yariksav\actives\dialog;

use yii;
use yii\base\Exception;
use yii\base\Object;
use yariksav\actives\base\CollectionMgr;
use yariksav\actives\base\ActiveObject;
use yii\helpers\ArrayHelper;

class StepMgr extends CollectionMgr
{
    public $config = [];
    public $next;
    public $stepNumeration = false;
    public $stepRemember = false;
    protected $currentStep;

    function __construct($owner, $config = []) {
        $conf = [
            'current'=>ArrayHelper::getValue($config, 'current'),
            'next'=>ArrayHelper::getValue($config, 'next'),
        ];
        unset($config['current'], $config['next']);

        $conf['config'] = $config;
        parent::__construct($owner, $conf);
    }

    protected function createObject($item) {
        //create dialog component

        if (empty($item['name']) || is_int($item['name'])) {
            throw new \Exception('Please get the name for step');
        }

        $step = ActiveObject::createObject(array_merge($item, $this->config));
        if ($step && $step->visible) {
            return $step;
        }
    }

    public function links() {
        $links = [];
        $index = 0;
        foreach($this->_collection as $name => $step) {
            $index++;
            $title = ($this->stepNumeration ? $index . '. ' : '').($step->title ? $step->title : ucfirst($step->name));
            $links[$name] = [
                'title' => $title,
                'current' => $name === $this->_current
            ];
        }
        return $links;
    }

    public function getCurrent() {
        if (!$this->currentStep) {
            if (isset($this->_collection[$this->_current])) {
                return $this->currentStep = $this->_collection[$this->_current];
            }
            $step = null;
            if ($this->stepRemember) {
                $path = get_class($this->owner).':step';
                $step = Yii::$app->session->get(get_class($this->owner).':step');
            }
            if (empty($this->_collection[$step])) {
                // get first step
                $step = array_keys($this->_collection)[0];
            }
            $this->currentStep = $this->_collection[$step];
            $this->_current = $this->currentStep->name;
        }
        return $this->currentStep;
    }

    public function setCurrent($value) {
        $this->_current = $value;
        $this->currentStep = null;
    }

    protected function rememberStep(){
        if ($this->stepRemember && $this->_current) {
            Yii::$app->session->set(get_class($this->owner).':step', $this->_current);
        }
    }

    public function next() {
        $keys = array_keys($this->_collection);
        $found = array_search($this->_current, $keys);
        if ($found >= 0 && $found < count($keys) - 1) {
            $this->current = $keys[$found + 1];
            $this->rememberStep();
            return true;
        }
        return false;
    }

    public function previous() {
        $keys = array_keys($this->_collection);
        $found = array_search($this->_current, $keys);
        if ($found > 0) {
            $this->current = $keys[$found - 1];
            $this->rememberStep();
            return true;
        }
        return false;
    }

    public function navigate($toStep) {
        if (empty($this->_collection[$toStep])) {
            throw new \Exception('Step not found');
        }
        $this->current = $toStep;
        $this->rememberStep();
    }

    public function addPrevNext($prev, $next) {
        $count = count($this->_collection);
        $currentIndex = 0;
        foreach ($this->_collection as $name=>$step) {
            if ($currentIndex > 0 && $prev) {
                $step->actions = [
                    'previous'=>[
                        'type'=>'button',
                        'icon'=>'fa fa-caret-left'
                    ],
                    '*'
                ];
            }
            if ($currentIndex < $count - 1 && $next) {
                $step->actions = [
                    '*',
                    'next'=>[
                        'type'=>'button',
                        'iconright'=>'fa fa-caret-right'
                    ]
                ];
            }
            $currentIndex++;
        }
    }

}