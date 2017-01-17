<?php
namespace yariksav\actives\view;

use yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\base\ViewContextInterface;
use yariksav\actives\base\ViewerTrait;

class ListView extends ActiveList
{
    /**
     * @var array the HTML attributes for the container of the rendering result of each data model.
     * The "tag" element specifies the tag name of the container element and defaults to "div".
     * If "tag" is false, it means no container element will be rendered.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $itemOptions;
    /**
     * @var string|callable the name of the view for rendering each data item, or a callback (e.g. an anonymous function)
     * for rendering each data item. If it specifies a view name, the following variables will
     * be available in the view:
     *
     * - `$model`: mixed, the data model
     * - `$key`: mixed, the key value associated with the data item
     * - `$index`: integer, the zero-based index of the data item in the items array returned by [[dataProvider]].
     * - `$owner`: ListView, this widget instance
     *
     * Note that the view name is resolved into the view file by the current context of the [[view]] object.
     *
     * If this property is specified as a callback, it should have the following signature:
     *
     * ```php
     * function ($model, $key, $index, $widget)
     * ```
     */

    public $itemView;
    /**
     * @var array additional parameters to be passed to [[itemView]] when it is being rendered.
     * This property is used only when [[itemView]] is a string representing a view name.
     */
    public $viewParams = [];

    public $cmp = 'ListView';

    /**
     * Renders a single data model.
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key value associated with the data model
     * @param integer $index the zero-based index of the data model in the model array returned by [[dataProvider]].
     * @return string the rendering result
     */
    public function renderItem($model, $key, $index)
    {
        if ($this->itemView === null) {
            $content = $key;
        } elseif (is_string($this->itemView)) {
            $content = $this->getView()->render($this->itemView, array_merge([
                'model' => $model,
                'key' => $key,
                'index' => $index,
                'owner' => $this,
            ], $this->viewParams), $this);
        } else {
            $content = call_user_func($this->itemView, $model, $key, $index, $this);
        }
        $options = $this->itemOptions;
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        //$options['data-key'] = is_array($key) ? json_encode($key, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $key;
        return [
            '_key'=>$key,
            '_tag'=>$tag,
            '_plugins' => $this->_plugins->buildItem($model),
            '_options'=>$options,
            '_content'=>$content,
        ];
    }

}
