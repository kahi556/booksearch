<?php
//
// 会員情報更新
//

session_start();

// ログイン状態のチェック(ログイン未ならログインページ)
if (!isset($_SESSION['login'])) {
	header("Location: login.php");
	exit;
}

// 変数初期化
$chk_msg = "";
$msg_info = "";
$wk_selectjob = "";
$time = date("Y-m-d H:i:s"); // 日時取得
$p_user_id = "";
$p_password = "";
$p_user_id = "";
$p_nickname = "";
$p_birth = "";
$p_gender = "";
$p_selectjob = "";

require("common/conf.php"); // 共通定義

//***********************************************
// 受信データをもとに変数の設定 POST
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_POST["user_id"])){$p_user_id=htmlspecialchars($_POST["user_id"]);}
	if(isset($_POST["password"])){$p_password=htmlspecialchars($_POST["password"]);}
	if(isset($_POST["nickname"])){$p_nickname=htmlspecialchars($_POST["nickname"]);}
	if(isset($_POST["birth"])){$p_birth=htmlspecialchars($_POST["birth"]);}
	if(isset($_POST["gender"])){$p_gender=htmlspecialchars($_POST["gender"]);}
	if(isset($_POST["selectjob"])){$p_selectjob=htmlspecialchars($_POST["selectjob"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_user_id=preg_replace("/;/"," ",addslashes($p_user_id));
	$p_password=preg_replace("/;/"," ",addslashes($p_password));
	$p_nickname=preg_replace("/;/"," ",addslashes($p_nickname));
	$p_birth=preg_replace("/;/"," ",addslashes($p_birth));
	$p_gender=preg_replace("/;/"," ",addslashes($p_gender));
	$p_selectjob=preg_replace("/;/"," ",addslashes($p_selectjob));
	
	//***********************************************
	// データチェック
	//***********************************************
	// メールアドレス（ユーザーID）[必須、半角英数チェック]
	if ($p_user_id == "") {
		$msg_info.= "メールアドレス（ユーザーID）は必須です。\n";
		$err = true;
	}elseif (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $p_user_id)) {
		$msg_info.= "メールアドレスの形式にて入力して下さい。\n";
		$err = true;
	}
	// パスワード[必須、半角英数、レングスチェック]
	if ($p_password == "") {
		$msg_info.= "パスワードは必須です。\n";
		$err = true;
	}elseif (!preg_match("/^[a-zA-Z0-9]+$/", $p_password)) {
		$msg_info.= "パスワードは半角英数にて入力して下さい。\n";
		$err = true;
	}elseif ((mb_strlen($p_password) < 5) || (mb_strlen($p_password) > 24)) {
		$msg_info.= "パスワードは5文字以上24文字以下にて入力して下さい。\n";
		$err = true;
	//}elseif ($p_password <> $p_password) {
	//	$msg_info.= "パスワードとパスワード(確認)が一致しません。\n";
	//	$err = true;
	}
	// ニックネーム[必須チェック]
	if ($p_nickname == "") {
		$msg_info.= "ニックネームは必須です。\n";
		$err = true;
	}
	if (!$err) {
		//***********************************************
		// DB接続
		//***********************************************
		include 'common/database.php';
		$obj = new comdb();
		
		// メールアドレス（ユーザーID）既存登録チェック
		$sql = "SELECT user_id";
		//$sql.= " FROM user_reg_table";
		$sql.= " FROM user_table";
		$sql.= " WHERE user_id = \"".$p_user_id."\"";
		$ret = $obj->Fetch($sql);
		if (count($ret) == 0){
			$err = true;
			$msg_info.= "メールアドレス（ユーザーID） [ ".$p_user_id." ] が見つかりません";
		}
	}
	
	if ($err) {
		// エラーあり
		$msg_info = $ERR_S.$msg_info.$ERR_E;
		$err_title = "変更";
		require("template/err.html"); // エラー画面テンプレート呼び出し
		exit;
	}
	
	$chk_msg.= "メールアドレス（ユーザーID）：".$p_user_id."<br>\n";
	$chk_msg.= "パスワード：".$p_password."<br>\n";
	$chk_msg.= "ニックネーム：".$p_nickname."<br>\n";
	$wk_birth = str_replace("-", "/", $p_birth);
	$chk_msg.= "生年月日：".$wk_birth."<br>\n";
	if ($p_gender == "M") {
		$wk_gender = "男性";
	}else{
		$wk_gender = "女性";
	}
	$chk_msg.= "性別：".$wk_gender."<br>\n";
	$arr_selectjob = explode(":", $p_selectjob);
	$chk_msg.= "職業：".$arr_selectjob[2]."<br>\n";
	
	//***********************************************
	// セッション情報保存
	//***********************************************
	$_SESSION["user_id"] = $p_user_id;
	$_SESSION["password"] = $p_password;
	$_SESSION["nickname"] = $p_nickname;
	$_SESSION["birth"] = $p_birth;
	$_SESSION["gender"] = $p_gender;
	$_SESSION["selectjob"] = $p_selectjob;
	
	if ($_POST["regist"]) {
		//***********************************************
		// 会員情報更新
		//***********************************************
		$arr_selectjob = explode(":", $p_selectjob);
		$sql = "UPDATE user_table SET";
		$sql.= " password = \"".crypt(sha1($p_password),TANE)."\"";
		$sql.= ",nickname = \"".$p_nickname."\"";
		$sql.= ",birth = \"".$p_birth."\"";
		$sql.= ",gender = \"".$p_gender."\"";
		$sql.= ",mjob_cd = \"".$arr_selectjob[0]."\"";
		$sql.= ",ljob_cd = \"".$arr_selectjob[1]."\"";
		$sql.= " WHERE user_id = \"".$p_user_id."\"";
		$ret = $obj->Execute($sql);
		if (!$ret){
			//echo "sql=".$sql;
			$err = true;
			$msg_info.= ERR_UPD."[user_table]\n";
		}
		header("Location: #page2");
		
	}else{
		//***********************************************
		// 変更内容確認
		//***********************************************
		include 'template/registconf.html';
	}
	exit;
}

//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();

//***********************************************
// ユーザー情報検索
//***********************************************
$sql = "SELECT user_id,nickname,birth,gender,mjob_cd,ljob_cd";
$sql.= " FROM user_table";
$sql.= " WHERE user_id = \"".$_SESSION['user_id']."\"";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		$p_user_id = $val["user_id"];
		$p_nickname = $val["nickname"];
		$p_birth = $val["birth"];
	}
}

