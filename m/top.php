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
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();
mysql_set_charset('utf8');

//***********************************************
// ユーザー情報検索(ログインユーザー)
//***********************************************
//$sql = "SELECT nickname,review_posts_cnt,thanks_cnt,tag";
$sql = "SELECT nickname,review_posts_cnt,thanks_cnt";
$sql.= " FROM user_table";
$sql.= " WHERE user_id = \"".$_SESSION["user_id"]."\"";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		$p_nickname = $val["nickname"]." さん";
		$p_review_posts_cnt = $val["review_posts_cnt"];
		$p_thanks_cnt = $val["thanks_cnt"];
		//$p_tag = $val["tag"];
	}
}
// タグをキーワードとして各リンクに分割
//if ($p_tag <> "") {
//	$arr_keyword = explode(",", $p_tag);
//	foreach($arr_keyword as $key => $val){
//		$wk_keyword.= "<a href=\"sbook.php#page2?w=".urlencode($val)."\">".$val."</a> ";
//	}
//}

//***********************************************
// 書評を登録した本検索(ログインユーザー)
//***********************************************
$sql = "SELECT brt.book_id,brt.tag,brt.thanks_cnt";
$sql.= ",bt.book_name,bt.imageurl";
$sql.= ",at.author_name";
$sql.= " FROM book_review_table brt";
$sql.= " INNER JOIN book_table bt ON bt.book_id = brt.book_id";
$sql.= " LEFT JOIN author_table at ON at.author_id = bt.author_id";
$sql.= " WHERE brt.user_id = \"".$_SESSION["user_id"]."\"";
$sql.= " ORDER BY brt.book_review_no DESC";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		//$arr_title = $val["book_name"];
		//$arr_isbn = $val["isbn"];
		//$arr_imageurl = $val["imageurl"];
		//$arr_book_review = $val["book_review"];
		//$arr_feeling = $val["feeling"];
		//$arr_author_name = $val["author_name"];
		// タグをキーワードとして各リンクに分割
		if ($val["tag"] <> "") {
			$arr_keyword = explode(",", $val["tag"]);
			foreach($arr_keyword as $key1 => $val1){
				$wk_keyword.= "<a href=\"sbook.php#page2?w=".urlencode($val1)."\">".$val1."</a> ";
			}
		}
		$html.="		<li><a href=\"sreview.php?id=".$val["book_id"]."\">\n";
		$html.="			<img src=\"".$val["imageurl"]."\" />\n";
		$html.="			<h3>".$val["book_name"]."</h3> \n";
		$html.="			<p>".$val["author_name"]."<br />";
		$html.="Thanks: ".$val["thanks_cnt"]."</p>\n";
		$html.="		</a></li>\n";
	}
}

include 'template/top.html';
?>
