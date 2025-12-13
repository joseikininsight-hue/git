<?php
/**
 * Grant Card Unified - Government Official Design v54.0
 * template-parts/grant-card-unified.php
 * 
 * 官公庁風デザイン統一版
 * - TOPページ・archive-grant.phpと完全統一
 * - 3つの表示モード対応（単体/グリッド/リスト）
 * - CLS（Cumulative Layout Shift）ゼロ
 * - SEO完全最適化
 * 
 * @package Grant_Insight_Government
 * @version 54.0.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

global $post;
$post_id = get_the_ID();
if (!$post_id) {
    return;
}

// ===== 基本データ =====
$title = get_the_title($post_id);
$permalink = get_permalink($post_id);
$excerpt = get_the_excerpt($post_id);

if (empty($excerpt)) {
    $content = get_the_content($post_id);
    $excerpt = wp_trim_words(strip_tags($content), 30, '...');
}

// ===== ACFフィールド =====
$organization = get_field('organization', $post_id) ?: '';
$organization_type = get_field('organization_type', $post_id) ?: 'national';
$deadline_date = get_field('deadline_date', $post_id) ?: '';
$application_status = get_field('application_status', $post_id) ?: 'open';
$adoption_rate = floatval(get_field('adoption_rate', $post_id));
$subsidy_rate_detailed = get_field('subsidy_rate_detailed', $post_id) ?: '';
$is_featured = get_field('is_featured', $post_id) ?: false;
$ai_summary = get_field('ai_summary', $post_id) ?: get_post_meta($post_id, 'ai_summary', true);
$max_subsidy_amount = get_field('max_subsidy_amount', $post_id) ?: '';

// ===== タクソノミー =====
$categories = get_the_terms($post_id, 'grant_category');
$prefectures = get_the_terms($post_id, 'grant_prefecture');

$main_category = '';
if ($categories && !is_wp_error($categories)) {
    $main_category = $categories[0]->name;
}

// ===== 地域表示 =====
$region_display = '全国';
if ($prefectures && !is_wp_error($prefectures)) {
    $count = count($prefectures);
    if ($count < 47) {
        if ($count === 1) {
            $region_display = $prefectures[0]->name;
        } else {
            $region_display = $count . '都道府県';
        }
    }
}

// ===== ステータス =====
$status_config = array(
    'open' => array('label' => '募集中', 'class' => 'status-open'),
    'upcoming' => array('label' => '募集予定', 'class' => 'status-upcoming'),
    'closed' => array('label' => '募集終了', 'class' => 'status-closed'),
);
$status_data = $status_config[$application_status] ?? $status_config['open'];

// ===== 締切情報 =====
$deadline_display = '';
$deadline_urgency = '';
$deadline_class = '';
$is_urgent = false;

if ($deadline_date) {
    $deadline_timestamp = strtotime($deadline_date);
    if ($deadline_timestamp && $deadline_timestamp > 0) {
        $current_time = current_time('timestamp');
        $days_remaining = ceil(($deadline_timestamp - $current_time) / (60 * 60 * 24));
        
        $deadline_display = date('Y/m/d', $deadline_timestamp) . '締切';
        
        if ($days_remaining <= 0) {
            $deadline_display = date('Y/m/d', $deadline_timestamp) . '締切済';
            $deadline_class = 'deadline-expired';
        } elseif ($days_remaining <= 3) {
            $deadline_urgency = 'あと' . $days_remaining . '日';
            $deadline_class = 'deadline-critical';
            $is_urgent = true;
        } elseif ($days_remaining <= 7) {
            $deadline_urgency = 'あと' . $days_remaining . '日';
            $deadline_class = 'deadline-warning';
            $is_urgent = true;
        } elseif ($days_remaining <= 14) {
            $deadline_urgency = 'あと' . $days_remaining . '日';
            $deadline_class = 'deadline-soon';
        } else {
            $deadline_class = 'deadline-normal';
        }
    }
}

// ===== 補助率表示 =====
$subsidy_display = '';
if ($subsidy_rate_detailed) {
    if (strpos($subsidy_rate_detailed, '2/3') !== false) {
        $subsidy_display = '2/3補助';
    } elseif (strpos($subsidy_rate_detailed, '1/2') !== false) {
        $subsidy_display = '1/2補助';
    } elseif (strpos($subsidy_rate_detailed, '3/4') !== false) {
        $subsidy_display = '3/4補助';
    } elseif (strpos($subsidy_rate_detailed, '100') !== false || strpos($subsidy_rate_detailed, '全額') !== false) {
        $subsidy_display = '全額補助';
    }
}

// ===== キャッチコピー =====
$catch_tags = array();

if ($is_featured) {
    $catch_tags[] = array('text' => '注目', 'type' => 'featured');
}

if ($is_urgent) {
    $catch_tags[] = array('text' => '締切間近', 'type' => 'urgent');
}

if ($adoption_rate >= 70) {
    $catch_tags[] = array('text' => '高採択率', 'type' => 'success');
}

// 最大2つまで
$catch_tags = array_slice($catch_tags, 0, 2);

// ===== 助成金額表示 =====
$amount_display = '';
if ($max_subsidy_amount) {
    $amount_num = intval($max_subsidy_amount);
    if ($amount_num >= 10000) {
        $amount_display = '最大' . number_format($amount_num / 10000) . '億円';
    } elseif ($amount_num >= 100) {
        $amount_display = '最大' . number_format($amount_num) . '万円';
    } else {
        $amount_display = '最大' . number_format($amount_num) . '万円';
    }
}
?>

<style>
/* ============================================
   🏛️ Government Official Grant Card v54.0
   官公庁風デザイン統一版
============================================ */

