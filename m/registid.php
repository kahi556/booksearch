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
$err_title = "会員本登録";

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
// 会員登録
//***********************************************
// ユーザID生成
//   ランダム文字列生成（文字コード利用）
//   a-z,A-Z,0-9 を使って30桁のランダム文字列を生成
for ($i = 0, $wk_user_id = null; $i < 30; ) {
	$num = mt_rand(0x30, 0x7A); // ASCII文字コード
	if ((0x30 <= $num && $num <= 0x39) || (0x41 <= $num && $num <= 0x5A)
	|| (0x61 <= $num && $num <= 0x7A)) {
		$wk_user_id .= chr($num); // 文字コードを文字に変換
		$i++;
	}
}
// ユーザ本登録
$sql = "INSERT INTO fg_user_table";
$sql.= " (user_id,login_id,password,nickname,birth,gender";
$sql.= ",mjob_cd,ljob_cd,rdate)";
$sql.= " SELECT '".$wk_user_id."'login_id,password,nickname,birth";
$sql.= ",gender,mjob_cd,ljob_cd,now()";
$sql.= " FROM fg_user_reg_table";
$sql.= " WHERE rkey = \"".$p_rkey."\"";
$ret = $obj->Execute($sql);
if (!$ret){
	$err = true;
	$msg_info.= ERR_REG."[fg_user_table]\n";
}

// ユーザ登録テーブル更新
$sql = "UPDATE fg_user_reg_table SET";
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
	require("template/err.html"); // エラー画面テンプレート呼び出し
	exit;
}else{
	$obj->Commit();
	$msg_info.= "登録しました。\n";
}

include 'template/registid.html';
?>
