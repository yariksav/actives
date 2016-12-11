<?php

namespace yariksav\actives\dialog;

use yii\helpers\ArrayHelper;
use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\PermissionTrait;
use yariksav\actives\base\VisibleTrait;

class BaseDialog extends ActiveObject {

    use PermissionTrait;
    use VisibleTrait;

    public $key;
    public $width;

    protected $_action;
    protected $isNewRecord;
    protected $_config;
    public $componentName = 'Dialog'; //????

    public function __construct($config = []) {
        $this->key =  ArrayHelper::getValue($config, 'key');
        $this->isNewRecord = !$this->key;
        $this->_config = $config;

        $this->_init();
        parent::__construct($config);
        $this->action = ArrayHelper::getValue($config, 'action', 'load');
    }

    protected function _init(){

    }

    public function run() {
        if (!$this->visible) {
            if (Yii::$app->user->isGuest) {
                throw new HttpException(401, Yii::t('app.error', 'Please login for this request.'));
            } else {
                throw new HttpException(423, Yii::t('app.error', 'You are not authorized to perform this action.'));
            }
        }
        // todo Add privilege
    }

    public function setAction($value) {
        $this->_action = $value;
    }

    public function getAction() {
        return $this->_action;
    }
}
