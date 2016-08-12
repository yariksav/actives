<?php
namespace yariksav\actives\view\plugins;

use yii;

class SearchPlugin extends Plugin
{
    public $text;
    public $delay = 500;
    public $characters = 2;

    public function build() {
        $res = parent::build();
        return array_merge($res, [
            'text'=>$this->text ? : Yii::t('actives', 'Search'),
            'value'=>$this->value,
            'characters' => $this->characters,
            'delay' => $this->delay
        ]);
    }
}
