<?php
namespace yariksav\actives\helpers;

class Html extends \yii\helpers\Html
{
    public static function i($class) {
        return '<i class="' . $class . '"></i> ';//
        //CHtml::tag(ag('i', array('class'=>$class));
    }

    public static function fa($class, $options = array()) {
        $options['class'] = 'fa ' . $class;
        return Html::tag('i', '', $options);
    }
}
?>