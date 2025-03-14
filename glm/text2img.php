<?php
namespace glm;
class text2img{
	public static $api_key="sk-2b9";
	public static $model="";
	public static function init($cfg=[]){

		$arr=explode(",",$cfg["glm_api_key"]);
		shuffle($arr);
		$api_key=$arr[0];
        self::$api_key= $api_key;
		if(!empty($cfg["glm_text2img_model"])){
			self::$model=$cfg["glm_text2img_model"];
		}else{
			self::$model='cogview-3-flash';
		}
		 
         
	}
	public static function post_json($url, $json,$Async="enable"){
	
		$api_key=self::$api_key;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 	
		curl_setopt($ch, CURLOPT_POST, TRUE); 	
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json); 	
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 	
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 1);	
		$asyncStr="";	
		if($Async=='enable'){	
			$asyncStr='X-DashScope-Async: '.$Async;	
		}
	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(	
			'Content-Type: application/json',	
			'Content-Length: ' . strlen($json),	
			'Authorization: Bearer '.$api_key,
			$asyncStr
		));	
		$ret = curl_exec($ch);
		curl_close($ch);
		return $ret;	
	}
	public static function post_json_sse($url, $json,$callback)
	{
		$api_key=self::$api_key;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		curl_setopt($ch, CURLOPT_POST, TRUE); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($json),
			'Authorization: Bearer '.$api_key,
			'X-DashScope-SSE: enable'
		));
		curl_setopt($ch, CURLOPT_WRITEFUNCTION,$callback);
		$ret = curl_exec($ch);
	 
		curl_close($ch);
		return $ret;
	}
	
	
	public static function create($ops=[]){	
		 
		 
		$prompt=$ops["prompt"];
        $size="1024x1024";
        $user_id="asasqweqwe";
		$num=1;
		if(isset($ops["num"])){
			$num=$ops["num"];
		}
		$ops=[
			 
			"model"=>self::$model,
			"prompt"=>$prompt,
			"user_id"=>$user_id, 
			"size"=>$size
		];	
		$list=[];
		for($i=0;$i<$num;$i++){
			$res=self::post_json("https://open.bigmodel.cn/api/paas/v4/images/generations",json_encode($ops),false);        
			$imgurl=""; 
			$arr=json_decode($res,true);
			if(isset($arr["data"][0]["url"])){
				$imgurl=$arr["data"][0]["url"];
				$list[]=$imgurl;
			}
		}
		
		
		return $list;
	}
	
	public static function checkTask($taskid){
		return [];
	}
	
}

 
 
