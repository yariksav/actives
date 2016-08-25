<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 22.05.2016
 * Time: 20:36
 */

namespace yariksav\actives\controls;

use Yii;

class AutocompleteControl extends SelectizeControl
{
    /**
     * @inheritdoc
     */
    public $requireModel = true;

    public function init(){
        $this->type = 'autocomplete';
        $this->placeholder = Yii::t('actives', 'Please type your search query here');
    }

    /**
     * @inheritdoc
     */
    public function getCollection() {
        if (is_callable($this->_collection)) {
            $collection = call_user_func_array($this->_collection, [
                'data' => $this->_model,
                'query' => $this->config['query'],
                'value' => $this->_value,
                'owner' => $this->owner
            ]);
        } else if (is_array($this->_collection)){
            $collection = $this->_collection;
        }
        return $this->renderItems($collection);
    }

}