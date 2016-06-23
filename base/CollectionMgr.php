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

class CollectionMgr extends Object
{
    protected $_current;
    protected $_collection;
    protected $owner;

    function __construct($owner, $config = []) {
        parent::__construct($config);
        $this->owner = $owner;
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

        $collection = [];
        foreach ($newItems as $name => $item) {
            if ($item === '*') {
                // if exists char "*" than load previous steps
                if ($this->_collection) {
                    foreach ($this->_collection as $key=>$existedItem) {
                        $collection[$key] = $existedItem;
                    }
                }
            } else if (is_string($item) && is_int($name)) {
                if(!preg_match('/^([\w\.]+)(:(\w*))?(:(.*))?$/', $item, $matches)) {
                    throw new CException(Yii::t('zii', 'The column must be specified in the format of "Name:Type:Label", where "Type" and "Label" are optional.'));
                }
                $name = $matches[1];
                $item = [];
                if(isset($matches[3]) && $matches[3] !== '') {
                    $item['type'] = $matches[3];
                }
                if(isset($matches[5])) {
                    $item['text'] = $matches[5];
                }
            }

            if (is_array($item)) {
                // if only necessary to add params
                if (substr($name, 0, 1) === '+') {
                    $name = substr($name, 1, strlen($name) - 1);
                    $instance = $this->_collection[$name];
                    Yii::configure($instance, $item);
                } else { // create new component
                    $item['name'] = $name;
                    $instance = $this->createObject($item);
                }

                if ($instance) {
                    $collection[$name] = $instance;
                }
            }
            // Remove control if returns false
            if (!$item && $name && (isset($collection[$name]) || isset($this->_collection[$name]))) {
                unset($collection[$name]);
                unset($this->_collection[$name]);
            }
        }

        $this->_collection = $collection;
        return $this->_collection;
    }
    
    public function each($callback) {
        if ($this->_columns) foreach($this->_columns as $name => $col) {
            call_user_func_array($callback, [$name, $col]);
        }
    }

    public function get($name) {
        return isset($this->_collection[$name]) ? $this->_collection[$name] : null;
    }

    public function getCurrent() {
        if (isset($this->_collection[$this->_current])) {
            return $this->_collection[$this->_current];
        }
        return null;
    }

    public function setCurrent($value) {
        $this->_current = $value;
    }
}