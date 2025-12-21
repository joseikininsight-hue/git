<?php
/**
 * Single Column Template - Government Official Design v9.0
 * コラム記事詳細ページ - 補助金図鑑スタイル
 * 
 * @package Grant_Insight_Ultimate
 * @subpackage Column_System
 * @version 9.0.0
 */

if (!defined('ABSPATH')) exit;

if (!have_posts()) {
    wp_redirect(home_url('/404'), 302);
    exit;
}

get_header();
the_post();

// ===================================
// データ取得・整形
// ===================================
$post_id = get_the_ID();
$canonical_url = get_permalink($post_id);
$site_name = get_bloginfo('name');
$post_title = get_the_title();
$post_content = get_the_content();
$post_excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_tags($post_content), 55);

// ===================================
// ヘルパー関数定義（single-column専用プレフィックス）
// ===================================

/**
 * ACFフィールド取得
 */
function gicsc_get_field($field_name, $pid, $default = '') {
    $value = function_exists('get_field') ? get_field($field_name, $pid) : get_post_meta($pid, $field_name, true);
    return ($value !== null && $value !== false && $value !== '') ? $value : $default;
}

/**
 * ACF配列フィールド取得
 */
function gicsc_get_field_array($field_name, $pid) {
    $value = function_exists('get_field') ? get_field($field_name, $pid) : get_post_meta($pid, $field_name, true);
    return is_array($value) ? $value : array();
}

/**
 * タクソノミー取得
 */
function gicsc_get_terms($pid, $taxonomy) {
    if (!taxonomy_exists($taxonomy)) return array();
    $terms = wp_get_post_terms($pid, $taxonomy);
    return (!is_wp_error($terms) && !empty($terms)) ? $terms : array();
}

/**
 * 読了時間計算
 */
function gicsc_calculate_reading_time($content) {
    $word_count = mb_strlen(strip_tags($content), 'UTF-8');
    return max(1, ceil($word_count / 400));
}

/**
 * 日付フォーマット
 */
function gicsc_format_date($date_string, $format = 'Y年n月j日') {
    if (empty($date_string)) return '';
    $timestamp = strtotime($date_string);
    return $timestamp ? date($format, $timestamp) : '';
}

/**
 * キーポイントパース
 */
function gicsc_parse_key_points($key_points) {
    $result = array();
    
    if (empty($key_points)) {
        return $result;
    }
    
    if (is_array($key_points)) {
        foreach ($key_points as $point) {
            $point_text = '';
            if (is_array($point)) {
                $point_text = isset($point['point']) ? $point['point'] : 
                             (isset($point['text']) ? $point['text'] : 
                             (isset($point['content']) ? $point['content'] : ''));
            } else {
                $point_text = $point;
            }
            $point_text = trim(wp_strip_all_tags($point_text));
            if (!empty($point_text) && mb_strlen($point_text, 'UTF-8') > 2) {
                $result[] = $point_text;
            }
        }
    } else {
        $clean_text = wp_strip_all_tags($key_points);
        $lines = preg_split('/\r\n|\r|\n/', $clean_text);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && mb_strlen($line, 'UTF-8') > 2) {
                $result[] = $line;
            }
        }
    }
    
    return $result;
}

// ===================================
// メタ情報取得
// ===================================
$column = array(
    'read_time' => gicsc_get_field('estimated_read_time', $post_id),
    'view_count' => intval(gicsc_get_field('view_count', $post_id, 0)),
    'difficulty' => gicsc_get_field('difficulty_level', $post_id, 'beginner'),
    'last_updated' => gicsc_get_field('last_updated', $post_id),
    'last_verified_date' => gicsc_get_field('last_verified_date', $post_id),
    'key_points' => gicsc_get_field('key_points', $post_id),
    'target_audience' => gicsc_get_field_array('target_audience', $post_id),
    'ai_summary' => gicsc_get_field('ai_summary', $post_id),
    'supervisor_name' => gicsc_get_field('supervisor_name', $post_id),
    'supervisor_title' => gicsc_get_field('supervisor_title', $post_id),
    'supervisor_profile' => gicsc_get_field('supervisor_profile', $post_id),
    'supervisor_image' => gicsc_get_field_array('supervisor_image', $post_id),
    'supervisor_url' => gicsc_get_field('supervisor_url', $post_id),
    'supervisor_credentials' => gicsc_get_field_array('supervisor_credentials', $post_id),
    'source_url' => gicsc_get_field('source_url', $post_id),
    'source_name' => gicsc_get_field('source_name', $post_id),
    'related_grants' => gicsc_get_field_array('related_grants', $post_id),
    'related_columns' => gicsc_get_field_array('related_columns', $post_id),
    'faq_items' => gicsc_get_field_array('faq_items', $post_id),
    'is_featured' => (bool)gicsc_get_field('is_featured', $post_id, false),
    'is_new' => (bool)gicsc_get_field('is_new', $post_id, false),
    'is_updated' => (bool)gicsc_get_field('is_updated', $post_id, false),
);

// 読了時間
if (!$column['read_time']) {
    $column['read_time'] = gicsc_calculate_reading_time($post_content);
}

// キーポイントをパース
$key_points_array = gicsc_parse_key_points($column['key_points']);

// デフォルト監修者
if (empty($column['supervisor_name'])) {
    $column['supervisor_name'] = '補助金図鑑編集部';
    $column['supervisor_title'] = '中小企業診断士・行政書士監修';
    $column['supervisor_profile'] = '補助金・助成金の専門家チーム。中小企業診断士、行政書士、税理士など各分野の専門家が在籍。年間1,000件以上の補助金申請支援実績があります。';
    $column['supervisor_credentials'] = array(
        array('credential' => '中小企業診断士'),
        array('credential' => '行政書士'),
        array('credential' => '認定経営革新等支援機関'),
    );
}

// 最終確認日
$last_verified = $column['last_verified_date'] ? $column['last_verified_date'] : get_the_modified_date('Y-m-d');
$last_verified_display = gicsc_format_date($last_verified);
$freshness_class = '';
$freshness_label = '確認';
if ($last_verified) {
    $diff = (current_time('timestamp') - strtotime($last_verified)) / 86400;
    if ($diff < 30) { $freshness_class = 'fresh'; $freshness_label = '最新'; }
    elseif ($diff < 90) { $freshness_class = 'recent'; $freshness_label = '確認済'; }
    elseif ($diff > 180) { $freshness_class = 'old'; $freshness_label = '要確認'; }
}

