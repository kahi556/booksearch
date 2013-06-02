<?php
/*
 * Amazon Product Advertising API 向け 電子署名付加関数
 *
 *     author  : Takahiro Baba (http://ringoon.jp/)
 *     license : クリエイティブ・コモンズ 表示 2.1 日本 ライセンス
 *               (http://creativecommons.org/licenses/by/2.1/jp/)
 *     version : 0.4 (2009/05/16)
 *
 */

function add_signature($url,$secret_key){
	
	// "Your Secret Access Key"
	//$secret_key = "1234567890";
	
	$ret_char = "\n";
	
	$url_array = parse_url($url);
	
	parse_str($url_array["query"], $param_array);
	
	$param_array["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
	
	ksort($param_array);
	
	$str = "GET".$ret_char.$url_array["host"].$ret_char.$url_array["path"].$ret_char;
	
	$str_param = "";
	while( list($key, $value) = each($param_array) ){
		$str_param =
			$str_param.strtr($key, "_", ".")."=".rawurlencode($value)."&";
	}
	$str = $str.substr($str_param, 0, strlen($str_param)-1);
	
	$signature = base64_encode( hash_hmac("sha256", $str, $secret_key, true) );
	
	$url_sig = 
		"http://".$url_array["host"].$url_array["path"]."?".
		$str_param."Signature=".rawurlencode($signature);
	
	return $url_sig;
}

?>