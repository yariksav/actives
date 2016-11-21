<?php

namespace app\models;

use yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class TreeLink extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%treelink}}';
    }

    public function rules()
    {
        return [
            [['id', 'parent_id', 'level', 'table'], 'required'],
            [['id', 'parent_id', 'level'], 'integer'],
            [['table'], 'string'],
            [['table'], 'string', 'max' => 32]
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