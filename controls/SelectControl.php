<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 22.05.2016
 * Time: 20:36
 */

namespace yariksav\actives\controls;

use yii\helpers\ArrayHelper;

class SelectControl extends CollectionControl {

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

        // render collection to proper format
        if (isset($this->fields) && is_array($this->fields)) {
            $collection = ArrayHelper::map(
                $collection,
                ArrayHelper::getValue($this->fields, 0, 'id'),
                ArrayHelper::getValue($this->fields, 1, 'name'),
                ArrayHelper::getValue($this->fields, 2)
            );
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