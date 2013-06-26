<?php
//
// 本を検索
//

session_start();

// ログイン状態のチェック
//if (!isset($_SESSION['login'])) {
//	header("Location: login.php?m=sb");
//	exit;
//}

// 変数初期化
$arr_linkurl = array();
$arr_isbn = array();
$arr_imageurl = array();
$arr_title = array();
$arr_author_name = array();
$arr_book_review = array();
$arr_feeling = array();
$arr_keyword = array();
$p_nickname = "";
$p_review_posts_cnt = "";
$p_thanks_cnt = "";
$p_tag = "";
$p_book_name = "";
$p_isbn = "";
$p_book_review = "";
$p_feeling = "";
$p_word = "";
$p_author_name = "";
$html = "";
$wk_keyword  = "";

//***********************************************
// 受信データをもとに変数の設定 GET
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "GET") { 
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_GET["w"])){$p_word=htmlspecialchars($_GET["w"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_word=preg_replace("/;/"," ",addslashes($p_word));
}
if ($p_word <> "") {
	$msg_info = "不正な処理です。";
	require("err.html"); // エラー画面テンプレート呼び出し
	exit;
}

//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();
mysql_set_charset('utf8');

//***********************************************
// キーワード検索(ログインユーザー)
//***********************************************
if ($p_word <> "") {
	$p_word = urldecode($p_word);
	
	$sql = "SELECT brt.thanks_cnt,brt.tag,brt.feeling,brt.thanks_cnt";
	$sql.= ",bt.isbn";
	$sql.= " FROM book_review_table brt";
	$sql.= " INNER JOIN book_table bt ON bt.book_id = brt.book_id";
	$sql.= " WHERE brt.user_id = \"".$_SESSION["user_id"]."\"";
	$sql.= " AND brt.tag LIKE \"%".$p_word."%\"";
	$ret = $obj->Fetch($sql);
	if (count($ret) <> 0){
		require("amazonaws.php");
		foreach($ret as $key => $val){
			// イメージ
			$wk_feeling_image = "<img src=\"images/".$val["feeling"].".gif\"";
			// amazon情報取得
			$recom_books_keyw = "";
			$recom_books_isbn = $val["isbn"];
			$isbn10 = ISBNTran( $recom_books_isbn );
			$data = amazon_info($recom_books_keyw, $isbn10);
			$wk_title = $data[0]->Title;
			$wk_author = $data[0]->Author;
			$wk_imageurl = $data[0]->ImageURL;
			$wk_linkurl = $data[0]->DetailPageURL;
		}
	}
}

include 'template/sbook.html';
?>