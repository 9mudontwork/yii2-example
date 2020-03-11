<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\FileExample */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'File Examples', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="file-example-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <img src="http://localhost:8080/file-example/view-file?id=87ce4bad5af83950ce911965ef0a006f" alt="">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'file_code:ntext',
            'file_key',
        ],
    ]) ?>

</div>
