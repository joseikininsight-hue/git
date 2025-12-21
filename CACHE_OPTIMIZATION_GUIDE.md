# 📋 キャッシュ最適化ガイド - 広告収益改善のための必須設定

## 🚨 重要：キャッシュと広告配信の競合問題

**問題**: ページキャッシュ（HTMLキャッシュ）を有効化すると、広告のランダム表示やデバイス出し分けが機能しなくなります。

**原因**: 
- 最初にアクセスしたユーザーが見た広告HTMLが固定される
- `RAND()` によるローテーションが停止
- `detect_device()` (PC/SP判定) が初回アクセス時のデバイスで固定される
- 結果：インプレッションはあるがCTR（クリック率）が激減

## ✅ 推奨設定

### 1. キャッシュプラグイン設定（WP Rocket、W3 Total Cache等）

#### **最重要設定：モバイルとPCでキャッシュを分ける**

```
設定 > Cache > Mobile Cache
☑️ Enable separate cache for mobile devices
```

これにより、PCユーザーとスマホユーザーで異なるキャッシュが生成され、デバイス出し分けが正常に機能します。

#### **広告スクリプトをキャッシュ除外**

```
設定 > JavaScript Settings > Exclude JavaScript Files
追加: /wp-content/themes/*/inc/affiliate-ad-manager.php
追加: admin-ajax.php?action=ji_track_*
```

#### **AJAXリクエストをキャッシュ除外**

```
設定 > Never Cache URL(s)
追加: /wp-admin/admin-ajax.php
```

### 2. Cloudflare使用時の設定

#### **Page Rules設定**

```
URL Pattern: example.com/wp-admin/admin-ajax.php*
Settings:
  - Cache Level: Bypass
  - Disable Performance
```

#### **Workers（上級者向け）**

デバイス判定をエッジで行い、適切なキャッシュを返す：

```javascript
addEventListener('fetch', event => {
  event.respondWith(handleRequest(event.request))
})

async function handleRequest(request) {
  const userAgent = request.headers.get('user-agent') || ''
  const isMobile = /Mobile|Android|iPhone/i.test(userAgent)
  
  // デバイスごとにキャッシュキーを変える
  const cacheKey = new Request(request.url, {
    headers: request.headers,
    cf: {
      cacheKey: request.url + (isMobile ? '?mobile=1' : '?desktop=1')
    }
  })
  
  return fetch(cacheKey)
}
```

### 3. WordPress側の設定確認

#### **functions.php または inc/affiliate-ad-manager.php**

デバイス判定が正しく機能しているか確認：

```php
/**
 * デバイス検出
 */
private function detect_device() {
    // キャッシュプラグインでモバイルキャッシュを分離している場合、これで正常に動作
    return wp_is_mobile() ? 'mobile' : 'desktop';
}
```

### 4. 動的広告配信（推奨：JavaScript遅延ロード）

**最も確実な方法**: 広告枠だけ出力し、JavaScriptで動的に取得

```php
// 広告枠のみ出力（キャッシュされてもOK）
<div class="ji-ad-slot" 
     data-position="content_middle" 
     data-post-id="<?php echo get_the_ID(); ?>"
     data-categories="<?php echo implode(',', $category_ids); ?>">
    <div class="loading-spinner"></div>
</div>

<script>
// ページロード後に広告を動的取得
document.addEventListener('DOMContentLoaded', function() {
    const adSlots = document.querySelectorAll('.ji-ad-slot');
    
    adSlots.forEach(function(slot) {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'ji_get_ad_html',
                position: slot.dataset.position,
                post_id: slot.dataset.postId,
                categories: slot.dataset.categories
            })
        })
        .then(response => response.text())
        .then(html => {
            slot.innerHTML = html;
        });
    });
});
</script>
```

## 🔍 キャッシュ動作確認方法

### 1. デバイス出し分けのテスト

```bash
# PCでアクセス
curl -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)" \
     https://example.com/grants/sample-grant/ > pc.html

# スマホでアクセス
curl -H "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)" \
     https://example.com/grants/sample-grant/ > mobile.html

# 差分確認（広告内容が異なれば正常）
diff pc.html mobile.html
```

### 2. 広告ランダム表示のテスト

```bash
# 同じページに5回アクセスして、広告が変わるか確認
for i in {1..5}; do
  curl https://example.com/grants/sample-grant/ | \
  grep -o 'data-ad-id="[0-9]*"' | head -1
done

# 結果例（正常な場合、異なるad_idが出る）:
# data-ad-id="12"
# data-ad-id="34"
# data-ad-id="12"
# data-ad-id="56"
# data-ad-id="34"
```

### 3. キャッシュヘッダーの確認

```bash
curl -I https://example.com/grants/sample-grant/

# 期待される結果:
# X-Cache: HIT from cloudflare (キャッシュが有効)
# Cache-Control: public, max-age=3600
# Vary: User-Agent (デバイスごとにキャッシュを分ける場合必須)
```

## 📊 収益改善のKPI

### 修正前後の比較指標

| 指標 | 修正前（想定） | 修正後（目標） |
|------|--------------|--------------|
| **CTR（クリック率）** | 0.3%〜0.8% | 1.5%〜3.0% |
| **表示回数精度** | 実際より少ない | 正確 |
| **デバイス不一致率** | 30%〜50% | 0% |
| **広告ローテーション** | 機能せず | 正常動作 |

### モニタリング方法

WordPress管理画面 > アフィリエイト広告 > 統計情報 で以下を確認：

1. **デバイス別CTR**: PCとスマホで極端な差がないか
2. **広告別表示回数**: 特定の広告だけに偏っていないか
3. **日別推移**: CTRが安定して改善しているか

## 🛠 トラブルシューティング

### Q1: 設定後もCTRが低い

**A**: 
1. ブラウザキャッシュをクリア（Ctrl+F5）
2. キャッシュプラグインの全キャッシュをパージ
3. 管理画面で広告の「デバイスターゲット」設定を確認

### Q2: 広告が全く表示されない

**A**:
1. `wp-content/debug.log` でPHPエラーを確認
2. ブラウザのコンソールでJavaScriptエラーを確認
3. 広告ブロッカーを一時的に無効化してテスト

### Q3: トラッキングが正常に動作しない

**A**:
1. WordPress管理画面 > アフィリエイト広告 > 設定 で「統計追跡を有効化」がONか確認
2. admin-ajax.phpへのアクセスがファイアウォールでブロックされていないか確認
3. ブラウザのプライバシー設定（Cookie許可）を確認

## 📞 サポート

問題が解決しない場合は、以下の情報をまとめてご連絡ください：

1. 使用中のキャッシュプラグイン名とバージョン
2. サーバー環境（Apache/Nginx、PHP version）
3. 直近1週間のCTR推移
4. ブラウザコンソールのエラーメッセージ（あれば）

---

**最終更新**: 2024-12-21  
**バージョン**: 1.0.0  
**適用テーマ**: Grant Insight Perfect v11.0.1+
