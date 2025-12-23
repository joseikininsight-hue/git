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

<article <?php post_class('relative bg-white p-8 mb-10 shadow-book-depth border border-gray-200 group transition-transform duration-300 hover:-translate-y-1'); ?>>
    
    <!-- Paper Clip Effect (Decorative) -->
    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 w-32 h-6 bg-gray-200 rounded-sm opacity-50 shadow-inner z-10 pointer-events-none"></div>

    <a href="<?php the_permalink(); ?>" class="block relative z-0">
        <div class="flex flex-col md:flex-row gap-6 md:gap-10">
            
            <!-- Left Column: Basic Info & Stats -->
            <div class="md:w-1/3 md:border-r border-gray-100 md:pr-6 flex flex-col justify-center text-center md:text-right">
                
                <!-- Category / Label -->
                <span class="block text-xs font-bold tracking-widest text-gray-400 uppercase mb-2 font-sans">
                    <?php echo esc_html($category_label); ?>
                </span>

                <!-- Title -->
                <h3 class="text-xl font-serif font-bold text-ink-primary mb-3 leading-snug group-hover:text-accent-red transition-colors">
                    <?php the_title(); ?>
                </h3>

                <!-- Organizer / Difficulty (Placeholder logic) -->
                <div class="text-accent-gold text-xs font-bold mb-4 font-sans">
                    <?php echo $organizer ? esc_html($organizer) : '主催機関不明'; ?>
                </div>

                <!-- Amount Badge -->
                <div class="mt-auto md:mt-0">
                    <span class="inline-block bg-highlight-blue text-white text-sm font-bold px-4 py-1.5 rounded-sm mx-auto md:ml-auto md:mr-0 shadow-sm font-sans tracking-wide">
                        最大<?php echo esc_html($amount_formatted); ?>
                    </span>
                </div>
            </div>

            <!-- Right Column: Description & Details -->
            <div class="md:w-2/3 flex flex-col justify-center">
                
                <!-- Description -->
                <p class="text-sm leading-7 font-serif text-gray-700 mb-5 line-clamp-3">
                    <?php echo esc_html($excerpt); ?>
                </p>

                <!-- Detail List Box -->
                <div class="bg-paper-bg p-4 rounded-sm border border-gray-100">
                    <ul class="text-xs text-gray-600 space-y-2 font-sans">
                        <!-- Rate -->
                        <?php if ($rate): ?>
                        <li class="flex items-start">
                            <span class="font-bold text-gray-400 w-16 shrink-0">補助率</span>
                            <span class="text-ink-primary font-medium"><?php echo esc_html($rate); ?></span>
                        </li>
                        <?php endif; ?>

                        <!-- Deadline -->
                        <li class="flex items-start">
                            <span class="font-bold text-gray-400 w-16 shrink-0">締切</span>
                            <span class="font-medium <?php echo $is_urgent ? 'text-accent-red font-bold' : 'text-ink-primary'; ?>">
                                <?php echo esc_html($deadline_display); ?>
                                <?php if ($is_urgent && $days_left !== null): ?>
                                    <span class="ml-2 inline-block bg-accent-red text-white text-[10px] px-1.5 py-0.5 rounded-sm">あと<?php echo $days_left; ?>日</span>
                                <?php endif; ?>
                            </span>
                        </li>

                        <!-- Tags -->
                        <?php if (!empty($tag_list)): ?>
                        <li class="flex items-start pt-1">
                            <span class="font-bold text-gray-400 w-16 shrink-0 mt-0.5">タグ</span>
                            <div class="flex flex-wrap gap-1.5">
                                <?php foreach ($tag_list as $tag): ?>
                                    <span class="inline-block border border-gray-300 text-gray-500 px-2 py-0.5 rounded-sm text-[10px]">
                                        #<?php echo esc_html($tag); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>
        </div>
    </a>
</article>
