<?php
namespace yariksav\actives\view\plugins;

use yii;

class SearchPlugin extends Plugin
{
    public $text;
    public $delay = 500;
    public $characters = 2;

    public function build() {
        if (!$this->text) {
            $this->text = Yii::t('actives', 'Search');
        }
        return [
            'name'=>$this->name,
            'text'=>$this->text,
            'characters' => $this->characters,
            'delay' => $this->delay
        ];
    }
}
