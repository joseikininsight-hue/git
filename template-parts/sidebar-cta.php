<?php
/**
 * Template Part: Sidebar CTA - Vertical Tab Style
 * サイドバーCTA - 縦タブスタイル（閉じ機能付き）
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 * 
 * === 特徴 ===
 * - 右サイドに縦タブ形式で配置
 * - 閉じる/開くトグル機能
 * - AdSenseとの競合なし
 * - 記事閲覧の邪魔にならない控えめなデザイン
 */

if (!defined('ABSPATH')) exit;

// ページ設定で非表示にする場合の判定
if (get_query_var('hide_sticky_cta')) return;
?>

<div id="sidebar-cta" class="sidebar-cta" data-state="open">
    <!-- 閉じた状態で表示するトグルボタン -->
    <button class="sidebar-cta-toggle" aria-label="メニューを開く" title="メニューを開く">
        <svg class="toggle-icon icon-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        <svg class="toggle-icon icon-close" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
    </button>

    <!-- メインコンテンツ -->
    <div class="sidebar-cta-content">
        <!-- 診断ボタン -->
        <a href="<?php echo esc_url(home_url('/subsidy-diagnosis/')); ?>" 
           class="sidebar-cta-btn btn-diagnosis"
           aria-label="無料診断">
            <span class="btn-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </span>
            <span class="btn-label">無料診断</span>
        </a>

        <!-- 検索ボタン -->
        <a href="<?php echo esc_url(home_url('/grants/')); ?>" 
           class="sidebar-cta-btn btn-search"
           aria-label="補助金を探す">
            <span class="btn-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </span>
            <span class="btn-label">補助金検索</span>
        </a>

        <!-- 閉じるボタン（コンテンツ内） -->
        <button class="sidebar-cta-close" aria-label="閉じる" title="閉じる">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
</div>

<style>
/* ============================================
   Sidebar CTA - Vertical Tab Style v1.0
   サイドバーCTA - 縦タブスタイル
   ============================================ */

:root {
    --scta-z-index: 9990;
    --scta-navy: #0D2A52;
    --scta-navy-light: #1A3D6E;
    --scta-gold: #C5A059;
    --scta-gold-light: #D4B77A;
    --scta-white: #ffffff;
    --scta-gray-100: #F8FAFC;
    --scta-gray-200: #E2E8F0;
    --scta-gray-600: #64748B;
    --scta-shadow: 0 4px 20px rgba(13, 42, 82, 0.15);
    --scta-shadow-hover: 0 8px 30px rgba(13, 42, 82, 0.25);
    --scta-radius: 12px;
    --scta-trans: cubic-bezier(0.4, 0, 0.2, 1);
    --scta-font: 'Noto Sans JP', sans-serif;
}

/* ============================================
   Main Container
   ============================================ */
.sidebar-cta {
    position: fixed;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    z-index: var(--scta-z-index);
    display: flex;
    align-items: center;
    font-family: var(--scta-font);
}

/* ============================================
   Toggle Button (閉じた状態で表示)
   ============================================ */
.sidebar-cta-toggle {
    position: absolute;
    right: 0;
    width: 32px;
    height: 80px;
    background: linear-gradient(180deg, var(--scta-navy) 0%, var(--scta-navy-light) 100%);
    border: none;
    border-radius: var(--scta-radius) 0 0 var(--scta-radius);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--scta-gold);
    box-shadow: var(--scta-shadow);
    transition: all 0.3s var(--scta-trans);
    opacity: 0;
    pointer-events: none;
}

.sidebar-cta[data-state="closed"] .sidebar-cta-toggle {
    opacity: 1;
    pointer-events: auto;
}

.sidebar-cta-toggle:hover {
    width: 40px;
    background: linear-gradient(180deg, var(--scta-navy-light) 0%, var(--scta-navy) 100%);
}

.sidebar-cta-toggle .icon-open {
    display: block;
}

.sidebar-cta-toggle .icon-close {
    display: none;
}

