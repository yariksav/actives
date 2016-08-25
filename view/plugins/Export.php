<?php
namespace yariksav\actives\view\plugins;

use yii;
use yariksav\actives\view\exports\ExportMgr;

class Export extends Plugin
{
    public $_exports;

    function __construct($owner, $config = []) {
        $this->_exports = new ExportMgr($owner);
        parent::__construct($owner, $config);
    }

    public function setCollection($value) {
        $this->_exports->load($value);
    }

    public function build() {
        $res = parent::build();
        return array_merge($res, [
            'collection'=>$this->_exports->build()
        ]);
    }

    public function setCurrent($value) {
        $this->_exports->setCurrent($value);
    }

    public function getCurrent() {
        return $this->_exports->getCurrent();
    }
}
