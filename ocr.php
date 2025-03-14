<?php
namespace AiApi;
class ocr{
	public static $class=""; 
	public static $cfg="";
	 
	public static function init($cfg=[]){
		self::$cfg=$cfg; 
	 

		if(file_exists(__DIR__."/".$cfg["apicom"]."/ocr.php")){
			require_once __DIR__."/".$cfg["apicom"]."/ocr.php";
				self::$class=$cfg["apicom"]."\\ocr";
				self::$class::init($cfg);
		}else{
			require_once __DIR__."/aliyun/ocr.php";
				self::$class="aliyun\\ocr";
				self::$class::init($cfg);
		}
	}
	 
	
	
	public static function get($imgurl){	
		
		$class=self::$class;	
		return $class::get($imgurl);
	}
	 
	
}

 
 
