<?php
namespace yariksav\actives\view;

use yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;

use yariksav\actives\view\columns\ColumnMgr;
use yariksav\actives\view\buttons\ButtonMgr;




class Grid extends ActiveView
{
    public $title;
    public $type;
    public $identifier = 'id';
    public $baseModel;// base model data if grid linked to some model
    public $dataProvider;

    protected $_columns;
    protected $_buttons;


    function __construct($config = []) {
        $this->_columns = new ColumnMgr($this);
        $this->_buttons = new ButtonMgr($this);

        parent::__construct($config);
    }

    public function setColumns($value) {
        $this->_columns->load($value);
    }

    public function setButtons($value) {
        $this->_buttons->load($value);
    }

    public function getResponse(){
        $response = $this->response;
//        if (isset($response->data)){
//            $response->system = base64_encode(json_encode($this->system));
//        }
        return $response;
    }

    protected function prepareData(){
        if (is_callable($this->data)) {
            $this->data = call_user_func_array($this->data, [
                'data' => $this->baseModel,
                'grid' => $this
            ]);
        }

        if ($this->data instanceof BaseDataProvider) {
            $provider = $this->data;
        }
        else if (is_array($this->data)) {
            $provider = new ArrayDataProvider(['allModels'=>$this->data]);
        }
        else if ($this->data instanceof ActiveQuery) {
            $provider = new ActiveDataProvider(['query'=>$this->data]);
        }
        else {
            throw new yii\base\Exception('Unknown type of data');
        }
        $this->_plugins->setProvider($provider);
        $this->dataProvider = $provider;
    }

    public function actionInit(){
        $this->prepareData();
        $this->renderTableBody();
        $this->renderOptions();
        //$this->renderFooter();
    }

    public function actionLoad(){
        $this->prepareData();
        $this->renderTableBody();
        //$this->renderFooter();
    }

    protected function renderOptions(){
        parent::renderOptions();

        if ($this->title) {
            $this->response->title = $this->title;
        }
        $this->response->name = $this->name;
        $this->response->url = Url::toRoute('/actives/api/grid');

        $this->response->columns = $this->_columns->build();
        $this->response->buttons = $this->_buttons->build();

        $this->response->affect = $this->affect;

        $this->response->params = new \stdClass();
        $this->response->params->class = $this->className();
    }

    public function renderTableBody(){
        $this->response->data = new \stdClass();
        $this->response->data->rows = [];
        if ($this->dataProvider) {
            $data = $this->dataProvider->getModels();
            if (($count = $this->dataProvider->getCount()) > 0) {
                if (count($data) > 0) {
                    foreach ($data as $row => $item) {
                        $this->response->data->rows[] = $this->renderTableRow($row, $item);
                    }
                }
            }
            $total = $this->dataProvider->getTotalCount();
            $this->response->data->total = (int)$total;
        }
    }

    public function renderTableRow($index, $data)
    {
        $htmlOptions = [];
        $row = [
            'buttons' => $this->_buttons->buildRow($data),
            'cells' => $this->_columns->buildRow($index, $data),
        ];

        if ($htmlOptions) {
            $row['options'] = $htmlOptions;
        }
        $id = null;
        if (is_object($data)) {
            $id = $data->{$this->identifier};
        } else if (isset($data[$this->identifier])) {
            $id = $data[$this->identifier];
        }

        if ($id) {
            $row['params'] = [
                'id' => $id
            ];
        }
        return $row;
    }


    public function actionExport(){
        $this->prepareData();
        $this->dataProvider->pagination = false;
        $this->_exports->current = $this->type;
        $this->renderTableBody();

        //$exports = $this->export();

        //$export = $exports[$this->request['type']];
        //$this->response->columns = $this->renderColumns();//!!!!!!!!!!!
        //$data = $this->response;//json_encode($this->response->data);
        //call_user_func($export['export'], $data);
        //$export['export']($data);
        $data = [];
        if ($this->response->data->rows) foreach($this->response->data->rows as $row){
            $item = [];
            foreach($row['cells'] as $cell){
                if (!is_string($cell))
                    $item[] = $cell;
            }
            $data[] = $item;
        }

        $this->_exports->current->export($data);

        Yii::$app->response->format = 'html';
        Yii::$app->end();
        //        return $export;
        //$this->convert_to_csv($data, 'report.csv', ';');
    }

    //    public function filter($key){
//        return $this->filter && isset($this->filter[$key]) ? $this->filter[$key] : null;
//    }

//    public static function prepareJsDefaults($scriptWrap = true){
//        $defaults = [
//            'labels'=>[
//                'all'=>Yii::t('actives', 'All'),
//                'infos'=>Yii::t('actives', 'Showing {start} to {end} of {total} entries'),
//                'infoTotal'=>Yii::t('actives', 'Entries: {total}'),
//                'loading'=>Yii::t('actives', 'Loading...'),
//                'noResults'=>Yii::t('actives', 'No results found'),
//                'refresh'=>Yii::t('actives', 'Refresh'),
//                'search'=>Yii::t('actives', 'Search'),
//                'exports'=>Yii::t('actives', 'Export to file'),
//                'filter'=>Yii::t('actives', 'Filter'),
//            ],
//            'ajax'=>[
//                'url'=>Url::toRoute('actives/api/grid')
//            ]
//        ];
//        return ';$.fn.sygrid.defaults('.json_encode($defaults).');';
//    }

}