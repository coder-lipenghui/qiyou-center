<?php

namespace backend\controllers;

use backend\models\TabGames;
use backend\models\TabGameUpdate;
use common\helps\ItemDefHelper;
use Yii;
use backend\models\TabPermission;
use backend\models\TabPermissionSearch;
use backend\models\MyTabPermission;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\TabItemdefDzy;

/**
 * PermissionController implements the CRUD actions for TabPermission model.
 */
class PermissionController extends Controller
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
     * Lists all TabPermission models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TabPermissionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

        ]);
    }

    /**
     * Displays a single TabPermission model.
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
     * Creates a new TabPermission model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TabPermission();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TabPermission model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
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

    /**
     * Deletes an existing TabPermission model.
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
    public function actionGetGamesByVersion($versionId)
    {
        $request=Yii::$app->request;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model=new MyTabPermission();
        return $model->getGamesByVersion($request->get('versionId'));
    }
    /**
     * 获取拥有权限的游戏列表
     */
    public function actionGetGame()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return MyTabPermission::getGames();
    }
    public function actionGetDistributor()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model=new MyTabPermission();
        $request=Yii::$app->request;
        if ($request->get('gameId'))
        {
            return $model->allowAccessDistributor($request->get('gameId'));
        }
        return ['code'=>-1,'msg'=>'获取分销商失败'];
    }
    /**
     * get distribution by game id
     */
    public function actionGetDistribution()
    {
        $model=new MyTabPermission();
        $response = Yii::$app->response;
        $requestParams=Yii::$app->request->getQueryParams();
        if (count($requestParams)>0)
        {
            $gid=$requestParams['gameId'];
            $did=null;
            if ($requestParams['distributorId'])
            {
                $did=$requestParams['distributorId'];
            }
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data=$model->allowAccessDistribution($gid,$did,Yii::$app->user->id);
            $response->send();
        }

    }
    public function actionGetAllDistribution()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model=new MyTabPermission();
        $request=Yii::$app->request;
        if ($request->get("gameId"))
        {
            return $model->allDistribution($request->get("gameId"),null);
        }
        return [];
    }
    public function actionGetServer()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model=new MyTabPermission();
        $request=Yii::$app->request;
        if($request->get('gameId') && $request->get('distributorId'))
        {
            $withOutMerged=$request->get('withOutMerged')==null?false:true;
            return $model->allowAccessServer($request->get('gameId'),$request->get('distributorId'),$withOutMerged);
        }
        return ['code'=>-1,'msg'=>'获取区服失败'];
    }
    public function actionGetItems()
    {
        $request=Yii::$app->request;
        $versionId=$request->get('versionId');
        $gameId=$request->get('gameId');
        if (!empty($versionId))
        {
            $items=ArrayHelper::map(ItemDefHelper::getAllItem($versionId),'id','name');
            return json_encode($items);
        }
        if (!empty($gameId) && empty($versionId))
        {
            $game=TabGames::find()->select(['versionId'])->where(['id'=>$gameId])->one();
            if (!empty($game))
            {
                $items=ArrayHelper::map(ItemDefHelper::getAllItem($game->versionId),'id','name');
                return json_encode($items);
            }
        }
        return json_encode([]);
    }
    public function actionGetGameUpdateVersion()
    {
        $request=Yii::$app->request;
        $versionId=$request->get('versionId');
        if (!empty($versionId))
        {
            $upateInfo=TabGameUpdate::find()->where(['versionId'=>$versionId])->orderBy('version DESC')->one();
            return $upateInfo['version']."";
        }

        return "0";
    }
    /**
     * Finds the TabPermission model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TabPermission the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TabPermission::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
