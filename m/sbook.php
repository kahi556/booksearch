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

include 'template/sbook.html';
?>