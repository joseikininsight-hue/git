<?php
/**
 * Mobile Search Modal - Government Official Design v54.0
 * モバイル検索モーダル - 官公庁風デザイン統一版
 * 
 * @package Grant_Insight_Government
 * @version 54.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// カテゴリーを取得
$mobile_categories = get_terms(array(
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 50
));

// 都道府県を取得
$mobile_prefectures = gi_get_all_prefectures();

// 助成金額の範囲
$amount_ranges = array(
    '0-100' => '〜100万円',
    '100-500' => '100万円〜500万円',
    '500-1000' => '500万円〜1000万円',
    '1000-3000' => '1000万円〜3000万円',
    '3000+' => '3000万円以上'
);
?>

<!-- モバイル用検索オーバーレイ -->
<div class="gov-mobile-overlay" 
     id="mobileSearchOverlay" 
     aria-hidden="true"
     role="presentation"></div>

<!-- モバイル用検索モーダル -->
<div class="gov-mobile-modal" 
     id="mobileSearchModal" 
     role="dialog" 
     aria-labelledby="mobile-search-title" 
     aria-modal="true"
     aria-hidden="true">
    
    <!-- ヘッダー -->
    <header class="gov-mobile-header">
        <div class="gov-mobile-header-content">
            <svg class="gov-mobile-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            <h2 class="gov-mobile-title" id="mobile-search-title">
                助成金を探す
            </h2>
        </div>
        <button class="gov-mobile-close" 
                id="mobileSearchClose" 
                type="button"
                aria-label="検索モーダルを閉じる">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </header>
    
    <!-- コンテンツ -->
    <div class="gov-mobile-content">
        <form class="gov-mobile-form" 
              id="mobile-search-form" 
              action="<?php echo esc_url(home_url('/grants/')); ?>"
              method="get"
              role="search"
              aria-label="モバイル補助金検索フォーム"
              novalidate>
            
            <!-- フリーワード検索 -->
            <div class="gov-form-group">
                <label class="gov-form-label" for="mobile-keyword-input">
                    <svg class="gov-label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <span>キーワード検索</span>
                </label>
                <div class="gov-input-wrapper">
                    <input type="search" 
                           id="mobile-keyword-input" 
                           name="search"
                           class="gov-form-input" 
                           placeholder="例：IT導入、設備投資、創業支援"
                           aria-label="フリーワード検索"
                           autocomplete="off">
                    <button class="gov-input-clear" 
                            id="mobile-keyword-clear" 
                            type="button"
                            style="display: none;"
                            aria-label="キーワードをクリア">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- カテゴリー -->
            <div class="gov-form-group">
                <label class="gov-form-label" for="mobile-category-select">
                    <svg class="gov-label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span>用途から探す</span>
                </label>
                <div class="gov-select-wrapper">
                    <select id="mobile-category-select" 
                            name="category" 
                            class="gov-form-select"
                            aria-label="補助金の用途を選択">
                        <option value="">カテゴリーを選択</option>
                        <?php if (!empty($mobile_categories) && !is_wp_error($mobile_categories)): ?>
                            <?php foreach ($mobile_categories as $cat): ?>
                                <option value="<?php echo esc_attr($cat->slug); ?>">
                                    <?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <svg class="gov-select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </div>
            </div>

            <!-- 都道府県 -->
            <div class="gov-form-group">
                <label class="gov-form-label" for="mobile-prefecture-select">
                    <svg class="gov-label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span>都道府県から探す</span>
                </label>
                <div class="gov-select-wrapper">
                    <select id="mobile-prefecture-select" 
                            name="prefecture" 
                            class="gov-form-select"
                            aria-label="都道府県を選択">
                        <option value="">都道府県を選択</option>
                        <?php if (!empty($mobile_prefectures)): ?>
                            <?php foreach ($mobile_prefectures as $pref): ?>
                                <option value="<?php echo esc_attr($pref['slug']); ?>"
                                        data-region="<?php echo esc_attr($pref['region']); ?>">
                                    <?php echo esc_html($pref['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <svg class="gov-select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </div>
            </div>

            <!-- 市町村（都道府県選択後に表示） -->
            <div class="gov-form-group" id="mobile-municipality-group" style="display: none;">
                <label class="gov-form-label" for="mobile-municipality-select">
                    <svg class="gov-label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span>市町村</span>
                    <span class="gov-form-optional">（任意）</span>
                </label>
                <div class="gov-select-wrapper">
                    <select id="mobile-municipality-select" 
                            name="municipality" 
                            class="gov-form-select"
                            aria-label="市町村を選択">
                        <option value="">市町村を選択</option>
                    </select>
                    <svg class="gov-select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </div>
                <div class="gov-form-loading" id="mobile-municipality-loading" style="display: none;">
                    <div class="gov-spinner"></div>
                    <span>読み込み中...</span>
                </div>
            </div>

            <!-- 助成金額 -->
            <div class="gov-form-group">
                <label class="gov-form-label" for="mobile-amount-select">
                    <svg class="gov-label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                    <span>助成金額</span>
                    <span class="gov-form-optional">（任意）</span>
                </label>
                <div class="gov-select-wrapper">
                    <select id="mobile-amount-select" 
                            name="amount" 
                            class="gov-form-select"
                            aria-label="助成金額の範囲を選択">
                        <option value="">指定なし</option>
                        <?php foreach ($amount_ranges as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>">
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <svg class="gov-select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </div>
            </div>

            <!-- 募集状況 -->
            <div class="gov-form-group">
                <label class="gov-form-label" for="mobile-status-select">
                    <svg class="gov-label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span>募集状況</span>
                    <span class="gov-form-optional">（任意）</span>
                </label>
                <div class="gov-select-wrapper">
                    <select id="mobile-status-select" 
                            name="status" 
                            class="gov-form-select"
                            aria-label="募集状況を選択">
                        <option value="">すべて</option>
                        <option value="active">募集中</option>
                        <option value="upcoming">募集予定</option>
                        <option value="closed">募集終了</option>
                    </select>
                    <svg class="gov-select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </div>
            </div>

            <!-- ボタングループ -->
            <div class="gov-button-group">
                <button type="button" 
                        class="gov-btn gov-btn-reset" 
                        id="mobile-reset-btn"
                        aria-label="検索条件をクリア">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <polyline points="1 4 1 10 7 10"/>
                        <polyline points="23 20 23 14 17 14"/>
                        <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                    </svg>
                    <span>クリア</span>
                </button>
                <button type="submit" 
                        class="gov-btn gov-btn-search" 
                        id="mobile-search-btn"
                        aria-label="助成金を検索">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <span>検索する</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* ============================================
   🏛️ Mobile Search Modal - Government Official v54.0
   官公庁風デザイン統一版
============================================ */

