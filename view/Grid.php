<?php
namespace yariksav\actives\view;

use yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yariksav\actives\Module;
use yariksav\actives\controls\ControlMgr;
use yariksav\actives\view\columns\ColumnMgr;
use yariksav\actives\view\buttons\ButtonMgr;
use yariksav\actives\view\plugins\PluginMgr;


class Grid extends ActiveView
{
    //!!
    public $sort;
    public $page;
    public $newcount;
    //!!
    const C_GRID_SELECTION =1;


    public $filter = [];
    protected $filterPrepared = [];
    protected $filters;
    public $title;
    //protected $dateFilter;

    //public $columnSelection = false;
    public $refreshButton = true;
    public $saveState = false;

    public $dataProvider;
    //public $blankDisplay='&nbsp;';
    //public $nullDisplay='&nbsp;';

    protected $rowCount = 10;
    protected $identifier = 'id';
    protected $tree;


    public $itemOptions;
    protected  $storage;


    // new!
    protected $_columns;
    protected $_buttons;
    protected $_plugins;

    function __construct($config = []) {
        $this->_columns = new ColumnMgr($this);
        $this->_buttons = new ButtonMgr($this);
        $this->_plugins = new PluginMgr($this);
        parent::__construct($config);
    }

    public function setColumns($value) {
        $this->_columns->load($value);
    }

    public function setButtons($value) {
        $this->_buttons->load($value);
    }

    public function setPlugins($value) {
        $this->_plugins->load($value);
    }

    protected function _init(){



        $this->init();
        $this->rowCount = $this->getState('rowCount', $this->rowCount);

        $this->filter = $this->evaluateExpression($this->filter);

        // Save filters for recovery state
        if ($this->saveState) {
            if ($this->request['method'] == 'init') {
                $rememberedFilter = $this->getState('filters');
                if ($rememberedFilter && is_array($rememberedFilter)) foreach ($rememberedFilter as $key=>$value)
                    if (empty($this->filter[$key]))
                        $this->filter[$key] = $value;
                    //$this->filter = array_merge($this->filter, $rememberedFilter);
            }
            if ($this->request['method'] == 'load') {
                $this->setState('filters', $this->filter);
            }
        }


        //$this->searchPhrase = isset($this->request['searchPhrase']) ? $this->request['searchPhrase'] : false;
        //$this->filter = isset($this->request['filter']) ? $this->request['filter'] : array();
/*		if (isset($this->request['data'])) {
            $this->dataProvider = $this->request['data'];
            if (!$this->dataProvider instanceof CDataProvider)
                $this->dataProvider  = new CArrayDataProvider($this->dataProvider);
        }
        else
            $this->dataProvider = $this->data();*/


        //$this->_columns = isset($this->columns) ? $this->evaluateExpression($this->columns, ['grid'=>$this]) : $this->columns();
        //$this->initColumns();

    }

//
//    protected function _wrap($data, $view){
//        $name = $this->name . '-' . time();
//        $view->registerJs(";$(\".$name\").sygrid(".json_encode($data).");", yii\web\View::POS_READY);
//        return Html::tag('div', '', ['class' => $name.'  '.$this->name.' grid-view clear-top']);
//    }

    public function getResponse(){
        $response = $this->response;
        if (isset($response->data)){
            $response->system = base64_encode(json_encode($this->system));
        }
        return $response;
    }

    public function init(){}
    public function data(){}


    public function filters(){}
    public function export(){}

    /*
     *  ACTIONS
     */

    protected function prepareData(){
        $model = isset($this->request['model']) ? $this->request['model'] : null;
        $data = isset($this->request['data']) ? $this->evaluateExpression($this->request['data'], ['data'=>$model, 'grid'=>$this]) : $this->data();
        $this->dataProvider = ($data instanceof ActiveDataProvider || $data instanceof ArrayDataProvider)? $data : new ArrayDataProvider(['allModels'=>$data]);
    }

    public function actionInit(){
        //$this->prepareFilters();

        $this->prepareData();
        $this->setSorting();
        $this->setPagination();
        $this->renderTableBody();

        $this->renderOptions();

        //$this->renderFooter();
    }