.grant-card-gov {
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
    --gov-red: #c62828;
    --gov-red-light: #ffebee;
    --gov-orange: #e65100;
    --gov-orange-light: #fff3e0;
    
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
    --gov-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
    --gov-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    --gov-shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
    --gov-radius: 4px;
    --gov-radius-lg: 8px;
    
    color-scheme: light !important;
}

@media (prefers-color-scheme: dark) {
    .grant-card-gov,
    .grant-card-gov * {
        color-scheme: light !important;
    }
}

/* ===== Base Styles ===== */
.grant-card-gov {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    transition: all var(--gov-transition);
    cursor: pointer;
    display: block;
    position: relative;
    min-height: 140px;
    overflow: hidden;
    font-family: var(--gov-font-sans);
    color: var(--gov-gray-900);
    will-change: box-shadow, border-color;
    transform: translateZ(0);
    backface-visibility: hidden;
}

.grant-card-gov:hover {
    box-shadow: var(--gov-shadow-lg);
    border-color: var(--gov-navy-400);
    transform: translateY(-2px);
}

.grant-card-gov:active {
    transform: translateY(0);
}

.card-link-gov {
    text-decoration: none;
    color: inherit;
    display: block;
    padding: 16px;
}

/* ===== Header ===== */
.card-header-gov {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    min-height: 24px;
}

.header-left-gov {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

/* Status Badge */
.status-badge-gov {
    padding: 4px 10px;
    border-radius: var(--gov-radius);
    font-size: 11px;
    font-weight: 700;
    line-height: 1;
    height: 22px;
    display: inline-flex;
    align-items: center;
}

.status-open {
    background: var(--gov-green-light);
    color: var(--gov-green);
}

.status-upcoming {
    background: var(--gov-navy-100);
    color: var(--gov-navy-700);
}

.status-closed {
    background: var(--gov-gray-100);
    color: var(--gov-gray-600);
}

/* Catch Tags */
.catch-tags-gov {
    display: flex;
    gap: 4px;
}

.catch-tag-gov {
    padding: 3px 8px;
    border-radius: var(--gov-radius);
    font-size: 10px;
    font-weight: 700;
    line-height: 1;
    height: 18px;
    display: inline-flex;
    align-items: center;
}

.catch-featured {
    background: linear-gradient(135deg, var(--gov-gold) 0%, var(--gov-gold-light) 100%);
    color: var(--gov-navy-900);
}

.catch-urgent {
    background: var(--gov-red);
    color: var(--gov-white);
    animation: pulse 1.5s ease infinite 0.2s;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.85; }
}

.catch-success {
    background: var(--gov-green);
    color: var(--gov-white);
}

/* Category Tag */
.category-tag-gov {
    font-size: 11px;
    font-weight: 700;
    color: var(--gov-navy-600);
    background: var(--gov-navy-50);
    padding: 4px 10px;
    border-radius: var(--gov-radius);
    line-height: 1;
    height: 22px;
    display: inline-flex;
    align-items: center;
}

