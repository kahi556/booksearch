<?php
//
// ログイン後トップページ
//

session_start();

// ログイン状態のチェック(ログイン未ならログインページ)
if (!isset($_SESSION['login'])) {
	header("Location: login?");
	exit;
}

require("common/conf.php"); // 共通定義

// 変数初期化
$arr_linkurl = array();
$arr_isbn = array();
$arr_imageurl = array();
$arr_title = array();
$arr_author_name = array();
$arr_book_review = array();
$ARR_FEELING = array();
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
$arr_temp_keyword = array();

//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();

//***********************************************
// ユーザー情報検索(ログインユーザー)
//***********************************************
//$sql = "SELECT nickname,review_posts_cnt,thanks_cnt";
//$sql.= " FROM user_table";
//$sql.= " WHERE user_id = \"".$_SESSION["user_id"]."\"";
//$ret = $obj->Fetch($sql);
//if (count($ret) <> 0){
//	foreach($ret as $key => $val){
//		$p_nickname = $val["nickname"]." さん";
//		$p_review_posts_cnt = $val["review_posts_cnt"];
//		$p_thanks_cnt = $val["thanks_cnt"];
//		//$p_tag = $val["tag"];
//	}
//}
//***********************************************
// ユーザー情報(ログインユーザー)
//***********************************************
$p_nickname = $_SESSION["nickname"]." さん";
$p_review_posts_cnt = $_SESSION["review_posts_cnt"];
$p_thanks_cnt = $_SESSION["thanks_cnt"];

//***********************************************
// 書評を登録した本検索(ログインユーザー)
//***********************************************
$sql = "SELECT brt.book_id,brt.tag,brt.thanks_cnt";
$sql.= ",bt.book_name,bt.imageurl";
$sql.= ",at.author_name";
$sql.= " FROM fg_book_review_table brt";
$sql.= " INNER JOIN fg_book_table bt ON bt.book_id = brt.book_id";
$sql.= " LEFT JOIN fg_author_table at ON at.author_id = bt.author_id";
$sql.= " WHERE brt.user_id = \"".$_SESSION["user_id"]."\"";
$sql.= " ORDER BY brt.book_review_no DESC";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		//$arr_title = $val["book_name"];
		//$arr_isbn = $val["isbn"];
		//$arr_imageurl = $val["imageurl"];
		//$arr_book_review = $val["book_review"];
		//$ARR_FEELING = $val["feeling"];
		//$arr_author_name = $val["author_name"];
		// タグをキーワードとして各リンクに分割
		// 重複したキーワードは除く
		if ($val["tag"] <> "") {
			$arr_keyword = explode(",", $val["tag"]);
			foreach($arr_keyword as $key1 => $val1){
				$flg_match = false;
				for($i=0; $i<count($arr_temp_keyword); $i++){
					if ($val1 == $arr_temp_keyword[$i]) { // 重複キーワードがあるかチェック
						$flg_match = true;
						break;
					}
				}
				if (!$flg_match) {
					// 重複キーワードがなかった場合
					$wk_keyword.= "<a rel=\"external\" href=\"./sbook?wk=w&wd=".urlencode($val1)."#page2\">".$val1."</a> ";
					$arr_temp_keyword[] = $val1;
				}
			}
		}
		$html.="		<li><a rel=\"external\" href=\"creview?id=".$val["book_id"]."\">\n";
		$html.="			<img src=\"".$val["imageurl"]."\" />\n";
		$html.="			<h3>".$val["book_name"]."</h3> \n";
		$html.="			<p>".$val["author_name"]."<br />";
		$html.="Thanks: ".$val["thanks_cnt"]."</p>\n";
		$html.="		</a></li>\n";
	}
}else{
	$html.="				<li>見つかりませんでした</li>\n";
}

include 'template/top.html';
?>
