<?php
/**
 * Template Part: Grant Card - Zukan Dictionary Style (Enhanced v7.0)
 * 補助金図鑑 - 辞書風カードデザイン（リッチ版）
 * 
 * Design inspiration from paper-sheet style with:
 * - Left accent line for visual hierarchy
 * - Clear meta information layout  
 * - Enhanced typography and spacing
 * - Ribbon markers for special items
 * 
 * @package Grant_Insight_Perfect
 * @version 7.0.0 - ACFフィールド名修正
 */

// セキュリティチェック
if (!defined('ABSPATH')) exit;

// ==================================================
// 1. データ取得・整形処理
// ==================================================
$post_id = get_the_ID();

// ACFフィールドからデータ取得（正しいフィールド名を使用）
// get_field()が使えない場合はget_post_meta()にフォールバック
$deadline       = get_post_meta($post_id, 'deadline_date', true);
$amount_text    = get_post_meta($post_id, 'max_amount', true);           // テキスト形式の金額（例: "300万円"）
$amount_numeric = get_post_meta($post_id, 'max_amount_numeric', true);   // 数値形式の金額（円単位）
$rate           = get_post_meta($post_id, 'subsidy_rate_detailed', true); // 補助率詳細
$organizer      = get_post_meta($post_id, 'organization', true);          // 実施組織
$is_featured    = get_post_meta($post_id, 'is_featured', true);
$status         = get_post_meta($post_id, 'application_status', true);

// カテゴリラベル
$categories = get_the_terms($post_id, 'grant_category');
$category_label = ($categories && !is_wp_error($categories)) ? $categories[0]->name : '補助金';

// 締切日計算
$days_left = null;
$is_urgent = false;
$deadline_display = '随時受付';

if ($deadline) {
    $deadline_date = new DateTime($deadline);
    $now = new DateTime();
    $diff = $now->diff($deadline_date);
    
    if ($diff->invert == 0) {
        $days_left = $diff->days;
        $is_urgent = ($days_left <= 14);
    }
    $deadline_display = date('Y年n月j日', strtotime($deadline));
}

// 金額フォーマット
// 優先順位: 1.テキスト形式 → 2.数値形式からフォーマット → 3.デフォルト
$amount_formatted = '';
$amount_class = '';

if (!empty($amount_text)) {
    // テキスト形式がある場合はそのまま使用
    $amount_formatted = $amount_text;
    // 大きな金額の場合はクラス追加
    if (preg_match('/億|[1-9][0-9]{3,}万/', $amount_text)) {
        $amount_class = 'amount-large';
    }
} elseif (!empty($amount_numeric) && $amount_numeric > 0) {
    // 数値形式から表示用にフォーマット
    $amount = intval($amount_numeric);
    if ($amount >= 100000000) {
        $amount_formatted = number_format($amount / 100000000, 1) . '億円';
        $amount_class = 'amount-large';
    } elseif ($amount >= 10000) {
        $amount_formatted = number_format($amount / 10000) . '万円';
        if ($amount >= 10000000) {
            $amount_class = 'amount-large';
        }
    } else {
        $amount_formatted = number_format($amount) . '円';
    }
}

// デフォルト値の設定
if (empty($amount_formatted)) {
    $amount_formatted = '詳細参照';
}

// 抜粋文の生成
$excerpt = get_the_excerpt();
if (empty($excerpt)) {
    $excerpt = wp_trim_words(strip_tags(get_the_content()), 60, '...');
}

// タグ（最大3つまで）
$tags = get_the_terms($post_id, 'grant_tag');
$tag_list = [];
if ($tags && !is_wp_error($tags)) {
    foreach (array_slice($tags, 0, 3) as $tag) {
        $tag_list[] = $tag->name;
    }
}

// 都道府県情報を取得
$prefectures = get_the_terms($post_id, 'grant_prefecture');
$current_prefecture = null;

// 現在閲覧中の都道府県アーカイブか確認
if (is_tax('grant_prefecture')) {
    $current_term = get_queried_object();
    $current_prefecture = $current_term->name;
}

