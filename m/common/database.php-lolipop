<?php
/*---------------------------------
データベース接続＆ログイン
---------------------------------*/
class database{
	Protected $SVN = 'localhost';
	//Protected $SVN = 'mysql1.webcrow-php.netowl.jp';

	Protected $UNM = 'feegmanage';//
	Protected $PW = 'G7at54DqahA';//
	//Protected $UNM = 'feegle_conc';//
	//Protected $PW = 'snJ78Kdf';//

	Protected $DBN = 'feegle';
	//Protected $DBN = 'feegle_booksearch';

	//オブジェクトが生成される際、自動的に呼ばれるメソッド「コンストラクタ」
	function __construct(){

    	//DB接続
		# MySQLへ接続する 戻り値は成功時はリンクId、失敗時はFalse
		$this->link_id = mysql_connect($this->SVN,$this->UNM,$this->PW);
		if (!$this->link_id) {
		    die('DB接続に失敗しました。' . mysql_error());
		}

		$db_selected = mysql_select_db($this->DBN, $this->link_id);
		if (!$db_selected){
		    die('DB接続に失敗しました。(select_db)'.mysql_error());
		}
		mysql_set_charset('utf8');

	}
	//オブジェクトが消滅するときに自動的に呼ばれるメソッド「デストラクタ」
	function __destruct(){
		mysql_close($this->link_id);
	}

	public function __get($key){
		return $this->$key;
	}

	/*----------------------------------------
	 データ取得関数
	引数
	$sql	->	sql文
	$rows	->	結果配列(byref)
	戻り値
	失敗	->	false
	成功	->	取得したデータの配列
	----------------------------------------*/
	function Fetch1($sql,&$rows){
			$ret = mysql_query($sql);

		if (!$ret){
			//失敗
			//$this->errmsg ="SELECT失敗". mysql_error();
			return false;
		}

		$rows = array();
		while ($row = mysql_fetch_array($ret)) {
			$rows[]=$row;
		}
		return true;
	}

	/*----------------------------------------
	 データ更新
	戻り値
	失敗	->	false
	成功	->	true
	----------------------------------------*/
	public function Execute($sql){

		try {
			return mysql_query($sql);
		} catch (Exception $e) {
			echo $e->getMessage(), PHP_EOL;
		}

	}
}

class comdb extends database {
	private $errmsg;
	protected $db;

	function __construct(){

		$this->db = new database();

	}
	function __destruct(){

	}
	public function __get($key){
		return $this->$key;
	}

	/*----------------------------------------
		トランザクション開始
	----------------------------------------*/
	public function BeginTran(){
		//オートコミットをオフに設定
		$ret = $this->db->Execute("set autocommit = 0");

		//トランザクション開始
		$ret = $this->db->Execute("begin");
	}
	/*----------------------------------------
	 トランザクションコミット
	----------------------------------------*/
	public function Commit(){

		$ret = $this->db->Execute("commit");

	}
	/*----------------------------------------
	 トランザクションロールバック
	----------------------------------------*/
	public function RollBack(){

		$ret = $this->db->Execute("rollback");

	}
	/*----------------------------------------
	 ログイン
	戻り値
	失敗	->	false
	$errmsgにエラー内容格納
	成功	->	取得したデータ
	----------------------------------------*/
	public function Fetch($sql){
		$ret = $this->db->Fetch1($sql,$rows);
		return $rows;
	}
	/*----------------------------------------
		ログイン 
		戻り値
			失敗	->	false
						$errmsgにエラー内容格納
			成功	->	取得したデータ
	----------------------------------------*/
	public function Login($uid,$pw){
		$this->errmsg ="";
		try{
			if (empty($uid) or empty($pw)) {
				$this->errmsg ="ログインIDまたはパスワードが正しくありません。";
				return false;
			}

			//$sql ="SELECT nickname,birth,gender,mjob_cd";
			//$sql.= ",ljob_cd,review_posts_cnt,thanks_cnt";
			$sql ="SELECT *";
			$sql.= " FROM user_table";
			$sql.=" WHERE user_id = '".$uid."'";
			$sql.=" AND password = '".$pw."'";

			//echo "SQL：".$sql;
			//exit;

			$ret = $this->db->Fetch1($sql,$rows);

			if (!$ret) {
				$this->errmsg ="SELECT失敗". mysql_error();
				return false;
			}

			if (count($rows) == 1){

				return $rows[0];
			}else{
				$this->errmsg ="ユーザーIDまたはパスワードが正しくありません。";
				return false;
			}
		}catch (Exception $e) {
			$this->errmsg= "システムエラー：".$e->getMessage();
			echo "システムエラー：".$e->getMessage();
			return false;
		}
	}
}
?>