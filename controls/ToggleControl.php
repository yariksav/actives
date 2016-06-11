<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 22.05.2016
 * Time: 12:02
 */

namespace yariksav\actives\controls;


class ToggleControl extends SelectionControl {

    public function init() {
        $this->type = 'toggler';
    }
}