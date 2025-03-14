<?php
namespace glm;
class text2video{
	public static $api_key="sk-2b9";
	public static $model="";
	public static function init($cfg=[]){
		$arr=explode(",",$cfg["glm_api_key"]);
		shuffle($arr);
		$api_key=$arr[0];
        self::$api_key= $api_key;
		if(!empty($cfg["glm_text2video_model"])){
			self::$model=$cfg["glm_text2video_model"];
		}else{
			self::$model='cogvideox-flash';
		} 
         
        
	}

	public static function curl_get($url){
		$api_key=self::$api_key;
		$ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_HEADER, 0);
		 curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	 
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Bearer '.$api_key,
		));
		 $content= curl_exec($ch);
		 curl_close($ch);
		 return $content;
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
        $size="1080x1920";
        $user_id="asasqweqwe";
		$with_audio=false;
		if(isset($ops["with_audio"])){
			$with_audio=$ops["with_audio"];
		}
		$ops=[
			 
			"model"=>self::$model,
			"prompt"=>$prompt,
			"user_id"=>$user_id, 
			"size"=>$size,
			"with_audio"=>$with_audio
		];	
		$res=self::post_json("https://open.bigmodel.cn/api/paas/v4/videos/generations",json_encode($ops),false);
        
		$taskid=""; 
		$arr=json_decode($res,true);
		if(isset($arr["id"])){
			$taskid=$arr["id"];
		}
		
		return $taskid;
	}
	
	public static function checkTask($taskid){
		$url="https://open.bigmodel.cn/api/paas/v4/async-result/".$taskid;
		$res=self::curl_get($url);
		 
		$arr=json_decode($res,true);
		 
		$mp4url="";
		$imgurl="";
		$data=[
			"error"=>0
		];
		if(!empty($arr["error"])){
			return [
				"error"=>1,
				"message"=>$arr["error"]
			];
		}
		if(!empty($arr["video_result"][0])){
			$mp4url=$arr["video_result"][0]["url"];
			$imgurl=$arr["video_result"][0]["cover_image_url"];
			$data=[
				"mp4url"=>$mp4url,
				"imgurl"=>$imgurl,
				"error"=>0
			];
		}
		return $data;
	}
	
}

 
 
