<?php
/**
 * AdSense Optimization Module
 * Google AdSense の表示最適化と LiteSpeed Cache / PageSpeed Module との連携
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 * 
 * 主な機能:
 * 1. AdSense スクリプトの遅延読み込み除外
 * 2. 手動広告ユニット配置用のヘルパー関数
 * 3. コンセントモードの最適化
 * 4. Site Kit との重複防止
 * 5. 記事内の広告挿入ポイント作成
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================================================
 * 1. LiteSpeed Cache との完全な連携設定
 * ============================================================================
 */

/**
 * LiteSpeed JS Defer 除外リスト（最重要）
 * AdSense スクリプトは defer/async を適用すると動作しない
 */
add_filter('litespeed_optm_js_defer_exc', 'gi_adsense_js_defer_excludes', 99);
function gi_adsense_js_defer_excludes($excludes) {
    $adsense_excludes = [
        // Google AdSense 関連（完全一致用）
        'adsbygoogle.js',
        'pagead2.googlesyndication.com',
        'googlesyndication.com',
        'googleads.g.doubleclick.net',
        'googleadservices.com',
        'www.googletagservices.com',
        'fundingchoicesmessages',
        
        // パターンマッチング用
        'adsbygoogle',
        'googlesyndication',
        'googleads',
        'doubleclick',
        'pagead',
        
        // Google Tag Manager / Analytics（AdSense連携用）
        'googletagmanager.com',
        'google-analytics.com',
        'gtag',
    ];
    
    return array_unique(array_merge($excludes, $adsense_excludes));
}

/**
 * LiteSpeed JS Combine 除外リスト
 * AdSense スクリプトを他のJSと結合すると広告が表示されない
 */
add_filter('litespeed_optm_js_exc', 'gi_adsense_js_combine_excludes', 99);
function gi_adsense_js_combine_excludes($excludes) {
    $adsense_excludes = [
        'adsbygoogle.js',
        'pagead2.googlesyndication.com',
        'googlesyndication',
        'googleads',
        'doubleclick',
        'fundingchoicesmessages',
        'googletagmanager',
        'gtag',
    ];
    
    return array_unique(array_merge($excludes, $adsense_excludes));
}

/**
 * LiteSpeed JS Minify 除外リスト
 */
add_filter('litespeed_optm_js_min_exc', 'gi_adsense_js_minify_excludes', 99);
function gi_adsense_js_minify_excludes($excludes) {
    $adsense_excludes = [
        'adsbygoogle',
        'googlesyndication',
        'googleads',
        'pagead',
    ];
    
    return array_unique(array_merge($excludes, $adsense_excludes));
}

/**
 * LiteSpeed Inline JS 除外
 */
add_filter('litespeed_optm_js_inline_defer_exc', 'gi_adsense_inline_js_excludes', 99);
function gi_adsense_inline_js_excludes($excludes) {
    $adsense_excludes = [
        'adsbygoogle',
        'googlesyndication',
        'window.adsbygoogle',
        'google_ad_client',
        'enable_page_level_ads',
    ];
    
    return array_unique(array_merge($excludes, $adsense_excludes));
}

/**
 * LiteSpeed LazyLoad 除外（広告画像用）
 */
add_filter('litespeed_media_lazy_img_excludes', 'gi_adsense_lazyload_excludes', 99);
function gi_adsense_lazyload_excludes($excludes) {
    $adsense_excludes = [
        'googlesyndication',
        'googleads',
        'doubleclick',
        'adsbygoogle',
    ];
    
    return array_unique(array_merge($excludes, $adsense_excludes));
}

/**
 * LiteSpeed Localize 除外（外部スクリプト最適化から除外）
 */
add_filter('litespeed_optm_localize_exc', 'gi_adsense_localize_excludes', 99);
function gi_adsense_localize_excludes($excludes) {
    $adsense_excludes = [
        'pagead2.googlesyndication.com',
        'googleads.g.doubleclick.net',
        'googleadservices.com',
    ];
    
    return array_unique(array_merge($excludes, $adsense_excludes));
}