:root {
    /* 官公庁カラーパレット */
    --gov-navy-900: #0d1b2a;
    --gov-navy-800: #1b263b;
    --gov-navy-700: #2c3e50;
    --gov-navy-600: #34495e;
    --gov-navy-500: #415a77;
    --gov-navy-400: #778da9;
    --gov-navy-300: #a3b1c6;
    --gov-navy-200: #cfd8e3;
    --gov-navy-100: #e8ecf1;
    --gov-navy-50: #f4f6f8;
    
    /* アクセントカラー - 金 */
    --gov-gold: #c9a227;
    --gov-gold-light: #d4b77a;
    --gov-gold-pale: #f0e6c8;
    
    /* セマンティックカラー */
    --gov-green: #2e7d32;
    --gov-green-light: #e8f5e9;
    
    /* ニュートラル */
    --gov-white: #ffffff;
    --gov-black: #1a1a1a;
    --gov-gray-900: #212529;
    --gov-gray-800: #343a40;
    --gov-gray-700: #495057;
    --gov-gray-600: #6c757d;
    --gov-gray-500: #adb5bd;
    --gov-gray-400: #ced4da;
    --gov-gray-300: #dee2e6;
    --gov-gray-200: #e9ecef;
    --gov-gray-100: #f8f9fa;
    
    /* タイポグラフィ */
    --gov-font-serif: "Shippori Mincho", "Yu Mincho", "YuMincho", "Hiragino Mincho ProN", serif;
    --gov-font-sans: 'Noto Sans JP', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    --gov-font-mono: 'SF Mono', 'Monaco', 'Cascadia Code', monospace;
    
    /* Effects */
    --gov-transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    --gov-transition-slow: 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    --gov-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
    --gov-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    --gov-shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
    --gov-radius: 4px;
    --gov-radius-lg: 8px;
}

/* ===== Overlay ===== */
.gov-mobile-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(13, 27, 42, 0.8);
    z-index: 9998;
    opacity: 0;
    transition: opacity var(--gov-transition);
}

