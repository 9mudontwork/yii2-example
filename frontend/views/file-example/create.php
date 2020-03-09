<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\FileExample */

$this->title = 'Create File Example';
$this->params['breadcrumbs'][] = ['label' => 'File Examples', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-example-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
