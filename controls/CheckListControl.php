<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 22.05.2016
 * Time: 10:28
 */

namespace yariksav\actives\controls;

use yii\helpers\ArrayHelper;

class CheckListControl extends SelectionControl
{


    public $selected;

    /**
     * @inheritdoc
     */
    public function init(){
        $this->type = 'checklist';
    }

    public function getSelection() {
        $selection= [];
        if (is_callable($this->_selection)) {
            $selection = call_user_func_array($this->_selection, [
                'data' => $this->_model,
                'activeObject' => $this->_activeObject
            ]);
        }
        // render selection to proper format
        if (isset($this->fields) && is_array($this->fields)) {
            $selection = ArrayHelper::map(
                $selection,
                ArrayHelper::getValue($this->fields, 0, 'id'),
                ArrayHelper::getValue($this->fields, 1, 'name'),
                ArrayHelper::getValue($this->fields, 2)
            );
        }
        //TODO Add render INFO variable
        return $selection;
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