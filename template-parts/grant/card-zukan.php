<?php
/**
 * Template Part: Grant Card - Editor's Pick Style
 * 書籍風デザイン（クリップ留めカードスタイル）
 * 
 * @package Grant_Insight_Perfect
 * @version 4.1.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) exit;

// ==================================================
// 1. データ取得・整形処理
// ==================================================
$post_id = get_the_ID();

// メタデータ取得
$deadline       = get_post_meta($post_id, 'deadline_date', true);
$amount         = get_post_meta($post_id, 'grant_amount_max', true);
$rate           = get_post_meta($post_id, 'grant_rate', true);
$organizer      = get_post_meta($post_id, 'grant_organizer', true);
$is_featured    = get_post_meta($post_id, 'is_featured', true);
$status         = get_post_meta($post_id, 'application_status', true);

// カテゴリラベル
$categories = get_the_terms($post_id, 'grant_category');
$category_label = ($categories && !is_wp_error($categories)) ? $categories[0]->name : '補助金';

// 締切日計算
$days_left = null;
$is_urgent = false;
$deadline_display = '随時受付'; // デフォルト

if ($deadline) {
    $deadline_date = new DateTime($deadline);
    $now = new DateTime();
    $diff = $now->diff($deadline_date);
    
    // 過去の日付でない場合
    if ($diff->invert == 0) {
        $days_left = $diff->days;
        $is_urgent = ($days_left <= 14); // 2週間前なら緊急表示
    }
    $deadline_display = date('Y年n月j日', strtotime($deadline));
}

// 金額フォーマット (例: 3000000 -> 300万円, 100000000 -> 1億円)
$amount_formatted = '金額不明';
if ($amount) {
    if ($amount >= 100000000) {
        $amount_formatted = ($amount / 100000000) . '億円';
    } elseif ($amount >= 10000) {
        $amount_formatted = number_format($amount / 10000) . '万円';
    }
}

// 抜粋文の生成
$excerpt = get_the_excerpt();
if (empty($excerpt)) {
    $excerpt = wp_trim_words(strip_tags(get_the_content()), 80, '...');
}

// タグ（最大3つまで）
$tags = get_the_terms($post_id, 'grant_tag');
$tag_list = [];
if ($tags && !is_wp_error($tags)) {
    foreach (array_slice($tags, 0, 3) as $tag) {
        $tag_list[] = $tag->name;
    }
}

// ==================================================
// 2. HTML出力
// ==================================================
?>

<article <?php post_class('group py-6 border-b border-gray-200 border-dashed hover:bg-gray-50 transition-colors px-2'); ?>>
    <a href="<?php the_permalink(); ?>" class="block">
        <div class="flex flex-col md:flex-row gap-4 items-start">
            <div class="md:w-1/4">
                <span class="font-serif font-bold text-xs bg-book-cover text-white px-2 py-1 mb-2 inline-block">
                    <?php echo esc_html($category_label); ?>
                </span>
                <div class="text-[10px] text-gray-500 font-sans mt-1">
                    <?php echo $organizer ? esc_html($organizer) : '主催機関不明'; ?>
                </div>
            </div>
            <div class="md:w-1/2">
                <h3 class="text-lg font-serif font-bold text-ink-primary mb-2 group-hover:text-accent-red transition-colors">
                    <?php the_title(); ?>
                </h3>
                <p class="text-sm text-gray-600 font-serif leading-relaxed mb-2 line-clamp-2">
                    <?php echo esc_html($excerpt); ?>
                </p>
                <div class="mt-1">
                    <?php foreach ($tag_list as $tag): ?>
                        <span class="inline-block bg-gray-200 text-gray-600 text-[10px] px-2 py-0.5 rounded-full mr-1">
                            <?php echo esc_html($tag); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="md:w-1/4 flex flex-col items-end justify-center text-right">
                <div class="text-xs text-gray-500 mb-1">補助上限</div>
                <div class="font-bold text-ink-primary font-serif text-lg border-b border-accent-gold leading-none pb-1">
                    <?php echo esc_html($amount_formatted); ?>
                </div>
                <div class="text-xs text-gray-400 mt-2">
                    補助率: <?php echo $rate ? esc_html($rate) : '要確認'; ?>
                </div>
            </div>
        </div>
    </a>
</article>
