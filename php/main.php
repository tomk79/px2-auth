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
		$options = json_decode( json_encode($options) );

		if( !@$options->auth_level_request ){
			// 認証が必要なページがないなら何もしない。
			return true;
		}
		$auth_level_request = $px->auth->get_auth_level(null, $options->auth_level_request);

		if( $px->auth->user()->get_auth_level() >= $auth_level_request ){
			// ユーザーの認証レベルが要求以上なら何もしない。
			return true;
		}

		foreach( $px->bowl()->get_keys() as $key ){
			$src = $px->bowl()->get_clean( $key );
			if( $key == 'main' ){
				// mainのコンテンツを
				if( !$px->auth->user()->is_login() ){
					// ログインフォームに置き換える
					$src = $px->auth->mk_login_form();
				}else{
					// forbidden画面に置き換える
					$src = $px->auth->mk_forbidden_page();
				}
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

	/**
	 * forbidden ページを生成する
	 */
	public function mk_forbidden_page(){
		$rtn = '';
		ob_start(); ?>
<p>このページにアクセスするために必要な権限が不足しています。</p>
<?php
		$rtn .= ob_get_clean();
		return $rtn;
	}

	/**
	 * パスが要求する認証レベル値を取得する
	 */
	private function get_auth_level( $path = null, $auth_level_request = null ){
		if(!strlen($path)){
			$path = $this->px->req()->get_request_file_path();
		}
		if(!is_object($auth_level_request)){
			return 0;
		}

		foreach( $auth_level_request as $path_required => $auth_level ){
			$preg_pattern = preg_quote( $path_required, '/' );
			if( preg_match('/'.preg_quote('\*','/').'/',$preg_pattern) ){
				// ワイルドカードが使用されている場合
				$preg_pattern = preg_replace('/'.preg_quote('\*','/').'/','(?:.*?)',$preg_pattern);//ワイルドカードをパターンに反映
				$preg_pattern = $preg_pattern.'$';//前方・後方一致
			}
			if( preg_match( '/^'.$preg_pattern.'/s' , $path ) ){
				return $auth_level;
			}
		}
		return 0;

	}
}
