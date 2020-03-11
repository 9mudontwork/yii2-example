<?php

namespace frontend\controllers;

use Yii;
use frontend\models\FileExample;
use frontend\models\search\FileExampleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\helpers\HandleFile;

class FileExampleController extends Controller
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

    public function actionIndex()
    {
        $searchModel = new FileExampleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionFile($id, $width = null, $height = null)
    {
        \console\P::R($id);
        $model = new FileExample();

        $file = $model->find()->where(['file_key' => $id])->one();

        if (!empty($file)) {
            $file = (new HandleFile)->viewFile($file->file_code);
            return $file;
        } else {
            throw new \yii\web\HttpException(404, 'Page not found.');
        }
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

    public function actionCreate()
    {
        $model = new FileExample();

        if ($model->load(Yii::$app->request->post())) {

            $handleFile = new HandleFile();
            $handleFile->useIn('common', '/storage/upload-xxxx/');
            $fileCode = $handleFile->doUpload('file_code', $model);

            $model->file_code = $fileCode;
            $model->file_key = $handleFile->getFileKey($fileCode);

            \console\P::R($fileCode);

            if ($model->save()) {
                // 
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    protected function findModel($id)
    {
        if (($model = FileExample::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
