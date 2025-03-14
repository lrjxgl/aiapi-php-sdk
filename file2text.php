<?php
namespace aiapi;
class file2text{
	public static $class="";
	public static $cfg="";
	public static function init($cfg=[]){
		self::$cfg=$cfg;
	 
	
		if(file_exists(__DIR__."/".$cfg["apicom"]."/file2text.php")){
			require_once __DIR__."/".$cfg["apicom"]."/file2text.php";
			self::$class=$cfg["apicom"]."\\file2text";
			self::$class::init($cfg);
			 
		}else{
			require_once __DIR__."/glm/file2text.php";
			self::$class="glm\\file2text";
			self::$class::init($cfg);
		}
		
	}
	
	public static function upload($file){
		return self::$class::upload($file);
	}
	
	public static function getContent($file){
		return self::$class::getContent($file);
	}
	
}