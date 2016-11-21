<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 22.05.2016
 * Time: 10:28
 */

namespace yariksav\actives\controls;

use yii\helpers\ArrayHelper;

class CheckListControl extends SelectControl
{

    public $selected;

    /**
     * @inheritdoc
     */
    public function init(){
        $this->type = 'checklist';
    }


    /**
     * @inheritdoc
     */
    public function getValue() {
        $value = parent::getValue();
//        if ($value && isset($this->fields['selected'])) {
//            $value = array_values(ArrayHelper::getColumn($value, $this->fields['selected']));
//        }
        if (!$value) {
            $value = $this->default;
        }
        return $value;
    }

}