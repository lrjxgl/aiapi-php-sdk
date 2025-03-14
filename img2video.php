<?php
namespace AiApi;
class Img2Video{
	public static $class=""; 
	public static $cfg="";
	public static function init($cfg=[]){
		self::$cfg=$cfg;
		if(file_exists(__DIR__."/".$cfg["apicom"]."/img2video.php")){
			require_once __DIR__."/".$cfg["apicom"]."/img2video.php";
				self::$class=$cfg["apicom"]."\\img2video";
				self::$class::init($cfg);
		}else{
			require_once __DIR__."/glm/img2video.php";
			self::$class="glm\\img2video";
			self::$class::init($cfg);
		}
	}
	 
	
	public static function create($config){	
		$class=self::$class;
		
		return $class::create($config);
		 
		 
	}

    public static function checkTask($taskid){
		 $class=self::$class;
         return $class::checkTask($taskid);
		 
	}
	 
	
}

 
 
