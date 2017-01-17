<?php

namespace yariksav\actives\base;

use yii;
/**
 * Owned Trait
 * @property Object $owned
 * @package yariksav\actives\base
 */
trait OwnedTrait
{
    /**
     * @var  Object owner of this element
     */
    protected $_owner;

    public function getOwner() {
        return $this->_owner;
    }

    public function setOwner($value) {
        $this->_owner = $value;
    }

}