<?php
//
// 書評を書く
//

session_start();

// ログイン状態のチェック
if (!isset($_SESSION['login'])) {
	header("Location: login.php?m=wr");
	exit;
}

// 変数初期化
$p_review = "";
$p_tag = "";
$p_feeling = "";
$p_chk = "";
$p_author_id = "";
$p_book_id = "";
$sel_feeling = "";
$wk_search_key = "";
$msg_info = "";
$time = date("Y-m-d H:i:s"); // 日時取得

require("common/conf.php"); // 共通定義

//***********************************************
// 受信データをもとに変数の設定 POST
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_POST["chk"])){$p_chk=htmlspecialchars($_POST["chk"]);}
	if ($p_chk == "") {
		//***********************************************
		// これから確認
		//***********************************************
		if(isset($_POST["review"])){$p_review=htmlspecialchars($_POST["review"]);}
		if(isset($_POST["tag"])){$p_tag=htmlspecialchars($_POST["tag"]);}
		if(isset($_POST["feeling"])){$p_feeling=htmlspecialchars($_POST["feeling"]);}
		// 文字のエスケープ（セキュリティ対策）
		$p_review=preg_replace("/;/"," ",addslashes($p_review));
		$p_tag=preg_replace("/;/"," ",addslashes($p_tag));
		$p_feeling=preg_replace("/;/"," ",addslashes($p_feeling));
		if ($p_review == "") {
			$msg_info = "入力内容にエラーがあります";
			include 'template/wreview.html';
			exit;
		}
		$_SESSION["review"] = $p_review;
		$p_tag = str_replace("、", ",", $p_tag); // カンマ区切りを半角に統一
		$p_tag = str_replace("，", ",", $p_tag); // カンマ区切りを半角に統一
		$_SESSION["tag"] = $p_tag;
		$_SESSION["feeling"] = $p_feeling;
		foreach ($ARR_FEELING as $key => $val) {
			if ($key == $p_feeling) {
				$sel_feeling = $val;
				break;
			}
		}
		include 'template/wreviewchk.html';
		exit;
	}
}else{
	$msg_info = "エラーがあります";
	include 'template/wreview.html';
	exit;
}

//***********************************************
// 確認済
//***********************************************

//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();
mysql_set_charset('utf8');

//***********************************************
// トランザクション開始
//***********************************************
$obj->BeginTran();

//***********************************************
// 既存著者検索
//***********************************************
$sql = "SELECT author_id";
$sql.= " FROM author_table";
$sql.= " WHERE author_name = \"".$_SESSION["author"]."\"";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		$p_author_id = $val["author_id"];
	}
}else{
	//***********************************************
	// 既存著者がないため登録
	//***********************************************
	$sql = "INSERT INTO author_table";
	$sql.= " (author_name,user_id,rdate) VALUES";
	$sql.= "(\"".$_SESSION["author"]."\"";
	$sql.= ",\"".$_SESSION["user_id"]."\",\"".$time."\")";
	$ret = $obj->Execute($sql);
	if (!$ret){
		//$msg_info.= "sql=".$sql;
		$err = true;
		$msg_info.= ERR_REG."[author_table]\n";
	}else{
		$p_author_id = mysql_insert_id();
	}
}
//***********************************************
// 既存書籍検索
//***********************************************
$sql = "SELECT book_id";
$sql.= " FROM book_table";
$sql.= " WHERE isbn = \"".$_SESSION["isbn"]."\"";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		$p_book_id = $val["book_id"];
	}
}else{
	//***********************************************
	// 既存書籍がないため登録
	//***********************************************
	$sql = "INSERT INTO book_table";
	$sql.= " (book_name,isbn,imageurl,author_id,user_id,rdate) VALUES";
	$sql.= "(\"".$_SESSION["title"]."\",\"".$_SESSION["isbn"]."\"";
	$sql.= ",\"".$_SESSION["imageurl"]."\",\"".$p_author_id."\"";
	$sql.= ",\"".$_SESSION["user_id"]."\",\"".$time."\")";
	$ret = $obj->Execute($sql);
	if (!$ret){
		//$msg_info.= "sql=".$sql;
		$err = true;
		$msg_info.= ERR_REG."[book_table]\n";
	}else{
		$p_book_id = mysql_insert_id();
	}
}
//***********************************************
// 書評登録
//***********************************************
$sql = "INSERT INTO book_review_table";
$sql.= " (book_review,book_id,tag,feeling,user_id,rdate) VALUES";
$sql.= "(\"".$_SESSION["review"]."\",\"".$p_book_id."\"";
$sql.= ",\"".$_SESSION["tag"]."\",\"".$_SESSION["feeling"]."\"";
$sql.= ",\"".$_SESSION["user_id"]."\",\"".$time."\")";
$ret = $obj->Execute($sql);
if (!$ret){
	//$msg_info.= "sql=".$sql;
	$err = true;
	$msg_info.= ERR_REG."[book_review_table]\n";
}else{
	$p_book_id = mysql_insert_id();
}

if ($err) {
	$obj->RollBack();
}else{
	$obj->Commit();
	$msg_info.= "書評を登録しました\n";
}
require("common/sess_clear_review.php"); // 書評関連セッション情報クリア
include 'template/wreviewend.html';
?>