.gov-mobile-overlay.active {
    display: block;
    opacity: 1;
}

/* ===== Modal ===== */
.gov-mobile-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--gov-white);
    z-index: 9999;
    transform: translateY(100%);
    transition: transform var(--gov-transition-slow);
    overflow: hidden;
    flex-direction: column;
    font-family: var(--gov-font-sans);
}

.gov-mobile-modal.active {
    display: flex;
    transform: translateY(0);
}

/* ===== Header ===== */
.gov-mobile-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-900) 100%);
    border-bottom: 3px solid var(--gov-gold);
    flex-shrink: 0;
    box-shadow: var(--gov-shadow);
}

.gov-mobile-header-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.gov-mobile-icon {
    color: var(--gov-gold);
    flex-shrink: 0;
}

.gov-mobile-title {
    font-family: var(--gov-font-serif);
    font-size: 18px;
    font-weight: 700;
    margin: 0;
    color: var(--gov-white);
    letter-spacing: 0.02em;
}

.gov-mobile-close {
    width: 40px;
    height: 40px;
    background: transparent;
    border: 2px solid var(--gov-white);
    border-radius: var(--gov-radius);
    color: var(--gov-white);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--gov-transition);
    padding: 0;
}

.gov-mobile-close:active {
    background: var(--gov-white);
    color: var(--gov-navy-900);
    transform: scale(0.95);
}

/* ===== Content ===== */
.gov-mobile-content {
    flex: 1;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 24px 20px 100px;
    background: linear-gradient(180deg, var(--gov-navy-50) 0%, var(--gov-white) 100%);
}

/* ===== Form ===== */
.gov-mobile-form {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.gov-form-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.gov-form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: var(--gov-navy-900);
}

.gov-label-icon {
    color: var(--gov-navy-600);
    flex-shrink: 0;
}

.gov-form-optional {
    font-size: 11px;
    font-weight: 400;
    color: var(--gov-gray-600);
    margin-left: auto;
}

/* Input Wrapper */
.gov-input-wrapper {
    position: relative;
}

.gov-form-input {
    width: 100%;
    padding: 14px 16px;
    padding-right: 48px;
    font-family: var(--gov-font-sans);
    font-size: 15px;
    font-weight: 500;
    color: var(--gov-gray-900);
    background: var(--gov-white);
    border: 2px solid var(--gov-gray-300);
    border-radius: var(--gov-radius);
    transition: all var(--gov-transition);
    box-sizing: border-box;
}

.gov-form-input:focus {
    outline: none;
    border-color: var(--gov-navy-700);
    box-shadow: 0 0 0 3px rgba(27, 38, 59, 0.1);
}

.gov-form-input::placeholder {
    color: var(--gov-gray-500);
    font-weight: 400;
}

.gov-input-clear {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 28px;
    height: 28px;
    background: var(--gov-gray-200);
    border: none;
    border-radius: 50%;
    color: var(--gov-gray-600);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    transition: all var(--gov-transition);
}

.gov-input-clear:active {
    background: var(--gov-gray-300);
    transform: translateY(-50%) scale(0.95);
}

/* Select Wrapper */
.gov-select-wrapper {
    position: relative;
}

.gov-form-select {
    width: 100%;
    padding: 14px 40px 14px 16px;
    font-family: var(--gov-font-sans);
    font-size: 15px;
    font-weight: 500;
    color: var(--gov-gray-900);
    background: var(--gov-white);
    border: 2px solid var(--gov-gray-300);
    border-radius: var(--gov-radius);
    appearance: none;
    cursor: pointer;
    transition: all var(--gov-transition);
    box-sizing: border-box;
}

.gov-form-select:focus {
    outline: none;
    border-color: var(--gov-navy-700);
    box-shadow: 0 0 0 3px rgba(27, 38, 59, 0.1);
}

.gov-select-arrow {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gov-navy-600);
    pointer-events: none;
}

/* Loading */
.gov-form-loading {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: var(--gov-navy-50);
    border: 1px solid var(--gov-navy-200);
    border-radius: var(--gov-radius);
    font-size: 13px;
    color: var(--gov-navy-700);
}

.gov-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid var(--gov-navy-200);
    border-top-color: var(--gov-navy-700);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ===== Button Group ===== */
.gov-button-group {
    display: flex;
    gap: 12px;
    margin-top: 8px;
    padding-top: 24px;
    border-top: 2px solid var(--gov-gray-200);
}

