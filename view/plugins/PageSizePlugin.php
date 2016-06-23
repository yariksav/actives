<?php
namespace yariksav\actives\view\plugins;

use yii;

class PageSizePlugin extends Plugin
{
    public $text;
    public $button = 'link';
    public $collection = [
        20=>'20',
        50=>'50',
        100=>'100'
    ];

    function init() {
        if (!$this->text) {
            $this->text = Yii::t('actives', 'Show <span></span> elements');
        }
        parent::init();
    }

    public function build() {
        $res = parent::build();
        $res['text'] = $this->text;
        $res['button'] = $this->button;
        $res['collection'] = $this->collection;
        return $res;
    }
}
