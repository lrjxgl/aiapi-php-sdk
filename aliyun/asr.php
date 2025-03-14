<?php
namespace aliyun;
class asr{
	public static $api_key="sk-2b9";
	public static $model="";
    public static function init($cfg=[]){
		$arr=explode(",",$cfg["ali_api_key"]);
		shuffle($arr);
		$api_key=$arr[0];
        self::$api_key= $api_key;
		if(!empty($cfg["ali_asr_model"])){
			self::$model=$cfg["ali_asr_model"];
		}else{
			self::$model='qwen-audio-asr';
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
	
	
	public static function get($mp3url){	
		 
		 
		$ops=[
			 
			"model"=>self::$model,
            "input"=>[
                "messages"=>[
                    [
                        "role"=>"user",
                        "content"=>[
                            ["audio"=>$mp3url]
                        ]
                    ]
                ]
            ]
			 
			 
		];	
        
		$res=self::post_json("https://dashscope.aliyuncs.com/api/v1/services/aigc/multimodal-generation/generation",json_encode($ops),false);
		 
        $content=""; 
		$arr=json_decode($res,true);
         
		if(isset($arr["output"]["choices"][0]["message"]["content"][0]["text"])){
			$content=$arr["output"]["choices"][0]["message"]["content"][0]["text"];
		}else{
            print_r("出错");
			print_r($res);
		 
		} 
		
		return $content;
	}
	 
	
}

 
 
