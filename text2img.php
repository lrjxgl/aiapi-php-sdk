<?php
namespace AiApi;
class Text2Img{
	public static $class=""; 
	public static $cfg="";
	public static function init($cfg=[]){
		self::$cfg=$cfg;
	 
	 

		if(file_exists(__DIR__."/".$cfg["apicom"]."/text2img.php")){
			require_once __DIR__."/".$cfg["apicom"]."/text2img.php";
			self::$class=$cfg["apicom"]."\\text2img";
			self::$class::init($cfg);
		}else{
			require_once __DIR__."/glm/text2img.php";
			self::$class="glm\\text2img";
			self::$class::init($cfg);
		}
	}
	 
	
	public static function create($config){	
		$class=self::$class;
		
		return $class::create($config);
		 
		 
	}
	 
	
}
/*
$cfg=[];
Text2ImgAPi::init($cfg);
$config=[
	"prompt"=>"一只鸟在天上飞"
]; 
$imgurl=Text2ImgAPi::create($config);
echo $imgurl;
*/
 
