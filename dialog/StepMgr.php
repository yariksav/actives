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
use yii\helpers\ArrayHelper;
use yariksav\actives\base\Collection;
use yariksav\actives\base\ActiveObject;

class StepMgr extends Collection
{
    protected $_config = [];
    public $next;
    public $numeration = false;
    public $navigation = true;
    public $remember = false;

    protected $currentStep;
    protected $_current;

    function __construct($owner, $config = []) {
        $conf = [
            'current'=>ArrayHelper::getValue($config, 'current'),
            'next'=>ArrayHelper::getValue($config, 'next'),
        ];
        unset($config['current'], $config['next']);
        $this->_config = $config;

        if (in_array($this->_config['action'], ['navigation', 'next', 'previous'])) {
            $this->_config['action'] = 'load';
        }
        parent::__construct($owner, $conf);
    }

    protected function createObject($item) {
        //create dialog component

        if (empty($item['name']) || is_int($item['name'])) {
            throw new \Exception('Please get the name for step');
        }

        $step = ActiveObject::createObject(array_merge($item, $this->_config));
        if ($step && $step->visible) { //todo add  && $step->privilege
            return $step;
        }
    }

    public function links() {
        $links = [];
        $index = 0;
        foreach($this->_collection as $name => $step) {
            $index++;
            $title = ($this->numeration ? $index . '. ' : '').($step->title ? $step->title : ucfirst($step->name));
            $links[$name] = [
                'text' => $title,
                'active' => $name === $this->_current
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
            if ($this->remember) {
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
        if ($this->remember && $this->_current) {
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
                $step->setActions([
                    'previous'=>[
                        'type'=>'button',
                        '~position'=>0,
                        'icon'=>'fa fa-caret-left'
                    ]
                ]);
            }
            if ($currentIndex < $count - 1 && $next) {
                $step->setActions([
                    'next'=>[
                        'type'=>'button',
                        'iconright'=>'fa fa-caret-right'
                    ]
                ]);
            }
            $currentIndex++;
        }
    }

}