<?php
namespace yariksav\actives\view\plugins;

use yariksav\actives\controls\ControlMgr;
use yii;

class Filter extends Plugin
{
    protected $_controls;
    protected $_filters;

    function __construct($owner, $config = []) {
        $this->_controls = new ControlMgr($owner);
        parent::__construct($owner, $config);
        $this->_controls->model = $this->value;
        $this->registerEvents();
    }

    protected function registerEvents() {
        $this->owner->on('beforeData', function ($event) {
            $values = $this->value;
            $provider = $event->sender->provider;
            foreach($this->_filters as $key=>$filter) {
                if (isset($values[$key]) && $values[$key] !== '') {
                    call_user_func($filter, $provider, $values[$key]);
                }
            }
        });
    }

    function setControls ($controls) {
        foreach($controls as $key=>&$control) {
            if (isset($control['apply'])) {
                $this->_filters[$key] = $control['apply'];
                unset($control['apply']);
            }
        }
        $this->_controls->load($controls);
    }

    public function setValue($value) {
        parent::setValue($value);
        $this->_controls->model = $value;
    }

    public function build() {
        $ret = parent::build();
        return array_merge($ret, [
            'controls' => $this->_controls->build()
        ]);
    }

}
