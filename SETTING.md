# SETTING 導入手順


## 動作実績環境


    ■Apache
    Version : 2.2
    ダウンロード : http://httpd.apache.org/download.cgi

    ■PHP
    Version : 5.2
    ダウンロード : http://www.php.net/downloads.php
    拡張モジュール : mbstring,mysql
    外部ライブラリ : PEAR/MDB2(2.4.0),PEAR/File_Archive(1.5.5)
                     
    ■MySQL
    Version : 5.5
    ダウンロード: http://dev.mysql.com/downloads/


## 環境設定


####■前提

Apache、PHP、MySQLが動作可能な環境である必要があります。

####■インストール


#####GitHubのリポジトリをCloneする場合

    cd 《Webサーバドキュメントルート》
    git clone --recursive git@github.com:MightyCraft/WorkingHoursMng.git

#####GitHubからソースをダウンロードしインストールする場合

1. GitHubからソースをダウンロードします。  
    https://github.com/MightyCraft/WorkingHoursMng.git
1. Webサーバの公開用ディレクトリのドキュメントルート以下に、解凍したファイルを展開します。
2. GitHubからダウンロードしたファイルを上記フォルダ以下に解凍・保存します。

以下のディレクトリ構成になるように、保存します。
		
	/
	  WorkingHoursMng/
        app/
        cache/
        documentroot/
        log/
        mcweb/

#####注意事項

* logフォルダとcacheフォルダが無い場合は、空フォルダでよいので、必ず作成してください。
* logフォルダとcacheフォルダは、フォルダへの書き込み権限をもつように設定してください。  
  (Linuxサーバ上に作成する場合は、アクセス権限を777に変更してください。)

####■シンボリックリンクの作成

シンボリックリンクを生成します。
	
**Windows環境の場合**

linkdコマンドが存在しない場合は、ダウンロードしてください。
		
設定方法は、`linkd.exe`は、パスの通っているところに配置してください。

`《C:\WINDOWS\system32\》`がお勧めです。

cmdを起動し、`《Webサーバドキュメントルート》/WorkingHoursMng/documentroot/`ディレクトリに移動したあとに、
以下のコマンドを実行してください。

    linkd link 《Webサーバドキュメントルート》\WorkingHoursMng\app\html

**Linux環境の場合**

`《Webサーバドキュメントルート》/WorkingHoursMng/documentroot/`以下に
`《Webサーバドキュメントルート》/WorkingHoursMng/app/html`へのシンボリックリンクを生成します。


#####documentroot内完成図

    development	 - 開発者用便利PHPフォルダ
    link		 - "../app/html"へのシンボリックリンク
    -.htaccess
    entry.php

※ シンボリックリンクでHTMLなどが丸見えになってしまうという心配があるかもしれませんが、
mod_rewriteが画像以外へのアクセスを防ぐようになっています。  

※ 環境設定によって参照するdocumentroot内が図と異なる場合があります。後述の「各種アプリケーション設定 複数環境」 参照

####■各種設定

#####設定ファイル(release.ini.php）の設定方法 

1. `app/define/release.ini.php.sample`をリネーム  
    release.ini.php.sample  
    ↓  
    release.ini.php
1. PEARへのパスを設定
1. **URL_DOMAIN_ROOT**設定	サイトURLのdocumentrootパス指定	(※ 最後に「/」をつける)
1. **URL_SITE_ROOT**設定	    サイトURLのdocumentrootパス指定	(※ 最後に「/」をつける)
1. **URL_FRAMEWORK_PHP**設定	サイトURLのdocumentrootパス指定	(※ 最後に「/」をつけない)
1. **DIR_LIB_ROOT**設定	    保存ファイルパス指定		    (※ 最後に「/」をつけない)
1. DB接続の設定
    `DatabaseSetting::setMaster('mysql://[ユーザID]:[パスワード]@[サーバ]/[DB名]?charset=[文字セット]');`
	
※後述の「MySQL設定」で実際の初期データベース設定をおこいます。  
※3～6補足 Windows環境の場合は、ドライブは『大文字』。\は「/」にする。

