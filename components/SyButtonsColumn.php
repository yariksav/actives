<?php

namespace yariksav\actives\components;

class SyButtonsColumn extends SyDataColumn{

    public $htmlOptions=array('align'=>'center', 'class'=>'grid-buttons');
    public $buttons;
    public $header = '';
    public $name = 'buttons';
    public $dialog = false;
    public $primaryKey;

    public function init(){
        parent::init();
        $this->width = (sizeof($this->buttons)*35).'px';
        $this->align = 'center';
        $this->sortable = false;
    }

    protected function renderDataCellContent($row,$data){
        $buttons = array();
        foreach($this->buttons as $id=>$button) {
            if (!is_array($button)){
                $id = $button;
                $button = array();
            }
            $renderedButton = $this->renderButton($id,$button,$row,$data);
            if ($renderedButton)
                $buttons[] = $renderedButton;
        }
        return $buttons;
    }

    //public function renderHeaderCell(){
        //$this->headerHtmlOptions['width'] = (sizeof($this->buttons)*35).'px';
        //parent::renderHeaderCell();
    //}

    protected function renderButton($id, $button, $row, $data){
        if (isset($button['visible']) && !$this->evaluateExpression($button['visible'],array('data'=>$data, 'row'=>$row)))
            return;
        $icon = isset($button['icon']) ? $button['icon'] : '';

        $label=isset($button['label']) ? $button['label'] : $id;
        if (is_callable($label))
            $label=$this->evaluateExpression($label,array('data'=>$data));

        $options=isset($button['options']) ? $button['options'] : array();
        if (is_callable($options))
            $options = $this->evaluateExpression($options,array('data'=>$data));


        //$rowid = is_callable($this->rowid) ? $this->evaluateExpression($this->rowid,array('data'=>$data)) : $data->primaryKey;

        if ($this->primaryKey){
            if (is_array($data))
                $rowid = $data[$this->primaryKey];
            else
                $rowid = $data->{$this->primaryKey};
        }
        else
            $rowid = $data->primaryKey;


        if ($this->dialog) {
            if ($id == 'update') {
                if (!$options) {
                    //$options = array('data-dialog'=>$this->dialog);
                    //$options = array('sy-dialog' => $this->dialog);// . '&id=' . $rowid);
                }
                $icon = 'pencil';
                $label = \Yii::t('app', 'Update');
                $options['data-dialog']=$this->dialog;
            }
            if ($id == 'delete') {
                //if (!$options)
                    //$options = array('sy-dialog' => $this->dialog . '&actiontype=D&id=' . $rowid);
                $icon = 'times';
                $label = \Yii::t('app', 'Delete');
                $options['data-event']=$id;
                $options['data-dialog']=$this->dialog;
            }
        }

        if(!isset($options['title']))
            $options['title']=$label;

        if ($icon) {
            if (strpos($icon, 'fa') === false)
                $icon = 'fa fa-'.implode('fa-', explode(' ', $icon));
            $options['icon']=$icon;
        }
        return array('type'=>'icon', 'options'=>$options);
    }
}
