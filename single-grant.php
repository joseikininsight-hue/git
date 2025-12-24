<?php
/**
 * Grant Single Page - Ultimate Edition v500
 * 補助金図鑑 - 本・辞典スタイル + 統合セクションデザイン
 * 
 * @package Grant_Insight_Ultimate
 * @version 500.0.0
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

// ===================================
// ヘルパー関数定義（single-grant専用プレフィックス）
// ===================================

/**
 * ACFフィールド取得
 */
function gisg_get_field($field_name, $pid, $default = '') {
    $value = function_exists('get_field') ? get_field($field_name, $pid) : get_post_meta($pid, $field_name, true);
    return ($value !== null && $value !== false && $value !== '') ? $value : $default;
}

/**
 * ACF配列フィールド取得
 */
function gisg_get_field_array($field_name, $pid) {
    $value = function_exists('get_field') ? get_field($field_name, $pid) : get_post_meta($pid, $field_name, true);
    return is_array($value) ? $value : array();
}

/**
 * タクソノミー取得
 */
function gisg_get_terms($pid, $taxonomy) {
    if (!taxonomy_exists($taxonomy)) return array();
    $terms = wp_get_post_terms($pid, $taxonomy);
    return (!is_wp_error($terms) && !empty($terms)) ? $terms : array();
}

/**
 * 金額フォーマット
 */
function gisg_format_amount($amount) {
    if (!is_numeric($amount) || $amount <= 0) return '';
    $amount = intval($amount);
    if ($amount >= 100000000) return number_format($amount / 100000000, 1) . '億円';
    if ($amount >= 10000) return number_format($amount / 10000) . '万円';
    return number_format($amount) . '円';
}

/**
 * 補助金カードデータ取得
 */
function gisg_get_grant_card($pid) {
    $rate = gisg_get_field('subsidy_rate_detailed', $pid);
    if (!$rate) {
        $rate = gisg_get_field('subsidy_rate', $pid);
    }
    if (!$rate) {
        $max = floatval(gisg_get_field('subsidy_rate_max', $pid, 0));
        $min = floatval(gisg_get_field('subsidy_rate_min', $pid, 0));
        if ($max > 0) {
            $rate = ($min > 0 && $min != $max) ? $min . '%〜' . $max . '%' : $max . '%';
        }
    }
    if (!$rate) {
        $rate = gisg_get_field('subsidy_rate_limit', $pid);
    }

    return array(
        'id' => $pid,
        'title' => get_the_title($pid),
        'permalink' => get_permalink($pid),
        'organization' => gisg_get_field('organization', $pid),
        'max_amount' => gisg_get_field('max_amount', $pid),
        'max_amount_numeric' => intval(gisg_get_field('max_amount_numeric', $pid, 0)),
        'subsidy_rate' => $rate,
        'subsidy_rate_max' => floatval(gisg_get_field('subsidy_rate_max', $pid, 0)),
        'deadline' => gisg_get_field('deadline', $pid),
        'deadline_date' => gisg_get_field('deadline_date', $pid),
        'grant_difficulty' => gisg_get_field('grant_difficulty', $pid, 'normal'),
        'adoption_rate' => floatval(gisg_get_field('adoption_rate', $pid, 0)),
        'online_application' => (bool)gisg_get_field('online_application', $pid, false),
        'jgrants_available' => (bool)gisg_get_field('jgrants_available', $pid, false),
        'preparation_days' => intval(gisg_get_field('preparation_days', $pid, 14)),
        'application_status' => gisg_get_field('application_status', $pid, 'open'),
    );
}

/**
 * 類似補助金取得
 */
function gisg_get_similar($current_id, $tax_data, $manual_ids = array()) {
    $similar = array();
    $exclude = array($current_id);
    
    // 手動設定の類似補助金
    if (!empty($manual_ids) && is_array($manual_ids)) {
        foreach ($manual_ids as $item) {
            $id = 0;
            if (is_array($item)) {
                $id = isset($item['ID']) ? intval($item['ID']) : (isset($item['id']) ? intval($item['id']) : 0);
            } else {
                $id = intval($item);
            }
            
            if ($id > 0 && get_post_status($id) === 'publish' && !in_array($id, $exclude)) {
                $similar[$id] = gisg_get_grant_card($id);
                $exclude[] = $id;
            }
        }
    }
    
    // 自動取得
    if (count($similar) < 4) {
        $args = array(
            'post_type' => 'grant',
            'posts_per_page' => 10,
            'post__not_in' => $exclude,
            'post_status' => 'publish',
            'meta_query' => array(
                array('key' => 'application_status', 'value' => 'open'),
            ),
        );
        
        if (!empty($tax_data['categories']) && is_array($tax_data['categories'])) {
            $cat_ids = array();
            foreach ($tax_data['categories'] as $cat) {
                if (is_object($cat) && isset($cat->term_id)) {
                    $cat_ids[] = $cat->term_id;
                }
            }
            if (!empty($cat_ids)) {
                $args['tax_query'] = array(
                    array('taxonomy' => 'grant_category', 'field' => 'term_id', 'terms' => $cat_ids),
                );
            }
        }
        
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts() && count($similar) < 4) {
                $query->the_post();
                $qid = get_the_ID();
                if (!isset($similar[$qid])) {
                    $similar[$qid] = gisg_get_grant_card($qid);
                }
            }
            wp_reset_postdata();
        }
    }
    
    return array_slice($similar, 0, 4, true);
}

// ===================================
// ACFデータ取得
// ===================================
$grant = array(
    'organization' => gisg_get_field('organization', $post_id),
    'organization_type' => gisg_get_field('organization_type', $post_id),
    'grant_number' => gisg_get_field('grant_number', $post_id),
    'max_amount' => gisg_get_field('max_amount', $post_id),
    'max_amount_numeric' => intval(gisg_get_field('max_amount_numeric', $post_id, 0)),
    'min_amount_numeric' => intval(gisg_get_field('min_amount_numeric', $post_id, 0)),
    'subsidy_rate' => gisg_get_field('subsidy_rate', $post_id),
    'subsidy_rate_detailed' => gisg_get_field('subsidy_rate_detailed', $post_id),
    'subsidy_rate_max' => floatval(gisg_get_field('subsidy_rate_max', $post_id, 0)),
    'subsidy_rate_min' => floatval(gisg_get_field('subsidy_rate_min', $post_id, 0)),
    'deadline' => gisg_get_field('deadline', $post_id),
    'deadline_date' => gisg_get_field('deadline_date', $post_id),
    'start_date' => gisg_get_field('start_date', $post_id),
    'application_period' => gisg_get_field('application_period', $post_id),
    'next_deadline' => gisg_get_field('next_deadline', $post_id),
    'grant_target' => gisg_get_field('grant_target', $post_id),
    'target_company_size' => gisg_get_field_array('target_company_size', $post_id),
    'target_employee_count' => gisg_get_field('target_employee_count', $post_id),
    'target_capital' => gisg_get_field('target_capital', $post_id),
    'target_years_in_business' => gisg_get_field('target_years_in_business', $post_id),
    'area_notes' => gisg_get_field('area_notes', $post_id),
    'regional_limitation' => gisg_get_field('regional_limitation', $post_id),
    'contact_info' => gisg_get_field('contact_info', $post_id),
    'contact_phone' => gisg_get_field('contact_phone', $post_id),
    'contact_email' => gisg_get_field('contact_email', $post_id),
    'contact_hours' => gisg_get_field('contact_hours', $post_id),
    'official_url' => gisg_get_field('official_url', $post_id),
    'application_url' => gisg_get_field('application_url', $post_id),
    'guideline_url' => gisg_get_field('guideline_url', $post_id),
    'application_status' => gisg_get_field('application_status', $post_id, 'open'),
    'required_documents' => gisg_get_field('required_documents', $post_id),
    'required_documents_detailed' => gisg_get_field('required_documents_detailed', $post_id),
    'required_documents_list' => gisg_get_field_array('required_documents_list', $post_id),
    'eligible_expenses' => gisg_get_field('eligible_expenses', $post_id),
    'eligible_expenses_detailed' => gisg_get_field('eligible_expenses_detailed', $post_id),
    'eligible_expenses_list' => gisg_get_field_array('eligible_expenses_list', $post_id),
    'ineligible_expenses' => gisg_get_field('ineligible_expenses', $post_id),
    'ineligible_expenses_list' => gisg_get_field_array('ineligible_expenses_list', $post_id),
    'adoption_rate' => floatval(gisg_get_field('adoption_rate', $post_id, 0)),
    'adoption_count' => intval(gisg_get_field('adoption_count', $post_id, 0)),
    'application_count' => intval(gisg_get_field('application_count', $post_id, 0)),
    'budget_total' => intval(gisg_get_field('budget_total', $post_id, 0)),
    'budget_remaining' => intval(gisg_get_field('budget_remaining', $post_id, 0)),
    'grant_difficulty' => gisg_get_field('grant_difficulty', $post_id, 'normal'),
    'difficulty_level' => gisg_get_field('difficulty_level', $post_id, '中級'),
    'preparation_days' => intval(gisg_get_field('preparation_days', $post_id, 14)),
    'review_period' => gisg_get_field('review_period', $post_id),
    'review_period_days' => intval(gisg_get_field('review_period_days', $post_id, 0)),
    'is_featured' => (bool)gisg_get_field('is_featured', $post_id, false),
    'is_new' => (bool)gisg_get_field('is_new', $post_id, false),
    'is_popular' => (bool)gisg_get_field('is_popular', $post_id, false),
    'online_application' => (bool)gisg_get_field('online_application', $post_id, false),
    'jgrants_available' => (bool)gisg_get_field('jgrants_available', $post_id, false),
    'views_count' => intval(gisg_get_field('views_count', $post_id, 0)),
    'bookmark_count' => intval(gisg_get_field('bookmark_count', $post_id, 0)),
    'ai_summary' => gisg_get_field('ai_summary', $post_id),
    'application_method' => gisg_get_field('application_method', $post_id),
    'application_flow' => gisg_get_field('application_flow', $post_id),
    'application_flow_steps' => gisg_get_field_array('application_flow_steps', $post_id),
    'application_tips' => gisg_get_field('application_tips', $post_id),
    'common_mistakes' => gisg_get_field('common_mistakes', $post_id),
    'success_points' => gisg_get_field('success_points', $post_id),
    'success_cases' => gisg_get_field_array('success_cases', $post_id),
    'similar_grants' => gisg_get_field_array('similar_grants', $post_id),
    'comparison_points' => gisg_get_field_array('comparison_points', $post_id),
    'supervisor_name' => gisg_get_field('supervisor_name', $post_id),
    'supervisor_title' => gisg_get_field('supervisor_title', $post_id),
    'supervisor_profile' => gisg_get_field('supervisor_profile', $post_id),
    'supervisor_image' => gisg_get_field_array('supervisor_image', $post_id),
    'supervisor_url' => gisg_get_field('supervisor_url', $post_id),
    'supervisor_credentials' => gisg_get_field_array('supervisor_credentials', $post_id),
    'source_url' => gisg_get_field('source_url', $post_id),
    'source_name' => gisg_get_field('source_name', $post_id),
    'last_verified_date' => gisg_get_field('last_verified_date', $post_id),
    'update_history' => gisg_get_field_array('update_history', $post_id),
    'faq_items' => gisg_get_field_array('faq_items', $post_id),
    'related_columns' => gisg_get_field_array('related_columns', $post_id),
    'related_grants' => gisg_get_field_array('related_grants', $post_id),
);