// 「全国」タグの処理：都道府県ページで表示する場合は県名を追加
if (!empty($tag_list)) {
    foreach ($tag_list as $key => $tag_name) {
        if ($tag_name === '全国' && $current_prefecture) {
            $tag_list[$key] = "全国（{$current_prefecture}対応）";
        }
    }
}

// 都道府県タグも同様に処理
if ($prefectures && !is_wp_error($prefectures) && $current_prefecture) {
    foreach ($prefectures as $pref) {
        if ($pref->name === '全国') {
            // 「全国」の場合、現在の都道府県名を追加
            $tag_list[] = "全国（{$current_prefecture}対応）";
            break;
        }
    }
}

// ステータスラベル
$status_labels = array(
    'open' => '募集中',
    'upcoming' => '募集予定',
    'closed' => '募集終了',
    'suspended' => '一時停止',
);
$status_label = isset($status_labels[$status]) ? $status_labels[$status] : '';
$status_class = $status === 'open' ? 'status-open' : ($status === 'closed' ? 'status-closed' : '');

// 補助率のフォーマット（表示用）
$rate_display = '';
if (!empty($rate)) {
    // すでにフォーマット済みの場合はそのまま使用
    $rate_display = $rate;
} else {
    $rate_display = '詳細参照';
}

// 組織名のフォーマット
$organizer_display = !empty($organizer) ? $organizer : '詳細参照';

// ==================================================
// 2. HTML出力 - Paper Sheet Style Card
// ==================================================
?>

<article <?php post_class('zukan-grant-card-v2 group'); ?>>
    <a href="<?php the_permalink(); ?>" class="card-inner-v2">
        
        <!-- 左側アクセントライン -->
        <div class="card-accent-line"></div>
        
        <!-- カテゴリバッジ（左上） -->
        <div class="card-category-badge">
            <?php echo esc_html($category_label); ?>
        </div>
        
        <?php if ($is_featured || $is_urgent): ?>
        <!-- リボンマーク（注目/緊急） -->
        <div class="card-ribbon <?php echo $is_urgent ? 'ribbon-urgent' : 'ribbon-featured'; ?>"></div>
        <?php endif; ?>
        
        <!-- メインコンテンツエリア -->
        <div class="card-body-v2">
            <!-- ヘッダー部分 -->
            <div class="card-header-v2">
                <span class="card-organizer"><?php echo esc_html($organizer_display); ?></span>
                <?php if ($status_label): ?>
                <span class="card-status <?php echo esc_attr($status_class); ?>"><?php echo esc_html($status_label); ?></span>
                <?php endif; ?>
            </div>
            
            <!-- タイトル -->
            <h3 class="card-title-v2"><?php the_title(); ?></h3>
            
            <!-- 説明文 -->
            <p class="card-excerpt-v2"><?php echo esc_html($excerpt); ?></p>
            
            <!-- タグ -->
            <?php if (!empty($tag_list)): ?>
            <div class="card-tags-v2">
                <?php foreach ($tag_list as $tag): ?>
                <span class="card-tag">#<?php echo esc_html($tag); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- メタ情報（右側） -->
        <div class="card-meta-v2">
            <div class="meta-item meta-amount">
                <span class="meta-label">MAX AMOUNT</span>
                <span class="meta-value <?php echo esc_attr($amount_class); ?>"><?php echo esc_html($amount_formatted); ?></span>
            </div>
            <div class="meta-item meta-rate">
                <span class="meta-label">補助率</span>
                <span class="meta-value"><?php echo esc_html($rate_display); ?></span>
            </div>
            <div class="meta-item meta-deadline">
                <span class="meta-label">Deadline</span>
                <span class="meta-value <?php echo $is_urgent ? 'deadline-urgent' : ''; ?>">
                    <svg class="deadline-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <?php echo $deadline ? esc_html($deadline_display) : '随時'; ?>
                </span>
            </div>
            <?php if ($is_urgent && $days_left !== null): ?>
            <div class="meta-urgent-badge">
                あと<?php echo $days_left; ?>日
            </div>
            <?php endif; ?>
            <div class="card-action-v2">
                <span>View Details</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14m-7-7l7 7-7 7"/>
                </svg>
            </div>
        </div>
        
    </a>
</article>
