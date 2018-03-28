<?php
/**
 * px2-auth
 */
namespace tomk79\pickles2\auth;

/**
 * main.php
 */
class main{

	/** Picklesオブジェクト */
	private $px;

	/**
	 * ユーザー認証する
	 *
	 * @param object $px Picklesオブジェクト
	 * @param object $options オプション
	 */
	static public function auth($px, $options){
		$main = new self($px, $options);
	}

	/**
	 * Constructor
	 *
	 * @param object $px $pxオブジェクト
	 * @param object $options オプション
	 */
	public function __construct( $px, $options = null ){
		$this->px = $px;
	}

}
