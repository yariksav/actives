<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 22.05.2016
 * Time: 20:36
 */

namespace yariksav\actives\controls;

use yii\helpers\ArrayHelper;

class SelectControl extends SelectionControl {

    public $button;
    public $empty = false;
    /**
     * @inheritdoc
     */
    public function init(){
        $this->type = 'select';
    }

    public function getSelection() {
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
        return $selection;
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