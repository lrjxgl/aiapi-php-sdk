<?php
namespace aliyun;
require_once dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';

use Ratchet\Client\Connector;
use React\EventLoop\Loop;
use React\Socket\Connector as SocketConnector;

# 若没有将API Key配置到环境变量，可将下行替换为：$api_key="your_api_key"。不建议在生产环境中直接将API Key硬编码到代码中，以减少API Key泄露风险。
 

class TTS
{
    private static $api_key;
    public static $model;
    private static $websocket_url = 'wss://dashscope.aliyuncs.com/api-ws/v1/inference/'; // WebSocket服务器地址

    public static function init($cfg)
    {
        $arr=explode(",",$cfg["ali_api_key"]);
		shuffle($arr);
		$api_key=$arr[0];
        self::$api_key= $api_key;
		if(!empty($cfg["ali_tts_model"])){
			self::$model=$cfg["ali_tts_model"];
		}else{
			self::$model='cosyvoice-v1';
		}
    }

    /**
     * 生成语音文件
     * @param string $word 要合成的文本
     * @param string $output_file 输出文件路径
     */
    public static function get($word, $output_file)
    {
        if (!self::$api_key) {
            throw new \Exception("API Key未初始化，请先调用init()方法设置API Key");
        }
        
        $loop = Loop::get();

        if (file_exists($output_file)) {
            file_put_contents($output_file, ''); // 清空文件内容
        }

        $socketConnector = new SocketConnector($loop, [
            'tcp' => [
                'bindto' => '0.0.0.0:0',
            ],
            'tls' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $connector = new Connector($loop, $socketConnector);

        $headers = [
            'Authorization' => 'bearer ' . self::$api_key,
            'X-DashScope-DataInspection' => 'enable'
        ];

        $connector(self::$websocket_url, [], $headers)->then(
            function ($conn) use ($loop, $output_file, $word) {
               // echo "连接到WebSocket服务器\n";

                $taskId = self::generateTaskId();
                self::sendRunTaskMessage($conn, $taskId);

                $sendContinueTask = function () use ($conn, $loop, $taskId, $word) {
                    $continueTaskMessage = json_encode([
                        "header" => [
                            "action" => "continue-task",
                            "task_id" => $taskId,
                            "streaming" => "duplex"
                        ],
                        "payload" => [
                            "input" => [
                                "text" => $word
                            ]
                        ]
                    ]);
                    //echo "准备发送continue-task指令: " . $continueTaskMessage . "\n";
                    $conn->send($continueTaskMessage);
                    //echo "continue-task指令已发送\n";

                    self::sendFinishTaskMessage($conn, $taskId);
                };

                $taskStarted = false;

                $conn->on('message', function ($msg) use ($conn, $sendContinueTask, $loop, &$taskStarted, $taskId, $output_file) {
                    if ($msg->isBinary()) {
                        file_put_contents($output_file, $msg->getPayload(), FILE_APPEND);
                    } else {
                        $response = json_decode($msg, true);
                        if (isset($response['header']['event'])) {
                            self::handleEvent($conn, $response, $sendContinueTask, $loop, $taskId, $taskStarted);
                        } else {
                            //echo "未知的消息格式\n";
                        }
                    }
                });

                $conn->on('close', function ($code = null, $reason = null) {
                   // echo "连接已关闭\n";
                    if ($code !== null) {
                       // echo "关闭代码: " . $code . "\n";
                    }
                    if ($reason !== null) {
                       // echo "关闭原因：" . $reason . "\n";
                    }
                });
            },
            function ($e) {
                //echo "无法连接：{$e->getMessage()}\n";
            }
        );

        $loop->run();
    }

    private static function generateTaskId(): string
    {
        return bin2hex(random_bytes(16));
    }

    private static function sendRunTaskMessage($conn, $taskId)
    {
        $runTaskMessage = json_encode([
            "header" => [
                "action" => "run-task",
                "task_id" => $taskId,
                "streaming" => "duplex"
            ],
            "payload" => [
                "task_group" => "audio",
                "task" => "tts",
                "function" => "SpeechSynthesizer",
                "model" => self::$model,
                "parameters" => [
                    "text_type" => "PlainText",
                    "voice" => "longxiaochun",
                    "format" => "mp3",
                    "sample_rate" => 22050,
                    "volume" => 50,
                    "rate" => 1,
                    "pitch" => 1
                ],
                "input" => (object)[]
            ]
        ]);
        //echo "准备发送run-task指令: " . $runTaskMessage . "\n";
        $conn->send($runTaskMessage);
       // echo "run-task指令已发送\n";
    }

    private static function sendFinishTaskMessage($conn, $taskId)
    {
        $finishTaskMessage = json_encode([
            "header" => [
                "action" => "finish-task",
                "task_id" => $taskId,
                "streaming" => "duplex"
            ],
            "payload" => [
                "input" => (object)[]
            ]
        ]);
        //echo "准备发送finish-task指令: " . $finishTaskMessage . "\n";
        $conn->send($finishTaskMessage);
        //echo "finish-task指令已发送\n";
    }

    private static function handleEvent($conn, $response, $sendContinueTask, $loop, $taskId, &$taskStarted)
    {
        switch ($response['header']['event']) {
            case 'task-started':
                //echo "任务开始，发送continue-task指令...\n";
                $taskStarted = true;
                $sendContinueTask();
                break;
            case 'result-generated':
                // 忽略result-generated事件
                break;
            case 'task-finished':
               // echo "任务完成\n";
                $conn->close();
                break;
            case 'task-failed':
               // echo "任务失败\n";
                //echo "错误代码：" . $response['header']['error_code'] . "\n";
                echo "错误信息：" . $response['header']['error_message'] . "\n";
                $conn->close();
                break;
            case 'error':
                echo "错误：" . $response['payload']['message'] . "\n";
                break;
            default:
                //echo "未知事件：" . $response['header']['event'] . "\n";
                break;
        }

        if ($response['header']['event'] == 'task-finished') {
            $loop->addTimer(1, function () use ($conn) {
                $conn->close();
                //echo "客户端关闭连接\n";
            });
        }

        if (!$taskStarted && in_array($response['header']['event'], ['task-failed', 'error'])) {
            $conn->close();
        }
    }
}

/*

// 使用示例
TTS::init(DASHSCOPE_API_KEY); // 初始化API Key
TTS::get("你是谁呢？这么厉害", "a.mp3"); // 生成语音文件
*/