<?php
//
// 本を検索
//

session_start();

// ログイン状態のチェック
//if (!isset($_SESSION['login'])) {
//	header("Location: login.php?m=sb");
//	exit;
//}

// 変数初期化
$arr_linkurl = array();
$arr_isbn = array();
$arr_imageurl = array();
$arr_title = array();
$arr_author_name = array();
$arr_book_review = array();
$ARR_FEELING = array();
$arr_keyword = array();
$p_nickname = "";
$p_review_posts_cnt = "";
$p_thanks_cnt = "";
$p_tag = "";
$p_book_name = "";
$p_book_review = "";
$p_isbn = "";
$p_feeling = "";
$p_author_name = "";
$p_wk = "";
$p_word = "";
$p_brno = "";
$p_user_id = "";
$html = "";
$wk_lkeyword  = "";
$feeling_image = "";
$time = date("Y-m-d H:i:s"); // 日時取得

require("common/conf.php"); // 共通定義

//***********************************************
// 受信データをもとに変数の設定 GET
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "GET") { 
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_GET["wk"])){$p_wk=htmlspecialchars($_GET["wk"]);}
	if(isset($_GET["wd"])){$p_word=htmlspecialchars($_GET["wd"]);}
	if(isset($_GET["brno"])){$p_brno=htmlspecialchars($_GET["brno"]);}
	if(isset($_GET["uid"])){$p_user_id=htmlspecialchars($_GET["uid"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_wk=preg_replace("/;/"," ",addslashes($p_wk));
	$p_word=preg_replace("/;/"," ",addslashes($p_word));
	$p_brno=preg_replace("/;/"," ",addslashes($p_brno));
	$p_user_id=preg_replace("/;/"," ",addslashes($p_user_id));
}

//***********************************************
// 受信データをもとに変数の設定 POST
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
	// 特殊文字をHTMLエンティティに変換（セキュリティ対策）
	if(isset($_POST["wk"])){$p_wk=htmlspecialchars($_POST["wk"]);}
	if(isset($_POST["wd"])){$p_word=htmlspecialchars($_POST["wd"]);}
	// 文字のエスケープ（セキュリティ対策）
	$p_wk=preg_replace("/;/"," ",addslashes($p_wk));
	$p_word=preg_replace("/;/"," ",addslashes($p_word));
}

//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();

//***********************************************
// サンクス
//***********************************************
if ($p_wk == "th") {
	//***********************************************
	// トランザクション開始
	//***********************************************
	$obj->BeginTran();
	
	//***********************************************
	// サンクス履歴登録
	//***********************************************
	$sql = "INSERT INTO fg_thanks_history_table";
	$sql.= " (book_review_no,user_id,rdate) VALUES";
	$sql.= " (\"".$p_brno."\",\"".$_SESSION["user_id"]."\",\"".$time."\")";
	$ret = $obj->Execute($sql);
	if (!$ret){
		$err = true;
		$msg_info.= "既に登録済です\n";
	}
	//***********************************************
	// 書評更新（サンクス）
	//***********************************************
	$sql = "UPDATE fg_book_review_table SET";
	$sql.= " thanks_cnt = thanks_cnt + 1";
	$sql.= " WHERE book_review_no = \"".$p_brno."\"";
	$ret = $obj->Execute($sql);
	if (!$ret){
		$err = true;
		$msg_info.= ERR_UPD."[fg_book_review_table]\n";
	}
	//***********************************************
	// ユーザー更新（サンクス）
	//***********************************************
	$sql = "UPDATE fg_user_table SET";
	$sql.= " thanks_cnt = thanks_cnt + 1";
	$sql.= " WHERE user_id = \"".$p_user_id."\"";
	$ret = $obj->Execute($sql);
	if (!$ret){
		$err = true;
		$msg_info.= ERR_UPD."[fg_user_table]\n";
	}
	
	if ($err) {
		$obj->RollBack();
		$msg_info.= "Thanks!に失敗しました\n";
	}else{
		$obj->Commit();
		$msg_info.= "Thanks!しました。\n";
	}
}



//***********************************************
// 気分orキーワード検索
//***********************************************
if (($p_wk <> "th") && ($p_word <> "")) {
	$sql = "SELECT brt.book_review_no as brtbook_review_no";
	$sql.= ",brt.book_review,brt.book_id";
	$sql.= ",brt.tag,brt.thanks_cnt,brt.feeling,brt.user_id";
	$sql.= ",bt.book_name,bt.imageurl,bt.detailpageurl";
	$sql.= ",at.author_name,ut.nickname";
	$sql.= ",tht.book_review_no as thtbook_review_no";
	$sql.= ",tht.delete_flg as thtdelete_flg";
	$sql.= " FROM fg_book_review_table brt";
	$sql.= " INNER JOIN fg_book_table bt ON bt.book_id = brt.book_id";
	$sql.= " INNER JOIN fg_user_table ut ON ut.user_id = brt.user_id";
	$sql.= " LEFT JOIN fg_author_table at ON at.author_id = bt.author_id";
	$sql.= " LEFT JOIN fg_thanks_history_table tht";
	$sql.= " ON tht.book_review_no = brt.book_review_no";
	$sql.= " AND tht.user_id = \"".$_SESSION["user_id"]."\"";
	if ($p_wk == "f") {
		// 気分検索
		$sql.= " WHERE brt.feeling = \"".$p_word."\"";
		// 気分を日本語変換
		$wk_feeling = $p_word;
		foreach($ARR_FEELING as $key1 => $val1){
			if ($key1 == $wk_feeling) {
				$wk_lkeyword = $val1;
				break;
			}
		}
	}else{
		// キーワード検索(複数語検索対応)
		$p_word = str_replace("　", " ", urldecode($p_word)); // 全角スペース→半角
		$arr_keyword = explode(" ", $p_word);
		foreach($arr_keyword as $key => $val){
			if ($key == 0) {
				$sql.= " WHERE (brt.tag LIKE \"%".$val."%\"";
			}else{
				$sql.= " OR brt.tag LIKE \"%".$val."%\"";
			}
			$sql.= " OR brt.book_review LIKE \"%".$val."%\"";
		}
		$sql.= ")";
		$wk_lkeyword = $p_word;
	}
	//if (isset($_SESSION['login'])) {
	//	// ログイン中のみの条件
	//	$sql.= " AND brt.user_id = \"".$_SESSION["user_id"]."\"";
	//}
	$ret = $obj->Fetch($sql);
	if (count($ret) <> 0){
		$html.= "<div data-role=\"collapsible-set\" data-theme=\"e\" data-content-theme=\"d\">\n";
		foreach($ret as $key => $val){
			$wk_mkeyword = "";
			if ($key == 0) {
				$html.= "	<div data-role=\"collapsible\" data-collapsed=\"false\">\n";
			}else{
				$html.= "	<div data-role=\"collapsible\">\n";
			}
			
			// タグをキーワードとして各リンクに分割
			if ($val["tag"] <> "") {
				$arr_keyword = explode(",", $val["tag"]);
				foreach($arr_keyword as $key1 => $val1){
					$wk_mkeyword.= "<a rel=\"external\" href=\"./sbook.php?wk=w&wd=".urlencode($val1)."\">".$val1."</a> ";
				}
			}
			
			// 気分を日本語変換
			$wk_feeling = $val["feeling"];
			foreach($ARR_FEELING as $key1 => $val1){
				if ($key1 == $wk_feeling) {
					$wk_feeling_j = $val1;
					break;
				}
			}
			
			$html.= "		<h3>".$val["book_name"]."</h3>\n";
			$html.= "		<table data-role=\"table\" id=\"book-table\" data-mode=\"reflow\" class=\"ui-responsive table-stroke\">\n";
			$html.= "			<thead>\n";
			$html.= "				<tr>\n";
			$html.= "					<th>&nbsp;</th>\n";
			$html.= "					<th>著者</th>\n";
			$html.= "					<th>Thanks</th>\n";
			$html.= "					<th>イメージ</th>\n";
			$html.= "					<th>Keyword</th>\n";
			$html.= "					<th>愛称</th>\n";
			$html.= "					<th>書評</th>\n";
			$html.= "				</tr>\n";
			$html.= "			</thead>\n";
			$html.= "			<tbody>\n";
			$html.= "				<tr>\n";
			$html.= "					<td>\n";
			$html.= "						<a href=\"".$val["detailpageurl"]."\" target=\"_blank\">\n";
			$html.= "						<img src=\"".$val["imageurl"]."\"></a>\n";
			$html.= "					</td>\n";
			$html.= "					<td>".$val["author_name"]."</td>\n";
			$html.= "					<td>".$val["thanks_cnt"]."</td>\n";
			$html.= "					<td>【 ".$wk_feeling_j." 】<br /><a rel=\"external\" href=\"./sbook.php?wk=f&wd=".$val["feeling"]."\"><img src=\"images/".$val["feeling"].".gif\"></a></td>\n";
			$html.= "					<td>".$wk_mkeyword."</td>\n";
			$html.= "					<td>".$val["nickname"]."</td>\n";
			$html.= "					<td>".$val["book_review"]."\n";
			if ((isset($_SESSION["login"])) && 
					($val["user_id"] <> $_SESSION["user_id"])) {
				// ログイン後で、
				// 自身が投稿していなかった場合
				if (($val["thtbook_review_no"] == "") ||
						($val["thtdelete_flg"] == 1)) {
					// Thanks!リンククリック未、またはThanks!取り消し済
					// Thanks!リンクを表示
					$html.= "					<div id=\"thanks".$val["brtbook_review_no"]."\"><a href=\"javascript:cThanks_y('".$val["brtbook_review_no"]."','".$val["user_id"]."')\">Thanks!</a></div>\n";
				}else{
					// Thanks!リンククリック済
					// Thanks!取り消しリンクを表示
					$html.= "					<div id=\"thanks".$val["brtbook_review_no"]."\"><a href=\"javascript:cThanks_n('".$val["brtbook_review_no"]."','".$val["user_id"]."')\">Thanks!取り消し</a></div>\n";
				}
			}
			$html.= "					</td>\n";
			$html.= "				</tr>\n";
			$html.= "			</tbody>\n";
			$html.= "		</table>\n";
			$html.= "	</div>\n";
		}
		$html.= "</div>\n";
	}else{
		$html.= "<p>見つかりませんでした</p>\n";
	}
	include 'template/sbook2.html';
	exit;
}else{
	if ($p_word == "") {
		// 気分画像編集
		$html_image.= "			";
		$html_image.= "<div class=\"viewport\">\n";
		$html_image.= "				";
		$html_image.= "<div class=\"flipsnap\">\n";
		foreach ($ARR_FEELING as $key => $val) {
			$html_image.= "					";
			$html_image.= "<div class=\"item\">";
			$html_image.= "<a rel=\"external\" href=\"sbook.php?wk=f&wd=".$key."\">\n";
			$html_image.= "					";
			$html_image.= "<img src=\"images/".$key.".gif\"></a></div>\n";
		}
		$html_image.= "				";
		$html_image.= "</div><!-- /flipsnap -->\n";
		$html_image.= "			";
		$html_image.= "</div><!-- /viewport -->\n";
	}
}

include 'template/sbook.html';
?>