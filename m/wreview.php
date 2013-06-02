<?php
//
// 書評を書く
//

session_start();

// 変数初期化
$p_book_name = "";
$p_review = "";

// ログイン状態のチェック
if (!isset($_SESSION['login'])) {
	header("Location: login.php?m=wr");
	exit;
}

//***********************************************
// 受信データをもとに変数の設定 POST
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_POST["book_name"])){$p_book_name=htmlspecialchars($_POST["book_name"]);}
	if(isset($_POST["review"])){$p_review=htmlspecialchars($_POST["review"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_book_name=preg_replace("/;/"," ",addslashes($p_book_name));
	$p_review=preg_replace("/;/"," ",addslashes($p_review));
	
	
}




include 'template/wreview.html';
?>