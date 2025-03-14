<?php
namespace glm;
class embedding{
	public static $api_key="sk-2b9";
	public static $model="";
	public static $dimensions="1024";
	public static function init($cfg=[]){
		$arr=explode(",",$cfg["glm_api_key"]);
		shuffle($arr);
		$api_key=$arr[0];
        self::$api_key= $api_key;
		if(!empty($cfg["glm_text2text_model"])){
			self::$model=$cfg["glm_text2text_model"];
		}else{
			self::$model='embedding-3';
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
	
	
	public static function batch($data=[]){	
		 
		 
		 
		$ops=[
			 
			"model"=>self::$model,
			"dimensions"=>self::$dimensions,
			"input"=>$data
		];	
		$res=self::post_json("https://open.bigmodel.cn/api/paas/v4/embeddings",json_encode($ops),false);
        
		$result=[]; 
		$arr=json_decode($res,true);
		if(isset($arr["data"])){
			 
			foreach($arr["data"] as $v){
				$result[]=$v["embedding"];
			}
		}
		
		return $result;
	}
	
	public static function single($content=[]){	
		 
		$data=[$content]; 
		 
		$ops=[
			 
			"model"=>self::$model,
			"dimensions"=>self::$dimensions,
			"input"=>$data
		];	
		$res=self::post_json("https://open.bigmodel.cn/api/paas/v4/embeddings",json_encode($ops),false);
        
		$result=""; 
		$arr=json_decode($res,true);
		if(isset($arr["data"][0]["embedding"])){
			$result=$arr["data"][0]["embedding"];
		}
		
		return $result;
	}
	
}

 
 
