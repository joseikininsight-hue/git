<?php
/**
 * 補助金・助成金情報サイト - 当サイトについてページ
 * Grant & Subsidy Information Site - About Us Page
 * @package Grant_Insight_About
 * @version 2.0-government-design
 * 
 * === 主要機能 ===
 * 1. サイト概要の説明
 * 2. 運営理念とサービス内容
 * 3. 信頼性と免責事項
 * 4. SEO最適化
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

get_header();

// 構造化データ
$about_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'AboutPage',
    'name' => '当サイトについて - 補助金図鑑',
    'description' => '補助金図鑑は、全国の補助金・助成金情報を効率的に検索できるAI活用型のポータルサイトです。',
    'url' => 'https://joseikin-insight.com/about/',
    'mainEntity' => array(
        '@type' => 'Organization',
        'name' => '補助金図鑑',
        'url' => 'https://joseikin-insight.com',
        'description' => '中小企業・個人事業主・スタートアップ企業向けの補助金・助成金情報検索サービス'
    )
);
?>

<!-- 構造化データ -->
<script type="application/ld+json">
<?php echo wp_json_encode($about_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<style>
/* ==========================================================================
   Official Government Design - About Page CSS
   官公庁デザイン - 当サイトについてページ
   ========================================================================== */

:root {
    /* 官公庁カラーパレット */
    --gov-navy-900: #0d1b2a;
    --gov-navy-800: #1b263b;
    --gov-navy-700: #2c3e50;
    --gov-navy-600: #34495e;
    --gov-navy-500: #415a77;
    --gov-navy-400: #778da9;
    --gov-navy-300: #a3b1c6;
    --gov-navy-200: #cfd8e3;
    --gov-navy-100: #e8ecf1;
    --gov-navy-50: #f4f6f8;
    
    /* アクセントカラー - 金 */
    --gov-gold: #c9a227;
    --gov-gold-light: #d4b77a;
    --gov-gold-pale: #f0e6c8;
    
    /* セマンティックカラー */
    --gov-green: #2e7d32;
    --gov-green-light: #e8f5e9;
    --gov-red: #c62828;
    --gov-red-light: #ffebee;
    
    /* ニュートラル */
    --gov-white: #ffffff;
    --gov-black: #1a1a1a;
    --gov-gray-900: #212529;
    --gov-gray-800: #343a40;
    --gov-gray-700: #495057;
    --gov-gray-600: #6c757d;
    --gov-gray-500: #adb5bd;
    --gov-gray-400: #ced4da;
    --gov-gray-300: #dee2e6;
    --gov-gray-200: #e9ecef;
    --gov-gray-100: #f8f9fa;
    
    /* タイポグラフィ */
    --gov-font-serif: "Shippori Mincho", "Yu Mincho", serif;
    --gov-font-sans: 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
    
    /* エフェクト */
    --gov-transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    --gov-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
    --gov-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    --gov-shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
    --gov-radius: 4px;
    --gov-radius-lg: 8px;
}

* {
    box-sizing: border-box;
}

.gov-about-page {
    font-family: var(--gov-font-sans);
    color: var(--gov-gray-900);
    background: linear-gradient(180deg, var(--gov-navy-50) 0%, var(--gov-white) 100%);
    line-height: 1.7;
    min-height: 100vh;
}

/* 上部アクセントライン */
.gov-about-page::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gov-navy-800) 0%, var(--gov-gold) 50%, var(--gov-navy-800) 100%);
    z-index: 9999;
}

.gov-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ==========================================================================
   Page Header
   ========================================================================== */
.gov-page-header {
    background: linear-gradient(135deg, var(--gov-navy-900) 0%, var(--gov-navy-700) 100%);
    padding: 60px 0 50px;
    position: relative;
    overflow: hidden;
}

.gov-page-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gov-gold) 0%, var(--gov-gold-light) 50%, var(--gov-gold) 100%);
}