// タクソノミー取得
$categories = gicsc_get_terms($post_id, 'column_category');
if (empty($categories)) {
    $categories = gicsc_get_terms($post_id, 'category');
}
$tags = gicsc_get_terms($post_id, 'column_tag');
if (empty($tags)) {
    $tags = gicsc_get_terms($post_id, 'post_tag');
}

// 難易度マップ
$difficulty_map = array(
    'beginner' => array('label' => '初級', 'level' => 1, 'class' => 'beginner'),
    'intermediate' => array('label' => '中級', 'level' => 2, 'class' => 'intermediate'),
    'advanced' => array('label' => '上級', 'level' => 3, 'class' => 'advanced'),
);
$difficulty = isset($difficulty_map[$column['difficulty']]) ? $difficulty_map[$column['difficulty']] : $difficulty_map['beginner'];

// 対象読者ラベル
$audience_labels = array(
    'startup' => array('label' => '創業・スタートアップを考えている方', 'icon' => 'rocket'),
    'sme' => array('label' => '中小企業の経営者・担当者', 'icon' => 'building'),
    'individual' => array('label' => '個人事業主・フリーランス', 'icon' => 'user'),
    'npo' => array('label' => 'NPO・一般社団法人', 'icon' => 'heart'),
    'agriculture' => array('label' => '農業・林業・漁業従事者', 'icon' => 'leaf'),
    'it' => array('label' => 'IT・デジタル関連事業者', 'icon' => 'laptop'),
    'manufacturing' => array('label' => '製造業・ものづくり企業', 'icon' => 'cog'),
    'service' => array('label' => 'サービス業・小売業', 'icon' => 'store'),
    'other' => array('label' => 'その他事業者', 'icon' => 'briefcase'),
);

// 閲覧数更新
$view_cookie = 'gicsc_viewed_' . $post_id;
if (!isset($_COOKIE[$view_cookie])) {
    update_post_meta($post_id, 'view_count', $column['view_count'] + 1);
    $column['view_count']++;
    $cookie_options = array(
        'expires' => time() + 86400,
        'path' => '/',
        'secure' => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax'
    );
    setcookie($view_cookie, '1', $cookie_options);
}

// パンくず
$breadcrumbs = array(
    array('name' => 'ホーム', 'url' => home_url('/')),
    array('name' => 'コラム', 'url' => get_post_type_archive_link('column') ?: home_url('/column/')),
);
if (!empty($categories[0])) {
    $cat_link = get_term_link($categories[0]);
    if (!is_wp_error($cat_link)) {
        $breadcrumbs[] = array('name' => $categories[0]->name, 'url' => $cat_link);
    }
}
$breadcrumbs[] = array('name' => $post_title, 'url' => $canonical_url);

// メタディスクリプション
$meta_desc = '';
if ($column['ai_summary']) {
    $meta_desc = mb_substr(wp_strip_all_tags($column['ai_summary']), 0, 120, 'UTF-8');
} elseif ($post_excerpt) {
    $meta_desc = mb_substr(wp_strip_all_tags($post_excerpt), 0, 120, 'UTF-8');
} else {
    $meta_desc = mb_substr(wp_strip_all_tags($post_content), 0, 120, 'UTF-8');
}

// OGP画像
$og_image = get_the_post_thumbnail_url($post_id, 'full');
if (!$og_image) {
    $og_image = get_template_directory_uri() . '/assets/images/ogp-default.jpg';
}

// 関連補助金取得
$related_grants = array();
if (!empty($column['related_grants'])) {
    foreach ($column['related_grants'] as $grant_item) {
        $grant_id = is_array($grant_item) ? (isset($grant_item['ID']) ? intval($grant_item['ID']) : 0) : intval($grant_item);
        if ($grant_id > 0 && get_post_status($grant_id) === 'publish') {
            $related_grants[] = array(
                'id' => $grant_id,
                'title' => get_the_title($grant_id),
                'permalink' => get_permalink($grant_id),
                'max_amount' => gicsc_get_field('max_amount', $grant_id),
                'deadline' => gicsc_get_field('deadline', $grant_id),
                'application_status' => gicsc_get_field('application_status', $grant_id, 'open'),
            );
        }
    }
}

// 自動で関連補助金を取得
if (count($related_grants) < 4) {
    $grant_exclude = wp_list_pluck($related_grants, 'id');
    $grant_query = new WP_Query(array(
        'post_type' => 'grant',
        'posts_per_page' => 4 - count($related_grants),
        'post_status' => 'publish',
        'post__not_in' => $grant_exclude,
        'meta_query' => array(
            array('key' => 'application_status', 'value' => 'open'),
        ),
        'orderby' => 'rand',
    ));
    if ($grant_query->have_posts()) {
        while ($grant_query->have_posts()) {
            $grant_query->the_post();
            $gid = get_the_ID();
            $related_grants[] = array(
                'id' => $gid,
                'title' => get_the_title(),
                'permalink' => get_permalink(),
                'max_amount' => gicsc_get_field('max_amount', $gid),
                'deadline' => gicsc_get_field('deadline', $gid),
                'application_status' => gicsc_get_field('application_status', $gid, 'open'),
            );
        }
        wp_reset_postdata();
    }
}

// 関連コラム取得
$related_columns = array();
if (!empty($column['related_columns'])) {
    foreach ($column['related_columns'] as $col_item) {
        $col_id = is_array($col_item) ? (isset($col_item['ID']) ? intval($col_item['ID']) : 0) : intval($col_item);
        if ($col_id > 0 && get_post_status($col_id) === 'publish') {
            $related_columns[] = array(
                'id' => $col_id,
                'title' => get_the_title($col_id),
                'permalink' => get_permalink($col_id),
                'thumbnail' => get_the_post_thumbnail_url($col_id, 'medium'),
                'date' => get_the_date('Y.m.d', $col_id),
                'read_time' => gicsc_get_field('estimated_read_time', $col_id, gicsc_calculate_reading_time(get_post_field('post_content', $col_id))),
            );
        }
    }
}

