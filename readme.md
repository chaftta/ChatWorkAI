# 概要

このプログラムは、チャットワークでGPTのBOTを動かすものです。

## 準備

1. チャットワークにBOT用のアカウントを準備する
1. ChatWorkAI/*.phpをWEBサーバーに配置する
1. 配置したURLをチャットワークのWebHookとして設定する
1. ChatGPTのアカウントを準備する
1. ChatGPTのAPIトークンを準備する
1. 配置しているindex.phpの定数に上記で準備した値を設定する

## 使い方

1. 準備したBOTアカウントをチャットルームに参加させる
1. BOT宛てにメッセージを出す
1. BOTが回答してくれる

### 例

私「  
[TO]BOT君  
今日の天気を教えてください  
」  

BOT「  
申し訳ありませんが、私は情報を持っていません。私はテキストベースの情報処理ができるAI言語モデルですので、現在の天気に関する情報は持っていません。天気に関する情報を知りたい場合は、気象庁や天気予報サイトなどを参照してください。  
」  