# ドメイン・URL移行ガイド

## 概要
「joseikin-insight.com」から新ドメインへの移行に関する注意事項とチェックリスト

## ⚠️ 重要な注意

### 現在の状態
以下のURLは**現時点では変更していません**（サイトが動作しなくなるため）：

1. **画像URL**
   - `https://joseikin-insight.com/gemini_generated_image_19k6yi19k6yi19k6/`
   - `https://joseikin-insight.com/wp-content/uploads/2025/05/cropped-logo3.webp`
   - `https://joseikin-insight.com/wp-content/uploads/2024/11/dashboard-screenshot.webp`
   - `https://joseikin-insight.com/wp-content/uploads/2025/12/`

2. **内部リンクURL**
   - `https://joseikin-insight.com/about/`
   - `https://joseikin-insight.com/contact/`
   - `https://joseikin-insight.com/privacy/`
   - `https://joseikin-insight.com/terms/`
   - `https://joseikin-insight.com/disclaimer/`
   - `https://joseikin-insight.com/subsidy-diagnosis/`

3. **HTTPSリダイレクト処理**
   - `inc/performance-optimization.php` 内の `http://joseikin-insight.com` → `https://joseikin-insight.com`

### 変更済み項目

✅ **メールアドレス**
- `info@joseikin-insight.com` → `info@hojokin-zukan.com`
- ページテンプレート（about, contact, disclaimer, privacy）で更新済み

✅ **SNS URL**
- Twitter: `@joseikininsight` → `@hojokin_zukan`
- Facebook: `facebook.com/joseikin.insight` → `facebook.com/hojokin.zukan`
- Instagram: `instagram.com/joseikin_insight` → `instagram.com/hojokin_zukan`
- Note: `note.com/joseikin_insight` → `note.com/hojokin_zukan`
- YouTube: 変更なし（チャンネルID）

---

## ドメイン移行手順（本番環境）

### Phase 1: 準備（移行前）

#### 1. 新ドメインの準備
- [ ] 新ドメイン（例: `hojokin-zukan.com`）を取得
- [ ] DNSレコードを設定（Aレコード、CNAMEレコード）
- [ ] SSL証明書を取得・設定（Let's Encrypt等）
- [ ] 新ドメインでWordPressにアクセス可能であることを確認

#### 2. メールアドレスの準備
- [ ] `info@hojokin-zukan.com` のメールアカウントを作成
- [ ] メール転送設定（旧→新）を設定
- [ ] テストメール送信で動作確認

#### 3. SNSアカウントの準備
- [ ] Facebook: 新ページ `facebook.com/hojokin.zukan` を作成
- [ ] Instagram: 新アカウント `@hojokin_zukan` を作成
- [ ] Note: 新アカウント `note.com/hojokin_zukan` を作成
- [ ] Twitter/X: `@hojokin_zukan` が既に設定済み

### Phase 2: WordPress設定変更

#### 1. WordPress設定
```
WordPress管理画面 > 設定 > 一般

- WordPress アドレス (URL): https://hojokin-zukan.com
- サイトアドレス (URL): https://hojokin-zukan.com
```

#### 2. パーマリンク再保存
```
WordPress管理画面 > 設定 > パーマリンク

- 変更なしで「変更を保存」をクリック（.htaccessを更新）
```

#### 3. データベース一括置換（WP-CLI推奨）
```bash
# WP-CLIを使用した安全な置換
wp search-replace 'joseikin-insight.com' 'hojokin-zukan.com' --all-tables --dry-run

# 問題なければ実行
wp search-replace 'joseikin-insight.com' 'hojokin-zukan.com' --all-tables

# HTTPSも確認
wp search-replace 'http://joseikin-insight.com' 'https://hojokin-zukan.com' --all-tables
```

**⚠️ 注意**: プラグイン「Better Search Replace」も使用可能ですが、必ずバックアップを取ってください。

### Phase 3: テーマファイルのURL変更

#### 1. 画像URLの一括置換
```bash
cd /home/user/webapp
find . -name "*.php" -exec sed -i 's|https://joseikin-insight\.com|https://hojokin-zukan.com|g' {} \;
```

#### 2. 内部リンクの確認
```bash
grep -r "joseikin-insight\.com" --include="*.php" . | grep -v ".git"
```

#### 3. performance-optimization.php の修正
```php
// 変更前
return str_replace('http://joseikin-insight.com', 'https://joseikin-insight.com', $content);

// 変更後
return str_replace('http://hojokin-zukan.com', 'https://hojokin-zukan.com', $content);
```

### Phase 4: リダイレクト設定

