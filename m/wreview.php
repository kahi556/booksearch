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
$p_isbn = "";
$p_book_name = "";
$wk_search_key = "";

//***********************************************
// 受信データをもとに変数の設定 GET
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "GET") {
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_GET["isbn"])){$p_isbn=htmlspecialchars($_GET["isbn"]);}
	if(isset($_GET["img"])){$p_img=htmlspecialchars($_GET["img"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_isbn=preg_replace("/;/"," ",addslashes($p_isbn));
	$p_img=preg_replace("/;/"," ",addslashes($p_img));
	if (($p_isbn == "") && ($p_img == "")){
		$error_message = "検索キーがみつかりません";
	}
}

// おすすめ書籍情報取得
require("amazonaws.php");
if (($p_isbn <> "") || ($p_book_name <> "")) {
	$isbn10 = ISBNTran( $p_isbn );
	$data = amazon_info($p_book_name, $isbn10);
	for ($i=0; $i < $get_book_suu; $i++) { 
		$arr_title[] = $data[$i]->Title;
		$arr_author[] = $data[$i]->Author;
		$arr_imageurl[] = $data[$i]->ImageURL;
		$arr_linkurl[] = $data[$i]->DetailPageURL;
	}
	
}

include 'template/wreview.html';
?>