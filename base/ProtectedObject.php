<?php

namespace yariksav\actives\base;

use yariksav\actives\base\PermissionTrait;
use yariksav\actives\base\VisibleTrait;

class ProtectedObject extends \yii\base\Object
{
    use PermissionTrait;
    use VisibleTrait;
}