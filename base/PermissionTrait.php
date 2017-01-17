<?php

namespace yariksav\actives\base;

use yii;
/**
 * Trait Permission Trait
 * @property string|array|bool $permissions
 * @package yariksav\actives\base
 */
trait PermissionTrait
{
    /**
     * @var  string|array|bool necessary permissions for object
     */
    public $permissions;

    /**
     * Checks if user has particular permission. Calculates only one time for perfomance
     * @return boolean
     */
    public function hasPermissions(){
        if ($this->permissions === null) {
            $this->permissions = true;
        }
        if (is_callable($this->permissions)) {
            $this->permissions = call_user_func($this->permissions);
        }
        if (is_string($this->permissions)) {
            if ($this->permissions === '*') {
                $this->permissions = true;
            } else if ($this->permissions === '@') {
                $this->permissions = !Yii::$app->user->isGuest;
            } else {
                $this->permissions = [$this->permissions];
            }
        }
        if (is_array($this->permissions)) {
            foreach ($this->permissions as $permission) {
                if (!Yii::$app->user->can($permission)) {
                    $this->permissions = false;
                    return false;
                }
            }
            $this->permissions = true;
        }
        return $this->permissions;
    }

}