.gov-btn {
    flex: 1;
    padding: 16px 20px;
    font-family: var(--gov-font-sans);
    font-size: 16px;
    font-weight: 700;
    border: 2px solid;
    border-radius: var(--gov-radius);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all var(--gov-transition);
    box-sizing: border-box;
}

.gov-btn:active {
    transform: scale(0.98);
}

.gov-btn-reset {
    background: var(--gov-white);
    border-color: var(--gov-gray-400);
    color: var(--gov-gray-700);
}

.gov-btn-reset:active {
    background: var(--gov-gray-100);
    border-color: var(--gov-gray-500);
}

.gov-btn-search {
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-900) 100%);
    border-color: var(--gov-navy-800);
    color: var(--gov-white);
    box-shadow: var(--gov-shadow);
}

.gov-btn-search:active {
    background: linear-gradient(135deg, var(--gov-navy-900) 0%, var(--gov-navy-800) 100%);
    box-shadow: var(--gov-shadow-sm);
}

/* ===== Accessibility ===== */
.gov-mobile-modal:focus {
    outline: none;
}

.gov-form-input:focus-visible,
.gov-form-select:focus-visible,
.gov-btn:focus-visible,
.gov-mobile-close:focus-visible {
    outline: 3px solid var(--gov-gold);
    outline-offset: 2px;
}

/* ===== Animation ===== */
@media (prefers-reduced-motion: reduce) {
    .gov-mobile-overlay,
    .gov-mobile-modal,
    .gov-form-input,
    .gov-form-select,
    .gov-btn,
    .gov-mobile-close {
        transition: none !important;
        animation: none !important;
    }
}

/* ===== Small Screens ===== */
@media (max-width: 360px) {
    .gov-mobile-header {
        padding: 14px 16px;
    }
    
    .gov-mobile-title {
        font-size: 16px;
    }
    
    .gov-mobile-content {
        padding: 20px 16px 100px;
    }
    
    .gov-mobile-form {
        gap: 20px;
    }
    
    .gov-btn {
        font-size: 15px;
        padding: 14px 16px;
    }
}

/* ===== Landscape ===== */
@media (max-height: 500px) {
    .gov-mobile-header {
        padding: 12px 16px;
    }
    
    .gov-mobile-content {
        padding: 16px 20px 80px;
    }
    
    .gov-mobile-form {
        gap: 16px;
    }
}
</style>

