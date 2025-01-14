<?php
/**
 * Created by PhpStorm.
 * User: lipenghui
 * Date: 2019-04-19
 * Time: 22:00
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = Yii::t('app', '邮件');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/api/itemSearch.js',['depends'=>'yii\web\YiiAsset']);
$this->registerJsFile('@web/js/common.js');
$this->registerJsFile('@web/js/api/dropdown_menu.js',['depends'=>'yii\web\YiiAsset']);
$this->registerJsFile('@web/js/api/mail.js',['depends'=>'yii\web\YiiAsset']);
?>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">提示</h4>
            </div>
            <div class="modal-body" id="cmdResult">

            </div>
        </div>
    </div>
</div>
<div class="panel panel-default">
    <?php
    $form=ActiveForm::begin([
        'id'=>'mailForm',
        'action'=>['mail'],
        'method'=>'get',
        'fieldConfig' => ['template' => '{input}'],
//        'options' => ['class' => 'form-inline']
    ]);
    ?>
    <div class="panel-body">
        <div class="row">
            <table class="table table-info">
                <tr>
                    <td>
                        <?=$form->field($searchModel,'gameId')->dropDownList(
                            $games,
                            [
                                "class"=>"selectpicker form-control col-xs-2",
                                "data-width"=>"fit",
                                "id"=>"games",
                                "onchange"=>"onChangeGame(this)",
                                "title"=>"选择游戏"
                            ]
                        );?>
                    </td>
                    <td>
                        <?=$form->field($searchModel,'distributorId')->dropDownList(
                            [],
                            [
                                "class"=>"selectpicker form-control col-xs-2",
                                "data-width"=>"fit",
                                "id"=>"platform",
                                "onchange"=>"changePt(this)",
                                "title"=>"选择分销商"
                            ]
                        );?>
                    </td>
                    <td>
                        <?=$form->field($searchModel,'type')->dropDownList(
                            [1=>'全区',2=>'单人'],
                            [
                                "class"=>"selectpicker form-control col-xs-2",
                                "data-width"=>"fit",
                                "id"=>"type",
                                "onchange"=>"changeType(this)",
                                "title"=>"发放类型"
                            ]
                        );?>
                    </td>
                    <td>
                        <?=$form->field($searchModel,'serverId')->dropDownList(
                            [],
                            [
                                "class"=>"selectpicker form-control col-xs-2",
                                "data-width"=>"fit",
                                "id"=>"servers",
                                "onchange"=>"changeServer(this)",
                                "title"=>"选择区服"
                            ]
                        );?>
                    </td>
                    <td class="col-md-10"></td>
                </tr>
            </table>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?=$form->field($searchModel,'playerName')->textInput(['placeholder'=>'玩家ID','id'=>'playerName'])?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?=$form->field($searchModel,'title')->textInput(['placeholder'=>'标题'])?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5">
                <?=$form->field($searchModel,'content')->textarea(['rows'=>5,'placeholder'=>'邮件正文,目前只能85个字(255字节)']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?=$form->field($searchModel,'items')->textInput(['placeholder'=>'附件，建议使用右侧"+"进行物品添加'])?>
            </div>
            <div class="col-md-1">
                <a class="btn btn-default" data-toggle="modal" data-target="#addItemDialog" href="#" >
                    <span class="glyphicon glyphicon-plus"></span>
                </a>
            </div>
        </div>
    </div>
    <?php
    ActiveForm::end();
    ?>
    <hr/>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-1">
                <button class="btn btn-info" onclick="doMailAjaxSubmit()"><span class="glyphicon glyphicon-send"></span> 发送</button></button>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addItemDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">添加物品</h4>
                </div>

                <div class="modal-body" id="kick_playerName">

                    <label>物品名称:</label>
                    <?php
                        echo Html::dropDownList(
                                "selectItems",
                                null,
                                [],
                                [
                                    'id'=>'selectItems',
                                    'class'=>'selectpicker',
                                    'data-live-search'=>'true'
                                ]
                            );
                    ?>
                    <label>数量:</label>
                    <button id="btnSub" onclick="doSub()"><span class="glyphicon glyphicon-minus-sign"></span></button>
                    <input id="itemNum" size="5" value="1"/>
                    <button id="btnAdd" onclick="doAdd()"><span class="glyphicon glyphicon-plus-sign"></span></button>
                    <label>绑定:</label>
                    <input type="checkbox" id="ckBind" checked="checked"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="btnOk()">确认</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
</div>