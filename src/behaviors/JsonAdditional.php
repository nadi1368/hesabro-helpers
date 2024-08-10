<?php

namespace hesabro\helpers\behaviors;

use Yii;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\base\Behavior as BaseBehavior;

/**
 * @property ActiveRecord $owner
 */
class JsonAdditional extends BaseBehavior
{
    public $ownerClassName;

    public $AdditionalDataProperty = [];

    public $fieldAdditional;

    public $jsonDirtyAttributes = [];

    /**
     * If notSaveNull is false then the attributes in json data can be null
     * If notSaveNull is true, if the attribute is null, it will not be saved in json data
     **/
    public bool $notSaveNull = false;

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeUpdate',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            BaseActiveRecord::EVENT_AFTER_FIND => 'afterFind',
        ];
    }

    /**
     * @return bool
     */
    public function afterFind() : bool
    {
        $data = $this->owner->{$this->fieldAdditional};
        foreach ($this->AdditionalDataProperty as $key => $type) {
            if (str_starts_with($type, "Class::") && isset($data[$key]) && $data[$key]) {
                $className = substr($type, 7);
                $this->owner->$key = new $className($data[$key]);
            } elseif (str_starts_with($type, "ClassArray::") && isset($data[$key]) && count($data[$key]) > 0) {
                $className = substr($type, 12);
                foreach ($data[$key] as $datum) {
                    $model = new $className($datum);
                    if (property_exists($model, 'isNewRecord')) {
                        $model->isNewRecord = false;
                    }
                    $this->owner->$key[] = $model;
                }
            } else {
                $this->owner->$key = $data[$key] ?? null;
            }
            $this->jsonDirtyAttributes[$key] = $data[$key] ?? null;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function beforeUpdate() : bool
    {
        $additionalData = [];
        foreach ($this->AdditionalDataProperty as $key => $type) {
            if(str_starts_with($type, "Class::") || str_starts_with($type, "ClassArray::")) {
                $type = 'Any';
            }
            if($this->notSaveNull === false) {
                $additionalData[$key] = Yii::$app->helper->format($this->owner->$key, $type);
            } elseif($this->owner->$key !== null) {
                $additionalData[$key] = Yii::$app->helper->format($this->owner->$key, $type);
            }
        }
        $this->owner->{$this->fieldAdditional} = $additionalData;
        return true;
    }


    /**
     * Returns a value indicating whether the named attribute has been changed.
     * @param string $name the name of the attribute.
     * identical values using `===`, defaults to `true`. Otherwise `==` is used for comparison.
     * @return bool whether the attribute has been changed
     */
    public function isJsonAttributeChanged(string $name) : bool
    {
        if ($this->owner->hasAttribute($name) && isset( $this->jsonDirtyAttributes[$name])) {

            return $this->owner->getAttribute($name) != $this->jsonDirtyAttributes[$name];
        }

        return $this->owner->hasAttribute($name) || isset($this->jsonDirtyAttributes[$name]);
    }
}