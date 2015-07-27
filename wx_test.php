<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "TokenToReplace");
$wechatObj = new wechatCallbackapiTest();

if(!isset($_GET['echostr'])){
	$wechatObj->responseMsg();
}else{
	$wechatObj->valid();
}

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    // 接入成功运行responseMsg, 实现自动回复
    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
            
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
            	$type = $postObj->MsgType;
                $event = $postObj->Event;

                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            
            // 回复图文消息
            if($type == "event" && $event == "subscribe"){
                $msgType = "text";
                $contentStr = "AABBB";
            	
                $picTpl = "
                <xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>1</ArticleCount>
<Articles>
<item>
<Title><![CDATA[123]]></Title> 
<Description><![CDATA[12333]]></Description>
<PicUrl><![CDATA[123]]></PicUrl>
<Url><![CDATA[123]]></Url>
</item>
</Articles>
</xml>";
            	$resultStr = sprintf($picTpl, $fromUsername, $toUsername, $time);
                echo $resultStr;
            }
            	
            //关键字自定义回复
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = $postObj->MsgType;
                    if(($keyword)=="1"){
                        $url="http://v.juhe.cn/weather/index?key=1fa544462478cacce193b1cd13809ac0&dtype=json&cityname=%E4%B8%8A%E6%B5%B7&format=json";
                    	$str=file_get_contents($url);
                        $de_json=json_decode($str,TRUE);
                        $contentStr = $de_json['result']['today']['temperature'].$de_json['result']['today']['weather'];
                    }
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,  $contentStr);
                	echo $resultStr;
                }
        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>
