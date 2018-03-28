<?php
/**
 * Test for tomk79\px2-auth
 */

class mainTest extends PHPUnit_Framework_TestCase{

	/**
	 * setup
	 */
	public function setup(){
		$this->fs = new \tomk79\filesystem();
	}

	/**
	 * 疎通確認テスト
	 */
	public function testPing(){

		// 実行してみる
		$output = $this->passthru( ['php', __DIR__.'/px2/standard/.px_execute.php', '/?PX=config' ] );
		// var_dump($output);
		$this->assertEquals( gettype(''), gettype($output) );

	}




	/**
	 * コマンドを実行し、標準出力値を返す
	 * @param array $ary_command コマンドのパラメータを要素として持つ配列
	 * @return string コマンドの標準出力値
	 */
	private function passthru( $ary_command ){
		$cmd = array();
		foreach( $ary_command as $row ){
			$param = '"'.addslashes($row).'"';
			array_push( $cmd, $param );
		}
		$cmd = implode( ' ', $cmd );
		ob_start();
		passthru( $cmd );
		$bin = ob_get_clean();
		return $bin;
	}// passthru()

}
