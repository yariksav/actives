<?php
namespace app\modules\sy\helpers;

use yii\helpers\Html;


class SyHtml extends Html
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