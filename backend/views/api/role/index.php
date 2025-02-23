<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\TabAreasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '角色信息查询');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/api/roleSearch.js',['depends'=>'yii\web\YiiAsset']);
?>

<div class="modal fade" id="denyLogin" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h5 class="modal-title" id="myModalLabel">禁止角色登录</h5>
            </div>

            <div class="modal-body">
                被禁止登录的角色会在SDK登录后无法进入游戏，跳回到SDK登录
                <?php
                $denyForm=ActiveForm::begin([
                    'id'=>'denyLoginForm',
                    'method'=>'post',
                    'options'=>['class'=>'form-inline']
                ]);
                ?>
                <table class="table table-condensed" style="table-layout: fixed;">
                    <tr class="hidden">
                        <td width="100">游戏:</td>
                        <td><?= $denyForm->field($denyLoginModel,'gameId')->textInput(['id'=>'denyLoginGameId'])->label(false);?></td>
                    </tr>
                    <tr class="hidden">
                        <td>区服:</td>
                        <td><?=$denyForm->field($denyLoginModel,'serverId')->textInput(['id'=>'denyLoginServerId'])->label(false);;?></td>
                    </tr>
                    <tr>
                        <td width="100">角色名:</td>
                        <td><?=$denyForm->field($denyLoginModel,'roleName')->textInput(['placeholder'=>'角色名称','id'=>'denyLoginRoleName'])->label(false);;?></td>
                    </tr>
                </table>
                <?php
                ActiveForm::end();
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" onclick="denyCharacter()">确认</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="allowLogin" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h5 class="modal-title" id="myModalLabel">允许登录</h5>
            </div>

            <div class="modal-body">
                <?php
                $allowForm=ActiveForm::begin([
                    'id'=>'allowLoginForm',
                    'method'=>'post',
                    'options'=>['class'=>'form-inline']
                ]);
                ?>
                <table class="table table-condensed" style="table-layout: fixed;">
                    <tr class="hidden">
                        <td width="100">游戏:</td>
                        <td><?= $allowForm->field($allowLoginModel,'gameId')->textInput(['id'=>'allowLoginGameId'])->label(false);?></td>
                    </tr>
                    <tr class="hidden">
                        <td>区服:</td>
                        <td><?=$allowForm->field($allowLoginModel,'serverId')->textInput(['id'=>'allowLoginServerId'])->label(false);;?></td>
                    </tr>
                    <tr>
                        <td width="100">角色名:</td>
                        <td><?=$allowForm->field($allowLoginModel,'roleName')->textInput(['placeholder'=>'角色名称','id'=>'allowLoginRoleName'])->label(false);;?></td>
                    </tr>
                </table>
                <?php
                ActiveForm::end();
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" onclick="allowCharacter()">确认</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="unvoice" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h5 class="modal-title" id="myModalLabel">禁言</h5>
            </div>

            <div class="modal-body" id="kick_playerName">
                <?php
                $unvoiceForm=ActiveForm::begin([
                    'id'=>'unvoiceForm',
                    'method'=>'post',
                    'options'=>['class'=>'form-inline']
                ]);
                ?>
                <table class="table table-condensed" style="table-layout: fixed;">
                    <tr class="hidden">
                        <td width="100">游戏:</td>
                        <td><?= $unvoiceForm->field($unvoiceModel,'gameId')->textInput(['id'=>'unvoiceGameId'])->label(false);?></td>
                    </tr>
                    <tr class="hidden">
                        <td>区服:</td>
                        <td><?=$unvoiceForm->field($unvoiceModel,'serverId')->textInput(['id'=>'unvoiceServerId'])->label(false);;?></td>
                    </tr>
                    <tr>
                        <td width="100">角色名:</td>
                        <td><?=$unvoiceForm->field($unvoiceModel,'roleName')->textInput(['placeholder'=>'角色名称','id'=>'unvoiceRoleName'])->label(false);;?></td>
                    </tr>
                    <tr>
                        <td>禁言时长:</td>
                        <td><?=$unvoiceForm->field($unvoiceModel,'time')->textInput(['placeholder'=>'禁言时长(max:9999999)','id'=>'unvoiceTime'])->label(false);;?></td>
                    </tr>
                </table>
                <?php
                ActiveForm::end();
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" onclick="submitUnvoice()">确认</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="OffLine" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">确认将玩家踢下线吗？</h4>
            </div>

            <div class="modal-body" id="kick_playerName">
                <?php
                $kickForm=ActiveForm::begin([
                    'id'=>'kickForm',
                    'action'=>'cmd/kick',
                    'method'=>'post',
                    'options'=>['class'=>'form-inline']
                ]);
                echo $kickForm->field($kickModel,'playerName')->textInput(['placeholder'=>'角色名称','disabled'=>'disabled']);
                ActiveForm::end();
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" onclick="submitKick()">确认</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="applyForVcion">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">扶持申请</h4>
            </div>
            <div class="modal-body">
                <?php
                $form=ActiveForm::begin([
                    'id'=>'createSupportForm',
                    'fieldConfig' => ['template' => '{input}'],
                    'class'=>'form-inline',
                ]);
                ?>
                <div class="alert alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <p class="text-info">非充值类型:金钻/元宝通过邮件形式发放，不算充值积分</p>
                    <p class="text-info">充值类型：模拟充值发放,记录充值积分等，玩家可以领取常规充值奖励等</p>
                    <p class="text-info">比例:1RMB=100金钻</p>
                </div>
                <table class="table table-condensed" style="table-layout: fixed;">
                    <tr>
                        <td width="100">基础:</td>
                        <td>
                            <div class="row">
                                <div class="col-md-3"><?=$form->field($supportModel,'type')->dropDownList(
                                        [0=>"非充值",1=>"充  值",2=>"计费商品",3=>"道具物品"],
                                        [
                                            "class"=>"selectpicker form-control col-xs-2",
                                            "data-width"=>"fit",
                                            "id"=>"supportType",
                                            "title"=>"类型",
                                            "onchange"=>"changeSupportType()"
                                        ]
                                    )?></div>
                                <div class="col-md-3">
                                    <?=$form->field($supportModel,'gameId')->dropDownList(
                                        $games,
                                        [
                                            "class"=>"selectpicker form-control col-xs-2 hidden",
                                            "data-width"=>"fit",
                                            "id"=>"supportGames",
//                                            "onchange"=>"changeSupportGame(this,'#supportDistributors')",
                                            "title"=>"选择游戏"
                                        ]
                                    )?></div>
                                <div class="col-md-3"><?=$form->field($supportModel,'distributorId')->dropDownList(
                                        [],
                                        [
                                            "class"=>"selectpicker form-control col-xs-2 hidden",
                                            "data-width"=>"fit",
                                            "id"=>"supportDistributors",
//                                            "onchange"=>"changeSupportDistributor(this,'#supportGames','#supportServers')",
                                            "title"=>"分销商"
                                        ]
                                    )?></div>
                                <div class="col-md-3"><?=$form->field($supportModel,'serverId')->dropDownList(
                                        [],
                                        [
                                            "class"=>"selectpicker form-control col-xs-2 hidden",
                                            "data-width"=>"fit",
                                            "id"=>"supportServers",
                                            "title"=>"选择区服"
                                        ]
                                    )?></div>
                            </div>
                        </td>
                    </tr>

                    <tr id="roleAccount" class="hidden">
                        <td>角色账号:</td>
                        <td><?= $form->field($supportModel,'roleAccount')->textInput(['id'=>'txtRoleAccount'])?></td>
                    </tr>
                    <tr id="roleId" class="hidden">
                        <td>角色ID:</td>
                        <td><?= $form->field($supportModel,'roleId')->textInput(['id'=>'txtRoleId'])?></td>
                    </tr>
                    <tr id="roleName" class="hidden">
                        <td>角色名称:</td>
                        <td><?= $form->field($supportModel,'roleName')->textInput(['id'=>'txtRoleName'])?></td>
                    </tr>
                    <tr id="reason" class="hidden">
                        <td>申请理由:</td>
                        <td><?= $form->field($supportModel,'reason')->textInput()?></td>
                    </tr>
                    <tr id="products" class="hidden">
                        <td>计费物品:</td>
                        <td>
                            <?= $form->field($supportModel,'productId')->dropDownList([],['id'=>'txtProducts'])?>
                        </td>
                    </tr>
                    <tr id="trSupporItems" class="hidden">
                        <td>
                            物品:
                        </td>
                        <td>
                            <table class="table table-bordered table-condensed" id="tabSupporItems">
                                <tr class="active">
                                    <td>物品名</td>
                                    <td>数量</td>
                                    <td>绑定</td>
                                    <td>-</td>
                                </tr>
                                <tr id="selectItem" class="hidden">
                                    <td>
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
                                    </td>
                                    <td><input size="5" value="1" id="itemNum"/></td>
                                    <td><input type="checkbox" id="ckBind" checked="checked"/></td>
                                    <td><div class="btn btn-small btn-success" id="btnAddItem" onclick="addItem()">确定</div></td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="center">
                                        <div class="btn btn-small btn-default" onclick="handleAddItem()">添加</div>
                                    </td>
                                </tr>
                            </table>

                            <?= $form->field($supportModel,'items')->hiddenInput(['id'=>'supporItems'])?>
                        </td>
                    </tr>
                    <tr id="number" class="hidden">
                        <td>申请数量:</td>
                        <td><?= $form->field($supportModel,'number')->textInput(['placeholder'=>'填写金钻数量。1RMB=100金钻','id'=>'txtNumber'])?></td>
                    </tr>
                </table>
            </div>
            <?php
            ActiveForm::end();
            ?>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-success" onclick="createSupport()">申请</button>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default">
    <?php
        echo $this->render('../commonSearch', ['searchModel' => $searchModel,'games'=>$games,'distributors'=>$distributors,'servers'=>$servers]);
    ?>
    <div class="panel-heading" onclick=""><label>基础信息</label></div>
    <div class="panel-body">
        <div class="row">
            <div class="row">
                <div class="col-md-3">
                    <table class="table table-striped table-bordered" id="roleList">
                        <thead>
                            <tr>
                                <td>角色列表</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>筛选后显示</td>
                            </tr>
                        </tbody>
                    </table>
                    <ul class="pagination hidden">
                        <li class="prev disabled"><a href="#"><</a></li>
                        <li class="next disabled"><a href="#">></a></li>
                    </ul>
                </div>
                <div class="col-md-9">
                    <ul class="nav nav-tabs" id="tabRoleInfo">
                        <li role="presentation" class="active"><a href="javascript:;" onclick="handlerTabSelected(this,'baseAttribute')">玩家属性</a></li>
                        <li role="presentation" ><a href="javascript:;" onclick="handlerTabSelected(this,'itemsAttribute')">玩家物品</a></li>
                        <li role="presentation" ><a href="javascript:;" onclick="handlerTabSelected(this,'paramsAttribute')">玩家变量</a></li>
                    </ul>
                    <div class="row hidden">
                        <div class="col-md-12">
                            <div  id="cloneAttrTarget" class="roleAttribute">
                                <table class="table table-condensed">
                                    <tr>
                                        <td>名称:<label class="chrname"></label></td>
                                        <td>账号:<label class="account"></label></td>
                                        <td>唯一:<label class="seedname"></label></td>
                                    </tr>
                                    <tr>
                                        <td>渠道ID:<label class="distributionUserId"></label></td>
                                        <td>渠道账号:<label class="distributionAccount"></label></td>
                                        <td>创建:<label class="create_time"></label></td>
                                    </tr>
                                    <tr>
                                        <td>战神:<label class="herolv"></label></td>
                                        <td>职业:<label class="job"></label></td>
                                        <td>性别:<label class="gender"></label></td>
                                    </tr>
                                    <tr>
                                        <td>等级:<label class="lv"></label></td>
                                        <td>金币:<label class="money"></label></td>
                                        <td>元宝:<label class="vcoin"></label></td>
                                    </tr>
                                    <tr>
                                        <td>行会:<label class="guild"></td>
                                        <td>血量:<label class="cur_hp"></label></td>
                                        <td>蓝量:<label class="cur_mp"></label></td>
                                    </tr>
                                    <tr>
                                        <td>登入:<label class="last_login_time"></label></td>
                                        <td>登出:<label class="last_logout_time"></label></td>
                                        <td>状态:<label class="deleted"></label></td>
                                    </tr>
                                </table>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button id="btnUnvoice" class="btn btn-info" data-toggle="modal" data-target="#unvoice" onclick="">禁言</button>
                                        <button id="btnAllowLogin" class="btn btn-info" data-toggle="modal" data-target="#denyLogin">禁止登录</button>
                                        <button id="btnDenyLogin" class="btn btn-info" data-toggle="modal" data-target="#allowLogin">允许登录</button>
                                        <button class="btn btn-info hidden" data-toggle="modal" data-target="#myModal">强制下线</button>
                                        <button class="btn btn-info" data-toggle="modal" data-target="#applyForVcion">道具申请</button>
                                        <button class="btn btn-info hidden">IP禁止登录</button>
                                        <button class="btn btn-info hidden">设备禁止登录</button>
                                        <button class="btn btn-info hidden">账号禁止登录</button>
                                    </div>
                                </div>
                                <div>
                                    <input type="hidden" id="hiddenChrname"/>
                                    <input type="hidden" id="hiddenAccount"/>
                                    <input type="hidden" id="hiddenRoleId"/>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row hidden">
                        <table class="roleWears" border="1" id="cloneWearsTarget">
                            <tr>
                                <td class="pos_-4" width="50" height="50"></td>
                                <td colspan="4"></td>
                                <td class="pos_-8" width="50" height="50"></td>
                            </tr>
                            <tr>
                                <td class="pos_-6" width="50" height="50"></td>
                                <td colspan="4"></td>
                                <td class="pos_-14" width="50" height="50"></td>
                            </tr>
                            <tr>
                                <td class="pos_-12" width="50" height="50"></td>
                                <td colspan="4"></td>
                                <td class="pos_-13" width="50" height="50"></td>
                            </tr>
                            <tr>
                                <td class="pos_-10" width="50" height="50"></td>
                                <td class="pos_"></td>
                                <td colspan="2"></td>
                                <td class="pos_"></td>
                                <td class="pos_-11" width="60" height="60"></td>
                            </tr>
                            <tr>
                                <td class="pos_-18" width="60" height="60"></td>
                                <td class="pos_-28" width="60" height="60"></td>
                                <td class="pos_-22" width="60" height="60"></td>
                                <td class="pos_-24" width="60" height="60"></td>
                                <td class="pos_-26" width="60" height="60"></td>
                                <td class="pos_-20" width="60" height="60"></td>
                            </tr>
                        </table>
                        <table class=""  id="cloneBagTarget">
                        </table>
                        <table class="" id="cloneDepotTarget">
                        </table>
                    </div>
                    <div id="baseAttribute" class="roleTable">

                    </div>
                    <div id="itemsAttribute" class="roleTable hidden">
                        <div class="row">
                            <div class="col-md-1">
                                <ul class="nav nav-pills" id="tabPosition">
                                    <li role="presentation" class="active"><a href="javascript:;" onclick="handlerTabPosSelected(this,'wears')">穿戴</a></li>
                                    <li role="presentation"><a href="javascript:;" onclick="handlerTabPosSelected(this,'bag')">背包</a></li>
                                    <li role="presentation"><a href="javascript:;" onclick="handlerTabPosSelected(this,'depot')">仓库</a></li>
                                </ul>
                            </div>
                            <div class="col-md-11">
                               <div class="row" id="roleWears">
<!--                                    身上物品-->
                               </div>
                                <div class="row" id="roleBag">
<!--                                    背包物品-->
                                </div>
                                <div class="row" id="roleDepot">
<!--                                    仓库物品-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="paramsAttribute" class="roleTable hidden">
                        暂时还没有
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