#### 1. .htaccess でリダイレクト（旧ドメインで設定）
```apache
# joseikin-insight.com の .htaccess
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{HTTP_HOST} ^joseikin-insight\.com$ [NC,OR]
RewriteCond %{HTTP_HOST} ^www\.joseikin-insight\.com$ [NC]
RewriteRule ^(.*)$ https://hojokin-zukan.com/$1 [R=301,L]
</IfModule>
```

#### 2. Cloudflare Page Rules（使用している場合）
```
URL: *joseikin-insight.com/*
Forwarding URL: 301 - Permanent Redirect
Destination: https://hojokin-zukan.com/$1
```

### Phase 5: SEO対策

#### 1. Google Search Console
- [ ] 新ドメインをプロパティとして追加
- [ ] サイトマップを再送信: `https://hojokin-zukan.com/sitemap_index.xml`
- [ ] アドレス変更ツールを使用（旧→新ドメイン）

#### 2. Google Analytics
- [ ] プロパティ設定でドメインを更新
- [ ] 参照元除外設定を確認

#### 3. 構造化データの確認
- [ ] Google Rich Results Test で確認
- [ ] Organization/WebSite schema のURLが新ドメインであることを確認

### Phase 6: 外部サービスの更新

#### 1. SNS連携
- [ ] OGP画像が新ドメインで表示されることを確認
- [ ] Facebook Debugger でキャッシュクリア
- [ ] Twitter Card Validator で確認

#### 2. メール通知
- [ ] WordPressメール送信元アドレスを `info@hojokin-zukan.com` に変更
- [ ] お問い合わせフォームの宛先を確認

#### 3. 広告・アフィリエイト
- [ ] Google AdSense: サイトURLを更新
- [ ] ASP (アフィリエイトサービス): 登録URLを更新
- [ ] 広告タグ内のドメイン参照を確認

---

## 検証チェックリスト

### ドメイン移行後の確認事項

#### 基本動作
- [ ] トップページが表示される
- [ ] 記事ページが表示される
- [ ] 画像が正しく表示される
- [ ] 内部リンクが動作する
- [ ] お問い合わせフォームが動作する

#### SEO
- [ ] robots.txt が正しい（新ドメイン）
- [ ] サイトマップが生成される
- [ ] 構造化データが有効
- [ ] OGP画像が表示される
- [ ] canonical URL が新ドメイン

#### リダイレクト
- [ ] 旧ドメイン → 新ドメインへ301リダイレクト
- [ ] www なし → あり（または逆）のリダイレクト
- [ ] http → https のリダイレクト

#### メール
- [ ] 新メールアドレスで受信可能
- [ ] WordPressからのメール送信が動作
- [ ] お問い合わせフォームからのメール受信

---

## トラブルシューティング

### 問題: 画像が表示されない
**原因**: データベース内のURLが更新されていない
**解決**: WP-CLI で search-replace を実行

### 問題: リダイレクトループが発生
**原因**: .htaccess の設定競合
**解決**: .htaccess を確認、WordPress設定のURLを確認

### 問題: SSL証明書エラー
**原因**: 新ドメインのSSL証明書が未設定
**解決**: Let's Encrypt 等でSSL証明書を取得

### 問題: 検索結果に旧ドメインが表示される
**原因**: Googleのインデックス更新待ち
**解決**: Google Search Console でアドレス変更を実施、時間経過を待つ

---

## 移行スケジュール例

| フェーズ | 所要時間 | 内容 |
|---------|---------|------|
| Phase 1 | 1-2日 | ドメイン取得、DNS設定、SSL設定 |
| Phase 2 | 30分 | WordPress設定変更 |
| Phase 3 | 1-2時間 | テーマファイル・DB一括置換 |
| Phase 4 | 30分 | リダイレクト設定 |
| Phase 5 | 1-2時間 | SEO・外部サービス更新 |
| Phase 6 | 30分 | 動作検証 |
| **合計** | **2-3日** | |

---

## 重要な注意事項

1. **必ずバックアップを取る**
   - データベースの完全バックアップ
   - wp-content/ フォルダのバックアップ
   - .htaccess のバックアップ

2. **段階的な移行を推奨**
   - まずステージング環境でテスト
   - 本番環境は深夜・休日に実施

3. **旧ドメインは最低6ヶ月維持**
   - 301リダイレクトを継続
   - SEO評価の引き継ぎのため

4. **SNSアカウントは並行運用**
   - 旧アカウントから新アカウントへ誘導
   - フォロワー移行期間を設ける

---

## 関連ドキュメント
- `SITE_NAME_UPDATE_INSTRUCTIONS.md` - サイト名変更手順
- `CACHE_OPTIMIZATION_GUIDE.md` - キャッシュ最適化ガイド

## 更新日
2025-12-21

## 作成者
GenSpark AI Developer
