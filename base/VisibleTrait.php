<?php

namespace yariksav\actives\base;

use yii;
use Closure;
/**
 * Trait Visible Trait
 * @property string|array|bool $permissions
 * @package yariksav\actives\base
 */
trait VisibleTrait
{
    /**
     * @var bool|Closure necessary permissions for object
     */
    protected $_visible = true;

    /**
     * Checks is component visible. Calculates only one time for perfomance
     * @return boolean
     */

    public function getVisible() {
        if (is_callable($this->_visible)) {
            $this->_visible = call_user_func_array($this->_visible, []);
        }
        return $this->_visible;
    }

    public function setVisible($value) {
        $this->_visible = $value;
    }
}