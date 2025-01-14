<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\TabOrdersRebateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tab Orders Rebates');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-orders-rebate-index">
    <div class="panel panel-default">
        <div class="panel-body">
            <?= Html::a(Yii::t('app', 'Create Tab Orders Rebate'), ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel panel-body">
                <?php Pjax::begin(); ?>
                                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            
                            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                            'id',
            'gameId',
            'distributorId',
            'distributionId',
            'orderId',
            //'distributionOrderId',
            //'distributionUserId',
            //'gameRoleId',
            'gameRoleName',
            //'gameServerId',
            //'gameServername',
            //'gameAccount',
            //'productId',
            //'productName',
            'payAmount',
            //'payStatus',
            //'payMode',
            //'payTime:datetime',
            //'createTime:datetime',
            'delivered',

                ['class' => 'yii\grid\ActionColumn'],
                ],
                ]); ?>
            
                <?php Pjax::end(); ?>
        </div>
    </div>
</div>
