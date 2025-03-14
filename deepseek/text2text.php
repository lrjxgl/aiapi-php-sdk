<?php
namespace deepseek;
class text2text{
	public static $api_key="sk-2b9";
	public static $model="";
	public static function init($cfg=[]){
		$arr=explode(",",$cfg["ds_api_key"]);
		shuffle($arr);
		$api_key=$arr[0];
        self::$api_key= $api_key;
		 

		self::$model=$cfg["dsmodel"];
	}
	public static function chat($prompt,$messages=[]){
	
	
		$url="https://api.deepseek.com/chat/completions";	
		$messages[]=[
				"role"=>"user",
				"content"=>$prompt	
		];
	
		$data=[	
			"model"=>self::$model,
			 
			"messages"=>$messages
		];
	
		$json=json_encode($data);
		$res=self::post_json($url,$json);
		$arr=json_decode($res,true);
		if(empty($arr["choices"][0]["message"]["content"])){	
			return "";	
		}else{	
			return $arr["choices"][0]["message"]["content"];	
		}
	} 

	public static function stream_chat($prompt,$messages=[],$callback=null){
		$messages[]=["role"=>"user","content"=>$prompt];
		$ops=[
			"model"=>self::$model,
			"messages"=>$messages,
			"stream"=>true
		];	
		$res=self::post_json_sse("https://api.deepseek.com/chat/completions",json_encode($ops),$callback);
		return $res;
	}
	
	public static  function post_json($url, $json){
	
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
			'Authorization: Bearer '.$api_key
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
	
}