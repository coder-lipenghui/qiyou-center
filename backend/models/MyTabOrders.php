<?php
/**
 * Created by PhpStorm.
 * User: lipenghui
 * Date: 2019-05-18
 * Time: 11:22
 */

namespace backend\models;

use common\helps\CurlHttpClient;
use common\helps\LoggerHelper;
use yii\helpers\ArrayHelper;

class MyTabOrders extends TabOrders
{

    /**
     * 总付费账号数
     * @return int|string
     */
    public static function getTotalPayingUser($gameId)
    {
        $distributions=self::getDistribution($gameId);
        if ($distributions)
        {
            $query=TabOrders::find();
            $query->where([
                'distributionId'=>$distributions,
                'payStatus'=>'1',
            ])->groupBy('gameAccount');

            return $query->count();
        }
        return 0;
    }

    /**
     * 总付费金额
     * @return float|int|mixed
     */
    public static function getTotalRevenue($gameId)
    {
        $distributions=self::getDistribution($gameId);
        if ($distributions)
        {
            $query=TabOrders::find()
                ->where([
                    'payStatus'=>'1',
                    'distributionId'=>$distributions])
                ->select(['payAmount'])->asArray();
            $revenue=$query->sum('payAmount');
            $revenue=$revenue?$revenue/100:0;
            return $revenue;
        }
        return 0;
    }
    /**
     * 今日付费玩家数量
     * @return int
     */
    public static function getTodayPayingUser($gameId)
    {
        $distributions=self::getDistribution($gameId);
        if ($distributions)
        {
            $query=TabOrders::find();
            $query->where([
                'distributionId'=>$distributions,
                "FROM_UNIXTIME(payTime,'%Y-%m-%d')"=>date('Y-m-d'),
                'payStatus'=>'1',
            ])->groupBy('gameAccount');
            $total=$query->count();
            return $total;
        }
        return 0;
    }
    /**
     * 昨日付费玩家数量
     * @return int
     */
    public static function getYesterdayPayingUser($gameId)
    {
        $distributions=self::getDistribution($gameId);
        if ($distributions)
        {
            $tmp=date('Y-m-d');
            $query=TabOrders::find();
            $query->where([
                'distributionId'=>$distributions,
                "FROM_UNIXTIME(payTime,'%Y-%m-%d')"=>date('Y-m-d',strtotime("$tmp -1 day")),
                'payStatus'=>'1',
            ])->groupBy('gameAccount');
            $total=$query->count();
            return $total;
        }
        return 0;
    }
    /**
     * 今日充值总金额
     * @return float|int|mixed
     */
    public static function getTodayRevenue($gameId)
    {
        $distributions=self::getDistribution($gameId);
        if ($distributions)
        {
            $query=TabOrders::find()
                ->where(['payStatus'=>'1','FROM_UNIXTIME(payTime,"%Y-%m-%d")'=>date('Y-m-d'),'distributionId'=>$distributions])
                ->select(['payAmount'])->asArray();
            $totalToday=$query->sum('payAmount');
            $totalToday=$totalToday?$totalToday/100:0;
            return $totalToday;
        }
        return 0;
    }

    /**
     * 昨日充值金额
     * @return float|int|mixed
     */
    public static function getYesterdayRevenue($gameId)
    {
        //TODO 需要根据用户权限进行各项统计
        $cond=['between','payTime',strtotime(date('Y-m-d')." 00:00:00")-86400,strtotime(date('Y-m-d')."23:59:59")-86400];

        $query=TabOrders::find()
            ->where($cond)
            ->andWhere(['gameId'=>$gameId])
            ->andWhere(['=','payStatus','1']);
        $totalYesterday=$query->sum('payAmount');
        $totalYesterday=$totalYesterday?$totalYesterday/100:0;

        return $totalYesterday;
    }
    public static function getRevenueByDistributor($gameId)
    {
        $distributors=self::getDistributors();
        $data=[];
        if ($distributors)
        {
            $query=self::find();
            $query->select(['gameId','distributorId','money'=>'payAmont/100'])
                ->where(['distributorId'=>$distributors,'gameId'=>$gameId])
                ->groupBy('distributorId')
                ->asArray();
            $data=$query->all();
        }
        return $data;
    }
    /**
     * 过去30天内每天的充值金额
     */
    public static function getLast30Revenue($gameId)
    {
        $start=date('Y-m-d',strtotime('-30 day'));
        $end=date('Y-m-d');
        return self::getRevenueGroupByDay($gameId,$start,$end);
    }

