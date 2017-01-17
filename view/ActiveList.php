<?php
namespace yariksav\actives\view;

use yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;

use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\Exception;
use yariksav\actives\view\plugins\PluginMgr;

abstract class ActiveList extends ActiveView
{
    public $title;
    public $identifier = 'id';

    protected $_provider;

    public function setData($value) {
        $this->_data = $value;

        if (is_callable($this->data)) {
            $this->_data = call_user_func_array($this->_data, [
                'data' => $this->baseModel,
                'grid' => $this
            ]);
        }

        if ($this->_data instanceof BaseDataProvider) {
            $this->_provider = $this->_data;
        }
        else if (is_array($this->_data)) {
            $this->_provider = new ArrayDataProvider(['allModels'=>$this->_data]);
        }
        else if ($this->_data instanceof ActiveQuery) {
            $this->_provider = new ActiveDataProvider(['query'=>$this->_data]);
        }
    }

    public function getProvider() {
        return $this->_provider;
    }

    public function setProvider($value) {
        if ($value instanceof ArrayDataProvider) {
            $this->_data = $value->allModels;
        }
        else if ($value instanceof ActiveDataProvider) {
            $this->_data = $value->query;
        }
        else {
            $this->_data = null;
        }
        $this->_provider = $value;
    }


    /**
     * Renders all data models.
     * @return string the rendering result
     */
    public function renderItems()
    {
        $this->trigger('beforeData');
        $models = $this->_provider->getModels();
        $keys = $this->_provider->getKeys();
        $rows = [];
        foreach (array_values($models) as $index => $model) {
            $rows[] = $this->renderItem($model, $keys[$index], $index);
        }
        $this->_response->data = new \stdClass();
        $this->_response->data->collection = $rows;
        $this->_response->data->total = $this->_provider->getTotalCount();

        if ($this->_provider->pagination) {
            $this->_response->data->pagination = [
                'limit' => $this->_provider->pagination->getLimit(),
                'offset' => $this->_provider->pagination->getOffset()
            ];
        }
    }

    /**
     * Renders a single data model.
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key value associated with the data model
     * @param integer $index the zero-based index of the data model in the model array returned by [[dataProvider]].
     * @return string the rendering result
     */
    public function renderItem($model, $key, $index)
    {
//        $row = [
//            'buttons' => $this->_buttons->buildRow($model),
//            'cells' => $this->_columns->buildRow($model, $key, $index),
//            'key' => $key,
//        ];
//        return $row;
    }

    protected function renderOptions() {
        parent::renderOptions();
        if ($this->title) {
            $this->_response->title = $this->title;
        }
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

        $this->_plugins['export']->current->export($exportData);
//
//        Yii::$app->response->format = 'html';
//        Yii::$app->end();
    }


}