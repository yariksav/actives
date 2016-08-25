<?php

namespace yariksav\actives\base;

use yii;
/**
 * Trait View Trait
 * @property string|array|bool $view
 * @package yariksav\actives\base
 * Need to implement interface ViewContextInterface 
 */
trait ViewerTrait
{
    /**
     * Returns the path of view file, linked to caller class. 
     * @return string the view path that prefixed to a relative view name.
     */
    public function getViewPath() {
        return realpath(dirname((new \ReflectionClass(static::class))->getFileName()));
    }


    private $_view;

    /**
     * Returns the view object that can be used to render views or view files.
     * The [[render()]] and [[renderFile()]] methods will use
     * this view object to implement the actual view rendering.
     * If not set, it will default to the "view" application component.
     * @return \yii\web\View the view object that can be used to render views or view files.
     */
    public function getView()
    {
        if ($this->_view === null) {
            $this->_view = Yii::$app->getView();
        }

        return $this->_view;
    }

    /**
     * Sets the view object to be used by this widget.
     * @param View $view the view object that can be used to render views or view files.
     */
    public function setView($view)
    {
        $this->_view = $view;
    }
}