/**
 * ============================================================================
 * 2. 手動広告ユニット配置用ヘルパー関数
 * ============================================================================
 */

/**
 * AdSense 手動広告ユニットを出力
 * 
 * @param string $slot_id     広告スロットID（AdSenseで発行されたもの）
 * @param string $format      広告フォーマット（auto, rectangle, horizontal, vertical）
 * @param string $position    配置位置の説明（ログ用）
 * @param array  $options     追加オプション
 * @return void
 */
function gi_display_adsense_unit($slot_id, $format = 'auto', $position = '', $options = []) {
    // AdSense クライアントID（テーマ設定またはSite Kitから取得）
    $client_id = gi_get_adsense_client_id();
    
    if (empty($client_id) || empty($slot_id)) {
        // デバッグモードの場合のみプレースホルダーを表示
        if (defined('WP_DEBUG') && WP_DEBUG) {
            echo '<!-- AdSense: Missing client_id or slot_id -->';
        }
        return;
    }
    
    // デフォルトオプション
    $defaults = [
        'responsive' => true,
        'class' => '',
        'style' => '',
        'data_full_width_responsive' => 'true',
    ];
    $opts = array_merge($defaults, $options);
    
    // スタイル設定
    $style = 'display:block;';
    if (!empty($opts['style'])) {
        $style .= $opts['style'];
    }
    
    // 追加クラス
    $class = 'gi-adsense-unit';
    if (!empty($opts['class'])) {
        $class .= ' ' . $opts['class'];
    }
    if (!empty($position)) {
        $class .= ' gi-adsense-' . sanitize_html_class($position);
    }
    
    ?>
    <aside class="<?php echo esc_attr($class); ?>" aria-label="広告">
        <ins class="adsbygoogle"
             style="<?php echo esc_attr($style); ?>"
             data-ad-client="<?php echo esc_attr($client_id); ?>"
             data-ad-slot="<?php echo esc_attr($slot_id); ?>"
             <?php if ($format !== 'auto'): ?>
             data-ad-format="<?php echo esc_attr($format); ?>"
             <?php else: ?>
             data-ad-format="auto"
             <?php endif; ?>
             <?php if ($opts['data_full_width_responsive'] === 'true'): ?>
             data-full-width-responsive="true"
             <?php endif; ?>
        ></ins>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </aside>
    <?php
}

/**
 * AdSense クライアントIDを取得
 * Site Kit 設定 → テーマ設定 → 定数の順で確認
 * 
 * @return string クライアントID
 */
function gi_get_adsense_client_id() {
    // 1. テーマ設定から取得
    $client_id = get_option('gi_adsense_client_id', '');
    
    // 2. Site Kit から取得を試みる
    if (empty($client_id)) {
        $sitekit_settings = get_option('googlesitekit_adsense_settings', []);
        if (!empty($sitekit_settings['clientID'])) {
            $client_id = $sitekit_settings['clientID'];
        }
    }
    
    // 3. 定数から取得
    if (empty($client_id) && defined('GI_ADSENSE_CLIENT_ID')) {
        $client_id = GI_ADSENSE_CLIENT_ID;
    }
    
    return $client_id;
}

/**
 * AdSense スロットIDを取得（位置別）
 * 
 * @param string $position 配置位置
 * @return string スロットID
 */
function gi_get_adsense_slot_id($position = 'default') {
    $slots = get_option('gi_adsense_slots', []);
    
    // デフォルトのスロット設定
    $default_slots = [
        'content_top' => '',
        'content_middle' => '',
        'content_bottom' => '',
        'sidebar_top' => '',
        'sidebar_middle' => '',
        'sidebar_bottom' => '',
        'infeed' => '',
        'multiplex' => '',
    ];
    
    $slots = array_merge($default_slots, $slots);
    
    return isset($slots[$position]) ? $slots[$position] : '';
}


