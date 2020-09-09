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
    /**
     * {@inheritdoc}
     */
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

    /**
     * Lists all files models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new filesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single files model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new files model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new files();

        if (_::isPost()) {

            // บันทึก 1 รูป ต่อ 1 row

            $fileUploadContents = _Files::upload([
                'attribute' => 'file_contents',
                'model' => $model,
                'folder' => '/common/files-storage/'
            ]);

            _Files::saveOneToOne($model, $fileUploadContents->getArray());


            // บันทึก หลายรูป ใน 1 field
            $model->file_contents = $fileUploadContents->getJsonString();

            if ($model->save()) {
                // return $this->redirect(['view', 'id' => $model->id]);
            }
        }




        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing files model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
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



    /**
     * Deletes an existing files model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the files model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return files the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = files::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
