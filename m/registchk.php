<?php
//
// 新規登録
//  登録内容の確認、および仮登録を行い、
//  仮登録メール送信を行う
//

session_start();

// ログイン状態のチェック(ログイン済ならログイン後トップページ)
//if (isset($_SESSION['login'])) {
//	header("Location: top.php");
//	exit;
//}

// 変数初期化
$chk_msg = "";
$msg_info = "";
$wk_selectjob = "";
$p_login_id = "";
$p_password = "";
$p_password2 = "";
$p_login_id = "";
$p_nickname = "";
$p_birth = "";
$p_gender = "";
$p_selectjob = "";
$p_consent = "";
$p_new = "";
$time = date("Y-m-d H:i:s"); // 日時取得
$err_title = "会員仮登録";

require("common/conf.php"); // 共通定義

//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();

//***********************************************
// 前画面からの戻り
//***********************************************
// セッション情報より取得
if(isset($_SESSION["login_id"])){$p_login_id=htmlspecialchars($_SESSION["login_id"]);}
if(isset($_SESSION["password"])){$p_password=htmlspecialchars($_SESSION["password"]);}
if(isset($_SESSION["password2"])){$p_password2=htmlspecialchars($_SESSION["password2"]);}
if(isset($_SESSION["nickname"])){$p_nickname=htmlspecialchars($_SESSION["nickname"]);}
if(isset($_SESSION["birth"])){$p_birth=htmlspecialchars($_SESSION["birth"]);}
if(isset($_SESSION["gender"])){$p_gender=htmlspecialchars($_SESSION["gender"]);}
if(isset($_SESSION["selectjob"])){$p_selectjob=htmlspecialchars($_SESSION["selectjob"]);}
if(isset($_SESSION["consent"])){$p_consent=htmlspecialchars($_SESSION["consent"]);}
if(isset($_SESSION["new"])){$p_new=htmlspecialchars($_SESSION["new"]);}
// セッション情報クリア
$_SESSION["login_id"] = "";
$_SESSION["password"] = "";
$_SESSION["password2"] = "";
$_SESSION["nickname"] = "";
$_SESSION["birth"] = "";
$_SESSION["gender"] = "";
$_SESSION["selectjob"] = "";
$_SESSION["consent"] = "";
$_SESSION["new"] = "";

