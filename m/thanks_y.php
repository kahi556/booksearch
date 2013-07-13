<?php
//
// 「Thanks!」リンククリック
//

session_start();

// ログイン状態のチェック
if (!isset($_SESSION['login'])) {
	header("Location: login.php");
	exit;
}

// 変数初期化
$p_brno = "";
$p_user_id = "";
$time = date("Y-m-d H:i:s"); // 日時取得

//***********************************************
// 受信データをもとに変数の設定 POST
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_POST["book_review_no"])){$p_brno=htmlspecialchars($_POST["book_review_no"]);}
	if(isset($_POST["user_id"])){$p_user_id=htmlspecialchars($_POST["user_id"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_brno=preg_replace("/;/"," ",addslashes($p_brno));
	$p_user_id=preg_replace("/;/"," ",addslashes($p_user_id));
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
// サンクス履歴登録 既にデータが存在すれば置き換え
//***********************************************
$sql = "REPLACE INTO fg_thanks_history_table";
$sql.= " (book_review_no,user_id,rdate) VALUES";
$sql.= " (\"".$p_brno."\",\"".$_SESSION["user_id"]."\",\"".$time."\")";
$ret = $obj->Execute($sql);
if (!$ret){
	$err = true;
	$msg_info.= ERR_REG."[fg_book_review_table]\n";
}
//***********************************************
// 書評更新（サンクス）
//***********************************************
$sql = "UPDATE fg_book_review_table SET";
$sql.= " thanks_cnt = thanks_cnt + 1";
$sql.= " WHERE book_review_no = \"".$p_brno."\"";
$ret = $obj->Execute($sql);
if (!$ret){
	$err = true;
	$msg_info.= ERR_UPD."[fg_book_review_table]\n";
}
//***********************************************
// ユーザー更新（サンクス）
//***********************************************
$sql = "UPDATE fg_user_table SET";
$sql.= " thanks_cnt = thanks_cnt + 1";
$sql.= " WHERE user_id = \"".$p_user_id."\"";
$ret = $obj->Execute($sql);
if (!$ret){
	$err = true;
	$msg_info.= ERR_UPD."[fg_user_table]\n";
}

if ($err) {
	$obj->RollBack();
	echo $msg_info."更新に失敗しました";
}else{
	$obj->Commit();
	echo "<a href=\"javascript:cThanks_n('".$p_brno."','".$p_user_id."')\">Thanks!を取り消す</a>";
}

?>