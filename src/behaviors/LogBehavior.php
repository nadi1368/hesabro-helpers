<?php

namespace hesabro\helpers\behaviors;

use common\models\mongo\MGLogs;
use Yii;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\Html;

/**
 * Class LogBehavior
 * @package common\behaviors
 * @author Nader <nader.bahadorii@gmail.com>
 * @property ActiveRecord $owner
 */
class LogBehavior extends \yii\base\Behavior
{
    public $ownerClassName;
    public $excludeAttribute = [];
    public $includeAttribute = [];
    public $necessaryAttribute = [];
    public $notOnNullValueAttributes = [];
    public $saveAfterInsert = false;
    public $savePostDataAfterInsert = false; // ذخیره اطلاعات ارسالی فرم بعد از ایجاد
    public $savePostDataAfterUpdate = false; // ذخیره اطلاعات ارسالی فرم بعد از بروز رسانی
    public MGLogs $modelLog;

	public $hasSlaveId = true;

    public string $ownerPrimaryKey = 'id';

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }


    public function afterInsert()
    {
        $logs = [];
        $change = false;

        if ($this->saveAfterInsert) {
            foreach ($this->owner->getAttributes() as $name => $value) {
                if (!in_array($name, $this->excludeAttribute)) {
                    $logs[$name] = (is_array($value) ? json_encode($value) : $value);
                    $change = true;
                }
            }
            if (isset($this->owner->AdditionalDataProperty) && is_array($this->owner->AdditionalDataProperty)) {
                // مقدار اولیه فیلدهای جیسون JsonAdditional::class
                foreach ($this->owner->AdditionalDataProperty as $key => $type) {
                    if(!empty($this->owner->$key))
                    {
                        $logs[$key] = (is_array($this->owner->$key) || is_object($this->owner->$key) ? json_encode($this->owner->$key) : $this->owner->$key);
                        $change = true;
                    }
                }
            }

            foreach ($this->necessaryAttribute as $name) {
                $logs[$name] = $this->owner->$name;
                $change = true;
            }

            if(Yii::$app->id != 'app-console' && $this->savePostDataAfterInsert && Yii::$app->request->isPost && is_array(Yii::$app->request->post()))
            {
                $logs['postData'] = json_encode(Yii::$app->request->post());
                $change = true;
            }
            if ($change) {
                try {
                    $model = new MGLogs();
                    $model->client_id = $this->hasSlaveId ? Yii::$app->client->id : null;
                    $model->model_class = $this->ownerClassName;
                    $model->model_id = (int)$this->owner->{$this->ownerPrimaryKey};
                    $model->logs = $logs;
                    $model->checked = MGLogs::CHECKED;
                    return $model->save();
                } catch (\Exception $e) {
                    Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
                    return false;
                }
            }
        }
        return true;
    }

    public function beforeUpdate()
    {
        $logs = [];
        $change = false;

        if ($this->necessaryAttribute instanceof \Closure) {
            $this->necessaryAttribute = call_user_func($this->necessaryAttribute, $this->owner);
        }

        if ($this->excludeAttribute instanceof \Closure) {
            $this->excludeAttribute = call_user_func($this->excludeAttribute, $this->owner);
        }

        foreach ($this->owner->getDirtyAttributes() as $name => $value) {
            if ($value != $this->owner->getOldAttribute($name) && ($value || !in_array($name, $this->notOnNullValueAttributes))) {
                if (in_array($name, $this->includeAttribute)) {
                    $oldAttribute = $this->owner->getOldAttribute($name);
                    $logs[$name] = $this->setValue((is_array($oldAttribute) ? json_encode($oldAttribute) : $oldAttribute), (is_array($value) ? json_encode($value) : $value));
                    $change = true;
                } elseif (!in_array($name, $this->excludeAttribute)) {
                    $oldAttribute = $this->owner->getOldAttribute($name);
                    $logs[$name] = $this->setValue((is_array($oldAttribute) ? json_encode($oldAttribute) : $oldAttribute), (is_array($value) ? json_encode($value) : $value));
                    $change = true;
                }
            }
        }
        if (isset($this->owner->jsonDirtyAttributes) && is_array($this->owner->jsonDirtyAttributes)) {
            // تغیرات فیلدهای جیسون JsonAdditional::class
            foreach ($this->owner->jsonDirtyAttributes as $key => $oldAttribute) {
                if (property_exists($this->owner, $key) && $oldAttribute != $this->owner->{$key}) {
                    $logs[$key] = $this->setValue((is_array($oldAttribute) || is_object($oldAttribute) ? json_encode($oldAttribute) : $oldAttribute),(is_array($this->owner->{$key}) || is_object($this->owner->{$key}) ? json_encode($this->owner->{$key}) : $this->owner->{$key}));
                    $change = true;
                }
            }
        }

        foreach ($this->necessaryAttribute as $name) {
            $logs[$name] = $this->setValue($this->owner->getOldAttribute($name), $this->owner->$name);
            $change = true;
        }

        if($this->savePostDataAfterUpdate && Yii::$app->request->isPost && is_array(Yii::$app->request->post()))
        {
            $logs['postData'] = json_encode(Yii::$app->request->post());
            $change = true;
        }
        if ($change) {
            try {
                $model = new MGLogs();
                $model->client_id = $this->hasSlaveId ? Yii::$app->client->id : null;;
                $model->model_class = $this->ownerClassName;
                $model->model_id = (int)$this->owner->{$this->ownerPrimaryKey};;
                $model->logs = $logs;
                $model->checked = MGLogs::UNCHECKED;
                if ($model->save()) {
                    $this->modelLog = $model;
                    return true;
                }
                return false;
            } catch (\Exception $e) {
                Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
                return false;
            }
        }
        return true;
    }

    public function afterUpdate(): void
    {
        if (isset($this->modelLog)) {
            $this->modelLog->checked = MGLogs::CHECKED;
            if (!$this->modelLog->save()) {
                Yii::error('error in checked MGLog after update: ' . Html::errorSummary($this->modelLog), __METHOD__ . ':' . __LINE__);
            }
        }
    }

    protected function setValue($from, $to)
    {
        return [
            $from,
            $to,
        ];
    }

    public function changeLogValue($name, $value)
    {
        $model_class = $this->ownerClassName;
        if ($name == "send_status") {
            return $model_class::itemAlias('SendStatus', $value);
        }
        if ($name == "status") {
            return $model_class::itemAlias('Status', $value);
        }

        return $value;
    }
}