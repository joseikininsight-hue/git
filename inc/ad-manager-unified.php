<?php
/**
 * Unified Ad Manager - アフィリエイト広告と手動AdSense広告の統合管理
 * 
 * Features:
 * - アフィリエイト広告の管理（既存システム）
 * - 手動設定のAdSense広告の管理
 * - 広告位置の統一管理
 * - 広告タイプ別の表示制御
 * - 優先度設定
 * 
 * @package Joseikin_Insight
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class JI_Unified_Ad_Manager {
    
    private $affiliate_manager;
    private $manual_adsense_codes = array();
    
    /**
     * コンストラクタ
     */
    public function __construct() {
        // アフィリエイト広告マネージャーの取得
        if (class_exists('JI_Affiliate_Ad_Manager')) {
            global $ji_affiliate_ad_manager;
            $this->affiliate_manager = $ji_affiliate_ad_manager;
        }
        
        // 手動AdSense広告コードの定義
        $this->define_manual_adsense_codes();
        
        // 管理メニューの追加
        add_action('admin_menu', array($this, 'add_unified_menu'), 100);
        
        // 設定の保存
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * 手動AdSense広告コードの定義
     */
    private function define_manual_adsense_codes() {
        // データベースから手動広告設定を取得
        $saved_codes = get_option('ji_manual_adsense_codes', array());
        
        // デフォルトの手動AdSense広告
        $default_codes = array(
            'in_content_ad' => array(
                'name' => '本文内広告',
                'code' => '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1430655994298384" crossorigin="anonymous"></script>
<!-- 本文内広告 -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-1430655994298384"
     data-ad-slot="4870183643"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>',
                'enabled' => true,
                'positions' => array(
                    'single_grant_content_middle',
                    'single_column_content_middle'
                )
            ),
            'sidebar_ad' => array(
                'name' => 'サイドバー広告',
                'code' => '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1430655994298384" crossorigin="anonymous"></script>
<!-- サイドバー広告 -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-1430655994298384"
     data-ad-slot="1234567890"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>',
                'enabled' => false,
                'positions' => array(
                    'single_grant_sidebar_middle',
                    'single_column_sidebar_middle',
                    'archive_grant_sidebar_middle'
                )
            )
        );
        
        // 保存されたコードとデフォルトをマージ
        $this->manual_adsense_codes = array_merge($default_codes, $saved_codes);
    }
    
    /**
     * 統合管理メニューの追加
     */
    public function add_unified_menu() {
        add_submenu_page(
            'ji-affiliate-ads', // 親メニュー
            '手動AdSense管理',
            '手動AdSense',
            'manage_options',
            'ji-manual-adsense',
            array($this, 'render_manual_adsense_page')
        );
        
        // 統合設定ページ
        add_submenu_page(
            'ji-affiliate-ads',
            '広告統合設定',
            '統合設定',
            'manage_options',
            'ji-ad-unified-settings',
            array($this, 'render_unified_settings_page')
        );
    }
    
    /**
     * 設定の登録
     */
    public function register_settings() {
        register_setting('ji_manual_adsense_settings', 'ji_manual_adsense_codes');
        register_setting('ji_ad_unified_settings', 'ji_ad_display_priority'); // affiliate / adsense / mixed
        register_setting('ji_ad_unified_settings', 'ji_ad_adsense_ratio'); // AdSense表示比率（0-100%）
    }
    
    /**
     * 手動AdSense管理ページ
     */
    public function render_manual_adsense_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // 設定の保存
        if (isset($_POST['save_manual_adsense']) && check_admin_referer('ji_manual_adsense_save')) {
            $codes = array();
            
            // 既存の広告を更新
            if (isset($_POST['ad_codes']) && is_array($_POST['ad_codes'])) {
                foreach ($_POST['ad_codes'] as $key => $data) {
                    $codes[$key] = array(
                        'name' => sanitize_text_field($data['name']),
                        'code' => wp_kses_post($data['code']),
                        'enabled' => isset($data['enabled']),
                        'positions' => isset($data['positions']) && is_array($data['positions']) 
                            ? array_map('sanitize_text_field', $data['positions']) 
                            : array()
                    );
                }
            }
            
            update_option('ji_manual_adsense_codes', $codes);
            $this->manual_adsense_codes = $codes;
            
            echo '<div class="notice notice-success"><p>手動AdSense設定を保存しました。</p></div>';
        }
        
        // 新規追加
        if (isset($_POST['add_new_adsense']) && check_admin_referer('ji_manual_adsense_add')) {
            $new_key = 'custom_' . time();
            $this->manual_adsense_codes[$new_key] = array(
                'name' => sanitize_text_field($_POST['new_name']),
                'code' => wp_kses_post($_POST['new_code']),
                'enabled' => isset($_POST['new_enabled']),
                'positions' => isset($_POST['new_positions']) && is_array($_POST['new_positions']) 
                    ? array_map('sanitize_text_field', $_POST['new_positions']) 
                    : array()
            );
            
            update_option('ji_manual_adsense_codes', $this->manual_adsense_codes);
            
            echo '<div class="notice notice-success"><p>新しいAdSense広告を追加しました。</p></div>';
        }
        
        // 削除
        if (isset($_GET['delete']) && check_admin_referer('ji_delete_adsense_' . $_GET['delete'])) {
            $delete_key = sanitize_text_field($_GET['delete']);
            unset($this->manual_adsense_codes[$delete_key]);
            update_option('ji_manual_adsense_codes', $this->manual_adsense_codes);
            
            echo '<div class="notice notice-success"><p>AdSense広告を削除しました。</p></div>';
        }
        
        ?>
        <div class="wrap">
            <h1>手動AdSense広告管理</h1>
            <hr class="wp-header-end">
            
            <div class="ji-notice notice-info" style="margin: 20px 0; padding: 12px; border-left: 4px solid #2271b1; background: #f0f6fc;">
                <p><strong>手動AdSense広告とは？</strong></p>
                <p>Google AdSenseから取得した広告コードを直接管理できます。アフィリエイト広告と組み合わせて表示することも可能です。</p>
            </div>
            
            <!-- 既存の広告一覧 -->
            <?php if (!empty($this->manual_adsense_codes)): ?>
            <h2>登録済みAdSense広告</h2>
            <form method="post" action="">
                <?php wp_nonce_field('ji_manual_adsense_save'); ?>
                
                <?php foreach ($this->manual_adsense_codes as $key => $ad): ?>
                <div class="postbox" style="margin-top: 20px;">
                    <div class="postbox-header">
                        <h3 class="hndle">
                            <?php echo esc_html($ad['name']); ?>
                            <span style="margin-left: 10px;">
                                <?php if ($ad['enabled']): ?>
                                    <span class="ji-status-badge active">有効</span>
                                <?php else: ?>
                                    <span class="ji-status-badge inactive">無効</span>
                                <?php endif; ?>
                            </span>
                        </h3>
                        <div class="handle-actions">
                            <a href="?page=ji-manual-adsense&delete=<?php echo esc_attr($key); ?>&_wpnonce=<?php echo wp_create_nonce('ji_delete_adsense_' . $key); ?>" 
                               class="button" 
                               onclick="return confirm('この広告を削除しますか？')">削除</a>
                        </div>
                    </div>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th><label for="ad_name_<?php echo esc_attr($key); ?>">広告名</label></th>
                                <td>
                                    <input type="text" 
                                           name="ad_codes[<?php echo esc_attr($key); ?>][name]" 
                                           id="ad_name_<?php echo esc_attr($key); ?>"
                                           value="<?php echo esc_attr($ad['name']); ?>" 
                                           class="regular-text" 
                                           required>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="ad_code_<?php echo esc_attr($key); ?>">広告コード</label></th>
                                <td>
                                    <textarea name="ad_codes[<?php echo esc_attr($key); ?>][code]" 
                                              id="ad_code_<?php echo esc_attr($key); ?>"
                                              rows="10" 
                                              class="large-text code" 
                                              required><?php echo esc_textarea($ad['code']); ?></textarea>
                                    <p class="description">Google AdSenseから取得した広告コードをそのまま貼り付けてください。</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="ad_positions_<?php echo esc_attr($key); ?>">表示位置</label></th>
                                <td>
                                    <?php $this->render_position_checkboxes($key, $ad['positions']); ?>
                                </td>
                            </tr>
                            <tr>
                                <th>有効化</th>
                                <td>
                                    <label>
                                        <input type="checkbox" 
                                               name="ad_codes[<?php echo esc_attr($key); ?>][enabled]" 
                                               value="1" 
                                               <?php checked($ad['enabled'], true); ?>>
                                        この広告を有効にする
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <p class="submit">
                    <input type="submit" name="save_manual_adsense" class="button button-primary" value="変更を保存">
                </p>
            </form>
            <?php endif; ?>
            
            <!-- 新規追加フォーム -->
            <h2 style="margin-top: 40px;">新しいAdSense広告を追加</h2>
            <form method="post" action="" class="postbox">
                <?php wp_nonce_field('ji_manual_adsense_add'); ?>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th><label for="new_name">広告名</label></th>
                            <td>
                                <input type="text" name="new_name" id="new_name" class="regular-text" placeholder="例: サイドバー広告" required>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="new_code">広告コード</label></th>
                            <td>
                                <textarea name="new_code" id="new_code" rows="10" class="large-text code" placeholder="<script async src=..."></textarea>
                                <p class="description">Google AdSenseから取得した広告コードを貼り付けてください。</p>
                            </td>
                        </tr>
                        <tr>
                            <th>表示位置</th>
                            <td>
                                <?php $this->render_position_checkboxes('new', array()); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>有効化</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="new_enabled" value="1" checked>
                                    この広告を有効にする
                                </label>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" name="add_new_adsense" class="button button-primary" value="追加">
                    </p>
                </div>
            </form>
        </div>
        
        <style>
        .ji-status-badge {
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }
        .ji-status-badge.active {
            background: #00a32a;
            color: white;
        }
        .ji-status-badge.inactive {
            background: #dcdcde;
            color: #50575e;
        }
        .ji-position-group {
            margin-bottom: 15px;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
        }
        .ji-position-group h4 {
            margin: 0 0 10px 0;
            font-size: 13px;
            font-weight: 600;
        }
        .ji-position-group label {
            display: block;
            margin: 5px 0;
        }
        </style>
        <?php
    }
    
    /**
     * 位置チェックボックスのレンダリング
     */
    private function render_position_checkboxes($prefix, $selected_positions = array()) {
        $position_groups = array(
            'シングルページ - 補助金' => array(
                'single_grant_sidebar_top' => 'サイドバー最上部',
                'single_grant_sidebar_middle' => 'サイドバー中央',
                'single_grant_sidebar_bottom' => 'サイドバー最下部',
                'single_grant_content_top' => 'コンテンツ上部',
                'single_grant_content_middle' => 'コンテンツ中央',
                'single_grant_content_bottom' => 'コンテンツ下部',
            ),
            'シングルページ - コラム' => array(
                'single_column_sidebar_top' => 'サイドバー上部',
                'single_column_sidebar_middle' => 'サイドバー中央',
                'single_column_sidebar_bottom' => 'サイドバー下部',
                'single_column_content_top' => 'コンテンツ上部',
                'single_column_content_middle' => 'コンテンツ中央',
                'single_column_content_bottom' => 'コンテンツ下部',
            ),
            'アーカイブページ - 補助金' => array(
                'archive_grant_sidebar_top' => 'サイドバー上部',
                'archive_grant_sidebar_middle' => 'サイドバー中央',
                'archive_grant_sidebar_bottom' => 'サイドバー下部',
                'archive_grant_content_top' => 'コンテンツ上部',
                'archive_grant_content_bottom' => 'コンテンツ下部',
            ),
            'アーカイブページ - コラム' => array(
                'archive_column_sidebar_top' => 'サイドバー上部',
                'archive_column_sidebar_middle' => 'サイドバー中央',
                'archive_column_sidebar_bottom' => 'サイドバー下部',
            ),
            'Taxonomy アーカイブ' => array(
                'category_grant_sidebar_top' => 'カテゴリ: SB上部',
                'category_grant_sidebar_middle' => 'カテゴリ: SB中央',
                'prefecture_grant_sidebar_top' => '都道府県: SB上部',
                'prefecture_grant_sidebar_middle' => '都道府県: SB中央',
                'municipality_grant_sidebar_top' => '市町村: SB上部',
                'purpose_grant_sidebar_top' => '目的: SB上部',
                'tag_grant_sidebar_top' => 'タグ: SB上部',
            )
        );
        
        foreach ($position_groups as $group_name => $positions) {
            echo '<div class="ji-position-group">';
            echo '<h4>' . esc_html($group_name) . '</h4>';
            foreach ($positions as $position_key => $position_label) {
                $checked = in_array($position_key, $selected_positions) ? 'checked' : '';
                $input_name = ($prefix === 'new') ? 'new_positions[]' : "ad_codes[{$prefix}][positions][]";
                echo '<label>';
                echo '<input type="checkbox" name="' . esc_attr($input_name) . '" value="' . esc_attr($position_key) . '" ' . $checked . '> ';
                echo esc_html($position_label);
                echo '</label>';
            }
            echo '</div>';
        }
    }
    
    /**
     * 統合設定ページ
     */
    public function render_unified_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // 設定の保存
        if (isset($_POST['save_unified_settings']) && check_admin_referer('ji_unified_settings_save')) {
            update_option('ji_ad_display_priority', sanitize_text_field($_POST['display_priority']));
            update_option('ji_ad_adsense_ratio', intval($_POST['adsense_ratio']));
            
            echo '<div class="notice notice-success"><p>統合設定を保存しました。</p></div>';
        }
        
        $display_priority = get_option('ji_ad_display_priority', 'mixed');
        $adsense_ratio = get_option('ji_ad_adsense_ratio', 50);
        
        ?>
        <div class="wrap">
            <h1>広告統合設定</h1>
            <hr class="wp-header-end">
            
            <form method="post" action="">
                <?php wp_nonce_field('ji_unified_settings_save'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">広告表示の優先度</th>
                        <td>
                            <label>
                                <input type="radio" name="display_priority" value="affiliate" <?php checked($display_priority, 'affiliate'); ?>>
                                アフィリエイト広告を優先
                            </label><br>
                            <label>
                                <input type="radio" name="display_priority" value="adsense" <?php checked($display_priority, 'adsense'); ?>>
                                手動AdSense広告を優先
                            </label><br>
                            <label>
                                <input type="radio" name="display_priority" value="mixed" <?php checked($display_priority, 'mixed'); ?>>
                                混合表示（比率設定）
                            </label>
                            <p class="description">同じ位置に複数の広告タイプが設定されている場合の表示優先度を設定します。</p>
                        </td>
                    </tr>
                    
                    <tr id="adsense_ratio_row" <?php echo $display_priority !== 'mixed' ? 'style="display:none;"' : ''; ?>>
                        <th scope="row">AdSense表示比率</th>
                        <td>
                            <input type="range" name="adsense_ratio" id="adsense_ratio_slider" min="0" max="100" value="<?php echo esc_attr($adsense_ratio); ?>" style="width: 300px;">
                            <span id="adsense_ratio_value"><?php echo esc_html($adsense_ratio); ?>%</span>
                            <p class="description">
                                混合表示時のAdSense広告の表示確率。<br>
                                0%: アフィリエイトのみ、100%: AdSenseのみ、50%: 半々
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="save_unified_settings" class="button button-primary" value="設定を保存">
                </p>
            </form>
            
            <div class="ji-info-section" style="margin-top: 40px; padding: 20px; background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 4px;">
                <h3>設定ガイド</h3>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li><strong>アフィリエイト広告を優先:</strong> アフィリエイト広告が優先的に表示され、なければAdSense広告が表示されます。</li>
                    <li><strong>手動AdSense広告を優先:</strong> 手動AdSense広告が優先的に表示され、なければアフィリエイト広告が表示されます。</li>
                    <li><strong>混合表示:</strong> 設定した比率でランダムに広告タイプが選択されます。</li>
                </ul>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // 優先度設定の変更時に比率設定の表示/非表示を切り替え
            $('input[name="display_priority"]').on('change', function() {
                if ($(this).val() === 'mixed') {
                    $('#adsense_ratio_row').show();
                } else {
                    $('#adsense_ratio_row').hide();
                }
            });
            
            // スライダーの値変更時に表示を更新
            $('#adsense_ratio_slider').on('input', function() {
                $('#adsense_ratio_value').text($(this).val() + '%');
            });
        });
        </script>
        <?php
    }
    
    /**
     * 統合広告取得
     * 
     * @param string $position 広告位置
     * @param array $options オプション
     * @return string 広告HTML
     */
    public function get_unified_ad($position, $options = array()) {
        $display_priority = get_option('ji_ad_display_priority', 'mixed');
        $adsense_ratio = get_option('ji_ad_adsense_ratio', 50);
        
        // 表示優先度による処理
        if ($display_priority === 'affiliate') {
            // アフィリエイト優先
            $affiliate_ad = $this->get_affiliate_ad($position, $options);
            if (!empty($affiliate_ad)) {
                return $affiliate_ad;
            }
            return $this->get_manual_adsense_ad($position);
            
        } elseif ($display_priority === 'adsense') {
            // AdSense優先
            $adsense_ad = $this->get_manual_adsense_ad($position);
            if (!empty($adsense_ad)) {
                return $adsense_ad;
            }
            return $this->get_affiliate_ad($position, $options);
            
        } else {
            // 混合表示（比率に基づくランダム選択）
            $random = mt_rand(1, 100);
            
            if ($random <= $adsense_ratio) {
                // AdSenseを表示
                $adsense_ad = $this->get_manual_adsense_ad($position);
                if (!empty($adsense_ad)) {
                    return $adsense_ad;
                }
                // フォールバック: アフィリエイト
                return $this->get_affiliate_ad($position, $options);
            } else {
                // アフィリエイトを表示
                $affiliate_ad = $this->get_affiliate_ad($position, $options);
                if (!empty($affiliate_ad)) {
                    return $affiliate_ad;
                }
                // フォールバック: AdSense
                return $this->get_manual_adsense_ad($position);
            }
        }
    }
    
    /**
     * アフィリエイト広告取得
     */
    private function get_affiliate_ad($position, $options = array()) {
        if (!$this->affiliate_manager || !method_exists($this->affiliate_manager, 'render_ad')) {
            return '';
        }
        
        return $this->affiliate_manager->render_ad($position, $options);
    }
    
    /**
     * 手動AdSense広告取得
     */
    private function get_manual_adsense_ad($position) {
        $available_ads = array();
        
        foreach ($this->manual_adsense_codes as $key => $ad) {
            if (!$ad['enabled']) {
                continue;
            }
            
            if (in_array($position, $ad['positions'])) {
                $available_ads[] = $ad;
            }
        }
        
        if (empty($available_ads)) {
            return '';
        }
        
        // ランダムに1つ選択
        $selected_ad = $available_ads[array_rand($available_ads)];
        
        return '<div class="ji-manual-adsense-ad" data-position="' . esc_attr($position) . '">' . 
               $selected_ad['code'] . 
               '</div>';
    }
}

// グローバルインスタンス作成
global $ji_unified_ad_manager;
$ji_unified_ad_manager = new JI_Unified_Ad_Manager();

/**
 * ヘルパー関数: 統合広告を表示
 * 
 * @param string $position 広告位置
 * @param array $options オプション
 */
function ji_display_unified_ad($position, $options = array()) {
    global $ji_unified_ad_manager;
    
    if ($ji_unified_ad_manager) {
        echo $ji_unified_ad_manager->get_unified_ad($position, $options);
    }
}
