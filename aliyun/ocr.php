<?php
namespace aliyun;
class ocr{
	public static $api_key="sk-2b9";
	public static $model="";
	public static function init($cfg=[]){
        $arr=explode(",",$cfg["ali_api_key"]);
		shuffle($arr);
		$api_key=$arr[0];
        self::$api_key= $api_key;
		if(!empty($cfg["ali_ocr_model"])){
			self::$model=$cfg["ali_ocr_model"];
		}else{
			self::$model='qwen-vl-max-latest';
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
	
	
	public static function get($imgurl){	
		
		$messages=[]; 
		$prompt=[
			
			["type"=>"image_url","image_url"=>[
			  "url"=>$imgurl
			]],
			["type"=>"text","text"=>"只要给我图片上的所有文字，不要多余的内容"]
		]; 
		$messages[]=[
			"role"=>"user",
			"content"=>$prompt
		];
		$ops=[
			 
			"model"=>self::$model,
	 
			"messages"=>$messages,
			 
		];	
		$res=self::post_json("https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions",json_encode($ops),false);
		 
        $content=""; 
		$arr=json_decode($res,true);
		if(isset($arr["choices"][0]["message"]["content"])){
			$content=$arr["choices"][0]["message"]["content"];
		}elseif(!empty($arr["text"])){
			$content=$arr["text"];
		}else{
			print_r($res);
			print_r($imgurl);
		} 
		
		return $content;
	}
	 
	
}

 
 
