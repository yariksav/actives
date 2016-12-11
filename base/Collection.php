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
use yii\helpers\ArrayHelper;

class Collection extends Object implements \Iterator
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
            // if item exist - configure or remove it
            if (isset($this->_collection[$name])) {
                if ($item === false) {
                    unset($this->_collection[$name]);
                } else if (is_array($item)) {
                    Yii::configure($this->_collection[$name], $item);
                }
                continue;
            }

            // maintain simple string param
            if (is_string($item) && is_int($name)) {
                if(!preg_match('/^([\w\.]+)(:(\w*))?(:(.*))?$/', $item, $matches)) {
                    throw new yii\base\Exception(Yii::t('zii', 'The column must be specified in the format of "Name:Type:Label", where "Type" and "Label" are optional.'));
                }
                $name = $matches[1];
                $item = [];
                if(isset($matches[3]) && $matches[3] !== '') {
                    $item['type'] = $matches[3];
                }
            } else if (is_string($item) && is_string($name)) {
                $item = ['class'=>$item];
            }

            if (is_array($item)) {
                // if only necessary to add params
                if (empty($item['name'])) {
                    $item['name'] = $name;
                }
            }

            $position = ArrayHelper::remove($item, '~position');

            if ($instance = $this->createObject($item)) {
                if ($position !== null) {
                    $insert = [];
                    $insert[$name] = $instance;
                    if (is_string($position)) {
                        $position = array_search($position, array_keys($this->_collection));
                    }
                    $this->_collection = array_merge(
                        array_slice($this->_collection, 0, $position),
                        $insert,
                        array_slice($this->_collection, $position)
                    );

                    array_splice( $this->_collection, $position, 0, $new );
                } else {
                    $this->_collection[$name] = $instance;
                }
            }
        }
    }

    public function get($name) {
        return isset($this->_collection[$name]) ? $this->_collection[$name] : null;
    }

    public function clear() {
        $this->_collection = [];
    }

    public function rewind()
    {
        reset($this->_collection);
    }

    public function current()
    {
        return current($this->_collection);
    }

    public function key()
    {
        return key($this->_collection);
    }

    public function next()
    {
        return next($this->_collection);
    }

    public function valid()
    {
        $key = key($this->_collection);
        return ($key !== NULL && $key !== FALSE);
    }


//reset($List);
//while (key($List) !== $id && key($List) !== null) next($List);
//if(key($List) === null) end($List);

}