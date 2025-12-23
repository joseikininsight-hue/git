<?php
/**
 * Template Part: Grant Card - ZUKAN (図鑑) Style v3.0
 * 補助金カード（ペーパークリップスタイル）
 * HTMLリファレンスデザインを忠実に再現
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Grant_System
 * @version 3.0.0 - Paper Clip Card Style
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// 現在の投稿情報を取得
$post_id = get_the_ID();
$deadline = get_post_meta($post_id, 'deadline_date', true);
$amount = get_post_meta($post_id, 'grant_amount_max', true);
$rate = get_post_meta($post_id, 'grant_rate', true);
$organizer = get_post_meta($post_id, 'grant_organizer', true);
$categories = get_the_terms($post_id, 'grant_category');
$prefecture = get_the_terms($post_id, 'grant_prefecture');
$is_featured = get_post_meta($post_id, 'is_featured', true);
$view_count = intval(get_post_meta($post_id, 'view_count', true));
$application_status = get_post_meta($post_id, 'application_status', true);

// 締切日までの日数計算
$days_left = null;
$is_urgent = false;
$deadline_display = '';
if ($deadline) {
    $deadline_date = new DateTime($deadline);
    $now = new DateTime();
    $diff = $now->diff($deadline_date);
    if ($diff->invert == 0) {
        $days_left = $diff->days;
        $is_urgent = ($days_left <= 14);
    }
    $deadline_display = date('Y/m/d', strtotime($deadline));
}

// 新着判定（7日以内）
$is_new = (strtotime(get_the_date('Y-m-d')) > strtotime('-7 days'));

// 抜粋
$excerpt = get_the_excerpt();
if (empty($excerpt)) {
    $excerpt = wp_trim_words(strip_tags(get_the_content()), 60, '...');
}

// タグ取得
$tags = get_the_terms($post_id, 'grant_tag');
$tag_names = array();
if ($tags && !is_wp_error($tags)) {
    foreach (array_slice($tags, 0, 3) as $tag) {
        $tag_names[] = $tag->name;
    }
}

// 金額フォーマット
$amount_display = '';
$amount_unit = '';
if ($amount) {
    if ($amount >= 10000) {
        $amount_display = number_format($amount / 10000, 1);
        $amount_unit = '億円';
    } elseif ($amount >= 1) {
        $amount_display = number_format($amount);
        $amount_unit = '万円';
    }
}

// 補助率フォーマット
$rate_display = '';
if ($rate) {
    $rate_display = $rate;
}

// 募集状況テキスト
$status_text = '';
$status_class = '';
if ($application_status) {
    switch ($application_status) {
        case 'active':
        case 'open':
            $status_text = '募集中';
            $status_class = '';
            break;
        case 'upcoming':
            $status_text = '募集予定';
            $status_class = 'upcoming';
            break;
        case 'closed':
            $status_text = '募集終了';
            $status_class = 'closed';
            break;
    }
}

// カテゴリ名
$category_name = '';
if ($categories && !is_wp_error($categories)) {
    $category_name = $categories[0]->name;
}

// 都道府県名
$prefecture_name = '';
if ($prefecture && !is_wp_error($prefecture)) {
    $prefecture_name = $prefecture[0]->name;
}
?>

<article class="zukan-grant-card" itemscope itemtype="https://schema.org/GovernmentService">
    <a href="<?php the_permalink(); ?>" class="zukan-grant-card-link" itemprop="url">
        
        <!-- バッジエリア -->
        <div class="zukan-grant-badges">
            <?php if ($is_featured): ?>
            <span class="zukan-badge zukan-badge-featured">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                注目
            </span>
            <?php endif; ?>
            
            <?php if ($is_urgent && $days_left !== null): ?>
            <span class="zukan-badge zukan-badge-urgent">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                あと<?php echo $days_left; ?>日
            </span>
            <?php endif; ?>
            
            <?php if ($is_new && !$is_urgent): ?>
            <span class="zukan-badge zukan-badge-new">NEW</span>
            <?php endif; ?>
            
            <?php if ($status_text): ?>
            <span class="zukan-badge zukan-badge-status <?php echo esc_attr($status_class); ?>">
                <?php echo esc_html($status_text); ?>
            </span>
            <?php endif; ?>
        </div>
        
        <!-- コンテンツエリア（3カラム構成） -->
        <div class="zukan-grant-content">
            
            <!-- 左側 - カテゴリ・地域 -->
            <div class="zukan-grant-category-area">
                <?php if ($category_name): ?>
                <span class="zukan-grant-category-badge" itemprop="serviceType">
                    <?php echo esc_html($category_name); ?>
                </span>
                <?php endif; ?>
                
                <?php if ($prefecture_name): ?>
                <div class="zukan-grant-prefecture" itemprop="areaServed">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <?php echo esc_html($prefecture_name); ?>
                </div>
                <?php endif; ?>
                
                <?php if ($organizer): ?>
                <div class="zukan-grant-organizer">
                    <?php echo esc_html(mb_strimwidth($organizer, 0, 24, '...')); ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- 中央 - タイトル・説明・タグ -->
            <div class="zukan-grant-main">
                <h3 class="zukan-grant-title" itemprop="name">
                    <?php the_title(); ?>
                </h3>
                
                <p class="zukan-grant-description" itemprop="description">
                    <?php echo esc_html($excerpt); ?>
                </p>
                
                <?php if (!empty($tag_names)): ?>
                <div class="zukan-grant-tags">
                    <?php foreach ($tag_names as $tag_name): ?>
                    <span class="zukan-grant-tag"><?php echo esc_html($tag_name); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- 右側 - 金額・締切 -->
            <div class="zukan-grant-amount-area">
                <?php if ($amount_display): ?>
                <div class="zukan-grant-amount-label">補助上限</div>
                <div class="zukan-grant-amount">
                    <?php echo esc_html($amount_display); ?><span class="zukan-grant-amount-unit"><?php echo esc_html($amount_unit); ?></span>
                </div>
                <?php else: ?>
                <div class="zukan-grant-amount-label">補助額</div>
                <div class="zukan-grant-amount" style="font-size: 14px;">要確認</div>
                <?php endif; ?>
                
                <?php if ($rate_display): ?>
                <div class="zukan-grant-rate">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                    </svg>
                    補助率 <?php echo esc_html($rate_display); ?>
                </div>
                <?php endif; ?>
                
                <?php if ($deadline_display): ?>
                <div class="zukan-grant-deadline <?php echo $is_urgent ? 'urgent' : ''; ?>">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <?php echo esc_html($deadline_display); ?>
                </div>
                <?php endif; ?>
            </div>
            
        </div>
        
        <!-- フッター -->
        <div class="zukan-grant-footer">
            <div class="zukan-grant-meta">
                <span class="zukan-grant-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <?php echo get_the_date('Y.m.d'); ?>
                </span>
                <?php if ($view_count > 0): ?>
                <span class="zukan-grant-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <?php echo number_format($view_count); ?>
                </span>
                <?php endif; ?>
            </div>
            <span class="zukan-grant-more">詳細を見る</span>
        </div>
        
    </a>
</article>
