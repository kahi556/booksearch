<?php
//
// 書評を書いた本の情報表示
//

session_start();

// ログイン状態のチェック(ログイン未ならログインページ)
if (!isset($_SESSION['login'])) {
	header("Location: login.php");
	exit;
}

// 変数初期化
$p_book_id = "";
$wk_feeling_image = "";
$wk_title = "";
$wk_author = "";
$wk_imageurl = "";
$wk_linkurl = "";
$wk_keyword = "";
$arr_keyword = array();

//***********************************************
// 受信データをもとに変数の設定 GET
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "GET") { 
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_GET["id"])){$p_book_id=htmlspecialchars($_GET["id"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_book_id=preg_replace("/;/"," ",addslashes($p_book_id));
}
if ($p_book_id == "") {
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
// 書評を登録した本検索
//***********************************************
$sql = "SELECT brt.thanks_cnt,brt.tag,brt.feeling";
$sql.= ",bt.book_name,bt.imageurl";
$sql.= ",at.author_name";
$sql.= " FROM book_review_table brt";
$sql.= " INNER JOIN book_table bt ON bt.book_id = brt.book_id";
$sql.= " LEFT JOIN author_table at ON at.author_id = bt.author_id";
$sql.= " WHERE brt.user_id = \"".$_SESSION["user_id"]."\"";
$sql.= " AND brt.book_id = \"".$p_book_id."\"";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	require("amazonaws.php");
	foreach($ret as $key => $val){
		// タグをキーワードとして各リンクに分割
		if ($val["tag"] <> "") {
			$arr_keyword = explode(",", $val["tag"]);
			foreach($arr_keyword as $key => $val){
				$wk_keyword.= "<a href=\"sbook.php#page2?m=1\">".$val."</a> ";
			}
		}
		// イメージ
		$wk_feeling_image = "<img src=\"images/".$val["feeling"].".gif\"";
		// 
		$wk_title = $val["book_name"];
		$wk_author = $val["author_name"];
		$wk_imageurl = $val["imageurl"];
		$wk_linkurl = "";
	}
}



include 'template/sreview.html';
?>
