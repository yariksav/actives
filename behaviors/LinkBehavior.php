<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yariksav\actives\behaviors;

use yii\base\Behavior;
use yii\base\InvalidCallException;
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
     * @var array set changed links
     * if not sets - events will not be triggered
     */
    protected $newValues;

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
    protected $values;
    /**
     * @var callable insert new calculated links ids
     * ```php
     * function ($model) {...}
     * ```
     * If not set links will be deleted automatically
     */
    public $insertLinks;
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
    public $deleteLinks;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        if ($this->attribute === null || $this->linkModel === null || $this->linkKeys === null) {
            throw new InvalidConfigException('Properties "attribute", "linkModel" and "linkKeys" must be specified.');
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

        if (!$this->insertLinks) {
            $this->insertLinks = function($model, $ids) {
                $class = $this->linkModel;
                foreach ($ids as $id) {
                    (new $class([
                        $this->linkKeys[0]=>$this->owner[$this->key],
                        $this->linkKeys[1]=>$id
                    ]))->save();
                }
            };
        }

        if (!$this->deleteLinks) {
            $this->deleteLinks = function($model, $ids) {
                $class = $this->linkModel;
                $class::deleteAll([
                    $this->linkKeys[0]=>$this->owner[$this->key],
                    $this->linkKeys[1]=>(array)$ids
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

    public function saveLinks()
    {
        if ($this->newValues === null) {
            return;
        }

        $currencies = $this->getValue();

        if ($delete = array_diff($currencies, $this->newValues)) {
            call_user_func($this->deleteLinks, $this->owner, $delete);
        }

        if ($insert = array_diff($this->newValues, $currencies)) {
            call_user_func($this->insertLinks, $this->owner, $insert);//(new CountryCurrency(['countryId'=>$this->id, 'currencyId'=>$id]))->save();
        }
    }

    public function deleteLinks() {
        call_user_func($this->deleteLinks, $this->owner, $this->getValue());
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => [$this, 'saveLinks'],
            BaseActiveRecord::EVENT_AFTER_UPDATE => [$this, 'saveLinks'],
            BaseActiveRecord::EVENT_BEFORE_DELETE => [$this, 'deleteLinks'],
        ];
    }
}