/* ===== Title ===== */
.card-title-gov {
    font-family: var(--gov-font-serif);
    font-size: 16px;
    font-weight: 700;
    line-height: 1.4;
    color: var(--gov-navy-900);
    margin: 0 0 8px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 45px;
    max-height: 45px;
    transition: color var(--gov-transition);
}

.grant-card-gov:hover .card-title-gov {
    color: var(--gov-navy-600);
}

/* ===== Summary ===== */
.card-summary-gov {
    font-size: 13px;
    line-height: 1.6;
    color: var(--gov-gray-700);
    margin: 0 0 12px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 42px;
    max-height: 42px;
}

/* ===== Meta Info ===== */
.card-meta-gov {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 12px;
    min-height: 28px;
}

.meta-item-gov {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: var(--gov-gray-700);
    padding: 5px 10px;
    background: var(--gov-navy-50);
    border-radius: var(--gov-radius);
    font-weight: 600;
    height: 28px;
    line-height: 1;
}

.meta-icon-gov {
    width: 14px;
    height: 14px;
    stroke: var(--gov-navy-600);
    stroke-width: 2;
    flex-shrink: 0;
}

.meta-highlight-gov {
    color: var(--gov-navy-800);
    font-weight: 700;
}

/* ===== Footer ===== */
.card-footer-gov {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 12px;
    border-top: 1px solid var(--gov-gray-200);
    min-height: 48px;
    flex-wrap: wrap;
    gap: 10px;
}

