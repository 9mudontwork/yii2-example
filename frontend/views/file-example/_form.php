<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\FileExample */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="file-example-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'file_code')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'file_key')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