/**
 * ============================================================================
 * 3. 記事本文への手動広告挿入機能
 * ============================================================================
 */

/**
 * 記事本文に手動広告を挿入
 * 自動広告が効かない場合のフォールバック
 * 
 * @param string $content 記事本文
 * @return string 広告挿入後の記事本文
 */
function gi_insert_manual_adsense_in_content($content) {
    // 条件チェック
    if (!is_singular() || is_feed() || is_admin()) {
        return $content;
    }
    
    // REST APIリクエストは除外
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return $content;
    }
    
    // 手動広告挿入が無効の場合はスキップ
    if (get_option('gi_adsense_manual_insert_enabled', '0') !== '1') {
        return $content;
    }
    
    // 投稿タイプチェック
    $allowed_post_types = apply_filters('gi_adsense_allowed_post_types', ['grant', 'column', 'post']);
    if (!in_array(get_post_type(), $allowed_post_types, true)) {
        return $content;
    }
    
    // 広告挿入位置（H2の番号、0始まり）
    $insert_positions = get_option('gi_adsense_insert_positions', [0, 2]);
    
    // スロットID取得
    $slot_id = gi_get_adsense_slot_id('content_middle');
    if (empty($slot_id)) {
        return $content;
    }
    
    // H2タグの位置を取得
    $h2_pattern = '/<h2[^>]*>/i';
    preg_match_all($h2_pattern, $content, $matches, PREG_OFFSET_CAPTURE);
    
    if (empty($matches[0])) {
        return $content;
    }
    
    // 広告HTMLを生成
    ob_start();
    gi_display_adsense_unit($slot_id, 'auto', 'content-middle', [
        'class' => 'gi-adsense-in-content',
    ]);
    $ad_html = ob_get_clean();
    
    // 後ろから挿入（位置ズレ防止）
    $insert_positions = array_reverse(array_unique($insert_positions));
    
    foreach ($insert_positions as $pos) {
        if (isset($matches[0][$pos])) {
            $offset = $matches[0][$pos][1];
            $content = substr_replace($content, $ad_html, $offset, 0);
        }
    }
    
    return $content;
}
// 手動広告挿入を有効化する場合は以下をアンコメント
// add_filter('the_content', 'gi_insert_manual_adsense_in_content', 25);


/**
 * ============================================================================
 * 4. コンセントモード最適化
 * ============================================================================
 */

/**
 * Site Kit コンセントモードのデフォルト設定を調整
 * ad_storage を granted に変更して広告表示を優先
 * 
 * 注意: GDPR/個人情報保護法の要件を満たす場合のみ使用
 */
add_action('wp_head', 'gi_optimize_consent_mode', 1);
function gi_optimize_consent_mode() {
    // Site Kit が有効でない場合はスキップ
    if (!class_exists('Google\Site_Kit\Plugin')) {
        return;
    }
    
    // 日本向けサイトの場合、コンセントモードを調整
    // 日本ではGDPR適用外のため、デフォルトでgrantedに設定可能
    $optimize_consent = get_option('gi_adsense_optimize_consent', '1');
    
    if ($optimize_consent !== '1') {
        return;
    }
    
    ?>
    <script>
    // AdSense コンセントモード最適化（日本向け）
    // Site Kit のデフォルト設定を上書き
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    
    // 日本向けサイトの場合、広告関連を granted に設定
    gtag('consent', 'update', {
        'ad_storage': 'granted',
        'ad_user_data': 'granted',
        'ad_personalization': 'granted',
        'analytics_storage': 'granted'
    });
    </script>
    <?php
}


/**
 * ============================================================================
 * 5. Site Kit との重複防止
 * ============================================================================
 */

/**
 * Site Kit AdSense コードとテーマ出力の重複チェック
 */
