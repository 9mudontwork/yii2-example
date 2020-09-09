<?php

namespace frontend\controllers;

use Yii;
use common\helpers\_;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\helpers\HandleFile;
use frontend\models\FileExample;

use yii\web\NotFoundHttpException;
use frontend\models\search\FileExampleSearch;

class FileController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionView($id, $width = null, $height = null)
    {
        _::print($id);
        
        // if (!empty($file)) {
        //     $file = _Files::viewFile($file->file_code);
        //     return $file;
        // } else {
        //     throw new \yii\web\HttpException(404, 'Page not found.');
        // }
    }

    public function actionDownloadFile($id)
    {
        $model = new FileExample();

        $file = $model->find()->where(['file_key' => $id])->one();

        if (!empty($file)) {
            $file = (new HandleFile)->downloadFile($file->file_code);
            return $file;
        } else {
            throw new \yii\web\HttpException(404, 'Page not found.');
        }
    }
}
