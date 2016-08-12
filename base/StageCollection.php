<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 23.05.2016
 * Time: 21:12
 */

namespace yariksav\actives\base;

use yii;
use yii\base\Object;

class StageCollection extends Object
{
    //protected $_current;
    protected $_collection;
    protected $owner;

    function __construct($owner = null, $config = []) {
        parent::__construct($config);
        $this->owner = $owner;
        $this->_collection = [];
    }

    protected function createItem($item) {
        return Yii::createObject($item, [
            $this->owner
        ]);
    }

    /**
     * Prepare and merge actions for particular object
     * @param array $value Items options
     * @return array List of prepared object for particular active object
     */
    public function load(array $newItems) {
        if (!$newItems || !is_array($newItems)) {
            return;
        }

        foreach ($newItems as $name => $item) {
            if (isset($this->_collection[$name])) {
                if ($item === false) {
                    unset($this->_collection[$name]);
                } else if (is_array($item)) {
                    Yii::configure($this->_collection[$name], $item);
                }
                continue;
            }

            if (is_string($item) && is_int($name)) {
                if(!preg_match('/^([\w\.]+)(:(\w*))?(:(.*))?$/', $item, $matches)) {
                    throw new yii\base\Exception(Yii::t('zii', 'The column must be specified in the format of "Name:Type:Label", where "Type" and "Label" are optional.'));
                }
                $name = $matches[1];
                $item = [];
                if(isset($matches[3]) && $matches[3] !== '') {
                    $item['type'] = $matches[3];
                }
            }

            if (is_array($item)) {
                // if only necessary to add params
                if (empty($item['name'])) {
                    $item['name'] = $name;
                }
            }

            $instance = $this->createObject($item);
            if ($instance) {
                $this->_collection[$name] = $instance;
            }
        }
    }
//
//    public function get($name) {
//        return isset($this->_collection[$name]) ? $this->_collection[$name] : null;
//    }
//
//    public function getCurrent() {
//        if (isset($this->_collection[$this->_current])) {
//            return $this->_collection[$this->_current];
//        }
//        return null;
//    }
//
//    public function setCurrent($value) {
//        $this->_current = $value;
//    }
//
//    protected function render(){
//        $result = [];
//        if ($this->_collection) foreach ($this->_collection as $name=>$item) {
//
//            if (!$item->visible || !$item->hasPermissions()) {
//                continue;
//            }
//
//            $result[$name] = $item->build();
//        }
//        return $result;
//    }
}