<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\TabOrdersLog */

$this->title = Yii::t('app', 'Create Tab Orders Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tab Orders Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-orders-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
