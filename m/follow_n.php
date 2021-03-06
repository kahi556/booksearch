<?php
//
// 「フォローを取り消す」リンククリック
//

session_start();

// ログイン状態のチェック
if (!isset($_SESSION['login'])) {
	header("Location: login?");
	exit;
}

// 変数初期化
$p_brno = "";
$p_f_user_id = "";

//***********************************************
// 受信データをもとに変数の設定 POST
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_POST["book_review_no"])){$p_brno=htmlspecialchars($_POST["book_review_no"]);}
	if(isset($_POST["follow_user_id"])){$p_f_user_id=htmlspecialchars($_POST["follow_user_id"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_brno=preg_replace("/;/"," ",addslashes($p_brno));
	$p_f_user_id=preg_replace("/;/"," ",addslashes($p_f_user_id));
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
// フォロー更新 削除フラグを立てる
//***********************************************
$sql = "UPDATE fg_follow_table SET";
$sql.= " delete_flg = 1";
$sql.= " WHERE follow_user_id = \"".$p_f_user_id."\"";
$sql.= " AND user_id = \"".$_SESSION["user_id"]."\"";
$ret = $obj->Execute($sql);
if (!$ret){
	$err = true;
	$msg_info.= ERR_UPD."[fg_follow_table]\n";
}
//***********************************************
// ユーザー更新（非フォロー）減算
//***********************************************
$sql = "UPDATE fg_user_table SET";
$sql.= " follower_cnt = follower_cnt - 1";
$sql.= " WHERE user_id = \"".$p_f_user_id."\"";
$ret = $obj->Execute($sql);
if (!$ret){
	$err = true;
	$msg_info.= ERR_UPD."[fg_user_table]\n";
}
//***********************************************
// ユーザー更新（フォロー）減算
//***********************************************
$sql = "UPDATE fg_user_table SET";
$sql.= " follow_cnt = follow_cnt - 1";
$sql.= " WHERE user_id = \"".$_SESSION["user_id"]."\"";
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
	echo "<a href=\"javascript:cFollow_y('".$p_f_user_id."')\">フォロー</a>";
}

?>