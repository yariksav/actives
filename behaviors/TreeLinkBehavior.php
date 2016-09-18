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
 * TreeLinkBehavior automatically creates tree links
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
 *            'class' => TreeLinkBehavior::className(),
 *            'model' => TreeLink::className(),
 *            'key' => 'id',
 *            'parentKey' => 'parentId',
 *         ],
 *     ];
 * }
 * ```
 *
 * @author Savaryn Yaroslav <yaiksav@gmail.com>
 */

class TreeLinkBehavior extends Behavior
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

    public function onSave()
    {

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

    public static function removeLinks($table, $id){
        self::deleteAll(['table'=>$table, 'id'=>$id]);
    }

    public static function addLinks($table, $id, $parents)
    {
        self::removeLinks($table, $id);
        foreach($parents as $key=>$parent){
            $link = new TreeLink(['table'=>$table, 'id'=>$id, 'level'=>$key, 'parent_id'=>$parent]);
            $link->save();
        }
    }

    public static function getChildren($table, $id, $fields = null)
    {
        $idField = ArrayHelper::getValue($fields, 0, 'id');
        $parentField = ArrayHelper::getValue($fields, 1, 'parent_id');
        $nameField = ArrayHelper::getValue($fields, 2, 'name');
        $query = new yii\db\Query();
        $query->select("t2.$idField, t2.$parentField, t2.$nameField, tlink.level")
            ->from($table.' t1, '.self::tableName().' tlink, '.$table.' t2')
            ->where('
                    t1.'.$idField.' = tlink.parent_id
                AND tlink.id = t2.'.$idField.'
                AND t2.'.$idField.' != t2.'.$parentField.'
                AND t1.'.$idField.' = :id

                AND tlink.table = :table
            ')->params([':id'=>$id, ':table'=>$table]);
        return $query->all();
    }

    public static function getParents($table, $id, $fields = null)
    {
        $idField = ArrayHelper::getValue($fields, 0, 'id');
        $parentField = ArrayHelper::getValue($fields, 1, 'parent_id');
        $nameField = ArrayHelper::getValue($fields, 2, 'name');
        $query = new yii\db\Query();
        $query->select("t2.$idField, t2.$parentField, t2.$nameField, tlink.level")
            ->from($table.' t1, '.self::tableName().' tlink, '.$table.' t2')
            ->where('
                    t1.'.$idField.' = tlink.id
                AND tlink.parent_id = t2.'.$idField.'
                AND t1.'.$idField.' = :id

                AND tlink.table = :table
            ')->params([':id'=>$id, ':table'=>$table]);
        return $query->all();
    }
}