// 自動で関連コラムを取得
if (count($related_columns) < 4) {
    $col_exclude = array_merge(array($post_id), wp_list_pluck($related_columns, 'id'));
    $related_query = new WP_Query(array(
        'post_type' => array('column', 'post'),
        'posts_per_page' => 4 - count($related_columns),
        'post__not_in' => $col_exclude,
        'post_status' => 'publish',
        'orderby' => 'rand',
    ));
    if ($related_query->have_posts()) {
        while ($related_query->have_posts()) {
            $related_query->the_post();
            $related_columns[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'permalink' => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                'date' => get_the_date('Y.m.d'),
                'read_time' => gicsc_get_field('estimated_read_time', get_the_ID(), gicsc_calculate_reading_time(get_the_content())),
            );
        }
        wp_reset_postdata();
    }
}

// 人気コラム取得
$popular_columns = new WP_Query(array(
    'post_type' => array('column', 'post'),
    'posts_per_page' => 5,
    'post__not_in' => array($post_id),
    'post_status' => 'publish',
    'meta_key' => 'view_count',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
));

// 締切間近の補助金
$deadline_soon_grants = new WP_Query(array(
    'post_type' => 'grant',
    'posts_per_page' => 5,
    'post_status' => 'publish',
    'meta_query' => array(
        array('key' => 'application_status', 'value' => 'open'),
        array('key' => 'deadline_date', 'value' => date('Y-m-d'), 'compare' => '>=', 'type' => 'DATE'),
        array('key' => 'deadline_date', 'value' => date('Y-m-d', strtotime('+30 days')), 'compare' => '<=', 'type' => 'DATE'),
    ),
    'meta_key' => 'deadline_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
));

// FAQ生成
$faq_items = array();
if (!empty($column['faq_items'])) {
    foreach ($column['faq_items'] as $faq) {
        if (is_array($faq) && !empty($faq['question']) && !empty($faq['answer'])) {
            $faq_items[] = $faq;
        }
    }
}

if (count($faq_items) < 3) {
    $default_faqs = array(
        array(
            'question' => 'この記事の情報は最新ですか？',
            'answer' => 'はい、' . $last_verified_display . '時点で内容を確認・更新しています。補助金制度は変更されることがありますので、申請前に必ず公式サイトで最新情報をご確認ください。'
        ),
        array(
            'question' => '補助金の申請サポートは受けられますか？',
            'answer' => '当サイトでは補助金申請のサポートサービスを提供しています。専門家による申請書類の作成支援や、採択率を高めるためのアドバイスを受けることができます。'
        ),
        array(
            'question' => '関連する補助金を探すにはどうすればいいですか？',
            'answer' => '当サイトのAI診断機能を使えば、あなたの事業に最適な補助金を簡単に見つけることができます。また、補助金一覧ページから条件で絞り込み検索も可能です。'
        ),
    );
    foreach ($default_faqs as $dfaq) {
        if (count($faq_items) < 5) {
            $faq_items[] = $dfaq;
        }
    }
}

// 目次
$toc_items = array();
if (!empty($column['ai_summary'])) $toc_items[] = array('id' => 'summary', 'title' => 'AI要約');
$toc_items[] = array('id' => 'content', 'title' => '記事本文');
if (!empty($key_points_array)) $toc_items[] = array('id' => 'keypoints', 'title' => 'この記事のポイント');
if (!empty($related_grants)) $toc_items[] = array('id' => 'grants', 'title' => '関連する補助金');
if (!empty($faq_items)) $toc_items[] = array('id' => 'faq', 'title' => 'よくある質問');
?>

<!-- 構造化データ -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@graph": [
        {
            "@type": "BreadcrumbList",
            "itemListElement": [
                <?php foreach ($breadcrumbs as $i => $crumb): ?>
                {"@type": "ListItem", "position": <?php echo $i + 1; ?>, "name": <?php echo json_encode($crumb['name'], JSON_UNESCAPED_UNICODE); ?>, "item": "<?php echo esc_url($crumb['url']); ?>"}<?php echo $i < count($breadcrumbs) - 1 ? ',' : ''; ?>
                <?php endforeach; ?>
            ]
        },
        {
            "@type": "Article",
            "headline": <?php echo json_encode($post_title, JSON_UNESCAPED_UNICODE); ?>,
            "description": <?php echo json_encode($meta_desc, JSON_UNESCAPED_UNICODE); ?>,
            "image": "<?php echo esc_url($og_image); ?>",
            "url": "<?php echo esc_url($canonical_url); ?>",
            "datePublished": "<?php echo get_the_date('c'); ?>",
            "dateModified": "<?php echo get_the_modified_date('c'); ?>",
            "author": {
                "@type": "Organization",
                "name": <?php echo json_encode($column['supervisor_name'], JSON_UNESCAPED_UNICODE); ?>
            },
            "publisher": {
                "@type": "Organization",
                "name": "<?php echo esc_js($site_name); ?>",
                "logo": {
                    "@type": "ImageObject",
                    "url": "<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>"
                }
            },
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "<?php echo esc_url($canonical_url); ?>"
            }
        }
        <?php if (!empty($faq_items)): ?>
        ,{
            "@type": "FAQPage",
            "mainEntity": [
                <?php foreach (array_slice($faq_items, 0, 5) as $i => $faq): ?>
                {"@type": "Question", "name": <?php echo json_encode($faq['question'], JSON_UNESCAPED_UNICODE); ?>, "acceptedAnswer": {"@type": "Answer", "text": <?php echo json_encode($faq['answer'], JSON_UNESCAPED_UNICODE); ?>}}<?php echo $i < min(count($faq_items), 5) - 1 ? ',' : ''; ?>
                <?php endforeach; ?>
            ]
        }
        <?php endif; ?>
    ]
}
</script>

<div class="gic-progress" id="progressBar"></div>

<!-- 📚 図鑑インデックスタブ（左端）- モバイル・PC共通でサイドバー目次に統一のため削除 -->

