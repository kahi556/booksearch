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

include 'template/top.html';
?>
