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
//    protected $_data;
//
//
//    public function getData() {
//        return $this->_data;
//    }

    function init() {
        if ($this->_provider && $this->_plugins) {
            $this->_plugins->setProvider($this->_provider);
        }
    }

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
        $models = $this->_provider->getModels();
        $keys = $this->_provider->getKeys();
        $rows = [];
        foreach (array_values($models) as $index => $model) {
            $rows[] = $this->renderItem($model, $keys[$index], $index);
        }
        $this->_response->data = new \stdClass();
        $this->_response->data->rows = $rows;
        $this->_response->data->total = $this->_provider->getTotalCount();
    }

    protected function renderOptions() {
        parent::renderOptions();
        if ($this->title) {
            $this->_response->title = $this->title;
        }
    }

}