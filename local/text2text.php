<?php
namespace local;
class text2text{
	public static $api_key="sk-2b9";
	public static $model="";
	public static $baseurl="http://127.0.0.1:11434/";
	public static function init($cfg=[]){
		self::$api_key='';
		self::$model='deepseek-r1:8b';
		self::$model='qwen2.5:7b';
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
	
	
	public static function chat($prompt,$messages=[]){	
		 
		 
		$messages[]=["role"=>"user","content"=>$prompt];	
		$ops=[
			 
			"model"=>self::$model,
			//"temperature"=>1.5,
			 
			"messages"=>$messages,
			"stream"=>false
		];	
		$res=self::post_json(self::$baseurl."v1/chat/completions",json_encode($ops),false);
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
		$res=self::post_json_sse(self::$baseurl."/v1/chat/completions",json_encode($ops),$callback);
		 
		 return $res;
	}
	
}
/*
header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		header('Connection: keep-alive');
		header('X-Accel-Buffering: no');
text2text::init();
$prompt=$_GET['prompt'];

$callback=function($ch, $data) {
	
	$lines=explode("\n",$data);
	foreach($lines as $line){
		$ex=explode(":",$line);
		if($ex[0]=='data'){
			$json=substr($line,5);
			$arr=json_decode($json,true);
			$con=$arr["choices"][0]["delta"]["content"];
			echo $con;
			ob_flush();
		    flush(); 
			if($arr["choices"][0]["finish_reason"]=='stop'){
				//用户中止
				break; 
			}
			 
			
		}
	}
	return strlen($data);
};
$res=text2text::stream_chat($prompt,[],$callback);
//echo nl2br($res); 
*/ 