// デフォルト監修者
if (empty($grant['supervisor_name'])) {
    $grant['supervisor_name'] = '補助金図鑑編集部';
    $grant['supervisor_title'] = '中小企業診断士・行政書士監修';
    $grant['supervisor_profile'] = '補助金・助成金の専門家チーム。中小企業診断士、行政書士、税理士など各分野の専門家が在籍。年間1,000件以上の補助金申請支援実績があります。';
    $grant['supervisor_credentials'] = array(
        array('credential' => '中小企業診断士'),
        array('credential' => '行政書士'),
        array('credential' => '認定経営革新等支援機関'),
    );
}

// タクソノミー
$taxonomies = array(
    'categories' => gisg_get_terms($post_id, 'grant_category'),
    'prefectures' => gisg_get_terms($post_id, 'grant_prefecture'),
    'municipalities' => gisg_get_terms($post_id, 'grant_municipality'),
    'industries' => gisg_get_terms($post_id, 'grant_industry'),
    'purposes' => gisg_get_terms($post_id, 'grant_purpose'),
    'tags' => gisg_get_terms($post_id, 'post_tag'),
);

// 地域判定
$is_nationwide = ($grant['regional_limitation'] === 'nationwide' || empty($taxonomies['prefectures']));

// 金額表示
$formatted_max = gisg_format_amount($grant['max_amount_numeric']);
$formatted_min = gisg_format_amount($grant['min_amount_numeric']);
if (!$formatted_max && $grant['max_amount']) $formatted_max = $grant['max_amount'];

$amount_display = '';
if ($formatted_min && $formatted_max && $formatted_min !== $formatted_max) {
    $amount_display = $formatted_min . '〜' . $formatted_max;
} elseif ($formatted_max) {
    $amount_display = '最大' . $formatted_max;
}

// 補助率表示
$subsidy_rate_display = $grant['subsidy_rate_detailed'] ? $grant['subsidy_rate_detailed'] : $grant['subsidy_rate'];
if (!$subsidy_rate_display && $grant['subsidy_rate_max'] > 0) {
    if ($grant['subsidy_rate_min'] > 0 && $grant['subsidy_rate_min'] != $grant['subsidy_rate_max']) {
        $subsidy_rate_display = $grant['subsidy_rate_min'] . '%〜' . $grant['subsidy_rate_max'] . '%';
    } else {
        $subsidy_rate_display = $grant['subsidy_rate_max'] . '%';
    }
}

// 締切計算
$deadline_info = '';
$days_remaining = 0;
$deadline_status = 'normal';

if ($grant['deadline_date']) {
    $deadline_timestamp = strtotime($grant['deadline_date'] . ' 23:59:59');
    if ($deadline_timestamp) {
        $deadline_info = date('Y年n月j日', $deadline_timestamp);
        $days_remaining = floor(($deadline_timestamp - current_time('timestamp')) / 86400);
        
        if ($days_remaining < 0) $deadline_status = 'closed';
        elseif ($days_remaining <= 3) $deadline_status = 'critical';
        elseif ($days_remaining <= 7) $deadline_status = 'urgent';
        elseif ($days_remaining <= 14) $deadline_status = 'warning';
        elseif ($days_remaining <= 30) $deadline_status = 'soon';
    }
} elseif ($grant['deadline']) {
    $deadline_info = $grant['deadline'];
}

// 難易度マップ
$difficulty_map = array(
    'very_easy' => array('label' => 'とても易しい', 'level' => 1),
    'easy' => array('label' => '易しい', 'level' => 2),
    'normal' => array('label' => '普通', 'level' => 3),
    'hard' => array('label' => '難しい', 'level' => 4),
    'expert' => array('label' => '専門家向け', 'level' => 5),
);
$difficulty = isset($difficulty_map[$grant['grant_difficulty']]) ? $difficulty_map[$grant['grant_difficulty']] : $difficulty_map['normal'];

// ステータスマップ
$status_map = array(
    'open' => array('label' => '募集中', 'class' => 'open'),
    'closed' => array('label' => '募集終了', 'class' => 'closed'),
    'upcoming' => array('label' => '募集予定', 'class' => 'upcoming'),
    'suspended' => array('label' => '一時停止', 'class' => 'suspended'),
);
$status = isset($status_map[$grant['application_status']]) ? $status_map[$grant['application_status']] : $status_map['open'];

// 閲覧数更新
$view_cookie = 'gi_viewed_' . $post_id;
if (!isset($_COOKIE[$view_cookie])) {
    update_post_meta($post_id, 'views_count', $grant['views_count'] + 1);
    $grant['views_count']++;
    $cookie_options = array(
        'expires' => time() + 86400,
        'path' => '/',
        'secure' => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax'
    );
    setcookie($view_cookie, '1', $cookie_options);
}

// 読了時間
$content = get_the_content();
$reading_time = max(1, ceil(mb_strlen(strip_tags($content), 'UTF-8') / 400));

// 最終確認日
$last_verified = $grant['last_verified_date'] ? $grant['last_verified_date'] : get_the_modified_date('Y-m-d');
$last_verified_display = date('Y年n月j日', strtotime($last_verified));
$freshness_class = '';
$freshness_label = '確認';
if ($last_verified) {
    $diff = (current_time('timestamp') - strtotime($last_verified)) / 86400;
    if ($diff < 90) { $freshness_class = 'fresh'; $freshness_label = '最新情報'; }
    elseif ($diff > 180) { $freshness_class = 'old'; $freshness_label = '情報古'; }
}

// パンくず
$breadcrumbs = array(
    array('name' => 'ホーム', 'url' => home_url('/')),
    array('name' => '補助金一覧', 'url' => home_url('/grants/')),
);
if (!empty($taxonomies['categories'][0])) {
    $cat_link = get_term_link($taxonomies['categories'][0]);
    if (!is_wp_error($cat_link)) {
        $breadcrumbs[] = array('name' => $taxonomies['categories'][0]->name, 'url' => $cat_link);
    }
}
$breadcrumbs[] = array('name' => get_the_title(), 'url' => $canonical_url);

// チェックリスト
$checklist_items = array();
$checklist_items[] = array('id' => 'target', 'category' => 'eligibility', 'label' => '対象者の要件を満たしている', 'description' => $grant['grant_target'] ? strip_tags($grant['grant_target']) : '', 'required' => true, 'help' => '事業者区分、業種、従業員数などの要件を確認してください。');

if (!$is_nationwide) {
    $area_text = '';
    if (!empty($taxonomies['prefectures'])) {
        $pref_names = array();
        foreach (array_slice($taxonomies['prefectures'], 0, 3) as $pref) {
            $pref_names[] = $pref->name;
        }
        $area_text = implode('、', $pref_names);
    }
    $checklist_items[] = array('id' => 'area', 'category' => 'eligibility', 'label' => '対象地域に該当する', 'description' => $area_text ? '対象: ' . $area_text : '', 'required' => true, 'help' => '事業所の所在地が対象地域内にあることを確認してください。');
}

$checklist_items[] = array('id' => 'deadline', 'category' => 'timing', 'label' => '申請期限内である', 'description' => $deadline_info ? '締切: ' . $deadline_info : '', 'required' => true, 'help' => '申請書類の準備期間も考慮して、余裕を持って申請してください。');
$checklist_items[] = array('id' => 'business_plan', 'category' => 'documents', 'label' => '事業計画書を作成できる', 'description' => '', 'required' => true, 'help' => '補助事業の目的、内容、効果を明確に記載した計画書が必要です。');

$docs = $grant['required_documents_detailed'] ? $grant['required_documents_detailed'] : $grant['required_documents'];
$checklist_items[] = array('id' => 'documents', 'category' => 'documents', 'label' => '必要書類を準備できる', 'description' => $docs ? strip_tags($docs) : '', 'required' => true, 'help' => '決算書、登記簿謄本、納税証明書などが必要になることが多いです。');

$expenses = $grant['eligible_expenses_detailed'] ? $grant['eligible_expenses_detailed'] : $grant['eligible_expenses'];
$checklist_items[] = array('id' => 'expenses', 'category' => 'eligibility', 'label' => '対象経費に該当する事業である', 'description' => $expenses ? strip_tags($expenses) : '', 'required' => true, 'help' => '補助対象となる経費の種類を確認してください。');

if ($grant['subsidy_rate_max'] > 0 && $grant['subsidy_rate_max'] < 100) {
    $self_funding = 100 - $grant['subsidy_rate_max'];
    $checklist_items[] = array('id' => 'self_funding', 'category' => 'financial', 'label' => '自己負担分の資金を確保できる', 'description' => '自己負担: 約' . $self_funding . '%', 'required' => true, 'help' => '補助金は後払いのため、一時的に全額を負担する必要があります。');
}

if ($grant['jgrants_available']) {
    $checklist_items[] = array('id' => 'gbizid', 'category' => 'preparation', 'label' => 'GビズIDプライムを取得済み', 'description' => 'jGrants申請に必要', 'required' => true, 'help' => 'GビズIDの取得には2〜3週間かかる場合があります。');
}

if ($grant['online_application']) {
    $checklist_items[] = array('id' => 'online', 'category' => 'preparation', 'label' => '電子申請の環境が整っている', 'description' => 'オンライン申請対応', 'required' => false, 'help' => 'パソコン、インターネット環境、PDFファイル作成環境が必要です。');
}

