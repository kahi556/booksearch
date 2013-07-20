<?php
//
// メールアドレス変更
//  仮登録メール内のリンクをクリック後の処理
//  本登録を行う
//

// 変数初期化
$chk_msg = "";
$msg_info = "";
$p_rkey = "";
$wk_user_id = "";
$wk_login_id = "";
$time = date("Y-m-d H:i:s"); // 日時取得
$err_title = "メールアドレス変更";

require("common/conf.php"); // 共通定義

//***********************************************
// 受信データをもとに変数の設定 GET
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "GET") {
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_GET["rkey"])){$p_rkey=htmlspecialchars($_GET["rkey"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_rkey=preg_replace("/;/"," ",addslashes($p_rkey));
}

//***********************************************
// URLチェック
//***********************************************
if ($p_rkey == "") {
	$msg_info = "URLが正しくありません。";
	require("template/err.html"); // エラー画面テンプレート呼び出し
	exit;
}

//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();

//***********************************************
// トランザクション開始
//***********************************************
$obj->BeginTran();

//***********************************************
// メールアドレス変更
//***********************************************

// 仮登録内容検索
$sql = " SELECT user_id,login_id";
$sql.= " FROM fg_user_reg_table";
$sql.= " WHERE rkey = \"".$p_rkey."\"";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		$wk_user_id = $val["user_id"];
		$wk_login_id = $val["login_id"];
	}
}

// ユーザ本登録
$sql = "UPDATE fg_user_table SET";
$sql.= " login_id = \"".$wk_login_id."\"";
$sql.= " WHERE user_id = \"".$wk_user_id."\"";
$ret = $obj->Execute($sql);
if (!$ret){
	$err = true;
	$msg_info.= ERR_UPD."[fg_user_table]\n";
}

// ユーザ登録テーブル更新
$sql = "UPDATE fg_user_reg_table SET";
$sql.= " status = 1";
$sql.= " WHERE rkey = \"".$p_rkey."\"";
$ret = $obj->Execute($sql);
if (!$ret){
	$err = true;
	$msg_info.= ERR_UPD."[fg_user_reg_table]\n";
}

if ($err) {
	$obj->RollBack();
	$msg_info.= "URLが正しくありません。";
	$msg_info.= "更新に失敗しました。\n";
	require("template/err.html"); // エラー画面テンプレート呼び出し
	exit;
}else{
	$obj->Commit();
	$msg_info.= "更新しました。\n";
}

include 'template/registm.html';
?>
