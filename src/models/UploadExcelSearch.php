<?php

namespace hesabro\helpers\models;

use hesabro\helpers\validators\DateValidator;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UploadExcelSearch represents the model behind the search form of `common\models\UploadExcel`.
 */
class UploadExcelSearch extends UploadExcel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'creator_id', 'update_id', 'created', 'changed', 'status'], 'integer'],
            [['type', 'file_name'], 'safe'],
            [['date'], DateValidator::class],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UploadExcel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>['defaultOrder'=>['id'=>SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['date'] = [
            'asc' => ['JSON_EXTRACT(' . UploadExcel::tableName() . '.`additional_data`, "$.date")' => SORT_ASC],
            'desc' => ['JSON_EXTRACT(' . UploadExcel::tableName() . '.`additional_data`, "$.date")' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'created' => $this->created,
            'changed' => $this->changed,
            'status' => $this->status,
            'JSON_EXTRACT(' . UploadExcel::tableName() . '.`additional_data`, "$.date")' => $this->date,
        ]);

        $query->andFilterWhere(['in', 'type', $this->type])
            ->andFilterWhere(['like', 'file_name', $this->file_name]);

        return $dataProvider;
    }
}