if ($difficulty['level'] >= 4) {
    $checklist_items[] = array('id' => 'support', 'category' => 'preparation', 'label' => '認定経営革新等支援機関に相談済み', 'description' => '専門家のサポート推奨', 'required' => false, 'help' => '商工会議所、金融機関、税理士などに相談することをお勧めします。');
}

$checklist_categories = array(
    'eligibility' => array('label' => '申請資格'),
    'timing' => array('label' => 'スケジュール'),
    'documents' => array('label' => '書類準備'),
    'financial' => array('label' => '資金計画'),
    'preparation' => array('label' => '事前準備'),
);

// 類似補助金
$similar_grants = gisg_get_similar($post_id, $taxonomies, $grant['similar_grants']);

// 締切間近
$deadline_soon_grants = new WP_Query(array(
    'post_type' => 'grant',
    'posts_per_page' => 5,
    'post__not_in' => array($post_id),
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

// 人気補助金
$popular_grants = new WP_Query(array(
    'post_type' => 'grant',
    'posts_per_page' => 5,
    'post__not_in' => array($post_id),
    'post_status' => 'publish',
    'meta_key' => 'views_count',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
));

// おすすめコラム
$recommended_columns = array();
if (!empty($grant['related_columns'])) {
    foreach ($grant['related_columns'] as $col_item) {
        $col_id = is_array($col_item) ? (isset($col_item['ID']) ? intval($col_item['ID']) : 0) : intval($col_item);
        if ($col_id > 0 && get_post_status($col_id) === 'publish') {
            $recommended_columns[] = array(
                'id' => $col_id,
                'title' => get_the_title($col_id),
                'permalink' => get_permalink($col_id),
                'thumbnail' => get_the_post_thumbnail_url($col_id, 'thumbnail'),
                'date' => get_the_date('Y.m.d', $col_id),
            );
        }
    }
}

if (count($recommended_columns) < 3) {
    $col_exclude = wp_list_pluck($recommended_columns, 'id');
    $col_query = new WP_Query(array(
        'post_type' => array('post', 'column'),
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'post__not_in' => $col_exclude,
    ));
    if ($col_query->have_posts()) {
        while ($col_query->have_posts() && count($recommended_columns) < 3) {
            $col_query->the_post();
            $recommended_columns[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'permalink' => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                'date' => get_the_date('Y.m.d'),
            );
        }
        wp_reset_postdata();
    }
}

// FAQ
$faq_items = array();
if (!empty($grant['faq_items'])) {
    foreach ($grant['faq_items'] as $faq) {
        if (is_array($faq) && !empty($faq['question']) && !empty($faq['answer'])) {
            $faq_items[] = $faq;
        }
    }
}

if ($grant['grant_target'] && count($faq_items) < 6) {
    $faq_items[] = array('question' => 'この補助金の対象者は誰ですか？', 'answer' => strip_tags($grant['grant_target']));
}

$docs_faq = $grant['required_documents_detailed'] ? $grant['required_documents_detailed'] : $grant['required_documents'];
if ($docs_faq && count($faq_items) < 6) {
    $faq_items[] = array('question' => '申請に必要な書類は何ですか？', 'answer' => strip_tags($docs_faq));
}

$expenses_faq = $grant['eligible_expenses_detailed'] ? $grant['eligible_expenses_detailed'] : $grant['eligible_expenses'];
if ($expenses_faq && count($faq_items) < 6) {
    $faq_items[] = array('question' => 'どのような経費が対象になりますか？', 'answer' => strip_tags($expenses_faq));
}

if (count($faq_items) < 6) {
    $faq_items[] = array('question' => '申請から採択までどのくらいかかりますか？', 'answer' => $grant['review_period'] ? $grant['review_period'] : '通常、申請から採択決定まで1〜2ヶ月程度かかります。');
}

// 目次
$toc_items = array();
if (!empty($grant['ai_summary'])) $toc_items[] = array('id' => 'summary', 'title' => 'AI要約');
$toc_items[] = array('id' => 'details', 'title' => '補助金詳細');
$toc_items[] = array('id' => 'content', 'title' => '補助金概要');
$toc_items[] = array('id' => 'checklist', 'title' => '申請チェックリスト');
if (!empty($grant['application_flow']) || !empty($grant['application_flow_steps'])) $toc_items[] = array('id' => 'flow', 'title' => '申請の流れ');
if (!empty($grant['application_tips'])) $toc_items[] = array('id' => 'tips', 'title' => '申請のコツ');
if (!empty($grant['success_cases'])) $toc_items[] = array('id' => 'cases', 'title' => '採択事例');
if (!empty($similar_grants)) $toc_items[] = array('id' => 'compare', 'title' => '類似補助金比較');
if (!empty($faq_items)) $toc_items[] = array('id' => 'faq', 'title' => 'よくある質問');
if (!empty($grant['contact_phone']) || !empty($grant['contact_email']) || !empty($grant['official_url'])) {
    $toc_items[] = array('id' => 'contact', 'title' => 'お問い合わせ');
}

// メタ情報
$meta_desc = '';
if ($grant['ai_summary']) {
    $meta_desc = mb_substr(wp_strip_all_tags($grant['ai_summary']), 0, 120, 'UTF-8');
} elseif (has_excerpt()) {
    $meta_desc = mb_substr(wp_strip_all_tags(get_the_excerpt()), 0, 120, 'UTF-8');
} else {
    $meta_desc = mb_substr(wp_strip_all_tags($content), 0, 120, 'UTF-8');
}
?>

<?php
/**
 * 構造化データ出力 (SEO最適化版)
 */
if (!function_exists('gi_is_seo_plugin_active') || !gi_is_seo_plugin_active()):
?>
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
            "headline": <?php echo json_encode(get_the_title(), JSON_UNESCAPED_UNICODE); ?>,
            "description": <?php echo json_encode($meta_desc, JSON_UNESCAPED_UNICODE); ?>,
            "url": "<?php echo esc_url($canonical_url); ?>",
            "datePublished": "<?php echo get_the_date('c'); ?>",
            "dateModified": "<?php echo get_the_modified_date('c'); ?>",
            "author": {
                "@type": "Organization",
                "name": <?php echo json_encode($grant['supervisor_name'] ? $grant['supervisor_name'] : '補助金図鑑編集部', JSON_UNESCAPED_UNICODE); ?>
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
            },
            "about": {
                "@type": "FinancialProduct",
                "name": <?php echo json_encode(get_the_title(), JSON_UNESCAPED_UNICODE); ?>,
                "provider": {
                    "@type": "GovernmentOrganization",
                    "name": <?php echo json_encode($grant['organization'] ? $grant['organization'] : '行政機関', JSON_UNESCAPED_UNICODE); ?>
                }
                <?php if ($grant['max_amount_numeric'] > 0): ?>
                ,"offers": {
                    "@type": "Offer",
                    "price": "0",
                    "priceCurrency": "JPY",
                    "description": <?php echo json_encode($amount_display ? '補助金額: ' . $amount_display : '金額は要確認', JSON_UNESCAPED_UNICODE); ?>
                }
                <?php endif; ?>
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
<?php endif; ?>

<!-- パンくずリスト -->
<nav class="gi-breadcrumb" aria-label="パンくずリスト">
    <div class="gi-breadcrumb-inner">
        <ol class="gi-breadcrumb-list">
            <?php foreach ($breadcrumbs as $i => $crumb): ?>
            <li class="gi-breadcrumb-item">
                <?php if ($i < count($breadcrumbs) - 1): ?>
                <a href="<?php echo esc_url($crumb['url']); ?>" class="gi-breadcrumb-link">
                    <span class="gi-breadcrumb-text"><?php echo esc_html($crumb['name']); ?></span>
                </a>
                <span class="gi-breadcrumb-sep" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </span>
                <?php else: ?>
                <span class="gi-breadcrumb-current">
                    <span class="gi-breadcrumb-text"><?php echo esc_html($crumb['name']); ?></span>
                </span>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</nav>

<div class="gi-page gi-grant-page">
    <div class="gi-container">
        
        <!-- ヒーロー -->
        <header class="gi-hero">
            <div class="gi-hero-badges">
                <span class="gi-badge gi-badge-<?php echo esc_attr($status['class']); ?>"><?php echo esc_html($status['label']); ?></span>
                <?php if ($days_remaining > 0 && $days_remaining <= 14): ?>
                <span class="gi-badge gi-badge-<?php echo esc_attr($deadline_status); ?>">残り<?php echo $days_remaining; ?>日</span>
                <?php endif; ?>
                <?php if ($grant['is_featured']): ?><span class="gi-badge gi-badge-featured">注目</span><?php endif; ?>
                <?php if ($grant['is_new']): ?><span class="gi-badge gi-badge-recent">新着</span><?php endif; ?>
            </div>
            <h1 class="gi-hero-title"><?php the_title(); ?></h1>
            <div class="gi-hero-meta">
                <span class="gi-hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    約<?php echo $reading_time; ?>分で読了
                </span>
                <span class="gi-hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <?php echo number_format($grant['views_count']); ?>回閲覧
                </span>
                <span class="gi-hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                    <?php echo esc_html($last_verified_display); ?><span class="<?php echo $freshness_class; ?>"><?php echo $freshness_label; ?></span>
                </span>
            </div>
        </header>

        <!-- メトリクス -->
        <section class="gi-metrics" aria-label="重要情報">
            <div class="gi-metric">
                <div class="gi-metric-label">補助金額</div>
                <div class="gi-metric-value highlight"><?php echo $amount_display ? esc_html($amount_display) : '要確認'; ?></div>
                <?php if ($subsidy_rate_display): ?><div class="gi-metric-sub">補助率 <?php echo esc_html($subsidy_rate_display); ?></div><?php endif; ?>
            </div>
            <div class="gi-metric">
                <div class="gi-metric-label">申請締切</div>
                <div class="gi-metric-value <?php echo ($deadline_status === 'critical' || $deadline_status === 'urgent') ? 'urgent' : ''; ?>">
                    <?php if ($days_remaining > 0): ?>残り<?php echo $days_remaining; ?>日<?php else: ?><?php echo $deadline_info ? esc_html($deadline_info) : '要確認'; ?><?php endif; ?>
                </div>
                <?php if ($deadline_info && $days_remaining > 0): ?><div class="gi-metric-sub"><?php echo esc_html($deadline_info); ?></div><?php endif; ?>
            </div>
            <div class="gi-metric">
                <div class="gi-metric-label">難易度</div>
                <div class="gi-metric-value"><?php echo esc_html($difficulty['label']); ?></div>
                <div class="gi-metric-stars" aria-label="難易度 <?php echo $difficulty['level']; ?> / 5">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <svg class="gi-metric-star <?php echo $i <= $difficulty['level'] ? 'active' : ''; ?>" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="gi-metric">
                <div class="gi-metric-label">
                    採択率
                    <span class="gi-tooltip-trigger">
                        <span class="gi-ai-estimate-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                            AI推定
                        </span>
                        <span class="gi-tooltip">この採択率はAIが過去のデータや類似補助金の傾向から推定した参考値です。公式発表の数値ではありません。</span>
                    </span>
                </div>
                <div class="gi-metric-value"><?php echo $grant['adoption_rate'] > 0 ? number_format($grant['adoption_rate'], 1) . '%' : '—'; ?></div>
                <?php if ($grant['adoption_count'] > 0): ?>
                <div class="gi-metric-sub"><?php echo number_format($grant['adoption_count']); ?>社採択</div>
                <?php endif; ?>
                <div class="gi-metric-ai-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    参考値・公式発表ではありません
                </div>
            </div>
        </section>

        <div class="gi-layout">
            <main class="gi-main" id="main-content">
                
                <!-- AI要約 -->
                <?php if ($grant['ai_summary']): ?>
                <section class="gi-summary" id="summary" aria-labelledby="summary-title">
                    <div class="gi-summary-header">
                        <div class="gi-summary-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
                        <span class="gi-summary-label" id="summary-title">AI要約</span>
                        <span class="gi-summary-badge">30秒で理解</span>
                    </div>
                    <p class="gi-summary-text"><?php echo nl2br(esc_html($grant['ai_summary'])); ?></p>
                </section>
                <?php endif; ?>

                <!-- ===================================
                     01 補助金詳細 - 統合セクション（写真1枚目・3枚目参考）
                     =================================== -->
                <section class="gi-integrated-section" id="details" aria-labelledby="details-title">
                    <!-- 統合ヘッダー -->
                    <header class="gi-integrated-header">
                        <!-- 左側：セクション番号 -->
                        <div class="gi-integrated-number">
                            <div class="gi-integrated-number-inner">
                                <span class="gi-integrated-number-label">Section</span>
                                <span class="gi-integrated-number-value">01</span>
                            </div>
                        </div>
                        <!-- 右側：タイトルエリア -->
                        <div class="gi-integrated-title-area">
                            <div class="gi-integrated-book-icon">
                                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="8" y="6" width="44" height="52" rx="2" fill="#1b263b" stroke="#c9a227" stroke-width="2"/>
                                    <rect x="12" y="6" width="40" height="52" rx="2" fill="#0d1b2a"/>
                                    <path d="M16 12h28M16 18h28M16 24h20" stroke="#c9a227" stroke-width="1.5" stroke-linecap="round" opacity="0.6"/>
                                    <rect x="52" y="6" width="4" height="52" fill="#c9a227"/>
                                    <path d="M54 10v44" stroke="#b8941f" stroke-width="1"/>
                                    <circle cx="32" cy="40" r="8" fill="#c9a227" opacity="0.2"/>
                                    <path d="M28 40l3 3 5-6" stroke="#c9a227" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="gi-integrated-title-content">
                                <div class="gi-integrated-entry-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                    補助金図鑑 ENTRY No.<?php echo str_pad($post_id, 5, '0', STR_PAD_LEFT); ?>
                                </div>
                                <h2 class="gi-integrated-main-title" id="details-title">補助金詳細</h2>
                                <p class="gi-integrated-sub-title"><?php echo esc_html($grant['organization'] ? $grant['organization'] : '補助金'); ?>の詳細情報</p>
                                <div class="gi-integrated-en-title">Details</div>
                            </div>
                            <div class="gi-integrated-ribbon"></div>
                        </div>
                    </header>
                    
                    <!-- 統合セクション本体 - 写真1枚目参考 -->
                    <div class="gi-integrated-body">
                        <!-- 金額・スケジュール + 採択率統計情報 の2カラム構成 -->
                        <div class="gi-details-two-col">
                            <!-- 左カラム：金額・スケジュール -->
                            <div class="gi-details-left-col">
                                <div class="gi-details-section-title">金額・スケジュール</div>
                                
                                <div class="gi-details-field">
                                    <div class="gi-details-label">補助金額</div>
                                    <div class="gi-details-value-large"><?php echo $amount_display ? esc_html($amount_display) : '要確認'; ?></div>
                                </div>
                                
                                <div class="gi-details-field">
                                    <div class="gi-details-label">申請締切</div>
                                    <div class="gi-details-value-deadline<?php echo ($deadline_status === 'critical' || $deadline_status === 'urgent') ? ' gi-details-value-deadline--urgent' : ''; ?>"><?php echo $deadline_info ? esc_html($deadline_info) : '要確認'; ?></div>
                                    <?php if ($days_remaining > 0): ?>
                                    <div class="gi-details-remaining">（残り<?php echo $days_remaining; ?>日）</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- 右カラム：採択率・統計情報 -->
                            <div class="gi-details-right-col">
                                <div class="gi-details-stats-header">
                                    採択率・統計情報
                                    <span class="gi-ai-estimate-badge">AI推定</span>
                                </div>
                                
                                <div class="gi-details-rate-box">
                                    <div class="gi-details-label">推定採択率</div>
                                    <div class="gi-details-rate-value"><?php echo $grant['adoption_rate'] > 0 ? number_format($grant['adoption_rate'], 1) . '%' : '—'; ?></div>
                                    <div class="gi-details-rate-note">※参考値</div>
                                </div>
                                
                                <div class="gi-details-disclaimer">
                                    ご注意：この採択率・統計情報は、AIが過去の公開データや類似補助金の傾向を分析して推定した参考値です。公式機関が発表した数値ではありません。
                                </div>
                            </div>
                        </div>
                        
                        <!-- 対象要件・申請要件の統合テーブル -->
                        <div class="gi-details-requirements-grid">
                            <!-- 左側：対象要件 -->
                            <div class="gi-details-requirements-left">
                                <div class="gi-details-section-title gi-details-section-title--no-border">対象要件</div>
                                
                                <?php if ($grant['organization']): ?>
                                <div class="gi-details-item">
                                    <div class="gi-details-label">主催機関</div>
                                    <div class="gi-details-value gi-details-value--bold"><?php echo esc_html($grant['organization']); ?></div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="gi-details-item">
                                    <div class="gi-details-label">対象地域</div>
                                    <div class="gi-details-value">
                                        <?php if ($is_nationwide): ?>
                                        全国
                                        <?php elseif (!empty($taxonomies['prefectures'])): ?>
                                        <?php 
                                        $pref_names = array();
                                        foreach (array_slice($taxonomies['prefectures'], 0, 3) as $pref) {
                                            $pref_names[] = $pref->name;
                                        }
                                        echo esc_html(implode('、', $pref_names));
                                        if (count($taxonomies['prefectures']) > 3) {
                                            echo ' 他' . (count($taxonomies['prefectures']) - 3) . '件';
                                        }
                                        ?>
                                        <?php else: ?>要確認<?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if ($grant['grant_target']): ?>
                                <div class="gi-details-item">
                                    <div class="gi-details-label">対象者</div>
                                    <div class="gi-details-value"><?php echo wp_strip_all_tags($grant['grant_target']); ?></div>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- 右側：申請要件 -->
                            <div class="gi-details-requirements-right">
                                <div class="gi-details-section-title gi-details-section-title--no-border">申請要件</div>
                                
                                <?php if ($docs): ?>
                                <div class="gi-details-item">
                                    <div class="gi-details-label">必要書類</div>
                                    <div class="gi-details-docs-box"><?php echo wp_strip_all_tags($docs); ?></div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($expenses): ?>
                                <div class="gi-details-item">
                                    <div class="gi-details-label">対象経費</div>
                                    <div class="gi-details-value"><?php echo wp_strip_all_tags($expenses); ?></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- アフィリエイト記事欄（補助金詳細後） -->
                <?php 
                ob_start();
                if (function_exists('ji_display_ad')) {
                    ji_display_ad('single_grant_article_after_details');
                }
                $ad_after_details = ob_get_clean();

                if (!empty($ad_after_details)): 
                ?>
                <aside class="gi-article-ad-section" aria-label="関連サービス紹介">
                    <header class="gi-article-ad-section-header">
                        <span class="gi-article-ad-section-badge">PR</span>
                        <span class="gi-article-ad-section-title">申請に役立つサービス</span>
                    </header>
                    <?php echo $ad_after_details; ?>
                </aside>
                <?php endif; ?>

                <!-- ===================================
                     02 補助金概要
                     =================================== -->
                <section class="gi-section" id="content" aria-labelledby="content-title">
                    <header class="gi-section-numbered-header">
                        <div class="gi-section-number-box">
                            <div class="gi-section-number-inner">
                                <span class="gi-section-number-label">Section</span>
                                <span class="gi-section-number-value">02</span>
                            </div>
                        </div>
                        <div class="gi-section-title-box">
                            <svg class="gi-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                            <h2 class="gi-section-title" id="content-title">補助金概要</h2>
                            <span class="gi-section-en">Overview</span>
                        </div>
                    </header>
                    
                    <div class="gi-section-body">
                        <div class="gi-dict-note">
                            <span class="gi-dict-note-text">この補助金に関する詳細な説明と申請に必要な情報を掲載しています。最新情報は公式サイトで必ずご確認ください。</span>
                        </div>
                        
                        <div class="gi-content"><?php echo apply_filters('the_content', $content); ?></div>
                        
                        <div class="gi-encyclopedia-divider">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        </div>
                    </div>
                </section>

                <!-- アフィリエイト記事欄（チェックリスト前） -->
                <?php 
                ob_start();
                if (function_exists('ji_display_ad')) {
                    ji_display_ad('single_grant_article_before_checklist');
                }
                $ad_before_checklist = ob_get_clean();

                if (!empty($ad_before_checklist)): 
                ?>
                <aside class="gi-article-ad-section" aria-label="おすすめサービス">
                    <header class="gi-article-ad-section-header">
                        <span class="gi-article-ad-section-badge">PR</span>
                        <span class="gi-article-ad-section-title">補助金申請をサポート</span>
                    </header>
                    <?php echo $ad_before_checklist; ?>
                </aside>
                <?php endif; ?>

                <!-- ===================================
                     03 申請前チェックリスト - 写真2枚目参考（クリーム背景・穴あき装飾）
                     =================================== -->
                <section class="gi-section" id="checklist" aria-labelledby="checklist-title">
                    <header class="gi-section-numbered-header">
                        <div class="gi-section-number-box">
                            <div class="gi-section-number-inner">
                                <span class="gi-section-number-label">Section</span>
                                <span class="gi-section-number-value">03</span>
                            </div>
                        </div>
                        <div class="gi-section-title-box">
                            <svg class="gi-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            <h2 class="gi-section-title" id="checklist-title">申請前チェックリスト</h2>
                            <span class="gi-section-en">Checklist</span>
                            <div class="gi-checklist-header-actions">
                                <button class="gi-checklist-action-btn" id="checklistReset" type="button">リセット</button>
                                <button class="gi-checklist-action-btn" id="checklistPrint" type="button">印刷</button>
                            </div>
                        </div>
                    </header>
                    
                    <div class="gi-section-body gi-section-body--no-padding">
                        <!-- 写真2枚目参考：クリーム背景 + 左端穴あき装飾 -->
                        <div class="gi-checklist-v2">
                            <!-- 穴あき装飾 -->
                            <div class="gi-checklist-v2-holes">
                                <div class="gi-checklist-v2-hole"></div>
                                <div class="gi-checklist-v2-hole"></div>
                                <div class="gi-checklist-v2-hole"></div>
                            </div>
                            
                            <!-- ヘッダー：進捗表示 -->
                            <div class="gi-checklist-v2-header">
                                <div class="gi-checklist-v2-progress">
                                    <span class="gi-checklist-v2-count" id="checklistCount">0 / <?php echo count($checklist_items); ?> 完了</span>
                                    <span class="gi-checklist-v2-percent" id="checklistPercent">0%</span>
                                </div>
                            </div>
                            
                            <!-- チェックリスト本体 -->
                            <?php 
                            $grouped_items = array();
                            foreach ($checklist_items as $item) {
                                $cat = $item['category'];
                                if (!isset($grouped_items[$cat])) $grouped_items[$cat] = array();
                                $grouped_items[$cat][] = $item;
                            }
                            foreach ($grouped_items as $cat_key => $items):
                                $cat_info = isset($checklist_categories[$cat_key]) ? $checklist_categories[$cat_key] : array('label' => $cat_key);
                            ?>
                            <div class="gi-checklist-v2-category">
                                <div class="gi-checklist-v2-category-title"><?php echo esc_html($cat_info['label']); ?></div>
                                <div class="gi-checklist-v2-items">
                                    <?php foreach ($items as $item): ?>
                                    <div class="gi-checklist-v2-item gi-checklist-item" data-id="<?php echo esc_attr($item['id']); ?>" data-required="<?php echo $item['required'] ? 'true' : 'false'; ?>">
                                        <div class="gi-checklist-v2-checkbox gi-checklist-checkbox" role="checkbox" aria-checked="false" tabindex="0">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        </div>
                                        <div class="gi-checklist-v2-content">
                                            <div class="gi-checklist-v2-label-row">
                                                <?php if ($item['required']): ?><span class="gi-checklist-v2-required">必須</span><?php endif; ?>
                                                <span class="gi-checklist-v2-label"><?php echo esc_html($item['label']); ?></span>
                                            </div>
                                            <?php if (!empty($item['description'])): ?><div class="gi-checklist-v2-desc"><?php echo esc_html($item['description']); ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <!-- 結果メッセージ -->
                            <div class="gi-checklist-v2-result" id="checklistResult">
                                <div class="gi-checklist-v2-result-title" id="checklistResultText">チェックを入れて申請可否を確認しましょう</div>
                                <div class="gi-checklist-v2-result-sub" id="checklistResultSub">必須項目をすべてクリアすると申請可能です</div>
                                <div class="gi-checklist-v2-cta">
                                    <?php if ($grant['official_url']): ?>
                                    <a href="<?php echo esc_url($grant['official_url']); ?>" class="gi-btn gi-btn-accent gi-btn-full gi-checklist-cta-btn" target="_blank" rel="noopener">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                        公式サイトで申請する
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- アフィリエイト記事欄（チェックリスト後） -->
                <?php 
                ob_start();
                if (function_exists('ji_display_ad')) {
                    ji_display_ad('single_grant_article_after_checklist');
                }
                $ad_after_checklist = ob_get_clean();

                if (!empty($ad_after_checklist)): 
                ?>
                <aside class="gi-article-ad-section" aria-label="関連サービス">
                    <header class="gi-article-ad-section-header">
                        <span class="gi-article-ad-section-badge">PR</span>
                        <span class="gi-article-ad-section-title">申請をスムーズに</span>
                    </header>
                    <?php echo $ad_after_checklist; ?>
                </aside>
                <?php endif; ?>

                <!-- 申請フロー -->
                <?php if (!empty($grant['application_flow_steps']) || !empty($grant['application_flow'])): ?>
                <section class="gi-section" id="flow" aria-labelledby="flow-title">
                    <header class="gi-section-numbered-header">
                        <div class="gi-section-number-box">
                            <div class="gi-section-number-inner">
                                <span class="gi-section-number-label">Section</span>
                                <span class="gi-section-number-value">04</span>
                            </div>
                        </div>
                        <div class="gi-section-title-box">
                            <svg class="gi-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="6" y1="3" x2="6" y2="15"/><circle cx="18" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M18 9a9 9 0 0 1-9 9"/></svg>
                            <h2 class="gi-section-title" id="flow-title">申請の流れ</h2>
                            <span class="gi-section-en">Flow</span>
                        </div>
                    </header>
                    <div class="gi-section-body">
                        <div class="gi-flow">
                            <?php 
                            $steps = array();
                            if (!empty($grant['application_flow_steps'])) {
                                foreach ($grant['application_flow_steps'] as $step) {
                                    if (is_array($step) && !empty($step['title'])) {
                                        $steps[] = array('title' => $step['title'], 'desc' => isset($step['description']) ? $step['description'] : '');
                                    }
                                }
                            } elseif (!empty($grant['application_flow'])) {
                                $flow_lines = array_filter(explode("\n", $grant['application_flow']));
                                foreach ($flow_lines as $line) {
                                    $parts = preg_split('/[:：]/', trim($line), 2);
                                    $steps[] = array('title' => trim($parts[0]), 'desc' => isset($parts[1]) ? trim($parts[1]) : '');
                                }
                            }
                            foreach ($steps as $i => $step):
                            ?>
                            <div class="gi-flow-step">
                                <div class="gi-flow-num"><?php echo $i + 1; ?></div>
                                <div class="gi-flow-content">
                                    <h3 class="gi-flow-title"><?php echo esc_html($step['title']); ?></h3>
                                    <?php if (!empty($step['desc'])): ?><p class="gi-flow-desc"><?php echo esc_html($step['desc']); ?></p><?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- アフィリエイト記事欄（申請フロー後） -->
                <?php 
                ob_start();
                if (function_exists('ji_display_ad')) {
                    ji_display_ad('single_grant_article_after_flow');
                }
                $ad_after_flow = ob_get_clean();

                if (!empty($ad_after_flow)): 
                ?>
                <aside class="gi-article-ad-section" aria-label="おすすめツール">
                    <header class="gi-article-ad-section-header">
                        <span class="gi-article-ad-section-badge">PR</span>
                        <span class="gi-article-ad-section-title">申請書類作成をサポート</span>
                    </header>
                    <?php echo $ad_after_flow; ?>
                </aside>
                <?php endif; ?>

                <!-- 申請のコツ -->
                <?php if ($grant['application_tips']): ?>
                <section class="gi-section" id="tips" aria-labelledby="tips-title">
                    <header class="gi-section-header">
                        <svg class="gi-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v1m0 18v1m9-10h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <h2 class="gi-section-title" id="tips-title">申請のコツ・ポイント</h2>
                        <span class="gi-section-en">Tips</span>
                    </header>
                    <div class="gi-section-body">
                        <div class="gi-content"><?php echo wp_kses_post($grant['application_tips']); ?></div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- よくある失敗 -->
                <?php if ($grant['common_mistakes']): ?>
                <section class="gi-section" aria-labelledby="mistakes-title">
                    <header class="gi-section-header">
                        <svg class="gi-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <h2 class="gi-section-title" id="mistakes-title">よくある失敗・注意点</h2>
                        <span class="gi-section-en">Caution</span>
                    </header>
                    <div class="gi-section-body">
                        <div class="gi-content" style="background: var(--zukan-error-light); padding: 20px; border-left: 4px solid var(--zukan-error);"><?php echo wp_kses_post($grant['common_mistakes']); ?></div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- 採択事例 -->
                <?php if (!empty($grant['success_cases'])): ?>
                <section class="gi-section" id="cases" aria-labelledby="cases-title">
                    <header class="gi-section-header">
                        <svg class="gi-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <h2 class="gi-section-title" id="cases-title">採択事例</h2>
                        <span class="gi-section-en">Cases</span>
                    </header>
                    <div class="gi-section-body">
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                            <?php foreach ($grant['success_cases'] as $case): if (!is_array($case)) continue; ?>
                            <div style="background: var(--zukan-border-light); border: 1px solid var(--zukan-border-color); padding: 20px;">
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                    <div style="width: 48px; height: 48px; background: var(--zukan-success-light); display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--zukan-success)" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    </div>
                                    <div>
                                        <?php if (!empty($case['industry'])): ?><div style="font-size: 14px; color: var(--zukan-ink-light);"><?php echo esc_html($case['industry']); ?></div><?php endif; ?>
                                        <?php if (!empty($case['amount'])): ?><div style="font-size: 18px; font-weight: 900; color: var(--zukan-success);"><?php echo esc_html($case['amount']); ?></div><?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!empty($case['purpose'])): ?><p style="font-size: 14px; color: var(--zukan-ink-secondary); line-height: 1.6;"><?php echo esc_html($case['purpose']); ?></p><?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- 類似補助金比較 - Section 05 -->
                <?php if (!empty($similar_grants)): ?>
                <section class="gi-section" id="compare" aria-labelledby="compare-title">
                    <header class="gi-section-numbered-header">
                        <div class="gi-section-number-box">
                            <div class="gi-section-number-inner">
                                <span class="gi-section-number-label">Section</span>
                                <span class="gi-section-number-value">05</span>
                            </div>
                        </div>
                        <div class="gi-section-title-box">
                            <svg class="gi-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                            <h2 class="gi-section-title" id="compare-title">類似補助金との比較</h2>
                            <span class="gi-section-en">Comparison</span>
                        </div>
                    </header>
                    <div class="gi-section-body">
                        <div class="gi-ai-disclaimer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <div class="gi-ai-disclaimer-text">
                                <strong>採択率について：</strong>比較表内の採択率はAIによる推定値であり、公式発表の数値ではありません。補助金選びの参考としてご活用ください。
                            </div>
                        </div>
                        
                        <div class="gi-compare">
                            <table class="gi-compare-table">
                                <thead>
                                    <tr>
                                        <th>比較項目</th>
                                        <th class="gi-compare-current-header">
                                            <div class="gi-compare-grant-header">
                                                <span class="gi-compare-grant-name">この補助金</span>
                                                <?php if ($grant['organization']): ?><span class="gi-compare-grant-org"><?php echo esc_html($grant['organization']); ?></span><?php endif; ?>
                                            </div>
                                        </th>
                                        <?php foreach ($similar_grants as $sg): ?>
                                        <th>
                                            <div class="gi-compare-grant-header">
                                                <span class="gi-compare-grant-name" title="<?php echo esc_attr($sg['title']); ?>">
                                                    <?php echo esc_html(mb_substr($sg['title'], 0, 25, 'UTF-8')); ?><?php if (mb_strlen($sg['title'], 'UTF-8') > 25): ?>...<?php endif; ?>
                                                </span>
                                                <?php if ($sg['organization']): ?><span class="gi-compare-grant-org"><?php echo esc_html($sg['organization']); ?></span><?php endif; ?>
                                            </div>
                                        </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>補助金額</th>
                                        <td class="gi-compare-current"><span class="gi-compare-value highlight"><?php echo $amount_display ? esc_html($amount_display) : '要確認'; ?></span></td>
                                        <?php foreach ($similar_grants as $sg): ?><td><span class="gi-compare-value"><?php echo $sg['max_amount'] ? esc_html($sg['max_amount']) : '要確認'; ?></span></td><?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <th>補助率</th>
                                        <td class="gi-compare-current"><span class="gi-compare-value"><?php echo $subsidy_rate_display ? esc_html($subsidy_rate_display) : '—'; ?></span></td>
                                        <?php foreach ($similar_grants as $sg): ?><td><span class="gi-compare-value"><?php echo $sg['subsidy_rate'] ? esc_html($sg['subsidy_rate']) : '—'; ?></span></td><?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <th>申請締切</th>
                                        <td class="gi-compare-current"><span class="gi-compare-value"><?php echo $deadline_info ? esc_html($deadline_info) : '随時'; ?></span></td>
                                        <?php foreach ($similar_grants as $sg): ?><td><span class="gi-compare-value"><?php echo $sg['deadline'] ? esc_html($sg['deadline']) : '随時'; ?></span></td><?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <th>難易度</th>
                                        <td class="gi-compare-current">
                                            <div class="gi-compare-stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?><svg class="gi-compare-star <?php echo $i <= $difficulty['level'] ? 'active' : ''; ?>" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><?php endfor; ?>
                                            </div>
                                        </td>
                                        <?php foreach ($similar_grants as $sg): 
                                            $sg_diff = isset($difficulty_map[$sg['grant_difficulty']]) ? $difficulty_map[$sg['grant_difficulty']] : $difficulty_map['normal'];
                                        ?>
                                        <td>
                                            <div class="gi-compare-stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?><svg class="gi-compare-star <?php echo $i <= $sg_diff['level'] ? 'active' : ''; ?>" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><?php endfor; ?>
                                            </div>
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <th>
                                            採択率
                                            <span class="gi-ai-estimate-badge" style="display: block; margin-top: 4px;">AI推定</span>
                                        </th>
                                        <td class="gi-compare-current">
                                            <span class="gi-compare-value <?php echo $grant['adoption_rate'] >= 50 ? 'highlight' : ''; ?>">
                                                <?php echo $grant['adoption_rate'] > 0 ? number_format($grant['adoption_rate'], 1) . '%' : '—'; ?>
                                            </span>
                                            <?php if ($grant['adoption_rate'] > 0): ?>
                                            <span class="gi-compare-ai-note">※参考値</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php foreach ($similar_grants as $sg): ?>
                                        <td>
                                            <span class="gi-compare-value <?php echo $sg['adoption_rate'] >= 50 ? 'highlight' : ''; ?>">
                                                <?php echo $sg['adoption_rate'] > 0 ? number_format($sg['adoption_rate'], 1) . '%' : '—'; ?>
                                            </span>
                                            <?php if ($sg['adoption_rate'] > 0): ?>
                                            <span class="gi-compare-ai-note">※参考値</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <th>準備目安</th>
                                        <td class="gi-compare-current"><span class="gi-compare-value">約<?php echo $grant['preparation_days']; ?>日</span></td>
                                        <?php foreach ($similar_grants as $sg): ?><td><span class="gi-compare-value">約<?php echo $sg['preparation_days'] ? $sg['preparation_days'] : 14; ?>日</span></td><?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <th>詳細</th>
                                        <td class="gi-compare-current">—</td>
                                        <?php foreach ($similar_grants as $sg): ?><td><a href="<?php echo esc_url($sg['permalink']); ?>" class="gi-compare-link">詳細を見る →</a></td><?php endforeach; ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- FAQ - Section 06 -->
                <?php if (!empty($faq_items)): ?>
                <section class="gi-section" id="faq" aria-labelledby="faq-title">
                    <header class="gi-section-numbered-header">
                        <div class="gi-section-number-box">
                            <div class="gi-section-number-inner">
                                <span class="gi-section-number-label">Section</span>
                                <span class="gi-section-number-value">06</span>
                            </div>
                        </div>
                        <div class="gi-section-title-box">
                            <svg class="gi-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            <h2 class="gi-section-title" id="faq-title">よくある質問</h2>
                            <span class="gi-section-en">FAQ</span>
                        </div>
                    </header>
                    <div class="gi-section-body">
                        <div class="gi-faq-list">
                            <?php foreach ($faq_items as $faq): ?>
                            <details class="gi-faq-item">
                                <summary class="gi-faq-question">
                                    <span class="gi-faq-q-mark">Q</span>
                                    <span class="gi-faq-question-text"><?php echo esc_html($faq['question']); ?></span>
                                    <svg class="gi-faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                </summary>
                                <div class="gi-faq-answer"><?php echo nl2br(esc_html($faq['answer'])); ?></div>
                            </details>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- アフィリエイト記事欄（お問い合わせ前） -->
                <?php 
                ob_start();
                if (function_exists('ji_display_ad')) {
                    ji_display_ad('single_grant_article_before_contact');
                }
                $ad_before_contact = ob_get_clean();

                if (!empty($ad_before_contact)): 
                ?>
                <aside class="gi-article-ad-section" aria-label="便利なサービス">
                    <header class="gi-article-ad-section-header">
                        <span class="gi-article-ad-section-badge">PR</span>
                        <span class="gi-article-ad-section-title">補助金活用のヒント</span>
                    </header>
                    <?php echo $ad_before_contact; ?>
                </aside>
                <?php endif; ?>

                <!-- お問い合わせセクション - 新デザイン -->
                <section class="gi-contact-section-v2" id="contact" aria-labelledby="contact-title">
                    <div class="gi-contact-v2-header">
                        <div class="gi-contact-v2-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <h2 class="gi-contact-v2-title" id="contact-title">お問い合わせ</h2>
                        <span class="gi-contact-v2-sub">CONTACT</span>
                    </div>
                    
                    <div class="gi-contact-v2-body">
                        <!-- 公式サイト + 広告バナー -->
                        <div class="gi-contact-v2-row">
                            <div class="gi-contact-v2-official">
                                <div class="gi-contact-v2-official-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                </div>
                                <div class="gi-contact-v2-official-content">
                                    <div class="gi-contact-v2-official-label">公式サイト</div>
                                    <?php if ($grant['official_url']): ?>
                                    <a href="<?php echo esc_url($grant['official_url']); ?>" class="gi-contact-v2-official-link" target="_blank" rel="noopener">
                                        公式サイトを見る →
                                    </a>
                                    <?php else: ?>
                                    <span class="gi-contact-v2-official-na">—</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- 広告バナースロット -->
                            <div class="gi-contact-v2-ad">
                                <?php if (function_exists('ji_display_ad')): ?>
                                <?php ji_display_ad('single_grant_contact_banner'); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 補助金ID・情報ソースセクション - 新デザイン -->
                <section class="gi-meta-section-v2">
                    <div class="gi-meta-v2-header">
                        <div class="gi-meta-v2-id">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="12" y2="12"/></svg>
                            補助金図鑑 #<?php echo esc_html($post_id); ?>
                        </div>
                        <span class="gi-meta-v2-year"><?php echo date('Y'); ?>年版</span>
                    </div>
                </section>

                <!-- 情報ソースセクション - 新デザイン -->
                <section class="gi-source-section-v2">
                    <div class="gi-source-v2-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span class="gi-source-v2-label">情報ソース</span>
                    </div>
                    
                    <div class="gi-source-v2-body">
                        <div class="gi-source-v2-name"><?php echo esc_html($grant['source_name'] ? $grant['source_name'] : ($grant['organization'] ? $grant['organization'] : '公式情報')); ?></div>
                        <div class="gi-source-v2-verified">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <?php echo esc_html($last_verified_display); ?> 確認済み
                        </div>
                    </div>
                    
                    <div class="gi-source-v2-footer">
                        ※最新情報は必ず公式サイトでご確認ください。本ページの情報は参考情報です。
                    </div>
                </section>

                <!-- 監修・編集セクション - 新デザイン -->
                <aside class="gi-supervisor-section-v2">
                    <div class="gi-supervisor-v2-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span class="gi-supervisor-v2-label">監修・編集</span>
                    </div>
                    
                    <div class="gi-supervisor-v2-body">
                        <div class="gi-supervisor-v2-main">
                            <div class="gi-supervisor-v2-avatar">
                                <?php if (!empty($grant['supervisor_image']) && isset($grant['supervisor_image']['url'])): ?>
                                <img src="<?php echo esc_url($grant['supervisor_image']['url']); ?>" alt="<?php echo esc_attr($grant['supervisor_name']); ?>" loading="lazy" decoding="async" width="56" height="56">
                                <?php else: ?>
                                <span class="gi-supervisor-v2-avatar-text">図</span>
                                <?php endif; ?>
                            </div>
                            <div class="gi-supervisor-v2-info">
                                <div class="gi-supervisor-v2-name"><?php echo esc_html($grant['supervisor_name']); ?></div>
                                <div class="gi-supervisor-v2-title"><?php echo esc_html($grant['supervisor_title']); ?></div>
                            </div>
                        </div>
                        
                        <p class="gi-supervisor-v2-bio"><?php echo esc_html($grant['supervisor_profile']); ?></p>
                        
                        <div class="gi-supervisor-v2-badges">
                            <?php if (!empty($grant['supervisor_credentials'])): ?>
                                <?php foreach ($grant['supervisor_credentials'] as $cred): if (!is_array($cred) || empty($cred['credential'])) continue; ?>
                                <span class="gi-supervisor-v2-badge"><?php echo esc_html($cred['credential']); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <span class="gi-supervisor-v2-badge">中小企業診断士</span>
                            <span class="gi-supervisor-v2-badge">行政書士</span>
                            <span class="gi-supervisor-v2-badge">認定経営革新等支援機関</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </aside>

            </main>

            <!-- サイドバー -->
            <aside class="gi-sidebar">
                
                <!-- 広告枠（上部） -->
                <?php if (function_exists('ji_display_ad')): ?>
                <section class="gi-sidebar-section gi-ad-section gi-ad-top" aria-label="広告">
                    <header class="gi-sidebar-header"><span class="gi-sidebar-title gi-pr-label">PR</span></header>
                    <div class="gi-sidebar-body">
                        <div class="gi-ad-slot" id="adSlotTop"><?php ji_display_ad('single_grant_sidebar_top'); ?></div>
                    </div>
                </section>
                <?php endif; ?>
                
                <!-- AIアシスタント -->
                <section class="gi-sidebar-section gi-ai-section" aria-labelledby="ai-title">
                    <header class="gi-sidebar-header">
                        <h3 class="gi-sidebar-title" id="ai-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            AIアシスタント
                        </h3>
                    </header>
                    <div class="gi-ai-body">
                        <div class="gi-ai-messages" id="aiMessages" aria-live="polite">
                            <div class="gi-ai-msg">
                                <div class="gi-ai-avatar">AI</div>
                                <div class="gi-ai-bubble">この補助金について何でもお聞きください。対象者、金額、必要書類などお答えします。</div>
                            </div>
                        </div>
                        <div class="gi-ai-input-area">
                            <div class="gi-ai-input-wrap">
                                <textarea class="gi-ai-input" id="aiInput" placeholder="質問を入力..." rows="1" aria-label="AIへの質問"></textarea>
                                <button class="gi-ai-send" id="aiSend" type="button" aria-label="送信">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                </button>
                            </div>
                            <div class="gi-ai-suggestions">
                                <button class="gi-ai-chip" data-action="diagnosis">資格診断</button>
                                <button class="gi-ai-chip" data-action="roadmap">ロードマップ</button>
                                <button class="gi-ai-chip" data-q="対象者を教えて">対象者</button>
                                <button class="gi-ai-chip" data-q="補助金額は？">金額</button>
                                <button class="gi-ai-chip" data-q="必要書類は？">書類</button>
                                <button class="gi-ai-chip" data-q="申請方法は？">申請</button>
                                <button class="gi-ai-chip" data-q="締切はいつ？">締切</button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 目次 -->
                <section class="gi-sidebar-section">
                    <header class="gi-sidebar-header"><h3 class="gi-sidebar-title">目次</h3></header>
                    <div class="gi-sidebar-body">
                        <div class="gi-sidebar-list">
                            <?php foreach ($toc_items as $item): ?>
                            <div class="gi-sidebar-list-item">
                                <a href="#<?php echo esc_attr($item['id']); ?>" class="gi-sidebar-list-link">
                                   <div class="gi-sidebar-list-content"><div class="gi-sidebar-list-title"><?php echo esc_html($item['title']); ?></div></div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <!-- 広告枠（上部2） -->
                <?php if (function_exists('ji_display_ad')): ?>
                <section class="gi-sidebar-section gi-ad-section gi-ad-upper" aria-label="広告">
                    <header class="gi-sidebar-header"><span class="gi-sidebar-title gi-pr-label">PR</span></header>
                    <div class="gi-sidebar-body">
                        <div class="gi-ad-slot" id="adSlotUpper"><?php ji_display_ad('single_grant_sidebar_upper'); ?></div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- CTAボタン -->
                <section class="gi-sidebar-section">
                    <div class="gi-sidebar-body gi-cta-buttons">
                        <?php if ($grant['official_url']): ?>
                        <a href="<?php echo esc_url($grant['official_url']); ?>" class="gi-btn gi-btn-primary gi-btn-full" target="_blank" rel="noopener">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            公式サイトで申請
                        </a>
                        <?php endif; ?>
                        <?php if ($grant['application_url']): ?>
                        <a href="<?php echo esc_url($grant['application_url']); ?>" class="gi-btn gi-btn-accent gi-btn-full" target="_blank" rel="noopener">申請フォームへ</a>
                        <?php endif; ?>
                        <button class="gi-btn gi-btn-secondary gi-btn-full" id="bookmarkBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                            <span>保存する</span>
                        </button>
                        <button class="gi-btn gi-btn-secondary gi-btn-full" id="shareBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                            シェア
                        </button>
                    </div>
                </section>

                <!-- 広告枠（中部1） -->
                <?php if (function_exists('ji_display_ad')): ?>
                <section class="gi-sidebar-section gi-ad-section gi-ad-middle" aria-label="広告">
                    <header class="gi-sidebar-header"><span class="gi-sidebar-title gi-pr-label">PR</span></header>
                    <div class="gi-sidebar-body">
                        <div class="gi-ad-slot" id="adSlotMiddle1"><?php ji_display_ad('single_grant_sidebar_middle'); ?></div>
                    </div>
                </section>
                <?php endif; ?>
                
                <!-- 締切間近 -->
                <?php if ($deadline_soon_grants->have_posts()): ?>
                <section class="gi-sidebar-section" aria-labelledby="deadline-soon-title">
                    <header class="gi-sidebar-header">
                        <h3 class="gi-sidebar-title" id="deadline-soon-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            締切間近
                        </h3>
                    </header>
                    <div class="gi-sidebar-body">
                        <div class="gi-sidebar-list">
                            <?php while ($deadline_soon_grants->have_posts()): $deadline_soon_grants->the_post();
                                $item_deadline = gisg_get_field('deadline_date', get_the_ID());
                                $item_days = $item_deadline ? floor((strtotime($item_deadline) - current_time('timestamp')) / 86400) : 0;
                                $item_status = $item_days <= 7 ? 'urgent' : 'warning';
                            ?>
                            <div class="gi-sidebar-list-item">
                                <a href="<?php the_permalink(); ?>" class="gi-sidebar-list-link">
                                    <span class="gi-sidebar-rank <?php echo $item_status; ?>"><?php echo $item_days; ?>日</span>
                                    <div class="gi-sidebar-list-content">
                                        <div class="gi-sidebar-list-title"><?php the_title(); ?></div>
                                        <div class="gi-sidebar-list-meta"><?php echo date('n/j', strtotime($item_deadline)); ?>締切</div>
                                    </div>
                                </a>
                            </div>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- 人気ランキング -->
                <?php if ($popular_grants->have_posts()): ?>
                <section class="gi-sidebar-section" aria-labelledby="popular-title">
                    <header class="gi-sidebar-header">
                        <h3 class="gi-sidebar-title" id="popular-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            人気ランキング
                        </h3>
                    </header>
                    <div class="gi-sidebar-body">
                        <div class="gi-sidebar-list">
                            <?php $rank = 1; while ($popular_grants->have_posts()): $popular_grants->the_post();
                                $rank_class = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : ''));
                            ?>
                            <div class="gi-sidebar-list-item">
                                <a href="<?php the_permalink(); ?>" class="gi-sidebar-list-link">
                                    <span class="gi-sidebar-rank <?php echo $rank_class; ?>"><?php echo $rank; ?></span>
                                    <div class="gi-sidebar-list-content">
                                        <div class="gi-sidebar-list-title"><?php the_title(); ?></div>
                                        <div class="gi-sidebar-list-meta"><?php echo number_format(intval(gisg_get_field('views_count', get_the_ID(), 0))); ?>回閲覧</div>
                                    </div>
                                </a>
                            </div>
                            <?php $rank++; endwhile; wp_reset_postdata(); ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- 広告枠（下部2） -->
                <?php if (function_exists('ji_display_ad')): ?>
                <section class="gi-sidebar-section gi-ad-section gi-ad-lower" aria-label="広告">
                    <header class="gi-sidebar-header"><span class="gi-sidebar-title gi-pr-label">PR</span></header>
                    <div class="gi-sidebar-body">
                        <div class="gi-ad-slot" id="adSlotLower"><?php ji_display_ad('single_grant_sidebar_lower'); ?></div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- 広告枠（スティッキー対応） -->
                <?php if (function_exists('ji_display_ad')): ?>
                <section class="gi-sidebar-section gi-ad-section gi-ad-sticky" aria-label="広告">
                    <header class="gi-sidebar-header"><span class="gi-sidebar-title gi-pr-label">PR</span></header>
                    <div class="gi-sidebar-body">
                        <div class="gi-ad-slot" id="adSlotSticky"><?php ji_display_ad('single_grant_sidebar_sticky'); ?></div>
                    </div>
                </section>
                <?php endif; ?>
                
                <!-- おすすめコラム -->
                <?php if (!empty($recommended_columns)): ?>
                <section class="gi-sidebar-section" aria-labelledby="columns-title">
                    <header class="gi-sidebar-header">
                        <h3 class="gi-sidebar-title" id="columns-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                            関連コラム
                        </h3>
                    </header>
                    <div class="gi-sidebar-body">
                        <?php foreach ($recommended_columns as $col): ?>
                        <a href="<?php echo esc_url($col['permalink']); ?>" class="gi-column-card">
                            <div class="gi-column-thumb"><?php if ($col['thumbnail']): ?><img src="<?php echo esc_url($col['thumbnail']); ?>" alt="<?php echo esc_attr($col['title']); ?>" loading="lazy"><?php endif; ?></div>
                            <div class="gi-column-content">
                                <div class="gi-column-title"><?php echo esc_html($col['title']); ?></div>
                                <div class="gi-column-date"><?php echo esc_html($col['date']); ?></div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- 広告枠（下部） -->
                <?php if (function_exists('ji_display_ad')): ?>
                <section class="gi-sidebar-section gi-ad-section gi-ad-bottom" aria-label="広告">
                    <header class="gi-sidebar-header"><span class="gi-sidebar-title gi-pr-label">PR</span></header>
                    <div class="gi-sidebar-body">
                        <div class="gi-ad-slot" id="adSlotBottom"><?php ji_display_ad('single_grant_sidebar_bottom'); ?></div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- 関連カテゴリ -->
                <?php if (!empty($taxonomies['categories'])): ?>
                <section class="gi-sidebar-section">
                    <header class="gi-sidebar-header">
                        <h3 class="gi-sidebar-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            関連カテゴリ
                        </h3>
                    </header>
                    <div class="gi-sidebar-body">
                        <div class="gi-tags" style="flex-direction: column; gap: 8px;">
                            <?php foreach (array_slice($taxonomies['categories'], 0, 5) as $cat): 
                                $cat_link = get_term_link($cat);
                                if (is_wp_error($cat_link)) continue;
                            ?>
                            <a href="<?php echo esc_url($cat_link); ?>" class="gi-tag" style="justify-content: center; width: 100%;"><?php echo esc_html($cat->name); ?>の補助金</a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

            </aside>
        </div>
    </div>

    <!-- 関連補助金セクション - Section 07 -->
    <?php if (!empty($similar_grants)): ?>
    <section class="gi-related-v2" aria-labelledby="related-title">
        <div class="gi-container">
            <!-- セクションヘッダー - Section番号付き -->
            <header class="gi-related-v2-header">
                <div class="gi-related-v2-number-box">
                    <span class="gi-related-v2-number-label">Section</span>
                    <span class="gi-related-v2-number-value">07</span>
                </div>
                <div class="gi-related-v2-title-area">
                    <h2 class="gi-related-v2-title" id="related-title">関連補助金</h2>
                    <p class="gi-related-v2-sub">Related Grants</p>
                </div>
            </header>
            
            <!-- カードグリッド -->
            <div class="gi-related-v2-grid">
                <?php foreach ($similar_grants as $i => $sg): 
                    // 都道府県取得
                    $sg_prefectures = get_the_terms($sg['id'], 'grant_prefecture');
                    $sg_pref_name = '';
                    if ($sg_prefectures && !is_wp_error($sg_prefectures)) {
                        $sg_pref_name = $sg_prefectures[0]->name;
                    }
                    // 対象者取得（カスタムフィールド）
                    $sg_target = get_field('grant_target', $sg['id']);
                    $sg_target_short = $sg_target ? mb_substr(wp_strip_all_tags($sg_target), 0, 50) . '...' : '';
                ?>
                <article class="gi-related-v2-card">
                    <a href="<?php echo esc_url($sg['permalink']); ?>" class="gi-related-v2-card-link">
                        <!-- PICK UP ラベル -->
                        <div class="gi-related-v2-pickup">
                            <span class="gi-related-v2-pickup-label">PICK UP NO.<?php echo $i + 1; ?></span>
                        </div>
                        
                        <!-- カードヘッダー：タイトル -->
                        <h3 class="gi-related-v2-card-title">
                            <?php if ($sg_pref_name): ?>
                            <span class="gi-related-v2-card-pref">【<?php echo esc_html($sg_pref_name); ?>】</span>
                            <?php endif; ?>
                            <?php echo esc_html($sg['title']); ?>
                        </h3>
                        
                        <!-- 難易度 -->
                        <div class="gi-related-v2-card-difficulty">
                            <span class="gi-related-v2-card-difficulty-label">★ 難易度：</span>
                            <span class="gi-related-v2-card-difficulty-value">高</span>
                        </div>
                        
                        <!-- 金額バッジ -->
                        <?php if ($sg['max_amount']): ?>
                        <div class="gi-related-v2-card-amount">
                            <span class="gi-related-v2-card-amount-badge"><?php echo esc_html($sg['max_amount']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <!-- 説明文 -->
                        <p class="gi-related-v2-card-desc">
                            <?php 
                            $sg_excerpt = get_field('ai_summary', $sg['id']);
                            if (!$sg_excerpt) {
                                $sg_excerpt = get_the_excerpt($sg['id']);
                            }
                            echo esc_html(mb_substr(wp_strip_all_tags($sg_excerpt), 0, 120));
                            ?>...
                        </p>
                        
                        <!-- 対象者 -->
                        <?php if ($sg_target_short): ?>
                        <div class="gi-related-v2-card-target">
                            <span class="gi-related-v2-card-target-label">対象：</span>
                            <span class="gi-related-v2-card-target-value"><?php echo esc_html($sg_target_short); ?></span>
                        </div>
                        <?php endif; ?>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<!-- モバイルFAB -->
<div class="gi-mobile-fab">
    <button class="gi-fab-btn" id="mobileAiBtn" type="button" aria-label="AIアシスタントを開く">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span>AI相談</span>
    </button>
</div>
<!-- モバイルパネル -->
<div class="gi-mobile-overlay" id="mobileOverlay"></div>
<div class="gi-mobile-panel" id="mobilePanel">
    <div class="gi-panel-handle"></div>
    <header class="gi-panel-header">
        <h2 class="gi-panel-title">AIアシスタント</h2>
        <button class="gi-panel-close" id="panelClose" type="button" aria-label="閉じる">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </header>
    <div class="gi-panel-tabs">
        <button class="gi-panel-tab active" data-tab="ai">AI質問</button>
        <button class="gi-panel-tab" data-tab="toc">目次</button>
        <button class="gi-panel-tab" data-tab="action">アクション</button>
    </div>
    <div class="gi-panel-content">
        <div class="gi-panel-content-tab active" id="tabAi">
            <div class="gi-mobile-ai-messages" id="mobileAiMessages">
                <div class="gi-ai-msg"><div class="gi-ai-avatar">AI</div><div class="gi-ai-bubble">この補助金について何でもお聞きください。</div></div>
            </div>
            <div class="gi-mobile-ai-input-wrap">
                <textarea class="gi-mobile-ai-input" id="mobileAiInput" placeholder="質問を入力..." rows="1" aria-label="AIへの質問"></textarea>
                <button class="gi-mobile-ai-send" id="mobileAiSend" type="button" aria-label="送信">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
            <div class="gi-mobile-ai-chips">
                <button class="gi-mobile-ai-chip" data-action="diagnosis">資格診断</button>
                <button class="gi-mobile-ai-chip" data-action="roadmap">ロードマップ</button>
                <button class="gi-mobile-ai-chip" data-q="対象者を教えて">対象者</button>
                <button class="gi-mobile-ai-chip" data-q="補助金額は？">金額</button>
                <button class="gi-mobile-ai-chip" data-q="必要書類は？">書類</button>
                <button class="gi-mobile-ai-chip" data-q="申請方法は？">申請</button>
            </div>
        </div>
        <div class="gi-panel-content-tab" id="tabToc">
            <div class="gi-sidebar-list">
                <?php foreach ($toc_items as $item): ?>
                <div class="gi-sidebar-list-item">
                    <a href="#<?php echo esc_attr($item['id']); ?>" class="gi-sidebar-list-link mobile-toc-link">
                        <div class="gi-sidebar-list-content"><div class="gi-sidebar-list-title"><?php echo esc_html($item['title']); ?></div></div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="gi-panel-content-tab" id="tabAction">
            <div class="gi-cta-buttons">
                <?php if ($grant['official_url']): ?>
                <a href="<?php echo esc_url($grant['official_url']); ?>" class="gi-btn gi-btn-primary gi-btn-full" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    公式サイトで申請
                </a>
                <?php endif; ?>
                <?php if ($grant['application_url']): ?>
                <a href="<?php echo esc_url($grant['application_url']); ?>" class="gi-btn gi-btn-accent gi-btn-full" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    申請フォームへ
                </a>
                <?php endif; ?>
                <button class="gi-btn gi-btn-secondary gi-btn-full" id="mobileBookmarkBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    <span>保存する</span>
                </button>
                <button class="gi-btn gi-btn-secondary gi-btn-full" id="mobileShareBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    シェアする
                </button>
                <button class="gi-btn gi-btn-secondary gi-btn-full" id="mobilePrintBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    印刷する
                </button>
            </div>
        </div>
    </div>
</div>

<!-- トースト通知 -->
<div class="gi-toast" id="giToast"></div>

<!-- JavaScript設定 - CONFIG のみ (ロジックは assets/js/single-grant.js に外部化済み) -->
<script>
var CONFIG = {
    postId: <?php echo $post_id; ?>,
    ajaxUrl: '<?php echo admin_url("admin-ajax.php"); ?>',
    nonce: '<?php echo wp_create_nonce("gi_ai_nonce"); ?>',
    url: '<?php echo esc_js($canonical_url); ?>',
    title: <?php echo json_encode(get_the_title(), JSON_UNESCAPED_UNICODE); ?>,
    totalChecklist: <?php echo count($checklist_items); ?>,
    grantData: {
        organization: <?php echo json_encode($grant['organization'], JSON_UNESCAPED_UNICODE); ?>,
        maxAmount: <?php echo json_encode($amount_display, JSON_UNESCAPED_UNICODE); ?>,
        subsidyRate: <?php echo json_encode($subsidy_rate_display, JSON_UNESCAPED_UNICODE); ?>,
        deadline: <?php echo json_encode($deadline_info, JSON_UNESCAPED_UNICODE); ?>,
        daysRemaining: <?php echo $days_remaining; ?>,
        target: <?php echo json_encode(strip_tags($grant['grant_target']), JSON_UNESCAPED_UNICODE); ?>,
        documents: <?php echo json_encode(strip_tags($docs), JSON_UNESCAPED_UNICODE); ?>,
        expenses: <?php echo json_encode(strip_tags($expenses), JSON_UNESCAPED_UNICODE); ?>,
        difficulty: <?php echo json_encode($difficulty['label'], JSON_UNESCAPED_UNICODE); ?>,
        adoptionRate: <?php echo $grant['adoption_rate']; ?>,
        onlineApplication: <?php echo $grant['online_application'] ? 'true' : 'false'; ?>,
        jgrantsAvailable: <?php echo $grant['jgrants_available'] ? 'true' : 'false'; ?>,
        officialUrl: <?php echo json_encode($grant['official_url'], JSON_UNESCAPED_UNICODE); ?>,
        preparationDays: <?php echo $grant['preparation_days']; ?>,
        reviewPeriod: <?php echo json_encode($grant['review_period'], JSON_UNESCAPED_UNICODE); ?>
    }
};
</script>

<?php get_footer(); ?>
