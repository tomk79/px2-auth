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

	/** プラグイン設定オブジェクト */
	private $options;

	/** ユーザーオブジェクト */
	private $user;

	/**
	 * ユーザー認証する
	 *
	 * @param object $px Picklesオブジェクト
	 * @param object $options オプション
	 */
	static public function auth($px, $options){
		$auth_main = new self($px, $options);

		// $px に自身を登録する
		$px->auth = $auth_main;
	}

	/**
	 * ページが要求する認証レベルを判定して、自動的にログイン画面を表示する
	 *
	 * @param object $px Picklesオブジェクト
	 * @param object $options オプション
	 */
	static public function filter($px, $options){
		if( !is_object($px->auth) ){
			// auth() が設定されていなければ何もしない。
			return false;
		}

		if( $px->auth->user()->is_login() ){
			// ユーザーがログイン済みなら何もしない。
			return true;
		}
		foreach( $px->bowl()->get_keys() as $key ){
			$src = $px->bowl()->get_clean( $key );
			if( $key == 'main' ){
				// mainのコンテンツはログインフォームに置き換える
				$src = $px->auth->mk_login_form();
			}else{
				// その他のコンテンツは削除する
				$src = '';
			}
			$px->bowl()->replace( $src, $key );
		}

		return true;
	}

	/**
	 * Constructor
	 *
	 * @param object $px $pxオブジェクト
	 * @param object $options オプション
	 */
	public function __construct( $px, $options = null ){
		$this->px = $px;
		$this->options = $options;

		$this->dba = new dba($this, $px, $options->db);
		$this->user = new user($this, $px);

		if( $px->req()->get_param('auth-login-do-login') ){
			// ユーザーがログインを試みているとき
			$this->user->login( array(
				'id'=>$px->req()->get_param('auth-login-account'),
				'pw'=>$px->req()->get_param('auth-login-password'),
			) );
		}
	}


	/**
	 * ユーザーオブジェクトを取得する
	 */
	public function user(){
		return $this->user;
	}

	/**
	 * データベースアクセスオブジェクトを取得する
	 */
	public function dba(){
		return $this->dba;
	}

	/**
	 * ログインフォームを生成する
	 */
	public function mk_login_form(){
		$rtn = '';
		ob_start(); ?>
<form action="" method="post">
	<p>User ID: <input type="text" name="auth-login-account" /></p>
	<p>Password: <input type="password" name="auth-login-password" /></p>
	<p><button type="submit">ログインする</button></p>
	<input type="hidden" name="auth-login-do-login" value="1" />
</form>
<?php
		$rtn .= ob_get_clean();
		return $rtn;
	}
}
