<?php
//
// 共通定義（UTF-8で保存）
//   【本番用】
//   本番、テスト環境の切り替えは「ドメイン,MySQL」の設定を変更
//

// GMAIL
define("YOUR_GMAIL_REFADDRESS","info@xxxxx.com");
define("YOUR_GMAIL_ADDRESS","send@xxxxxx.com");
define("YOUR_GMAIL_PASS","yyyyyy");
define("SMTPAUTH",true);
define("SMTPSECURE","ssl");
define("SMTPHOST","smtp.gmail.com");
define("SMTPPORT",465);
define("SMTPCHARSET","iso-2022-jp");
define("SMTPENCODING","7bit");

// URL
define("URL", "{$_SERVER['HTTP_HOST']}");
// ドメイン
define("DOMAIN_SSL", "https://xxxxxx.com");
define("DOMAIN_DIAGNOSIS", "https://xxxxxx.com");
define("SUBDOMAIN", "www.");

// 暗号化のタネ
define("TANE", "Jy5D32S9kx"); // パスワードのタネ
define("TANE_MAIL", "F6y8iD3mNv"); // メールアドレスのタネ

// 処理完了メッセージ
define("WORK_S"  , "<div style='color:#ff4500; font-size:16px;'>");
define("WORK_E"  , "<br /></div>");
define("WORK_NML", "が完了しました");
// エラーメッセージ
define("ERR_S"   , "<div style='color:#ff0000;'>");
define("ERR_E"   , "<br /></div>");
define("ERR_AREA", "の値が範囲外です");
define("ERR_DBSL", "DB検索エラー！！！");
define("ERR_DCHK", "の日付形式が妥当ではありません");
define("ERR_DEL" , "DB削除エラー！！！");
define("ERR_DELE", "ファイル削除できませんでした");
define("ERR_DUPL", "は既に登録されています");
define("ERR_DISP", "表示対象ファイルがみつかりません");
define("ERR_EISU", "は半角英数を入力して下さい");
define("ERR_EIJI", "は半角英字を入力して下さい");
define("ERR_FUTU", "が未来日です");
define("ERR_FILE", "取込可能なファイルではありません");
define("ERR_IND" , "は必須です");
define("ERR_INJU", "が不正です");
define("ERR_INP" , "を入力して下さい");
define("ERR_LENG", "の長さが不正です");
define("ERR_MAIL", "メールアドレスを正しく入力して下さい");
define("ERR_NODT", "該当データはありません");
define("ERR_NOFL", "CSVで指定された取込ファイルがありません");
define("ERR_NOZP", "ZIP展開の取込ファイルがCSVにありません");
define("ERR_NULL", "が設定されていません");
define("ERR_REG" , "DB登録エラー！！！");
define("ERR_ROLE", "権限がありません");
define("ERR_SEL" , "を選択して下さい");
define("ERR_SU"  , "は半角数値を入力して下さい");
define("ERR_TCHK", "の時間形式が妥当ではありません");
define("ERR_UPCK", "アップロードチェックエラー！！！");
define("ERR_UPD" , "DB更新エラー！！！");
define("ERR_UPLD", "アップロードエラー！！！");
define("ERR_UPSL", "アップロード対象ファイルにＣＳＶファイルが必要です");

// フラグ
define("FLAG_ON", 1); // オン
define("FLAG_OFF", 2); // オフ

// セッション設定
ini_set('session.gc_maxlifetime', 1800);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

// コード変換
function convert($str) {
    return mb_convert_encoding($str, mb_internal_encoding(), "auto");
}
?>