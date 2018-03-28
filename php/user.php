<?php
/**
 * px2-auth: $user
 */
namespace tomk79\pickles2\auth;

/**
 * user.php
 */
class user{

	/** $mainオブジェクト */
	private $main;

	/** Picklesオブジェクト */
	private $px;

	/** 認証情報 */
	private $auth_info = array();

	/** 認証用のセッションキー */
	private $session_key = 'auth-login-user-info';

	/**
	 * Constructor
	 *
	 * @param object $main $mainオブジェクト
	 * @param object $px $pxオブジェクト
	 */
	public function __construct( $main, $px ){
		$this->main = $main;
		$this->px = $px;
		$this->auth_info = $this->px->req()->get_session($this->session_key);
		if(!is_array($this->auth_info)){
			$this->auth_info = array(
				'account'=>null,
				'name'=>null,
				'auth_level'=>0,
			);
		}
		if( @!$this->auth_info['auth_level'] ){
			$this->auth_info['auth_level'] = 0;
		}
	}


	/**
	 * ログインする
	 */
	public function login( $auth_info ){
		if( !strlen(@$auth_info['id']) ){
			return false;
		}

		$user_info = $this->main->dba()->get_user_info($auth_info['id']);
		if( $user_info['password'] !== sha1($auth_info['pw']) ){
			return false;
		}

		// ログイン成功
		$this->auth_info['account'] = $user_info['account'];
		$this->auth_info['name'] = $user_info['name'];
		$this->auth_info['auth_level'] = 1;
		$this->px->req()->set_session($this->session_key, $this->auth_info);
		return true;
	}

	/**
	 * ユーザーがログイン状態にあるか調べる
	 */
	public function is_login(){
		if( @$this->auth_info['auth_level'] ){
			return true;
		}
		return false;
	}

	/**
	 * ログアウトする
	 */
	public function logout(){
		$this->px->req()->delete_session($this->session_key);
		return true;
	}

}
