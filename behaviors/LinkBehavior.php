<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yariksav\actives\behaviors;

use yii\base\Behavior;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * LinkBehavior automatically creates or deletes links records.
 *
 * To use LinkBehavior, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use yii\behaviors\TimestampBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         LinkBehavior::className()
 *     ];
 * }
 * ```
 *
 * ```php
 *
 * public function behaviors()
 * {
 *     return [
 *         [
 *            'class' => LinkBehavior::className(),
 *            'linkModel' => Links::className(),
 *            'linkKeys' => ['modelId', 'linkedId'],
 *            'attribute' => 'links'
 *         ],
 *     ];
 * }
 * ```
 *
 * @author Savaryn Yaroslav <yaiksav@gmail.com>
 */

class LinkBehavior extends Behavior
{

    /**
     * @var string new attribute of model, where will possible to get|set links ids
     */
    public $attribute;
    /**
     * @var string model key, if not sets - will be primary key of model
     */
    public $key;
    /**
     * @var string class of linked model
     */
    public $linkModel;
    /**
     * @var array link model keys [keyToModel, keyToLinkedModel]
     */
    public $linkKeys;

    /**
     * @var array | boolean set changed links
     * if false - events will not trigger
     */
    protected $newValues = false;

    /**
     * @var callable gets links values
     * ```php
     * function ($model, $ids)
     * {
     *     foreach ($ids as $id) {
     *          // insert link $id
     *     }
     * }
     * ```
     * If not set links will be deleted automatically
     */
    public $values;
    /**
     * @var callable insert new calculated links ids
     * ```php
     * function ($model) {...}
     * ```
     * If not set links will be deleted automatically
     */
    public $insert;
    /**
     * @var callable delete calculated links ids
     * ```php
     * function ($model, $ids)
     * {
     *      // delete links
     * }
     * ```
     * If not set links will be deleted automatically
     */
    public $delete;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        if ($this->attribute === null) {
            throw new InvalidConfigException('Propertiy "attribute" must be specified.');
        }

        if ((!$this->insert || !$this->delete) && ($this->linkModel === null || $this->linkKeys === null)) {
            throw new InvalidConfigException('Either callbacks "insert", "delete" or properties "linkModel", "linkKeys" must be specified.');
        }

        if (!$this->values) {
            $this->values = function($model) {
                if (!$this->key) {
                    $this->key = $this->owner->primaryKey()[0];
                }
                $class = $this->linkModel;
                $links = $class::findAll([$this->linkKeys[0] =>$this->owner[$this->key]]);
                return ArrayHelper::getColumn($links, $this->linkKeys[1]);
            };
        }

        if (!$this->insert) {
            $this->insert = function($model, $ids) {
                $class = $this->linkModel;
                foreach ($ids as $id) {
                    (new $class([
                        $this->linkKeys[0]=>$this->owner[$this->key],
                        $this->linkKeys[1]=>$id
                    ]))->save();
                }
            };
        }

        if (!$this->delete) {
            $this->delete = function($model, $ids) {
                $class = $this->linkModel;
                $class::deleteAll([
                    $this->linkKeys[0]=>$this->owner[$this->key],
                    $this->linkKeys[1]=>$ids
                ]);
            };
        }
    }

    /**
     * Returns the value for the current links.
     * @return array the links value
     */
    protected function getValue() {
        return is_callable($this->values) ? call_user_func($this->values, $this->owner) : $this->values;
    }

    /*
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return $name === $this->attribute || parent::canGetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return $name === $this->attribute || parent::canSetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if ($name == $this->attribute) {
            return $this->getValue();
        } else {
            parent::__get($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if ($name == $this->attribute) {
            $this->newValues = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    public function onSave()
    {
        if ($this->newValues === false) {
            return;
        }

        $currentLinks = $this->getValue();

        if ($delete = array_diff($currentLinks, $this->newValues)) {
            call_user_func($this->delete, $this->owner, (array)$delete);
        }

        if ($insert = array_diff($this->newValues, $currentLinks)) {
            call_user_func($this->insert, $this->owner, (array)$insert);
        }
    }

    public function onDelete() {
        call_user_func($this->delete, $this->owner, $this->getValue());
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => [$this, 'onSave'],
            BaseActiveRecord::EVENT_AFTER_UPDATE => [$this, 'onSave'],
            BaseActiveRecord::EVENT_BEFORE_DELETE => [$this, 'onDelete'],
        ];
    }
}
