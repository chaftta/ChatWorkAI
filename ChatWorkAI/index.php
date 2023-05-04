<?php
include_once __DIR__.'/ChatGPT.php';
include_once __DIR__.'/ChatWork.php';

/** ChatWorkのAPIトークン */
const CW_API_TOKEN	= '';
/** ChatWorkのWEBHOOKトークン */
const CW_HOOK_TOKEN = '';
/** ChatWorkのルームID */
const CHAT_WORK_ROOM_ID = 0;
/** GPTのAPIキー */
const GPT_API_KEY = '';

$hook = new ChatWorkHook(CW_HOOK_TOKEN);
$result = $hook->mentionToMe(
	function(string $msg) use($hook) {
		// メンションの行を除外
		$msg = $hook->removeFirstMention($msg);
		// GPTに問い合わせ
		$gpt = new ChatGPT(GPT_API_KEY);
		$msg = $gpt->send($msg);
		// 結果をChatWorkに送信
		$cw  = new ChatWork(CW_API_TOKEN);
		$cw->sendMessage(CHAT_WORK_ROOM_ID, $msg);
	}
);
// 処理結果をクライアントに返す
header('Content-Type: application/json');
echo json_encode($result);



