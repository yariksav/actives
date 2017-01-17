<?php
namespace yariksav\actives\view\plugins;

use yii;

class Search extends Plugin
{
    public $text;
    public $delay = 500;
    public $characters = 2;

    function init() {
        $this->registerEvents();
        parent::init();
    }

    public function build() {
        $res = parent::build();
        return array_merge($res, [
            'text'=>$this->text ? : Yii::t('actives', 'Search'),
            'value'=>$this->value,
            'characters' => $this->characters,
            'delay' => $this->delay
        ]);
    }

    protected function registerEvents() {
        if (is_callable($this->apply)) {
            $this->owner->on('beforeData', function ($event) {
                if ($this->getValue()) {
                    call_user_func_array($this->apply, [
                        $event->sender->provider,
                        $this->_value
                    ]);
                }
            });
        }
    }
}
