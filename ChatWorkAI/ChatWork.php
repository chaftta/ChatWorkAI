<?php
/** チャットワークAPI */
class ChatWork {
	private $CHATWORK_TOKEN = "";
	/**
	 * コンストラクタ
	 * @param string $Token APIトークン
	 */
	public function __construct(string $Token){
		$this->CHATWORK_TOKEN = $Token;
	}
	/**
	 * チャットワークにメッセージを送信する
	 * @param int 		$RoomID		ルームID(URLでRIDの数字)
	 * @param string 	$Message	メッセージ
	 * @return string	レスポンス
	 */
	public function sendMessage(int $RoomID, string $Message): string {
		$API = "https://api.chatwork.com/v2/rooms/{$RoomID}/messages";
		$Headers = array("X-ChatWorkToken: ".$this->CHATWORK_TOKEN,);
		$Params = array("body" => $Message);

		$ch = curl_init($API);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $Params);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 0); // いつまでも待つ
		$res_data = curl_exec($ch);
		$res_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $res_data;
	}
	/**
	 * @param string	$Token 		APIトークン
	 * @param int		$RoomID 	ルームID(URLでRIDの数字)
	 * @param string	$Message	メッセージ
	 * @return string	レスポンス
	 */
	public static function send(string $Token, int $RoomID, string $Message): string {
		$Chat = new self($Token);
		return $Chat->sendMessage($RoomID, $Message);
	}
}
/** ChatWorkのWEB HOOKクラス */
class ChatWorkHook {
	/** @var string[] レスポンス用 */
	public const OK = ['status' => 'success'];
	/** @var array リクエストパラメータ */
	private $params;
	/** @var string Hookトークン */
	private $token;
	public function __construct(string $token) {
		$this->token = $token;
		// リクエストパラメータを取得する
		$body = file_get_contents('php://input');
		$this->params = json_decode($body, true);
	}
	/**
	 * 自分宛てのメンションを処理する
	 * @param Closure $func 自分宛てのメンション処理(パラメータ内容はChatWorkのwebhook参照)
	 * @return mixed 結果(ChatWorkのWebHook結果に返す値)
	 */
	public function mentionToMe(Closure $func) {
		// 自分宛て以外の場合(フック設定は自分宛てなので本来mention_to_me以外が来ることはないはず)
		if ($this->params['webhook_event_type'] !== 'mention_to_me')	return self::OK;
		// 有効なメッセージ本文が無い場合(データが壊れている以外はありえない)
		if (!isset($this->params['webhook_event']['body']))				return self::OK;
		// 自分宛てメンションのメッセージ処理
		$func($this->params['webhook_event']['body']);
		return self::OK;
	}

	/**
	 * 生のHookを実装する
	 * @param Closure $func 実装処理
	 * @return mixed 結果
	 */
	public function execute(Closure $func) {
		return $func($this->params);
	}
	/**
	 * @param string $message
	 * @return string
	 */
	public function removeFirstMention(string $message): string {
		$message = $this->toLF($message);
		// 最初の改行を探す
		$pos = strpos($message, "\n");
		// 改行がないのでそのままの文字列を使用する
		if ($pos === false) return $message;
		// 2行目の文字列を返す
		return substr($message, $pos + 1);
	}
	/**
	 * 改行コードをLFにする
	 * @param string $message 文字列
	 * @return string 改行コードがLFに統一された文字列
	 */
	public function toLF(string $message): string {
		$message = str_replace("\r\n", "\n", $message);
		$message = str_replace("\r", "\n", $message);
		return $message;
	}
}