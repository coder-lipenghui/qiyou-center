<?php
use yii\helpers\Html;
use yii\widgets\menu;

?>
<?php

/* @var $this yii\web\View */
$this->title = '';
$this->registerJsFile("@web/js/echarts.min.js",['position'=>\yii\web\View::POS_HEAD]);
?>
<div class="panel panel-default">
    <div class="panel-body">
        <b>快捷导航</b>
        <p>参数配置：渠道参数配置 </p>
        <p>玩家操作：日志信息 玩家明细</p>
        <p>数据分析：数据概况、</p>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <h3>商务&市场合作</h3>
        <p><hr/></p>
        <div class="row">
            <div class="col-md-3">
                <p><span class="glyphicon glyphicon-briefcase"><label> 商务合作 </label></span></p>
                <p><span class="glyphicon glyphicon-user">:</span></p>
                <p><span class="glyphicon glyphicon-earphone">:</span></p>
                <p><span class="glyphicon glyphicon-exclamation-sign">:<span class="badge"><small></small></span></span></p>
            </div>
            <div class="col-md-3">
                <p><b>市场合作</b></p>
                <p><span class="glyphicon glyphicon-user">:</span></p>
                <p><span class="glyphicon glyphicon-earphone">:</span></p>
                <p><span class="glyphicon glyphicon-exclamation-sign">:<span class="badge"><small></small></span></span></p>
            </div>
            <div class="col-md-3">
                <p><b>技术支持</b></p>
                <p><span class="glyphicon glyphicon-user">:</span></p>
                <p><span class="glyphicon glyphicon-earphone">:</span></p>
                <p><span class="glyphicon glyphicon-exclamation-sign">:<span class="badge"><small></small></span></span></p>
            </div>
            <div class="col-md-3">
                <p><b>联系我们</b></p>
                <p><span class="glyphicon glyphicon-user">:</span></p>
                <p><span class="glyphicon glyphicon-earphone">:</span></p>
                <p><span class="glyphicon glyphicon-exclamation-sign">:<span class="badge"><small></small></span></span></p>
            </div>
        </div>
    </div>
</div>