    public function actionLoad(){
        $this->prepareData();
        $this->setSorting();
        $this->setPagination();
        $this->renderTableBody();
        //$this->renderFooter();
    }

    public function actionSetCount(){
        if (isset($this->request['newcount'])) {
            $this->setState('rowCount', $this->rowCount = $this->request['newcount']);
        }
    }

    public function renderTitle(){

    }

    public function actionExport(){
        $this->prepareData();
        $this->dataProvider->pagination = false;
        $this->setSorting();
        $this->renderTableBody();
        $exports = $this->export();

        $export = $exports[$this->request['type']];
        //$this->response->columns = $this->renderColumns();//!!!!!!!!!!!
        $data = $this->response;//json_encode($this->response->data);
        call_user_func($export['export'], $data);
        //$export['export']($data);
        /*array();
        if ($this->response->data->rows) foreach($this->response->data->rows as $row){
            $item = array();
            foreach($row['cells'] as $cell){
                if (!is_string($cell))
                    $item[] = $cell;
            }
            $data[] = $item;
        }*/

        Yii::$app->response->format = 'html';
        return $export;
        //$this->convert_to_csv($data, 'report.csv', ';');
    }

    protected function setSorting(){
        $sort = [];
        if (isset($this->sort)) foreach($this->sort as $key => $value) {
            $sort[] = ($value == 'desc' ? '-' : '').$key;
        }
        $sort = implode(',', $sort);
        if ($sort)
            $_GET['sort'] = $sort;

        /*if (isset($this->request['sort'])) {
            $this->dataProvider->setSort([]);
            foreach ($this->request['sort'] as $key => $value) {
                $this->dataProvider->query->addOrderBy(new Expression($key . ' ' . $value));
            }
        }*/
    }

    protected function setPagination(){
        if ($this->dataProvider) {
/*            if (!$this->pagination || $this->rowCount < 0) {
                $this->dataProvider->pagination = false;
            } else if ($this->dataProvider->pagination) {
                $this->dataProvider->pagination->pageSize = $this->rowCount;
                $this->dataProvider->pagination->page = isset($this->request['page']) ? $this->request['page'] - 1 : 0;
            }*/
        }
    }

    function convert_to_csv($input_array, $output_file_name, $delimiter)
    {
        /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
        $f = fopen('php://memory', 'w');
        /** loop through array  */
        foreach ($input_array as $line) {
            /** default php csv handler **/
            fputcsv($f, $line, $delimiter);
        }
        /** rewrind the "file" with the csv lines **/
        fseek($f, 0);
        /** modify header to be downloadable csv file **/
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
        /** Send file to browser for download */
        fpassthru($f);
    }



    protected function prepareFilterFalues(){

    }

    protected function prepareFilters(){
        $this->filters = $this->filters();

        // Get filter from colimn if exists
        if ($this->_columns) foreach($this->_columns as $col) {
            $filterPossibleValue = $this->filter && $col->name && isset($this->filter[$col->name]) ? $this->filter[$col->name] : null;

            $filter = $col->renderFilterContent($filterPossibleValue);
            if ($filter)
                $this->filters[] = $filter;
        }

        // get filter from filters in settings
        if ($this->filters) foreach($this->filters as &$filter) {
            if (isset($filter['type']) && $filter['type']=='select')
                $filter['empty']=' ';
            if (isset($filter['name']) && isset($filter['value']) && empty($this->filter[$filter['name']]))
                $this->filter[$filter['name']] = $filter['value'];

            //if (empty($this->filter['label']))
            //	$this->filters['label'] = $this->blankDisplay;
        }
        //yii::log(var_export($this->filter, true), CLogger::LEVEL_INFO, 'grid');

        return $this->filters;
    }




