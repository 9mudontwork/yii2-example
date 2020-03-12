<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\FileExample */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="file-example-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'file_code', [])->fileInput([
        'id' => 'filer_input',
        'multiple' => true,
        // 'accept' => 'image/*'
    ])->label('upload with model') ?>

    <label class="control-label" for="filer_input">upload without model</label>
    <input type="file" name="files[]" id="filer_input_nomodel" multiple="multiple">

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>