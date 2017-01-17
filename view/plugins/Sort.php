<?php
namespace yariksav\actives\view\plugins;

use yii;
use yii\db\Expression;
use yii\db\ActiveQuery;

class Sort extends Plugin
{

    public $columns = [];//todo split logic Sort

    function init() {
        $this->registerEvents();
        parent::init();
    }

    public function build() {
        return array_merge(parent::build(), [
            'value'=>$this->value
        ]);
    }

    protected function registerEvents() {
        //todo!!!
//        if (is_callable($this->apply)) {
//            $this->owner->on('beforeData', function ($event) {
//                if ($this->getValue()) {
//                    call_user_func_array($this->apply, [
//                        $event->sender->provider,
//                        $this->_value
//                    ]);
//                }
//            });
//        }
    }

/*    public function setProvider($provider) {
        if ($this->apply) {
            parent::setProvider($query);
        } else {
            //TODO перенести на  public function setAttributeOrders($attributeOrders, $validate = true) в версии ншш 2.0.10
//            if (is_array($this->value) && $query instanceof ActiveQuery) {
//                foreach ($this->value as $key => $value) {
//                    $query->addOrderBy(new Expression($key . ' ' . $value));
//                }
//            } else {
                $sort = [];
                if ($this->value) {
                    foreach ($this->value as $key => $value) {
                        $sort[] = ($value == 'desc' ? '-' : '') . $key;
                    }
                }
                $sort = implode(',', $sort);
                if ($sort) {
                    $_GET['sort'] = $sort;
                }
           // }
        }
    }*/
}
