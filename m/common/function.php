<?php
function getYearlist(){

	$nowyear = (date('Y'));
	//年の配列（現在の年から-100年まで）//
	$yearlist[] = "--";
	for ($i = $nowyear-100; $i <= $nowyear; $i++) {
		$yearlist[] = $i;
	}
	return $yearlist;
}

function getMonthlist(){
	//月の配列（1-12まで）//
	$monthlist[] = "--";
	for ($i = 1; $i <= 12; $i++) {
		$monthlist[] = str_pad($i, 2, '0', STR_PAD_LEFT);
	}
	return $monthlist;
}

function getDaylist(){
	//日の配列（1-31まで）//
	$daylist[] = "--";
	for ($i = 1; $i <= 31; $i++) {
		$daylist[] = str_pad($i, 2, '0', STR_PAD_LEFT);
	}
	return $daylist;
}

function convNzNull($val){
	if ($val == ""){
		//空文字の場合、NULLを返す
		return "null";
	}else{
		return $val;
	}
}

function convNzInt($val){
	if ($val == ""){
		//空文字の場合、0を返す
		return 0;
	}else{
		return $val;
	}
}


?>