<?php
//
// 書評を書く
//

session_start();

// 変数初期化
$p_isbn = "";
$p_book_name = "";
$p_feeling = "";
$get_book_cnt = 10;
$link_url = "wreviewsch.php?isbn=";
$html = "";
$message = "";
$arr_selected = array();

// ログイン状態のチェック
if (!isset($_SESSION['login'])) {
	header("Location: login.php?m=wr");
	exit;
}

require("common/conf.php"); // 共通定義

//***********************************************
// 受信データをもとに変数の設定 GET
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "GET") {
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_GET["isbn"])){$p_isbn=htmlspecialchars($_GET["isbn"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_isbn=preg_replace("/;/"," ",addslashes($p_isbn));
}

//***********************************************
// 受信データをもとに変数の設定 POST
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_POST["isbn"])){$p_isbn=htmlspecialchars($_POST["isbn"]);}
	if(isset($_POST["book_name"])){$p_book_name=htmlspecialchars($_POST["book_name"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_isbn=preg_replace("/;/"," ",addslashes($p_isbn));
	$p_book_name=preg_replace("/;/"," ",addslashes($p_book_name));
	
}

if (($p_isbn <> "") || ($p_book_name <> "")) {
	// 書籍情報取得
	require("amazonaws.php");
	
	$isbn10 = ISBNTran( $p_isbn );
	$data = amazon_info($p_book_name, $isbn10);
	$wk_book_cnt = count($data);
	if ($wk_book_cnt == 0) {
		$message = "対象の書籍は見つかりませんでした。";
		$wk_get_book_cnt = 0;
	}else{
		//if ($wk_book_cnt > $get_book_cnt) { // 取得書籍数：上限数
		//	$wk_get_book_cnt = $get_book_cnt;
		//	$message = $wk_book_cnt."件中".$get_book_cnt."件表示しています。";
		//}else{
			$wk_get_book_cnt = $wk_book_cnt;
			$message = $wk_book_cnt."件見つかりました。";
		//}
		if ($p_isbn <> "") {
			// 特定の書籍あり
			$arr_title[] = $data[0]->Title;
			$arr_author[] = $data[0]->Author;
			$arr_imageurl[] = $data[0]->ImageURL;
			$_SESSION["isbn"] = $p_isbn;
			$_SESSION["title"] = $data[0]->Title;
			$_SESSION["author"] = $data[0]->Author;
			$_SESSION["imageurl"] = $data[0]->ImageURL;
			$_SESSION["detailpageurl"] = $data[0]->DetailPageURL;
			$cnt = 0;
			if(isset($_SESSION["feeling"])){
				foreach ($ARR_FEELING as $key => $val) {
					if ($key == $_SESSION["feeling"]) {
						$arr_selected[$cnt] = " selected";
					}else{
						$arr_selected[$cnt] = "";
					}
					$cnt++;
				}
			}
			include 'template/wreview.html';
			exit;
			
		}else{
			// 特定の書籍なし
			for ($i=0; $i < $wk_get_book_cnt; $i++) { 
				$arr_title[] = $data[$i]->Title;
				$arr_author[] = $data[$i]->Author;
				$arr_imageurl[] = $data[$i]->ImageURL;
				$arr_linkurl[] = $link_url.$data[$i]->ISBN;
				//$html.= "			<form name=\"form1\" method=\"post\" action=\"\">\n";
				$html.= "			<li><a href=\"".$arr_linkurl[$i]."\">\n";
				$html.= "				<img src=\"".$arr_imageurl[$i]."\" />\n";
				$html.= "				<h3>".$arr_title[$i]."</h3>\n";
				$html.= "				<p>".$arr_author[$i]."</p>\n";
				$html.= "			</a></li>\n";
				//$html.= "			</form>\n";
			}
		}
	}
	include 'template/wreviewhyo.html';
	exit;
	
}else{
	require("common/sess_clear_review.php"); // 書評関連セッション情報クリア
}

include 'template/wreviewsch.html';
?>