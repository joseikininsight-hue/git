<?php
/**
 * Template Part: Grant & Column Information Hub
 * コラム・補助金・最新情報セクション v38.0
 * 
 * @package Grant_Insight_Perfect
 * @version 38.0.0 - Clean Government Style Design
 * 
 * Design Concept:
 * - 官公庁風のシンプルで信頼感あるデザイン
 * - 白背景ベース + 濃紺×金のアクセント
 * - 企業サイト風の堅実なイメージ
 */

if (!defined('ABSPATH')) exit;

// ==========================================================================
// データ取得
// ==========================================================================

$today = date('Y-m-d');
$two_weeks_later = date('Y-m-d', strtotime('+14 days'));

$common_args = [
    'post_status' => 'publish',
    'no_found_rows' => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
];

// 注目の補助金
$grants_featured = new WP_Query(array_merge($common_args, [
    'post_type' => 'grant',
    'posts_per_page' => 3,
    'meta_key' => 'is_featured',
    'meta_value' => '1',
    'orderby' => 'date',
    'order' => 'DESC',
]));

// 締切間近の補助金
$grants_deadline = new WP_Query(array_merge($common_args, [
    'post_type' => 'grant',
    'posts_per_page' => 5,
    'meta_key' => 'deadline_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'meta_query' => [
        [
            'key' => 'deadline_date',
            'value' => $today,
            'compare' => '>=',
            'type' => 'DATE'
        ]
    ],
]));

// 新着補助金
$grants_new = new WP_Query(array_merge($common_args, [
    'post_type' => 'grant',
    'posts_per_page' => 5,
    'orderby' => 'date',
    'order' => 'DESC',
]));

// 新着コラム
$columns_new = new WP_Query(array_merge($common_args, [
    'post_type' => 'column',
    'posts_per_page' => 4,
    'orderby' => 'date',
    'order' => 'DESC',
]));

// 人気コラム
$columns_popular = new WP_Query(array_merge($common_args, [
    'post_type' => 'column',
    'posts_per_page' => 5,
    'meta_key' => 'view_count',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
]));

// カウントデータ
$count_grants = wp_count_posts('grant')->publish ?? 0;
$count_columns = wp_count_posts('column')->publish ?? 0;
$count_news = wp_count_posts('post')->publish ?? 0;
?>

<!-- ==========================================================================
     コラム・補助金・最新情報ハブ v38.0
     ========================================================================== -->
