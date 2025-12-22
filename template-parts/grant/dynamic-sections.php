<?php
/**
 * Dynamic Sections for Archive Pages - 補助金図鑑 Book Style Edition
 * 都道府県・市町村アーカイブページ用の動的コンテンツセクション
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0 - Book Encyclopedia Style
 * 
 * 使用方法:
 * include(get_template_directory() . '/template-parts/grant/dynamic-sections.php');
 * gi_render_dynamic_sections($context);
 * 
 * $context = [
 *     'type' => 'prefecture' | 'municipality',
 *     'term_id' => int,
 *     'term_name' => string,
 *     'term_slug' => string,
 *     'parent_prefecture' => array (市町村の場合)
 * ]
 */

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

/**
 * 動的セクションをレンダリング
 */
function gi_render_dynamic_sections($context) {
    $type = $context['type'] ?? 'prefecture';
    $term_id = $context['term_id'] ?? 0;
    $term_name = $context['term_name'] ?? '';
    $term_slug = $context['term_slug'] ?? '';
    $parent_prefecture = $context['parent_prefecture'] ?? null;
    
    // タイプに応じたタクソノミーを設定
    switch ($type) {
        case 'municipality':
            $taxonomy = 'grant_municipality';
            break;
        case 'category':
            $taxonomy = 'grant_category';
            break;
        case 'purpose':
            $taxonomy = 'grant_purpose';
            break;
        case 'tag':
            $taxonomy = 'grant_tag';
            break;
        default:
            $taxonomy = 'grant_prefecture';
    }
    $current_year = date('Y');
    $current_month = date('n');
    ?>
    
    <!-- 📚 動的コンテンツエリア - 補助金図鑑スタイル -->
    <div class="gi-dynamic-content book-encyclopedia-style">
        
        <!-- 📖 章1: 最近追加された補助金 -->
        <?php gi_render_recently_added_section($term_id, $term_name, $taxonomy); ?>
        
        <!-- 📖 章2: 今週の締切 -->
        <?php gi_render_deadline_this_week_section($term_id, $term_name, $taxonomy); ?>
        
        <!-- 📖 章3: 注目のピックアップ -->
        <?php gi_render_featured_pickup_section($term_id, $term_name, $taxonomy); ?>
        
        <!-- 📖 章4: カテゴリ別統計（カテゴリアーカイブでは都道府県別統計を表示） -->
        <?php if ($type === 'category'): ?>
        <?php gi_render_prefecture_stats_section($term_id, $term_name, $taxonomy); ?>
        <?php else: ?>
        <?php gi_render_category_stats_section($term_id, $term_name, $taxonomy); ?>
        <?php endif; ?>
        
        <!-- 📖 章5: 金額帯別ガイド -->
        <?php gi_render_amount_guide_section($term_id, $term_name, $taxonomy); ?>
        
    </div>
    
    <?php
}

/**
 * 最近追加された補助金セクション
 */
