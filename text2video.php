<?php
namespace AiApi;
class Text2Video{
	public static $class=""; 
	public static $cfg="";
	public static function init($cfg=[]){
		self::$cfg=$cfg;
		if(file_exists(__DIR__."/".$cfg["apicom"]."/text2video.php")){
			require_once __DIR__."/".$cfg["apicom"]."/text2video.php";
				self::$class=$cfg["apicom"]."\\text2video";
				self::$class::init($cfg);
		}else{
			require_once __DIR__."/glm/text2video.php";
			self::$class="glm\\text2video";
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
/* 
$cfg=[];
Text2VideoAPi::init($cfg);
$config=[
	"prompt"=>"一只鸟在天上飞"
]; 

$taskid=Text2VideoAPi::create($config);
//$taskid='7563331545918512917-8960013334131869772';
echo $taskid;
echo date("Y-m-d H:i:s")."\n";
while(true){
    echo "发起请求\n";
    $data=Text2VideoAPi::checkTask($taskid);
    if(!empty($data)){
        break;
    }
    sleep(3);
}
echo date("Y-m-d H:i:s")."\n";

print_r($data);
*/
 
 
