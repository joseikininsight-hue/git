<?php
/**
 * Template Part: Archive Sidebar Filters
 * 補助金アーカイブ共通サイドバーフィルターテンプレート
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) exit;

// カテゴリデータの取得
$all_categories = get_terms([
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC',
]);

// 地域グループ
$region_groups = [
    'hokkaido' => '北海道',
    'tohoku' => '東北',
    'kanto' => '関東',
    'chubu' => '中部',
    'kinki' => '近畿',
    'chugoku' => '中国',
    'shikoku' => '四国',
    'kyushu' => '九州・沖縄'
];
?>

<!-- サイドバー（PC only） - 図鑑スタイル -->
<aside class="yahoo-sidebar zukan-sidebar" role="complementary" aria-label="サイドバー">
    
    <!-- サイドバー検索ウィジェット -->
    <section class="sidebar-widget sidebar-search-widget">
        <h3 class="widget-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            キーワード検索
        </h3>
        <div class="widget-content">
            <div class="sidebar-search-form">
                <input type="text" 
                       id="sidebar-keyword-search" 
                       class="sidebar-search-input" 
                       placeholder="助成金名・キーワードで検索"
                       aria-label="キーワード検索">
                <button type="button" 
                        id="sidebar-search-btn" 
                        class="sidebar-search-btn"
                        aria-label="検索">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- サイドバー絞り込みウィジェット -->
    <section class="sidebar-widget sidebar-filter-widget">
        <h3 class="widget-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            絞り込み
        </h3>
        <div class="widget-content">
            
            <!-- カテゴリフィルター -->
            <div class="sidebar-filter-group" id="sidebar-category-filter">
                <button type="button" class="sidebar-filter-toggle" aria-expanded="false">
                    <span class="filter-group-label">カテゴリ</span>
                    <span class="filter-selected-count" id="category-selected-count" style="display: none;">0</span>
                    <svg class="toggle-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </button>
                <div class="sidebar-filter-options" style="display: none;">
                    <?php if ($all_categories && !is_wp_error($all_categories)): ?>
                        <?php foreach (array_slice($all_categories, 0, 8) as $category): ?>
                        <label class="sidebar-filter-option">
                            <input type="checkbox" 
                                   name="sidebar_category[]" 
                                   value="<?php echo esc_attr($category->slug); ?>">
                            <span class="checkbox-custom"></span>
                            <span class="option-label"><?php echo esc_html($category->name); ?></span>
                            <span class="option-count"><?php echo $category->count; ?></span>
                        </label>
                        <?php endforeach; ?>
                        <?php if (count($all_categories) > 8): ?>
                        <button type="button" class="sidebar-filter-more" data-target="category">さらに表示</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 都道府県フィルター -->
            <div class="sidebar-filter-group" id="sidebar-prefecture-filter">
                <button type="button" class="sidebar-filter-toggle" aria-expanded="false">
                    <span class="filter-group-label">都道府県</span>
                    <span class="filter-selected-count" id="prefecture-selected-count" style="display: none;">0</span>
                    <svg class="toggle-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </button>
                <div class="sidebar-filter-options" style="display: none;">
                    <?php 
                    $sidebar_prefectures = get_terms(array(
                        'taxonomy' => 'grant_prefecture',
                        'hide_empty' => true,
                        'orderby' => 'count',
                        'order' => 'DESC',
                        'number' => 15
                    ));
                    if ($sidebar_prefectures && !is_wp_error($sidebar_prefectures)):
                        foreach ($sidebar_prefectures as $pref): ?>
                    <label class="sidebar-filter-option">
                        <input type="checkbox" 
                               name="sidebar_prefecture[]" 
                               value="<?php echo esc_attr($pref->slug); ?>">
                        <span class="checkbox-custom"></span>
                        <span class="option-label"><?php echo esc_html($pref->name); ?></span>
                        <span class="option-count"><?php echo $pref->count; ?></span>
                    </label>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- 地域フィルター -->
            <div class="sidebar-filter-group" id="sidebar-region-filter">
                <button type="button" class="sidebar-filter-toggle" aria-expanded="false">
                    <span class="filter-group-label">地域</span>
                    <span class="filter-selected-count" id="region-selected-count" style="display: none;">0</span>
                    <svg class="toggle-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </button>
                <div class="sidebar-filter-options" style="display: none;">
                    <?php foreach ($region_groups as $region_slug => $region_name): ?>
                    <label class="sidebar-filter-option">
                        <input type="checkbox" 
                               name="sidebar_region[]" 
                               value="<?php echo esc_attr($region_slug); ?>">
                        <span class="checkbox-custom"></span>
                        <span class="option-label"><?php echo esc_html($region_name); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 助成金額フィルター -->
            <div class="sidebar-filter-group" id="sidebar-amount-filter">
                <button type="button" class="sidebar-filter-toggle" aria-expanded="false">
                    <span class="filter-group-label">助成金額</span>
                    <span class="filter-selected-count" id="amount-selected-count" style="display: none;">0</span>
                    <svg class="toggle-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </button>
                <div class="sidebar-filter-options" style="display: none;">
                    <label class="sidebar-filter-option">
                        <input type="checkbox" name="sidebar_amount[]" value="0-100">
                        <span class="checkbox-custom"></span>
                        <span class="option-label">〜100万円</span>
                    </label>
                    <label class="sidebar-filter-option">
                        <input type="checkbox" name="sidebar_amount[]" value="100-500">
                        <span class="checkbox-custom"></span>
                        <span class="option-label">100万円〜500万円</span>
                    </label>
                    <label class="sidebar-filter-option">
                        <input type="checkbox" name="sidebar_amount[]" value="500-1000">
                        <span class="checkbox-custom"></span>
                        <span class="option-label">500万円〜1000万円</span>
                    </label>
                    <label class="sidebar-filter-option">
                        <input type="checkbox" name="sidebar_amount[]" value="1000-3000">
                        <span class="checkbox-custom"></span>
                        <span class="option-label">1000万円〜3000万円</span>
                    </label>
                    <label class="sidebar-filter-option">
                        <input type="checkbox" name="sidebar_amount[]" value="3000+">
                        <span class="checkbox-custom"></span>
                        <span class="option-label">3000万円以上</span>
                    </label>
                </div>
            </div>

            <!-- 募集状況フィルター -->
            <div class="sidebar-filter-group" id="sidebar-status-filter">
                <button type="button" class="sidebar-filter-toggle" aria-expanded="false">
                    <span class="filter-group-label">募集状況</span>
                    <span class="filter-selected-count" id="status-selected-count" style="display: none;">0</span>
                    <svg class="toggle-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </button>
                <div class="sidebar-filter-options" style="display: none;">
                    <label class="sidebar-filter-option">
                        <input type="checkbox" name="sidebar_status[]" value="active">
                        <span class="checkbox-custom"></span>
                        <span class="option-label">募集中</span>
                    </label>
                    <label class="sidebar-filter-option">
                        <input type="checkbox" name="sidebar_status[]" value="upcoming">
                        <span class="checkbox-custom"></span>
                        <span class="option-label">募集予定</span>
                    </label>
                    <label class="sidebar-filter-option">
                        <input type="checkbox" name="sidebar_status[]" value="closed">
                        <span class="checkbox-custom"></span>
                        <span class="option-label">募集終了</span>
                    </label>
                </div>
            </div>

            <!-- フィルター適用ボタン -->
            <div class="sidebar-filter-actions">
                <button type="button" id="sidebar-apply-filter" class="sidebar-apply-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    この条件で検索
                </button>
                <button type="button" id="sidebar-reset-filter" class="sidebar-reset-btn">
                    リセット
                </button>
            </div>

        </div>
    </section>

    <?php 
    // アーカイブSEOコンテンツ: サイドバー追加コンテンツ
    if (function_exists('gi_output_archive_sidebar_content')) {
        gi_output_archive_sidebar_content();
    }
    ?>
    
    <!-- 広告枠1: サイドバー上部 -->
    <?php if (function_exists('ji_display_ad')): ?>
    <div class="sidebar-ad-space sidebar-ad-top">
        <?php ji_display_ad('archive_grant_sidebar_top', 'archive-grant'); ?>
    </div>
    <?php endif; ?>

    <!-- 広告枠2: サイドバー中央 -->
    <?php if (function_exists('ji_display_ad')): ?>
    <div class="sidebar-ad-space sidebar-ad-middle">
        <?php ji_display_ad('archive_grant_sidebar_middle', 'archive-grant'); ?>
    </div>
    <?php endif; ?>

    <!-- アクセスランキング -->
    <?php
    $ranking_periods = array(
        array('days' => 3, 'label' => '3日間', 'id' => 'ranking-3days'),
        array('days' => 7, 'label' => '週間', 'id' => 'ranking-7days'),
        array('days' => 0, 'label' => '総合', 'id' => 'ranking-all'),
    );
    
    $default_period = 3;
    $ranking_data = function_exists('ji_get_ranking') ? ji_get_ranking('grant', $default_period, 10) : array();
    ?>
    
    <section class="sidebar-widget sidebar-ranking">
        <h3 class="widget-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                <polyline points="17 6 23 6 23 12"/>
            </svg>
            アクセスランキング
        </h3>
        
        <div class="ranking-tabs">
            <?php foreach ($ranking_periods as $index => $period): ?>
                <button 
                    type="button" 
                    class="ranking-tab <?php echo $index === 0 ? 'active' : ''; ?>" 
                    data-period="<?php echo esc_attr($period['days']); ?>"
                    data-target="#<?php echo esc_attr($period['id']); ?>">
                    <?php echo esc_html($period['label']); ?>
                </button>
            <?php endforeach; ?>
        </div>
        
        <div class="widget-content">
            <?php foreach ($ranking_periods as $index => $period): ?>
                <div 
                    id="<?php echo esc_attr($period['id']); ?>" 
                    class="ranking-content <?php echo $index === 0 ? 'active' : ''; ?>"
                    data-period="<?php echo esc_attr($period['days']); ?>">
                    
                    <?php if ($index === 0): ?>
                        <?php if (!empty($ranking_data)): ?>
                            <ol class="ranking-list">
                                <?php foreach ($ranking_data as $rank => $item): ?>
                                    <li class="ranking-item rank-<?php echo $rank + 1; ?>">
                                        <a href="<?php echo get_permalink($item->post_id); ?>" class="ranking-link">
                                            <span class="ranking-number"><?php echo $rank + 1; ?></span>
                                            <span class="ranking-title">
                                                <?php echo esc_html(get_the_title($item->post_id)); ?>
                                            </span>
                                            <span class="ranking-views">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                <?php echo number_format($item->total_views); ?>
                                            </span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php else: ?>
                            <div class="ranking-empty" style="text-align: center; padding: 30px 20px; color: #666;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 10px; opacity: 0.3; display: block;">
                                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                                    <polyline points="17 6 23 6 23 12"/>
                                </svg>
                                <p style="margin: 0; font-size: 14px; font-weight: 500;">まだデータがありません</p>
                                <p style="margin: 5px 0 0; font-size: 12px; opacity: 0.7;">ページが閲覧されるとランキングが表示されます</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="ranking-loading">読み込み中...</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- 新着トピックス -->
    <section class="sidebar-widget sidebar-topics">
        <h3 class="widget-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                <line x1="6" y1="1" x2="6" y2="4"/>
                <line x1="10" y1="1" x2="10" y2="4"/>
                <line x1="14" y1="1" x2="14" y2="4"/>
            </svg>
            新着トピックス
        </h3>
        <div class="widget-content">
            <?php
            $recent_posts = get_posts(array(
                'post_type' => 'column',
                'posts_per_page' => 5,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            if ($recent_posts): ?>
                <ul class="topics-list">
                    <?php foreach ($recent_posts as $post): ?>
                        <li class="topics-item">
                            <a href="<?php echo get_permalink($post); ?>" class="topics-link">
                                <span class="topics-date"><?php echo get_the_date('Y.m.d', $post); ?></span>
                                <span class="topics-title"><?php echo esc_html($post->post_title); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="no-topics">新着トピックスはありません</p>
            <?php endif; ?>
        </div>
    </section>

</aside>
