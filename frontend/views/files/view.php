<?php

use common\helpers\_;
use yii\helpers\Html;
use common\helpers\_Files;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\files */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Files', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="files-view">

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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'file_key',
            [
                'attribute' => 'file_contents',
                'label' => 'file_contents',
                'value' => function ($model) {
                    $fileName = _Files::getFileName($model->file_contents);
                    $folderPath = _Files::getFolderPath($model->file_contents);

                    $fileUrl = _Files::getFileUrl($model->file_contents);

                    _::print_r($fileUrl);
                },
            ],
        ],
    ]) ?>

</div>