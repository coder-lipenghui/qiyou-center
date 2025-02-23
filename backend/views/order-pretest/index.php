<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\TabOrdersPretestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '删测返利');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-orders-pretest-index">
    <div class="panel panel-default">
        <div class="panel panel-body">
            <?php Pjax::begin(); ?>
            <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,
                'columns' => [
//                ['class' => 'yii\grid\SerialColumn'],

//                  'id',
//                  'distributionId',
                    ['attribute'=>'distributionUserId','label'=>'渠道账号'],
                    ['attribute'=>'total','label'=>'充值金钻'],
                    ['attribute'=>'rate','label'=>'返利比例(%)'],
                    //'type',
                    ['attribute'=>'got','label'=>'是否领取','value'=>function($model){return $model['got']==1?"已领取":"-";}],
                    //'rcvRoleId',
                    ['attribute'=>'rcvRoleName','label'=>'领取角色','value'=>function($model){return urldecode($model['rcvRoleName']);}],
                    ['attribute'=>'rcvServerId','label'=>'领取区服','value'=>'server.name'],
                    ['attribute'=>'rcvTime','label'=>'领取时间','value'=>function($model){return empty($model['rcvTime'])?"-":date('Y-m-d H:i:s',$model['rcvTime']);}],

                    ['class' => 'yii\grid\ActionColumn'],
                ],
                ]); ?>
            
                <?php Pjax::end(); ?>
        </div>
    </div>
</div>