.gov-header-content {
    text-align: center;
    position: relative;
    z-index: 1;
}

.gov-page-title {
    font-family: var(--gov-font-serif);
    font-size: 2.5rem;
    font-weight: 600;
    color: var(--gov-white);
    margin: 0 0 16px;
    letter-spacing: 0.1em;
}

.gov-page-subtitle {
    font-size: 1.125rem;
    color: var(--gov-gold-light);
    margin: 0;
    font-weight: 500;
}

/* ==========================================================================
   Breadcrumb
   ========================================================================== */
.gov-breadcrumb {
    background: var(--gov-white);
    border-bottom: 1px solid var(--gov-gray-200);
    padding: 16px 0;
}

.gov-breadcrumb-list {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
    font-size: 13px;
}

.gov-breadcrumb-item {
    display: flex;
    align-items: center;
}

.gov-breadcrumb-item:not(:last-child)::after {
    content: '›';
    margin-left: 8px;
    color: var(--gov-gray-400);
}

.gov-breadcrumb-item a {
    color: var(--gov-navy-600);
    text-decoration: none;
    transition: color var(--gov-transition);
}

.gov-breadcrumb-item a:hover {
    color: var(--gov-navy-900);
    text-decoration: underline;
}

.gov-breadcrumb-item.current {
    color: var(--gov-gray-600);
}

/* ==========================================================================
   Main Content
   ========================================================================== */
.gov-page-content {
    padding: 60px 0 80px;
}

.gov-content-section {
    margin-bottom: 60px;
}

.gov-section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--gov-navy-800);
}

.gov-section-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--gov-navy-800);
    border-radius: var(--gov-radius);
    color: var(--gov-gold);
}

.gov-section-title {
    font-family: var(--gov-font-serif);
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0;
}

.gov-section-content {
    line-height: 1.8;
}

.gov-lead-text {
    font-size: 1.125rem;
    color: var(--gov-gray-800);
    line-height: 1.9;
    margin: 0;
    padding: 20px 24px;
    background: var(--gov-navy-50);
    border-left: 4px solid var(--gov-gold);
    border-radius: var(--gov-radius);
}

.gov-section-content p {
    font-size: 1rem;
    color: var(--gov-gray-700);
    line-height: 1.8;
    margin: 0 0 20px;
}

.gov-section-content p:last-child {
    margin-bottom: 0;
}

/* ==========================================================================
   Service Items
   ========================================================================== */
.gov-service-item {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-left: 4px solid var(--gov-navy-800);
    border-radius: var(--gov-radius);
    padding: 28px;
    margin-bottom: 24px;
    transition: all var(--gov-transition);
}

.gov-service-item:hover {
    border-left-color: var(--gov-gold);
    box-shadow: var(--gov-shadow);
}

.gov-service-item:last-child {
    margin-bottom: 0;
}

.gov-service-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.gov-service-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: var(--gov-navy-100);
    border-radius: var(--gov-radius);
    color: var(--gov-navy-700);
}

.gov-service-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gov-service-list li {
    position: relative;
    padding-left: 24px;
    margin-bottom: 10px;
    color: var(--gov-gray-700);
    line-height: 1.6;
}

.gov-service-list li:last-child {
    margin-bottom: 0;
}

.gov-service-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.6em;
    width: 8px;
    height: 8px;
    background: var(--gov-gold);
    border-radius: 50%;
}

/* ==========================================================================
   Subsection
   ========================================================================== */
.gov-subsection {
    margin-bottom: 32px;
}

.gov-subsection:last-child {
    margin-bottom: 0;
}

.gov-subsection-title {
    font-size: 1.0625rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 16px;
    padding-left: 12px;
    border-left: 3px solid var(--gov-gold);
}

.gov-info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gov-info-list li {
    position: relative;
    padding-left: 24px;
    margin-bottom: 10px;
    color: var(--gov-gray-700);
    line-height: 1.6;
}

.gov-info-list li:last-child {
    margin-bottom: 0;
}

