<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\FileExampleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'File Examples';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-example-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create File Example', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'file_code:ntext',
            'file_key',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
