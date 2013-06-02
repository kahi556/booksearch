<?php
//
// おすすめ本登録
//

session_start();

// ログイン状態のチェック
if (!isset($_SESSION['login'])) {
	header("Location: login.php?m=os");
	exit;
}

include 'template/osusume.html';
?>