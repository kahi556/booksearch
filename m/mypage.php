<?php
//
// マイページ
//

session_start();

// ログイン状態のチェック(ログイン未ならログインページ)
if (!isset($_SESSION['login'])) {
	header("Location: login.php");
	exit;
}

//***********************************************
// 書評検索
//***********************************************
$sql = "SELECT *";
$sql.= " FROM book_review_table";
$sql.= " WHERE isbn = \"".$_SESSION["isbn"]."\"";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		$p_book_id = $val["book_id"];
	}
}

include 'template/mypage.html';
?>
