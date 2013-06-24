<?php
//
// 新規登録
//  本登録処理を行う
//  仮登録に送信されたメールのリンククリックより実行される
//

//session_start();

// ログイン状態のチェック(ログイン済ならログイン後トップページ)
if (isset($_SESSION['user_id'])) {
	header("Location: top.php");
	exit;
}

// 変数初期化
$chk_msg = "";
$msg_info = "";
$wk_selectjob = "";
$time = date("Y-m-d H:i:s"); // 日時取得

require("common/conf.php"); // 共通定義
require("common/PHPMailer_5.2.1/class.phpmailer.php"); // メール送信

//***********************************************
// 受信データをもとに変数の設定 GET
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "GET") { 
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_GET["rkey"])){$p_rkey=htmlspecialchars($_GET["rkey"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_rkey=preg_replace("/;/"," ",addslashes($p_rkey));
}
//***********************************************
// URLチェック
//***********************************************
if ($p_rkey == "") {
	$msg_info = "URLが正しくありません。";
	require("err.html"); // エラー画面テンプレート呼び出し
	exit;
}

//***********************************************
// ユーザー仮登録のキーチェック
//***********************************************
$sql = "SELECT * FROM user_reg_table";
$sql.= " WHERE rkey = \"".$p_rkey."\"";
$ret = $obj->Fetch($sql);
if (count($ret) == 1){
	foreach($ret as $key => $val){
		if ($val["status"] == 1) {
			$err = true;
			$msg_info = "既に登録済みです。";
		}else{
			$p_user_id = $row["user_id"];
			$p_nickname = $row["nickname"];
		}
	}
}else{
	$err = true;
	$msg_info = "仮登録のキーチェックにてエラーが発生しました。";
}

if (!$err) {
	//***********************************************
	// DB接続
	//***********************************************
	include 'common/database.php';
	$obj = new comdb();
	mysql_set_charset('utf8');
	
	//***********************************************
	// トランザクション開始
	//***********************************************
	
	//***********************************************
	// ユーザー本登録
	//***********************************************
	// ユーザーテーブル新規登録
	$sql_1 = "INSERT INTO user_table";
	$sql_1.= " (";
	$sql_2 = " VALUES(";
	// ユーザーID
	$sql_1.= "user_id";
	$sql_2.= "\"".$val["user_id"]."\"";
	// パスワード
	$sql_1.= ",password";
	$sql_2.= ",\"".$val["password"]."\"";
	// ログインID
	$sql_1.= ",nickname";
	$sql_2.= ",\"".$val["nickname"]."\"";
	// 生年月日
	$sql_1.= ",birth";
	$sql_2.= ",\"".$val["birth"]."\"";
	// 性別
	$sql_1.= ",gender";
	$sql_2.= ",\"".$val["gender"]."\"";
	// 中分類職業コード
	$sql_1.= ",mjob_cd";
	$sql_2.= ",\"".$val["mjob_cd"]."\"";
	// 大分類職業コード
	$sql_1.= ",ljob_cd";
	$sql_2.= ",\"".$val["ljob_cd"]."\"";
	// 登録日
	$sql_1.= ",rdate";
	$sql_2.= ",\"".$time."\"";
	$sql_1.= ")";
	$sql_2.= ")";
	$sql = $sql_1.$sql_2;
	$ret = $obj->Execute($sql);
	if (!$ret){
		//echo "sql=".$sql;
		$err = true;
		$msg_info.= ERR_REG."[user_table]\n";
	}
	
	// ユーザ登録テーブル更新
	$sql = "UPDATE user_reg_table SET";
	$sql.= " status = 1";
	$sql.= " WHERE user_id = \"".$p_user_id."\"";
	$ret = $obj->Execute($sql);
	if (!$ret){
		//echo "sql=".$sql;
		$err = true;
		$err_msg = ERR_UPD."[user_reg_table]\n";
	}
	
	//***********************************************
	// トランザクション終了
	//***********************************************
}

if (!$err) {
	//***********************************************
	// 本登録ご案内メール送信
	//***********************************************
	$wk_fileinfo = "common/txt/info_reg.txt"; // textファイルはsjis-win
	$wk_subject.= "【Feegle】へのご登録ありがとうございます。";
	$wk_body = str_replace("%email%", $p_user_id, file_get_contents($wk_fileinfo)); // メールテキストを取得後、メールアドレス（ログインID）を設定
	$wk_body = str_replace("%name_kanji%", mb_convert_encoding($p_nickname, "sjis-win", "UTF-8"), $wk_body); // 氏名漢字を設定
	
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
	$mail->FromName   = mb_encode_mimeheader("Feegle");
	$mail->AddReplyTo(YOUR_GMAIL_REFADDRESS, mb_encode_mimeheader(mb_convert_encoding("Reply-To", "JIS", "UTF-8")));
	$mail->Subject    = mb_convert_encoding($wk_subject, "JIS", "UTF-8");
	$mail->Body       = mb_convert_encoding($wk_body, "JIS", "sjis-win");
	$mail->AddAddress($p_user_id, mb_encode_mimeheader(mb_convert_encoding($p_user_id, "JIS", "UTF-8")));
	//$mail->AddBcc(YOUR_GMAIL_REFADDRESS);
	
	$flag = $mail -> Send();
	
	if (!$flag) {
		// メール送信エラー
		$err = true;
		$date = date("YmdHis"); // データ登録日時
		$err_msg = "新規会員登録完了メール送信時にエラーとなりました";
		$message = mb_convert_encoding($err_msg, "sjis-win", "UTF-8")."\n";
		$message.= mb_convert_encoding("メールアドレス:", "sjis-win", "UTF-8").$p_user_id."\n";
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

if ($err) {
	require("err.html"); // エラー画面テンプレート呼び出し
	exit;
}else{
	// 会員登録完了画面へリダイレクト
	header("Location: ./reg_end.php");
}
?>
?>
