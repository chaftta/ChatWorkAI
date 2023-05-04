<?php

/**
 * ChatGPTヘルパークラス
 * @example
 * 		$gpt = new ChatGPT('xxx-api-key-xxx');
 * 		$message = 'hi, good GPT life!'
 * 		echo $message, "\n";
 * 		echo $gpt->send($message), "\n";
 */
class ChatGPT {
	/** @var string APIのURL */
	private const apiUrl = 'https://api.openai.com/v1/completions';
	/** @var string APIキー */
	private $apiKey;
	/** @var [] リクエストパラメータ */
	private $options = [
		'model'				=> "text-davinci-003",
		'temperature'		=> 0.9,
		'max_tokens'		=> 512,
		'top_p'				=> 0.5,
		'frequency_penalty'	=> 0.0,
		'presence_penalty'	=> 0.0,
		'stop'				=> '[" Human:", " AI:"]',
	];
	/** @var mixed エラーがある場合にエラーメッセージ */
	public $error;
	/**
	 * コンストラクタ
	 * @param string $apiKey APIキー
	 */
	public function __construct(string $apiKey) {
		$this->apiKey = $apiKey;
	}
	/**
	 * メッセージを送信する
	 * @param string $message メッセージ
	 * @return string GPTの応答メッセージ
	 */
	public function send(string $message): string {
		// リクエストのボディ

		$ch = curl_init();
		$headers  = [
			'Accept: application/json',
			'Content-Type: application/json',
			'Authorization: Bearer ' . $this->apiKey,
		];

		$postData =  $this->options;
		$postData['prompt'] = $message;

		curl_setopt($ch, CURLOPT_URL, self::apiUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($ch);
		if($response === false){
			$this->error = curl_error($ch);
			return false;
		}

		// レスポンス結果を文字列だけ取り出して返す
		$result = json_decode($response, true);
		return trim($result['choices'][0]['text']);
	}
}

