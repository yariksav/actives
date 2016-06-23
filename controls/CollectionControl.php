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
    protected $_collection;

    public function getCollection() {
        return $this->_collection;
    }

    public function setCollection($value) {
        $this->_collection = $value;
    }

    /**
     * @inheritdoc
     */
    public function build() {
        $control = parent::build();
        $control['collection'] = $this->collection;
        return $control;
    }
}
