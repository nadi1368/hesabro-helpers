<?php

namespace hesabro\helpers\models;

/**
 * This is the ActiveQuery class for [[UploadExcel]].
 *
 * @see UploadExcel
 */
class UploadExcelQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UploadExcel[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UploadExcel|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param string $date
     * @return UploadExcelQuery
     */
    public function byDate(string $date)
    {
        return $this->andWhere(['JSON_EXTRACT(' . UploadExcel::tableName() . '.`additional_data`, "$.date")' => $date]);
    }

    /**
     * @param int $status
     * @return UploadExcelQuery
     */
    public function byStatus(int $status)
    {
        return $this->andWhere(['status' => $status]);
    }
    /**
     * @param int $status
     * @return UploadExcelQuery
     */
    public function byType(string $type)
    {
        return $this->andWhere(['type' => $type]);
    }

    /**
     * @return UploadExcelQuery
     */
    public function active() : self
    {
        return $this->andWhere(['<>', 'status', UploadExcel::STATUS_DELETE]);
    }
}
