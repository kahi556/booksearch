<?php
//$contents = 'http://feeds.feedburner.com/hatena/b/hotentry';
//$data = simplexml_load_file($contents);
//$contents = file_get_contents('http://feeds.feedburner.com/hatena/b/hotentry');
//$data = simplexml_load_string( $contents, 'SimpleXMLElement', LIBXML_NOCDATA );
//var_dump($data);
$str = fopen("http://feeds.feedburner.com/hatena/b/hotentry","r");
$ary = array();
while( !feof($str) ){
	$getTxt = fgets($str);
	$ary[] = $getTxt;
}
echo "count=".count($ary);
echo "check2";
exit;
//var_dump($ary);
?>