add_action('wp_head', 'gi_check_adsense_duplicate', 0);
function gi_check_adsense_duplicate() {
    // Site Kit が AdSense コードを出力しているかチェック
    $sitekit_adsense_active = false;
    
    if (class_exists('Google\Site_Kit\Plugin')) {
        $sitekit_settings = get_option('googlesitekit_adsense_settings', []);
        if (!empty($sitekit_settings['useSnippet']) && $sitekit_settings['useSnippet'] === true) {
            $sitekit_adsense_active = true;
        }
    }
    
    // グローバル変数で状態を保持
    $GLOBALS['gi_sitekit_adsense_active'] = $sitekit_adsense_active;
}

/**
 * テーマからのAdSenseコード出力制御
 * Site Kit が有効な場合は重複を防ぐ
 * 
 * @param bool $should_output
 * @return bool
 */
function gi_should_output_adsense_code() {
    // Site Kit が AdSense を出力している場合は false
    if (isset($GLOBALS['gi_sitekit_adsense_active']) && $GLOBALS['gi_sitekit_adsense_active']) {
        return false;
    }
    
    return true;
}


/**
 * ============================================================================
 * 6. 管理画面設定
 * ============================================================================
 */

/**
 * AdSense 設定ページを追加
 */
add_action('admin_menu', 'gi_adsense_add_settings_page');
function gi_adsense_add_settings_page() {
    add_submenu_page(
        'options-general.php',
        'AdSense設定',
        'AdSense設定',
        'manage_options',
        'gi-adsense-settings',
        'gi_adsense_settings_page'
    );
}

/**
 * AdSense 設定ページ表示
 */
