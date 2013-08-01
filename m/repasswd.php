<?php
//
// パスワード再発行
//

// 変数初期化
$chk_msg = "";
$msg_info = "";
$time = date("Y-m-d H:i:s"); // 日時取得
$p_login_id = "";
$p_birth = "";
$wk_password = "";
$err_title = "パスワード再発行";

require("common/conf.php"); // 共通定義

//***********************************************
// パスワード再発行
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_POST["login_id"])){$p_login_id=htmlspecialchars($_POST["login_id"]);}
	if(isset($_POST["birth"])){$p_birth=htmlspecialchars($_POST["birth"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_login_id=preg_replace("/;/"," ",addslashes($p_login_id));
	$p_birth=preg_replace("/;/"," ",addslashes($p_birth));
	
	//***********************************************
	// DB接続
	//***********************************************
	include 'common/database.php';
	$obj = new comdb();
	
	// 既存登録チェック
	$sql = "SELECT user_id";
	$sql.= " FROM fg_user_table";
	$sql.= " WHERE login_id = \"".$p_login_id."\"";
	$sql.= " AND birth = \"".$p_birth."\"";
	$ret = $obj->Fetch($sql);
	if (count($ret) == 0){
		$err = true;
		$msg_info.= "会員登録を確認できませんでした。メールアドレス、生年月日を再度ご確認ください。";
	}
	
	if (!$err) {
		//***********************************************
		// パスワード再発行
		//***********************************************
		// パスワード生成
		//   ランダム文字列生成（文字コード利用）
		//   a-z,A-Z,0-9 を使って8桁のランダム文字列を生成
		for ($i = 0, $wk_password = null; $i < 8; ) {
			$num = mt_rand(0x30, 0x7A); // ASCII文字コード
			if ((0x30 <= $num && $num <= 0x39) || (0x41 <= $num && $num <= 0x5A)
			|| (0x61 <= $num && $num <= 0x7A)) {
				$wk_password .= chr($num); // 文字コードを文字に変換
				$i++;
			}
		}
		$wk_fileinfo = "common/txt/infop.txt";
		$wk_subject.= "【feegle】パスワード再発行のお知らせ";
		// メールテキストを取得後、メールアドレス、仮パスワードを設定
		$wk_body = str_replace("%login_id%", $p_login_id, file_get_contents($wk_fileinfo));
		$wk_body = str_replace("%password%", $wk_password, $wk_body);
		// パスワード変更
		$sql = "UPDATE fg_user_table SET";
		$sql.= " password = \"".crypt(sha1($wk_password),TANE)."\"";
		$sql.= " WHERE login_id = \"".$p_login_id."\"";
		$sql.= " AND birth = \"".$p_birth."\"";
		$ret = $obj->Execute($sql);
		if (!$ret){
			//echo "sql=".$sql;
			$err = true;
			$msg_info.= ERR_UPD."[fg_user_table]\n";
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
				$msg_info.= "パスワード再発行メール送信時にエラーとなりました";
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
	}
	if ($err) {
		require("template/err.html"); // エラー画面テンプレート呼び出し
	}else{
		// パスワード再発行完了ページ
		header("Location: #page2");
	}
	exit;
}

include 'template/repasswd.html';
?>
