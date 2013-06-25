<?php
//
// ログイン前トップページ
//

session_start();

// ログイン状態のチェック(ログイン済ならログイン後トップページ)
if (isset($_SESSION['login'])) {
	header("Location: top.php");
	exit;
}

// 変数初期化
$arr_isbn = array();
$html = "";

require("common/sess_clear.php"); // セッション情報クリア

// おすすめ書籍情報取得
//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();
mysql_set_charset('utf8');

//***********************************************
// 書籍情報検索
//***********************************************
$sql = "SELECT isbn";
$sql.= " FROM book_table";
$sql.= " ORDER BY rand() limit 0,5";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		$arr_isbn[] = $val["isbn"];
	}
}
require("amazonaws.php");
foreach($arr_isbn as $key => $val){
	$recom_books_keyw = "";
	$recom_books_isbn = $val;
	$isbn10 = ISBNTran( $recom_books_isbn );
	$data = amazon_info($recom_books_keyw, $isbn10);
	//$arr_title[] = $data[0]->Title;
	//$arr_author[] = $data[0]->Author;
	//$arr_imageurl[] = $data[0]->ImageURL;
	//$arr_linkurl[] = $data[0]->DetailPageURL;
	$html.="		<li><a href=\"".$data[0]->DetailPageURL."\">\n";
	$html.="			<img src=\"".$data[0]->ImageURL."\" />\n";
	$html.="			<h3>".$data[0]->Title."</h3> \n";
	$html.="			<p>".$data[0]->Author."</p>\n";
	$html.="		</a></li>\n";
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>feegle</title>
	<meta name="description" content="feegle">
<?php @include("common/jquery.html"); ?>
</head>
<body>

<div data-role="page" id="page1" data-theme="c">
<?php @include("common/header.html"); ?>
	<div data-role="content" data-theme="c">
		<p>feegleは<br>
		感覚的に書籍を検索できるサービスです。
		</p>
		<br>
		<ul data-role="listview">
<?php echo $html ?>
		</ul>
		<br>
		<!-- <a rel="external" href="osusume.php" data-role="button" class="">おすすめ本登録</a> -->
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d">
<?php @include("common/footer.html"); ?>
	</div><!-- /footer -->
</div><!-- /page1 -->

</body>
</html>
