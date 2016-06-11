<?php
namespace yariksav\actives\view\buttons;


class RowButton extends Button
{
    public $in;

    public function build($model) {
        $ret = parent::build($model);
        if ($this->in) {
            $ret['in'] = $this->in;
        }
        return $ret;
    }

}
