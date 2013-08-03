<?php
//
// 書評一覧表示
//

session_start();

// ログイン状態のチェック
//if (!isset($_SESSION['login'])) {
//	header("Location: login?m=sb");
//	exit;
//}

// 変数初期化
$p_book_id = "";
$p_wk = "";
$p_word_j = "";
$wk_feeling_image = "";
$wk_book_review = "";
$wk_title = "";
$wk_author = "";
$wk_imageurl = "";
$wk_linkurl = "";
$wk_keyword = "";
$wk_feeling_image = "";
$wk_link = "";
$wk_thanks_cnt = 0;
$btn_mod = "";
$arr_nickname = array();
$arr_thanks_cnt = array();
$arr_feeling_image = array();
$arr_link = array();
$arr_book_review = array();
$arr_keyword = array();
$arr_selected = array();
$arr_keyword_temp = array();
$arr_feeling_temp = array();
$err_title = "書評一覧表示";

require("common/conf.php"); // 共通定義

//***********************************************
// 受信データをもとに変数の設定 GET
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "GET") { 
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_GET["id"])){$p_book_id=htmlspecialchars($_GET["id"]);}
	if(isset($_GET["wk"])){$p_wk=htmlspecialchars($_GET["wk"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_book_id=preg_replace("/;/"," ",addslashes($p_book_id));
	$p_wk=preg_replace("/;/"," ",addslashes($p_wk));
}
if ($p_book_id == "") {
	$msg_info = "不正な処理です。";
	require("template/err.html"); // エラー画面テンプレート呼び出し
	exit;
}

// ログイン状態のチェック(ログイン未で書評変更ならログインページ)
if (!isset($_SESSION['login']) && ($p_wk == "mod")) {
	header("Location: login?");
	exit;
}

//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();

//***********************************************
// 書評を登録した本検索
//***********************************************
$sql = "SELECT brt.book_review,brt.thanks_cnt,brt.tag,brt.feeling";
$sql.= ",bt.book_name,bt.imageurl,bt.detailpageurl,ut.nickname";
$sql.= ",at.author_name";
$sql.= " FROM fg_book_review_table brt";
$sql.= " INNER JOIN fg_book_table bt ON bt.book_id = brt.book_id";
$sql.= " INNER JOIN fg_user_table ut ON ut.user_id = brt.user_id";
$sql.= " LEFT JOIN fg_author_table at ON at.author_id = bt.author_id";
$sql.= " WHERE brt.book_id = \"".$p_book_id."\"";
if (isset($_SESSION['login'])) {
	// ログイン中のみの条件
	$sql.= " AND brt.user_id = \"".$_SESSION["user_id"]."\"";
}

$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	//$html.= "<div data-role=\"collapsible-set\" data-theme=\"d\" data-content-theme=\"d\">\n";
	foreach($ret as $key => $val){
		if ($key == 0) {
			$wk_book_name = $val["book_name"];
			$wk_imageurl = $val["imageurl"];
			$wk_author_name = $val["author_name"];
			$wk_detailpageurl = $val["detailpageurl"];
		//	$html.= "	<div data-role=\"collapsible\" data-collapsed=\"false\">\n";
		//}else{
		//	$html.= "	<div data-role=\"collapsible\">\n";
		}
		$wk_thanks_cnt += $val["thanks_cnt"];
		$arr_thanks_cnt[$key] = $val["thanks_cnt"];
		$arr_book_review[$key] = $val["book_review"];
		$arr_nickname[$key] = $val["nickname"];
		
		// 気分を日本語変換
		$flg_match = false;
		for($i=0; $i<count($arr_feeling_temp); $i++){
			if ($val["feeling"] == $arr_feeling_temp[$i]) { // 重複気分があるかチェック
				$flg_match = true;
				break;
			}
		}
		if (!$flg_match) {
			// 重複気分がなかった場合
			foreach($ARR_FEELING as $key1 => $val1){
				if ($key1 == $val["feeling"]) {
					$wk_feeling_image.= "【 ".$val1." 】<br /><a rel=\"external\" href=\"./sbook?wk=f&wd=".$val["feeling"]."\"><img src=\"images/".$val["feeling"].".gif\"></a><br />\n";						break;
				}
			}
			$arr_feeling_temp[] = $val["feeling"];
		}
		
		// 気分を日本語変換
		$wk_feeling = $val["feeling"];
		foreach($ARR_FEELING as $key1 => $val1){
			if ($key1 == $wk_feeling) {
				$arr_feeling_image[$key].= "【 ".$val1." 】<br /><a rel=\"external\" href=\"./sbook?wk=f&wd=".$val["feeling"]."\"><img src=\"images/".$val["feeling"].".gif\"></a><br />\n";
				break;
			}
		}
		
		// タグをキーワードとして各リンクに分割
		// 重複したキーワードは除く
		if ($val["tag"] <> "") {
			$arr_keyword = explode(",", $val["tag"]);
			foreach($arr_keyword as $key1 => $val1){
				$flg_match = false;
				for($i=0; $i<count($arr_keyword_temp); $i++){
					if ($val1 == $arr_keyword_temp[$i]) { // 重複キーワードがあるかチェック
						$flg_match = true;
						break;
					}
				}
				if (!$flg_match) {
					// 重複キーワードがなかった場合
					$wk_link.= "<a rel=\"external\" href=\"./sbook?wk=w&wd=".urlencode($val1)."\">".$val1."</a> ";
					$arr_keyword_temp[] = $val1;
				}
			}
		}
		// タグをキーワードとして各リンクに分割
		if ($val["tag"] <> "") {
			$arr_keyword = explode(",", $val["tag"]);
			foreach($arr_keyword as $key1 => $val1){
				$arr_link[$key].= "<a rel=\"external\" href=\"./sbook?wk=w&wd=".urlencode($val1)."\">".$val1."</a> ";
			}
		}
	}
	
	// 書籍情報
	$html.= "	<h3>".$wk_book_name."</h3>\n";
	$html.= "	<div data-role=\"none\" class=\"ui-body ui-body-c\">\n";
	$html.= "		<table data-role=\"table\" id=\"book-table\" data-mode=\"reflow\" class=\"ui-responsive table-stroke\">\n";
	$html.= "			<thead>\n";
	$html.= "				<tr>\n";
	$html.= "					<th>&nbsp;</th>\n";
	$html.= "					<th>著者</th>\n";
	$html.= "					<th>Thanks</th>\n";
	$html.= "					<th>イメージ</th>\n";
	$html.= "					<th>Keyword</th>\n";
	$html.= "				</tr>\n";
	$html.= "			</thead>\n";
	$html.= "			<tbody>\n";
	$html.= "				<tr>\n";
	$html.= "					<td>\n";
	$html.= "						<a href=\"".$wk_detailpageurl."\" target=\"_blank\">\n";
	$html.= "						<img src=\"".$wk_imageurl."\"></a>\n";
	$html.= "					</td>\n";
	$html.= "					<td>".$wk_author_name."</td>\n";
	$html.= "					<td>".$wk_thanks_cnt."</td>\n";
	$html.= "					<td>".$wk_feeling_image."</td>\n";
	$html.= "					<td>".$wk_link."</td>\n";
	$html.= "				</tr>\n";
	$html.= "			</tbody>\n";
	$html.= "		</table>\n";
	$html.= "	</div>\n";
	
	for($i=0; $i<count($arr_book_review); $i++) {
		$html.= "	<div data-role=\"none\" class=\"ui-body ui-body-c\">\n";
		$html.= "		<table data-role=\"table\" id=\"book-table\" data-mode=\"reflow\" class=\"ui-responsive table-stroke\">\n";
		$html.= "			<thead>\n";
		$html.= "				<tr>\n";
		$html.= "					<th>愛称</th>\n";
		$html.= "					<th>Thanks</th>\n";
		$html.= "					<th>イメージ</th>\n";
		$html.= "					<th>Keyword</th>\n";
		$html.= "					<th>書評</th>\n";
		$html.= "				</tr>\n";
		$html.= "			</thead>\n";
		$html.= "			<tbody>\n";
		$html.= "				<tr>\n";
		$html.= "					<td>".$arr_nickname[$i]."</td>\n";
		$html.= "					<td>".$arr_thanks_cnt[$i]."</td>\n";
		$html.= "					<td>".$arr_feeling_image[$i]."</td>\n";
		$html.= "					<td>".$arr_link[$i]."</td>\n";
		$html.= "					<td>".$arr_book_review[$i]."</td>\n";
		$html.= "				</tr>\n";
		$html.= "			</tbody>\n";
		$html.= "		</table>\n";
		$html.= "	</div>\n";
	}
	//$html.= "</div>\n";
}

// ログイン状態のチェック(ログイン済なら内容変更ボタン表示)
if (isset($_SESSION['login'])) {
	$btn_mod.= "		";
	$btn_mod.= "<a rel=\"external\" href=\"sreview?id=".$p_book_id."&wk=mod\" data-role=\"button\">内容変更</a>\n";
}

if ($p_wk == "mod") {
	// 内容変更表示
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
}else{
	// 検索結果表示
	if(isset($_SESSION["feeling"])){
		foreach ($ARR_FEELING as $key => $val) {
			if ($key == $_SESSION["feeling"]) {
				$p_word_j = $val;
				break;
			}
		}
	}
	include 'template/sreview.html';
}
?>
