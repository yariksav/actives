<?php
namespace yariksav\actives\view\plugins;

use yii;

class Pagination extends Plugin
{
    protected $_pageSize;
    public $text;
    public $button = 'link';
    public $page = 1;
    public $pageCount = 5;

    public $collection = [
        5=>'5',
        20=>'20',
        50=>'50',
        100=>'100'
    ];

    function init() {
        if (!$this->_pageSize) {
            $this->_pageSize = array_keys($this->collection)[0];
        }
        if (!$this->text) {
            $this->text = Yii::t('actives', 'Show <span></span> elements');
        }
        parent::init();
    }

    public function build() {
        return array_merge(parent::build(), [
            'text' => $this->text,
            'button' => $this->button,
            'collection' => $this->collection,
            'pageSize' => $this->pageSize,
            'pageCount' => $this->pageCount,
            'page' => $this->page
        ]);
    }

    public function setProvider($provider) {
        $provider->pagination->pageSize = $this->pageSize;
        $provider->pagination->page = $this->page - 1;
    }

    public function getPageSize() {
        if ($this->owner->method === 'init') {
            $this->_pageSize = $this->owner->getState($this->name.'size', $this->_pageSize);
        }
        return $this->_pageSize;
    }

    public function setPageSize($value) {
        $this->_pageSize = $value;
        if ($this->owner->method === 'load') {
            $this->owner->setState($this->name.'size', $value);
        }
    }
}
