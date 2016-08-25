<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 22.05.2016
 * Time: 20:36
 */

namespace yariksav\actives\controls;

use Yii;
use yii\base\InvalidParamException;
use yii\base\ViewContextInterface;
use yii\helpers\ArrayHelper;
use yariksav\actives\base\ViewerTrait;

class SelectizeControl extends SelectControl implements ViewContextInterface
{
    use ViewerTrait;

    public $key;
    public $item;
    public $itemView;
    public $multiple = false;
    public $placeholder;
    public $elementOptions;
    public $group;
    public $groups;

    /**
     * @inheritdoc
     */
    public function init(){
        $this->type = 'selectize';
        $this->placeholder = Yii::t('actives', 'Please select');
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
        return $this->renderItems($collection);
    }

    /**
     * @inheritdoc
     */
    public function build() {
        $control = array_merge(parent::build(), [
            'multiple' => $this->multiple,
            'placeholder' => $this->placeholder,
            'groups' => $this->renderGroups(),
            'elementOptions' => $this->elementOptions
        ]);
        return $control;
    }

    /**
     * Renders all data models.
     * @return array the rendering result
     */
    public function renderItems($collection) {
        $rows = [];
        foreach ($collection as $index => $model) {
            $rows[] = $this->renderItem($model, $index);
        }
        return $rows;
    }

    /**
     * Render model item.
     * @return array the rendering result
     */
    public function renderItem($model, $index) {
        $key = $this->key ? $model[$this->key] : $index;
        $ret = [
            'key'=>$key
        ];
//        if (!$this->item && !$this->itemView) {
//            throw new InvalidParamException('One of param item or itemView must implemented');
//        }
        if (is_string($this->item)) {
            $ret['item'] = $model[$this->item];
        } else if (is_callable($this->item)) {
            $ret['item'] = call_user_func($this->item, $model, $key, $index, $this);
        } else if (is_string($model)) {
            $ret['item'] = $model;
        }

        $field = isset($ret['item']) ? 'content' : 'item';

        if (is_string($this->itemView)) {
            $ret[$field] = $this->getView()->render($this->itemView, array_merge([
                'model' => $model,
                'key' => $key,
                'index' => $index,
                'control' => $this,
            ]), $this);
        } else if (is_callable($this->itemView)) {
            $ret[$field] = call_user_func($this->itemView, $model, $key, $index, $this);
        }

        if ($this->group && $this->groups) {
            if (is_callable($this->group)) {
                $ret['optgroup'] = call_user_func($this->group, $model, $key, $index, $this);
            }
        }
        return $ret;
    }
    /**
     * @return string the view path that may be prefixed to a relative view name.
     */
    public function getViewPath() {
        return realpath(dirname((new \ReflectionClass(get_class($this->owner)))->getFileName()));
    }

    public function renderGroups() {
        if (is_array($this->groups)) {
            $groups = $this->groups;
        } else if (is_callable($this->groups)) {
            $groups = call_user_func($this->groups, $this->_model, $this);
        }
        $ret = [];
        if ($groups) {
            foreach ($groups as $key=>$item) {
                $ret[] = ['value'=>$key, 'label'=>$item];
            }
        }
        return $ret;
    }

}