function gi_render_recently_added_section($term_id, $term_name, $taxonomy) {
    $recent_query = new WP_Query([
        'post_type' => 'grant',
        'posts_per_page' => 6,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'date_query' => [
            ['after' => '2 weeks ago']
        ],
        'tax_query' => [
            [
                'taxonomy' => $taxonomy,
                'field' => 'term_id',
                'terms' => $term_id
            ]
        ],
        'no_found_rows' => true
    ]);
    
    if (!$recent_query->have_posts()) {
        wp_reset_postdata();
        return;
    }
    ?>
    
    <section class="gi-section gi-recently-added book-chapter" aria-label="最近追加された補助金">
        <div class="chapter-header">
            <div class="chapter-number">
                <span class="chapter-label">Chapter</span>
                <span class="chapter-num">01</span>
            </div>
            <div class="chapter-title-area">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </span>
                    <span class="title-main">最近追加された<?php echo esc_html($term_name); ?>の補助金</span>
                </h2>
                <p class="section-subtitle">ここ2週間以内に追加された最新の補助金・助成金情報</p>
            </div>
            <div class="new-badge pulse-animation">
                <span>NEW</span>
            </div>
        </div>
        
        <div class="section-content">
            <div class="book-page-grid">
                <?php 
                $counter = 0;
                while ($recent_query->have_posts()): 
                    $recent_query->the_post();
                    $counter++;
                    $post_id = get_the_ID();
                    $deadline_date = get_field('deadline_date', $post_id) ?: '';
                    $max_amount = get_field('max_subsidy_amount', $post_id) ?: '';
                    $days_ago = human_time_diff(get_the_time('U'), current_time('timestamp'));
                    ?>
                    <article class="book-entry entry-<?php echo $counter; ?>">
                        <div class="entry-ribbon">
                            <span class="ribbon-text"><?php echo $days_ago; ?>前</span>
                        </div>
                        <div class="entry-content">
                            <h3 class="entry-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <div class="entry-meta">
                                <?php if ($max_amount): ?>
                                <span class="meta-amount">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M12 6v6l4 2"/>
                                    </svg>
                                    最大<?php echo number_format(intval($max_amount)); ?>万円
                                </span>
                                <?php endif; ?>
                                <?php if ($deadline_date): ?>
                                <span class="meta-deadline">
                                    締切: <?php echo date('Y/m/d', strtotime($deadline_date)); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="entry-arrow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        
        <div class="chapter-footer">
            <div class="page-turn-indicator">
                <span class="page-num">P.1</span>
            </div>
        </div>
    </section>
    
    <?php
}

/**
 * 今週の締切セクション
 */
function gi_render_deadline_this_week_section($term_id, $term_name, $taxonomy) {
    $today = date('Y-m-d');
    $week_later = date('Y-m-d', strtotime('+7 days'));
    
    $deadline_query = new WP_Query([
        'post_type' => 'grant',
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'meta_key' => 'deadline_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'deadline_date',
                'value' => $today,
                'compare' => '>=',
                'type' => 'DATE'
            ],
            [
                'key' => 'deadline_date',
                'value' => $week_later,
                'compare' => '<=',
                'type' => 'DATE'
            ]
        ],
        'tax_query' => [
            [
                'taxonomy' => $taxonomy,
                'field' => 'term_id',
                'terms' => $term_id
            ]
        ],
        'no_found_rows' => true
    ]);
    
    if (!$deadline_query->have_posts()) {
        wp_reset_postdata();
        return;
    }
    ?>
    
    <section class="gi-section gi-deadline-week book-chapter urgent-chapter" aria-label="今週の締切">
        <div class="chapter-header">
            <div class="chapter-number">
                <span class="chapter-label">Chapter</span>
                <span class="chapter-num">02</span>
            </div>
            <div class="chapter-title-area">
                <h2 class="section-title urgent-title">
                    <span class="title-icon urgent-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </span>
                    <span class="title-main">今週締切の<?php echo esc_html($term_name); ?>補助金</span>
                </h2>
                <p class="section-subtitle">7日以内に申請締切を迎える補助金・助成金</p>
            </div>
            <div class="urgent-badge pulse-animation">
                <span>締切間近</span>
            </div>
        </div>
        
        <div class="section-content">
            <div class="deadline-list">
                <?php 
                while ($deadline_query->have_posts()): 
                    $deadline_query->the_post();
                    $post_id = get_the_ID();
                    $deadline_date = get_field('deadline_date', $post_id) ?: '';
                    $deadline_ts = strtotime($deadline_date);
                    $days_left = ceil(($deadline_ts - current_time('timestamp')) / DAY_IN_SECONDS);
                    $max_amount = get_field('max_subsidy_amount', $post_id) ?: '';
                    $urgency_class = ($days_left <= 3) ? 'critical' : (($days_left <= 5) ? 'warning' : 'soon');
                    ?>
                    <article class="deadline-entry urgency-<?php echo $urgency_class; ?>">
                        <div class="deadline-countdown">
                            <span class="countdown-num"><?php echo $days_left; ?></span>
                            <span class="countdown-unit">日後</span>
                        </div>
                        <div class="deadline-content">
                            <h3 class="deadline-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <div class="deadline-meta">
                                <span class="deadline-date">
                                    <?php echo date('n月j日', $deadline_ts); ?>（<?php echo ['日','月','火','水','木','金','土'][date('w', $deadline_ts)]; ?>）締切
                                </span>
                                <?php if ($max_amount): ?>
                                <span class="deadline-amount">最大<?php echo number_format(intval($max_amount)); ?>万円</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="deadline-action">
                            <a href="<?php the_permalink(); ?>" class="action-btn">詳細</a>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        
        <div class="chapter-footer">
            <div class="page-turn-indicator">
                <span class="page-num">P.2</span>
            </div>
        </div>
    </section>
    
    <?php
}

