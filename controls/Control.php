<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 21.05.2016
 * Time: 10:11
 */

namespace yariksav\actives\controls;

use yii;
use Closure;
use yii\base\Model;
use yii\base\Object;
use yii\helpers\ArrayHelper;

use yariksav\actives\base\Component;
use yariksav\actives\base\PermissionTrait;
use yariksav\actives\base\VisibleTrait;

class Control extends Component
{
    use PermissionTrait;
    use VisibleTrait;

    public $config;
    public $save;
    public $afterSave;
    public $default;
    public $validate;
    public $data;
    public $placeholder;

    public $validation;

    /**
     * @var boolean Is model riquired for build control data.
     * It needs for some active controls as self update logic
     */
    public $requireModel = false;

    /**
     * @var string|Closure the caption or label of the control
     * @see getLabel
     */
    protected $owner;
    protected $_label;
    protected $_text;
    protected $_name;
    protected $_type;
    protected $_value;
    protected $_model;
    protected $_wrapper;

    /**
     * Constructor.
     * @param ActiveObject $owner the active object whom belongs this control
     * @param array $config configurations to be applied to the newly created query object
     */
    function __construct($owner, $model = null, $config = []) {
        parent::__construct($config);
        $this->owner = $owner;
        $this->model = $model;
    }

    public function getModel() {
        return $this->_model;
    }

    public function setModel($value) {
        $this->_model = $value;
        if (!$this->_model) {
            return;
        }
        if ($this->_model instanceof Model) {
            if (is_callable($this->validate)) {
                $this->_model->on(yii\base\Model::EVENT_BEFORE_VALIDATE,
                    function($event) {
                        $model = $event->data[0];
                        $control = $event->data[1];
                        $this->validate($model, $control);
                    }, [$value, $this]);
            }
            // Register After Save
            if (is_callable($this->afterSave)) {
                $eventName = $this->_model->getIsNewRecord() ? yii\db\ActiveRecord::EVENT_AFTER_INSERT : yii\db\ActiveRecord::EVENT_AFTER_UPDATE;

                $this->_model->on($eventName,
                    function($event){
                        $model = $event->data[0];
                        $control = $event->data[1];
                        call_user_func_array($control->afterSave, [$model, $control]);
                    }, [$value, $this]);
            }
        }
    }

    public function getValue() {
        $name = $this->_name;
        if (is_callable($this->_value)) {
            $this->_value = call_user_func_array($this->_value, [
                'data' => $this->_model,
                'owner' => $this->owner
            ]);
        } else if ($this->_model instanceof yii\db\BaseActiveRecord && $this->_model->hasAttribute($name)) {
            $this->_value = $this->_model->$name;
        } else if (!$this->_value) {
            $this->_value = isset($this->_model) && isset($this->_model[$name]) ? $this->_model[$name] : $this->default;
        }
        return $this->_value;
    }

    public function setValue($value) {
        $this->_value = $value;
    }

    public function getVisible() {
        if (is_callable($this->_visible)) {
            $this->_visible = call_user_func_array($this->_visible, [
                'data' => $this->_model,
                'owner' => $this->owner
            ]);
        }
        return $this->_visible;
    }

    public function getType() {
        return $this->_type;
    }

    public function setType($value) {
        $this->_type = $value;
    }

    public function getText() {
        if (is_callable($this->_text)) {
            $this->_text = call_user_func_array($this->_text, [
                'data' => $this->_model,
                'owner' => $this->owner
            ]);
        } else if (!$this->_text) {
            if ($this->_model instanceof Model) {
                $labels = $this->_model->attributeLabels();
                $this->_text = ArrayHelper::getValue($labels, $this->_name);
            }
        }
        return $this->_text;
    }

    public function setText($value) {
        $this->_text = $value;
    }

    /**
     * Returns the name for the control.
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    public function setName($value) {
        $this->_name = $value;
    }


    public function getWrapper() {
        return $this->_wrapper;
    }

    public function setWrapper($value) {
        $this->_wrapper = $value;
    }

    /**
     * Builds full control config array.
     * @return array the control config
     */
    public function build() {
        $control = [
            'type' => $this->_type
        ];

        $value = $this->value;
        if ($value !== null) {
            $control['value'] = $value;
        }
        if ($text = $this->text) {
            $control['text'] = $text;
        }
        if ($placeholder = $this->placeholder) {
            $control['placeholder'] = $placeholder;
        }
        if ($this->config) {
            $control['config'] = $this->config;
        }
        if ($this->validation) {
            $control['validation'] = $this->validation;
        }

        return array_merge($control, $this->load());
    }

    /**
     * Builds only control data.
     * @return array the control data
     */
    public function load() {
        return [];
    }

    public function update($value) {
        $this->_value = $value;
        $name = $this->_name;
        $model = $this->_model;

//        $callback = function ($event) use ($model, $value) {
//            call_user_func_array($this->afterSave, [
//                'model' => $model,
//                'value' => $value,
//                'event' => $event
//            ]);
//        };
//


        // Call Save
        if (is_callable($this->save)) {
            call_user_func_array($this->save, [
                'model' => $model,
                'value' => $value
            ]);
        } else {

            if (isset($model)) {
                if ($model instanceof Model) {
                    //if ($model->hasProperty($name)) { //if ($model->isAttributeActive($name)) {
                        $model->$name = $value;
                    //}
                } else if (isset($model[$name])) {
                    $model[$name] = $value;
                }
            }
        }
    }
//
//    public function validate() {
//        return null;
//    }
}