<?php


use wbraganca\dynamicform\DynamicFormWidget;
use yii\widgets\MaskedInput;
use hesabro\helpers\models\WageForm;

/* @var $this yii\web\View */
/* @var $model WageForm*/
/* @var $details WageForm[]*/
/* @var $form Yii\bootstrap4\ActiveForm */
?>
<hr />
<div class="row">
    <div class="col-md-3">
        <?= $form->field($model, 'wage_type')->radioList(WageForm::itemAlias('TypeWage')) ?>
    </div>
    <div class="col-md-9">
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-items', // required: css class selector
            'widgetItem' => '.item', // required: css class
            'limit' => 10, // the maximum times, an element can be cloned (default 999)
            'min' => 1, // 0 or 1 (default 1)
            'insertButton' => '.add-item', // css class
            'deleteButton' => '.remove-item', // css class
            'model' => $details[0],
            'formId' => $form->getId(),
            'formFields' => [
                'amount',
            ],
        ]); ?>
        <div class="container-items row">
            <?php foreach ($details as $index => $detail): ?>
                <div class="col-md-3 item">
                    <div class="row">
                        <div class="col-md-1">
                            <span class="fa fa-plus add-item pointer text-success" title="<?= Yii::t('app','New') ?>"></span>
                            <span class="fa fa-minus remove-item pointer text-danger" title="<?= Yii::t('app','Delete') ?>"></span>
                        </div>
                        <div class="col-md-10">
                            <?= $form->field($detail, "[{$index}]wage_amount")
                                ->widget(MaskedInput::className(),
                                    [
                                        'options' => [
                                            'autocomplete' => 'off',
                                        ],
                                        'clientOptions' => [
                                            'alias' => 'integer',
                                            'groupSeparator' => ',',
                                            'autoGroup' => true,
                                            'removeMaskOnSubmit' => true,
                                            'autoUnmask' => true,
                                        ],
                                    ]) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php DynamicFormWidget::end(); ?>
    </div>
</div>

<?php

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
     var index=-1;
      $(".container-items .item").each(function(){
        index++;
      });
      jQuery("#wageform-"+index+"-wage_amount").inputmask(inputmask_603943cc); 
});

';

$this->registerJs($js);
?>
