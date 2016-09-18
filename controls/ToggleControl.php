<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 22.05.2016
 * Time: 12:02
 */

namespace yariksav\actives\controls;


class ToggleControl extends CollectionControl {

    public function init() {
        $this->type = 'toggler';
    }

    public function build() {
        $ret = parent::build();
        if ($ret['value'] === null && $ret['collection']) {
            $ret['value'] = array_keys($ret['collection'])[0];
        }
        return $ret;
    }

    public function update($value) {
        $keys = array_keys($this->_collection);
        $key = array_search($value, $keys);
        parent::update($keys[$key]);
    }
}