<section class="info-hub" id="main-info-hub">
    <div class="info-hub__container">
        
        <!-- Section Header -->
        <header class="info-hub__header">
            <div class="info-hub__header-main">
                <h2 class="info-hub__title">コラム・補助金・最新情報</h2>
                <p class="info-hub__description">
                    コラム<span class="info-hub__count"><?php echo number_format($count_columns); ?></span>件、
                    補助金<span class="info-hub__count"><?php echo number_format($count_grants); ?></span>件の情報を掲載。
                    事業に役立つ最新情報をお届けします。
                </p>
            </div>
            
            <!-- Stats Bar -->
            <div class="info-hub__stats">
                <div class="info-hub__stat">
                    <span class="info-hub__stat-label">COLUMNS</span>
                    <span class="info-hub__stat-number"><?php echo number_format($count_columns); ?></span>
                </div>
                <div class="info-hub__stat">
                    <span class="info-hub__stat-label">GRANTS</span>
                    <span class="info-hub__stat-number"><?php echo number_format($count_grants); ?></span>
                </div>
                <div class="info-hub__stat info-hub__stat--muted">
                    <span class="info-hub__stat-label">NEWS</span>
                    <span class="info-hub__stat-number"><?php echo number_format($count_news); ?></span>
                </div>
            </div>
        </header>

        <!-- Columns & Ranking Grid -->
        <div class="info-hub__columns-grid">
            
            <!-- 新着コラム -->
            <section class="info-hub__section">
                <div class="info-hub__section-header">
                    <h3 class="info-hub__section-title">
                        <span class="info-hub__section-bar"></span>
                        新着コラム
                        <span class="info-hub__section-sub">専門家による最新の解説記事</span>
                    </h3>
                    <a href="<?php echo esc_url(home_url('/columns/')); ?>" class="info-hub__link">View All →</a>
                </div>

                <?php if ($columns_new->have_posts()) : ?>
                <div class="info-hub__articles">
                    <?php while ($columns_new->have_posts()) : $columns_new->the_post(); 
                        $categories = get_the_terms(get_the_ID(), 'column_category');
                        $cat_name = $categories && !is_wp_error($categories) ? $categories[0]->name : '解説';
                    ?>
                    <article class="article-card">
                        <a href="<?php the_permalink(); ?>" class="article-card__link">
                            <div class="article-card__image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium', ['class' => 'article-card__thumb', 'loading' => 'lazy']); ?>
                                <?php else : ?>
                                    <div class="article-card__no-image"></div>
                                <?php endif; ?>
                                <span class="article-card__badge"><?php echo esc_html($cat_name); ?></span>
                            </div>
                            <div class="article-card__body">
                                <time class="article-card__date"><?php echo get_the_date('Y.m.d'); ?></time>
                                <h4 class="article-card__title"><?php the_title(); ?></h4>
                            </div>
                        </a>
                    </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <?php else : ?>
                <div class="info-hub__empty">現在、コラムはありません</div>
                <?php endif; ?>
            </section>

            <!-- よく読まれている記事 -->
            <section class="info-hub__section">
                <div class="info-hub__section-header">
                    <h3 class="info-hub__section-title">
                        <span class="info-hub__section-bar"></span>
                        よく読まれている記事
                        <span class="info-hub__section-sub">多くの方に支持されているコラム</span>
                    </h3>
                    <a href="<?php echo esc_url(home_url('/columns/')); ?>" class="info-hub__link">View All Columns →</a>
                </div>

                <?php if ($columns_popular->have_posts()) : ?>
                <div class="ranking-list">
                    <div class="ranking-list__binding"></div>
                    <ol class="ranking-list__items">
                        <?php $rank = 1; while ($columns_popular->have_posts()) : $columns_popular->the_post(); 
                            $view_count = get_post_meta(get_the_ID(), 'view_count', true);
                        ?>
                        <li class="ranking-item <?php echo $rank <= 3 ? 'ranking-item--top' : 'ranking-item--rest'; ?>">
                            <span class="ranking-item__rank <?php echo $rank === 1 ? 'ranking-item__rank--gold' : ($rank <= 3 ? 'ranking-item__rank--silver' : ''); ?>"><?php echo $rank; ?></span>
                            <a href="<?php the_permalink(); ?>" class="ranking-item__link">
                                <h4 class="ranking-item__title"><?php the_title(); ?></h4>
                                <div class="ranking-item__meta">
                                    <time><?php echo get_the_date('Y.m.d'); ?></time>
                                    <?php if ($view_count) : ?>
                                    <span class="ranking-item__pv"><?php echo number_format($view_count); ?> PV</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </li>
                        <?php $rank++; endwhile; wp_reset_postdata(); ?>
                    </ol>
                </div>
                <?php else : ?>
                <div class="info-hub__empty">現在、人気コラムはありません</div>
                <?php endif; ?>
            </section>

        </div>

        <!-- Grant Information Section (Tabs) -->
        <section class="grant-tabs">
            <div class="grant-tabs__decoration"></div>

            <!-- Tabs Navigation -->
            <div class="grant-tabs__nav">
                <button class="grant-tabs__btn active" data-tab="featured" type="button">
                    注目の補助金
                </button>
                <button class="grant-tabs__btn" data-tab="deadline" type="button">
                    締切間近
                    <span class="grant-tabs__badge grant-tabs__badge--red">急</span>
                </button>
                <button class="grant-tabs__btn" data-tab="new" type="button">
                    新着補助金
                    <span class="grant-tabs__badge grant-tabs__badge--gold">New</span>
                </button>
            </div>

            <!-- Tab: Featured -->
            <div class="grant-tabs__panel active" id="panel-featured">
                <p class="grant-tabs__panel-desc">専門家が厳選した、今申請すべき補助金制度</p>
                
                <?php if ($grants_featured->have_posts()) : ?>
                <div class="grant-cards">
                    <?php while ($grants_featured->have_posts()) : $grants_featured->the_post(); 
                        $limit = get_post_meta(get_the_ID(), 'limit_amount', true);
                        $deadline = get_post_meta(get_the_ID(), 'deadline_date', true);
                        $is_urgent = $deadline && $deadline <= $two_weeks_later;
                    ?>
                    <article class="grant-card">
                        <div class="grant-card__top-line"></div>
                        <div class="grant-card__badges">
                            <span class="grant-card__badge grant-card__badge--gold">注目</span>
                            <?php if ($is_urgent) : ?>
                            <span class="grant-card__badge grant-card__badge--red">締切間近</span>
                            <?php endif; ?>
                        </div>
                        <h3 class="grant-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <?php if (has_excerpt()) : ?>
                        <p class="grant-card__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 40); ?></p>
                        <?php endif; ?>
                        <div class="grant-card__footer">
                            <div class="grant-card__deadline">
                                <span class="grant-card__deadline-label">締切</span>
                                <span class="grant-card__deadline-date <?php echo $is_urgent ? 'grant-card__deadline-date--urgent' : ''; ?>">
                                    <?php echo $deadline ? date('Y年m月d日', strtotime($deadline)) : '随時受付'; ?>
                                </span>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="grant-card__cta">詳細を見る</a>
                        </div>
                    </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <?php else : ?>
                <div class="info-hub__empty">現在、注目の補助金はありません</div>
                <?php endif; ?>
            </div>

            <!-- Tab: Deadline -->
            <div class="grant-tabs__panel" id="panel-deadline" hidden>
                <div class="grant-tabs__panel-header">
                    <p class="grant-tabs__panel-desc grant-tabs__panel-desc--red">申請期限が迫っている補助金</p>
                    <a href="<?php echo esc_url(home_url('/grants/?orderby=deadline')); ?>" class="info-hub__link">View All Deadlines →</a>
                </div>
                
                <?php if ($grants_deadline->have_posts()) : ?>
                <div class="deadline-list">
                    <?php while ($grants_deadline->have_posts()) : $grants_deadline->the_post(); 
                        $deadline = get_post_meta(get_the_ID(), 'deadline_date', true);
                        $days_left = ceil((strtotime($deadline) - strtotime($today)) / 86400);
                    ?>
                    <a href="<?php the_permalink(); ?>" class="deadline-item">
                        <div class="deadline-item__content">
                            <h4 class="deadline-item__title"><?php the_title(); ?></h4>
                            <span class="deadline-item__date">（<?php echo date('n月j日', strtotime($deadline)); ?>締切）</span>
                        </div>
                        <div class="deadline-item__info">
                            <span class="deadline-item__label">締切<?php echo date('m/d', strtotime($deadline)); ?></span>
                            <span class="deadline-item__days">あと<?php echo max(0, $days_left); ?>日</span>
                        </div>
                    </a>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <?php else : ?>
                <div class="info-hub__empty">現在、締切間近の補助金はありません</div>
                <?php endif; ?>
            </div>

            <!-- Tab: New -->
            <div class="grant-tabs__panel" id="panel-new" hidden>
                <div class="grant-tabs__panel-header">
                    <p class="grant-tabs__panel-desc">最近公開された補助金制度</p>
                    <a href="<?php echo esc_url(home_url('/grants/?orderby=new')); ?>" class="info-hub__link">View All New Grants →</a>
                </div>
                
                <?php if ($grants_new->have_posts()) : ?>
                <div class="new-grants-list">
                    <?php while ($grants_new->have_posts()) : $grants_new->the_post(); ?>
                    <a href="<?php the_permalink(); ?>" class="new-grant-item">
                        <span class="new-grant-item__date"><?php echo get_the_date('Y.m.d'); ?> 公開</span>
                        <h4 class="new-grant-item__title"><?php the_title(); ?></h4>
                    </a>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <?php else : ?>
                <div class="info-hub__empty">現在、新着補助金はありません</div>
                <?php endif; ?>
            </div>

        </section>

    </div>
</section>
