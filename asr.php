<?php
namespace AiApi;
class asr{
	public static $class=""; 
	public static $cfg="";
	 
	public static function init($cfg=[]){
		self::$cfg=$cfg; 
		 
		if(file_exists(__DIR__."/".$cfg["apicom"]."/asr.php")){
			require_once __DIR__."/".$cfg["apicom"]."/asr.php";
			self::$class=$cfg["apicom"]."\\asr";
			self::$class::init($cfg);
			 
		}else{
			require_once __DIR__."/aliyun/asr.php";
			self::$class="aliyun\\asr";
			self::$class::init($cfg);
		}
	}
	 
	
	
	public static function get($mp3url){	
		 
		$class=self::$class;	
		return $class::get($mp3url);
		
	}
	 
	
}

 
 
