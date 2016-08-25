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
        'select' => 'yariksav\actives\controls\SelectControl',
        'toggler' => 'yariksav\actives\controls\ToggleControl',
        'checklist' => 'yariksav\actives\controls\CheckListControl',
        'selectize' => 'yariksav\actives\controls\SelectizeControl',
        'autocomplete' => 'yariksav\actives\controls\AutocompleteControl',
        'password' => [
            'class' => 'yariksav\actives\controls\Control',
            'type' => 'password',
        ],
        'grid' => 'yariksav\actives\controls\GridControl',
        'listview' => 'yariksav\actives\controls\ListViewControl',
        'dialog' => 'yariksav\actives\controls\DialogControl',
        'tree' => 'yariksav\actives\controls\TreeControl',
        'tag' => 'yariksav\actives\controls\Control',
        'auth' => 'yariksav\actives\controls\Control',
        'googleMaps' => 'yariksav\actives\controls\GoogleMapsControl',
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

    protected function createObject($params) {
        if (empty($params['name']) || is_int($params['name'])) {
            throw new \Exception('Please get the name for control');
        }

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
        if (!$this->_collection)
            return [];

        $controls = [];
        foreach ($this->_collection as $name=>$control) {
            if ($control && $control->visible && $control->hasPermissions()) {
                $controls[$name] = $control->build();
            }
        }
        return $controls;
    }

    public function update($data){
        if (!$data) {
            return;
        }
        foreach ($data as $name=>$value) {
            $control = isset($this->_collection[$name]) ? $this->_collection[$name] : null;
            if ($control && $control->visible && $control->hasPermissions()) {
                $control->update($value);
            }
        }
    }



/*


    public function renderControl($control)
    {
        if (!ArrayHelper::getValue($control, 'visible', true))
            return;

        $ctrl = null;
        $value = ArrayHelper::getValue($control, 'value');

        $name = ArrayHelper::getValue($control, 'name', ArrayHelper::getValue($control, 'n'));
        $type = strtolower(ArrayHelper::getValue($control, 'type', ArrayHelper::getValue($control, 't')));
        $model = $this->_model;



        $label = ArrayHelper::getValue($control, 'l', ArrayHelper::getValue($control, 'label', ArrayHelper::getValue($this->_labels, $name)));
        if (is_callable($label)){
            $label = $this->evaluateExpression($label, ['data' => $model]);
        }
        // if value is function
        if ($value && !is_array($value) && is_callable($value)) {
            $value = $this->evaluateExpression($value, ['data' => $model]);
        }

        if (!$value) {
            $value = isset($model) && isset($model[$name]) ? $model[$name] : (isset($control['default']) ? $control['default'] : null);
            //if (property_exists($model, $name))
            //	$value = $model->{$name};
        }

        $enable = ArrayHelper::getValue($control, 'enable', true);

        $htmlOptions = ArrayHelper::getValue($control, 'htmlOptions', []);
        if (!$enable)
            $htmlOptions['disabled'] = true;
        if (isset($control['title']))
            $htmlOptions['title'] = $control['title'];

        switch ($type) {
            case 'tag':
                $ctrl = ['tag' => ArrayHelper::getValue($control, 'tag'), 'options' => ArrayHelper::getValue($control, 'options')];
                break;

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

            case 'grid':
            case 'tree':
                $control['model']=$model;
                if (empty($control['class']))
                    $control['class'] = 'Sy'.ucfirst($type);
                $data = SyActiveObject::createInstance($control);
                $ctrl = ['name' => $name, 'value' => $value, 'data' => $data->getResponse()];
                if (isset($control['container']))
                    $ctrl['container'] = $control['container'];
                break;

            case 'autocomplete':
                if (isset($control['fields']) && $value)
                    $value = ArrayHelper::getValue(self::prepareDataByFields($value, $control['fields']), 0);

                $ctrl = ['name' => $name, 'value' => $value, 'label' => $label];
                if (isset($control['fields'][0]))
                    $ctrl['key'] = $control['fields'][0];
                break;

            case 'html':
                $ctrl = ['html' => $value];
                break;

            case 'line':
                $ctrl = [];
                break;

            default:
                $ctrl = ['name' => $name, 'value' => $value, 'label' => $label];
                foreach($control as $key=>$item){
                    if (!in_array($key, ['type', 'name', 'value', 'label', 'controls']))
                        $ctrl[$key] = $item;
                }
                break;
        }


        if ($ctrl !== null) {
            $ctrl['type'] = $type;
            if ($label)
                $ctrl['label'] = $label;
            if ($htmlOptions) {
                $ctrl['options'] = $htmlOptions;
            }
            if (isset($ctrl['options']) && is_callable($ctrl['options'])) {
                $ctrl['options'] = $this->evaluateExpression($ctrl['options'], ['data' => $model]);
            }

            foreach($control as $key=>$value){
                if (in_array($key, ['button', 'live', 'template', 'config', 'container', 'controls', 'links']))
                    $ctrl[$key] = $value;
            }

            if ($label && !in_array($type, ['label', 'switch', 'toggler']) && (isset($this->required[$name])))
                $ctrl['required'] = true;
        } else if ($type)
            throw new \Exception('Control type ' . $type . ' was not found');

        return $ctrl;
    }*/
}