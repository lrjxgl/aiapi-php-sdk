<?php
namespace AiApi;
//知识库
class zskApi{
    public static $class=""; 
	public static $cfg="";
	public static function init($cfg=[]){
		self::$cfg=$cfg;
		require_once __DIR__."/glm/zsk.php"; 
		$class="glm\\zsk";
		self::$class=$class;
		$class::init($cfg);
	}

	public static function emb_list(){
		$class=self::$class;
		return $class::emb_list();
	}

    public static function ku_list(){
        $class=self::$class;
        return $class::ku_list();
    }

	public static function ku_create($data){
		$class=self::$class;
		return $class::ku_create($data);
	}

	public static function ku_update($id,$data){
		$class=self::$class;
		return $class::ku_update($data);
	}

}


