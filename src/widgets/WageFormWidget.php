<?php
namespace hesabro\helpers\widgets;

use Yii;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;



class WageFormWidget extends Widget
{
    /* @var $model WageFormWidget */
    public $model;

    /* @var $details WageFormWidget[] */
    public $details;
    /* @var $form yii\bootstrap4\ActiveForm */
    public $form;

    public function init()
    {
        parent::init();

        if (empty($this->model)) {
            throw new InvalidConfigException('Required `model` param isn\'t set.');
        }
        if (empty($this->form)) {
            throw new InvalidConfigException('Required `form` param isn\'t set.');
        }
    }

    public function run()
    {
        echo $this->render('wage_form', [
            'model' => $this->model->headerModel,
            'form'  =>$this->form,
            'details' => $this->model->detailsModel,
        ]);
    }

}