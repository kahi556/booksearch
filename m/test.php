<?php
    define("AWS_BASE_URL", "http://ecs.amazonaws.jp/onca/xml");
    define("AWS_ACCESS_KEY", "AKIAJ6RB3U2QTAWDTGVQ");
    define("AWS_SECRET_ACCESS_KEY", "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");
    define("AWS_OPTION_SERVICE", "AWSECommerceService");
    define("AWS_OPTION_VERSION", "2011-08-02");
    define("AWS_OPTION_ASSOCIATETAG", "hk556-22");
//    define("AWS_OPTION_SEARCHINDEX", "All");
    define("AWS_OPTION_SEARCHINDEX", "Books");
    define("AWS_OPTION_RESPONSEGROUP", "Medium");

    require_once(dirname(__FILE__) ."/Util.php");
    require_once(dirname(__FILE__) ."/BookInfo.php");

    //検索キーワード取得
    //$searchWord = $_POST['s_word'];
    $searchWord = "Harry Potter";

    //リクエストURL生成
    $baseurl = AWS_BASE_URL;
    $params = array();
    $params['Service'] = AWS_OPTION_SERVICE;
    $params['AWSAccessKeyId'] = AWS_ACCESS_KEY;
    $params['Version'] = AWS_OPTION_VERSION;
    $params['AssociateTag'] = AWS_OPTION_ASSOCIATETAG;
    $params['SearchIndex'] = AWS_OPTION_SEARCHINDEX;
    $params['ResponseGroup'] = AWS_OPTION_RESPONSEGROUP;
    $params['Operation'] = 'ItemSearch';
    $params['Keywords'] = $searchWord ;        //検索キーワード
    $params['ItemPage'] = 1;
    $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');

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

        $dataList[] = $data;
    }

    var_dump($dataList);
?>
