<?php


namespace backend\models\report;


use backend\models\TabDebugServers;
use backend\models\TabPlayers;
use backend\models\TabServers;

class ModelLevelLog extends TabLogLevel
{
    protected static $gameId;
    protected static $distributorId;

    public static function TabSuffix($gid,$did)
    {
        self::$gameId=$gid;
        self::$distributorId=$did;
    }
    public static function tableName()
    {
        $originalName=parent::tableName();
        if (self::$gameId && self::$distributorId)
        {
            return $originalName.'_'.self::$gameId.'_'.self::$distributorId;
        }
        return $originalName;
    }

    public function doRecord($params)
    {
        $result=['code'=>-1,'msg'=>'参数错误','data'=>[]];
        $this->logTime=time();
        $this->load(['ModelLevelLog'=>$params]);
        $this->roleName=urldecode($this->roleName);
        if($this->validate())
        {
            $player=TabPlayers::find()->where(['distributionUserId'=>$this->distributionUserId])->one();
            if ($player)
            {
                $query=TabServers::find();
                if ($this->serverId<15)
                {
                    $query=TabDebugServers::find();
                }
                $server=$query->where(['id'=>$this->serverId])->one();
                if ($server)
                {
                    if($this->save())
                    {
                        $result['code']=1;
                        $result['msg']="success";
                    }else{
                        $result['code']=-2;
                        $result['msg']="记录失败";
                        $result['data']=$this->getErrors();
                    }
                }else{
                    $result['code']=-3;
                    $result['msg']="区服不存在";
                }
            }else{
                $result['code']=-2;
                $result['msg']="渠道账号不存在";
            }
        }else{
            $result['data']=$this->getErrors();
        }
        return $result;
    }
}