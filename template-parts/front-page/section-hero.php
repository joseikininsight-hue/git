<?php
/**
 * Hero Section - 補助金図鑑×官公庁デザイン（2カラム版）
 * 左側：テキストコンテンツ / 右側：背景画像
 * 
 * @package Grant_Insight_Perfect
 * @version 56.0.0 - Two Column Layout
 */

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// ==========================================================================
// 設定値
// ==========================================================================
$hero_config = [
    // テキスト
    'tagline'           => '経済産業省・厚生労働省 補助金情報連携',
    'main_title_1'      => '補助金・助成金を',
    'main_title_2'      => 'AIで効率的に検索',
    'description'       => '全国20,000件以上の補助金・助成金情報から、あなたのビジネスに最適な制度をAIが瞬時にマッチング。専門家監修の信頼できる情報で、申請までサポートします。',
    
    // CTA
    'cta_primary_text'  => '今すぐ補助金を探す',
    'cta_primary_url'   => home_url('/grants/'),
    'cta_secondary_text'=> '3分で無料診断',
    'cta_secondary_url' => 'https://joseikin-insight.com/subsidy-diagnosis/',
    
    // 特徴
    'features' => [
        ['text' => '経済産業省・中小企業庁の公式データを収集', 'icon' => 'check'],
        ['text' => '中小企業診断士による情報監修', 'icon' => 'star'],
        ['text' => '会員登録不要・完全無料で利用可能', 'icon' => 'check'],
    ],
    
    // 背景画像（右側に表示）
    'bg_image' => 'https://joseikin-insight.com/wp-content/uploads/2025/12/%E5%90%8D%E7%A7%B0%E6%9C%AA%E8%A8%AD%E5%AE%9A%E3%81%AE%E3%83%87%E3%82%B6%E3%82%A4%E3%83%B3.png',
    
    // 統計データ
    'stats' => [
        ['number' => '10,000', 'unit' => '件以上', 'label' => '補助金データ収録', 'icon' => 'search'],
        ['number' => '47', 'unit' => '都道府県', 'label' => '全国対応', 'icon' => 'map'],
        ['number' => '24', 'unit' => '時間', 'label' => 'いつでも利用可能', 'icon' => 'clock'],
    ],
];
?>

<section 
    class="hero" 
    id="hero-section" 
    role="banner" 
    aria-labelledby="hero-heading"
    itemscope 
    itemtype="https://schema.org/WPHeader">
    
    <!-- 背景画像（右側に配置）+ モーションエフェクト -->
    <div class="hero__bg" aria-hidden="true">
        <img 
            src="<?php echo esc_url($hero_config['bg_image']); ?>" 
            alt="" 
            class="hero__bg-image"
            loading="eager"
            fetchpriority="high"
            decoding="async">
        <div class="hero__bg-overlay"></div>
        <div class="hero__bg-shimmer"></div>
    </div>
    
    <div class="hero__container">
        <div class="hero__grid">
            
            <!-- 左カラム：テキストコンテンツ -->
            <div class="hero__content">
                
                <!-- バッジグループ -->
                <div class="hero__badge-group">
                    <span class="hero__badge hero__badge--official">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <?php echo esc_html($hero_config['tagline']); ?>
                    </span>
                    <span class="hero__badge hero__badge--date">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        最終更新：<?php echo date('Y年n月j日'); ?>
                    </span>
                </div>
                
                <!-- メインタイトル -->
                <h1 class="hero__title" id="hero-heading" itemprop="headline">
                    <?php echo esc_html($hero_config['main_title_1']); ?><br>
                    <?php echo esc_html($hero_config['main_title_2']); ?>
                </h1>
                
                <!-- 説明文 -->
                <p class="hero__description" itemprop="description">
                    <?php echo esc_html($hero_config['description']); ?>
                </p>
                
                <!-- 統計バー -->
                <div class="hero__stats">
                    <?php foreach ($hero_config['stats'] as $index => $stat): ?>
                    <div class="hero__stat">
                        <div class="hero__stat-icon">
                            <?php if ($stat['icon'] === 'search'): ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="M21 21l-4.35-4.35"/>
                            </svg>
                            <?php elseif ($stat['icon'] === 'map'): ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <?php elseif ($stat['icon'] === 'clock'): ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            <?php endif; ?>
                        </div>
                        <div class="hero__stat-content">
                            <div class="hero__stat-main">
                                <span class="hero__stat-number"><?php echo esc_html($stat['number']); ?></span>
                                <span class="hero__stat-unit"><?php echo esc_html($stat['unit']); ?></span>
                            </div>
                            <span class="hero__stat-label"><?php echo esc_html($stat['label']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- CTAエリア -->
                <div class="hero__cta">
                    <a 
                        href="<?php echo esc_url($hero_config['cta_primary_url']); ?>" 
                        class="hero__btn hero__btn--primary"
                        aria-label="<?php echo esc_attr($hero_config['cta_primary_text']); ?>">
                        <svg class="hero__btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="M21 21l-4.35-4.35"/>
                        </svg>
                        <span><?php echo esc_html($hero_config['cta_primary_text']); ?></span>
                    </a>
                    <a 
                        href="<?php echo esc_url($hero_config['cta_secondary_url']); ?>" 
                        class="hero__btn hero__btn--secondary"
                        aria-label="<?php echo esc_attr($hero_config['cta_secondary_text']); ?>">
                        <svg class="hero__btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span><?php echo esc_html($hero_config['cta_secondary_text']); ?></span>
                    </a>
                </div>
                
                <!-- 特徴リスト -->
                <ul class="hero__features">
                    <?php foreach ($hero_config['features'] as $feature): ?>
                    <li class="hero__feature">
                        <span class="hero__feature-icon">
                            <?php if ($feature['icon'] === 'star'): ?>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <?php else: ?>
                            <svg width="12" height="12" viewBox="0 0 20 20" fill="none">
                                <path d="M6 10l3 3 5-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <?php endif; ?>
                        </span>
                        <span><?php echo esc_html($feature['text']); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                
            </div>
            
            <!-- 右カラム：画像スペース（背景画像で表示） -->
            <div class="hero__visual" aria-hidden="true"></div>
            
        </div>
    </div>
    
</section>