<!-- 📚 本・図鑑風パンくずリスト -->
<nav class="gic-breadcrumb gic-book-breadcrumb" aria-label="パンくずリスト">
    <div class="gic-breadcrumb-book-spine"></div>
    <div class="gic-breadcrumb-inner">
        <div class="gic-breadcrumb-book-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                <path d="M8 7h8M8 11h5"/>
            </svg>
        </div>
        <ol class="gic-breadcrumb-list">
            <?php foreach ($breadcrumbs as $i => $crumb): ?>
            <li class="gic-breadcrumb-item">
                <?php if ($i < count($breadcrumbs) - 1): ?>
                <a href="<?php echo esc_url($crumb['url']); ?>" class="gic-breadcrumb-link">
                    <span class="gic-breadcrumb-chapter">第<?php echo $i + 1; ?>章</span>
                    <span class="gic-breadcrumb-text"><?php echo esc_html($crumb['name']); ?></span>
                </a>
                <span class="gic-breadcrumb-sep" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </span>
                <?php else: ?>
                <span class="gic-breadcrumb-current">
                    <span class="gic-breadcrumb-chapter">本ページ</span>
                    <span class="gic-breadcrumb-text"><?php echo esc_html($crumb['name']); ?></span>
                </span>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ol>
        <div class="gic-breadcrumb-page-num">P.<?php echo str_pad($post_id, 3, '0', STR_PAD_LEFT); ?></div>
    </div>
</nav>

