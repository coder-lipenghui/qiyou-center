<?php


namespace backend\controllers\center;


use common\helps\CurlHttpClient;

class QusanguoController extends CenterController
{
    protected function loginValidate($request, $distribution)
    {
        $url="http://zsq.73guo.com/sdk.php/LoginNotify/login_verify";
        $appKey=$distribution->appKey;
        $body=[
            'token'=>$request['token'],
            'user_id'=>$request['uid'],
            'app_id'=>$distribution->appID
        ];
        $signStr = "app_id=".$body['app_id']."&mem_id=".$body['user_id']."&user_token=".$body['token']."&app_key=".$appKey;
        $body['sign']=md5($signStr);
        $params = json_encode($body);

        $curl=new CurlHttpClient();

        $result=$curl->sendPostData($url,$params);
        if ($result)
        {
            $resultArr=json_decode($result,true);
            if ($resultArr['status']=='200')
            {
                $player = array(
                    'distributionUserId'        => $resultArr['user_id'],
                    'distributionUserAccount'   => $resultArr['user_account'],
                    'distributionId'            => $distribution->id,
                );
                return $player;
            }
        }
        return null;
    }
    protected function orderValidate($distribution)
    {
        //构建返回信息
        $this->paymentDeliverFailed     = "DELIVER FAILED";
        $this->paymentAmountFailed      = "AMOUNT FAILED";
        $this->paymentRepeatingOrder    = "REPEATING ORDER";
        $this->paymentValidateFailed    = "VALIDATE FAILED";
        $this->paymentSuccess           = "SUCCESS";

        $jsonData=file_get_contents("php://input");


        if (empty($jsonData))
        {
            \Yii::error("请求参数为空","order");
            return false;
        }else{
            $urldata = json_decode($jsonData,true);
            $order_id = isset($urldata['out_trade_no']) ? $urldata['out_trade_no'] : '';
            $money = isset($urldata['price']) ? $urldata['price'] : 0.00;
            $order_status = isset($urldata['pay_status']) ? $urldata['pay_status'] : '';
            $attach = isset($urldata['extend']) ? $urldata['extend'] : ''; //CP扩展参数
            $sign = isset($urldata['sign']) ? $urldata['sign'] : ''; // 签名
            $money = number_format($money,2);

            //1 校验参数合法性
            if (empty($urldata) || empty($order_id) || empty($money) || empty($order_status) || empty($attach) || empty($sign)){
                //CP添加自定义参数合法检测
                \Yii::error("参数校验失败","order");
                return false;
            }
            $appKey=$distribution->appKey;
            $paramStr = $order_id.$urldata['price'].$order_status.$attach.$appKey;
            $verifySign = md5($paramStr);
            if (0 == strcasecmp($sign, $verifySign)){
                return [
                    'orderId'=>$attach,
                    'distributionOrderId'=>$order_id,
                    'payTime'=>time(),
                    'payAmount'=>$urldata['price']*100,
                ];
            }else{
                \Yii::error("请求参数:".$jsonData,"order");
                \Yii::error("sign对比失败:".$sign."==>".$verifySign,"order");
            }
        }
        return false;
    }
}