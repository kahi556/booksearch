<?php
//
// 新規登録
//  仮登録メール内のリンクをクリック後の処理
//  本登録を行う
//

// 変数初期化
$chk_msg = "";
$msg_info = "";
$p_rkey = "";
$time = date("Y-m-d H:i:s"); // 日時取得

require("common/conf.php"); // 共通定義

//***********************************************
// 受信データをもとに変数の設定 POST
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_POST["rkey"])){$p_rkey=htmlspecialchars($_POST["rkey"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_rkey=preg_replace("/;/"," ",addslashes($p_rkey));
}

//***********************************************
// URLチェック
//***********************************************
if ($p_rkey == "") {
	$err_msg = "URLが正しくありません。";
	require("err.html"); // エラー画面テンプレート呼び出し
	exit;
}

//***********************************************
// トランザクション開始
//***********************************************
$obj->BeginTran();

//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();

//***********************************************
// 会員登録
//***********************************************
// ユーザ本登録
$sql = "INSERT INTO fg_user_table";
$sql.= " (user_id,password,nickname,birth,gender";
$sql.= ",mjob_cd,ljob_cd,rdate)";
$sql.= " SELECT user_id,password,nickname,birth";
$sql.= ",gender,mjob_cd,ljob_cd,now()";
$sql.= " FROM fg_user_regist_table";
$sql.= " WHERE rkey = \"".$p_rkey."\"";
$ret = $obj->Execute($sql);
if (!$ret){
	$err = true;
	$msg_info.= ERR_REG."[fg_user_table]\n";
}

// ユーザ登録テーブル更新
$sql = "UPDATE fg_user_regist_table SET";
$sql.= " status = 1";
$sql.= " WHERE rkey = \"".$p_rkey."\"";
$ret = $obj->Execute($sql);
if (!$ret){
	$err = true;
	$msg_info.= ERR_UPD."[fg_user_regist_table]\n";
}

if ($err) {
	$obj->RollBack();
	$msg_info.= "URLが正しくありません。";
	$msg_info.= "登録に失敗しました。\n";
	require("err.html"); // エラー画面テンプレート呼び出し
	exit;
}else{
	$obj->Commit();
	$msg_info.= "登録しました。\n";
}

include 'template/registid.html';
?>