.gov-info-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.6em;
    width: 6px;
    height: 6px;
    background: var(--gov-navy-500);
    border-radius: 50%;
}

/* ==========================================================================
   Disclaimer Box
   ========================================================================== */
.gov-disclaimer-box {
    background: var(--gov-gray-100);
    border: 1px solid var(--gov-gray-300);
    border-left: 4px solid var(--gov-red);
    padding: 24px;
    border-radius: var(--gov-radius);
}

.gov-disclaimer-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gov-disclaimer-list li {
    position: relative;
    padding-left: 32px;
    margin-bottom: 12px;
    color: var(--gov-gray-800);
    line-height: 1.6;
    font-weight: 500;
}

.gov-disclaimer-list li:last-child {
    margin-bottom: 0;
}

.gov-disclaimer-list li::before {
    content: '!';
    position: absolute;
    left: 0;
    top: 0;
    width: 22px;
    height: 22px;
    background: var(--gov-red);
    color: var(--gov-white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8125rem;
    font-weight: 700;
}

/* ==========================================================================
   Operator Info Box
   ========================================================================== */
.gov-operator-box {
    background: var(--gov-white);
    border: 2px solid var(--gov-navy-200);
    border-radius: var(--gov-radius-lg);
    padding: 32px;
    box-shadow: var(--gov-shadow-sm);
}

.gov-operator-details {
    display: grid;
    gap: 16px;
}

.gov-operator-row {
    display: grid;
    grid-template-columns: 140px 1fr;
    gap: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--gov-gray-200);
}

.gov-operator-row:last-child {
    padding-bottom: 0;
    border-bottom: none;
}

.gov-operator-label {
    font-weight: 600;
    color: var(--gov-navy-800);
    font-size: 0.9375rem;
}

.gov-operator-value {
    color: var(--gov-gray-700);
    font-size: 0.9375rem;
}

.gov-operator-value a {
    color: var(--gov-navy-700);
    text-decoration: underline;
    transition: color var(--gov-transition);
}

.gov-operator-value a:hover {
    color: var(--gov-gold);
}

/* ==========================================================================
   Text Link
   ========================================================================== */
.gov-text-link {
    color: var(--gov-navy-700);
    text-decoration: underline;
    font-weight: 500;
    transition: color var(--gov-transition);
}

.gov-text-link:hover {
    color: var(--gov-gold);
}

/* ==========================================================================
   Privacy Note Box
   ========================================================================== */
.gov-privacy-note {
    margin-top: 24px;
    padding: 20px 24px;
    background: var(--gov-navy-50);
    border: 1px solid var(--gov-navy-200);
    border-radius: var(--gov-radius);
    font-size: 0.9375rem;
}

/* ==========================================================================
   Related Links
   ========================================================================== */
.gov-related-links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.gov-related-link-card {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 20px;
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius);
    text-decoration: none;
    transition: all var(--gov-transition);
}

.gov-related-link-card:hover {
    background: var(--gov-navy-50);
    border-color: var(--gov-navy-300);
    transform: translateX(4px);
}

.gov-link-card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    background: var(--gov-navy-100);
    border-radius: var(--gov-radius);
    color: var(--gov-navy-700);
    flex-shrink: 0;
    transition: all var(--gov-transition);
}

.gov-related-link-card:hover .gov-link-card-icon {
    background: var(--gov-gold-pale);
    color: var(--gov-navy-900);
}

.gov-link-card-content {
    flex: 1;
}

.gov-link-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 4px;
}

.gov-link-card-description {
    font-size: 0.8125rem;
    color: var(--gov-gray-600);
    margin: 0;
}

/* ==========================================================================
   Responsive
   ========================================================================== */
