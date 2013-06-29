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
$p_book_review = "";
$p_isbn = "";
$p_feeling = "";
$p_author_name = "";
$p_wk = "";
$p_word = "";
$html = "";
$wk_keyword  = "";
$feeling_image = "";

require("common/conf.php"); // 共通定義

//***********************************************
// 受信データをもとに変数の設定 GET
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "GET") { 
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_GET["wk"])){$p_wk=htmlspecialchars($_GET["wk"]);}
	if(isset($_GET["wd"])){$p_word=htmlspecialchars($_GET["wd"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_wk=preg_replace("/;/"," ",addslashes($p_wk));
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
	
	$sql = "SELECT brt.book_review,brt.book_id";
	$sql.= ",brt.tag,brt.thanks_cnt,brt.feeling";
	$sql.= ",bt.book_name,bt.imageurl";
	$sql.= ",at.author_name";
	$sql.= " FROM book_review_table brt";
	$sql.= " INNER JOIN book_table bt ON bt.book_id = brt.book_id";
	$sql.= " LEFT JOIN author_table at ON at.author_id = bt.author_id";
	if ($p_wk == "f") {
		// 気分検索
		$sql.= " WHERE brt.feeling = \"".$p_word."\"";
	}else{
		$sql.= " WHERE brt.tag LIKE \"%".$p_word."%\"";
	}
	if (isset($_SESSION['login'])) {
		// ログイン中のみの条件
		$sql.= " AND brt.user_id = \"".$_SESSION["user_id"]."\"";
	}
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
}else{
	// 気分画像編集
	foreach ($arr_feeling as $key => $val) {
        $html_image.= "			";
        $html_image.= "<div class=\"item\">";
		$html_image.= "<a rel=\"external\" href=\"sbook.php?wk=f&wd=".$key."#page2\">\n";
        $html_image.= "			";
		$html_image.= "<img src=\"images/".$key.".gif\"></a></div>\n";
	}
}

include 'template/sbook.html';
?>