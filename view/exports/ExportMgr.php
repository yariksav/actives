<?php

namespace yariksav\actives\view\filters;

use yii;
use yii\base;
use yii\base\Object;
use yariksav\actives\base\CollectionMgr;
use yariksav\actives\base\Component;


class ExportMgr extends CollectionMgr
{

    public static $builtInColumns = [
        'row' => 'yariksav\actives\view\buttons\RowButton'
    ];

    protected function createObject($params) {
        if (empty($params['name']) || is_int($params['name'])) {
            throw new \Exception('Please get the name for control');
        }

        if (empty($params['type']) && empty($params['class'])) {
            $params['class'] = Button::className();
        }

        if (isset($params['type'])) {
            $type = $params['type'];
            if (isset(static::$builtInColumns[$type])) {
                $type = static::$builtInColumns[$type];
            }
            if (is_array($type)) {
                $params = array_merge($type, $params);
            } else {
                $params['class'] = $type;
            }
        }
        return Yii::createObject($params, [
            $this->owner
        ]);
    }

    protected function prepareButtons(){
        $this->_buttons = isset($this->buttons) ? $this->evaluateExpression($this->buttons, ['grid'=>$this]) : $this->buttons();
    }

    public function build() {
        return $this->renderButtons();
    }

    public function buildRow($data) {
        return $this->renderButtons($data);
    }

    protected function renderButtons($model = false){
        $result = [];
        if ($this->_collection) foreach ($this->_collection as $name=>$button) {

            if ($model === false && $button instanceof RowButton) {
                continue;
            }
            if ($model !== false && !($button instanceof RowButton)) {
                continue;
            }

            if (!$button->isVisible($model) || !$button->hasPermissions()) {
                continue;
            }

            $btn = $button->build($model);

            if (isset($button->buttons)){
                $btn['buttons'] = $this->renderButtons($button->buttons, $data);
            }
            $result[$name] = $btn;
        }
        return $result;
    }

}