@media (max-width: 768px) {
    .gov-page-header {
        padding: 48px 0 40px;
    }
    
    .gov-page-title {
        font-size: 1.875rem;
    }
    
    .gov-page-content {
        padding: 48px 0 60px;
    }
    
    .gov-section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .gov-section-title {
        font-size: 1.25rem;
    }
    
    .gov-service-item {
        padding: 20px;
    }
    
    .gov-operator-row {
        grid-template-columns: 1fr;
        gap: 4px;
    }
    
    .gov-related-links-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .gov-container {
        padding: 0 16px;
    }
    
    .gov-page-title {
        font-size: 1.5rem;
    }
    
    .gov-page-subtitle {
        font-size: 1rem;
    }
    
    .gov-lead-text {
        font-size: 1rem;
        padding: 16px 20px;
    }
    
    .gov-operator-box {
        padding: 24px 20px;
    }
}

/* Print */
@media print {
    .gov-about-page {
        background: white;
    }
    
    .gov-about-page::before {
        display: none;
    }
    
    .gov-page-header {
        background: white;
        color: black;
        border-bottom: 2px solid black;
    }
    
    .gov-page-title,
    .gov-page-subtitle {
        color: black;
    }
    
    .gov-related-link-card {
        page-break-inside: avoid;
    }
}
</style>

