<?php
//
// ログイン後トップページ
//

session_start();

// ログイン状態のチェック(ログイン未ならログインページ)
if (!isset($_SESSION['login'])) {
	header("Location: login.php");
	exit;
}

// 変数初期化
$arr_linkurl = array();
$arr_imageurl = array();
$arr_title = array();
$arr_author = array();
$p_nickname = "";
$p_review_posts_cnt = "";
$p_thanks_cnt = "";
$p_keyword = "";
$p_book_name = "";
$p_isbn = "";
$p_book_review = "";
$p_feeling = "";
$p_author_name = "";

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
$sql.= " WHERE user_id = \"".$_SESSION["user_id"]."\"";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		$p_nickname = $val["nickname"];
		$p_review_posts_cnt = $val["review_posts_cnt"];
		$p_thanks_cnt = $val["thanks_cnt"];
		$p_keyword = $val["keyword"];
	}
}

//***********************************************
// 書評を登録した本検索
//***********************************************
$sql = "SELECT bt.book_name,bt.isbn";
$sql.= ",brt.book_review,brt.tag,brt.feeling,at.author_name";
$sql.= " FROM book_review_table brt";
$sql.= " INNER JOIN book_table bt ON bt.book_id = brt.book_id";
$sql.= " LEFT JOIN author_table at ON at.author_id = bt.author_id";
$sql.= " WHERE brt.user_id = \"".$_SESSION["user_id"]."\"";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		$p_book_name = $val["book_name"];
		$p_isbn = $val["isbn"];
		$p_book_review = $val["book_review"];
		$p_feeling = $val["feeling"];
		$p_author_name = $val["author_name"];
	}
}



include 'template/top.html';
?>
