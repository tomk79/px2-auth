# tomk79/px2-auth
Pickles 2 にユーザー認証機能を追加します。

## インストール - Install

```
$ composer require tomk79/px2-auth
```

## セットアップ - Setup

### Pickles 2 の `config.php` を編集する

```php
<?php
return call_user_func( function(){

	/* 中略 */

	// funcs: Before content
	$conf->funcs->before_content = array(
		// px2-auth
		'tomk79\pickles2\auth\main::auth('.json_encode(array(
			'db' => array(
				'dbms' => 'csv',
				'path' => './path/to/user_list.csv',
			),
		)).')',
	);

	/* 中略 */

	// processor
	$conf->funcs->processor->html = array(
		// px2-auth : filter
		'tomk79\pickles2\auth\main::filter('.json_encode(array(	
		)).')',
	);

	/* 中略 */

	return $conf;
} );
```

### オプション - Options

TBD.

## 更新履歴 - Change log

### tomk79/px2-auth 0.1.0 (未定)

- initial release.
- ???????????????????????????????????


## ライセンス - License

MIT License


## 作者 - Author

- Tomoya Koyanagi <tomk79@gmail.com>
- website: <http://www.pxt.jp/>
- Twitter: @tomk79 <http://twitter.com/tomk79/>
