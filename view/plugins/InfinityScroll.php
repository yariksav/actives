<?php
namespace yariksav\actives\view\plugins;

use yii;

class InfinityScroll extends Plugin
{
    public $pageSize = 20;
    public $page = 0;
    
    function init() {
        $this->registerEvents();
        parent::init();
    }

    protected function registerEvents() {
        $this->owner->on('beforeData', function ($event) {
            $owner = $event->sender;
            $provider = $owner->provider;
            $provider->pagination->pageSize = $this->pageSize;
            $provider->pagination->page = $this->page;
        }, [$this]);
    }

    public function build() {
        return array_merge(parent::build(), [
            'pageSize' => $this->pageSize,
        ]);
    }

/*    public function getPageSize() {
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
    }*/
}
