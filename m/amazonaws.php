<?php
//
// Amazon Product Advertising API：検索
// http://piyopiyocs.blog115.fc2.com/blog-entry-288.html
//

function amazon_info($searchWord, $searchISBN) {
    define("AWS_BASE_URL", "http://ecs.amazonaws.jp/onca/xml");
    define("AWS_ACCESS_KEY", "AKIAJ6RB3U2QTAWDTGVQ");
    define("AWS_SECRET_ACCESS_KEY", "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");
    define("AWS_OPTION_SERVICE", "AWSECommerceService");
    define("AWS_OPTION_VERSION", "2011-08-02");
    define("AWS_OPTION_ASSOCIATETAG", "hk556-22");
    //define("AWS_OPTION_SEARCHINDEX", "All");
    define("AWS_OPTION_SEARCHINDEX", "Books");
    define("AWS_OPTION_RESPONSEGROUP", "Medium");
    define("AWS_IDTYPE", "ISBN");
    
    require_once(dirname(__FILE__) ."/Util.php");
    require_once(dirname(__FILE__) ."/BookInfo.php");
    
    //リクエストURL生成
    $baseurl = AWS_BASE_URL;
    $params = array();
    $params['Service'] = AWS_OPTION_SERVICE;
    $params['AWSAccessKeyId'] = AWS_ACCESS_KEY;
    $params['Version'] = AWS_OPTION_VERSION;
    $params['AssociateTag'] = AWS_OPTION_ASSOCIATETAG;
    $params['SearchIndex'] = AWS_OPTION_SEARCHINDEX;
    $params['ResponseGroup'] = AWS_OPTION_RESPONSEGROUP;
    $params['ItemPage'] = 1;
    $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
	if ($searchISBN <> "") {
    	$params['Operation'] = 'ItemLookup';
	    $params['IdType'] = AWS_IDTYPE ;
	    $params['ItemId'] = $searchISBN ;
	}elseif ($searchWord <> "") {
	    $params['Keywords'] = $searchWord ;        //検索キーワード
    	$params['Operation'] = 'ItemSearch';
	}else{
		return "ERROR";
	}
    
    ksort($params);
    
    $caStr = '';
    foreach ($params as $k => $v) {
        $caStr .= '&'.Util::urlencode_rfc3986($k).'='.Util::urlencode_rfc3986($v);
    }
    $caStr = substr($caStr, 1);
    
    // 署名を作成します
    // - 規定の文字列フォーマットを作成
    // - HMAC-SHA256 を計算
    // - BASE64 エンコード
    $parsedUrl = parse_url($baseurl);
    $strToSign = "GET\n{$parsedUrl['host']}\n{$parsedUrl['path']}\n{$caStr}";
    $signature = base64_encode(hash_hmac('sha256', $strToSign, AWS_SECRET_ACCESS_KEY, true));
    
    // URL を作成します
    // - リクエストの末尾に署名を追加
    $url = $baseurl.'?'.$caStr.'&Signature='.Util::urlencode_rfc3986($signature);
    
    //XMLで情報を取得。
    $xml = @simplexml_load_file($url);
    
    $dataList = array();
    //検索結果データ展開
    foreach ($xml->Items->Item as $item) {
        $data = new BookInfo();
        
        $data->ISBN = (string) $item->ItemAttributes->ISBN; 
        $data->ASIN = (string) $item->ASIN; 
        $data->Title = (string) $item->ItemAttributes->Title; 
        $data->Author = (string) $item->ItemAttributes->Author; 
        $data->Publisher = (string) $item->ItemAttributes->Publisher; 
        $data->PublicationDate = (string) $item->ItemAttributes->PublicationDate; 
        $data->DetailPageURL = (string) $item->DetailPageURL; 
        $data->ImageURL = (string) $item->MediumImage->URL; 
		$data->EditorialReview = (string) $item->EditorialReviews->EditorialReview[1]->Content; 
        
        $dataList[] = $data;
    }
    
    //var_dump($dataList);
    return $dataList;
}

function ISBNTran ($ISBN) {
    if (strlen($ISBN) == 10) {
        //ISBN10からISBN13への変換
        $ISBNtmp = "978" . $ISBN;
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $weight = ($i % 2 == 0 ? 1 : 3);
            $sum += (int)substr($ISBNtmp, $i, 1) * (int)$weight;
        }
        //チェックディジットの計算
        $checkDgt = (10 - $sum % 10) == 10 ? 0 : (10 - $sum % 10);
        return "978" . substr($ISBN, 0, 9) . $checkDgt;
    } elseif (strlen($ISBN) == 13) {
        //ISBN13からISBN10への変換
        $ISBNtmp = substr($ISBN, 3, 9);
        $weight = 10;
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int)substr($ISBNtmp, $i, 1) * $weight;
            $weight--;
        }
        //チェックディジットの計算
        if ((11 - $sum % 11) == 11) {
            $checkDgt = 0;
        } elseif ((11 - $sum % 11) == 10) {
            $checkDgt = "X";
        } else {
            $checkDgt = (11 - $sum % 11);
        }
        return substr($ISBN, 3, 9) . $checkDgt;
    }
}
?>
