<?php
/**
 * px2-auth: $dba
 */
namespace tomk79\pickles2\auth;

/**
 * dba.php
 */
class dba{

	/** $mainオブジェクト */
	private $main;

	/** Picklesオブジェクト */
	private $px;

	/** データベース設定オブジェクト */
	private $db_options;

	/**
	 * Constructor
	 *
	 * @param object $main $mainオブジェクト
	 * @param object $px $pxオブジェクト
	 * @param object $db_optionsmain データベース設定オブジェクト
	 */
	public function __construct( $main, $px, $db_options ){
		$this->main = $main;
		$this->px = $px;
		$this->db_options = $db_options;

		$csv2json = new \tomk79\csv2json( $db_options->path );
		$user_db = array();
		foreach($csv2json->fetch_assoc() as $row){
			$user_db[$row['account']] = $row;
		}
		$this->user_db = $user_db;
		// var_dump($this->user_db);
	}

	/**
	 * ユーザー情報を取得する
	 * 
	 * @param string $account ユーザーアカウント名
	 */
	public function get_user_info($account){
		if(!is_array( @$this->user_db[$account] )){
			return false;
		}
		return $this->user_db[$account];
	}
}
