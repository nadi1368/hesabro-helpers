<?php

namespace hesabro\helpers\behaviors;

use hesabro\helpers\models\WageForm;
use common\models\BaseModel;
use Yii;
use yii\db\ActiveRecord;
use yii\base\Model;

/**
 * Class WageBehavior
 * @package backend\modules\accounting\behaviors
 * @author Nader <nader.bahadorii@gmail.com>
 */
class WageBehavior extends \yii\base\Behavior
{
    public $wage_type = 0;
    public $wage_amount = [];
    public $wage_type_behavior = 0;
    public $wage_amount_behavior;

    public function events()
    {
        return [
            Model::EVENT_AFTER_VALIDATE => 'afterValidate',
        ];
    }


    public function afterValidate()
    {
        $headerModel = $this->getHeaderModel();
        $headerModel->load(Yii::$app->request->post(), '');
        $this->wage_type = $headerModel->wage_type;
        $this->wage_type_behavior = $headerModel->wage_type;
        $details = BaseModel::createMultiple(WageForm::class, WageForm::SCENARIO_DETAILS);
        Model::loadMultiple($details, Yii::$app->request->post(), null);
        $this->wage_amount = [];
        $this->wage_amount_behavior = [];
        foreach ($details as $detail) {
            $this->wage_amount[] = $detail->wage_amount;
            $this->wage_amount_behavior[] = $detail->wage_amount;
        }

    }

    public function getHeaderModel()
    {
        return new WageForm([
            'scenario' => WageForm::SCENARIO_HEADER
        ]);
    }

    public function getDetailsModel()
    {
        $detail = new WageForm(['scenario' => WageForm::SCENARIO_DETAILS]);
        return [$detail];
    }
}