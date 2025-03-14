<?php
namespace glm;
class file2text{
	public static $api_key="sk-2b9";
	public static $model="";
	public static $baseUrl = "https://open.bigmodel.cn/api/paas/v4";
	public static function init($cfg=[]){
		$arr=explode(",",$cfg["glm_api_key"]);
		shuffle($arr);
		$api_key=$arr[0];
	    self::$api_key= $api_key;

	}
	
	public static function upload($filePath){
		$uploadUrl = self::$baseUrl . "/files";		
		$headers = [
		    'Authorization: Bearer ' . self::$api_key,
		];		
		$postFields = [
		    'purpose' => 'file-extract',
		    'file' => new \CURLFile($filePath, mime_content_type($filePath), basename($filePath))
		];	
		$ch = curl_init();
		curl_setopt_array($ch, [
		    CURLOPT_URL => $uploadUrl,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_HTTPHEADER => $headers,
			CURLOPT_SSL_VERIFYPEER=>false,
		    CURLOPT_POST => true,
		    CURLOPT_POSTFIELDS => $postFields,
		]);
		 
		$response = curl_exec($ch);
		 
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($httpCode !== 200) {
		    return [
				"error"=>1,
				"message"=>"网络出错"
			];
		}
		
		$fileObject = json_decode($response, true);
		$fileId = $fileObject['id'] ?? null;
		if (!$fileId) {
		    return [
		    	"error"=>2,
		    	"message"=>"无法取得ID"
		    ];
		}
		return [
			"error"=>0,
			"message"=>"上传成功",
			"fileId"=>$fileId
		];
	}
	
	public static function getContent($file){
		$res=self::upload($file);
		if(!isset($res["fileId"])){
			return [
				"error"=>1,
				"message"=>"文件上传失败"
			];
		} 
		$fileId=$res["fileId"];
		$headers = [
		    'Authorization: Bearer ' . self::$api_key,
		];
		$contentUrl = self::$baseUrl . "/files/" . $fileId . "/content";
		
		$ch = curl_init();
		curl_setopt_array($ch, [
		    CURLOPT_URL => $contentUrl,
		    CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER=>false,
		    CURLOPT_HTTPHEADER => $headers,
		]);
		
		$res = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($httpCode !== 200) {
		    return [
		    	"error"=>2,
		    	"message"=>"文件解析失败"
		    ];
		}
		$arr=json_decode($res,true);
		if(!isset($arr["content"])){
			return [
				"error"=>2,
				"message"=>"文件读取失败"
			];
		}
		return [
			"error"=>0,
			"message"=>"读取成功",
			"content"=>$arr["content"]
		];
	}
}