/**
 * 注目のピックアップセクション
 */
function gi_render_featured_pickup_section($term_id, $term_name, $taxonomy) {
    // 注目フラグがある、または閲覧数が多い補助金を取得
    $featured_query = new WP_Query([
        'post_type' => 'grant',
        'posts_per_page' => 4,
        'post_status' => 'publish',
        'meta_query' => [
            'relation' => 'OR',
            [
                'key' => 'is_featured',
                'value' => '1',
                'compare' => '='
            ],
            [
                'key' => 'adoption_rate',
                'value' => '60',
                'compare' => '>=',
                'type' => 'NUMERIC'
            ]
        ],
        'tax_query' => [
            [
                'taxonomy' => $taxonomy,
                'field' => 'term_id',
                'terms' => $term_id
            ]
        ],
        'no_found_rows' => true
    ]);
    
    if (!$featured_query->have_posts()) {
        wp_reset_postdata();
        return;
    }
    ?>
    
    <section class="gi-section gi-featured-pickup book-chapter featured-chapter" aria-label="注目の補助金">
        <div class="chapter-header">
            <div class="chapter-number">
                <span class="chapter-label">Chapter</span>
                <span class="chapter-num">03</span>
            </div>
            <div class="chapter-title-area">
                <h2 class="section-title featured-title">
                    <span class="title-icon featured-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </span>
                    <span class="title-main"><?php echo esc_html($term_name); ?>の注目補助金</span>
                </h2>
                <p class="section-subtitle">高採択率・注目度の高い補助金をピックアップ</p>
            </div>
            <div class="featured-badge">
                <span>PICK UP</span>
            </div>
        </div>
        
        <div class="section-content">
            <div class="featured-cards-grid">
                <?php 
                $rank = 0;
                while ($featured_query->have_posts()): 
                    $featured_query->the_post();
                    $rank++;
                    $post_id = get_the_ID();
                    $adoption_rate = get_field('adoption_rate', $post_id) ?: '';
                    $max_amount = get_field('max_subsidy_amount', $post_id) ?: '';
                    $ai_summary = get_field('ai_summary', $post_id) ?: get_the_excerpt();
                    $is_featured = get_field('is_featured', $post_id);
                    ?>
                    <article class="featured-card rank-<?php echo $rank; ?>">
                        <?php if ($is_featured): ?>
                        <div class="featured-ribbon">
                            <span>注目</span>
                        </div>
                        <?php endif; ?>
                        <div class="card-rank-badge">
                            <span><?php echo $rank; ?></span>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <p class="card-excerpt"><?php echo wp_trim_words($ai_summary, 40, '...'); ?></p>
                            <div class="card-stats">
                                <?php if ($adoption_rate): ?>
                                <div class="stat-item success">
                                    <span class="stat-label">採択率</span>
                                    <span class="stat-value"><?php echo esc_html($adoption_rate); ?>%</span>
                                </div>
                                <?php endif; ?>
                                <?php if ($max_amount): ?>
                                <div class="stat-item gold">
                                    <span class="stat-label">上限</span>
                                    <span class="stat-value"><?php echo number_format(intval($max_amount)); ?>万</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="card-link-overlay" aria-label="<?php the_title(); ?>の詳細を見る"></a>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        
        <div class="chapter-footer">
            <div class="page-turn-indicator">
                <span class="page-num">P.3</span>
            </div>
        </div>
    </section>
    
    <?php
}

/**
 * カテゴリ別統計セクション
 */
