<?php
//
// ユーザー仮登録テーブルより、作成後24時間経過したデータを削除
// 　cronにて実行

// 変数初期化
$arr_user_id = array();
$arr_rdate = array();
$wk_24hour = date("Y-m-d H:i:s",strtotime("-24 hour")); // 24時間前

require("common/conf.php"); // 共通定義

//***********************************************
// DB接続
//***********************************************
include 'common/database.php';
$obj = new comdb();

//***********************************************
// 削除対象データ検索
//***********************************************
$sql = "SELECT user_id,rdate";
$sql.= " FROM fg_user_reg_table";
$ret = $obj->Fetch($sql);
if (count($ret) <> 0){
	foreach($ret as $key => $val){
		$arr_user_id[] = $val["user_id"];
		$arr_rdate[] = $val["rdate"];
	}
}

for ($i=0; $i<count($arr_user_id); $i++) {
	// データ作成日時が24時間経過していたらデータ削除
	if ($arr_rdate[$i] < $wk_24hour) {
		//***********************************************
		// データ削除
		//***********************************************
		$sql = "DELETE FROM fg_user_reg_table";
		$sql.= " WHERE user_id = \"".$arr_user_id[$i]."\"";
		$ret = $obj->Execute($sql);
		if (!$ret){
			echo ERR_DEL."[fg_user_reg_table] user_id=".$arr_user_id[$i]."\n";
		}
	}
}
?>