    protected function renderOptions(){
        //$options = new stdClass();
        if ($this->title) {
            $this->response->title = $this->title;
        }
        $this->response->name = $this->name;
        $this->response->url = Url::toRoute('/actives/api/grid');

        //$this->response->columnSelection = $this->columnSelection;
        $this->response->refreshButton = $this->refreshButton;
        $this->response->plugins = $this->_plugins->build();


        //$this->response->export = $this->renderExport();

        $this->response->columns = $this->_columns->build();
        $this->response->buttons = $this->_buttons->build();
        $this->response->affect = $this->affect;
        //renderButtons($this->_buttons);

        if ($this->filters) {
            $helper = new ControlMgr($this->filter);
            //!!! $this->response->filters = $helper->buildControls($this->filters);
        }
        //$this->response->filter = $this->filter;
        /*$this->response->filter = $this->filter;
        if ($this->tree){
            $tree = SyView::getInstance($this->tree);
            if ($tree)
                $this->response->tree = $tree->getResponse();;
        }*/

        $this->response->params = new \stdClass();
        $this->response->params->class = $this->className();
        $this->response->params->filter = $this->filter;
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
            $pagination = $this->dataProvider->getPagination();
            if ($pagination) {
                $total = $this->dataProvider->getTotalCount();
                $this->response->data->total = (int)$total;
                $this->response->data->page = $pagination->page + 1;
                $this->response->data->rowCount = (int)$this->rowCount;
            }
        }

    }

    public function renderTableRow($index, $data)
    {
        $htmlOptions=[];

        /*if($this->rowHtmlOptionsExpression!==null)
        {
            $options=$this->evaluateExpression($this->rowHtmlOptionsExpression,array('row'=>$row,'data'=>$data));
            if(is_array($options))
                $htmlOptions = $options;
        }

        if($this->rowCssClassExpression!==null)
        {
            $class=$this->evaluateExpression($this->rowCssClassExpression,array('row'=>$row,'data'=>$data));
        }
        elseif(is_array($this->rowCssClass) && ($n=count($this->rowCssClass))>0)
            $class=$this->rowCssClass[$row%$n];
        if(!empty($class))
        {
            if(isset($htmlOptions['class']))
                $htmlOptions['class'].=' '.$class;
            else
                $htmlOptions['class']=$class;
        }*/


        $row = [
            'buttons' => $this->_buttons->buildRow($data),
            'cells' => $this->_columns->buildRow($index, $data),
        ];

        if ($htmlOptions) {
            $row['options'] = $htmlOptions;
        }
        if (isset($data[$this->identifier])) {
            $row['params'] = [
                'id' => $data[$this->identifier]
            ];
        }

        //if ($this->itemOptions)
        //    $rowArray['options']=$this->evaluateExpression($this->itemOptions,['row'=>$row,'data'=>$data]);

        /*$buttons = $this->getButtons();
        if ($buttons && isset($buttons['row'])){
            $rowArray['buttons'] = [];
            foreach ($buttons['row'] as $index=>$button){
                if (isset($button['visible']) && !$this->evaluateExpression($button['visible'],array('data'=>$data, 'row'=>$row)))
                    continue;
                if (isset($button['data']) && is_callable($button['data'])) {
                    $button['data'] = $this->evaluateExpression($button['data'], array('data' => $data, 'row' => $row));
                }
                $rowArray['buttons'][] = $index;
            }
        }*/
        return $row;
    }

    public function renderExport(){
        $ret = [];
        $exports = $this->export();
        if ($exports) foreach ($exports as $key=>$export){
            if (is_string($export))
                $ret[$key] = $export;
            else{
                if (isset($export['visible']) && !$export['visible'])
                    return;
                $ret[$key] = $export['text'];
            }
        }
        return $ret ? $ret : null;
    }

    public function filter($key){
        return $this->filter && isset($this->filter[$key]) ? $this->filter[$key] : null;
    }

    public static function prepareJsDefaults($scriptWrap = true){
        $defaults = [
            'labels'=>[
                'all'=>Module::t('app', 'All'),
                'infos'=>Module::t('app', 'Showing {start} to {end} of {total} entries'),
                'infoTotal'=>Module::t('app', 'Entries: {total}'),
                'loading'=>Module::t('app', 'Loading...'),
                'noResults'=>Module::t('app', 'No results found'),
                'refresh'=>Module::t('app', 'Refresh'),
                'search'=>Module::t('app', 'Search'),
                'exports'=>Module::t('app', 'Export to file'),
                'filter'=>Module::t('app', 'Filter'),
            ],
            'ajax'=>[
                'url'=>Url::toRoute('actives/api/grid')
            ]
        ];
        return ';$.fn.sygrid.defaults('.json_encode($defaults).');';
    }

}