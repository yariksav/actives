<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 22.05.2016
 * Time: 20:36
 */

namespace yariksav\actives\controls;

use yii\db\Query;
use yii\helpers\ArrayHelper;

class SelectControl extends CollectionControl {

    /*
     * @var string sets name of key field. If not asset - collection must be key=>value associative array
     */
    public $key;
    /**
     * @var string|\Closure sets the name of item field, or render in closure.
     */
    public $item;
    /**
     * @var string|\Closure the group field in model or value from function
     */
    public $group;


    public $button;

    /*
     * @var bool is select can work with empty result
     */
    public $empty = false;

    /**
     * @inheritdoc
     */
    public function init(){
        $this->type = 'select';
    }

    /**
     * @inheritdoc
     */
    public function getCollection() {
        if (is_callable($this->_collection)) {
            $collection = call_user_func_array($this->_collection, [
                'data' => $this->_model,
                'owner' => $this->owner
            ]);
        } else if (is_array($this->_collection)){
            $collection = $this->_collection;
        }

        if ($collection instanceof Query) {
            $collection = $collection->all();
        }
        // render collection to proper format
        if ($this->key && $this->item) {
            $collection = ArrayHelper::map($collection, $this->key, $this->item, $this->group);
        }
        return $collection;
    }

    /**
     * @inheritdoc
     */
    public function build() {
        $control = parent::build();
        if ($this->button) {
            $control['button'] = $this->button;
        }
        if ($this->empty) {
            $control['empty'] = $this->empty;
        }
        return $control;
    }
}