<article class="gov-about-page" itemscope itemtype="https://schema.org/AboutPage">
    
    <!-- Breadcrumb -->
    <nav class="gov-breadcrumb" aria-label="パンくずリスト">
        <div class="gov-container">
            <ol class="gov-breadcrumb-list">
                <li class="gov-breadcrumb-item"><a href="<?php echo home_url('/'); ?>">ホーム</a></li>
                <li class="gov-breadcrumb-item current" aria-current="page">当サイトについて</li>
            </ol>
        </div>
    </nav>
    
    <!-- Page Header -->
    <header class="gov-page-header">
        <div class="gov-container">
            <div class="gov-header-content">
                <h1 class="gov-page-title" itemprop="headline">当サイトについて</h1>
                <p class="gov-page-subtitle">About Us - サービス概要と運営情報</p>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <div class="gov-page-content">
        <div class="gov-container">
            
            <!-- 補助金図鑑とは -->
            <section class="gov-content-section" id="about-overview">
                <div class="gov-section-header">
                    <div class="gov-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4M12 8h.01"/>
                        </svg>
                    </div>
                    <h2 class="gov-section-title">補助金図鑑とは</h2>
                </div>
                <div class="gov-section-content">
                    <p class="gov-lead-text">
                        補助金図鑑は、全国の補助金・助成金情報を効率的に検索できるAI活用型のポータルサイトです。中小企業・個人事業主・スタートアップ企業の皆様が、ビジネスに適した支援制度を見つけ、申請手続きを円滑に進められるよう、情報提供とサポートサービスを行っています。
                    </p>
                </div>
            </section>
            
            <!-- 運営理念 -->
            <section class="gov-content-section" id="philosophy">
                <div class="gov-section-header">
                    <div class="gov-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <h2 class="gov-section-title">運営理念</h2>
                </div>
                <div class="gov-section-content">
                    <p>
                        私たちは「情報格差の解消」を通じて、日本の中小企業の成長を支援することを使命としています。従来、補助金・助成金の情報は分散しており、適切な制度を見つけることが困難でした。AIテクノロジーを活用することで、この課題を解決し、より多くの事業者が支援制度を活用できる環境づくりに貢献します。
                    </p>
                </div>
            </section>
            
            <!-- サービス内容 -->
            <section class="gov-content-section" id="services">
                <div class="gov-section-header">
                    <div class="gov-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <path d="M9 3v18M3 9h18M3 15h18M15 3v18"/>
                        </svg>
                    </div>
                    <h2 class="gov-section-title">サービス内容</h2>
                </div>
                <div class="gov-section-content">
                    
                    <div class="gov-service-item">
                        <h3 class="gov-service-title">
                            <span class="gov-service-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="M21 21l-4.35-4.35"/>
                                </svg>
                            </span>
                            補助金・助成金検索機能
                        </h3>
                        <ul class="gov-service-list">
                            <li>業種・地域・目的別の詳細検索</li>
                            <li>AIによる条件マッチング機能</li>
                            <li>リアルタイムでの最新情報更新</li>
                        </ul>
                    </div>
                    
                    <div class="gov-service-item">
                        <h3 class="gov-service-title">
                            <span class="gov-service-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </span>
                            情報提供サービス
                        </h3>
                        <ul class="gov-service-list">
                            <li>国・都道府県・市区町村の制度情報</li>
                            <li>申請要件・期限・必要書類の整理</li>
                            <li>制度変更・新設情報の通知</li>
                        </ul>
                    </div>
                    
                    <div class="gov-service-item">
                        <h3 class="gov-service-title">
                            <span class="gov-service-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </span>
                            申請サポートサービス
                        </h3>
                        <ul class="gov-service-list">
                            <li>申請書類作成のガイダンス</li>
                            <li>専門家による相談対応</li>
                            <li>申請手続きのフォローアップ</li>
                        </ul>
                    </div>
                </div>
            </section>
            
            <!-- 運営体制 -->
            <section class="gov-content-section" id="operation">
                <div class="gov-section-header">
                    <div class="gov-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <h2 class="gov-section-title">運営体制</h2>
                </div>
                <div class="gov-section-content">
                    
                    <div class="gov-subsection">
                        <h3 class="gov-subsection-title">情報収集・管理体制</h3>
                        <ul class="gov-info-list">
                            <li>各省庁・自治体の公式情報を定期的に収集</li>
                            <li>専門スタッフによる情報の精査・更新</li>
                            <li>複数のソースからの情報照合による正確性確保</li>
                        </ul>
                    </div>
                    
                    <div class="gov-subsection">
                        <h3 class="gov-subsection-title">専門チーム</h3>
                        <ul class="gov-info-list">
                            <li>補助金申請実務経験者</li>
                            <li>中小企業診断士</li>
                            <li>行政書士</li>
                            <li>ITシステム開発者</li>
                        </ul>
                    </div>
                </div>
            </section>
            
            <!-- 情報の信頼性について -->
            <section class="gov-content-section" id="reliability">
                <div class="gov-section-header">
                    <div class="gov-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <h2 class="gov-section-title">情報の信頼性について</h2>
                </div>
                <div class="gov-section-content">
                    
                    <div class="gov-subsection">
                        <h3 class="gov-subsection-title">情報源</h3>
                        <p>本サイトで提供する情報は、以下の公的機関の公式発表に基づいています：</p>
                        <ul class="gov-info-list">
                            <li>各省庁（経済産業省、厚生労働省、国土交通省等）</li>
                            <li>都道府県・市区町村の公式ウェブサイト</li>
                            <li>独立行政法人・公的機関の発表資料</li>
                        </ul>
                    </div>
                    
                    <div class="gov-subsection">
                        <h3 class="gov-subsection-title">更新頻度</h3>
                        <ul class="gov-info-list">
                            <li>毎日の自動データ収集</li>
                            <li>週2回の専門スタッフによる内容確認</li>
                            <li>重要な制度変更時の即座更新</li>
                        </ul>
                    </div>
                    
                    <div class="gov-disclaimer-box">
                        <h3 class="gov-subsection-title">免責事項</h3>
                        <ul class="gov-disclaimer-list">
                            <li>最終的な申請要件・条件は各制度の公式情報をご確認ください</li>
                            <li>申請結果についての保証はいたしかねます</li>
                            <li>制度内容は予告なく変更される場合があります</li>
                        </ul>
                    </div>
                </div>
            </section>
            
            <!-- 個人情報の取り扱い -->
            <section class="gov-content-section" id="privacy">
                <div class="gov-section-header">
                    <div class="gov-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <h2 class="gov-section-title">個人情報の取り扱い</h2>
                </div>
                <div class="gov-section-content">
                    <p>
                        当サイトでは、利用者の皆様に安心してサービスをご利用いただけるよう、個人情報保護法に基づき適切な管理を行っています。
                    </p>
                    <ul class="gov-info-list">
                        <li>取得した個人情報は、サービス提供目的のみに使用</li>
                        <li>第三者への提供は、法令に基づく場合を除き行いません</li>
                        <li>SSL暗号化通信による情報保護</li>
                        <li>定期的なセキュリティ監査の実施</li>
                    </ul>
                    <div class="gov-privacy-note">
                        詳細は<a href="<?php echo home_url('/privacy/'); ?>" class="gov-text-link">プライバシーポリシー</a>をご覧ください。
                    </div>
                </div>
            </section>
            
            <!-- 運営者情報 -->
            <section class="gov-content-section" id="operator">
                <div class="gov-section-header">
                    <div class="gov-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <h2 class="gov-section-title">運営者情報</h2>
                </div>
                <div class="gov-section-content">
                    <div class="gov-operator-box">
                        <div class="gov-operator-details">
                            <div class="gov-operator-row">
                                <span class="gov-operator-label">サイト名</span>
                                <span class="gov-operator-value">補助金図鑑</span>
                            </div>
                            <div class="gov-operator-row">
                                <span class="gov-operator-label">運営者</span>
                                <span class="gov-operator-value">中澤圭志</span>
                            </div>
                            <div class="gov-operator-row">
                                <span class="gov-operator-label">所在地</span>
                                <span class="gov-operator-value">〒136-0073<br>東京都江東区北砂3-23-8 401</span>
                            </div>
                            <div class="gov-operator-row">
                                <span class="gov-operator-label">メールアドレス</span>
                                <span class="gov-operator-value"><a href="mailto:info@hojokin-zukan.com">info@hojokin-zukan.com</a></span>
                            </div>
                            <div class="gov-operator-row">
                                <span class="gov-operator-label">サイトURL</span>
                                <span class="gov-operator-value"><a href="https://joseikin-insight.com/">https://joseikin-insight.com/</a></span>
                            </div>
                            <div class="gov-operator-row">
                                <span class="gov-operator-label">設立年</span>
                                <span class="gov-operator-value">2024年</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- 関連ページ -->
            <section class="gov-content-section" id="related-links">
                <div class="gov-section-header">
                    <div class="gov-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                    </div>
                    <h2 class="gov-section-title">関連ページ</h2>
                </div>
                
                <div class="gov-related-links-grid">
                    <a href="<?php echo home_url('/contact/'); ?>" class="gov-related-link-card">
                        <div class="gov-link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>
                        <div class="gov-link-card-content">
                            <h3 class="gov-link-card-title">お問い合わせ</h3>
                            <p class="gov-link-card-description">サービスに関するご質問はこちら</p>
                        </div>
                    </a>
                    
                    <a href="<?php echo home_url('/privacy/'); ?>" class="gov-related-link-card">
                        <div class="gov-link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <div class="gov-link-card-content">
                            <h3 class="gov-link-card-title">プライバシーポリシー</h3>
                            <p class="gov-link-card-description">個人情報の取り扱いについて</p>
                        </div>
                    </a>
                    
                    <a href="<?php echo home_url('/terms/'); ?>" class="gov-related-link-card">
                        <div class="gov-link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div class="gov-link-card-content">
                            <h3 class="gov-link-card-title">利用規約</h3>
                            <p class="gov-link-card-description">サービス利用の規約について</p>
                        </div>
                    </a>
                    
                    <a href="<?php echo home_url('/disclaimer/'); ?>" class="gov-related-link-card">
                        <div class="gov-link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                        <div class="gov-link-card-content">
                            <h3 class="gov-link-card-title">免責事項</h3>
                            <p class="gov-link-card-description">ご利用上の注意事項</p>
                        </div>
                    </a>
                </div>
            </section>
            
        </div>
    </div>
</article>

<?php get_footer(); ?>
