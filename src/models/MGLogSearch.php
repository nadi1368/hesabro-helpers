<?php

namespace hesabro\helpers\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class MGLogSearch extends MGLogs
{
	public $hasSlaveId = true;

    public $from_date;
    public $to_date;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'from_date', 'to_date'], 'integer'],
            [['model_class', 'model_id', 'controller', 'action', 'created', 'update_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function beforeValidate()
    {
        if ($this->model_id > 0) {
            $this->model_id = (int) $this->model_id;
        }
        
        if ($this->update_id > 0) {
            $this->update_id = (int) $this->update_id;
        }
        
        if ($this->from_date) {
            $this->from_date = Yii::$app->jdf::jalaliToTimestamp($this->from_date, 'Y/m/d H:i');
        }
        
        if ($this->to_date) {
            $this->to_date = Yii::$app->jdf::jalaliToTimestamp($this->to_date, 'Y/m/d H:i');
        }
        return parent::beforeValidate();
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
        $query = MGLogs::find()->byClient();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');

            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            //'client_id' => \Yii::$app->client->id,
            'model_class' => $this->model_class,
            'model_id' => $this->model_id,
            'controller' => $this->controller,
            'action' => $this->action,
            'created' => $this->created,
            'update_id' => $this->update_id,
        ]);

        if ($this->from_date) {
            $query->andWhere(['>=', 'created', $this->from_date]);
        }
        if ($this->to_date) {
            $query->andWhere(['<=', 'created', $this->to_date]);
        }

        return $dataProvider;
    }
}
