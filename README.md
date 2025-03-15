# aiapi-php-sdk 
  一个统一多个AI平台接口的sdk,支持文本生成、图片生成、视频生成、语音生成、音乐生成、向量转化，文件读取等各种接口。  
  
  官网交流： https://www.haicms.com/mm/ask/index  

# 使用方式
```
<?php

  require "aiapi/text2text.php";
  aiapi\text2text::init($cfg);
  $prompt = "你是谁";
  $history = [];
  $content = aiapi\text2text::chat($prompt,$history);

 ?> 
	
```
	