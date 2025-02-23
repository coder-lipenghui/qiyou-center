<?php
/**
 * Created by PhpStorm.
 * User: lipenghui
 * Date: 2019-04-19
 * Time: 15:25
 * 调用周期：
 *      1.派生类中设置：ApiName
 *      2.设置访问地址：initApiUrl(),需要拼接好请求参数
 *      3.获取JSON数据：getJsonData()
 * 其他：
 *
 */

namespace backend\controllers\api;

use backend\models\TabDebugServers;
use backend\models\TabGames;
use backend\models\TabServers;
use common\helps\CurlHttpClient;
use Yii;
use yii\web\Controller;

class BaseController extends Controller
{
    //API侧三个数据库对应的ID
    public static $API_DB_OCTGAME=1;
    public static $API_DB_OCENTER=2;
    public static $API_DB_OCTLOG=3;

    //游戏API接口名称
    public $apiName="";
    public $apiUrl="";
    public $apiParams=[];  //RESTful查询接口 统一采用get形式 所以这边用http_build_query
    public $apiDeafultParams=[];
    public $apiDb=0;


//    public $searchModel;
//    public $dataProvider=new ArrayDataProvider([]);

    private $inited=false;
    /**
     * 获取游戏服务器的API接口地址
     *
     * @param $gid 游戏ID
     * @param $did 平台ID
     * @param $sid 区服ID
     * @return string 服务器的真实url地址
     */
    protected function initApiUrl($gid,$did,$sid,$params)
    {
        $game=TabGames::find()->where(['id'=>$gid])->one();
        if ($game)
        {
            $server=null;
            if ($sid<15)
            {
                $server=TabDebugServers::find()->where(['id'=>$sid])->one();
            }else{
                $server=TabServers::find()->where(['id'=>$sid])->one();
                if (!empty($server) && !empty($server->mergeId))
                {
                    $server=TabServers::find()->where(['id'=>$server->mergeId])->one();
                }
            }
            if($server)
            {
                unset($params['serverId']);
                $this->apiDeafultParams=[
                    'sku'=>$game->sku,
                    'did'=>$did,
                    'serverId'=>$server->index,
                    'db'=>$this->apiDb];
                $this->apiParams=$params;//$defaultParam;
                if ($server->id<15)
                {
                    $this->apiDeafultParams['sku']='TEST';
                    $this->apiDeafultParams['did']=$game->versionId;
                }
                if (false)//本地测试
                {
                    $this->apiUrl="http://gameapi.com:8888/";
                }else{
                    $this->apiUrl="http://".$server->url."/api/";
                }
                $this->inited=true;
            }else{
                $this->inited=false;
            }
        }else{
            $this->inited=false;
        }

        return $this->inited;
    }
    /**
     * 从游戏服务器API获取Json数据
     * 统一get样式
     * @param $url
     * @return bool|string
     */
    protected function getJsonData()
    {
        if ($this->inited)
        {
            $url=$this->apiUrl.$this->apiName."?".http_build_query(array_merge($this->apiParams,$this->apiDeafultParams));
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_POSTFIELDS,"");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: text/json',
                'Content-Length: ' . strlen("")
            ));
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        }
        Yii::error("未设置ApiUrl");
        return null;
    }

    /**
     * 用于修改信息的PUT方法，需要提前构建好apiUrl及request form data
     * @return json json结果
     */
    protected function put()
    {
        if ($this->inited)
        {
            $url=$this->apiUrl.$this->apiName."?".http_build_query($this->apiDeafultParams);

            $curl=new CurlHttpClient();
            $response=$curl->RESTfulApi($url,"PUT",$this->apiParams);
            return $response;
        }else{
            exit("init failed");
        }
        Yii::error("未设置ApiUrl");
        return null;
    }
    protected function delete()
    {

    }
    /**
     * 解析背包仓库的的二进制数据
     * @param $data base64过的二进制数据
     * @return array
     */
    protected  function itemParser($data)
    {
        //TODO 这个接口需要整理到版本分类中，每个游戏有自己不同的写入规则
        $data=base64_decode($data);
        $binaryLen=strlen($data);
        $unpackFormat="lpos/ltype/lduration/lduramax/litemflag/sluck/lnumber/lcreatetime/lidentify/sprotect/lprice/llevel/llock/";
        $formatLen=48;
        $size=unpack('lcount/',$data);
        $total=$size['count'];
        $offset=4;
        $items=[];
        for ($i=0;$i<$total;$i++) {
            //将前面的int、short类型的值全部读取出来
            $item = unpack($unpackFormat,substr($data,$offset, $formatLen));
            $offset += $formatLen;
            //固定三个字符串值：itemplayer，itemfrom，itemtag
            for ($k=0; $k < 3; $k++) {
                $tempLen=0;
                for ($j=0; $j < $binaryLen; $j++) {
                    $char=unpack('a', substr($data, $offset+$j,$offset+$j+1));
                    if ($char[1]=="\0") {
                        $tempLen=$j+1;
                        break;
                    }
                }
                $temStr=unpack('a*',substr($data, $offset,$tempLen));
                $item[]=$temStr[1];
                $offset+=$tempLen;
            }
            $items[]=$item;
        }
        return $items;
    }
}