<?php

namespace yariksav\actives\view;

use yii\helpers\Url;
use yii\helpers\Html;

class Tree extends ActiveView
{
    protected $selected = array();
    protected $buttons;
    public $autoSelect = true;
    public $contextMenu = false;

    protected function _init(){
        if (isset($this->request['selected'])){
            $this->selected = is_array($this->request['selected']) ? $this->request['selected'] : array($this->request['selected']);
        }
        $this->buttons = isset($this->buttons) ? $this->evaluateExpression($this->buttons, ['grid'=>$this]) : $this->buttons();
    }

    protected function _wrap($data, $view){
        $name = $this->name . '-' . time();
        $view->registerJs(";$(\".$name\").sytree(".json_encode($data).");", \yii\web\View::POS_READY);
        return Html::tag('div', '', ['class' => $name]);
    }


    public function actionInit(){
        $this->renderData();
        $this->renderOptions();
    }

    public function actionLoad(){
        $this->renderData();
    }


    public function actionSelected(){
        var_export($this->request);
        $this->setState('selected', $this->request['selected']);
    }

    protected function data(){}

    protected function renderOptions(){
        $this->response->name = $this->name;
        $this->response->url = Url::toRoute('actives/api/grid');
        //$this->response->selected = $this->request['selected'];
        $this->response->labels = array(
            'refresh'=>\Yii::t('actives', 'Refresh'),
        );
        $this->response->contextmenu = $this->contextMenu;
        $this->response->buttons = $this->renderButtons($this->buttons);
        $this->response->autoSelect = $this->autoSelect;
        if ($this->scripts){
            $this->response->scripts = $this->scripts;
        }
    }

    protected function renderNodes(&$nodes){
        $withcontext = isset($this->request['contextmenu']) ? $this->request['contextmenu'] !== false : true;
        foreach($nodes as &$node){
            if ($withcontext)
                $node['buttons'] = $this->renderButtons($this->buttons(), $node);
            if (isset($node['children']) && is_array($node['children']))
                $this->renderNodes($node['children']);
        }
        return $nodes;
    }

    protected function renderData(){
        //$this->response->data = new stdClass();
        $data = $this->data();
        $this->response->data = $this->renderNodes($data);
    }

    protected function buildTree($params, $parent){
        $array = isset($params['data']) ? $params['data'] : array();
        $result = array();
        foreach($array as $index=>$row) {
            $rowId = $this->evaluateExpression($params['id'], array('data'=>$row));
            $rowParent = $this->evaluateExpression($params['parent'], array('data'=>$row));
            if($rowParent == $parent) {
                $row = $this->evaluateExpression($params['row'], array('data'=>$row));
                $children = $this->buildTree($params, $rowId);
                if ($children) {
                    $row['children'] = $children;
                }
                if ($this->selected && in_array($rowId, $this->selected))
                    $row['selected'] = true;
                $result[] = $row;
                unset($array[$index]);
            }
        }
        return $result;
    }

    protected function buttons(){
        return array();
    }

}