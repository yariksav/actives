<?php
namespace yariksav\actives\view;

use yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;



use yariksav\actives\view\columns\ColumnMgr;



class Grid extends ActiveList
{
    protected $_columns;
    public $cmp = 'Grid';

    function __construct($config = []) {
        $this->_columns = new ColumnMgr($this);
        parent::__construct($config);
    }

    public function setColumns($value) {
        $this->_columns->load($value);
    }

    protected function renderOptions(){
        parent::renderOptions();
        $this->_response->columns = $this->_columns->build();
    }

    public function renderItem($model, $key, $index)
    {
        $row = array_merge($this->_columns->buildItem($model, $key, $index), [
            '_plugins' => $this->_plugins->buildItem($model),
            '_key' => $key,
        ]);
        return $row;
    }

}