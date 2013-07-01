<?php
//
// 書評を書いた本の情報表示
//

session_start();

// ログイン状態のチェック
//if (!isset($_SESSION['login'])) {
//	header("Location: login.php?m=sb");
//	exit;
//}

// 変数初期化
$p_book_id = "";
$p_wk = "";
$p_word_j = "";
$wk_feeling_image = "";
$wk_book_review = "";
$wk_title = "";
$wk_author = "";
$wk_imageurl = "";
$wk_linkurl = "";
$wk_keyword = "";
$btn_mod = "";
$arr_keyword = array();
$arr_selected = array();

require("common/conf.php"); // 共通定義

//***********************************************
// 受信データをもとに変数の設定 GET
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "GET") { 
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_GET["id"])){$p_book_id=htmlspecialchars($_GET["id"]);}
	if(isset($_GET["wk"])){$p_wk=htmlspecialchars($_GET["wk"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_book_id=preg_replace("/;/"," ",addslashes($p_book_id));
	$p_wk=preg_replace("/;/"," ",addslashes($p_wk));
}
if ($p_book_id == "") {
	$msg_info = "不正な処理です。";
	require("err.html"); // エラー画面テンプレート呼び出し
	exit;
}

// ログイン状態のチェック(ログイン未で書評変更ならログインページ)
if (!isset($_SESSION['login']) && ($p_wk == "mod")) {
	header("Location: login.php");
	exit;
}

//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();

//***********************************************
// 書評を登録した本検索
//***********************************************
$sql = "SELECT brt.book_review,brt.thanks_cnt,brt.tag,brt.feeling";
$sql.= ",bt.book_name,bt.imageurl";
$sql.= ",at.author_name";
$sql.= " FROM book_review_table brt";
$sql.= " INNER JOIN book_table bt ON bt.book_id = brt.book_id";
$sql.= " LEFT JOIN author_table at ON at.author_id = bt.author_id";
$sql.= " WHERE brt.book_id = \"".$p_book_id."\"";
if (isset($_SESSION['login'])) {
	// ログイン中のみの条件
	$sql.= " AND brt.user_id = \"".$_SESSION["user_id"]."\"";
}
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		// タグをキーワードとして各リンクに分割
		if ($val["tag"] <> "") {
			$arr_keyword = explode(",", $val["tag"]);
			foreach($arr_keyword as $key1 => $val1){
				$wk_keyword.= "<a rel=\"external\" href=\"./sbook.php?wk=w&wd=".urlencode($val1)."\">".$val1."</a> ";
			}
		}
		// セッションに設定
		$_SESSION["review"] = $val["book_review"];
		$_SESSION["thanks_cnt"] = $val["thanks_cnt"];
		$_SESSION["title"] = $val["book_name"];
		$_SESSION["author"] = $val["author_name"];
		$_SESSION["imageurl"] = $val["imageurl"];
		$_SESSION["feeling"] = $val["feeling"];
		$_SESSION["tag"] = $val["tag"];
		$wk_linkurl = "";
		// イメージ
		$_SESSION["feeling_image"] = "<img src=\"images/".$val["feeling"].".gif\"";
	}
}

// ログイン状態のチェック(ログイン済なら内容変更ボタン表示)
if (isset($_SESSION['login'])) {
	$btn_mod.= "		";
	$btn_mod.= "<a rel=\"external\" href=\"sreview.php?id=".$p_book_id."&wk=mod\" data-role=\"button\">内容変更</a>\n";
}

if ($p_wk == "mod") {
	// 内容変更表示
	if(isset($_SESSION["feeling"])){
		foreach ($ARR_FEELING as $key => $val) {
			if ($key == $_SESSION["feeling"]) {
				$arr_selected[$cnt] = " selected";
			}else{
				$arr_selected[$cnt] = "";
			}
			$cnt++;
		}
	}
	include 'template/wreview.html';
}else{
	// 検索結果表示
	if(isset($_SESSION["feeling"])){
		foreach ($ARR_FEELING as $key => $val) {
			if ($key == $_SESSION["feeling"]) {
				$p_word_j = $val;
				break;
			}
		}
	}
	include 'template/sreview.html';
}
?>
