<?php
namespace glm;
class zsk{
    public static $api_key;
    public static $baseurl='https://open.bigmodel.cn/api/llm-application/open';
    public static function init($cfg){
        $arr=explode(",",$cfg["glm_api_key"]);
		shuffle($arr);
		$api_key=$arr[0];
        self::$api_key= $api_key;
		if(!empty($cfg["glm_text2video_model"])){
			self::$model=$cfg["glm_text2video_model"];
		}else{
			self::$model='cogvideox-flash';
		} 
    }
	public static function emb_list(){
        $url=self::$baseurl."/embedding";
        $res=self::curl_get($url);
        return json_decode($res,true);
    }

    public static function ku_list($page=1,$size=10){
        $url=self::$baseurl."/knowledge?page={$page}&size={$size}";
        $res=self::curl_get($url);
        return json_decode($res,true);
    }
	public static function ku_get($id){
        $url=self::$baseurl."knowledge/{$id}";
        $res=self::curl_get($url);
        return json_decode($res,true);
    }

	public static function ku_delete($id){
        $url=self::$baseurl."/knowledge/".$id;
        $res=self::curl_delete($url);
        return json_decode($res,true);
    }
    public static function ku_create($data){
        $url=self::$baseurl."/knowledge";
        $res=self::post_json($url,$data);
        return json_decode($res,true);
    }

	public static function ku_update($id,$data){
        $url=self::$baseurl."/knowledge/".$id;
        $res=self::post_json($url,$data);
        return json_decode($res,true);
    }


	public static function document_list($kid,$page=1,$size=10){
        $url=self::$baseurl."document?knowledge_id={$kid}&page={$page}&size={$size}";
        $res=self::curl_get($url);
        return json_decode($res,true);
    }

	public static function document_get($id){
        $url=self::$baseurl."document/{$id}";
        $res=self::curl_get($url);
        return json_decode($res,true);
    }
    public static function document_create($kid,$data){
        $url=self::$baseurl."/document/upload_document/{$kid}";
        $res=self::post_json($url,$data);
        return json_decode($res,true);
    }

	public static function document_update($id,$data){
        $url=self::$baseurl."/document/".$id;
        $res=self::post_json($url,$data);
        return json_decode($res,true);
    }

	public static function document_delete($id){
        $url=self::$baseurl."/document/{$id}";
        $res=self::curl_delete($url);
        return json_decode($res,true);
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

   public static function curl_delete($url){
	$api_key=self::$api_key;
	$ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_HEADER, 0);
	 curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
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

}
?>