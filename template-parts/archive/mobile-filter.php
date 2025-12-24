<?php
/**
 * Template Part: Mobile Filter
 * モバイル用フィルターパネルテンプレート
 * 
 * 使い方: <?php include(get_template_directory() . '/template-parts/archive/mobile-filter.php'); ?>
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) exit;

// カテゴリデータの取得
$mobile_categories = get_terms([
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 20,
]);

// 都道府県データの取得
$mobile_prefectures = get_terms([
    'taxonomy' => 'grant_prefecture',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 47,
]);

// 地域グループ
$mobile_region_groups = [
    'hokkaido' => '北海道',
    'tohoku' => '東北',
    'kanto' => '関東',
    'chubu' => '中部',
    'kinki' => '近畿',
    'chugoku' => '中国',
    'shikoku' => '四国',
    'kyushu' => '九州・沖縄'
];
?>

<!-- モバイルフィルタートグルボタン -->
<button type="button" id="mobile-filter-toggle" class="mobile-filter-toggle" aria-expanded="false" aria-controls="mobile-filter-panel">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
    </svg>
    <span>絞り込み</span>
    <span class="mobile-filter-count" id="mobile-filter-count" style="display: none;">0</span>
</button>

<!-- オーバーレイ -->
<div class="mobile-filter-overlay" id="mobile-filter-overlay" aria-hidden="true"></div>

<!-- モバイルフィルターパネル -->
<div class="mobile-filter-panel" id="mobile-filter-panel" role="dialog" aria-modal="true" aria-labelledby="mobile-filter-title">
    
    <!-- パネルヘッダー -->
    <div class="mobile-filter-header">
        <h3 id="mobile-filter-title">絞り込み検索</h3>
        <button type="button" id="mobile-filter-close" class="mobile-filter-close" aria-label="閉じる">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
    
    <!-- パネルコンテンツ -->
    <div class="mobile-filter-content">
        
        <!-- キーワード検索 -->
        <div class="mobile-filter-section">
            <label class="mobile-filter-label" for="mobile-keyword-search">キーワード検索</label>
            <div class="mobile-search-box">
                <input type="text" 
                       id="mobile-keyword-search" 
                       class="mobile-search-input" 
                       placeholder="助成金名・キーワードで検索">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="search-icon">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
            </div>
        </div>
        
        <!-- カテゴリフィルター -->
        <div class="mobile-filter-section">
            <button type="button" class="mobile-filter-accordion" aria-expanded="false">
                <span class="mobile-filter-label">カテゴリ</span>
                <span class="mobile-filter-badge" id="mobile-category-badge" style="display: none;">0</span>
                <svg class="accordion-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </button>
            <div class="mobile-filter-options" style="display: none;">
                <?php if ($mobile_categories && !is_wp_error($mobile_categories)): ?>
                    <?php foreach ($mobile_categories as $category): ?>
                    <label class="mobile-filter-option">
                        <input type="checkbox" name="mobile_category[]" value="<?php echo esc_attr($category->slug); ?>">
                        <span class="checkbox-mark"></span>
                        <span class="option-text"><?php echo esc_html($category->name); ?></span>
                        <span class="option-count"><?php echo $category->count; ?></span>
                    </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- 都道府県フィルター -->
        <div class="mobile-filter-section">
            <button type="button" class="mobile-filter-accordion" aria-expanded="false">
                <span class="mobile-filter-label">都道府県</span>
                <span class="mobile-filter-badge" id="mobile-prefecture-badge" style="display: none;">0</span>
                <svg class="accordion-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </button>
            <div class="mobile-filter-options" style="display: none;">
                <?php if ($mobile_prefectures && !is_wp_error($mobile_prefectures)): ?>
                    <?php foreach ($mobile_prefectures as $pref): ?>
                    <label class="mobile-filter-option">
                        <input type="checkbox" name="mobile_prefecture[]" value="<?php echo esc_attr($pref->slug); ?>">
                        <span class="checkbox-mark"></span>
                        <span class="option-text"><?php echo esc_html($pref->name); ?></span>
                        <span class="option-count"><?php echo $pref->count; ?></span>
                    </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- 地域フィルター -->
        <div class="mobile-filter-section">
            <button type="button" class="mobile-filter-accordion" aria-expanded="false">
                <span class="mobile-filter-label">地域</span>
                <span class="mobile-filter-badge" id="mobile-region-badge" style="display: none;">0</span>
                <svg class="accordion-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </button>
            <div class="mobile-filter-options" style="display: none;">
                <?php foreach ($mobile_region_groups as $region_slug => $region_name): ?>
                <label class="mobile-filter-option">
                    <input type="checkbox" name="mobile_region[]" value="<?php echo esc_attr($region_slug); ?>">
                    <span class="checkbox-mark"></span>
                    <span class="option-text"><?php echo esc_html($region_name); ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- 助成金額フィルター -->
        <div class="mobile-filter-section">
            <button type="button" class="mobile-filter-accordion" aria-expanded="false">
                <span class="mobile-filter-label">助成金額</span>
                <span class="mobile-filter-badge" id="mobile-amount-badge" style="display: none;">0</span>
                <svg class="accordion-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </button>
            <div class="mobile-filter-options" style="display: none;">
                <label class="mobile-filter-option">
                    <input type="checkbox" name="mobile_amount[]" value="0-100">
                    <span class="checkbox-mark"></span>
                    <span class="option-text">〜100万円</span>
                </label>
                <label class="mobile-filter-option">
                    <input type="checkbox" name="mobile_amount[]" value="100-500">
                    <span class="checkbox-mark"></span>
                    <span class="option-text">100万円〜500万円</span>
                </label>
                <label class="mobile-filter-option">
                    <input type="checkbox" name="mobile_amount[]" value="500-1000">
                    <span class="checkbox-mark"></span>
                    <span class="option-text">500万円〜1000万円</span>
                </label>
                <label class="mobile-filter-option">
                    <input type="checkbox" name="mobile_amount[]" value="1000-3000">
                    <span class="checkbox-mark"></span>
                    <span class="option-text">1000万円〜3000万円</span>
                </label>
                <label class="mobile-filter-option">
                    <input type="checkbox" name="mobile_amount[]" value="3000+">
                    <span class="checkbox-mark"></span>
                    <span class="option-text">3000万円以上</span>
                </label>
            </div>
        </div>
        
        <!-- 募集状況フィルター -->
        <div class="mobile-filter-section">
            <button type="button" class="mobile-filter-accordion" aria-expanded="false">
                <span class="mobile-filter-label">募集状況</span>
                <span class="mobile-filter-badge" id="mobile-status-badge" style="display: none;">0</span>
                <svg class="accordion-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </button>
            <div class="mobile-filter-options" style="display: none;">
                <label class="mobile-filter-option">
                    <input type="checkbox" name="mobile_status[]" value="active">
                    <span class="checkbox-mark"></span>
                    <span class="option-text">募集中</span>
                </label>
                <label class="mobile-filter-option">
                    <input type="checkbox" name="mobile_status[]" value="upcoming">
                    <span class="checkbox-mark"></span>
                    <span class="option-text">募集予定</span>
                </label>
                <label class="mobile-filter-option">
                    <input type="checkbox" name="mobile_status[]" value="closed">
                    <span class="checkbox-mark"></span>
                    <span class="option-text">募集終了</span>
                </label>
            </div>
        </div>
        
    </div>
    
    <!-- パネルフッター（固定ボタン） -->
    <div class="mobile-filter-footer">
        <button type="button" id="mobile-filter-reset" class="mobile-filter-btn mobile-filter-btn-secondary">
            リセット
        </button>
        <button type="button" id="mobile-filter-apply" class="mobile-filter-btn mobile-filter-btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            この条件で検索
        </button>
    </div>
    
</div>
