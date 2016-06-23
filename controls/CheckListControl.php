<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 22.05.2016
 * Time: 10:28
 */

namespace yariksav\actives\controls;

use yii\helpers\ArrayHelper;

class CheckListControl extends CollectionControl
{


    public $selected;

    /**
     * @inheritdoc
     */
    public function init(){
        $this->type = 'checklist';
    }

    public function getCollection() {
        $collection = [];
        if (is_callable($this->_collection)) {
            $collection = call_user_func_array($this->_collection, [
                'data' => $this->_model,
                'owner' => $this->owner
            ]);
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
        //TODO Add render INFO variable
        return $collection;
    }


    /**
     * @inheritdoc
     */
    public function getValue() {
        $value = parent::getValue();
        if ($value && isset($this->fields['selected'])) {
            $value = array_values(ArrayHelper::getColumn($value, $this->fields['selected']));
        }
            if (!$value)
            $value = $this->default;
        return $value;
    }


}