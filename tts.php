<?php
namespace AiApi;
class TTS
{
     
    public static $class=""; 
	public static $cfg="";
	 
	public static function init($cfg=[]){
		self::$cfg=$cfg; 
	 
        if(file_exists(__DIR__."/".$cfg["apicom"]."/tts.php")){
			require_once __DIR__."/".$cfg["apicom"]."/tts.php";
				self::$class=$cfg["apicom"]."\\tts";
				self::$class::init($cfg);
		}else{
			require_once __DIR__."/aliyun/tts.php";
			self::$class="aliyun\\tts";
			self::$class::init($cfg);
		}
	}

    /**
     * 生成语音文件
     * @param string $word 要合成的文本
     * @param string $output_file 输出文件路径
     */
    public static function get($word, $output_file)
    {
        $class=self::$class;	
		return $class::get($word, $output_file);
    }
}

 