<?php
namespace AiApi;
class text2music{
	public static $class=""; 
	public static $cfg="";
	public static function init($cfg=[]){
		self::$cfg=$cfg;
		if(file_exists(__DIR__."/".$cfg["apicom"]."/text2music.php")){
			require_once __DIR__."/".$cfg["apicom"]."/text2music.php";
			self::$class=$cfg["apicom"]."\\text2music";
		}else{
            require_once __DIR__."/minimax/text2music.php";
            self::$class=minimax."\\text2music";
        }
		self::$class::init($cfg);
	}
} 