<?php
/**
 * Template Part: Grant Archive List
 * 補助金アーカイブ共通リスト表示テンプレート
 * 
 * すべてのグラント関連アーカイブページで使用される共通テンプレート
 * - archive-grant.php
 * - taxonomy-grant_category.php
 * - taxonomy-grant_prefecture.php
 * - taxonomy-grant_municipality.php
 * - taxonomy-grant_purpose.php
 * - taxonomy-grant_tag.php
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 * 
 * 必要な変数:
 * - $archive_title: アーカイブタイトル
 * - $archive_description: アーカイブ説明文
 * - $total_grants: 総件数
 * - $query_args: WP_Query用引数（オプション、デフォルトはグローバルクエリ使用）
 * - $tax_term: 現在のタクソノミーターム（オプション）
 */

// セキュリティチェック
if (!defined('ABSPATH')) exit;

// デフォルト値の設定
$archive_title = isset($archive_title) ? $archive_title : '助成金・補助金一覧';
$archive_description = isset($archive_description) ? $archive_description : '最新の助成金・補助金情報を掲載しています。';
$total_grants = isset($total_grants) ? $total_grants : wp_count_posts('grant')->publish;
$total_grants_formatted = number_format($total_grants);

// ページネーション情報
$current_page = get_query_var('paged') ? get_query_var('paged') : 1;
$posts_per_page = 12;
$showing_from = (($current_page - 1) * $posts_per_page) + 1;
$showing_to = min($current_page * $posts_per_page, $total_grants);
?>

<!-- アーカイブSEOコンテンツ: イントロ（01傾向と対策） -->
<?php 
if (function_exists('gi_output_archive_intro_content')) {
    gi_output_archive_intro_content();
}

// アーカイブSEOコンテンツ: おすすめ記事（02編集部選定）
if (function_exists('gi_output_archive_featured_posts')) {
    gi_output_archive_featured_posts();
}
?>

<!-- 統合された検索結果ヘッダー -->
<section class="editors-pick-section results-header" id="list">
    <div class="editors-pick-header">
        <div class="flex items-center">
            <span class="editors-pick-number">03</span>
            <div class="editors-pick-title-wrap">
                <h2>補助金図鑑一覧</h2>
            </div>
        </div>
    </div>
    <div class="results-range-display">
        <div class="results-range-text">
            <span class="total-count" id="current-count"><?php echo esc_html($total_grants_formatted); ?></span> 件中 
            <span class="range-numbers" id="showing-from"><?php echo number_format($showing_from); ?></span>〜<span class="range-numbers" id="showing-to"><?php echo number_format($showing_to); ?></span> 件を表示
        </div>
        <div class="results-sort-select">
            <label for="archive-sort-select">並び替え:</label>
            <select id="archive-sort-select" onchange="window.location.href=this.value">
                <?php
                $current_orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';
                $sort_options = array(
                    '' => 'おすすめ順',
                    'new' => '新着順',
                    'deadline' => '締切が近い順',
                    'popular' => '人気順',
                );
                foreach ($sort_options as $value => $label) {
                    $url = add_query_arg('orderby', $value, remove_query_arg('orderby'));
                    if (empty($value)) {
                        $url = remove_query_arg('orderby');
                    }
                    $selected = ($current_orderby === $value || (empty($current_orderby) && empty($value))) ? 'selected' : '';
                    echo '<option value="' . esc_url($url) . '" ' . $selected . '>' . esc_html($label) . '</option>';
                }
                ?>
            </select>
        </div>
    </div>
</section>

<!-- Results Section -->
<section class="yahoo-results-section" id="grants-results-section">

    <!-- List Container: Dictionary Layout -->
    <div id="grants-container">
        <?php
        // クエリ引数が渡されていない場合はデフォルトを構築
        if (!isset($query_args)) {
            $query_args = array(
                'post_type' => 'grant',
                'posts_per_page' => $posts_per_page,
                'post_status' => 'publish',
                'paged' => $current_page,
                'orderby' => 'date',
                'order' => 'DESC'
            );
        }
        
        // クエリ実行
        $grants_query = new WP_Query($query_args);
        
        if ($grants_query->have_posts()) :
            $grant_count = 0;
            while ($grants_query->have_posts()) : 
                $grants_query->the_post();
                
                // 図鑑スタイルのカードを使用
                include(get_template_directory() . '/template-parts/grant/card-zukan.php');
                
                $grant_count++;
                
                // 4件目と8件目の後にインフィード広告を挿入
                if (($grant_count === 4 || $grant_count === 8) && function_exists('ji_display_ad')) : ?>
                    <div class="archive-infeed-ad" style="margin: 24px 0; padding: 15px; background: #f9f9f9; border-radius: 8px; text-align: center;">
                        <span style="font-size: 10px; color: #999; display: block; text-align: left; margin-bottom: 8px;">スポンサーリンク</span>
                        <?php ji_display_ad('archive_grant_infeed'); ?>
                    </div>
                <?php endif;
                
            endwhile;
            wp_reset_postdata();
        else :
            // 結果なしの場合（図鑑スタイル）
            echo '<div class="zukan-empty-state">';
            echo '該当する項目はこの巻には記されていないようだ...';
            echo '</div>';
        endif;
        ?>
    </div>

    <!-- 結果なし（AJAX用） -->
    <div class="no-results" id="no-results" style="display: none;">
        <svg class="no-results-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
        </svg>
        <h3 class="no-results-title">該当する助成金が見つかりませんでした</h3>
        <p class="no-results-message">検索条件を変更して再度お試しください。</p>
    </div>

    <!-- ページネーション -->
    <div class="pagination-wrapper zukan-pagination" id="pagination-wrapper">
        <?php
        if (isset($grants_query) && $grants_query->max_num_pages > 1) {
            $big = 999999999;
            
            // 現在のクエリパラメータを保持
            $preserved_params = array();
            foreach ($_GET as $key => $value) {
                if (!empty($value) && $key !== 'paged') {
                    $preserved_params[$key] = sanitize_text_field($value);
                }
            }
            
            // ベースURL
            $base_url = add_query_arg($preserved_params, str_replace($big, '%#%', esc_url(get_pagenum_link($big))));
            
            // ページネーションリンクに#listアンカーを追加
            $pagination_links = paginate_links(array(
                'base' => $base_url,
                'format' => '&paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $grants_query->max_num_pages,
                'type' => 'plain',
                'prev_text' => '前へ',
                'next_text' => '次へ',
                'mid_size' => 2,
                'end_size' => 1,
            ));
            
            if ($pagination_links) {
                // #listアンカーを各リンクに追加
                $pagination_links = preg_replace('/href=["\']([^"\']+)["\']/i', 'href="$1#list"', $pagination_links);
                echo $pagination_links;
            }
        }
        ?>
    </div>
</section>

<!-- アーカイブSEOコンテンツ: アウトロ -->
<?php 
if (function_exists('gi_output_archive_outro_content')) {
    gi_output_archive_outro_content();
}
?>
