<?php

namespace yariksav\actives\behaviors;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class DatetimeBehavior extends TimestampBehavior
{
    /**
     * @inheritdoc
     */
    public $createdAtAttribute = 'createdAt';
    /**
     * @inheritdoc
     */
    public $updatedAtAttribute = 'updatedAt';

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            return new Expression('CURRENT_TIMESTAMP');
        }
        return parent::getValue($event);
    }
}
