<?php
/**
 * 補助金図鑑 - Zukan Style Footer
 * ヘッダーデザインと統一された図鑑風フッター
 * 外部CSS・JS読み込み版
 * 
 * @package Joseikin_Insight_Footer
 * @version 10.0.0 (Zukan Edition)
 */

// SNS URLヘルパー関数
if (!function_exists('gi_get_sns_urls')) {
    function gi_get_sns_urls() {
        return [
            'twitter' => get_option('gi_sns_twitter_url', 'https://twitter.com/joseikininsight'),
            'facebook' => get_option('gi_sns_facebook_url', 'https://facebook.com/hojokin.zukan'),
            'linkedin' => get_option('gi_sns_linkedin_url', ''),
            'instagram' => get_option('gi_sns_instagram_url', 'https://instagram.com/hojokin_zukan'),
            'youtube' => get_option('gi_sns_youtube_url', 'https://www.youtube.com/channel/UCbfjOrG3nSPI3GFzKnGcspQ'),
            'note' => get_option('gi_sns_note_url', 'https://note.com/hojokin_zukan')
        ];
    }
}

// 統計情報取得関数
if (!function_exists('gi_get_cached_stats')) {
    function gi_get_cached_stats() {
        $stats = wp_cache_get('gi_stats', 'grant_insight');
        
        if (false === $stats) {
            $stats = [
                'total_grants' => wp_count_posts('grant')->publish ?? 0,
                'active_grants' => 0,
                'today_updated' => 0
            ];
            
            // 募集中の助成金数を取得
            $active_query = new WP_Query([
                'post_type' => 'grant',
                'post_status' => 'publish',
                'meta_query' => [
                    [
                        'key' => 'grant_status',
                        'value' => 'active',
                        'compare' => '='
                    ]
                ],
                'posts_per_page' => -1,
                'fields' => 'ids'
            ]);
            $stats['active_grants'] = $active_query->found_posts;
            wp_reset_postdata();
            
            // 本日更新数を取得
            $today_query = new WP_Query([
                'post_type' => 'grant',
                'post_status' => 'publish',
                'date_query' => [
                    ['after' => 'today']
                ],
                'posts_per_page' => -1,
                'fields' => 'ids'
            ]);
            $stats['today_updated'] = $today_query->found_posts;
            wp_reset_postdata();
            
            wp_cache_set('gi_stats', $stats, 'grant_insight', 3600);
        }
        
        return $stats;
    }
}

// 外部CSS/JSファイルパス
$footer_css_path = get_template_directory() . '/assets/css/footer.css';
$footer_js_path = get_template_directory() . '/assets/js/footer.js';
$footer_css_url = get_template_directory_uri() . '/assets/css/footer.css';
$footer_js_url = get_template_directory_uri() . '/assets/js/footer.js';

