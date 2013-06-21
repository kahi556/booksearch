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

require("common/sess_clear.php"); // セッション情報クリア

// おすすめ書籍情報取得
require("amazonaws.php");
$recom_books_keyw = "IT起業";
$recom_books_isbn = "9784798029351";
$isbn10 = ISBNTran( $recom_books_isbn );
$data = amazon_info($recom_books_keyw, $isbn10);
for ($i=0; $i < 3; $i++) { 
	$arr_title[] = $data[$i]->Title;
	$arr_author[] = $data[$i]->Author;
	$arr_imageurl[] = $data[$i]->ImageURL;
	$arr_linkurl[] = $data[$i]->DetailPageURL;
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
	<div data-role="header" data-theme="d">
		<h1>feegle</h1>
		<a rel="external" href="../" data-role="button" class="ui-btn-right">PC版</a>
<?php @include("common/header.html"); ?>
	</div><!-- /header -->
	
	<div data-role="content" data-theme="c">
		<p>feegleは<br>
		書籍検索の仕方（あり方）を変えることにより、<br>
		必要な人に必要な物（書籍情報）を届け、<br>
		誰かの生き方、心情、行動に好影響を与える、<br>
		または好影響を受けることを実感できるサービスです。<br>
		</p>
		<br>
		<ul data-role="listview">
			<li><a href="<?php echo $arr_linkurl[0] ?>">
				<img src="<?php echo $arr_imageurl[0] ?>" />
				<h3><?php echo $arr_title[0] ?></h3> 
				<p><?php echo $arr_author[0] ?></p> 
			</a></li> 
			<li><a href="<?php echo $arr_linkurl[1] ?>">
				<img src="<?php echo $arr_imageurl[1] ?>" />
				<h3><?php echo $arr_title[1] ?></h3> 
				<p><?php echo $arr_author[1] ?></p> 
			</a></li> 
			<li><a href="<?php echo $arr_linkurl[2] ?>">
				<img src="<?php echo $arr_imageurl[2] ?>" />
				<h3><?php echo $arr_title[2] ?></h3> 
				<p><?php echo $arr_author[2] ?></p> 
			</a></li> 
		</ul>
		<br>
		<a rel="external" href="login.php?m=os" data-role="button" class="">おすすめ本登録</a>
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d">
<?php @include("common/footer.html"); ?>
	</div><!-- /footer -->
</div><!-- /page1 -->

<div data-role="page" id="page2" data-theme="c">
	<div data-role="header" data-theme="d">
		<h1></h1>
	</div><!-- /header -->
	
	<div data-role="content" data-theme="c">
		<h4>
		</h4>
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d">
<?php @include("common/footer.html"); ?>
	</div><!-- /footer -->
</div><!-- /page2 -->

<div data-role="page" id="page3" data-theme="c">
	<div data-role="header" data-theme="d">
		<h1></h1>
		<a href="#page1" data-rel="back" data-icon="arrow-l">Back</a>
	</div><!-- /header -->
	
	<div data-role="content" data-theme="c">
		<h4>
		</h4>
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d">
<?php @include("common/footer.html"); ?>
	</div><!-- /footer -->
</div><!-- /page3 -->

<div data-role="page" id="page4" data-theme="c">
	<div data-role="header" data-theme="d">
		<h1></h1>
		<a href="#page1" data-rel="back" data-icon="arrow-l">Back</a>
	</div><!-- /header -->
	
	<div data-role="content" data-theme="c">
		<h4>
		</h4>
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d">
<?php @include("common/footer.html"); ?>
	</div><!-- /footer -->
</div><!-- /page4 -->

<div data-role="page" id="page5" data-theme="c">
	<div data-role="header" data-theme="d">
		<h1></h1>
		<a href="#page1" data-rel="back" data-icon="arrow-l">Back</a>
	</div><!-- /header -->
	
	<div data-role="content" data-theme="c">
		<h4>
		</h4>
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d">
<?php @include("common/footer.html"); ?>
	</div><!-- /footer -->
</div><!-- /page5 -->

<div data-role="page" id="page6" data-theme="c">
	<div data-role="header" data-theme="d">
		<h1></h1>
		<a href="#page1" data-rel="back" data-icon="arrow-l">Back</a>
	</div><!-- /header -->
	
	<div data-role="content" data-theme="c">
		<h4>
		</h4>
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d">
<?php @include("common/footer.html"); ?>
	</div><!-- /footer -->
</div><!-- /page6 -->

</body>
</html>