//***********************************************
// 受信データをもとに変数の設定 POST
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_POST["login_id"])){$p_login_id=htmlspecialchars($_POST["login_id"]);}
	if(isset($_POST["password"])){$p_password=htmlspecialchars($_POST["password"]);}
	if(isset($_POST["password2"])){$p_password2=htmlspecialchars($_POST["password2"]);}
	if(isset($_POST["nickname"])){$p_nickname=htmlspecialchars($_POST["nickname"]);}
	if(isset($_POST["birth"])){$p_birth=htmlspecialchars($_POST["birth"]);}
	if(isset($_POST["gender"])){$p_gender=htmlspecialchars($_POST["gender"]);}
	if(isset($_POST["selectjob"])){$p_selectjob=htmlspecialchars($_POST["selectjob"]);}
	if(isset($_POST["consent"])){$p_consent=htmlspecialchars($_POST["consent"]);}
	if(isset($_POST["new"])){$p_new=htmlspecialchars($_POST["new"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_login_id=preg_replace("/;/"," ",addslashes($p_login_id));
	$p_password=preg_replace("/;/"," ",addslashes($p_password));
	$p_password2=preg_replace("/;/"," ",addslashes($p_password2));
	$p_nickname=preg_replace("/;/"," ",addslashes($p_nickname));
	$p_birth=preg_replace("/;/"," ",addslashes($p_birth));
	$p_gender=preg_replace("/;/"," ",addslashes($p_gender));
	$p_selectjob=preg_replace("/;/"," ",addslashes($p_selectjob));
	$p_consent=preg_replace("/;/"," ",addslashes($p_consent));
	$p_new=preg_replace("/;/"," ",addslashes($p_new));
	
	//***********************************************
	// データチェック
	//***********************************************
	// メールアドレス（ログインID）[必須、半角英数チェック]
	if ($p_login_id == "") {
		$msg_info.= "メールアドレス（ログインID）は必須です。\n";
		$err = true;
	}elseif (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $p_login_id)) {
		$msg_info.= "メールアドレスの形式にて入力して下さい。\n";
		$err = true;
	//}elseif ($p_email <> $p_email2) {
	//	$msg_info.= "メールアドレスとメールアドレス(確認)が一致しません。\n";
	//	$err = true;
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
	}elseif ($p_password <> $p_password2) {
		$msg_info.= "パスワードとパスワード(確認)が一致しません。\n";
		$err = true;
	}
	// 愛称[必須チェック]
	if ($p_nickname == "") {
		$msg_info.= "愛称は必須です。\n";
		$err = true;
	}
	// 会員利用規約[同意チェック]
	if ($p_consent <> "Yes") {
		$msg_info.= "「会員利用規約」の同意が必要です。\n";
		$err = true;
	}
	
	if ($err) {
		// エラーあり
		$msg_info = $ERR_S.$msg_info.$ERR_E;
		$err_title = "新規登録";
		require("template/err.html"); // エラー画面テンプレート呼び出し
		exit;
	}
	
	if ($_POST["regist"]) {
		//***********************************************
		// 仮会員登録
		//***********************************************
		$wk_fileinfo = "common/txt/info.txt";
		$wk_subject.= "【feegle】仮登録完了のお知らせ";
		$rkey = md5($p_login_id.$time.TANE); // キー生成
		$url_text = URL_SSL."/registid.php?rkey=".$rkey;
		// メールテキストを取得後、URLを設定
		$wk_body = str_replace("%url_text%", $url_text, file_get_contents($wk_fileinfo));
		// 愛称を設定
		$wk_body = str_replace("%name_kanji%", mb_convert_encoding($p_nickname, "sjis-win", "UTF-8"), $wk_body);
		$arr_selectjob = explode(":", $p_selectjob);
		// ユーザ仮登録
		$sql = "INSERT INTO fg_user_reg_table";
		$sql.= " (login_id,password,nickname,birth,gender";
		$sql.= ",mjob_cd,ljob_cd,rkey,rdate) VALUES";
		$sql.= "(\"".$p_login_id."\",\"".crypt(sha1($p_password),TANE)."\"";
		$sql.= ",\"".$p_nickname."\",\"".$p_birth."\",\"".$p_gender."\"";
		$sql.= ",\"".$arr_selectjob[0]."\",\"".$arr_selectjob[1]."\"";
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
				$msg_info.= "登録確認メール送信時にエラーとなりました";
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
		
	}else{
		// メールアドレス（ログインID）既存登録チェック
		$sql = "SELECT ut.login_id";
		$sql.= " FROM fg_user_table ut";
		$sql.= " WHERE ut.login_id = \"".$p_login_id."\"";
		$sql.= " UNION ";
		$sql.= "SELECT urt.login_id";
		$sql.= " FROM fg_user_reg_table urt";
		$sql.= " WHERE urt.login_id = \"".$p_login_id."\"";
		$ret = $obj->Fetch($sql);
		if (count($ret) > 0){
			$err = true;
			foreach($ret as $key => $val){
				if ($val["login_id"] <> "") {
					$msg_info.= "メールアドレス（ログインID） [ ".$p_login_id." ] ".ERR_DUPL;
				}
			}
		}
	}
	
	if ($err) {
		// エラーあり
		$msg_info = $ERR_S.$msg_info.$ERR_E;
		$err_title = "新規登録";
		require("template/err.html");// エラー画面テンプレート呼び出し
	}
	
	$chk_msg.= "メールアドレス（ログインID）：".$p_login_id."<br>\n";
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
	
	//***********************************************
	// セッション情報保存
	//***********************************************
	$_SESSION["login_id"] = $p_login_id;
	$_SESSION["password"] = $p_password;
	$_SESSION["nickname"] = $p_nickname;
	$_SESSION["birth"] = $p_birth;
	$_SESSION["gender"] = $p_gender;
	$_SESSION["selectjob"] = $p_selectjob;
	$_SESSION["consent"] = $p_consent;
	$_SESSION["new"] = $p_new;
	
	// 仮登録確認ページ
	include 'template/registconf.html';
	exit;
}

//***********************************************
// 職業選択ボックス生成
//***********************************************
$wk_ljob_cd = "";
$sql = "SELECT mt.mjob_name,mt.mjob_cd,mt.ljob_cd,lt.ljob_name";
$sql.= " FROM fg_mjob_table mt";
$sql.= " INNER JOIN fg_ljob_table lt ON lt.ljob_cd = mt.ljob_cd";
$sql.= " ORDER BY mt.ljob_cd,mt.mjob_cd";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		if ($wk_ljob_cd <> $val["ljob_cd"]) {
			$wk_selectjob.= "					";
			$wk_selectjob.= "<optgroup label=\"".$val["ljob_name"]."\">\n";
			$wk_ljob_cd = $val["ljob_cd"];
		}
		$wk_selectjob.= "						";
		$wk_selectjob.= "<option value=\"".$val["mjob_cd"].":".$val["ljob_cd"];
		$wk_selectjob.= ":".$val["mjob_name"]."\">";
		$wk_selectjob.= $val["mjob_name"]."</option>\n";
	}
}

include 'template/registchk.html';
?>
