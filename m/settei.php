<?php
//
// 設定
//

session_start();

// ログイン状態のチェック(ログイン未ならログインページ)
if (!isset($_SESSION['login'])) {
	header("Location: login.php");
	exit;
}

//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();
mysql_set_charset('utf8');

//***********************************************
// ユーザー情報検索
//***********************************************
$sql = "SELECT *";
$sql.= " FROM user_table";
$sql.= " WHERE user_id = \"".$_SESSION['user_id']."\"";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		$p_book_id = $val["book_id"];
	}
}

include 'template/settei.html';
?>
