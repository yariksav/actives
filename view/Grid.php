<?php
namespace yariksav\actives\view;

use yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;



use yariksav\actives\view\columns\ColumnMgr;



class Grid extends ActiveList
{
    protected $_columns;
    public $componentName = 'Grid';

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
    /**
     * Renders all data models.
     * @return string the rendering result
     */
//    public function renderItems(){
//        //$data = [
//        //    'rows'=>[]
//        //];
//
//        if (!$this->_provider) {
//            return null;
//        }
//
//        $models = array_values($this->_provider->getModels());
//        $keys = $this->_provider->getKeys();
//        $rows = [];
//        foreach ($models as $index => $model) {
//            $key = $keys[$index];
////            if ($this->beforeRow !== null) {
////                $row = call_user_func($this->beforeRow, $model, $key, $index, $this);
////                if (!empty($row)) {
////                    $rows[] = $row;
////                }
////            }
//
//            $rows[] = $this->renderItem($model, $key, $index);
//
////            if ($this->afterRow !== null) {
////                $row = call_user_func($this->afterRow, $model, $key, $index, $this);
////                if (!empty($row)) {
////                    $rows[] = $row;
////                }
////            }
//        }
////        $models = $this->_provider->getModels();
////        $keys = $this->_provider->getKeys();
////            if (($count = $this->_provider->getCount()) > 0) {
////                if (count($data) > 0) {
////                    foreach ($data as $row => $item) {
////                        $this->response->data->rows[] = $this->renderItem($row, $item);
////                    }
////                }
////            }
////        }
//        $this->response->data = new \stdClass();
//        $this->response->data->rows = $rows;
//        $this->response->data->total = (int)$this->_provider->getTotalCount();
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


    public function actionExport(){
        $this->prepareData();
        $this->_provider->pagination = false;

        $data = $this->_provider->getModels();
        $exportData = [];
        //todo
//        if (($count = $this->_provider->getCount()) > 0) {
//            if (count($data) > 0) {
//                foreach ($data as $row => $item) {
//                    $exportData[] = $this->_columns->buildRow($row, $item);
//                }
//            }
//        }

        $this->_plugins->get('export')->current->export($exportData);

        Yii::$app->response->format = 'html';
        Yii::$app->end();
    }

}