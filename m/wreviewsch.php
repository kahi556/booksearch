<?php
//
// 書評を書く
//

session_start();

// 変数初期化
$p_isbn = "";
$p_book_name = "";
$p_wk = "";
$p_feeling = "";
$p_review = "";
$p_tag = "";
$wk_get_book_cnt = 0;
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
	if(isset($_POST["wk"])){$p_wk=htmlspecialchars($_POST["wk"]);}
	if(isset($_POST["feeling"])){$p_feeling=htmlspecialchars($_POST["feeling"]);}
	if(isset($_POST["review"])){$p_review=htmlspecialchars($_POST["review"]);}
	if(isset($_POST["tag"])){$p_tag=htmlspecialchars($_POST["tag"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_isbn=preg_replace("/;/"," ",addslashes($p_isbn));
	$p_book_name=preg_replace("/;/"," ",addslashes($p_book_name));
	$p_wk=preg_replace("/;/"," ",addslashes($p_wk));
	$p_feeling=preg_replace("/;/"," ",addslashes($p_feeling));
	$p_review=preg_replace("/;/"," ",addslashes($p_review));
	$p_tag=preg_replace("/;/"," ",addslashes($p_tag));
	
	$_SESSION["review"] = $p_review;
	$_SESSION["tag"] = $p_tag;
}

if ($p_wk == "f") {
	// 気分画像名の取り出し
	foreach ($ARR_FEELING as $key => $val) {
		if ($key == $p_feeling) {
			$sel_feeling = $val;
			break;
		}
	}
	$html_image.= "			<div class=\"ui-grid-a\">\n";
	$html_image.= "				<div class=\"ui-block-a\">\n";
	$html_image.= "					<p>気分: </p>\n";
	$html_image.= "				</div><!-- /ui-block-a -->\n";
	$html_image.= "				<div class=\"ui-block-b\">\n";
	$html_image.= "					【 ".$sel_feeling." 】<br />\n";
	$html_image.= "					<img src=\"images/".$p_feeling.".gif\" alt=\"".$sel_feeling."\" />\n";
	$html_image.= "				</div><!-- /ui-block-b -->\n";
	$html_image.= "			</div><!-- /ui-grid-a -->\n";
	$html_image.= "			<input type=\"hidden\" name=\"feeling\" value=\"".$p_feeling."\" />\n";
	
	include 'template/wreview.html';
	exit;
	
}elseif (($p_isbn <> "") || ($p_book_name <> "")) {
	// 書籍情報取得
	require("amazonaws.php");
	
	$isbn10 = ISBNTran( $p_isbn );
	$data = amazon_info($p_book_name, $isbn10);
	$wk_book_cnt = count($data);
	if ($wk_book_cnt == 0) {
		$message = "対象の書籍は見つかりませんでした。";
		$wk_get_book_cnt = 0;
		
	}else{
		$wk_get_book_cnt = $wk_book_cnt;
		$message = $wk_book_cnt."件見つかりました。";
		if ($p_isbn <> "") {
			// 特定の書籍あり
			$_SESSION["isbn"] = $p_isbn;
			$_SESSION["title"] = $data[0]->Title;
			$_SESSION["author"] = $data[0]->Author;
			$_SESSION["imageurl"] = $data[0]->ImageURL;
			$_SESSION["detailpageurl"] = $data[0]->DetailPageURL;
			$cnt = 0;
			if(isset($_SESSION["feeling"])){
				// 気分画像名の取り出し
				foreach ($ARR_FEELING as $key => $val) {
					if ($key == $_SESSION["feeling"]) {
						$sel_feeling = $val;
						break;
					}
				}
				// 気分画像特定済
				$html_image.= "			<div class=\"ui-grid-a\">\n";
				$html_image.= "				<div class=\"ui-block-a\">\n";
				$html_image.= "					<p>気分: </p>\n";
				$html_image.= "				</div><!-- /ui-block-a -->\n";
				$html_image.= "				<div class=\"ui-block-b\">\n";
				$html_image.= "					<img src=\"images/".$_SESSION["feeling"].".gif\" alt=\"".$sel_feeling."\" />\n";
				$html_image.= "				</div><!-- /ui-block-b -->\n";
				$html_image.= "			</div><!-- /ui-grid-a -->\n";
				$html_image.= "		<input type=\"hidden\" name=\"feeling\" value=\"".$_SESSION["feeling"]."\" />\n";
				
			}else{
				// 気分画像編集
				$html_image.= "		<div data-role=\"fieldcontain\">\n";
				$html_image.= "			<p>気分: </p>\n";
				$html_image.= "			<div class=\"viewport\">\n";
				$html_image.= "				<div class=\"flipsnap\">\n";
				foreach ($ARR_FEELING as $key => $val) {
					$html_image.= "					<div class=\"item\">";
					$html_image.= "<a href=\"javascript:feelingsch('".$key."');\">\n";
					$html_image.= "					<img src=\"images/".$key.".gif\" alt=\"".$val."\" /></a></div>\n";
				}
				$html_image.= "				</div><!-- /flipsnap -->\n";
				$html_image.= "			</div><!-- /viewport -->\n";
				$html_image.= "		</div><!-- /fieldcontain -->\n";
				$html_image.= "		<input type=\"hidden\" name=\"wk\" value=\"f\" />\n";
				$html_image.= "		<input type=\"hidden\" name=\"feeling\" value=\"\" />\n";
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
				$html.= "			<li><a rel=\"external\" href=\"".$arr_linkurl[$i]."\">\n";
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