//***********************************************
// 性別フリップ生成
//***********************************************
$wk_selected_m = "";
$wk_selected_f = "";
$wk_gender = "";
if ($val["gender"] == "M") {
	$wk_selected_m = " selected";
}else{
	$wk_selected_f = " selected";
}
	$wk_gender.= " 						";
	$wk_gender.= "<option value=\"M\"".$wk_selected_m.">男性</option>\n";
	$wk_gender.= " 						";
	$wk_gender.= "<option value=\"F\"".$wk_selected_f.">女性</option>\n";

//***********************************************
// 職業選択ボックス生成
//***********************************************
$wk_ljob_cd = "";
$sql1 = "SELECT mt.mjob_name,mt.mjob_cd,mt.ljob_cd,lt.ljob_name";
$sql1.= " FROM mjob_table mt";
$sql1.= " INNER JOIN ljob_table lt ON lt.ljob_cd = mt.ljob_cd";
$sql1.= " ORDER BY mt.ljob_cd,mt.mjob_cd";
$ret1 = $obj->Fetch($sql1);
if (count($ret1) <> 0){
	foreach($ret1 as $key1 => $val1){
		if ($wk_ljob_cd <> $val1["ljob_cd"]) {
			$wk_selectjob.= "					";
			$wk_selectjob.= "<optgroup label=\"".$val1["ljob_name"]."\">\n";
			$wk_ljob_cd = $val1["ljob_cd"];
		}
		if (($val["mjob_cd"] == $val1["mjob_cd"]) &&
			($val["ljob_cd"] == $val1["ljob_cd"])) {
			$wk_selected = " selected";
		}else{
			$wk_selected = "";
		}
		$wk_selectjob.= "						";
		$wk_selectjob.= "<option value=\"".$val1["mjob_cd"].":".$val1["ljob_cd"];
		$wk_selectjob.= ":".$val1["mjob_name"]."\"".$wk_selected.">";
		$wk_selectjob.= $val1["mjob_name"]."</option>\n";
	}
}

include 'template/settei.html';
?>