.footer-left-gov {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

/* Deadline Info */
.deadline-info-gov {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.deadline-badge-gov {
    padding: 6px 12px;
    border-radius: var(--gov-radius);
    font-size: 12px;
    font-weight: 700;
    border: 2px solid;
    height: 32px;
    display: inline-flex;
    align-items: center;
    line-height: 1;
    white-space: nowrap;
}

.deadline-urgency-gov {
    font-size: 11px;
    font-weight: 800;
    padding: 5px 10px;
    height: 28px;
}

.deadline-date-gov {
    font-size: 12px;
    font-weight: 600;
}

/* Critical */
.deadline-urgency-gov.deadline-critical {
    background: var(--gov-red);
    color: var(--gov-white);
    border-color: var(--gov-red);
    animation: pulse 1.5s ease infinite 0.3s;
}

.deadline-date-gov.deadline-critical {
    background: var(--gov-red-light);
    color: var(--gov-red);
    border-color: var(--gov-red);
}

/* Warning */
.deadline-urgency-gov.deadline-warning {
    background: var(--gov-orange);
    color: var(--gov-white);
    border-color: var(--gov-orange);
}

.deadline-date-gov.deadline-warning {
    background: var(--gov-orange-light);
    color: var(--gov-orange);
    border-color: var(--gov-orange);
}

/* Soon */
.deadline-urgency-gov.deadline-soon {
    background: var(--gov-gold);
    color: var(--gov-navy-900);
    border-color: var(--gov-gold);
}

.deadline-date-gov.deadline-soon {
    background: var(--gov-gold-pale);
    color: var(--gov-navy-800);
    border-color: var(--gov-gold);
}

/* Normal */
.deadline-date-gov.deadline-normal {
    background: var(--gov-navy-50);
    color: var(--gov-navy-700);
    border-color: var(--gov-navy-200);
}

/* Expired */
.deadline-date-gov.deadline-expired {
    background: var(--gov-gray-100);
    color: var(--gov-gray-600);
    border-color: var(--gov-gray-300);
}

/* Adoption Badge */
.adoption-badge-gov {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    background: var(--gov-green-light);
    border-radius: var(--gov-radius);
    font-size: 12px;
    font-weight: 700;
    color: var(--gov-green);
    height: 32px;
    line-height: 1;
    white-space: nowrap;
}

.adoption-icon-gov {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    stroke-width: 2;
    flex-shrink: 0;
}

/* Amount Badge */
.amount-badge-gov {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    background: var(--gov-gold-pale);
    border: 2px solid var(--gov-gold);
    border-radius: var(--gov-radius);
    font-size: 12px;
    font-weight: 700;
    color: var(--gov-navy-900);
    height: 32px;
    line-height: 1;
    white-space: nowrap;
}

.amount-icon-gov {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    stroke-width: 2;
    flex-shrink: 0;
}

/* Footer Right */
.footer-right-gov {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--gov-gray-600);
    line-height: 1;
}

.org-name-gov {
    font-weight: 600;
    color: var(--gov-navy-700);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 180px;
}

.region-name-gov {
    padding: 4px 10px;
    background: var(--gov-navy-50);
    border-radius: var(--gov-radius);
    font-weight: 600;
    color: var(--gov-navy-700);
    white-space: nowrap;
    height: 24px;
    display: inline-flex;
    align-items: center;
    line-height: 1;
}

/* ===== Grid View (2-3 columns) ===== */
[data-view="grid"] .grant-card-gov {
    min-height: 180px;
}

[data-view="grid"] .card-link-gov {
    padding: 14px;
}

[data-view="grid"] .card-title-gov {
    font-size: 15px;
    min-height: 42px;
    max-height: 42px;
}

[data-view="grid"] .card-summary-gov {
    font-size: 12px;
    min-height: 38px;
    max-height: 38px;
}

[data-view="grid"] .card-footer-gov {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
}

[data-view="grid"] .footer-right-gov {
    width: 100%;
}

/* ===== List View (Compact) ===== */
[data-view="list"] .grant-card-gov {
    min-height: 100px;
}

[data-view="list"] .card-link-gov {
    padding: 12px;
}

[data-view="list"] .card-title-gov {
    font-size: 14px;
    -webkit-line-clamp: 1;
    min-height: 20px;
    max-height: 20px;
}

[data-view="list"] .card-summary-gov {
    display: none;
}

[data-view="list"] .card-meta-gov {
    margin-bottom: 8px;
}

[data-view="list"] .card-footer-gov {
    padding-top: 8px;
    min-height: 36px;
}

[data-view="list"] .catch-tags-gov {
    display: none;
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .grant-card-gov {
        width: 100% !important;
        max-width: 100% !important;
    }
    
    .card-link-gov {
        padding: 14px;
    }
    
    .card-title-gov {
        font-size: 15px;
        min-height: 42px;
        max-height: 42px;
    }
    
    .card-summary-gov {
        font-size: 12px;
        line-height: 1.7;
        min-height: 41px;
        max-height: 41px;
    }
    
    .card-footer-gov {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        min-height: auto;
    }
    
    .footer-left-gov {
        width: 100%;
    }
    
    .footer-right-gov {
        width: 100%;
    }
    
    .org-name-gov {
        max-width: 100%;
    }
}

@media (max-width: 480px) {
    .grant-card-gov {
        min-height: 120px;
    }
    
    .card-link-gov {
        padding: 12px;
    }
    
    .card-title-gov {
        font-size: 14px;
        line-height: 1.35;
        min-height: 38px;
        max-height: 38px;
    }
    
    .card-summary-gov {
        font-size: 11px;
        line-height: 1.5;
        min-height: 33px;
        max-height: 33px;
    }
    
    .catch-tags-gov {
        display: none;
    }
    
    .card-meta-gov {
        gap: 6px;
        margin-bottom: 8px;
    }
    
    .meta-item-gov {
        font-size: 11px;
        padding: 4px 8px;
        height: 24px;
    }
    
    .card-footer-gov {
        padding-top: 8px;
    }
    
    .deadline-badge-gov {
        font-size: 11px;
        padding: 5px 10px;
        height: 28px;
    }
    
    .adoption-badge-gov,
    .amount-badge-gov {
        font-size: 11px;
        padding: 5px 10px;
        height: 28px;
    }
}

/* ===== Performance ===== */
.grant-card-gov {
    transform: translateZ(0);
    backface-visibility: hidden;
    will-change: box-shadow;
}

/* ===== Accessibility ===== */
.grant-card-gov:focus-within {
    outline: 3px solid var(--gov-gold);
    outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
    .grant-card-gov,
    .grant-card-gov *,
    .catch-urgent,
    .deadline-critical {
        animation: none !important;
        transition: none !important;
    }
}

/* ===== Font Optimization ===== */
.grant-card-gov {
    font-display: swap;
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* ===== Print ===== */
@media print {
    .grant-card-gov {
        border: 1px solid #000;
        box-shadow: none;
        page-break-inside: avoid;
    }
    
    .catch-tags-gov {
        display: none;
    }
}
</style>

<article class="grant-card-gov" 
         data-post-id="<?php echo esc_attr($post_id); ?>"
         itemscope 
         itemtype="https://schema.org/GovernmentService">
    
    <a href="<?php echo esc_url($permalink); ?>" 
       class="card-link-gov" 
       itemprop="url"
       aria-label="<?php echo esc_attr($title); ?>の詳細を見る">
        
        <!-- ===== Header ===== -->
        <div class="card-header-gov">
            <div class="header-left-gov">
                <!-- Status Badge -->
                <span class="status-badge-gov <?php echo esc_attr($status_data['class']); ?>" 
                      aria-label="ステータス: <?php echo esc_attr($status_data['label']); ?>">
                    <?php echo esc_html($status_data['label']); ?>
                </span>
                
                <!-- Catch Tags -->
                <?php if (!empty($catch_tags)): ?>
                <div class="catch-tags-gov">
                    <?php foreach ($catch_tags as $tag): ?>
                        <span class="catch-tag-gov catch-<?php echo esc_attr($tag['type']); ?>"
                              aria-label="<?php echo esc_attr($tag['text']); ?>">
                            <?php echo esc_html($tag['text']); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Category Tag -->
            <?php if ($main_category): ?>
                <span class="category-tag-gov" 
                      itemprop="category"
                      aria-label="カテゴリ: <?php echo esc_attr($main_category); ?>">
                    <?php echo esc_html($main_category); ?>
                </span>
            <?php endif; ?>
        </div>
        
        <!-- ===== Title ===== -->
        <h3 class="card-title-gov" itemprop="name">
            <?php echo esc_html($title); ?>
        </h3>
        
        <!-- ===== Summary ===== -->
        <?php if ($ai_summary): ?>
        <p class="card-summary-gov" itemprop="description">
            <?php echo esc_html($ai_summary); ?>
        </p>
        <?php elseif ($excerpt): ?>
        <p class="card-summary-gov" itemprop="description">
            <?php echo esc_html($excerpt); ?>
        </p>
        <?php endif; ?>
        
        <!-- ===== Meta Info ===== -->
        <div class="card-meta-gov">
            <?php if ($subsidy_display): ?>
            <div class="meta-item-gov">
                <svg class="meta-icon-gov" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-linecap="round"/>
                </svg>
                <span class="meta-highlight-gov"><?php echo esc_html($subsidy_display); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- ===== Footer ===== -->
        <div class="card-footer-gov">
            <div class="footer-left-gov">
                
                <!-- Deadline -->
                <?php if ($deadline_display): ?>
                <div class="deadline-info-gov">
                    <?php if ($deadline_urgency): ?>
                    <div class="deadline-badge-gov deadline-urgency-gov <?php echo esc_attr($deadline_class); ?>"
                         aria-label="緊急度: <?php echo esc_attr($deadline_urgency); ?>">
                        <?php echo esc_html($deadline_urgency); ?>
                    </div>
                    <?php endif; ?>
                    <div class="deadline-badge-gov deadline-date-gov <?php echo esc_attr($deadline_class); ?>" 
                         itemprop="validThrough" 
                         content="<?php echo esc_attr($deadline_date); ?>"
                         aria-label="締切日: <?php echo esc_attr($deadline_display); ?>">
                        <?php echo esc_html($deadline_display); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Amount -->
                <?php if ($amount_display): ?>
                <div class="amount-badge-gov" aria-label="助成金額: <?php echo esc_attr($amount_display); ?>">
                    <svg class="amount-icon-gov" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-linecap="round"/>
                    </svg>
                    <?php echo esc_html($amount_display); ?>
                </div>
                <?php endif; ?>
                
                <!-- Adoption Rate -->
                <?php if ($adoption_rate > 0): ?>
                <div class="adoption-badge-gov" aria-label="採択率: <?php echo esc_attr($adoption_rate); ?>%">
                    <svg class="adoption-icon-gov" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    採択率<?php echo esc_html($adoption_rate); ?>%
                </div>
                <?php endif; ?>
                
            </div>
            
            <div class="footer-right-gov">
                <?php if ($organization): ?>
                    <span class="org-name-gov" 
                          itemprop="provider"
                          aria-label="実施機関: <?php echo esc_attr($organization); ?>">
                        <?php echo esc_html($organization); ?>
                    </span>
                    <span aria-hidden="true">・</span>
                <?php endif; ?>
                <span class="region-name-gov" 
                      itemprop="areaServed"
                      aria-label="対象地域: <?php echo esc_attr($region_display); ?>">
                    <?php echo esc_html($region_display); ?>
                </span>
            </div>
        </div>
        
    </a>
    
</article>
