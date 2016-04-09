<?php

namespace yariksav\actives\components;

use yii\helpers\ArrayHelper;
use yii\base\Model;


class SyDialogConstructor extends SyComponent {

    public $style;
    public $scripts = '';
    protected $required = array();

    public $model;
    public $labels = array();

    public function __construct($model = null){
        $this->model = $model;

        // вычисление обязытельных полей из модели
        if ($this->model instanceof Model){
            $rules = $this->model->rules();
            if ($rules)
                foreach($rules as $rule){
                    if (in_array($rule[1], array('required', 'unique'))){
                        if (is_array($rule[0])) foreach($rule[0] as $ruleitem){
                            $this->required[$ruleitem] = true;
                        }
                        else
                            $this->required[$rule[0]] = true;
                        //$this->required = array_merge($this->required, $rule[0]);
                        //foreach(preg_split("/(,)/", $rule[0]) as $ruleitem){
                        //	$this->required[trim($ruleitem)] = true;
                        //}
                    }
                }
        }

        $this->labels = ($this->model && $this->model instanceof Model) ? $this->model->attributeLabels() : array();
    }

    /*public function run() {


        return $this->buildControls($this->controls);
    }*/

    protected function prepareControls($controls, $model){

    }


    public static function prepareDataByFields($data, $fields){
        if (!$data || !$fields)
            return $data;
        $vardata = [];
        if (!is_array($data))
            $data = [$data];

        foreach($data as $item) {
            $varitem = [];
            foreach ($fields as $id => $key) {
                $varitem[$id] = ArrayHelper::getValue($item, $key);
            }
            $vardata[] = $varitem;
        }
        return $vardata;
    }

    public function buildControls($controls){
        if (!$controls)
            throw new Exception('No one control was found');

        $retControls = array();
        foreach ($controls as $control) {
            if ($ctrl = $this->renderControl($control))
                $retControls[] = $ctrl;
        }
        return $retControls;
    }