.sidebar-cta[data-state="open"] .sidebar-cta-toggle .icon-open {
    display: none;
}

.sidebar-cta[data-state="open"] .sidebar-cta-toggle .icon-close {
    display: block;
}

/* ============================================
   Content Container
   ============================================ */
.sidebar-cta-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
    background: var(--scta-white);
    border-radius: var(--scta-radius) 0 0 var(--scta-radius);
    box-shadow: var(--scta-shadow);
    overflow: hidden;
    transition: all 0.35s var(--scta-trans);
    transform: translateX(0);
    border-left: 3px solid var(--scta-gold);
}

.sidebar-cta[data-state="closed"] .sidebar-cta-content {
    transform: translateX(100%);
    opacity: 0;
    pointer-events: none;
}

/* ============================================
   CTA Buttons
   ============================================ */
.sidebar-cta-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 16px 12px;
    text-decoration: none;
    border: none;
    background: var(--scta-white);
    cursor: pointer;
    transition: all 0.25s var(--scta-trans);
    position: relative;
    min-width: 72px;
}

.sidebar-cta-btn:hover {
    background: var(--scta-gray-100);
}

.sidebar-cta-btn:active {
    transform: scale(0.98);
}

/* Icon */
.sidebar-cta-btn .btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    transition: all 0.25s var(--scta-trans);
}

/* Label - 縦書き風 */
.sidebar-cta-btn .btn-label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.05em;
    writing-mode: vertical-rl;
    text-orientation: upright;
    line-height: 1;
    transition: color 0.25s var(--scta-trans);
}

/* Badge */
.sidebar-cta-btn .btn-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    font-size: 8px;
    font-weight: 700;
    color: var(--scta-white);
    background: #EF4444;
    padding: 2px 4px;
    border-radius: 4px;
    letter-spacing: 0.02em;
    animation: badge-pulse 2s infinite;
}

@keyframes badge-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* ============================================
   Button Variants
   ============================================ */

/* 診断ボタン */
.btn-diagnosis .btn-icon {
    background: rgba(197, 160, 89, 0.1);
    color: var(--scta-gold);
}

.btn-diagnosis .btn-label {
    color: var(--scta-navy);
}

.btn-diagnosis:hover .btn-icon {
    background: var(--scta-gold);
    color: var(--scta-white);
}

/* 検索ボタン */
.btn-search .btn-icon {
    background: rgba(13, 42, 82, 0.1);
    color: var(--scta-navy);
}

.btn-search .btn-label {
    color: var(--scta-navy);
}

.btn-search:hover .btn-icon {
    background: var(--scta-navy);
    color: var(--scta-white);
}

/* ============================================
   Close Button
   ============================================ */
.sidebar-cta-close {
    position: absolute;
    top: 4px;
    left: 4px;
    width: 24px;
    height: 24px;
    background: transparent;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--scta-gray-600);
    opacity: 0.5;
    transition: all 0.2s var(--scta-trans);
    border-radius: 4px;
}

.sidebar-cta-close:hover {
    opacity: 1;
    background: var(--scta-gray-100);
    color: var(--scta-navy);
}

/* ============================================
   Mobile Responsive
   ============================================ */
@media (max-width: 767px) {
    .sidebar-cta {
        top: auto;
        bottom: 100px;
        transform: none;
    }
    
    .sidebar-cta-content {
        border-radius: 10px 0 0 10px;
    }
    
    .sidebar-cta-btn {
        padding: 12px 10px;
        min-width: 60px;
    }
    
    .sidebar-cta-btn .btn-icon {
        width: 36px;
        height: 36px;
    }
    
    .sidebar-cta-btn .btn-icon svg {
        width: 18px;
        height: 18px;
    }
    
    .sidebar-cta-btn .btn-label {
        font-size: 10px;
    }
    
    .sidebar-cta-toggle {
        width: 28px;
        height: 70px;
    }
    
    .sidebar-cta-close {
        width: 20px;
        height: 20px;
    }
    
    .sidebar-cta-close svg {
        width: 14px;
        height: 14px;
    }
}