    /**
     * 过去30天内每天的充值人数
     * (全渠道每天总和)
     * @throws \yii\db\Exception
     */
    public static function getLast30PayingUser($gameId)
    {
        $start=date('Y-m-d',strtotime('-30 day'));
        $end=date('Y-m-d');
        return self::getPayingUserGroupByDay($gameId,$start,$end);
    }
    /**
     * 获取起止日期内每日充值金额
     * @param $start
     * @param $end
     * @return array
     * @throws \yii\db\Exception
     */
    private static function getRevenueGroupByDay($gameId,$start,$end)
    {
        //测试SQL:
        //SELECT t1.time,if(t2.amount is NULL,0,t2.amount) FROM (SELECT DAY_SHORT_DESC as time FROM calendar WHERE DAY_SHORT_DESC>='2019-08-01' AND DAY_SHORT_DESC<='2019-09-01') as t1 LEFT JOIN (SELECT SUM(payAmount/100) as amount,FROM_UNIXTIME(payTime,'%Y-%m-%d') as time FROM tab_orders WHERE distributionId in (1,5,7) and payStatus='1' AND FROM_UNIXTIME(payTime,'%Y-%m-%d')>='2019-08-01' and FROM_UNIXTIME(payTime,'%Y-%m-%d')<='2019-09-01' GROUP BY FROM_UNIXTIME(payTime,'%Y-%m-%d')) as t2 ON t1.time=t2.time ORDER BY t1.time;
        $distributions=self::getDistributionString($gameId);
        if ($distributions)
        {
            $sql = "SELECT t1.time,if(t2.amount is NULL,0,t2.amount) as amount FROM 
            (SELECT DAY_SHORT_DESC as time FROM calendar WHERE DAY_SHORT_DESC>='$start' AND DAY_SHORT_DESC<='$end') as t1 
            LEFT JOIN 
            (SELECT SUM(payAmount/100) as amount,FROM_UNIXTIME(payTime,'%Y-%m-%d') as time FROM tab_orders WHERE distributionId in ($distributions) and payStatus='1' AND FROM_UNIXTIME(payTime,'%Y-%m-%d')>='$start' and FROM_UNIXTIME(payTime,'%Y-%m-%d')<='$end' GROUP BY FROM_UNIXTIME(payTime,'%Y-%m-%d')) as t2 
            ON t1.time=t2.time 
            ORDER BY t1.time";
            $data = $query = \Yii::$app->db->createCommand($sql)->queryAll();
            return $data;
        }
        return [];
    }

