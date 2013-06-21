<?php
//
// ログイン
//

session_start();

$viewUserId = "";
// ログイン状態のチェック(ログイン済ならログイン後トップページ)
if (isset($_SESSION['login'])) {
	header("Location: top.php");
	exit;
}

require("common/conf.php"); // 共通定義

// ログイン後の処理振り分け、セッション情報クリア
if (isset($_GET['m'])) {
	if ($_GET['m'] == "wr") { // 書評を書く
		$msg_info = "書評を書くにはログインが必要です。<br>";
		$_SESSION['link'] = "wreview.php";
	}elseif ($_GET['m'] == "os") { // おすすめ本登録
		$msg_info = "おすすめ本を登録するにはログインが必要です。<br>";
		$_SESSION['link'] = "osusume.php";
	//}elseif ($_GET['m'] == "sb") { // 本を検索
	//	$msg_info = "本を検索するにはログインが必要です。<br>";
	//	$_SESSION['link'] = "sbook.php";
	}
}elseif (isset($_POST['user_id'])) { // ユーザーID入力後
}else{
	require("common/sess_clear.php"); // セッション情報クリア
}

// エラーメッセージを格納する変数を初期化
$errorMessage = "";
if (isset($_POST['user_id'])) {
	$viewUserId = htmlspecialchars($_POST['user_id'], ENT_QUOTES);
	$viewPassword = crypt(sha1($_POST['password']),TANE);
	include 'common/database.php';
	
	$obj = new comdb();
	
	// 会員ログイン
	$ret = $obj->Login($viewUserId, $viewPassword);
	if(!$ret){
		$msg_info = $obj->errmsg;
		include 'template/login.html';
		exit;
		
	}else{
		$errorMessage = "";
		//セッション保管
		//session_regenerate_id(TRUE);
		
		$_SESSION['user_id'] = $ret['USER_ID'];
		$_SESSION['nickname'] = $ret['NICKNAME'];
		$_SESSION['birth'] = $ret['BIRTH'];
		if ($ret['GENDER'] == 'M'){
			$_SESSION['gendername'] = "男性";
		}else{
			$_SESSION['gendername'] = "女性";
		}
		$_SESSION['mjob_cd'] = $ret['MJOB_CD'];
		$_SESSION['ljob_cd'] = $ret['LJOB_CD'];
		$_SESSION['login'] = "y";
		
		if (isset($_SESSION['link'])) {
			header('Location: '.$_SESSION['link']);
		}else{
			//ログイン後トップページへ
			header('Location: top.php');
		}
		exit;
	}
}

include 'template/login.html';
?>
