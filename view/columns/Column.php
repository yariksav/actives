<?php

namespace yariksav\actives\view\columns;

use yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use yii\db\ActiveQueryInterface;
use yii\helpers\Inflector;
use yariksav\actives\base\Component;
use yariksav\actives\base\ProtectedObject;

class Column extends ProtectedObject
{

    public $name;
    public $value;
    public $header;
    public $format = null;
    //public $grid;
    public $type='text';
    public $footer;
    public $sortable=true;
    public $filter;
    public $filterValue;

    public $align;
    public $width;
    public $on;
    public $hidden;
    public $data;

    public $htmlOptions = [];
    public $headerOptions = [];
    //public $footerOptions = [];

    //-------------
    protected $owner;


    function __construct($owner, $config = []) {
        parent::__construct($config);
        $this->owner = $owner;
    }

    public function init()
    {
        //parent::init();
        if($this->name===null)
            $this->sortable=false;
        if($this->name===null && $this->value===null)
            throw new \Exception(\Yii::t('app.error','Either "name" or "value" must be specified for CDataColumn.'));
        if ($this->name === null)
            $this->name = $this->header;
    }

    public function prepareFilterValue($value){
        $this->filterValue = $value;
        return $value;
    }

    public function renderFilterContent()
    {
        if ($this->filter){
            if ($this->filter === true){
                $this->filter = ['type'=>'textbox'];
            }

            if (!isset($this->filter['type']) && is_array($this->filter)){
                $this->filter = [
                    'type' => 'select',
                    'data' => $this->filter
                ];
            }

            if (empty($this->filter['label']) && strlen($this->header) > 0)
                $this->filter['label'] = $this->header;

            if (empty($this->filter['name']))
                $this->filter['name']=$this->name;
        }
        return $this->filter;
    }

    public function renderHeader()
    {
        $provider = $this->owner->dataProvider;
        $header = $this->header;
        if ($header === null) {
            if ($provider instanceof ActiveDataProvider && $provider->query instanceof ActiveQueryInterface) {
                /* @var $model Model */
                $model = new $provider->query->modelClass;
                $header = $model->getAttributeLabel($this->name);
            } else {
                $models = $provider->getModels();
                if (($model = reset($models)) instanceof Model) {
                    /* @var $model Model */
                    $header = $model->getAttributeLabel($this->name);
                } else {
                    $header = Inflector::camel2words($this->name);
                }
            }
        }

        if ($this->align)
            $this->htmlOptions['align']=$this->align;

        if ($this->width)
            $this->headerOptions['width'] = $this->width;

        $column = [
            'options'=>$this->htmlOptions,
            'header'=>$header,
        ];
        if ($this->hidden === true) {
            $column['visible'] = false;
        }

        $column['sortable'] = $this->sortable;

        if ($this->headerOptions) {
            $column['headerOptions'] = $this->headerOptions;
        }
        return $column;
    }

    public function renderDataCell($row, $data){
        if($this->value!==null) {
            $value = call_user_func_array($this->value, [
                'data' => $data,
                'row' => $row,
                'columnData' => $this->data
            ]);
        } elseif($this->name!==null) {
            $value = ArrayHelper::getValue($data, $this->name);
        }
        if ($this->format){
            $function = is_array($this->format) ? ArrayHelper::getValue($this->format, 0) : $this->format;
            $arg1 = is_array($this->format) ? ArrayHelper::getValue($this->format, 1) : null;
            $function = 'as' . ucfirst($function);
            $value = Yii::$app->formatter->$function($value, $arg1);
        }
        return $value;
    }
}
