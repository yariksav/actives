<?php

namespace yariksav\actives\view\columns;

use yii;
use yii\base;
use yii\base\Object;
use yariksav\actives\base\CollectionMgr;
use yariksav\actives\base\Component;


class ColumnMgr extends CollectionMgr
{

    public static $builtInColumns = [
        'status' => 'yariksav\actives\view\columns\StateColumn',
        'date' => 'yariksav\actives\view\columns\DateColumn',
        'serial' => 'yariksav\actives\view\columns\SerialColumn'
    ];

    protected function createObject($params) {
        if (empty($params['name']) || is_int($params['name'])) {
            throw new \Exception('Please get the name for control');
        }

        if (empty($params['type']) && empty($params['class'])) {
            $params['class'] = Column::className();
        }

        if (isset($params['type'])) {
            $type = $params['type'];
            if (isset(static::$builtInColumns[$type])) {
                $type = static::$builtInColumns[$type];
            }
            if (is_array($type)) {
                $params = array_merge($type, $params);
            } else {
                $params['class'] = $type;
            }
        }
        return Yii::createObject($params, [
            $this->owner
        ]);

/*        $name = $params['name'];
        if (empty($name) || is_int($name)) {
            throw new \Exception('Please get the name for action');
        }

        if (empty($params['class'])) {
            $params['class'] = Action::className();
        }
        $obj = Yii::createObject($params, [$this->owner]);
        // Disable previous action events if exists
        if (isset($this->_collection[$name]) && $this->_collection[$name] instanceof DialogAction) {
            $this->_collection[$name]->disableEvents();
        }
        return $obj;*/
    }


    /**
     * Creates column objects and initializes them.
     */
    protected function initColumns()
    {
        if ($this->_columns) foreach($this->_columns as $name => $col)
        {
            if (is_string($col)) {
                $column = $this->createDataColumn($col);
            } else {
                $column = Yii::createObject(array_merge($col, [
                    'class' => empty($col['class']) ? DataColumn::className() : __NAMESPACE__.'\\'.$col['class'],
                    'grid' => $this
                ]));
            }

            if(!$column->visible) {
                unset($this->_columns[$i]);
                continue;
            }
            $this->_columns[$i]=$column;
        }

        if ($this->_columns) foreach($this->_columns as $column) {
            $column->init();

            $filterValue = ($this->filter && $column->name && isset($this->filter[$column->name])) ? $this->filter[$column->name] : null;
            $this->filterPrepared[$column->name] = $column->prepareFilterValue($filterValue);

        }
    }



/*
    protected function createDataColumn($text)
    {
        if(!preg_match('/^([\w\.]+)(:(\w*))?(:(.*))?$/',$text,$matches))
            throw new CException(Yii::t('zii','The column must be specified in the format of "Name:Type:Label", where "Type" and "Label" are optional.'));
        $column = new DataColumn($this->owner);
        $column->name=$matches[1];
        if(isset($matches[3]) && $matches[3]!=='')
            $column->type=$matches[3];
        if(isset($matches[5]))
            $column->header=$matches[5];
        return $column;
    }*/

    public function build() {
        $columns = [];
        $columnsState = [];// todo ; (array)json_decode($this->getState('columnsState'));
        if ($this->_collection) foreach($this->_collection as $name => $col) {
            $column = $col->renderHeader();
            /*if ($this->columnSelection && $columnsState && isset($columnsState[$column['id']])) {
                $state = (array)$columnsState[$column['id']];
                $column['visible'] = filter_var(ArrayHelper::getValue($state, 'visible', true), FILTER_VALIDATE_BOOLEAN);
            }*/
            if (isset($column['options']) && !$column['options'])
                unset($column['options']);
            $columns[$name] = $column;
        }
        return $columns;
    }

    protected function footer(){
       /* $footer = array();
        if ($this->_columns) foreach($this->_columns as $col) {
            if (isset($col->footer)){
                if (is_callable($col->footer))
                    $col->footer = $this->evaluateExpression($col->footer,array('data'=>$this));
                $footer[$col->name]=$col->footer;
            }
        }
        if ($footer)
            $this->response->data->footer = $footer;*/
    }

    public function actionSetColumnsState(){
        $this->setState('columnsState', json_encode($this->request['columns']));
    }

    public function buildRow($model, $key, $index) {
        $result = [];
        if ($this->_collection) foreach($this->_collection as $column) {
            $result[$column->name] = $column->renderDataCell($model, $key, $index);
        }
        return $result;
    }

}