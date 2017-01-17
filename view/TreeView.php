<?php
namespace yariksav\actives\view;

use yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;



use yariksav\actives\view\columns\ColumnMgr;



class TreeView extends ActiveList
{
    //protected $_columns;
    public $cmp = 'Tree';

    function __construct($config = []) {
      //  $this->_columns = new ColumnMgr($this);
        parent::__construct($config);
    }
//
//    public function setColumns($value) {
//        $this->_columns->load($value);
//    }

//    protected function renderOptions(){
//        parent::renderOptions();
//        $this->response->columns = $this->_columns->build();
//    }
    /**
     * Renders a single data model.
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key value associated with the data model
     * @param integer $index the zero-based index of the data model in the model array returned by [[dataProvider]].
     * @return string the rendering result
     */
    public function renderItem($model, $key, $index)
    {
//        $htmlOptions = [];
        $row = [
            'buttons' => $this->_buttons->buildRow($model),
            'cells' => $this->_columns->buildRow($model, $key, $index),
            'key' => $key,
            //'params' => ['id' => $key]
        ];

//        if ($htmlOptions) {
//            $row['options'] = $htmlOptions;
//        }

        return $row;
    }

}