<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 22.05.2016
 * Time: 12:22
 */

namespace yariksav\actives\controls;


class SelectionControl extends Control {

    public $fields;
    protected $_selection;

    public function getSelection() {
        return $this->_selection;
    }

    public function setSelection($value) {
        $this->_selection = $value;
    }

    /**
     * @inheritdoc
     */
    public function build() {
        $control = parent::build();
        $control['selection'] = $this->selection;
        return $control;
    }
}