    public function renderControl($control)
    {
        if (!ArrayHelper::getValue($control, 'visible', true))
            return;

        $ctrl = null;
        $value = ArrayHelper::getValue($control, 'value');
        $this->scripts .= ArrayHelper::getValue($control, 'script');
        $name = ArrayHelper::getValue($control, 'name', ArrayHelper::getValue($control, 'n'));
        $type = strtolower(ArrayHelper::getValue($control, 'type', ArrayHelper::getValue($control, 't')));
        $model = $this->model;



        $label = ArrayHelper::getValue($control, 'l', ArrayHelper::getValue($control, 'label', ArrayHelper::getValue($this->labels, $name)));
        if (is_callable($label)){
            $label = $this->evaluateExpression($label, array('data' => $model));
        }
        // if value is function
        if ($value && !is_array($value) && is_callable($value)) {
            $value = $this->evaluateExpression($value, array('data' => $model));
        }

        if (!$value) {
            $value = isset($model) && isset($model[$name]) ? $model[$name] : (isset($control['default']) ? $control['default'] : null);
            //if (property_exists($model, $name))
            //	$value = $model->{$name};
        }

        /*if (!$value) {
            if (strpos($name, '->') !== false || strpos($name, '(') !== false) {
                eval('$value = $model->' . $name . ';');
            } else if (isset($model) && isset($model[$name])) {
                $value = $model[$name];
            } else if (isset($control['default']))
                $value = $control['default'];
        }*/

        $enable = ArrayHelper::getValue($control, 'enable', true);

        $htmlOptions = ArrayHelper::getValue($control, 'htmlOptions', array());
        if (!$enable)
            $htmlOptions['disabled'] = true;
        if (isset($control['title']))
            $htmlOptions['title'] = $control['title'];

        /*$class = $htmlOptions['class'] = ArrayHelper::getValue($htmlOptions, 'class', ArrayHelper::getValue($control, 'class'));

        if (strpos($htmlOptions['class'], 'date') !== false
            || strpos($htmlOptions['class'], 'time') !== false){

            if (strpos($htmlOptions['class'], 'date') !== false){
                $datetime_ts = strtotime($value);
                if ($datetime_ts < 10000000)
                    $value = '';
                else
                    if (strpos($htmlOptions['class'], 'datetime') !== false){
                        $value = date(C_DATEFORMAT.' H:i', $datetime_ts);
                    }
                    else
                        $value = date(C_DATEFORMAT, $datetime_ts);

            }

            $this->scripts .= "
                $('.date').datepicker({dateFormat: '" . C_DATEFORMAT_JS . "'});

                $('.datetime').datetimepicker({
                    dateFormat: '" . C_DATEFORMAT_JS . "',
                    timeFormat: '" . C_TIMEFORMAT_JS . "'
                 });

                 $('.time').timepicker({timeFormat: '" . C_TIMEFORMAT_JS . "'});
            ";
        }
        if (in_array($type, array('textbox', 'password', 'select', 'textarea')))
            $htmlOptions['class'] .= ($htmlOptions['class']?' ':'').'form-control';
        */
        switch ($type) {
            case 'tag':
                $ctrl = ['tag' => ArrayHelper::getValue($control, 'tag'), 'options' => ArrayHelper::getValue($control, 'options')];
                break;

            case 'number':
                $ctrl = ['name' => $name, 'value' => $value, 'label' => $label];
                break;

            case 'toggler':
                $ctrl = array('name' => $name, 'value' => $value, 'label' => $label, 'data'=>$control['data']);
                break;

            case 'select':
                $data = ArrayHelper::getValue($control, 'data');

                if (!$data) {
                    unset($htmlOptions['empty']);
                    unset($htmlOptions['prompt']);
                    $htmlOptions['options']['disabled'] = array('disabled' => true);
                    $value = false;
                } else {
                    if (is_callable($data))
                        $data = $this->evaluateExpression($data, array('data' => $model));

                    if (isset($control['fields'])) {
                        $fileds = $control['fields'];
                        $data = ArrayHelper::map($data, ArrayHelper::getValue($fileds, 0, 'id'), ArrayHelper::getValue($fileds, 1, 'name'), ArrayHelper::getValue($fileds, 2));
                    }
                }
                $ctrl = ['name' => $name, 'value' => $value, 'label' => $label, 'data' => $data];
                if (isset($control['empty']))
                    $ctrl['empty'] = $control['empty'];
                if (isset($control['button']))
                    $ctrl['button'] = $control['button'];
                break;


            case 'checklist':
                $data = ArrayHelper::getValue($control, 'data');
                if (is_callable($data))
                    $data = $this->evaluateExpression($data, array('data' => $model));

                if (isset($control['fields'])) {
                    $fields = $control['fields'];
                    if (is_array($fields))
                        $data = ArrayHelper::map($data, ArrayHelper::getValue($fields, 0, 'id'), ArrayHelper::getValue($fields, 1, 'name'), ArrayHelper::getValue($fields, 2));

                    if ($value && isset($fields['selected']))
                        $value = ArrayHelper::getColumn($value, $fields['selected']);
                }
                if (!$value)
                    $value = ArrayHelper::getValue($control, 'defvalue');

                $ctrl = ['name' => $name, 'value' => $value, 'label' => $label, 'data' => $data];
                break;

            case 'treebox':
                $options = isset($control['options']) ? $this->evaluateExpression($control['options'], array('data' => $model)) : array();
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
                $ctrl = array('name' => $name, 'value' => $value, 'label' => $label);
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
                $ctrl['options'] = $this->evaluateExpression($ctrl['options'], array('data' => $model));
            }

            foreach($control as $key=>$value){
                if (in_array($key, ['button', 'live', 'template', 'config', 'container', 'controls', 'links']))
                    $ctrl[$key] = $value;
            }

            if ($label && !in_array($type, array('label', 'switch', 'toggler')) && (isset($this->required[$name])))
                $ctrl['required'] = true;
        } else if ($type)
            throw new \Exception('Control type ' . $type . ' was not found');

        return $ctrl;
    }
}