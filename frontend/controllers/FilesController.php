<?php

namespace frontend\controllers;

use Yii;
use common\helpers\_;
use yii\web\Controller;
use common\helpers\_Files;
use frontend\models\files;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use frontend\models\search\files as filesSearch;

/**
 * FilesController implements the CRUD actions for files model.
 */
class FilesController extends Controller
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
        $searchModel = new filesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViews($id)
    {
        $fileName = $id;
        return _Files::render(_Files::exampleFolder, $fileName);
    }

    public function actionView($id)
    {

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new files();

        if (_::isPost()) {

            // บันทึก 1 รูป ต่อ 1 row

            $fileUploadContents = _Files::upload([
                'attribute' => 'file_contents',
                'model' => $model,
                'folderPath' => _Files::exampleFolder
            ]);

            // $fileUploadContents->rowSave($model);


            // บันทึก หลายรูป ใน 1 field
            $model->file_key = _Files::generateKey();
            $model->file_contents = $fileUploadContents->multipleSave();

            if ($model->save()) {
                _::print($fileUploadContents->fileContents);
                // return $this->redirect(['view', 'id' => $model->id]);
            }
        }




        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {


        // $fileContents = $_files->uploads('file_contents')->in('/storage/upload-xxxx/');

        // $model = $this->findModel($id);


        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id]);
        // }

        // return $this->render('update', [
        //     'model' => $model,
        // ]);
    }




    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    protected function findModel($id)
    {
        if (($model = files::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