######記述例

    //2. PEARへのパスを設定
    set_include_path('C:/app/xampp/php/PEAR');
    ・
    ・
    //3. URL_DOMAIN_ROOT	サイトURLのdocumentrootパス指定
    define('URL_DOMAIN_ROOT', 'http://localhost/WorkingHoursMng/documentroot/');
    //4. URL_SITE_ROOT	    サイトURLのdocumentrootパス指定
    define('URL_SITE_ROOT', 'http://localhost/WorkingHoursMng/documentroot/');
    //5. URL_FRAMEWORK_PHP	サイトURLのdocumentrootパス指定
    define('URL_FRAMEWORK_PHP', 'http://localhost/WorkingHoursMng/documentroot');
    ・
    ・
    //6. DIR_LIB_ROOT	    保存ファイルパス指定
    define('DIR_LIB_ROOT',	'C:/app/xampp/htdocs/WorkingHoursMng');
    ・
    ・
    //7. DB接続の設定
    DatabaseSetting::setMaster('mysql://root:pass@localhost/working_hours_mng?charset=utf8');
    DatabaseSetting::setSlaves(
    array(
    	'mysql://root:pass@localhost/working_hours_mng?charset=utf8',
    		'mysql://root:pass@localhost/working_hours_mng?charset=utf8',
    		'mysql://root:pass@localhost/working_hours_mng?charset=utf8'
    	)
		
※ 環境設定によって参照する設定ファイルが異なる場合があります。「各種アプリケーション設定 複数環境 参照」
		
#####■MySQL設定

`create_working_hours_mng.sql`のスクリプトを実行します。
		
#####■Apache設定

Apache Portable Runtime library が IPv6をサポートしていない場合は`httpd.conf`に下記修正を行います。
		
    Listen 80
    ↓
    Listen 0.0.0.0:80
	
#####■Web工数表に管理ユーザでログイン出来れば環境設定完了

ID/PASS root/root	
    `http://《WEBサーバアドレス》/WorkingHoursMng/documentroot/`

###各種アプリケーション設定

#####■マスタ設定

######アカウント作成：ログインするために必要

管理ユーザでログイン後、アカウント情報画面よりユーザを登録します。  
(アカンウント管理＞アカウント一覧 新規追加押下)
			
アカウント登録には下記のマスタ設定を使用します。
* 権限マスタ (その他マスタ管理＞権限管理押下)  
  初期データとして「システム管理者」が登録されています。必要に応じて登録・修正してください。

* 部署マスタ (その他マスタ管理＞部署管理押下)  
  初期データとして「総務部」「営業部」「制作部」が登録されています。必要に応じて登録・修正してください。

* 役職マスタ (その他マスタ管理＞役職管理押下)  
  初期データとして「一般」が登録されています。必要に応じて登録・修正してください。

* 社員タイプマスタ (その他マスタ管理＞社員タイプ管理押下)  
  初期データとして「正社員」が登録されています。必要に応じて登録・修正してください。

* 社員コストマスタ (その他マスタ管理＞社員コスト管理押下)  
  初期データとして「基準値」が登録されています。必要に応じて登録・修正してください。

※必要権限・登録方法等についてはそれぞれ同梱の「HELP.md」,「REFERENCE.md」参照
		
######プロジェクト登録：システムを利用するのに必要

プロジェクト新規追加画面よりプロジェクトを登録します。  
(プロジェクト管理＞既存案件一覧/終了案件一覧 新規追加押下)

プロジェクト登録には下記のマスタ設定を使用します。
* クライアントマスタ　(クライアント管理押下)  
  初期データは未登録の状態です。必要に応じて登録・修正してください。  
  (こちらのマスタデータが登録されていないとプロジェクトの登録は不可です。)

* 社員コストマスタ
  ※アカウント作成参照

※必要権限・登録方法等についてはそれぞれ同梱の「HELP.md」,「REFERENCE.md」参照

#####■バッチ設定

######使用済みコスト工数集計バッチ：最新の使用済みコスト工数を集計するのに必要

使用済みコスト工数集計するには下記コマンドを実行します。

    php -f 《実ファイル格納ディレクトリ》/WorkingHoursMng/documentroot/entry.php /commandline/cron/cost
　　
例) 実ファイル格納ディレクトリが`C:/project/`の場合

    php -f C:/project/WorkingHoursMng/documentroot/entry.php /commandline/cron/cost    

実行後以下の文字列がのみ表示されば正常終了となります。

    start=2013-08-01 17:25:12
    end=2013-08-01 17:25:12

※こちらはcron等に登録しユーザの更新が少ない時間帯に自動実行することを推奨します。  
※コスト工数の説明は同梱の「HELP.md」の「※2 コスト工数について」をご確認下さい。

#####■複数環境

当システムでは環境設定を使用して、現在有効な設定ファイルをロードする機能を持っています。 
	
#####定義環境

以下の３つの環境が定義されており

環境毎に参照する`《Webサーバドキュメントルート》/WorkingHoursMng/documentroot/`配下の設定ファイルが異なります。
	  

1. ローカル環境  
ソースに手を入れる担当者の作業PCに作成する作業用環境を想定した定義です。  
設定ファイル `local.ini.php` を参照	  
2. デバッグ環境  
修正したソースの動作確認環境を想定した定義です。  
設定ファイル `debug.ini.php` を参照
3. リリース環境  
実際に運用する本番環境を想定した定義です。  
設定ファイル `release.ini.php` を参照

いずれも`****.ini.php.sample`という「.sample」付きのファイルが存在するので
「.sample」無しにリネームして使用して下さい。

#####設定方法
	 
`《Webサーバドキュメントルート》/WorkingHoursMng/documentroot/`配下に規定のファイルを配置することで環境指定が可能です。

1. ローカル環境  
  `.local` ファイル(中身は空のファイル)を配置します。
2. デバッグ環境  
  `.debug` ファイル(中身は空のファイル)を配置します。
3. リリース環境  
  `.release` ファイル(中身は空のファイル)を配置します。
  もしくは `.local`  `.debug`  `.release` のいずれも配置しないようにします。
	   
また `.local`  `.debug`  `.release` が複数同時に存在する場合は
1 < 2 < 3 の優先度で有効になる環境が決まります。
	  
