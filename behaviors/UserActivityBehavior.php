<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yariksav\actives\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * UserActivityBehavior automatically creates table activity log
 *
 * To use UserActivityBehavior, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use yariksav\actives\behaviors\UserActivityBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         [
 *            'class' => UserActivityBehavior::className(),
 *            'model' => SomeTable::className(),
 *            'attributes' => []
 *            'actions' => ['insert', 'update', 'delete']
 *         ],
 *     ];
 * }
 * ```
 *
 * @author Savaryn Yaroslav <yaiksav@gmail.com>
 */

class UserActivityBehavior extends Behavior
{

    /**
     * @var array list of attributes that are to be automatically changed attributes.
     * The array keys are the ActiveRecord events upon which the attributes are to be updated,
     * and the array values are the corresponding attribute(s) to be updated. You can use a string to represent
     * a single attribute, or an array to represent a list of attributes. For example,
     *
     * ```php
     * [
     *     ActiveRecord::EVENT_AFTER_INSERT => [] // log all attributes
     *     ActiveRecord::EVENT_BEFORE_UPDATE => ['attribute1', 'attribute2'], // log specific attributes
     * ]
     * ```
     */
    public $attributes = [];
    /**
     * @var boolean whether to skip this behavior when the `$owner` has not been
     * modified
     */
    public $skipUpdateOnClean = true;
    /**
     * @var string model for saving action
     */
    public $model;

    /**
     * @inheritdoc
     */
    public function init() {

        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_AFTER_INSERT => [],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => [],
                BaseActiveRecord::EVENT_BEFORE_DELETE => []
            ];
        }
    }

    protected function insertActivity($params) {
        $model = $this->model;
        $activity = new $model(array_merge([
            'userId'=>Yii::$app->user->getId(),
            'table'=>$this->owner->tableSchema->name,
            'key'=>$this->owner->getPrimaryKey(),
            'createdAt'=> new Expression('Now()')
        ], $params));
        $activity->save();
    }

    public function events()
    {
        return array_fill_keys(
            array_keys($this->attributes),
            'evaluateEvents'
        );
    }

    public function evaluateEvents($event)
    {
        if ($this->skipUpdateOnClean
            && $event->name == ActiveRecord::EVENT_BEFORE_UPDATE
            && empty($this->owner->dirtyAttributes)
        ) {
            return;
        }
        $action = strcspn($event->name, 'ABCDEFGHJIJKLMNOPQRSTUVWXYZ');
        $action = $event->name[$action];
        $data = $event->name == ActiveRecord::EVENT_BEFORE_UPDATE ? $this->owner->dirtyAttributes : $this->owner->attributes;
        $this->insertActivity([
            'action'=>$action,
            'data'=>json_encode($data)
        ]);
//
//        if (!empty($this->attributes[$event->name])) {
//            $attributes = (array) $this->attributes[$event->name];
//            $value = $this->getValue($event);
//            foreach ($attributes as $attribute) {
//                // ignore attribute names which are not string (e.g. when set by TimestampBehavior::updatedAtAttribute)
//                if (is_string($attribute)) {
//                    $this->owner->$attribute = $value;
//                }
//            }
//        }
    }
}
