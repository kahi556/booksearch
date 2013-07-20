<?php
//
// ログイン前トップページ
//

session_start();

require("common/conf.php"); // 共通定義

// ログイン状態のチェック(ログイン済ならログイン後トップページ)
if (isset($_SESSION['login'])) {
	header("Location: top.php");
	exit;
}

// 変数初期化
$arr_isbn = array();
$html_image = "";
$max_disp_suu = 5; // 書籍最大表示数

require("common/sess_clear.php"); // セッション情報クリア

// おすすめ書籍情報取得
//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();

//***********************************************
// 書籍情報検索
//***********************************************
$sql = "SELECT book_id,imageurl";
$sql.= " FROM fg_book_table";
$sql.= " ORDER BY rand() limit 0,".$max_disp_suu;
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		$arr_isbn[] = $val["isbn"];
		$html_image.= "			";
		$html_image.= "<div class=\"item\"><a rel=\"external\" href=\"sreview.php?id=".$val["book_id"]."\">";
		$html_image.= "			";
		$html_image.= "<img src=\"".$val["imageurl"]."\"></a></div>\n";
	}
}

//require("amazonaws.php");
//foreach($arr_isbn as $key => $val){
//	// amazon情報取得
//	$recom_books_keyw = "";
//	$recom_books_isbn = $val;
//	$isbn10 = ISBNTran( $recom_books_isbn );
//	$data = amazon_info($recom_books_keyw, $isbn10);
//	//$arr_title[] = $data[0]->Title;
//	//$arr_author[] = $data[0]->Author;
//	//$arr_imageurl[] = $data[0]->ImageURL;
//	//$arr_linkurl[] = $data[0]->DetailPageURL;
//	$html.="		<li><a href=\"".$data[0]->DetailPageURL."\">\n";
//	$html.="			<img src=\"".$data[0]->ImageURL."\" />\n";
//	$html.="			<h3>".$data[0]->Title."</h3> \n";
//	$html.="			<p>".$data[0]->Author."</p>\n";
//	$html.="		</a></li>\n";
//}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta http-equiv="content-language" content="ja">
	<meta charset="utf-8">
	<title>feegle | 感覚的に書籍を検索</title>
	<meta name="description" content="feegle">
<?php @include("common/jquery.html"); ?>
	<script type="text/javascript" src="scripts/sns.js"></script>
	<script type="text/javascript" src="scripts/flipsnap.js"></script>
	<link rel="stylesheet" href="css/flipsnap5.css" />
	<link rel="stylesheet" href="css/clearfix.css" />
	<script>
	$(function(){
		Flipsnap('.flipsnap');
	});
	</script>
</head>
<body>

<div data-role="page" id="page1" data-theme="c">
<?php @include("common/header.html"); ?>

	<div data-role="content" data-theme="c">
		<p>feegleは<br>
		感覚的に書籍を検索できるサービスです。
		</p>
		<br>
		<div class="viewport">
			<p>おすすめ</p>
    		<div class="flipsnap">
<?php echo $html_image ?>
    		</div>
		</div>
		<br>
		<!-- <a rel="external" href="osusume.php" data-role="button" class="">おすすめ本登録</a> -->
		<!--Social Button-->
		<div class="block clearfix">
			<div id="facebook_like"></div>
			<div id="twitter"></div>
			<div id="evernote"></div>
			<div id="hatena"></div>
			<div id="gree"></div>
			<!-- +1 ボタン を表示したい位置に次のタグを貼り付けてください。 -->
			<div class="g-plusone" data-size="medium"></div>
			<!-- 最後の +1 ボタン タグの後に次のタグを貼り付けてください。 -->
			<script type="text/javascript">
			  window.___gcfg = {lang: 'ja'};
			  (function() {
			    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			    po.src = 'https://apis.google.com/js/plusone.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			  })();
			</script>
		</div><!-- /block clearfix -->
		<!--/Social Button-->
	</div><!-- /content -->
	
<?php @include("common/footer.html"); ?>
</div><!-- /page1 -->

</body>
</html>
