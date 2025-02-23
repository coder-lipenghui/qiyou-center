<?php
/**
 * Created by PhpStorm.
 * User: lipenghui
 * Date: 2019-04-24
 * Time: 16:37
 */

namespace backend\models\api;

use Yii;
class VcoinRecord extends BaseApiModel
{

    public $addvc;//增加/移除元宝数量
    public $nowvc;
    public $playername;
    public $type;
    public $src;
    public $isBind;
    public function rules()
    {
        $rules=parent::rules();
        $myRules=[
            [['playerName','type','isBind'],'required'],
            [['addvc'],'integer','integerOnly' => true, 'min'=>1],
            [['src','isBind'],'integer']
        ];
        return array_merge($rules,$myRules);
    }

    public function attributeLabels()
    {
        $parentLabels= parent::attributeLabels();
        $myLabels=[
            'playername'=> Yii::t('app','玩家名称'),
            'src'=> Yii::t('app','操作方式'),
            'addvc'=>  Yii::t('app','操作元宝数'),
            'nowvc'=>  Yii::t('app','操作后剩余'),
            'type' => Yii::t('app','记录方式'),
        ];
        return array_merge($parentLabels,$myLabels);
    }

}