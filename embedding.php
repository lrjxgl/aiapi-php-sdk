<?php
namespace AiApi; 
class embedding{
    public static $class="";
	public static $cfg="";
	public static function init($cfg=[]){
		self::$cfg=$cfg;
		

		if(file_exists(__DIR__."/".$cfg["apicom"]."/embedding.php")){
			require_once __DIR__."/".$cfg["apicom"]."/embedding.php";
			self::$class=$cfg["apicom"]."\\embedding";
			self::$class::init($cfg);
			 
		}else{
			require_once __DIR__."/glm/embedding.php"; 
			$class="glm\\embedding";
			self::$class=$class;
			$class::init($cfg);
		}
	}

	public static function batch($data){
		$class=self::$class;
        return  $class::batch($data);
	}

    public static function single($content){
		$class=self::$class;
        return  $class::single($content);
	}

}