function gi_adsense_settings_page() {
    // 設定保存処理
    if (isset($_POST['gi_adsense_save']) && check_admin_referer('gi_adsense_settings')) {
        update_option('gi_adsense_client_id', sanitize_text_field($_POST['client_id']));
        update_option('gi_adsense_optimize_consent', isset($_POST['optimize_consent']) ? '1' : '0');
        update_option('gi_adsense_manual_insert_enabled', isset($_POST['manual_insert_enabled']) ? '1' : '0');
        
        // スロットID保存
        $slots = [
            'content_top' => sanitize_text_field($_POST['slot_content_top'] ?? ''),
            'content_middle' => sanitize_text_field($_POST['slot_content_middle'] ?? ''),
            'content_bottom' => sanitize_text_field($_POST['slot_content_bottom'] ?? ''),
            'sidebar_top' => sanitize_text_field($_POST['slot_sidebar_top'] ?? ''),
            'sidebar_middle' => sanitize_text_field($_POST['slot_sidebar_middle'] ?? ''),
            'sidebar_bottom' => sanitize_text_field($_POST['slot_sidebar_bottom'] ?? ''),
            'infeed' => sanitize_text_field($_POST['slot_infeed'] ?? ''),
            'multiplex' => sanitize_text_field($_POST['slot_multiplex'] ?? ''),
        ];
        update_option('gi_adsense_slots', $slots);
        
        // 挿入位置保存
        $positions = array_map('intval', explode(',', $_POST['insert_positions'] ?? '0,2'));
        update_option('gi_adsense_insert_positions', $positions);
        
        echo '<div class="notice notice-success"><p>設定を保存しました。</p></div>';
    }
    
    // 現在の設定値
    $client_id = get_option('gi_adsense_client_id', '');
    $optimize_consent = get_option('gi_adsense_optimize_consent', '1');
    $manual_insert_enabled = get_option('gi_adsense_manual_insert_enabled', '0');
    $slots = get_option('gi_adsense_slots', []);
    $insert_positions = get_option('gi_adsense_insert_positions', [0, 2]);
    
    // Site Kit 状態確認
    $sitekit_active = class_exists('Google\Site_Kit\Plugin');
    $sitekit_settings = get_option('googlesitekit_adsense_settings', []);
    $sitekit_adsense_enabled = !empty($sitekit_settings['useSnippet']);
    
    ?>
    <div class="wrap">
        <h1>AdSense 最適化設定</h1>
        
        <?php if ($sitekit_active): ?>
        <div class="notice notice-info">
            <p>
                <strong>Site Kit 検出:</strong> 
                AdSense モジュール: <?php echo $sitekit_adsense_enabled ? '<span style="color:green;">有効</span>' : '<span style="color:gray;">無効</span>'; ?>
                <?php if ($sitekit_adsense_enabled): ?>
                <br><small>Site Kit が AdSense コードを出力しています。手動設定と重複しないよう注意してください。</small>
                <?php endif; ?>
            </p>
        </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <?php wp_nonce_field('gi_adsense_settings'); ?>
            
            <h2>基本設定</h2>
            <table class="form-table">
                <tr>
                    <th><label for="client_id">AdSense クライアントID</label></th>
                    <td>
                        <input type="text" name="client_id" id="client_id" class="regular-text" 
                               value="<?php echo esc_attr($client_id); ?>" 
                               placeholder="ca-pub-XXXXXXXXXXXXXXXX">
                        <p class="description">
                            AdSense の「パブリッシャーID」を入力（例: ca-pub-1234567890123456）
                            <?php if ($sitekit_adsense_enabled): ?>
                            <br><strong>※ Site Kit が有効なため、通常は空欄のままで OK です。</strong>
                            <?php endif; ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th>コンセントモード最適化</th>
                    <td>
                        <label>
                            <input type="checkbox" name="optimize_consent" value="1" <?php checked($optimize_consent, '1'); ?>>
                            日本向けサイト用にコンセントモードを最適化する
                        </label>
                        <p class="description">
                            Site Kit のデフォルト設定（ad_storage: denied）を上書きし、広告表示を優先します。<br>
                            日本ではGDPRの適用対象外のため、通常は有効で問題ありません。
                        </p>
                    </td>
                </tr>
            </table>
            
            <h2>手動広告挿入（自動広告のフォールバック）</h2>
            <table class="form-table">
                <tr>
                    <th>手動広告挿入</th>
                    <td>
                        <label>
                            <input type="checkbox" name="manual_insert_enabled" value="1" <?php checked($manual_insert_enabled, '1'); ?>>
                            記事本文に手動で広告を挿入する
                        </label>
                        <p class="description">
                            自動広告が表示されない場合に、H2見出しの前に手動で広告を挿入します。
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="insert_positions">挿入位置（H2番号）</label></th>
                    <td>
                        <input type="text" name="insert_positions" id="insert_positions" class="regular-text" 
                               value="<?php echo esc_attr(implode(',', $insert_positions)); ?>" 
                               placeholder="0,2">
                        <p class="description">
                            広告を挿入するH2見出しの番号（0始まり、カンマ区切り）<br>
                            例: 0,2 = 1番目と3番目のH2の前に挿入
                        </p>
                    </td>
                </tr>
            </table>
            
            <h2>広告スロットID設定</h2>
            <p class="description">AdSense で作成した広告ユニットのスロットIDを設定します。手動広告挿入で使用されます。</p>
            <table class="form-table">
                <tr>
                    <th><label for="slot_content_middle">コンテンツ中央</label></th>
                    <td>
                        <input type="text" name="slot_content_middle" id="slot_content_middle" class="regular-text" 
                               value="<?php echo esc_attr($slots['content_middle'] ?? ''); ?>" 
                               placeholder="1234567890">
                        <p class="description">記事本文中に挿入される広告</p>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="slot_sidebar_top">サイドバー上部</label></th>
                    <td>
                        <input type="text" name="slot_sidebar_top" id="slot_sidebar_top" class="regular-text" 
                               value="<?php echo esc_attr($slots['sidebar_top'] ?? ''); ?>" 
                               placeholder="1234567890">
                    </td>
                </tr>
                
                <tr>
                    <th><label for="slot_sidebar_middle">サイドバー中央</label></th>
                    <td>
                        <input type="text" name="slot_sidebar_middle" id="slot_sidebar_middle" class="regular-text" 
                               value="<?php echo esc_attr($slots['sidebar_middle'] ?? ''); ?>" 
                               placeholder="1234567890">
                    </td>
                </tr>
                
                <tr>
                    <th><label for="slot_infeed">インフィード</label></th>
                    <td>
                        <input type="text" name="slot_infeed" id="slot_infeed" class="regular-text" 
                               value="<?php echo esc_attr($slots['infeed'] ?? ''); ?>" 
                               placeholder="1234567890">
                        <p class="description">記事一覧に挿入されるインフィード広告</p>
                    </td>
                </tr>
            </table>
            
            <h2>LiteSpeed Cache 連携状態</h2>
            <table class="form-table">
                <tr>
                    <th>除外設定</th>
                    <td>
                        <p style="color: green;">✓ AdSense スクリプトは以下の最適化から自動的に除外されます：</p>
                        <ul style="list-style: disc; margin-left: 20px;">
                            <li>JS Defer（遅延読み込み）</li>
                            <li>JS Combine（結合）</li>
                            <li>JS Minify（圧縮）</li>
                            <li>Inline JS Defer</li>
                            <li>Image LazyLoad（広告画像）</li>
                            <li>JS Localize（外部スクリプト最適化）</li>
                        </ul>
                        <p class="description">
                            これらの設定は <code>inc/adsense-optimization.php</code> で管理されています。
                        </p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="gi_adsense_save" class="button button-primary" value="設定を保存">
            </p>
        </form>
        
        <hr>
        
        <h2>トラブルシューティング</h2>
        <div style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
            <h3 style="margin-top: 0;">広告が表示されない場合のチェックリスト</h3>
            <ol>
                <li><strong>キャッシュクリア:</strong> LiteSpeed Cache → 全キャッシュ消去 を実行</li>
                <li><strong>AdSense 承認状態:</strong> AdSense 管理画面でサイトが承認済みか確認</li>
                <li><strong>コンセントモード:</strong> 上記の「コンセントモード最適化」を有効化</li>
                <li><strong>手動広告テスト:</strong> 「手動広告挿入」を有効化し、スロットIDを設定</li>
                <li><strong>ブラウザ確認:</strong> シークレットモードで確認（広告ブロッカー無効）</li>
            </ol>
            
            <h4>デバッグ方法</h4>
            <p>ブラウザの開発者ツール（F12）→ Console タブで以下を確認：</p>
            <ul>
                <li>「adsbygoogle.push() error」が出ていないか</li>
                <li>「Ad request url」が正常に発行されているか</li>
            </ul>
        </div>
    </div>
    <?php
}


/**
 * ============================================================================
 * 7. AdSense 広告用 CSS
 * ============================================================================
 */

/**
 * AdSense 広告のスタイルを出力
 */
add_action('wp_head', 'gi_adsense_styles', 100);
function gi_adsense_styles() {
    ?>
    <style>
    /* AdSense ユニット共通スタイル */
    .gi-adsense-unit {
        margin: 1.5em 0;
        text-align: center;
        clear: both;
    }
    
    .gi-adsense-in-content {
        margin: 2em 0;
        padding: 0;
    }
    
    /* AdSense コンテナの最小高さ（CLS対策） */
    .gi-adsense-unit ins.adsbygoogle {
        min-height: 100px;
    }
    
    /* サイドバー用 */
    .gi-adsense-sidebar_top,
    .gi-adsense-sidebar_middle,
    .gi-adsense-sidebar_bottom {
        margin: 1em 0;
    }
    
    /* モバイル対応 */
    @media (max-width: 768px) {
        .gi-adsense-unit {
            margin: 1em 0;
        }
        
        .gi-adsense-unit ins.adsbygoogle {
            min-height: 50px;
        }
    }
    </style>
    <?php
}
