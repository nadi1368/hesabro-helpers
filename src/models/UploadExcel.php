<?php

namespace hesabro\helpers\models;

use hesabro\hris\models\EmployeeRollCall;
use hesabro\hris\models\SalaryItemsAddition;
use backend\modules\excel\models\Params;
use backend\modules\excel\models\ParamsChecks;
use hesabro\helpers\behaviors\JsonAdditional;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use hesabro\helpers\validators\DateValidator;
use phpDocumentor\Reflection\Types\Boolean;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use mamadali\S3Storage\behaviors\StorageUploadBehavior;
use mamadali\S3Storage\components\S3Storage;
use backend\models\User;

/**
 * This is the model class for table "{{%upload_excel}}".
 *
 * @property int $id
 * @property string $creator_id
 * @property string $update_id
 * @property string $type
 * @property string $file_name
 * @property string $status
 * @property string $created
 * @property string $changed
 *
 * @mixin StorageUploadBehavior
 */
class UploadExcel extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INSERTED = 2;
    const STATUS_DELETE = 0;

    const SCENARIO_UPLOAD = 'upload';
    const SCENARIO_UPLOAD_ORG = 'upload_org';
    const SCENARIO_UPLOAD_RollCall_MONTHLY = 'upload_roll_call_monthly'; // حضور و غیاب ماهانه
    const SCENARIO_UPLOAD_RollCall_DAILY = 'upload_roll_call_daily'; // حضور و غیاب روزانه
    const SCENARIO_UPLOAD_SALARY_NON_CASH = 'upload_salary_non_cash'; // مزایای غیر نقدی

    const TYPE_ROLL_CALL_DAILY = 'roll_call_daily';
    const TYPE_ROLL_CALL_MONTHLY = 'roll_call_monthly';
    const TYPE_SALARY_NON_CASH = 'salary_non_cash';

    public $excelFile;
    public $month;

    /** additional data */
    public $modelId = 0;
    public $description;
    public $date;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%upload_excel}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPLOAD, self::SCENARIO_UPLOAD_ORG]],
            [['creator_id', 'update_id', 'created', 'changed', 'status'], 'integer'],
            [['date'], 'required', 'on' => [self::SCENARIO_UPLOAD_RollCall_DAILY]],
            [['month', 'file_name'], 'required', 'on' => [self::SCENARIO_UPLOAD_SALARY_NON_CASH]],
            [['date'], DateValidator::class, 'on' => [self::SCENARIO_UPLOAD_RollCall_DAILY, self::SCENARIO_UPLOAD_SALARY_NON_CASH]],
            [['date'], 'validateUniqueDate', 'on' => [self::SCENARIO_UPLOAD_RollCall_DAILY]],
            [['date'], 'compare', 'compareValue' => Yii::$app->jdate->date("Y/m/d"), 'operator' => '<', 'type' => 'string', 'on' => [self::SCENARIO_UPLOAD_RollCall_DAILY]],
            [['file_name'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx', 'maxSize' => 1024 * 1024 * 50, 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_UPLOAD, self::SCENARIO_UPLOAD_ORG]],
            [['file_name'], 'file', 'skipOnEmpty' => false,
                'mimeTypes' => [
                    'text/comma-separated-values',
                    'text/csv',
                    'text/plain',
                    'application/csv',
                    'application/excel',
                    'application/vnd.ms-excel',
                    'application/vnd.msexcel',
                ],
                'maxSize' => 1024 * 1024 * 50, 'on' => [self::SCENARIO_UPLOAD_RollCall_MONTHLY, self::SCENARIO_UPLOAD_RollCall_DAILY, self::SCENARIO_UPLOAD_SALARY_NON_CASH]],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_UPLOAD] = ['type', 'file_name'];
        $scenarios[self::SCENARIO_UPLOAD_ORG] = ['file_name'];
        $scenarios[self::SCENARIO_UPLOAD_RollCall_MONTHLY] = ['file_name'];
        $scenarios[self::SCENARIO_UPLOAD_RollCall_DAILY] = ['file_name', 'date'];
        $scenarios[self::SCENARIO_UPLOAD_SALARY_NON_CASH] = ['file_name', 'date', 'month'];

        return $scenarios;
    }

    /**
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validateUniqueDate($attribute, $params) : void
    {
        if (!$this->hasErrors() && self::find()->byType($this->type)->byDate($this->date)->byStatus(self::STATUS_INSERTED)->limit(1)->one() !== null) {
            $this->addError($attribute, 'فایل در تاریخ مورد نظر ثبت شده است.اگر اشتباه ثبت شده.لطفا ابتدا فایل رو پاک نمایید.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'update_id' => Yii::t('app', 'Update ID'),
            'type' => Yii::t('app', 'Model Type'),
            'excelFile' => Yii::t("app", "Excle File"),
            'file_name' => Yii::t('app', 'File Name'),
            'status' => Yii::t('app', 'Status'),
            'created' => Yii::t('app', 'Created'),
            'changed' => Yii::t('app', 'Changed'),
            'date' => Yii::t('app', 'Date'),
        ];
    }

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    public function getUpdate()
    {
        return $this->hasOne(User::class, ['id' => 'update_id']);
    }

    /**
     * {@inheritdoc}
     * @return UploadExcelQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new UploadExcelQuery(get_called_class());
        return $query->active();
    }


    public static function itemAlias($type, $code = NULL)
    {

        $_items = [
            'Type' => self::getTypeData(),
            'TypeChecks' => self::getTypeDataChecks(),
            'Status' => [
                self::STATUS_DELETE => Yii::t("app", "Delete"),
                self::STATUS_ACTIVE => Yii::t("app", "Wait Confirm"),
                self::STATUS_INSERTED => Yii::t("app", "Inserted"),
            ],
            'Months' => [
                '01' => 'فروردین',
                '02' => 'اردیبهشت',
                '03' => 'خرداد',
                '04' => 'تیر',
                '05' => 'مرداد',
                '06' => 'شهریور',
                '07' => 'مهر',
                '08' => 'آبان',
                '09' => 'آذر',
                '10' => 'دی',
                '11' => 'بهمن',
                '12' => 'اسفند'
            ],
        ];

        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    protected static function getTypeData()
    {
        $data = Params::$data;
        $type = [];
        foreach ($data as $key => $value) {
            $type[$key] = $value['title'];
        }

        return $type;
    }

    protected static function getTypeDataChecks()
    {
        $data = ParamsChecks::$data;
        $type = [];
        foreach ($data as $key => $value) {
            $type[$key] = $value['title'];
        }

        return $type;
    }

    public function deleteOperation()
    {
        if ($this->type == 'OrgUser' || $this->type == 'Customer') {
            // بالانس سازمانی
            OrderPayBalance::updateAll(['status' => OrderPayBalance::STATUS_REFUSE], ['excel_id' => $this->id]);
        }

        $this->status = self::STATUS_DELETE;
        return $this->save(false);
    }

    /**
     * @return bool
     */
    public function setInserted(): bool
    {
        $this->status = self::STATUS_INSERTED;
        return $this->save(false);
    }

    /**
     * @return bool
     */
    public function canDelete()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function softDelete()
    {
        $this->status = self::STATUS_DELETE;
        return $this->save(false);
    }

    /**
     * @return bool
     */
    public function afterSoftDelete()
    {
        $flag = true;
        if ($this->type == self::TYPE_ROLL_CALL_DAILY || $this->type == self::TYPE_ROLL_CALL_MONTHLY ) {
            if (EmployeeRollCall::find()->andWhere(['period_id' => $this->id])->exists()) {
                $flag = (boolean)EmployeeRollCall::deleteAll(['period_id' => $this->id]);
            }

            if (SalaryItemsAddition::find()->andWhere(['period_id' => $this->id])->exists()) {
                $flag = $flag && (boolean)SalaryItemsAddition::deleteAll(['period_id' => $this->id]);
            }
        }
        return $flag;
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_ACTIVE;
        }
        return parent::beforeSave($insert);
    }


    public function getDefaultExcel()
    {
        return [
            'phone' => 4,
            'code' => 3,
            'remind' => '',
            'default_remind' => 0,
            'org_id' => '',
            'default_org_id' => $this->modelId,
            'first_name' => 1,
            'last_name' => 2,
            'sex' => '',
            'default_sex' => 1,
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'changed'
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'creator_id',
                'updatedByAttribute' => 'update_id',
            ],
            [
                'class' => StorageUploadBehavior::class,
                'attributes' => ['file_name'],
                'accessFile' => S3Storage::ACCESS_PRIVATE,
                'scenarios' => [self::SCENARIO_UPLOAD, self::SCENARIO_UPLOAD_ORG, self::SCENARIO_UPLOAD_RollCall_MONTHLY, self::SCENARIO_UPLOAD_RollCall_DAILY, self::SCENARIO_UPLOAD_SALARY_NON_CASH],
                'path' => 'Excel/{id}',
            ],
            [
                'class' => TraceBehavior::class,
                'ownerClassName' => self::class
            ],
            [
                'class' => LogBehavior::class,
                'ownerClassName' => 'common\models\UploadExcel',
                'saveAfterInsert' => true
            ],
            [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'modelId' => 'Integer',
                    'description' => 'String',
                    'date' => 'String',
                ],

            ],
        ];
    }
}
