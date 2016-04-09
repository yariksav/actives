<?php
namespace yariksav\actives\components;

use yii\helpers\ArrayHelper;

class SyStateColumn extends SyDataColumn
{
    //public $header = '';
    public $values = array();
    public $sortable = false;
    public $filter;

    public function init()
    {
        parent::init();
        $this->align = 'center';
        $this->width = 50;
    }

    public function renderFilterContent(){
        if($this->filter && $this->values){
            $data = array();
            foreach($this->values as $key=>$value){
                $data[$key]=$value['title'];
            }
            $this->filter = array(
                'type'=>$this->filter,
                'data'=>$data,
            );
        }
        return parent::renderFilterContent();
    }

    public function renderDataCell($row,$data){
        $value = isset($this->value) ? $this->evaluateExpression($this->value,['data'=>$data]) : ArrayHelper::getValue($data,$this->name);
        if ($value === false && isset($this->values['false']))
            $value = 'false';
        if ($value === true && isset($this->values['true']))
            $value = 'true';
        $status = ArrayHelper::getValue($this->values, $value);
        if (!$status)
            $status = ArrayHelper::getValue($this->values, 'default');
        if (!$status)
            return $value;

        if (isset($status['type'])){
            $type = $status['type'];
            unset($status['type']);
        }
        else
            $type = 'label';

        return array('type'=>$type, 'options' => $status);
    }
}
