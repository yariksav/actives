<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 22.05.2016
 * Time: 12:22
 */

namespace yariksav\actives\controls;

class CollectionControl extends Control {

    public $fields;
    /**
     * @var array the list of items
     */
    protected $_collection;

    /**
     * Returns the list of items.
     */
    public function getCollection() {
        return $this->_collection;
    }

    /**
     * Sets the list of items.
     */
    public function setCollection($value) {
        $this->_collection = $value;
    }

    /**
     * @inheritdoc
     */
    public function load() {
        return [
            'collection'=>$this->getCollection()
        ];
    }
}
