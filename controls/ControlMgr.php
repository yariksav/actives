<?php

namespace yariksav\actives\controls;


use yii;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use yariksav\actives\base\Collection;

class ControlMgr extends Collection {

    /**
     * @var array list of built-in controls (name => class or configuration)
     */
    public static $builtInControls = [
        'label' => 'yariksav\actives\controls\Control',
        'text' => 'yariksav\actives\controls\Control',
        'number' => 'yariksav\actives\controls\Control',
        'textbox' => 'yariksav\actives\controls\Control',
        'textarea' => 'yariksav\actives\controls\Control',
        'htmleditor' => 'yariksav\actives\controls\Control',
        'select' => 'yariksav\actives\controls\SelectControl',
        'toggler' => 'yariksav\actives\controls\ToggleControl',
        'checklist' => 'yariksav\actives\controls\CheckListControl',
        'selectize' => 'yariksav\actives\controls\SelectizeControl',
        'autocomplete' => 'yariksav\actives\controls\AutocompleteControl',
        'password' => [
            'class' => 'yariksav\actives\controls\Control',
            'type' => 'password',
        ],
        'grid' => 'yariksav\actives\controls\ActiveControl',
        'tree' => 'yariksav\actives\controls\ActiveControl',
        'listview' => 'yariksav\actives\controls\ActiveControl',
        'view' => 'yariksav\actives\controls\ActiveControl',

        'dialog' => 'yariksav\actives\controls\DialogControl',
        'tree' => 'yariksav\actives\controls\TreeControl',
        'tag' => 'yariksav\actives\controls\Control',
        'socialAuth' => 'yariksav\actives\controls\SocialAuth',
        'googleMaps' => 'yariksav\actives\controls\GoogleMapsControl',
        'fileUpload' => 'yariksav\actives\controls\FileUploadControl',
        'imageUpload' => 'yariksav\actives\controls\ImageUploadControl',
    ];

    protected $_required = [];
    protected $_model;
    protected $_labels = [];

    public function __construct($owner, $model = null){
        $this->_model = $model;
        $this->owner= $owner;

        // get required fields from model
        if ($this->_model instanceof Model){
            $rules = $this->_model->rules();
            if ($rules) {
                foreach ($rules as $rule) {
                    if (in_array($rule[1], [
                        'required',
                        'unique'
                    ])) {
                        if (is_array($rule[0]))
                            foreach ($rule[0] as $ruleitem) {
                                $this->required[$ruleitem] = true;
                            } else {
                            $this->required[$rule[0]] = true;
                        }
                    }
                }
            }
        }
        $this->_labels = ($this->_model && $this->_model instanceof Model) ? $this->_model->attributeLabels() : [];
    }

    public function getModel() {
        return $this->_model;
    }

    public function setModel($value) {
        $this->_model = $value;
        if ($this->_collection) {
            foreach ($this->_collection as $control) {
                $control->model = $value;
            }
        }
    }

    protected function createItem($params, $name = null) {
        if (!$name || is_int($name)) {
            throw new \Exception('Please get the name for control');
        }
        $params['name'] = $name;

        if (empty($params['type']) && empty($params['class'])) {
            throw new yii\base\Exception('Control must have type or class property');
        }

        if (isset($params['type'])) {
            $type = $params['type'];
            if (isset(static::$builtInControls[$type])) {
                $type = static::$builtInControls[$type];
            }
            if (is_array($type)) {
                $params = array_merge($type, $params);
            } else {
                $params['class'] = $type;
            }
        }
        return Yii::createObject($params, [
            $this->owner,
            $this->_model
        ]);
    }

    public function build() {
        if (!$this->_collection) {
            return [];
        }

        $controls = [];
        foreach ($this as $name => $control) {
            if ($control && $control->visible && $control->hasPermissions()) {
                $controls[$name] = $control->build();
            }
        }
        return $controls;
    }

    public function setValues($values){
        if (!$values) {
            return;
        }
        foreach ($values as $name=>$value) {
            $control = isset($this->_collection[$name]) ? $this->_collection[$name] : null;
            if ($control && $control->visible && $control->hasPermissions()) {
                $control->update($value);
            }
        }
    }
//
//    public function validate() {
//        $validation = [];
//        foreach ($this->_collection as $name=>$control) {
//            $res = $control->validate();
//            if (is_string($res)) {
//                $validation[$name] = [$res];
//            }
//        }
//        return $validation;
//    }


/*

    case 'number':
        $ctrl = ['name' => $name, 'value' => $value, 'label' => $label];
        break;

    case 'treebox':
        $options = isset($control['options']) ? $this->evaluateExpression($control['options'], ['data' => $model]) : [];
        $options['selected'] = $value;
        $options['contextmenu'] = false;
        $options['class'] = $control['class'];
        $data = SyActiveObject::createInstance($options);

        $ctrl = ['name' => $name, 'value' => $value, 'label' => $label, 'data' => $data->response];
        break;

    case 'tree':
        $control['model']=$model;
        if (empty($control['class']))
            $control['class'] = 'Sy'.ucfirst($type);
        $data = SyActiveObject::createInstance($control);
        $ctrl = ['name' => $name, 'value' => $value, 'data' => $data->getResponse()];
        if (isset($control['container']))
            $ctrl['container'] = $control['container'];
        break;

    }*/
}