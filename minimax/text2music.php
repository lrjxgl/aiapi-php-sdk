<?php
namespace minimax;
class text2music{
	public static $api_key="sk-2b9";
	public static $model="";
    public static $baseurl="https://api.minimax.chat";
	public static function init($cfg=[]){
		$arr=explode(",",$cfg["minimax_api_key"]);
		shuffle($arr);
		$api_key=$arr[0];
        self::$api_key= $api_key;
		if(!empty($cfg["minimax_music_model"])){
			self::$model=$cfg["minimax_music_model"];
		}else{
			self::$model='music-01';
		} 
         
        
	}

    public static function music_upload($data){
        $url = self::$baseurl."/v1/music_upload";
        $api_key = self::$api_key;
        $file_name =basename($data["file"]);
        $file_path = $data["file"];

        $headers = [
            'Authorization: Bearer ' . $api_key,
        ];

        $payload = [
            'purpose' => 'song',
        ];
        $cfile = new \CURLFile($file_path, 'audio/mpeg', $file_name);
        $post_fields = array_merge($payload, ['file' => $cfile]);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $response = curl_exec($ch);
        $res=[];
        if (!curl_errno($ch)) {
             
            $arr=json_decode($response,true); 
             $res= [
                $arr["voice_id"],
                $arr["instrumental_id"]
             ]; 
        }

        curl_close($ch);
        return $res; 
    }

	public static function curl_get($url){
		$api_key=self::$api_key;
		$ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_HEADER, 0);
		 curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	 
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Bearer '.$api_key,
		));
		 $content= curl_exec($ch);
		 curl_close($ch);
		 return $content;
   } 
	public static function post_json($url, $json,$Async="enable"){
	
		$api_key=self::$api_key;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 	
		curl_setopt($ch, CURLOPT_POST, TRUE); 	
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json); 	
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 	
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 1);	
		$asyncStr="";	
		if($Async=='enable'){	
			$asyncStr='X-DashScope-Async: '.$Async;	
		}
	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(	
			'Content-Type: application/json',	
			'Content-Length: ' . strlen($json),	
			'Authorization: Bearer '.$api_key,
			$asyncStr
		));	
		$ret = curl_exec($ch);
		curl_close($ch);
		return $ret;	
	}
	public static function post_json_sse($url, $json,$callback)
	{
		$api_key=self::$api_key;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		curl_setopt($ch, CURLOPT_POST, TRUE); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($json),
			'Authorization: Bearer '.$api_key,
			'X-DashScope-SSE: enable'
		));
		curl_setopt($ch, CURLOPT_WRITEFUNCTION,$callback);
		$ret = curl_exec($ch);
	 
		curl_close($ch);
		return $ret;
	}
	
	
	public static function create($ops=[],$audio_setting=[]){	
		 
	
		$lyrics=$ops["lyrics"];
        $refer_voice="";
        if(!empty($ops["refer_voice"])){
            $refer_voice=$ops["refer_voice"];
        }
        $refer_instrumental="";
        if(!empty($ops["refer_instrumental"])){
            $refer_instrumental=$ops["refer_instrumental"];
        }
        $refer_vocal="";
        if(!empty($ops["refer_vocal"])){
            $refer_vocal=$ops["refer_vocal"];
        }
        $audio_setting=[
            "sample_rate"=> 44100,
            "bitrate"=> 256000,
            "format"=> "mp3"
        ];
        if(!empty($ops["audio_setting"])){
            $audio_setting=$ops["audio_setting"];
        }
		$data=[
			 
			"model"=>self::$model,
			"lyrics"=>$lyrics,
            "audio_setting"=>$audio_setting
		 
		];	
        if(!empty($refer_voice)){
            $data["refer_voice"]=$refer_voice;
        }
        if(!empty($refer_instrumental)){
            $data["refer_instrumental"]=$refer_instrumental;
        }
        if(!empty($refer_vocal)){
            $data["refer_vocal"]=$refer_vocal;
        }


        $json=json_encode($data);
       
		$res=self::post_json(self::$baseurl."/v1/music_generation",$json,false);
        
		 
		$arr=json_decode($res,true);
		if(!empty($arr["data"]["audio"])){
			return hex2bin($arr["data"]["audio"]);
		}else{
            print_r($res);
        }
		
		return "";
	}
	
	public static function checkTask($taskid){
		return true;
	}
	
}
 
 
