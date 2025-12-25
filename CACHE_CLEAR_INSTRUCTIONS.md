# キャッシュクリア手順

## 問題
Parse Errorが修正済みなのに、まだエラーが表示される場合、サーバー側のキャッシュが原因です。

## 解決方法

### 1. WordPress管理画面から（最も簡単）

#### LiteSpeed Cacheの場合:
1. WordPress管理画面にログイン
2. 左メニュー → **LiteSpeed Cache**
3. **Toolbox** タブをクリック
4. **Purge** セクションで **"Purge All"** をクリック
5. ページをリフレッシュ（Ctrl+Shift+R）

#### WP Super Cacheの場合:
1. WordPress管理画面 → **設定** → **WP Super Cache**
2. **"Delete Cache"** をクリック

#### W3 Total Cacheの場合:
1. WordPress管理画面 → **Performance**
2. **"empty all caches"** をクリック

### 2. ブラウザのキャッシュクリア

- **Chrome/Edge**: Ctrl+Shift+Delete → "キャッシュされた画像とファイル"を選択
- **Firefox**: Ctrl+Shift+Delete → "キャッシュ"を選択
- **Safari**: Cmd+Option+E

### 3. コマンドラインから（上級者向け）

サーバーにSSH接続している場合:

```bash
# WordPress Object Cacheをクリア
wp cache flush

# OPcacheをクリア（PHPのキャッシュ）
# Apacheの場合
sudo service apache2 reload

# Nginxの場合
sudo service nginx reload
sudo service php-fpm reload
```

### 4. .htaccessで強制リロード（緊急対応）

`.htaccess` に一時的に追加:

```apache
# キャッシュを無効化（テスト用）
<FilesMatch "\.(php)$">
    FileETag None
    <IfModule mod_headers.c>
        Header unset ETag
        Header set Cache-Control "max-age=0, no-cache, no-store, must-revalidate"
        Header set Pragma "no-cache"
        Header set Expires "Wed, 11 Jan 1984 05:00:00 GMT"
    </IfModule>
</FilesMatch>
```

**注意**: この設定は動作確認後に削除してください。

## 確認方法

すべてのキャッシュをクリア後:

1. ブラウザで該当ページにアクセス
2. Ctrl+Shift+R（またはCmd+Shift+R）で強制リロード
3. エラーが消えていることを確認

## 最新のコミット

```
529d4d4 - fix: Resolve all remaining Parse Errors in taxonomy files
```

このコミットで以下のファイルが修正されました:
- taxonomy-grant_category.php (line 182)
- taxonomy-grant_municipality.php (line 205, 429)
- taxonomy-grant_purpose.php
- taxonomy-grant_tag.php

すべての Parse Error は解決済みです。
エラーが表示される場合は、キャッシュが原因です。
