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
	<script type="text/javascript" src="scripts/flipsnap.js"></script>
	<script>
		$(function(){
	Flipsnap('.flipsnap');
	})
	</script>
	
	<style>
	.viewport {
	    width: 600px;
	    overflow: hidden;
	    margin: 0 auto;
	}
	.flipsnap {
	    width: 1000px; /* 200px(item width) * 5(item count) */
	}
	.item {
	    float: left;
	    width: 190px;
	    font-size: 50px;
	    text-align: center;
	    padding: 5px 0;
	    background: #efefef;
	    border: 1px solid #ffc753;
	    color: #666666;
	    cursor: pointer;
	}
	</style>
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
	</div><!-- /content -->
	
<?php @include("common/footer.html"); ?>
</div><!-- /page1 -->

</body>
</html>