    /**
     * 获取起止日期内每日充值人数(全渠道)
     * @param $start
     * @param $end
     * @return array
     * @throws \yii\db\Exception
     */
    private static function getPayingUserGroupByDay($gameId,$start,$end)
    {
        //测试SQL：
        //SELECT t1.time,if(t2.number is NULL,0,t2.number) FROM (SELECT DAY_SHORT_DESC as time FROM calendar WHERE DAY_SHORT_DESC>='2019-08-01' AND DAY_SHORT_DESC<='2019-09-01') as t1 LEFT JOIN (SELECT COUNT(*) as number,FROM_UNIXTIME(payTime,'%Y-%m-%d') as time FROM tab_orders WHERE distributionId in (1,5,7) and payStatus='1' AND FROM_UNIXTIME(payTime,'%Y-%m-%d')>='2019-08-01' and FROM_UNIXTIME(payTime,'%Y-%m-%d')<='2019-09-01' GROUP BY gameAccount) as t2 ON t1.time=t2.time ORDER BY t1.time;

        $distributions=self::getDistributionString($gameId);
        if ($distributions)
        {
            $sql="SELECT t1.time,if(t2.number is NULL,0,t2.number) as number FROM 
            (SELECT DAY_SHORT_DESC as time FROM calendar WHERE DAY_SHORT_DESC>='$start' AND DAY_SHORT_DESC<='$end') as t1 
            LEFT JOIN 
            (SELECT count(*) as number,FROM_UNIXTIME(payTime,'%Y-%m-%d') as time FROM tab_orders WHERE distributionId in ($distributions) and payStatus='1' AND FROM_UNIXTIME(payTime,'%Y-%m-%d')>='$start' and FROM_UNIXTIME(payTime,'%Y-%m-%d')<='$end' GROUP BY gameAccount) as t2 
            ON t1.time=t2.time 
            ORDER BY t1.time";
            $data=$query=\Yii::$app->db->createCommand($sql)->queryAll();
            return $data;
        }
        return [];
    }
    public static function getPingUserGroupByDistributor($day,$gameId)
    {
        $start=date('Y-m-d',strtotime("-$day day"));
        $end=date('Y-m-d');
        return self::getPayingUserGroupByDistributorAndDate($gameId,$start,$end);
    }
    /**
     * 获取时段内各分销商每天付费人数
     * @param $gameId
     * @param $start
     * @param $end
     */
    private static function getPayingUserGroupByDistributorAndDate($gameId,$start,$end)
    {
        $distributors=self::getDistributors($gameId);
        $result=[];
        if (!empty($distributors))
        {
            foreach($distributors as $distributor)
            {
                $sql="SELECT t1.time,if(t2.number is NULL,0,t2.number) as number FROM 
                (SELECT DAY_SHORT_DESC as time FROM calendar WHERE DAY_SHORT_DESC>='$start' AND DAY_SHORT_DESC<='$end') as t1 
                LEFT JOIN 
                (SELECT gameId,distributorId,count(*) as number,FROM_UNIXTIME(payTime,'%Y-%m-%d') as time FROM tab_orders WHERE distributorId =$distributor and gameId=$gameId and payStatus='1' AND FROM_UNIXTIME(payTime,'%Y-%m-%d')>='$start' and FROM_UNIXTIME(payTime,'%Y-%m-%d')<='$end' GROUP BY distributorId,FROM_UNIXTIME(payTime,'%Y-%m-%d')) as t2 
                ON t1.time=t2.time 
                ORDER BY t1.time";
                $query=\Yii::$app->db->createCommand($sql);
                $data=$query->queryAll();
                $result[$distributor.""]=$data;
            }
        }
        return $result;
    }
    /**
     * 本月充值金额
     * @return float|int|mixed
     */
    public static function currentMonthAmount()
    {
        //TODO 需要根据用户权限进行各项统计
        $beginDate=date('Y-m-01',strtotime(date("Y-m-d")));
        $endDate=date('Y-m-d', strtotime("$beginDate +1 month -1 day"));

        $cond=['between','payTime',strtotime($beginDate),strtotime($endDate)];

        $query=TabOrders::find()
            ->where($cond)
            ->andWhere(['=','payStatus','1'])
            ->select(['payAmount'])->asArray();

        $totalToday=$query->sum('payAmount');
        $totalToday=$totalToday?$totalToday/100:0;

        return $totalToday;
    }

