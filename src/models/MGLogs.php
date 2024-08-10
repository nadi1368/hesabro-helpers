<?php

namespace hesabro\helpers\models;

use hesabro\helpers\Module;
use Yii;
use yii\mongodb\ActiveRecord;

/**
 *
 * @property int $_id
 * @property int $client_id
 * @property string $model_class
 * @property int $model_id
 * @property int $checked
 * @property string[] $logs
 * @property string $ip
 * @property string $controller
 * @property string $action
 * @property string $params
 * @property int $created
 * @property int $update_id
 *
 * @property object $update
 */
class MGLogs extends ActiveRecord
{
    const UNCHECKED = 0;
    const CHECKED = 1;

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'logs';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'client_id', 'model_class', 'model_id', 'logs', 'ip', 'controller', 'action', 'created', 'update_id', 'params', 'checked'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model_class', 'model_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'update_id' => Yii::t('hesabro-helper', 'Update ID'),
            'created' => Yii::t('hesabro-helper', 'Update'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdate()
    {
        return $this->hasOne(Module::instance()->user, ['id' => 'update_id']);
    }

    public static function getChangedValue($attribute, $value, $ownerClassName)
    {
        if (is_array($value)) {
            return [
                'from' => $value[0],
                'to' => $value[1],
            ];
        }

        $values = explode('###=>', (string)$value);

        if (count($values) > 1) {
            return [
                'from' => $values[0],
                'to' => $values[1],
            ];
        }

        return ['from' => $value];
    }

    public static function itemAlias($type, $code = null)
    {
        $items = [
            'IgnoreClass' => Module::instance()->ignoreClasses
        ];

        return $code ? ($items[$type][$code] ?? false) : $items[$type] ?? false;
    }

    /**
     * @param string $model_class
     * @param int $model_id
     * @param array $logs
     * @return bool
     */
    public static function saveManual(string $model_class, int $model_id, array $logs): bool
    {
        $model = new self();
        $model->client_id = Yii::$app->client->id;
        $model->model_class = $model_class;
        $model->model_id = $model_id;
        $model->logs = $logs;
        return $model->save();
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->ip = Yii::$app->request->userIP ?? null;
            $this->created = time();
            $this->update_id = Yii::$app->user->id ?? null;
            $this->controller = Yii::$app->params['queueRunningController'] ?? Yii::$app->id . '/' . Yii::$app->controller->uniqueId;
            $this->action = Yii::$app->params['queueRunningAction'] ?? $this->action ?: Yii::$app->controller->action->id;
            $this->params = Yii::$app->request->queryParams ?? null;
        }
        return parent::beforeSave($insert);
    }
}
