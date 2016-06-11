<?php

namespace yariksav\actives\view\columns;

use yii\helpers\ArrayHelper;

class DateColumn extends Column{

    public $width = 190;
    public $align = 'center';
    private $dates;

    public $defaultRange = 1;
    public $unixTimeStamp = false;
    public $dateFormat = 'd.m.Y';
    public $dateFormatJS = 'yyyy.mm.dd';
    public $timeFormat = 'H:i:s';
    public $format = false;
    public $picker = 'datepicker';
    public $dbFormat = 'd.m.Y H:i:s';
    //public $dbFormatFrom = 'Y-m-d 00:00:00';
    //public $dbFormatTo = 'Y-m-d 24:00:00';


    public function init(){
        if (!$this->format)
            $this->format = $this->dateFormat.' '.$this->timeFormat;

        //$this->grid->afterAjaxUpdate=($this->grid->afterAjaxUpdate ? $this->grid->afterAjaxUpdate : '').' function(id, data){'.$this->getDatePickerScript().'}';
        parent::init();
    }

    /*protected function getDate($name){
        $name = $this->getInputName($name);
        if (isset($_GET[$name])){
            Yii::app()->session->add($name, $_GET[$name]);
        }
        $default = date('c', (strpos($name, 'from') !== false ? time('d') - 86400*($this->defaultRange - 1) : time('d')));
        return Yii::app()->session->get($name, $default);
    }*/

    public function prepareFilterValue($values){
        $values = SyDatePicker::getDateRangeFromPostRequest($values);
        if (!$values){
            $values = SyDatePicker::getDefautRange($this->defaultRange * 86400);
        }
        $values['start_ts'] = $values['start'];
        $values['end_ts'] = $values['end'] + 86400;

        if ($this->unixTimeStamp){
            $values['start_db'] = $values['start_ts'];
            $values['end_db'] = $values['end_ts'];
        }
        else{
            $values['start_db'] = date($this->dbFormat, $values['start']);
            $values['end_db'] = date($this->dbFormat, $values['end']);
        }
        return parent::prepareFilterValue($values);
    }

    public function renderFilterContent(){
        $this->filter = [
            'name'=>$this->name,
            'type'=>'daterangepicker',
            'container'=>'.action-bar',
            'live'=>true,
            'data'=>[
                'opens'=> 'left',
                'startDate'=>date($this->dateFormat, $this->filterValue['start']),
                'endDate'=>date($this->dateFormat, $this->filterValue['end']),
                'locale'=>[
                    'filter'=>'DD.MM.YYYY h:mm'
                ],
                'ranges'=>SyDatePicker::dateRanges($this->dateFormat),
            ],
        ];
        return parent::renderFilterContent();
    }

    /*public function prepareDataProvider($provider){
        $dbdatefrom = strtotime($this->getDate('from'));
        if (!$this->unixTimeStamp)
            $dbdatefrom = date($this->dbFormatFrom, $dbdatefrom);

        $dbdateto = strtotime($this->getDate('to'));
        if ($this->unixTimeStamp)
        $dbdateto += 86400;
      else   
            $dbdateto = date($this->dbFormatTo, $dbdateto);
        $provider->criteria->addBetweenCondition($this->name, $dbdatefrom, $dbdateto);
    }*/

    public function renderDataCell($row, $data){
        if ($this->value!==null)
            $value=$this->evaluateExpression($this->value,array('data'=>$data,'row'=>$row));
        else if($this->name!==null){
            $value=ArrayHelper::getValue($data,$this->name);
            if ($value){
                if (!$this->unixTimeStamp)
                    $value = strtotime($value);
                //$value = date($this->format, $value);
                $value = \Yii::$app->formatter->asDatetime($value);
            }
        }

        return $value===null ? $this->grid->nullDisplay : $value;//$this->grid->getFormatter()->format($value,$this->type);
    }
}