function gi_render_category_stats_section($term_id, $term_name, $taxonomy) {
    // この地域の補助金のカテゴリ別集計
    $categories = get_terms([
        'taxonomy' => 'grant_category',
        'hide_empty' => true
    ]);
    
    if (empty($categories) || is_wp_error($categories)) {
        return;
    }
    
    $category_counts = [];
    foreach ($categories as $cat) {
        $count_query = new WP_Query([
            'post_type' => 'grant',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'tax_query' => [
                'relation' => 'AND',
                [
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => $term_id
                ],
                [
                    'taxonomy' => 'grant_category',
                    'field' => 'term_id',
                    'terms' => $cat->term_id
                ]
            ],
            'no_found_rows' => false
        ]);
        
        if ($count_query->found_posts > 0) {
            $category_counts[] = [
                'name' => $cat->name,
                'slug' => $cat->slug,
                'count' => $count_query->found_posts,
                'link' => add_query_arg('category', $cat->slug, get_term_link($term_id, $taxonomy))
            ];
        }
        wp_reset_postdata();
    }
    
    // 件数順にソート
    usort($category_counts, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    
    // 上位8件のみ
    $category_counts = array_slice($category_counts, 0, 8);
    
    if (empty($category_counts)) {
        return;
    }
    
    $max_count = max(array_column($category_counts, 'count'));
    ?>
    
    <section class="gi-section gi-category-stats book-chapter stats-chapter" aria-label="カテゴリ別統計">
        <div class="chapter-header">
            <div class="chapter-number">
                <span class="chapter-label">Chapter</span>
                <span class="chapter-num">04</span>
            </div>
            <div class="chapter-title-area">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                        </svg>
                    </span>
                    <span class="title-main"><?php echo esc_html($term_name); ?>の補助金カテゴリ</span>
                </h2>
                <p class="section-subtitle">分野別の補助金・助成金件数</p>
            </div>
        </div>
        
        <div class="section-content">
            <div class="category-chart">
                <?php foreach ($category_counts as $cat): 
                    $percentage = round(($cat['count'] / $max_count) * 100);
                    ?>
                    <a href="<?php echo esc_url($cat['link']); ?>" class="chart-bar-link">
                        <div class="chart-bar">
                            <div class="bar-label"><?php echo esc_html($cat['name']); ?></div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width: <?php echo $percentage; ?>%">
                                    <span class="bar-count"><?php echo $cat['count']; ?>件</span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="chapter-footer">
            <div class="page-turn-indicator">
                <span class="page-num">P.4</span>
            </div>
        </div>
    </section>
    
    <?php
}

/**
 * 都道府県別統計セクション（カテゴリアーカイブ用）
 */
function gi_render_prefecture_stats_section($term_id, $term_name, $taxonomy) {
    // このカテゴリの補助金の都道府県別集計
    $prefectures = get_terms([
        'taxonomy' => 'grant_prefecture',
        'hide_empty' => true
    ]);
    
    if (empty($prefectures) || is_wp_error($prefectures)) {
        return;
    }
    
    $pref_counts = [];
    foreach ($prefectures as $pref) {
        $count_query = new WP_Query([
            'post_type' => 'grant',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'tax_query' => [
                'relation' => 'AND',
                [
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => $term_id
                ],
                [
                    'taxonomy' => 'grant_prefecture',
                    'field' => 'term_id',
                    'terms' => $pref->term_id
                ]
            ],
            'no_found_rows' => false
        ]);
        
        if ($count_query->found_posts > 0) {
            $pref_counts[] = [
                'name' => $pref->name,
                'slug' => $pref->slug,
                'count' => $count_query->found_posts,
                'link' => get_term_link($pref->term_id, 'grant_prefecture')
            ];
        }
        wp_reset_postdata();
    }
    
    // 件数順にソート
    usort($pref_counts, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    
    // 上位10件のみ
    $pref_counts = array_slice($pref_counts, 0, 10);
    
    if (empty($pref_counts)) {
        return;
    }
    
    $max_count = max(array_column($pref_counts, 'count'));
    ?>
    
    <section class="gi-section gi-prefecture-stats book-chapter stats-chapter" aria-label="都道府県別統計">
        <div class="chapter-header">
            <div class="chapter-number">
                <span class="chapter-label">Chapter</span>
                <span class="chapter-num">04</span>
            </div>
            <div class="chapter-title-area">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </span>
                    <span class="title-main"><?php echo esc_html($term_name); ?>の補助金 都道府県別</span>
                </h2>
                <p class="section-subtitle">地域別の補助金・助成金件数（上位10都道府県）</p>
            </div>
        </div>
        
        <div class="section-content">
            <div class="category-chart">
                <?php foreach ($pref_counts as $pref): 
                    $percentage = round(($pref['count'] / $max_count) * 100);
                    ?>
                    <a href="<?php echo esc_url($pref['link']); ?>" class="chart-bar-link">
                        <div class="chart-bar">
                            <div class="bar-label"><?php echo esc_html($pref['name']); ?></div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width: <?php echo $percentage; ?>%">
                                    <span class="bar-count"><?php echo $pref['count']; ?>件</span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="chapter-footer">
            <div class="page-turn-indicator">
                <span class="page-num">P.4</span>
            </div>
        </div>
    </section>
    
    <?php
}

/**
 * 金額帯別ガイドセクション
 */
function gi_render_amount_guide_section($term_id, $term_name, $taxonomy) {
    $amount_ranges = [
        ['min' => 0, 'max' => 100, 'label' => '〜100万円', 'class' => 'small'],
        ['min' => 100, 'max' => 500, 'label' => '100〜500万円', 'class' => 'medium'],
        ['min' => 500, 'max' => 1000, 'label' => '500〜1000万円', 'class' => 'large'],
        ['min' => 1000, 'max' => 5000, 'label' => '1000〜5000万円', 'class' => 'xlarge'],
        ['min' => 5000, 'max' => 999999, 'label' => '5000万円以上', 'class' => 'mega']
    ];
    
    $amount_data = [];
    foreach ($amount_ranges as $range) {
        $query = new WP_Query([
            'post_type' => 'grant',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'max_subsidy_amount',
                    'value' => $range['min'],
                    'compare' => '>=',
                    'type' => 'NUMERIC'
                ],
                [
                    'key' => 'max_subsidy_amount',
                    'value' => $range['max'],
                    'compare' => '<',
                    'type' => 'NUMERIC'
                ]
            ],
            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => $term_id
                ]
            ],
            'no_found_rows' => false
        ]);
        
        if ($query->found_posts > 0) {
            $amount_data[] = [
                'label' => $range['label'],
                'class' => $range['class'],
                'count' => $query->found_posts,
                'min' => $range['min'],
                'max' => $range['max']
            ];
        }
        wp_reset_postdata();
    }
    
    if (empty($amount_data)) {
        return;
    }
    ?>
    
    <section class="gi-section gi-amount-guide book-chapter guide-chapter" aria-label="金額帯別ガイド">
        <div class="chapter-header">
            <div class="chapter-number">
                <span class="chapter-label">Chapter</span>
                <span class="chapter-num">05</span>
            </div>
            <div class="chapter-title-area">
                <h2 class="section-title">
                    <span class="title-icon gold-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </span>
                    <span class="title-main"><?php echo esc_html($term_name); ?>の補助金 金額帯別ガイド</span>
                </h2>
                <p class="section-subtitle">事業規模に合わせた補助金を見つけましょう</p>
            </div>
        </div>
        
        <div class="section-content">
            <div class="amount-guide-grid">
                <?php foreach ($amount_data as $data): ?>
                    <div class="amount-card amount-<?php echo $data['class']; ?>">
                        <div class="amount-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                <path d="M16 7V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v3"/>
                            </svg>
                        </div>
                        <div class="amount-label"><?php echo esc_html($data['label']); ?></div>
                        <div class="amount-count"><?php echo $data['count']; ?><span class="count-unit">件</span></div>
                        <a href="<?php echo esc_url(add_query_arg('amount', $data['min'] . '-' . $data['max'], get_term_link($term_id, $taxonomy))); ?>" class="amount-link">
                            この金額帯を見る →
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="chapter-footer">
            <div class="page-turn-indicator">
                <span class="page-num">P.5</span>
            </div>
        </div>
    </section>
    
    <?php
}