<div class="gic-page">
    <div class="gic-container">
        
        <!-- 📚 図鑑風ヘッダー（コラム用） -->
        <div class="gic-zukan-header">
            <div class="gic-zukan-book-icon">
                <!-- 本のSVGアイコン（コラム用） -->
                <svg viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="6" y="4" width="40" height="48" rx="2" fill="#1b263b" stroke="#c9a227" stroke-width="2"/>
                    <rect x="10" y="4" width="36" height="48" rx="2" fill="#0d1b2a"/>
                    <path d="M14 10h24M14 16h24M14 22h16" stroke="#c9a227" stroke-width="1.5" stroke-linecap="round" opacity="0.6"/>
                    <rect x="46" y="4" width="4" height="48" fill="#c9a227"/>
                    <path d="M48 8v40" stroke="#b8941f" stroke-width="1"/>
                    <path d="M14 34h24M14 40h18" stroke="rgba(255,255,255,0.3)" stroke-width="1" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="gic-zukan-header-content">
                <div class="gic-zukan-category-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    補助金図鑑 コラム
                </div>
                <div class="gic-zukan-entry-number">COLUMN No.<?php echo str_pad($post_id, 5, '0', STR_PAD_LEFT); ?></div>
                <p class="gic-zukan-title">
                    <?php 
                    if (!empty($categories[0])) {
                        echo esc_html($categories[0]->name);
                    } else {
                        echo 'お役立ち情報';
                    }
                    ?>
                </p>
            </div>
            <div class="gic-bookmark-ribbon"></div>
        </div>

        <!-- ヒーロー -->
        <header class="gic-hero">
            <div class="gic-hero-badges">
                <?php if (!empty($categories)): ?>
                    <?php foreach (array_slice($categories, 0, 2) as $cat): 
                        $cat_link = get_term_link($cat);
                        if (!is_wp_error($cat_link)):
                    ?>
                    <a href="<?php echo esc_url($cat_link); ?>" class="gic-badge gic-badge-category"><?php echo esc_html($cat->name); ?></a>
                    <?php endif; endforeach; ?>
                <?php endif; ?>
                <span class="gic-badge gic-badge-<?php echo esc_attr($difficulty['class']); ?>"><?php echo esc_html($difficulty['label']); ?></span>
                <?php if ($column['is_featured']): ?><span class="gic-badge gic-badge-featured">注目</span><?php endif; ?>
                <?php if ($column['is_new']): ?><span class="gic-badge gic-badge-recent">新着</span><?php endif; ?>
                <?php if ($freshness_class): ?><span class="gic-badge gic-badge-<?php echo $freshness_class; ?>"><?php echo $freshness_label; ?></span><?php endif; ?>
            </div>
            <h1 class="gic-hero-title"><?php echo esc_html($post_title); ?></h1>
            <div class="gic-hero-meta">
                <span class="gic-hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('Y年n月j日'); ?></time>
                </span>
                <span class="gic-hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    約<?php echo intval($column['read_time']); ?>分で読めます
                </span>
                <span class="gic-hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <?php echo number_format($column['view_count']); ?>回閲覧
                </span>
                <span class="gic-hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                    <?php echo esc_html($last_verified_display); ?>確認
                </span>
            </div>
        </header>

        <!-- 📚 図鑑エントリーバッジ -->
        <div class="gic-entry-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            補助金図鑑 コラム #<?php echo str_pad($post_id, 5, '0', STR_PAD_LEFT); ?>
        </div>

        <!-- メトリクス -->
        <section class="gic-metrics" aria-label="記事情報">
            <div class="gic-metric">
                <div class="gic-metric-label">読了時間</div>
                <div class="gic-metric-value highlight">約<?php echo intval($column['read_time']); ?>分</div>
                <div class="gic-metric-sub"><?php echo number_format(mb_strlen(strip_tags($post_content), 'UTF-8')); ?>文字</div>
            </div>
            <div class="gic-metric">
                <div class="gic-metric-label">難易度</div>
                <div class="gic-metric-value"><?php echo esc_html($difficulty['label']); ?></div>
                <div class="gic-metric-stars">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                    <svg class="gic-metric-star <?php echo $i <= $difficulty['level'] ? 'active' : ''; ?>" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="gic-metric">
                <div class="gic-metric-label">閲覧数</div>
                <div class="gic-metric-value"><?php echo number_format($column['view_count']); ?></div>
                <div class="gic-metric-sub">累計</div>
            </div>
            <div class="gic-metric">
                <div class="gic-metric-label">関連補助金</div>
                <div class="gic-metric-value"><?php echo count($related_grants); ?>件</div>
                <div class="gic-metric-sub">募集中含む</div>
            </div>
        </section>

        <div class="gic-layout">
            <main class="gic-main" id="main-content">
                
                <!-- AI要約 -->
                <?php if ($column['ai_summary']): ?>
                <section class="gic-summary" id="summary" aria-labelledby="summary-title">
                    <div class="gic-summary-header">
                        <div class="gic-summary-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                        </div>
                        <span class="gic-summary-label" id="summary-title">AI要約</span>
                        <span class="gic-summary-badge">30秒で理解</span>
                    </div>
                    <p class="gic-summary-text"><?php echo nl2br(esc_html($column['ai_summary'])); ?></p>
                </section>
                <?php endif; ?>

                <!-- 対象読者 -->
                <?php if (!empty($column['target_audience'])): ?>
                <aside class="gic-audience">
                    <h2 class="gic-audience-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        この記事はこんな方におすすめ
                    </h2>
                    <ul class="gic-audience-list">
                        <?php foreach ($column['target_audience'] as $audience): 
                            if (!isset($audience_labels[$audience])) continue;
                            $aud = $audience_labels[$audience];
                        ?>
                        <li class="gic-audience-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                            <?php echo esc_html($aud['label']); ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </aside>
                <?php endif; ?>

                <!-- アイキャッチ -->
                <?php if (has_post_thumbnail()): ?>
                <figure class="gic-thumbnail">
                    <?php the_post_thumbnail('large', array('alt' => esc_attr($post_title), 'loading' => 'eager')); ?>
                </figure>
                <?php endif; ?>

                <!-- 記事本文 -->
                <article class="gic-section gic-book-style" id="content" aria-labelledby="content-title">
                    <header class="gic-section-header">
                        <!-- 📚 章番号 -->
                        <div class="gic-chapter-number">
                            <span class="gic-chapter-number-circle">1</span>
                            <span class="gic-chapter-number-text">CHAPTER ONE</span>
                        </div>
                    </header>
                    <header class="gic-section-header" style="border-bottom: none; padding-bottom: 0;">
                        <svg class="gic-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <h2 class="gic-section-title" id="content-title">記事本文</h2>
                        <span class="gic-section-en">Content</span>
                    </header>
                    
                    <!-- 📚 図鑑風注釈 -->
                    <div class="gic-dict-note">
                        <span class="gic-dict-note-text">この記事は補助金・助成金に関する知識を深めるためのコラムです。実際の申請時は必ず公式情報をご確認ください。</span>
                    </div>
                    
                    <div class="gic-content">
                        <?php echo apply_filters('the_content', $post_content); ?>
                        
                        <!-- 広告枠（記事直下） - アフィリエイト/自社CTA推奨：高成約率ポイント -->
                        <?php if (function_exists('ji_display_ad')): ?>
                        <div class="gic-ad-content-bottom" style="margin-top: 40px;">
                            <?php ji_display_ad('single_column_content_bottom'); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </article>

                <!-- 📚 セクション区切り -->
                <div class="gic-encyclopedia-divider">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </div>

                <!-- ポイントまとめ -->
                <?php if (!empty($key_points_array)): ?>
                <section class="gic-keypoints" id="keypoints" aria-labelledby="keypoints-title">
                    <h2 class="gic-keypoints-title" id="keypoints-title">
                        <svg viewBox="0 0 24 24" fill="#c9a227" stroke="#0d1b2a" stroke-width="1.5">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        <span>この記事のポイント</span>
                    </h2>
                    <ul class="gic-keypoints-list">
                        <?php foreach ($key_points_array as $i => $point_text): ?>
                        <li class="gic-keypoints-item">
                            <span class="gic-keypoints-num"><?php echo ($i + 1); ?></span>
                            <span class="gic-keypoints-text"><?php echo esc_html($point_text); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <?php endif; ?>

                <!-- 📚 本・図鑑風CTA -->
                <section class="gic-cta gic-book-cta">
                    <div class="gic-cta-book-spine"></div>
                    <div class="gic-cta-book-corner gic-cta-book-corner-tl"></div>
                    <div class="gic-cta-book-corner gic-cta-book-corner-tr"></div>
                    <div class="gic-cta-book-corner gic-cta-book-corner-bl"></div>
                    <div class="gic-cta-book-corner gic-cta-book-corner-br"></div>
                    <div class="gic-cta-inner">
                        <div class="gic-cta-book-header">
                            <div class="gic-cta-chapter">📖 次のステップへ</div>
                            <div class="gic-cta-book-ribbon">おすすめ</div>
                        </div>
                        <div class="gic-cta-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                        </div>
                        <h2 class="gic-cta-title">あなたに合う補助金を今すぐ見つけましょう</h2>
                        <p class="gic-cta-desc">AI診断で最適な補助金を提案。補助金図鑑であなたのビジネスに最適な支援制度を見つけましょう。</p>
                        <div class="gic-cta-buttons">
                            <a href="<?php echo esc_url(home_url('/subsidy-diagnosis/')); ?>" class="gic-cta-btn gic-cta-btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 11l3 3L22 4"/>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                </svg>
                                <span>AIで診断する</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/grants/')); ?>" class="gic-cta-btn gic-cta-btn-secondary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.35-4.35"/>
                                </svg>
                                <span>一覧から探す</span>
                            </a>
                        </div>
                        <div class="gic-cta-book-footer">
                            <span class="gic-cta-page-num">補助金図鑑</span>
                        </div>
                    </div>
                </section>

                <!-- 関連補助金 -->
                <?php if (!empty($related_grants)): ?>
                <section class="gic-section gic-book-style" id="grants" aria-labelledby="grants-title">
                    <header class="gic-section-header">
                        <!-- 📚 章番号 -->
                        <div class="gic-chapter-number">
                            <span class="gic-chapter-number-circle">2</span>
                            <span class="gic-chapter-number-text">CHAPTER TWO</span>
                        </div>
                    </header>
                    <header class="gic-section-header" style="border-bottom: none; padding-bottom: 0;">
                        <svg class="gic-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        <h2 class="gic-section-title" id="grants-title">関連する補助金</h2>
                        <span class="gic-section-en">Related Grants</span>
                    </header>
                    <div class="gic-grants-grid">
                        <?php foreach ($related_grants as $grant): ?>
                        <a href="<?php echo esc_url($grant['permalink']); ?>" class="gic-grant-card">
                            <span class="gic-grant-badge <?php echo $grant['application_status'] === 'open' ? 'open' : 'closed'; ?>">
                                <?php echo $grant['application_status'] === 'open' ? '募集中' : '募集終了'; ?>
                            </span>
                            <h3 class="gic-grant-title"><?php echo esc_html($grant['title']); ?></h3>
                            <div class="gic-grant-meta">
                                <?php if ($grant['max_amount']): ?><span><strong><?php echo esc_html($grant['max_amount']); ?></strong></span><?php endif; ?>
                                <?php if ($grant['deadline']): ?><span><?php echo esc_html($grant['deadline']); ?></span><?php endif; ?>
                            </div>
                            <span class="gic-grant-link">詳細を見る →</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- FAQ -->
                <?php if (!empty($faq_items)): ?>
                <section class="gic-section gic-book-style" id="faq" aria-labelledby="faq-title">
                    <header class="gic-section-header">
                        <!-- 📚 章番号 -->
                        <div class="gic-chapter-number">
                            <span class="gic-chapter-number-circle">3</span>
                            <span class="gic-chapter-number-text">CHAPTER THREE</span>
                        </div>
                    </header>
                    <header class="gic-section-header" style="border-bottom: none; padding-bottom: 0;">
                        <svg class="gic-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <h2 class="gic-section-title" id="faq-title">よくある質問</h2>
                        <span class="gic-section-en">FAQ</span>
                    </header>
                    <div class="gic-faq-list">
                        <?php foreach ($faq_items as $faq): ?>
                        <details class="gic-faq-item">
                            <summary class="gic-faq-question">
                                <span class="gic-faq-q-mark">Q</span>
                                <span class="gic-faq-question-text"><?php echo esc_html($faq['question']); ?></span>
                                <svg class="gic-faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </summary>
                            <div class="gic-faq-answer"><?php echo nl2br(esc_html($faq['answer'])); ?></div>
                        </details>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- タグ -->
                <?php if (!empty($tags)): ?>
                <nav class="gic-tags" aria-label="タグ">
                    <?php foreach ($tags as $tag): 
                        $tag_link = get_term_link($tag);
                        if (is_wp_error($tag_link)) continue;
                    ?>
                    <a href="<?php echo esc_url($tag_link); ?>" class="gic-tag">#<?php echo esc_html($tag->name); ?></a>
                    <?php endforeach; ?>
                </nav>
                <?php endif; ?>

                <!-- シェア -->
                <aside class="gic-share">
                    <h2 class="gic-share-title">この記事をシェア</h2>
                    <div class="gic-share-buttons">
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($canonical_url); ?>&text=<?php echo urlencode($post_title); ?>" target="_blank" rel="noopener noreferrer" class="gic-share-btn">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            X
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($canonical_url); ?>" target="_blank" rel="noopener noreferrer" class="gic-share-btn">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Facebook
                        </a>
                        <a href="https://social-plugins.line.me/lineit/share?url=<?php echo urlencode($canonical_url); ?>" target="_blank" rel="noopener noreferrer" class="gic-share-btn">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/></svg>
                            LINE
                        </a>
                    </div>
                </aside>

                <!-- 📚 図鑑風フッター -->
                <div class="gic-zukan-footer">
                    <div class="gic-zukan-footer-page">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        補助金図鑑 コラム #<?php echo str_pad($post_id, 5, '0', STR_PAD_LEFT); ?>
                    </div>
                    <div class="gic-book-mark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                        <?php echo date('Y年版'); ?>
                    </div>
                </div>

                <!-- 情報ソース -->
                <div class="gic-source-card">
                    <div class="gic-source-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span class="gic-source-label">情報ソース</span>
                    </div>
                    <div class="gic-source-body">
                        <div class="gic-source-info">
                            <div class="gic-source-name"><?php echo esc_html($column['source_name'] ?: '補助金図鑑編集部'); ?></div>
                            <div class="gic-source-verified">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <?php echo esc_html($last_verified_display); ?> 確認済み
                            </div>
                        </div>
                        <?php if ($column['source_url']): ?>
                        <a href="<?php echo esc_url($column['source_url']); ?>" class="gic-source-link" target="_blank" rel="noopener noreferrer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            公式ページを確認
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="gic-source-footer">※最新情報は必ず公式サイトでご確認ください。本ページの情報は参考情報です。</div>
                </div>

                <!-- 監修者 -->
                <aside class="gic-supervisor">
                    <div class="gic-supervisor-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        監修・編集
                    </div>
                    <div class="gic-supervisor-content">
                        <div class="gic-supervisor-avatar">
                            <?php if (!empty($column['supervisor_image']) && isset($column['supervisor_image']['url'])): ?>
                            <img src="<?php echo esc_url($column['supervisor_image']['url']); ?>" alt="<?php echo esc_attr($column['supervisor_name']); ?>" loading="lazy" decoding="async" width="72" height="72">
                            <?php else: ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <?php endif; ?>
                        </div>
                        <div class="gic-supervisor-info">
                            <div class="gic-supervisor-name"><?php echo esc_html($column['supervisor_name']); ?></div>
                            <div class="gic-supervisor-title"><?php echo esc_html($column['supervisor_title']); ?></div>
                            <p class="gic-supervisor-bio"><?php echo esc_html($column['supervisor_profile']); ?></p>
                            <?php if (!empty($column['supervisor_credentials'])): ?>
                            <div class="gic-supervisor-credentials">
                                <?php foreach ($column['supervisor_credentials'] as $cred): 
                                    if (!is_array($cred) || empty($cred['credential'])) continue;
                                ?>
                                <span class="gic-supervisor-credential"><?php echo esc_html($cred['credential']); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($column['supervisor_url'])): ?>
                            <div style="margin-top:12px;"><a href="<?php echo esc_url($column['supervisor_url']); ?>" target="_blank" rel="noopener" style="text-decoration:underline;font-size:14px;font-weight:600;">監修者プロフィールを見る →</a></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </aside>

            </main>

            <!-- サイドバー -->
            <aside class="gic-sidebar">
                
                <!-- 広告枠（サイドバー上部） - AdSense推奨：ファーストビュー高CPC -->
                <?php if (function_exists('ji_display_ad')): ?>
                <section class="gic-sidebar-section gic-ad-section" aria-label="広告">
                    <div class="gic-ad-slot">
                        <?php ji_display_ad('single_column_sidebar_top'); ?>
                    </div>
                </section>
                <?php endif; ?>
                
                <!-- AIアシスタント -->
                <section class="gic-sidebar-section gic-ai-section" aria-labelledby="ai-title">
                    <header class="gic-sidebar-header">
                        <h3 class="gic-sidebar-title" id="ai-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            AIアシスタント
                        </h3>
                    </header>
                    <div class="gic-ai-body">
                        <div class="gic-ai-messages" id="aiMessages" aria-live="polite">
                            <div class="gic-ai-msg">
                                <div class="gic-ai-avatar">AI</div>
                                <div class="gic-ai-bubble">この記事について何でもお聞きください。補助金のこと、申請方法など、お気軽にどうぞ。</div>
                            </div>
                        </div>
                        <div class="gic-ai-input-area">
                            <div class="gic-ai-input-wrap">
                                <textarea class="gic-ai-input" id="aiInput" placeholder="質問を入力..." rows="1" aria-label="AIへの質問"></textarea>
                                <button class="gic-ai-send" id="aiSend" type="button" aria-label="送信">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                </button>
                            </div>
                            <div class="gic-ai-suggestions">
                                <button class="gic-ai-chip" data-action="diagnosis">補助金診断</button>
                                <button class="gic-ai-chip" data-q="この記事のポイントは？">ポイント</button>
                                <button class="gic-ai-chip" data-q="関連する補助金は？">補助金</button>
                                <button class="gic-ai-chip" data-q="申請方法を教えて">申請方法</button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 目次 -->
                <section class="gic-sidebar-section">
                    <header class="gic-sidebar-header">
                        <h3 class="gic-sidebar-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                            目次
                        </h3>
                    </header>
                    <div class="gic-sidebar-body">
                        <nav class="gic-toc-nav" id="tocNav">
                            <ul>
                                <?php foreach ($toc_items as $item): ?>
                                <li><a href="#<?php echo esc_attr($item['id']); ?>"><?php echo esc_html($item['title']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </nav>
                    </div>
                </section>

                <!-- CTAボタン -->
                <section class="gic-sidebar-section">
                    <div class="gic-sidebar-body" style="display: flex; flex-direction: column; gap: 12px;">
                        <a href="<?php echo esc_url(home_url('/subsidy-diagnosis/')); ?>" class="gic-cta-btn gic-cta-btn-primary" style="justify-content: center;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            AIで診断する
                        </a>
                        <a href="<?php echo esc_url(home_url('/grants/')); ?>" class="gic-cta-btn gic-cta-btn-secondary" style="justify-content: center; background: var(--gic-white); color: var(--gic-gov-navy-900);">
                            補助金一覧を見る
                        </a>
                        <button class="gic-cta-btn gic-cta-btn-secondary" id="bookmarkBtn" style="justify-content: center; background: var(--gic-white); color: var(--gic-gov-navy-900);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                            <span>保存する</span>
                        </button>
                    </div>
                </section>

                <!-- 締切間近の補助金 -->
                <?php if ($deadline_soon_grants->have_posts()): ?>
                <section class="gic-sidebar-section" aria-labelledby="deadline-soon-title">
                    <header class="gic-sidebar-header">
                        <h3 class="gic-sidebar-title" id="deadline-soon-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            締切間近の補助金
                        </h3>
                    </header>
                    <div class="gic-sidebar-body">
                        <div class="gic-popular-list">
                            <?php while ($deadline_soon_grants->have_posts()): $deadline_soon_grants->the_post();
                                $item_deadline = gicsc_get_field('deadline_date', get_the_ID());
                                $item_days = $item_deadline ? floor((strtotime($item_deadline) - current_time('timestamp')) / 86400) : 0;
                                $item_status = $item_days <= 7 ? 'rank-1' : ($item_days <= 14 ? 'rank-2' : 'rank-3');
                            ?>
                            <div class="gic-popular-item">
                                <a href="<?php the_permalink(); ?>" class="gic-popular-link">
                                    <span class="gic-popular-rank <?php echo $item_status; ?>"><?php echo $item_days; ?>日</span>
                                    <div class="gic-popular-content">
                                        <div class="gic-popular-title"><?php the_title(); ?></div>
                                        <div class="gic-popular-views"><?php echo date('n/j', strtotime($item_deadline)); ?>締切</div>
                                    </div>
                                </a>
                            </div>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- 人気コラムランキング -->
                <?php if ($popular_columns->have_posts()): ?>
                <section class="gic-sidebar-section" aria-labelledby="popular-title">
                    <header class="gic-sidebar-header">
                        <h3 class="gic-sidebar-title" id="popular-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            人気のコラム
                        </h3>
                    </header>
                    <div class="gic-sidebar-body">
                        <div class="gic-popular-list">
                            <?php $rank = 1; while ($popular_columns->have_posts()): $popular_columns->the_post();
                                $rank_class = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : ''));
                            ?>
                            <div class="gic-popular-item">
                                <a href="<?php the_permalink(); ?>" class="gic-popular-link">
                                    <span class="gic-popular-rank <?php echo $rank_class; ?>"><?php echo $rank; ?></span>
                                    <div class="gic-popular-content">
                                        <div class="gic-popular-title"><?php the_title(); ?></div>
                                        <div class="gic-popular-views"><?php echo number_format(intval(gicsc_get_field('view_count', get_the_ID(), 0))); ?>回閲覧</div>
                                    </div>
                                </a>
                            </div>
                            <?php $rank++; endwhile; wp_reset_postdata(); ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- 関連カテゴリ -->
                <?php if (!empty($categories)): ?>
                <section class="gic-sidebar-section">
                    <header class="gic-sidebar-header">
                        <h3 class="gic-sidebar-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            関連カテゴリ
                        </h3>
                    </header>
                    <div class="gic-sidebar-body">
                        <div class="gic-tags" style="flex-direction: column; gap: 8px;">
                            <?php foreach (array_slice($categories, 0, 5) as $cat): 
                                $cat_link = get_term_link($cat);
                                if (is_wp_error($cat_link)) continue;
                            ?>
                            <a href="<?php echo esc_url($cat_link); ?>" class="gic-tag" style="justify-content: center; width: 100%;"><?php echo esc_html($cat->name); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- 広告枠（サイドバー追尾） - AdSenseマルチプレックス推奨 -->
                <?php if (function_exists('ji_display_ad')): ?>
                <div class="gic-sidebar-sticky" style="position: sticky; top: 100px;">
                    <section class="gic-sidebar-section gic-ad-section" aria-label="広告">
                        <div class="gic-ad-slot">
                            <?php ji_display_ad('single_column_sidebar_bottom'); ?>
                        </div>
                    </section>
                </div>
                <?php endif; ?>

            </aside>
        </div>
    </div>

    <!-- 関連コラム -->
    <?php if (!empty($related_columns)): ?>
    <section class="gic-related" aria-labelledby="related-title">
        <div class="gic-container">
            <header class="gic-related-header">
                <p class="gic-related-en">Related Columns</p>
                <h2 class="gic-related-title" id="related-title">関連するコラム</h2>
            </header>
            <div class="gic-related-grid">
                <?php foreach ($related_columns as $rel): ?>
                <a href="<?php echo esc_url($rel['permalink']); ?>" class="gic-related-card">
                    <div class="gic-related-card-thumb">
                        <?php if ($rel['thumbnail']): ?>
                        <img src="<?php echo esc_url($rel['thumbnail']); ?>" alt="<?php echo esc_attr($rel['title']); ?>" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <div class="gic-related-card-body">
                        <h3 class="gic-related-card-title"><?php echo esc_html($rel['title']); ?></h3>
                        <div class="gic-related-card-meta">
                            <span><?php echo esc_html($rel['date']); ?></span>
                            <span>約<?php echo intval($rel['read_time']); ?>分</span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<!-- モバイルFAB -->
