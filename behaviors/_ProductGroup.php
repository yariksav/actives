<?php

namespace app\models;

use yii;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

class ProductGroup extends ActiveRecord
{

	public static function tableName()
	{
		return '{{%productgroup}}';
	}

	public function rules()
	{
		return [
			['name', 'required'],
			['name', 'string', 'min' => 2, 'max' => 128],
			[['id', 'parent_id'], 'integer'],
			['status', 'in', 'range' => ['A', 'D']],
			[['code', 'remark'], 'safe']
		];
	}

	public function attributeLabels()
	{
		return [
			'id' => Yii::t('app', 'Id'),
			'name' => Yii::t('app', 'Name'),
			'code' => Yii::t('app', 'Code'),
			'parent_id' => Yii::t('app', 'Parent'),
			'remark' => Yii::t('app', 'Remark'),
			'status' => Yii::t('app', 'Status'),
		];
	}

	/*	public function relations(){
            return array(
                'childsCount'=>array(self::STAT, get_class($this), 'parent_id'),
                'productsCount'=>array(self::STAT, 'Product', 'group_id'),
            );
        }*/

	public function getParent()
	{
		return $this->hasOne(self::className(), ['id'=>'parent_id']);
	}

	public function getProductCount()
	{
		return Product::find()->where(['group_id' => $this->id])->count();
	}

	public function getChildrenCount()
	{
		return count($this->getChildren(false));
	}

	public function getParents(){
		$parents = [];
		$parent = $this->parent;
		while($parent){
			$parents[] = $parent;
			$parent = $parent->parent;
		};
		return $parents;
	}

	public function getPath(){
		$names = ArrayHelper::getColumn(TreeLink::getParents(self::tableName(), $this->id), 'name');
		return $names ? implode(' ► ', array_reverse($names)) : '';
	}

	public function getWithOutChildren()
	{
		$query = ProductGroup::find();
		if ($this->id) {
			$query->andWhere(['NOT IN', 'id', $this->getChildrenIds()]);
		}
		return $query->all();
	}

	public function getChildrenIds()
	{
		return ArrayHelper::getColumn(TreeLink::getChildren(self::tableName(), $this->id), 'id');
	}

	public function afterSave($insert, $changedAttributes)
	{
		self::setPaths($this, ArrayHelper::getColumn($this->parents, 'id'));
	}

	public static function setPaths($group, $parents){
		if (!$group)
			return;
		$parents = array_merge([$group->id], $parents);
		TreeLink::addLinks(self::tableName(), $group->id, $parents);
		$children = $group->findAll(['parent_id' => $group->id]);
		if ($children) foreach ($children as $child){
			self::setPaths($child, $parents);
		}
	}


	/*$path = '';
    $pathid = '';
    $parent = ($this->parent_id) ? SyFunc::getItemParents($this->tableName(), $this->parent_id) : null;
    if ($parent) {
        foreach ($parent as $item) {
            $path .= $item['name'] . ' | ';
            $pathid .= $item['id'] . ',';
        }
    }
    $this->isNewRecord = false;
    $this->pathid = $pathid . $this->id;
    $this->saveAttributes(array(
        'pathid' => $pathid . $this->id,
        'path' => $path . $this->name
    ));

    parent::afterSave($insert, $changedAttributes);*/


	/*public function getProductGroupData(){
		$data = Yii::app()->db->createCommand()
			->select()
			->from($this->tableName())
			->queryAll();
		return $data;
	}

	public static function getGroups(){
		return self::model()->findAll(
			array(
				'condition'=>"t.status=:status",
				'order'=>'t.path asc',
				'params'=> array(':status'=>'A')
			)
		);
	}
	
	public static function getTree($parentId){
		return Yii::app()->db->createCommand("
			SELECT g.id,
					 g.code,
					 g.name,
					 g.status,
					 COUNT(g2.id) AS hasChildren 
			  FROM {{productgroup}} AS g 
			  LEFT OUTER JOIN {{productgroup}} AS g2 ON g2.parent_id = g.id
			 WHERE g.parent_id = :id
				AND g.id != g.parent_id
				GROUP BY g.id, g.code, g.name, g.status
				ORDER BY g.name ASC	 "
			 )->queryAll(true, array(':id'=>$parentId));
		}
	}*/
}