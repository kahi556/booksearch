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
	
	$sql = "SELECT brt.book_review,brt.book_id,brt.tag,brt.thanks_cnt,brt.feeling";
	$sql.= ",bt.book_name,bt.imageurl";
	$sql.= ",at.author_name";
	$sql.= " FROM book_review_table brt";
	$sql.= " INNER JOIN book_table bt ON bt.book_id = brt.book_id";
	$sql.= " LEFT JOIN author_table at ON at.author_id = bt.author_id";
	$sql.= " WHERE brt.user_id = \"".$_SESSION["user_id"]."\"";
	$sql.= " AND brt.tag LIKE \"%".$p_word."%\"";
	$ret = $obj->Fetch($sql);
	if (count($ret) <> 0){
		$html.= "<div data-role=\"collapsible-set\" data-theme=\"e\" data-content-theme=\"d\">\n";
		foreach($ret as $key => $val){
			if ($key == 0) {
				$html.= "	<div data-role=\"collapsible\" data-collapsed=\"false\">\n";
			}else{
				$html.= "	<div data-role=\"collapsible\">\n";
			}
			$html.= "		<h3>".$val["book_name"]."[".$val["author_name"]."]</h3>\n";
			$html.= "		<a href=\"\">\n";
			$html.= "		<img src=\"".$val["imageurl"]."\"></a>\n";
			$html.= "		<p>Thanks: ".$val["thanks_cnt"]."</p>\n";
			$html.= "		<p>イメージスタンプ: <br />\n";
			$html.= "		<img src=\"images/".$val["feeling"].".gif\"</p>\n";
			$html.= "		<p>書評: ".$val["book_review"]."</p>\n";
			$html.= "	</div>\n";
		}
		$html.= "</div>\n";
	}
}

include 'template/sbook.html';
?>