<div class="gic-mobile-fab">
    <button class="gic-fab-btn" id="mobileAiBtn" type="button" aria-label="AIアシスタントを開く">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span>AI相談</span>
    </button>
</div>

<!-- モバイルパネル -->
<div class="gic-mobile-overlay" id="mobileOverlay"></div>
<div class="gic-mobile-panel" id="mobilePanel">
    <div class="gic-panel-handle"></div>
    <header class="gic-panel-header">
        <h2 class="gic-panel-title">AIアシスタント</h2>
        <button class="gic-panel-close" id="panelClose" type="button" aria-label="閉じる">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </header>
    <div class="gic-panel-tabs">
        <button class="gic-panel-tab active" data-tab="ai">AI質問</button>
        <button class="gic-panel-tab" data-tab="toc">目次</button>
        <button class="gic-panel-tab" data-tab="action">アクション</button>
    </div>
    <div class="gic-panel-content">
        <div class="gic-panel-content-tab active" id="tabAi">
            <div class="gic-ai-messages" id="mobileAiMessages" style="min-height: 180px; max-height: 240px; margin-bottom: 16px; padding: 16px; background: var(--gic-gray-50); border: 1px solid var(--gic-gray-200); border-radius: var(--gic-radius-lg);">
                <div class="gic-ai-msg">
                    <div class="gic-ai-avatar">AI</div>
                    <div class="gic-ai-bubble">この記事について何でもお聞きください。</div>
                </div>
            </div>
            <div class="gic-ai-input-wrap">
                <textarea class="gic-ai-input" id="mobileAiInput" placeholder="質問を入力..." rows="2" aria-label="AIへの質問"></textarea>
                <button class="gic-ai-send" id="mobileAiSend" type="button" aria-label="送信">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
            <div class="gic-ai-suggestions" style="margin-top: 12px;">
                <button class="gic-ai-chip" data-action="diagnosis">補助金診断</button>
                <button class="gic-ai-chip" data-q="この記事のポイントは？">ポイント</button>
                <button class="gic-ai-chip" data-q="関連する補助金は？">補助金</button>
            </div>
        </div>
        <div class="gic-panel-content-tab" id="tabToc">
            <nav class="gic-toc-nav" id="mobileTocNav">
                <ul>
                    <?php foreach ($toc_items as $item): ?>
                    <li><a href="#<?php echo esc_attr($item['id']); ?>" class="mobile-toc-link"><?php echo esc_html($item['title']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
        <div class="gic-panel-content-tab" id="tabAction">
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <a href="<?php echo esc_url(home_url('/subsidy-diagnosis/')); ?>" class="gic-cta-btn gic-cta-btn-primary" style="justify-content: center;">AIで補助金診断</a>
                <a href="<?php echo esc_url(home_url('/grants/')); ?>" class="gic-cta-btn gic-cta-btn-secondary" style="justify-content: center; background: var(--gic-white); border-color: var(--gic-gray-300); color: var(--gic-gov-navy-900);">補助金一覧を見る</a>
                <button class="gic-cta-btn gic-cta-btn-secondary" id="mobileBookmarkBtn" style="justify-content: center; background: var(--gic-white); border-color: var(--gic-gray-300); color: var(--gic-gov-navy-900);">
                    <span>保存する</span>
                </button>
                <button class="gic-cta-btn gic-cta-btn-secondary" id="mobileShareBtn" style="justify-content: center; background: var(--gic-white); border-color: var(--gic-gray-300); color: var(--gic-gov-navy-900);">シェアする</button>
            </div>
        </div>
    </div>
</div>

<div class="gic-toast" id="gicToast"></div>

<script>
var CONFIG = {
    postId: <?php echo $post_id; ?>,
    ajaxUrl: '<?php echo admin_url("admin-ajax.php"); ?>',
    nonce: '<?php echo wp_create_nonce("gic_ai_nonce"); ?>',
    url: '<?php echo esc_js($canonical_url); ?>',
    title: <?php echo json_encode($post_title, JSON_UNESCAPED_UNICODE); ?>
};
</script>

<?php get_footer(); ?>
