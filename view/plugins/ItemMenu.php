<?php
namespace yariksav\actives\view\plugins;

use yariksav\actives\view\buttons\ButtonMgr;
use yii;


class ItemMenu extends Plugin
{
    public $buildItem;
    protected $_actions;

    function __construct($owner, $config = []) {
        $this->_actions = new ButtonMgr();
        $this->buildItem = function($model) {
            return [
                'actions'=>$this->_actions->build($model),
            ];
        };
        parent::__construct($owner, $config);
    }

    public function setActions($value){
        $this->_actions->load($value);
    }

}