<script>
(function() {
    'use strict';
    
    const AJAX_URL = '<?php echo admin_url("admin-ajax.php"); ?>';
    
    // モバイル検索モーダルの初期化
    function initMobileSearchModal() {
        const searchModal = document.getElementById('mobileSearchModal');
        const searchOverlay = document.getElementById('mobileSearchOverlay');
        const searchClose = document.getElementById('mobileSearchClose');
        const prefectureSelect = document.getElementById('mobile-prefecture-select');
        const municipalityGroup = document.getElementById('mobile-municipality-group');
        const municipalitySelect = document.getElementById('mobile-municipality-select');
        const municipalityLoading = document.getElementById('mobile-municipality-loading');
        const resetBtn = document.getElementById('mobile-reset-btn');
        const searchForm = document.getElementById('mobile-search-form');
        const keywordInput = document.getElementById('mobile-keyword-input');
        const keywordClear = document.getElementById('mobile-keyword-clear');
        
        if (!searchModal || !searchOverlay || !searchClose) {
            console.warn('❌ Mobile search modal elements not found');
            return;
        }
        
        console.log('✅ Mobile search modal initialized');
        
        // モーダルを開く関数をグローバルに公開
        window.openMobileSearchModal = function() {
            searchModal.classList.add('active');
            searchOverlay.classList.add('active');
            searchModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            
            // フォーカスをモーダル内に移動
            if (keywordInput) {
                setTimeout(() => keywordInput.focus(), 100);
            }
        };
        
        // モーダルを閉じる
        function closeModal() {
            searchModal.classList.remove('active');
            searchOverlay.classList.remove('active');
            searchModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
        
        // 閉じるボタンクリック
        if (searchClose) {
            searchClose.addEventListener('click', closeModal);
        }
        
        // オーバーレイクリック
        if (searchOverlay) {
            searchOverlay.addEventListener('click', closeModal);
        }
        
        // ESCキーで閉じる
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchModal.classList.contains('active')) {
                closeModal();
            }
        });
        
        // キーワード入力時のクリアボタン表示
        if (keywordInput && keywordClear) {
            keywordInput.addEventListener('input', function() {
                if (this.value.trim().length > 0) {
                    keywordClear.style.display = 'flex';
                } else {
                    keywordClear.style.display = 'none';
                }
            });
            
            keywordClear.addEventListener('click', function() {
                keywordInput.value = '';
                keywordClear.style.display = 'none';
                keywordInput.focus();
            });
        }
        
        // 都道府県選択時の処理
        if (prefectureSelect && municipalityGroup && municipalitySelect) {
            prefectureSelect.addEventListener('change', function() {
                const selectedPrefecture = this.value;
                
                if (selectedPrefecture) {
                    municipalityGroup.style.display = 'flex';
                    loadMunicipalitiesMobile(selectedPrefecture);
                } else {
                    municipalityGroup.style.display = 'none';
                    municipalitySelect.innerHTML = '<option value="">市町村を選択</option>';
                }
            });
        }
        
        // リセットボタンの処理
        if (resetBtn && searchForm) {
            resetBtn.addEventListener('click', function() {
                searchForm.reset();
                if (municipalityGroup) {
                    municipalityGroup.style.display = 'none';
                }
                if (municipalitySelect) {
                    municipalitySelect.innerHTML = '<option value="">市町村を選択</option>';
                }
                if (keywordClear) {
                    keywordClear.style.display = 'none';
                }
            });
        }
        
        // フォーム送信時のバリデーション
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                // 何も入力されていない場合は警告
                const keyword = keywordInput ? keywordInput.value.trim() : '';
                const category = document.getElementById('mobile-category-select')?.value || '';
                const prefecture = prefectureSelect?.value || '';
                
                if (!keyword && !category && !prefecture) {
                    e.preventDefault();
                    alert('検索条件を1つ以上入力してください。');
                    return false;
                }
            });
        }
    }
    
    // 市町村を読み込む（AJAX）
    function loadMunicipalitiesMobile(prefectureSlug) {
        const municipalitySelect = document.getElementById('mobile-municipality-select');
        const municipalityLoading = document.getElementById('mobile-municipality-loading');
        
        if (!municipalitySelect) {
            return;
        }
        
        console.log('🔄 Loading municipalities for:', prefectureSlug);
        
        // ローディング表示
        if (municipalityLoading) {
            municipalityLoading.style.display = 'flex';
        }
        municipalitySelect.innerHTML = '<option value="">読み込み中...</option>';
        municipalitySelect.disabled = true;
        
        // AJAX リクエスト
        const formData = new FormData();
        formData.append('action', 'gi_get_municipalities_for_prefecture');
        formData.append('prefecture_slug', prefectureSlug);
        
        fetch(AJAX_URL, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('📡 Municipality response:', data);
            
            if (municipalityLoading) {
                municipalityLoading.style.display = 'none';
            }
            municipalitySelect.disabled = false;
            
            let municipalities = [];
            
            // データ構造の確認
            if (data.success) {
                if (data.data && data.data.data && Array.isArray(data.data.data.municipalities)) {
                    municipalities = data.data.data.municipalities;
                } else if (data.data && Array.isArray(data.data.municipalities)) {
                    municipalities = data.data.municipalities;
                } else if (Array.isArray(data.municipalities)) {
                    municipalities = data.municipalities;
                } else if (Array.isArray(data.data)) {
                    municipalities = data.data;
                }
            }
            
            if (municipalities.length > 0) {
                let html = '<option value="">市町村を選択</option>';
                municipalities.forEach(function(municipality) {
                    html += '<option value="' + municipality.slug + '">' + 
                            municipality.name + '</option>';
                });
                municipalitySelect.innerHTML = html;
                console.log('✅ Loaded', municipalities.length, 'municipalities');
            } else {
                municipalitySelect.innerHTML = '<option value="">市町村が見つかりません</option>';
                console.warn('⚠️ No municipalities found');
            }
        })
        .catch(error => {
            console.error('❌ Error loading municipalities:', error);
            if (municipalityLoading) {
                municipalityLoading.style.display = 'none';
            }
            municipalitySelect.disabled = false;
            municipalitySelect.innerHTML = '<option value="">読み込みエラー</option>';
        });
    }
    
    // DOMContentLoaded後に初期化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMobileSearchModal);
    } else {
        initMobileSearchModal();
    }
    
    console.log('✅ Mobile search modal script loaded');
})();
</script>