    /**
     * 获取某个渠道的今日充值金额
     * @param $gameId
     * @param $distributionId
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function todayAmountByDistribution($gameId,$distributionId)
    {
        //TODO 需要根据用户权限进行各项统计
        $date=date('Y-m-d');
        $query=MyTabOrders::find();
        $query->select(['amount'=>'sum(payAmount/100)'])
              ->where(['gameId'=>$gameId,'distributionId'=>$distributionId,'payStatus'=>'1',"FROM_UNIXTIME(payTime,'%Y-%m-%d')"=>$date])
              ->asArray();
        return $query->one();
    }

    /**
     * 本月各分销商充值金额
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function amountGroupByDistributor()
    {
        //TODO 需要根据用户权限进行各项统计
        $beginDate=date('Y-m-01',strtotime(date("Y-m-d")));
        $endDate=date('Y-m-d', strtotime("$beginDate +1 month -1 day"));
        $cond=['between','payTime',strtotime($beginDate),strtotime($endDate)];

        $query=TabOrders::find()
            ->select(['value'=>'sum(payAmount)/100','distributorId'])
            ->join('LEFT JOIN','tab_distribution','tab_orders.distributionId=tab_distribution.id')
            ->where($cond)
            ->asArray()
            ->andWhere(['=','payStatus','1'])
            ->groupBy(['distributorId']);

        $data=$query->all();


        for ($i=0;$i<count($data);$i++)
        {
            $distributor=TabDistributor::find()->where(['id'=>(int)$data[$i]['distributorId']])->one();
            if ($distributor)
            {
                $data[$i]['name']=$distributor->name;
                unset($data[$i]['distributorId']);
            }
        }
        return $data;
    }

    private static function getDistributionString($gameId)
    {
        $distributions=null;
        $uid=\Yii::$app->user->id;
        $modelPermission=new MyTabPermission();
        $permissions=$modelPermission->getDistributionByUidAndGameId($uid,$gameId);
        if ($permissions)
        {
            $distributionsArr=ArrayHelper::getColumn($permissions,'distributionId');
            $distributions=join(",",$distributionsArr);
        }
        return $distributions;
    }
    private static function getDistribution($gameId)
    {
        $distributons=null;
        $uid=\Yii::$app->user->id;
        $modelPermission=new MyTabPermission();
        $permissions=$modelPermission->getDistributionByUidAndGameId($uid,$gameId);

        if ($permissions)
        {
            $distributons=ArrayHelper::getColumn($permissions,'distributionId');
        }
        return $distributons;
    }
    private static function getDistributors($gameId=null)
    {
        $distributions=[];
        $uid=\Yii::$app->user->id;
        $modelPermission=new MyTabPermission();
        $permissions=$modelPermission->getDistributorsByUid($uid,$gameId);
        if ($permissions)
        {
            $distributions=ArrayHelper::getColumn($permissions,'distributorId');
        }
        return $distributions;
    }
    /**
     * 发货接口
     * 如果你不知道这个是做什么的，请不要乱动。
     * @param $orderId
     * @param $distribution
     * @return bool
     */
    public static function deliver($orderId,$distribution)
    {
        $order=null;
        if($distribution->isDebug || $distribution->isDebug==1)
        {
            $order=TabOrdersDebug::find()->where(['orderId'=>$orderId])->one();
        }else{
            $order=TabOrders::find()->where(['orderId'=>$orderId])->one();
        }
        if ($order===null)
        {
            $msg="订单不存在";
            \Yii::error($msg." orderId:".$orderId,"order");
            return false;
        }
        if ($order->delivered>0)
        {
            $msg="该笔订单已发货";
            LoggerHelper::OrderError($order->gameId, $order->distributionId, $msg,"");
        }else {
            if ($order->payStatus > 0) {
                $server=null;
                if($distribution->isDebug || $distribution->isDebug==1) {
                    $server = TabDebugServers::find()->where(['id' => $order->gameServerId])->one();
                }else{
                    $server = TabServers::find()->where(['id' => $order->gameServerId])->one();
                }
                if ($server === null) {
                    $msg = "区服不存在";
                    LoggerHelper::OrderError($order->gameId, $order->distributionId, $msg, $server->getFirstError());
                    return false;
                }
                if (!empty($server->mergeId))
                {
                    $tmp=TabServers::find()->where(['id'=>$server->mergeId])->one();
                    if (!empty($tmp))
                    {
                        $server=$tmp;
                    }
                }
                $distribution = TabDistribution::find()->where(['id' => $order->distributionId])->one();
                if ($distribution === null) {
                    $msg = "渠道不存在";
                    LoggerHelper::OrderError($order->gameId, $order->distributionId, $msg, $distribution->getFirstError());
                    return false;
                }
                $game = TabGames::find()->where(['id' => $order->gameId])->one();
                if(empty($game))
                {
                    $msg = "游戏不存在";
                    LoggerHelper::OrderError($order->gameId, $order->distributionId, $msg, $game->getFirstError());
                    return false;
                }
                //获取计费点信息
                $productQuery=TabProduct::find()->where(['productId'=>$order->productId,'gameId'=>$game->versionId]);
                $product=$productQuery->one();

                $requestBody = [
                    'channelId' => $distribution->id,
                    'paytouser' => $order->gameAccount,
                    'roleid' => $order->gameRoleId,
                    'paynum' => $order->orderId,
                    'payscript' => $product->productScript,
                    'paygold' => $order->payAmount / 100 * $distribution->ratio,//发放元宝数量= 分/100*比例
                    'paymoney' => $order->payAmount / 100,
                    'flags' => 1,// 1：充值发放 其他：非充值发放
                    'paytime' => $order->payTime,
                    'serverid' => $order->gameServerId,
                    'type' => $product->type,
                    'port'=>$server->masterPort
                ];
                $paymentKey = $game->paymentKey;
                $requestBody['flag'] = md5($requestBody['type'] . $requestBody['payscript'] . $requestBody['paynum'] . $requestBody['roleid'] . urlencode($requestBody['paytouser']) . $requestBody['paygold'] . $requestBody['paytime'] . $paymentKey);
                $resultJson=[];
                $curl = new CurlHttpClient();
                $url="http://" . $server->url;
                if (true)//新后台的发货接口
                {
                    $getBody=[
                        'sku'=>$game->sku,
                        'did'=>$distribution->distributorId,
                        'serverId'=>$server->index,
                        'db'=>$requestBody['type']==1?2:1 //脚本类型的需要走octgame,常规类型走ocenter
                    ];
                    if ($server->id<=15)
                    {
                        $getBody['sku']="TEST";
                        $getBody['did']=$game->versionId;
                    }
                    if (!empty($distribution->mingleDistributionId))
                    {
                        $tmp=TabDistribution::find()->where(['id'=>$distribution->mingleDistributionId])->one();
                        $tmpGame=TabGames::find()->where(['id'=>$tmp->gameId])->one();
                        if (!empty($tmp)&& !empty($tmpGame))
                        {
                            $getBody['sku']=$tmpGame->sku;
                            $getBody['did']=$tmp->distributorId;
                        }
                    }
                    $url = $url. "/api/payment?" . http_build_query($getBody);
                    $resultJson =$curl->sendPostData($url,$requestBody);
                }else{
                    $url = $url. "/app/ckcharge.php?" . http_build_query($requestBody);
                    $resultJson = $curl->fetchUrl($url);
                }
                $result = json_decode($resultJson, true);
                $msg = "";
                switch ($result['code']) {
                    case 1:  //发货成功
                    case -5: //订单重复
                        $order->delivered = '1';//发货状态：0：未发货 1：已发货
                        if (!$order->save()) {
                            $msg = "更新订单发货状态失败";
                            LoggerHelper::OrderError($order->gameId, $order->distributionId, $msg, $order->getErrors());
                            return false;
                        }
                        return true;
                        break;
                    case -1: //防沉迷数据库连接失败
                        $msg = "防沉迷数据库连接失败";
                        break;
                    case -2: //账号未找到
                        $msg = "[" . $requestBody['paytouser'] . "]账号未找到";
                        break;
                    case -3: //IP限制，暂时废弃
                        $msg = "IP限制";
                        break;
                    case -4: //sign验证出错
                        $msg = "sign验证出错";
                        break;
                    case -6: //超时，暂时废弃
                        $msg = "超时";
                        break;
                    case -8: //发货参数不全
                        $msg = "发货参数不全";
                        break;
                    case -9: //发货数与金额比例不正确，服务器侧写死了【paymoney*100=paygold】
                        $msg = "发货数与金额比例不正确";
                        break;
                }
                LoggerHelper::OrderError($order->gameId, $order->distributionId, $msg, $resultJson);

            } else {
                $msg = "订单未支付成功";
                LoggerHelper::OrderError($order->gameId, $order->distributionId, $msg, $order->getFirstError());
            }
        }
        return false;
    }
}