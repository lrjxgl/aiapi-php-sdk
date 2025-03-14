<?php
namespace doubao;
class text2text{
	public static $api_key="sk-2b9";
	public static $model="";
	public static $baseurl="https://ark.cn-beijing.volces.com";
	public static function init($cfg=[]){
		$arr=explode(",",$cfg["doubao_api_key"]);
		shuffle($arr);
		$api_key=$arr[0];
        self::$api_key= $api_key;
		if(!empty($cfg["doubao_text2text_model"])){
			self::$model=$cfg["doubao_text2text_model"];
		}else{
			self::$model='Doubao-1.5-pro-32k';
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
	
	
	public static function chat($prompt,$messages=[]){	
		 
		 
		$messages[]=["role"=>"user","content"=>$prompt];	
		$ops=[
			 
			"model"=>self::$model,
			//"temperature"=>1.5,
			 
			"messages"=>$messages,
			"stream"=>false
		];	
		$res=self::post_json(self::$baseurl."/api/v3/chat/completions",json_encode($ops),false);
		 
		$content=""; 
		$arr=json_decode($res,true);
		
		if(isset($arr["choices"][0]["message"]["content"])){
			$content=$arr["choices"][0]["message"]["content"];
		}elseif(!empty($arr["text"])){
			$content=$arr["text"];
		} 
		
		return $content;
	}
	
	public static function stream_chat($prompt,$messages=[],$callback=null){
		$messages[]=["role"=>"user","content"=>$prompt];
		$ops=[
			"model"=>self::$model,
			"messages"=>$messages,
			"stream"=>true
		];	
		$res=self::post_json_sse(self::$baseurl."/api/v3/chat/completions",json_encode($ops),$callback);
		 
		return $res;
	}
	
}

 
 
