<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\files */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="files-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    // $form->field($model, 'file_key')->textInput(['maxlength' => true]) 
    ?>

    <?php
    // $form->field($model, 'file_contents')->textarea(['rows' => 6]) 
    ?>

    <?= $form->field($model, 'file_contents', [])->fileInput([
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