// キャッシュバスティング用のバージョン
$footer_css_version = file_exists($footer_css_path) ? filemtime($footer_css_path) : '10.0.0';
$footer_js_version = file_exists($footer_js_path) ? filemtime($footer_js_path) : '10.0.0';
?>

    </main>

    <!-- Footer CSS (External) -->
    <link rel="stylesheet" href="<?php echo esc_url($footer_css_url); ?>?v=<?php echo esc_attr($footer_css_version); ?>" id="zukan-footer-styles">

    <!-- Zukan Style Footer - ヘッダーと統一されたデザイン -->
    <footer class="zukan-footer" role="contentinfo" itemscope itemtype="https://schema.org/WPFooter">
        
        <!-- しおり装飾 -->
        <div class="zukan-footer-bookmark" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
            <span><?php echo date('Y'); ?>年版</span>
        </div>
        
        <div class="zukan-footer-inner">
            
            <!-- Main Navigation -->
            <nav class="zukan-footer-nav" aria-label="フッターナビゲーション">
                <div class="zukan-nav-grid">
                    
                    <!-- Column 1: ロゴ・連絡先 -->
                    <div class="zukan-nav-brand">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="zukan-brand-logo" aria-label="<?php bloginfo('name'); ?> ホームへ">
                            <div class="zukan-logo-icon">
                                <span>図</span>
                            </div>
                            <div class="zukan-logo-text-wrapper">
                                <div class="zukan-logo-title"><?php bloginfo('name'); ?></div>
                                <div class="zukan-logo-subtitle">Subsidy Archive</div>
                            </div>
                        </a>
                        
                        <p class="zukan-brand-description">
                            日本全国の補助金・助成金情報を一元化。あなたのビジネスや生活に最適な支援制度を見つけるお手伝いをします。
                        </p>
                        
                        <div class="zukan-contact-info">
                            <div class="zukan-contact-item">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                <a href="<?php echo esc_url(home_url('/contact/')); ?>">お問い合わせフォーム</a>
                            </div>
                            <div class="zukan-contact-item">
                                <i class="fas fa-clock" aria-hidden="true"></i>
                                <span>営業時間: 平日 9:00〜18:00</span>
                            </div>
                        </div>
                        
                        <div class="zukan-social-row" aria-label="ソーシャルメディア">
                            <?php
                            $sns_urls = gi_get_sns_urls();
                            // X (Twitter)
                            if (!empty($sns_urls['twitter'])) {
                                echo '<a href="' . esc_url($sns_urls['twitter']) . '" class="zukan-social-link" aria-label="Xでフォロー" target="_blank" rel="noopener noreferrer">';
                                echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>';
                                echo '</a>';
                            }
                            // Facebook
                            if (!empty($sns_urls['facebook'])) {
                                echo '<a href="' . esc_url($sns_urls['facebook']) . '" class="zukan-social-link" aria-label="Facebookでフォロー" target="_blank" rel="noopener noreferrer">';
                                echo '<i class="fab fa-facebook-f" aria-hidden="true"></i>';
                                echo '</a>';
                            }
                            // Instagram
                            if (!empty($sns_urls['instagram'])) {
                                echo '<a href="' . esc_url($sns_urls['instagram']) . '" class="zukan-social-link" aria-label="Instagramでフォロー" target="_blank" rel="noopener noreferrer">';
                                echo '<i class="fab fa-instagram" aria-hidden="true"></i>';
                                echo '</a>';
                            }
                            // YouTube
                            if (!empty($sns_urls['youtube'])) {
                                echo '<a href="' . esc_url($sns_urls['youtube']) . '" class="zukan-social-link" aria-label="YouTubeでフォロー" target="_blank" rel="noopener noreferrer">';
                                echo '<i class="fab fa-youtube" aria-hidden="true"></i>';
                                echo '</a>';
                            }
                            // note
                            if (!empty($sns_urls['note'])) {
                                echo '<a href="' . esc_url($sns_urls['note']) . '" class="zukan-social-link" aria-label="noteでフォロー" target="_blank" rel="noopener noreferrer">';
                                echo '<i class="fas fa-pen-nib" aria-hidden="true"></i>';
                                echo '</a>';
                            }
                            // LinkedIn
                            if (!empty($sns_urls['linkedin'])) {
                                echo '<a href="' . esc_url($sns_urls['linkedin']) . '" class="zukan-social-link" aria-label="LinkedInでフォロー" target="_blank" rel="noopener noreferrer">';
                                echo '<i class="fab fa-linkedin-in" aria-hidden="true"></i>';
                                echo '</a>';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- Column 2: 主要リンク -->
                    <div class="zukan-nav-column">
                        <h3 class="zukan-nav-title">主要リンク</h3>
                        <ul class="zukan-nav-list">
                            <li>
                                <a href="<?php echo esc_url(get_post_type_archive_link('grant')); ?>" class="zukan-nav-link">
                                    補助金・助成金一覧
                                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(add_query_arg('application_status', 'open', get_post_type_archive_link('grant'))); ?>" class="zukan-nav-link zukan-nav-link-live">
                                    <span class="zukan-live-dot" aria-hidden="true"></span>
                                    募集中の補助金
                                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(home_url('/subsidy-diagnosis/')); ?>" class="zukan-nav-link">
                                    補助金診断
                                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(home_url('/column/')); ?>" class="zukan-nav-link">
                                    コラム
                                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Column 3: サポート -->
                    <div class="zukan-nav-column">
                        <h3 class="zukan-nav-title">サポート</h3>
                        <ul class="zukan-nav-list">
                            <li>
                                <a href="<?php echo esc_url(home_url('/about/')); ?>" class="zukan-nav-link">
                                    当サイトについて
                                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="zukan-nav-link">
                                    お問い合わせ
                                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(home_url('/news/')); ?>" class="zukan-nav-link">
                                    お知らせ
                                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                </div>
            </nav>
            
        </div>
        
        <!-- Bottom Section -->
        <div class="zukan-footer-bottom">
            <div class="zukan-footer-inner">
                <div class="zukan-bottom-wrapper">
                    <div class="zukan-bottom-left">
                        <p class="zukan-copyright">
                            &copy; <?php echo date('Y'); ?> <strong><?php bloginfo('name'); ?></strong>. All rights reserved.
                        </p>
                        <?php
                        $stats = gi_get_cached_stats();
                        $last_updated = get_option('gi_last_updated', date('Y-m-d'));
                        ?>
                        <div class="zukan-update-info">
                            <span class="zukan-update-dot" aria-hidden="true"></span>
                            <span>最終更新: <?php echo esc_html(date_i18n('Y年n月j日', strtotime($last_updated))); ?></span>
                            <?php if (!empty($stats['total_grants'])): ?>
                            <span>|</span>
                            <span>掲載数: <?php echo number_format($stats['total_grants']); ?>件</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <nav class="zukan-legal-links" aria-label="法的情報">
                        <a href="<?php echo esc_url(home_url('/privacy/')); ?>" class="zukan-legal-link">プライバシーポリシー</a>
                        <a href="<?php echo esc_url(home_url('/terms/')); ?>" class="zukan-legal-link">利用規約</a>
                        <a href="<?php echo esc_url(home_url('/disclaimer/')); ?>" class="zukan-legal-link">免責事項</a>
                    </nav>
                </div>
            </div>
        </div>
    </footer>

    <?php 
    // サイドバーCTA（縦タブスタイル）を表示
    // AdSenseアンカー広告との競合なし、閉じ機能付き、AI相談ボタン統合
    get_template_part('template-parts/sidebar-cta'); 
    
    // 旧: 下部固定CTA（AdSense競合の可能性があるため無効化）
    // get_template_part('template-parts/global-sticky-cta'); 
    ?>

    <?php wp_footer(); ?>
    
    <!-- Footer JS (External) -->
    <script src="<?php echo esc_url($footer_js_url); ?>?v=<?php echo esc_attr($footer_js_version); ?>" defer></script>
</body>
</html>
