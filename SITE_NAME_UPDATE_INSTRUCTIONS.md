# サイト名変更手順

## 概要
「助成金インサイト」「補助金インサイト」から「**補助金図鑑**」への統一変更

## 完了済み
✅ header.php - サイト名表示を「補助金図鑑」に変更
✅ footer.php - サイト名表示を「補助金図鑑」に変更

## 必須：WordPressダッシュボード設定

### 1. サイトタイトルの変更
WordPress管理画面で以下の設定を変更してください：

**手順：**
1. WordPress管理画面にログイン
2. **設定 > 一般** にアクセス
3. **サイトのタイトル** を `助成金インサイト` または `補助金インサイト` から **`補助金図鑑`** に変更
4. **変更を保存** をクリック

### 2. サイトキャッチフレーズ（推奨）
- **現在のキャッチフレーズ確認：** 設定 > 一般
- **推奨キャッチフレーズ：** `日本全国の補助金・助成金情報を一元化`

### 3. OGタグ・メタ情報の確認
SEOプラグイン（Yoast SEO、All in One SEO等）を使用している場合：
- プラグイン設定画面で「サイト名」「Open Graph サイト名」を確認
- 必要に応じて「補助金図鑑」に変更

## 影響範囲

### 自動反映される箇所（変更不要）
- `<?php bloginfo('name'); ?>` を使用している全ての箇所
  - フッターのコピーライト表示（footer.php 730行目）
  - ヘッダーのロゴaltテキスト（header.php）
  - メタタグ・OGタグ

### 手動確認が必要な箇所
- ロゴ画像（変更が必要な場合）
  - ヘッダーロゴ: `header.php` 1617行目・1850行目
  - フッターロゴ: `footer.php` 556行目
  - 現在のURL: `https://joseikin-insight.com/gemini_generated_image_19k6yi19k6yi19k6/`
  - **対応：** デザイナーに新ロゴ作成依頼、または既存ロゴを「補助金図鑑」用に更新

- SNS URL（必要に応じて変更）
  - Twitter: `https://twitter.com/joseikininsight` → 新アカウント
  - Facebook: `https://facebook.com/joseikin.insight` → 新ページ
  - Instagram: `https://instagram.com/joseikin_insight` → 新アカウント
  - YouTube: `https://www.youtube.com/channel/UCbfjOrG3nSPI3GFzKnGcspQ` → チャンネル名変更
  - note: `https://note.com/joseikin_insight` → アカウント名変更

## 検証手順

### 1. ブラウザでの表示確認
- [ ] ヘッダーのモバイルロゴテキストが「補助金図鑑」と表示される
- [ ] フッターのコピーライトが「© 2025 **補助金図鑑**」と表示される
- [ ] ページタイトルのサイト名が「補助金図鑑」になっている

### 2. SEO確認
```bash
# Open Graph メタタグ確認
curl -s https://your-domain.com/ | grep 'og:site_name'
# 期待される出力: <meta property="og:site_name" content="補助金図鑑" />

# ページタイトル確認
curl -s https://your-domain.com/ | grep '<title>'
# 期待される出力: <title>ページ名 | 補助金図鑑</title>
```

### 3. キャッシュクリア
変更後、以下のキャッシュをクリアしてください：
- [ ] WordPressオブジェクトキャッシュ
- [ ] ブラウザキャッシュ（Ctrl+Shift+R / Cmd+Shift+R）
- [ ] CDN/Cloudflareキャッシュ（Cloudflare使用時）
- [ ] WP Rocketなどのキャッシュプラグイン

## トラブルシューティング

### 問題：サイト名が反映されない
**原因1：** WordPressのキャッシュ
```bash
# WordPress CLI使用時
wp cache flush
```

**原因2：** テーマまたはプラグインがハードコーディング
```bash
# テーマファイル内検索
grep -r "助成金インサイト\|補助金インサイト" /path/to/theme/
```

### 問題：OGタグにサイト名が反映されない
- SEOプラグイン設定を確認
- プラグインキャッシュをクリア
- `header.php`の`ji_generate_seo_meta`関数を確認（23行目以降）

## 参考リンク
- WordPress公式ドキュメント: https://ja.wordpress.org/support/article/settings-general-screen/
- `get_bloginfo()`: https://developer.wordpress.org/reference/functions/get_bloginfo/

## 変更日
2025-12-21

## 変更者
GenSpark AI Developer

---
**注意：** この手順は緊急広告収益最適化PR（#3）の一環として実施されています。
