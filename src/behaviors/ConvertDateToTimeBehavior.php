<?php

namespace hesabro\helpers\behaviors;

use Yii;
use yii\db\BaseActiveRecord;
use yii\base\Behavior as BaseBehavior;

/**
 * Class ConvertDateToTimeBehavior
 * @package common\behaviors
 * @author Nader <nader.bahadorii@gmail.com>
 * @property yii\db\ActiveRecord $owner
 */
class ConvertDateToTimeBehavior extends BaseBehavior
{
    public array $attributes = [];

    public array $scenarios = [];

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
        ];
    }

    /**
     * @return bool
     */
    public function afterFind(): bool
    {
        if (in_array($this->owner->getScenario(), $this->scenarios)) {
            foreach ($this->attributes as $attribute) {
                if (!empty($this->owner->{$attribute})) {
                    $this->owner->{$attribute} = Yii::$app->jdf::jdate('Y/m/d H:i:s', $this->owner->{$attribute});
                }
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function beforeSave(): bool
    {
        if (in_array($this->owner->getScenario(), $this->scenarios)) {
            foreach ($this->attributes as $attribute) {
                if (!empty($this->owner->{$attribute})) {
                    $this->owner->{$attribute} = strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($this->owner->{$attribute}));
                }
            }
        }
        return true;
    }
}