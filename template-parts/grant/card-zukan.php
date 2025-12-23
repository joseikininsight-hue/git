<?php
/**
 * Template Part: Grant Card - ZUKAN (図鑑) Style v4.0
 * 補助金カード（左アクセントライン・クリーンデザイン）
 * HTMLリファレンスを忠実に再現
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Grant_System
 * @version 4.0.0 - Clean Card with Left Accent Line
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
    $excerpt = wp_trim_words(strip_tags(get_the_content()), 80, '...');
}

// タグ取得
$tags = get_the_terms($post_id, 'grant_tag');
$tag_names = array();
if ($tags && !is_wp_error($tags)) {
    foreach (array_slice($tags, 0, 4) as $tag) {
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

// カテゴリ名
$category_name = '';
$category_label = '';
if ($categories && !is_wp_error($categories)) {
    $category_name = $categories[0]->name;
    // 短縮ラベル
    $label_map = array(
        '創業・起業' => '創業',
        '設備投資' => '設備',
        'IT・DX' => 'IT/DX',
        '省エネ・環境' => '省エネ',
        '販路開拓' => '販路',
        '人材・雇用' => '人材',
        '研究開発' => 'R&D',
    );
    $category_label = isset($label_map[$category_name]) ? $label_map[$category_name] : mb_substr($category_name, 0, 4);
}

// 都道府県名
$prefecture_name = '';
if ($prefecture && !is_wp_error($prefecture)) {
    $prefecture_name = $prefecture[0]->name;
}
?>

<article class="zukan-grant-card" itemscope itemtype="https://schema.org/GovernmentService">
    <a href="<?php the_permalink(); ?>" class="zukan-grant-card-link" itemprop="url">
        
        <!-- 左側コンテンツ -->
        <div class="zukan-grant-content">
            
            <!-- バッジ・カテゴリエリア -->
            <div class="zukan-grant-badges">
                <?php if ($category_label): ?>
                <span class="zukan-badge zukan-grant-category-badge" itemprop="serviceType">
                    <?php echo esc_html($category_label); ?>
                </span>
                <?php endif; ?>
                
                <?php if ($organizer): ?>
                <span class="zukan-grant-organizer">
                    <?php echo esc_html(mb_strimwidth($organizer, 0, 30, '...')); ?>
                </span>
                <?php endif; ?>
            </div>
            
            <!-- メインコンテンツ -->
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
            
        </div>
        
        <!-- 右側メタ情報 -->
        <div class="zukan-grant-amount-area">
            <?php if ($amount_display): ?>
            <div>
                <p class="zukan-grant-amount-label">Max Amount</p>
                <p class="zukan-grant-amount"><?php echo esc_html($amount_display); ?><span class="zukan-grant-amount-unit"><?php echo esc_html($amount_unit); ?></span></p>
            </div>
            <?php else: ?>
            <div>
                <p class="zukan-grant-amount-label">Max Amount</p>
                <p class="zukan-grant-amount" style="font-size: 14px;">要確認</p>
            </div>
            <?php endif; ?>
            
            <?php if ($rate_display): ?>
            <div>
                <p class="zukan-grant-amount-label">Rate</p>
                <p class="zukan-grant-rate"><?php echo esc_html($rate_display); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($deadline_display): ?>
            <div>
                <p class="zukan-grant-amount-label">Deadline</p>
                <p class="zukan-grant-deadline <?php echo $is_urgent ? 'urgent' : ''; ?>">
                    <?php echo esc_html($deadline_display); ?>
                    <?php if ($is_urgent && $days_left !== null): ?>
                    <span style="margin-left: 4px; font-size: 10px; background: #a63737; color: #fff; padding: 2px 6px; border-radius: 2px;">
                        あと<?php echo $days_left; ?>日
                    </span>
                    <?php endif; ?>
                </p>
            </div>
            <?php endif; ?>
            
            <span class="zukan-grant-more">詳細を見る</span>
        </div>
        
    </a>
</article>
