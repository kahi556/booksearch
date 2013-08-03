<?php
//
// 会員情報更新
//

session_start();

// ログイン状態のチェック(ログイン未ならログインページ)
if (!isset($_SESSION['login'])) {
	header("Location: login?");
	exit;
}

// 変数初期化
$chk_msg = "";
$msg_info = "";
$wk_selectjob = "";
$time = date("Y-m-d H:i:s"); // 日時取得
$p_login_id = "";
$p_password = "";
$p_password2 = "";
$p_nickname = "";
$p_birth = "";
$p_gender = "";
$p_selectjob = "";
$err_title = "会員情報更新";

require("common/conf.php"); // 共通定義

//***********************************************
// メールアドレス更新
//***********************************************
//***********************************************
// 受信データをもとに変数の設定 POST
//***********************************************
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST["login_id"] <> "")) {
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_POST["login_id"])){$p_login_id=htmlspecialchars($_POST["login_id"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_login_id=preg_replace("/;/"," ",addslashes($p_login_id));
	
	//***********************************************
	// データチェック
	//***********************************************
	// メールアドレス（ユーザーID）[必須、半角英数チェック]
	if ($p_login_id == "") {
		$msg_info.= "メールアドレス（ユーザーID）は必須です。\n";
		$err = true;
	}elseif (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $p_login_id)) {
		$msg_info.= "メールアドレスの形式にて入力して下さい。\n";
		$err = true;
	}
	if (!$err) {
		//***********************************************
		// DB接続
		//***********************************************
		include 'common/database.php';
		$obj = new comdb();
		
		// メールアドレス（ログインID）既存登録チェック
		$sql = "SELECT ut.login_id";
		$sql.= " FROM fg_user_table ut";
		$sql.= " WHERE ut.login_id = \"".$p_login_id."\"";
		$sql.= " UNION ";
		$sql.= "SELECT urt.login_id";
		$sql.= " FROM fg_user_reg_table urt";
		$sql.= " WHERE urt.login_id = \"".$p_login_id."\"";
		$ret = $obj->Fetch($sql);
		if (count($ret) <> 0){
			$err = true;
			$msg_info.= "メールアドレス（ログインID） [ ".$p_login_id." ] ".ERR_DUPL;
		}
	}
	
	if ($err) {
		// エラーあり
		$msg_info = $ERR_S.$msg_info.$ERR_E;
		$err_title = "メールアドレス（ログインID）変更";
		require("template/err.html"); // エラー画面テンプレート呼び出し
		exit;
	}
	
	//***********************************************
	// メールアドレス仮登録
	//***********************************************
	$wk_fileinfo = "common/txt/infom.txt";
	$wk_subject.= "【feegle】メールアドレス仮登録完了のお知らせ";
	$rkey = md5($p_login_id.$time.TANE); // キー生成
	$url_text = URL_SSL."/registm?rkey=".$rkey;
	// メールテキストを取得後、URLを設定
	$wk_body = str_replace("%url_text%", $url_text, file_get_contents($wk_fileinfo));
	// 愛称を設定
	$wk_body = str_replace("%name_kanji%", mb_convert_encoding($_SESSION['nickname'], "sjis-win", "UTF-8"), $wk_body);
	// メールアドレス仮登録
	$sql = "INSERT INTO fg_user_reg_table";
	$sql.= " (user_id,login_id,rkey,rdate) VALUES";
	$sql.= "(\"".$_SESSION["user_id"]."\",\"".$p_login_id."\"";
	$sql.= ",\"".$rkey."\",\"".$time."\")";
	$ret = $obj->Execute($sql);
	if (!$ret){
		echo "sql=".$sql;
		$err = true;
		$msg_info.= ERR_REG."[fg_user_reg_table]\n";
	}
	if (!$err) {
		// メール送信ライブラリ取り込み
		require("common/PHPMailer_5.2.1/class.phpmailer.php");
		//***********************************************
		// メール送信 JISに変換して送信
		//***********************************************
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth   = SMTPAUTH;
		$mail->SMTPSecure = SMTPSECURE;
		$mail->Host       = SMTPHOST;
		$mail->Port       = SMTPPORT;
		$mail->Username   = YOUR_GMAIL_ADDRESS;
		$mail->Password   = YOUR_GMAIL_PASS;
		$mail->CharSet    = SMTPCHARSET;
		$mail->Encoding   = SMTPENCODING;
		$mail->From       = YOUR_GMAIL_ADDRESS;
		$mail->FromName   = mb_encode_mimeheader("feegle");
		$mail->AddReplyTo(YOUR_GMAIL_REFADDRESS, mb_encode_mimeheader(mb_convert_encoding("Reply-To", "JIS", "UTF-8")));
		$mail->Subject    = mb_convert_encoding($wk_subject, "JIS", "UTF-8");
		$mail->Body       = mb_convert_encoding($wk_body, "JIS", "sjis-win");
		$mail->AddAddress($p_login_id, mb_encode_mimeheader(mb_convert_encoding($p_login_id, "JIS", "UTF-8")));
		//$mail->AddBcc(YOUR_GMAIL_REFADDRESS);
		
		$flag = $mail -> Send();
		
		if (!$flag) {
			// メール送信エラー
			$err = true;
			$date = date("YmdHis"); // データ登録日時
			$msg_info.= "変更確認メール送信時にエラーとなりました";
			$message = mb_convert_encoding($msg_info, "sjis-win", "UTF-8")."\n";
			$message.= mb_convert_encoding("メールアドレス:", "sjis-win", "UTF-8").$p_login_id."\n";
			// ファイルを書き込み専用でオープンします。
			$fno = fopen("common/mail_err/".$date."_err.txt", 'w');
			// 文字列を書き出します。
			fwrite($fno, $message);
			// ファイルをクローズします。
			fclose($fno); 
			
			// Mozilla系を混乱させないためExpiresヘッダを送信しない
			session_cache_limiter('private_no_expire');
		}
	}
	// 仮登録完了ページ
	header("Location: #page3");
	exit;
}

//***********************************************
// ユーザー情報更新
//***********************************************
//***********************************************
// 受信データをもとに変数の設定 POST
//***********************************************
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST["password"] <> "")) {
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_POST["password"])){$p_password=htmlspecialchars($_POST["password"]);}
	if(isset($_POST["password2"])){$p_password2=htmlspecialchars($_POST["password2"]);}
	if(isset($_POST["nickname"])){$p_nickname=htmlspecialchars($_POST["nickname"]);}
	if(isset($_POST["birth"])){$p_birth=htmlspecialchars($_POST["birth"]);}
	if(isset($_POST["gender"])){$p_gender=htmlspecialchars($_POST["gender"]);}
	if(isset($_POST["selectjob"])){$p_selectjob=htmlspecialchars($_POST["selectjob"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_password=preg_replace("/;/"," ",addslashes($p_password));
	$p_password2=preg_replace("/;/"," ",addslashes($p_password2));
	$p_nickname=preg_replace("/;/"," ",addslashes($p_nickname));
	$p_birth=preg_replace("/;/"," ",addslashes($p_birth));
	$p_gender=preg_replace("/;/"," ",addslashes($p_gender));
	$p_selectjob=preg_replace("/;/"," ",addslashes($p_selectjob));
	
	//***********************************************
	// データチェック
	//***********************************************
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
	}elseif ($p_password <> $p_password2) {
		$msg_info.= "パスワードとパスワード(確認)が一致しません。\n";
		$err = true;
	}
	// 愛称[必須チェック]
	if ($p_nickname == "") {
		$msg_info.= "愛称は必須です。\n";
		$err = true;
	}
	if (!$err) {
		//***********************************************
		// DB接続
		//***********************************************
		include 'common/database.php';
		$obj = new comdb();
		
		// 既存登録チェック
		$sql = "SELECT user_id";
		$sql.= " FROM fg_user_table";
		$sql.= " WHERE user_id = \"".$_SESSION["user_id"]."\"";
		$ret = $obj->Fetch($sql);
		if (count($ret) == 0){
			$err = true;
			$msg_info.= "更新対象が見つかりません";
		}
	}
	
	if ($err) {
		// エラーあり
		$msg_info = $ERR_S.$msg_info.$ERR_E;
		$err_title = "会員情報変更";
		require("template/err.html"); // エラー画面テンプレート呼び出し
		exit;
	}
	
	$chk_msg.= "パスワード：".$p_password."<br>\n";
	$chk_msg.= "愛称：".$p_nickname."<br>\n";
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
	$chk_msg.= "<input type=\"hidden\" name=\"registchk\" value=\"y\">\n";
	
	//***********************************************
	// セッション情報保存
	//***********************************************
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
		$sql = "UPDATE fg_user_table SET";
		$sql.= " password = \"".crypt(sha1($p_password),TANE)."\"";
		$sql.= ",nickname = \"".$p_nickname."\"";
		$sql.= ",birth = \"".$p_birth."\"";
		$sql.= ",gender = \"".$p_gender."\"";
		$sql.= ",mjob_cd = \"".$arr_selectjob[0]."\"";
		$sql.= ",ljob_cd = \"".$arr_selectjob[1]."\"";
		$sql.= " WHERE user_id = \"".$_SESSION["user_id"]."\"";
		$ret = $obj->Execute($sql);
		if (!$ret){
			//echo "sql=".$sql;
			$err = true;
			$msg_info.= ERR_UPD."[fg_user_table]\n";
		}
		// 更新完了ページ
		header("Location: #page4");
		
	}else{
		// 更新確認ページ
		include 'template/updconf.html';
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
$sql = "SELECT login_id,nickname,birth,gender,mjob_cd,ljob_cd";
$sql.= " FROM fg_user_table";
$sql.= " WHERE user_id = \"".$_SESSION['user_id']."\"";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		$p_login_id = $val["login_id"];
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
$sql1.= " FROM fg_mjob_table mt";
$sql1.= " INNER JOIN fg_ljob_table lt ON lt.ljob_cd = mt.ljob_cd";
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