/* 超小型画面 */
@media (max-width: 375px) {
    .sidebar-cta {
        bottom: 80px;
    }
    
    .sidebar-cta-btn {
        padding: 10px 8px;
        min-width: 52px;
    }
    
    .sidebar-cta-btn .btn-icon {
        width: 32px;
        height: 32px;
    }
    
    .sidebar-cta-btn .btn-label {
        font-size: 9px;
    }
}

/* ============================================
   Animation
   ============================================ */
@keyframes sidebar-slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.sidebar-cta[data-state="open"] .sidebar-cta-content {
    animation: sidebar-slide-in 0.4s var(--scta-trans);
}

/* ============================================
   Print: 印刷時は非表示
   ============================================ */
@media print {
    .sidebar-cta {
        display: none !important;
    }
}

/* ============================================
   Reduced Motion: アニメーション軽減
   ============================================ */
@media (prefers-reduced-motion: reduce) {
    .sidebar-cta,
    .sidebar-cta-content,
    .sidebar-cta-btn,
    .sidebar-cta-toggle {
        transition: none;
        animation: none;
    }
}
</style>

<script>
(function() {
    'use strict';

    const sidebar = document.getElementById('sidebar-cta');
    if (!sidebar) return;

    const content = sidebar.querySelector('.sidebar-cta-content');
    const toggleBtn = sidebar.querySelector('.sidebar-cta-toggle');
    const closeBtn = sidebar.querySelector('.sidebar-cta-close');

    // LocalStorage キー
    const STORAGE_KEY = 'sidebar_cta_state';

    // 状態を復元
    function restoreState() {
        const savedState = localStorage.getItem(STORAGE_KEY);
        if (savedState === 'closed') {
            sidebar.dataset.state = 'closed';
        }
    }

    // 状態を保存
    function saveState(state) {
        localStorage.setItem(STORAGE_KEY, state);
    }

    // 開く
    function openSidebar() {
        sidebar.dataset.state = 'open';
        saveState('open');
        toggleBtn.setAttribute('aria-label', 'メニューを閉じる');
        toggleBtn.setAttribute('title', 'メニューを閉じる');
    }

    // 閉じる
    function closeSidebar() {
        sidebar.dataset.state = 'closed';
        saveState('closed');
        toggleBtn.setAttribute('aria-label', 'メニューを開く');
        toggleBtn.setAttribute('title', 'メニューを開く');
    }

    // トグル
    function toggleSidebar() {
        if (sidebar.dataset.state === 'open') {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    // イベントリスナー
    toggleBtn.addEventListener('click', toggleSidebar);
    closeBtn.addEventListener('click', closeSidebar);

    // ボタンのクリックイベント（GAトラッキング）
    sidebar.querySelectorAll('.sidebar-cta-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const label = this.querySelector('.btn-label')?.innerText || '';
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    'event_category': 'Sidebar CTA',
                    'event_label': label
                });
            }
        });
    });

    // 初期化時に状態復元
    restoreState();

    // 初回訪問から3秒後に自動で開く（閉じていた場合）
    const hasVisited = localStorage.getItem('sidebar_cta_visited');
    if (!hasVisited) {
        localStorage.setItem('sidebar_cta_visited', 'true');
        // 初回は開いた状態で表示
        setTimeout(() => {
            if (sidebar.dataset.state === 'closed') {
                openSidebar();
            }
        }, 3000);
    }

    // スクロール時の表示制御（オプション - 下部に近づいたら自動で開く）
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            const scrollY = window.scrollY;
            const docHeight = document.documentElement.scrollHeight;
            const winHeight = window.innerHeight;
            const scrollPercent = scrollY / (docHeight - winHeight);
            
            // 60%以上スクロールしたら開く（閉じられていた場合のみ）
            // if (scrollPercent > 0.6 && sidebar.dataset.state === 'closed') {
            //     openSidebar();
            // }
        }, 100);
    }, { passive: true });

})();
</script>
