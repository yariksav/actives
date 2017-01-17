<?php
namespace yariksav\actives\view\plugins;

use yii;

class Pagination extends Plugin
{
    protected $_pageSize;
    protected $_page = 0;

    public $collection = [
        5=>'5',
        20=>'20',
        100=>'100',
        //-1=>'All'
    ];

    function init() {
        $this->registerEvents();
        parent::init();
    }

    protected function registerEvents() {
        $this->owner->on('beforeData', function ($event) {
            $owner = $event->sender;
            $provider = $owner->provider;
            if ($this->pageSize > 0) {
                $provider->pagination->pageSize = $this->pageSize;
                $provider->pagination->page = $this->page;
            } else {
                $provider->pagination = false;
            }
        }, [$this]);
    }

    public function build() {
        return array_merge(parent::build(), [
            'collection' => $this->collection,
            'pageSize' => $this->pageSize,
        ]);
    }

    public function getPage() {
        return $this->_page;
    }

    public function setPage($value) {
        $this->_page = $value;
    }

    public function getPageSize() {
        if (!$this->_pageSize) {
            $this->_pageSize = $this->owner->getState($this->owner->className().'size', array_keys($this->collection)[0]);
        }
        return $this->_pageSize;
    }

    public function setPageSize($value) {
        if (isset($this->collection[$value])) {
            $this->_pageSize = $value;
        }
    }

    public function setPageSizeChanged($value) {
        if (isset($this->collection[$value]) && $value > 0) {
            $this->owner->setState($this->owner->className() . 'size', $value);
        }
    }
}
