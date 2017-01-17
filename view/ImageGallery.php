<?php
namespace yariksav\actives\view;

use yariksav\actives\base\ActiveObject;
use yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\base\ViewContextInterface;
use yariksav\actives\base\ViewerTrait;

class ImageGallery extends ActiveView
{
    public $cmp = 'ImageGallery';
  //  public $renderItem;

//    public function renderItem($model, $key, $index)
//    {
//        return call_user_func($this->renderItem, $model, $key, $index, $this);
////        $options = $this->itemOptions;
////        $tag = ArrayHelper::remove($options, 'tag', 'div');
////        //$options['data-key'] = is_array($key) ? json_encode($key, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $key;
////        return [
////            'content'=>$content,
////            'key'=>$key,
////            'tag'=>$tag,
////            'options'=>$options,
////            'buttons' => $this->_buttons->buildRow($model),
////        ];
//    }

}
