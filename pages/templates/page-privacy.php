<?php
/**
 * 補助金・助成金情報サイト - プライバシーポリシーページ
 * Grant & Subsidy Information Site - Privacy Policy Page
 * @package Grant_Insight_Privacy
 * @version 2.0-government-design
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

get_header();

// 構造化データ
$privacy_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'プライバシーポリシー - 補助金図鑑',
    'description' => '補助金図鑑のプライバシーポリシー。個人情報の取り扱い、収集する情報、利用目的などについて定めています。',
    'url' => 'https://joseikin-insight.com/privacy/',
    'datePublished' => '2025-10-09',
    'dateModified' => '2025-10-09'
);
?>

<!-- 構造化データ -->
<script type="application/ld+json">
<?php echo wp_json_encode($privacy_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<style>
/* ==========================================================================
   Official Government Design - Privacy Policy Page CSS
   官公庁デザイン - プライバシーポリシーページ
   ========================================================================== */

:root {
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
    --gov-gold: #c9a227;
    --gov-gold-light: #d4b77a;
    --gov-gold-pale: #f0e6c8;
    --gov-green: #2e7d32;
    --gov-green-light: #e8f5e9;
    --gov-red: #c62828;
    --gov-red-light: #ffebee;
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
    --gov-font-serif: "Shippori Mincho", "Yu Mincho", serif;
    --gov-font-sans: 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
    --gov-transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    --gov-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
    --gov-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    --gov-shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
    --gov-radius: 4px;
    --gov-radius-lg: 8px;
}

* { box-sizing: border-box; }

.gov-privacy-page {
    font-family: var(--gov-font-sans);
    color: var(--gov-gray-900);
    background: linear-gradient(180deg, var(--gov-navy-50) 0%, var(--gov-white) 100%);
    line-height: 1.7;
    min-height: 100vh;
}

.gov-privacy-page::before {
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
    max-width: 960px;
    margin: 0 auto;
    padding: 0 24px;
}

/* Header */
.gov-page-header {
    background: linear-gradient(135deg, var(--gov-navy-900) 0%, var(--gov-navy-700) 100%);
    padding: 60px 0 50px;
    position: relative;
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
}

.gov-page-title {
    font-family: var(--gov-font-serif);
    font-size: 2.5rem;
    font-weight: 600;
    color: var(--gov-white);
    margin: 0 0 20px;
    letter-spacing: 0.1em;
}

.gov-page-meta {
    display: flex;
    justify-content: center;
    gap: 32px;
    flex-wrap: wrap;
}

.gov-meta-item {
    font-size: 0.875rem;
    color: var(--gov-gold-light);
    margin: 0;
}

/* Breadcrumb */
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

/* Content */
.gov-page-content {
    padding: 60px 0 80px;
}

.gov-content-section {
    margin-bottom: 48px;
}

/* Preamble */
.gov-preamble {
    background: var(--gov-navy-50);
    border-left: 4px solid var(--gov-gold);
    padding: 24px 28px;
    border-radius: var(--gov-radius);
    margin-bottom: 48px;
}

.gov-preamble p {
    margin: 0;
    color: var(--gov-gray-800);
    line-height: 1.9;
    font-size: 1rem;
}

/* TOC */
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

.gov-toc-nav {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    padding: 24px;
}

.gov-toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 8px;
}

.gov-toc-link {
    display: block;
    padding: 10px 16px;
    color: var(--gov-navy-800);
    text-decoration: none;
    border-radius: var(--gov-radius);
    transition: all var(--gov-transition);
    font-size: 0.9375rem;
    border-left: 3px solid transparent;
}

.gov-toc-link:hover {
    background: var(--gov-navy-50);
    border-left-color: var(--gov-gold);
    transform: translateX(4px);
}

/* Policy Sections */
.gov-policy-section {
    scroll-margin-top: 80px;
    margin-bottom: 48px;
}

.gov-policy-title {
    font-family: var(--gov-font-serif);
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gov-white);
    margin: 0 0 24px;
    padding: 16px 20px;
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-700) 100%);
    border-radius: var(--gov-radius);
}

.gov-policy-content {
    padding: 0 8px;
}

.gov-policy-content p {
    color: var(--gov-gray-700);
    line-height: 1.8;
    margin-bottom: 20px;
}

.gov-article-list {
    list-style: none;
    counter-reset: article-counter;
    padding: 0;
    margin: 0;
}

.gov-article-list > li {
    counter-increment: article-counter;
    position: relative;
    padding-left: 32px;
    margin-bottom: 20px;
    line-height: 1.8;
    color: var(--gov-gray-700);
}

.gov-article-list > li::before {
    content: counter(article-counter) ".";
    position: absolute;
    left: 0;
    font-weight: 600;
    color: var(--gov-navy-800);
}

.gov-sub-list {
    list-style: none;
    padding: 0;
    margin: 16px 0 0;
}

.gov-sub-list li {
    position: relative;
    padding-left: 24px;
    margin-bottom: 10px;
    line-height: 1.7;
}

.gov-sub-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.65em;
    width: 6px;
    height: 6px;
    background: var(--gov-gold);
    border-radius: 50%;
}

.gov-sub-sub-list {
    list-style: none;
    padding: 0;
    margin: 12px 0 0 16px;
}

.gov-sub-sub-list li {
    position: relative;
    padding-left: 20px;
    margin-bottom: 8px;
    font-size: 0.9375rem;
}

.gov-sub-sub-list li::before {
    content: '・';
    position: absolute;
    left: 0;
    color: var(--gov-gray-500);
}

.gov-subsection-title {
    font-size: 1.0625rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 28px 0 16px;
    padding-left: 12px;
    border-left: 3px solid var(--gov-gold);
}

.gov-info-list {
    list-style: none;
    padding: 0;
    margin: 16px 0;
}

.gov-info-list li {
    position: relative;
    padding-left: 24px;
    margin-bottom: 10px;
    color: var(--gov-gray-700);
    line-height: 1.7;
}

.gov-info-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.65em;
    width: 6px;
    height: 6px;
    background: var(--gov-navy-500);
    border-radius: 50%;
}

.gov-purpose-list {
    list-style: none;
    counter-reset: purpose-counter;
    padding: 0;
    margin: 16px 0;
}

.gov-purpose-list li {
    counter-increment: purpose-counter;
    position: relative;
    padding-left: 28px;
    margin-bottom: 10px;
    color: var(--gov-gray-700);
    line-height: 1.7;
}

.gov-purpose-list li::before {
    content: counter(purpose-counter) ".";
    position: absolute;
    left: 0;
    font-weight: 600;
    color: var(--gov-navy-800);
}

/* Highlight Section */
.gov-highlight-section {
    background: var(--gov-navy-50);
    border: 2px solid var(--gov-navy-200);
    border-radius: var(--gov-radius-lg);
    padding: 28px;
}

/* Contact Box */
.gov-contact-box {
    background: var(--gov-white);
    border: 2px solid var(--gov-navy-200);
    border-radius: var(--gov-radius-lg);
    padding: 28px;
    margin-top: 24px;
}

.gov-contact-title {
    font-family: var(--gov-font-serif);
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--gov-gold);
}

.gov-contact-details {
    display: grid;
    gap: 16px;
}

.gov-contact-row {
    display: grid;
    grid-template-columns: 140px 1fr;
    gap: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--gov-gray-200);
}

.gov-contact-row:last-child {
    padding-bottom: 0;
    border-bottom: none;
}

.gov-contact-label {
    font-weight: 600;
    color: var(--gov-navy-800);
    font-size: 0.9375rem;
}

.gov-contact-value {
    color: var(--gov-gray-700);
    font-size: 0.9375rem;
}

.gov-contact-value a {
    color: var(--gov-navy-700);
    text-decoration: underline;
    transition: color var(--gov-transition);
}

.gov-contact-value a:hover {
    color: var(--gov-gold);
}

.gov-contact-note {
    margin-top: 20px;
    padding: 16px;
    background: var(--gov-gray-100);
    border-radius: var(--gov-radius);
    font-size: 0.875rem;
    color: var(--gov-gray-600);
    line-height: 1.7;
}

.gov-text-link {
    color: var(--gov-navy-700);
    text-decoration: underline;
    font-weight: 500;
    transition: color var(--gov-transition);
}

.gov-text-link:hover {
    color: var(--gov-gold);
}

/* Security Grid */
.gov-security-intro {
    color: var(--gov-gray-700);
    line-height: 1.8;
    margin-bottom: 28px;
}

.gov-security-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

.gov-security-item {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    padding: 28px;
    text-align: center;
    transition: all var(--gov-transition);
}

.gov-security-item:hover {
    border-color: var(--gov-navy-300);
    box-shadow: var(--gov-shadow);
}

.gov-security-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 64px;
    height: 64px;
    background: var(--gov-navy-50);
    border: 2px solid var(--gov-navy-200);
    border-radius: 50%;
    margin-bottom: 16px;
    color: var(--gov-navy-700);
}

.gov-security-title {
    font-size: 1.0625rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 10px;
}

.gov-security-description {
    font-size: 0.875rem;
    color: var(--gov-gray-600);
    line-height: 1.6;
    margin: 0;
}

/* Supplementary */
.gov-supplementary {
    background: var(--gov-gray-100);
    border-left: 4px solid var(--gov-navy-600);
    padding: 20px 24px;
    border-radius: var(--gov-radius);
}

.gov-supplementary p {
    margin: 0;
    color: var(--gov-gray-700);
}

/* Related Links */
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

.gov-link-card-content { flex: 1; }

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

/* Page Top Button */
.gov-page-top-wrapper {
    text-align: center;
    margin-top: 60px;
}

.gov-page-top-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-700) 100%);
    color: var(--gov-white);
    text-decoration: none;
    border-radius: var(--gov-radius);
    font-weight: 600;
    transition: all var(--gov-transition);
    box-shadow: var(--gov-shadow-sm);
}

.gov-page-top-btn:hover {
    background: linear-gradient(135deg, var(--gov-navy-900) 0%, var(--gov-navy-800) 100%);
    transform: translateY(-2px);
    box-shadow: var(--gov-shadow);
}

/* Responsive */
@media (max-width: 768px) {
    .gov-page-header { padding: 48px 0 40px; }
    .gov-page-title { font-size: 1.875rem; }
    .gov-page-meta { flex-direction: column; gap: 8px; }
    .gov-toc-list { grid-template-columns: 1fr; }
    .gov-contact-row { grid-template-columns: 1fr; gap: 4px; }
    .gov-security-grid { grid-template-columns: 1fr; }
    .gov-related-links-grid { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
    .gov-container { padding: 0 16px; }
    .gov-page-title { font-size: 1.5rem; }
    .gov-section-header { flex-direction: column; align-items: flex-start; gap: 8px; }
    .gov-policy-title { font-size: 1.125rem; padding: 14px 16px; }
}

html { scroll-behavior: smooth; }
</style>

<article class="gov-privacy-page" itemscope itemtype="https://schema.org/WebPage">
    
    <!-- Breadcrumb -->
    <nav class="gov-breadcrumb" aria-label="パンくずリスト">
        <div class="gov-container">
            <ol class="gov-breadcrumb-list">
                <li class="gov-breadcrumb-item"><a href="<?php echo home_url('/'); ?>">ホーム</a></li>
                <li class="gov-breadcrumb-item current" aria-current="page">プライバシーポリシー</li>
            </ol>
        </div>
    </nav>
    
    <!-- Header -->
    <header class="gov-page-header">
        <div class="gov-container">
            <div class="gov-header-content">
                <h1 class="gov-page-title" itemprop="headline">プライバシーポリシー</h1>
                <div class="gov-page-meta">
                    <p class="gov-meta-item">制定日：<time datetime="2025-10-09">2025年10月9日</time></p>
                    <p class="gov-meta-item">最終改定日：<time datetime="2025-10-09">2025年10月9日</time></p>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Content -->
    <div class="gov-page-content">
        <div class="gov-container">
            
            <!-- Preamble -->
            <div class="gov-preamble">
                <p>補助金図鑑（以下「当サイト」といいます）は、運営者である中澤圭志（以下「当社」といいます）が、ユーザーの個人情報の重要性を認識し、個人情報の保護に関する法律（以下「個人情報保護法」といいます）を遵守すると共に、以下のプライバシーポリシー（以下「本ポリシー」といいます）に従い、適切な取扱い及び保護に努めます。</p>
            </div>
            
            <!-- TOC -->
            <section class="gov-content-section">
                <div class="gov-section-header">
                    <div class="gov-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="8" y1="6" x2="21" y2="6"/>
                            <line x1="8" y1="12" x2="21" y2="12"/>
                            <line x1="8" y1="18" x2="21" y2="18"/>
                            <line x1="3" y1="6" x2="3.01" y2="6"/>
                            <line x1="3" y1="12" x2="3.01" y2="12"/>
                            <line x1="3" y1="18" x2="3.01" y2="18"/>
                        </svg>
                    </div>
                    <h2 class="gov-section-title">目次</h2>
                </div>
                <nav class="gov-toc-nav">
                    <ul class="gov-toc-list">
                        <li><a href="#section-1" class="gov-toc-link">第1条（個人情報）</a></li>
                        <li><a href="#section-2" class="gov-toc-link">第2条（個人情報の収集方法）</a></li>
                        <li><a href="#section-3" class="gov-toc-link">第3条（個人情報を収集・利用する目的）</a></li>
                        <li><a href="#section-4" class="gov-toc-link">第4条（利用目的の変更）</a></li>
                        <li><a href="#section-5" class="gov-toc-link">第5条（個人情報の第三者提供）</a></li>
                        <li><a href="#section-6" class="gov-toc-link">第6条（個人情報の開示）</a></li>
                        <li><a href="#section-7" class="gov-toc-link">第7条（個人情報の訂正及び削除）</a></li>
                        <li><a href="#section-8" class="gov-toc-link">第8条（個人情報の利用停止等）</a></li>
                        <li><a href="#section-9" class="gov-toc-link">第9条（Cookie等の使用）</a></li>
                        <li><a href="#section-10" class="gov-toc-link">第10条（アクセス解析ツール）</a></li>
                        <li><a href="#section-11" class="gov-toc-link">第11条（プライバシーポリシーの変更）</a></li>
                        <li><a href="#section-12" class="gov-toc-link">第12条（お問い合わせ窓口）</a></li>
                    </ul>
                </nav>
            </section>
            
            <!-- Article 1 -->
            <section class="gov-policy-section" id="section-1">
                <h2 class="gov-policy-title">第1条（個人情報）</h2>
                <div class="gov-policy-content">
                    <p>「個人情報」とは、個人情報保護法にいう「個人情報」を指すものとし、生存する個人に関する情報であって、当該情報に含まれる氏名、生年月日、住所、電話番号、連絡先その他の記述等により特定の個人を識別できる情報及び容貌、指紋、声紋にかかるデータ、及び健康保険証の保険者番号などの当該情報単体から特定の個人を識別できる情報（個人識別情報）を指します。</p>
                </div>
            </section>
            
            <!-- Article 2 -->
            <section class="gov-policy-section" id="section-2">
                <h2 class="gov-policy-title">第2条（個人情報の収集方法）</h2>
                <div class="gov-policy-content">
                    <p>当サイトは、ユーザーが利用登録をする際に氏名、メールアドレス、会社名、電話番号などの個人情報をお尋ねすることがあります。また、ユーザーと提携先などとの間でなされたユーザーの個人情報を含む取引記録や決済に関する情報を、当サイトの提携先（情報提供元、広告主、広告配信先などを含みます。以下「提携先」といいます）などから収集することがあります。</p>
                    
                    <h3 class="gov-subsection-title">収集する個人情報の種類</h3>
                    <ul class="gov-info-list">
                        <li>氏名（法人の場合は担当者名）</li>
                        <li>メールアドレス</li>
                        <li>電話番号</li>
                        <li>会社名・団体名</li>
                        <li>住所</li>
                        <li>業種・従業員数等の属性情報</li>
                        <li>その他、サービス提供に必要な情報</li>
                    </ul>
                </div>
            </section>
            
            <!-- Article 3 -->
            <section class="gov-policy-section" id="section-3">
                <h2 class="gov-policy-title">第3条（個人情報を収集・利用する目的）</h2>
                <div class="gov-policy-content">
                    <p>当サイトが個人情報を収集・利用する目的は、以下のとおりです。</p>
                    <ol class="gov-purpose-list">
                        <li>当サイトのサービスの提供・運営のため</li>
                        <li>ユーザーからのお問い合わせに回答するため（本人確認を行うことを含む）</li>
                        <li>ユーザーが利用中のサービスの新機能、更新情報、キャンペーン等及び当サイトが提供する他のサービスの案内のメールを送付するため</li>
                        <li>メンテナンス、重要なお知らせなど必要に応じたご連絡のため</li>
                        <li>利用規約に違反したユーザーや、不正・不当な目的でサービスを利用しようとするユーザーの特定をし、ご利用をお断りするため</li>
                        <li>ユーザーにご自身の登録情報の閲覧や変更、削除、ご利用状況の閲覧を行っていただくため</li>
                        <li>サービスの改善・新サービスの開発のための分析を行うため</li>
                        <li>補助金・助成金情報のマッチング精度向上のため</li>
                        <li>上記の利用目的に付随する目的</li>
                    </ol>
                </div>
            </section>
            
            <!-- Article 4 -->
            <section class="gov-policy-section" id="section-4">
                <h2 class="gov-policy-title">第4条（利用目的の変更）</h2>
                <div class="gov-policy-content">
                    <ol class="gov-article-list">
                        <li>当サイトは、利用目的が変更前と関連性を有すると合理的に認められる場合に限り、個人情報の利用目的を変更するものとします。</li>
                        <li>利用目的の変更を行った場合には、変更後の目的について、当サイト所定の方法により、ユーザーに通知し、または本ウェブサイト上に公表するものとします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- Article 5 -->
            <section class="gov-policy-section gov-highlight-section" id="section-5">
                <h2 class="gov-policy-title">第5条（個人情報の第三者提供）</h2>
                <div class="gov-policy-content">
                    <ol class="gov-article-list">
                        <li>当サイトは、次に掲げる場合を除いて、あらかじめユーザーの同意を得ることなく、第三者に個人情報を提供することはありません。ただし、個人情報保護法その他の法令で認められる場合を除きます。
                            <ul class="gov-sub-list">
                                <li>人の生命、身体または財産の保護のために必要がある場合であって、本人の同意を得ることが困難であるとき</li>
                                <li>公衆衛生の向上または児童の健全な育成の推進のために特に必要がある場合であって、本人の同意を得ることが困難であるとき</li>
                                <li>国の機関もしくは地方公共団体またはその委託を受けた者が法令の定める事務を遂行することに対して協力する必要がある場合であって、本人の同意を得ることにより当該事務の遂行に支障を及ぼすおそれがあるとき</li>
                                <li>予め次の事項を告知あるいは公表し、かつ当サイトが個人情報保護委員会に届出をしたとき
                                    <ul class="gov-sub-sub-list">
                                        <li>利用目的に第三者への提供を含むこと</li>
                                        <li>第三者に提供されるデータの項目</li>
                                        <li>第三者への提供の手段または方法</li>
                                        <li>本人の求めに応じて個人情報の第三者への提供を停止すること</li>
                                        <li>本人の求めを受け付ける方法</li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li>前項の定めにかかわらず、次に掲げる場合には、当該情報の提供先は第三者に該当しないものとします。
                            <ul class="gov-sub-list">
                                <li>当サイトが利用目的の達成に必要な範囲内において個人情報の取扱いの全部または一部を委託する場合</li>
                                <li>合併その他の事由による事業の承継に伴って個人情報が提供される場合</li>
                                <li>個人情報を特定の者との間で共同して利用する場合であって、その旨並びに共同して利用される個人情報の項目、共同して利用する者の範囲、利用する者の利用目的および当該個人情報の管理について責任を有する者の氏名または名称について、あらかじめ本人に通知し、または本人が容易に知り得る状態に置いた場合</li>
                            </ul>
                        </li>
                    </ol>
                </div>
            </section>
            
            <!-- Article 6 -->
            <section class="gov-policy-section" id="section-6">
                <h2 class="gov-policy-title">第6条（個人情報の開示）</h2>
                <div class="gov-policy-content">
                    <ol class="gov-article-list">
                        <li>当サイトは、本人から個人情報の開示を求められたときは、本人に対し、遅滞なくこれを開示します。ただし、開示することにより次のいずれかに該当する場合は、その全部または一部を開示しないこともあり、開示しない決定をした場合には、その旨を遅滞なく通知します。
                            <ul class="gov-sub-list">
                                <li>本人または第三者の生命、身体、財産その他の権利利益を害するおそれがある場合</li>
                                <li>当サイトの業務の適正な実施に著しい支障を及ぼすおそれがある場合</li>
                                <li>その他法令に違反することとなる場合</li>
                            </ul>
                        </li>
                        <li>前項の定めにかかわらず、履歴情報および特性情報などの個人情報以外の情報については、原則として開示いたしません。</li>
                    </ol>
                </div>
            </section>
            
            <!-- Article 7 -->
            <section class="gov-policy-section" id="section-7">
                <h2 class="gov-policy-title">第7条（個人情報の訂正及び削除）</h2>
                <div class="gov-policy-content">
                    <ol class="gov-article-list">
                        <li>ユーザーは、当サイトの保有する自己の個人情報が誤った情報である場合には、当サイトが定める手続きにより、当サイトに対して個人情報の訂正、追加または削除（以下「訂正等」といいます）を請求することができます。</li>
                        <li>当サイトは、ユーザーから前項の請求を受けてその請求に応じる必要があると判断した場合には、遅滞なく、当該個人情報の訂正等を行うものとします。</li>
                        <li>当サイトは、前項の規定に基づき訂正等を行った場合、または訂正等を行わない旨の決定をしたときは遅滞なく、これをユーザーに通知します。</li>
                    </ol>
                </div>
            </section>
            
            <!-- Article 8 -->
            <section class="gov-policy-section" id="section-8">
                <h2 class="gov-policy-title">第8条（個人情報の利用停止等）</h2>
                <div class="gov-policy-content">
                    <ol class="gov-article-list">
                        <li>当サイトは、本人から、個人情報が、利用目的の範囲を超えて取り扱われているという理由、または不正の手段により取得されたものであるという理由により、その利用の停止または消去（以下「利用停止等」といいます）を求められた場合には、遅滞なく必要な調査を行います。</li>
                        <li>前項の調査結果に基づき、その請求に応じる必要があると判断した場合には、遅滞なく、当該個人情報の利用停止等を行います。</li>
                        <li>当サイトは、前項の規定に基づき利用停止等を行った場合、または利用停止等を行わない旨の決定をしたときは、遅滞なく、これをユーザーに通知します。</li>
                        <li>前2項にかかわらず、利用停止等に多額の費用を有する場合その他利用停止等を行うことが困難な場合であって、ユーザーの権利利益を保護するために必要なこれに代わるべき措置をとれる場合は、この代替策を講じるものとします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- Article 9 -->
            <section class="gov-policy-section" id="section-9">
                <h2 class="gov-policy-title">第9条（Cookie（クッキー）等の使用）</h2>
                <div class="gov-policy-content">
                    <ol class="gov-article-list">
                        <li>当サイトは、ユーザーによるサービスの利用状況を把握するため、Cookie（クッキー）を使用することがあります。Cookieとは、ウェブサーバーからユーザーのブラウザに送信され、ユーザーが使用しているコンピュータのハードディスクに保存される情報です。</li>
                        <li>Cookieには個人を特定する情報は含まれませんが、当サイトが保有する個人情報と関連付けられる場合があります。</li>
                        <li>ユーザーは、ブラウザの設定によりCookieの受け取りを拒否することができます。ただし、Cookieを拒否した場合、当サイトの一部のサービスが利用できなくなる場合があります。</li>
                    </ol>
                    
                    <h3 class="gov-subsection-title">Cookieの利用目的</h3>
                    <ul class="gov-info-list">
                        <li>ユーザーの利便性向上（ログイン状態の保持など）</li>
                        <li>サイトの利用状況の分析</li>
                        <li>サービスの改善・最適化</li>
                        <li>広告配信の最適化</li>
                    </ul>
                </div>
            </section>
            
            <!-- Article 10 -->
            <section class="gov-policy-section" id="section-10">
                <h2 class="gov-policy-title">第10条（アクセス解析ツール）</h2>
                <div class="gov-policy-content">
                    <p>当サイトでは、Googleによるアクセス解析ツール「Googleアナリティクス」を使用しています。このGoogleアナリティクスはデータの収集のためにCookieを使用しています。このデータは匿名で収集されており、個人を特定するものではありません。</p>
                    <p>この機能はCookieを無効にすることで収集を拒否することが出来ますので、お使いのブラウザの設定をご確認ください。この規約に関しての詳細は<a href="https://marketingplatform.google.com/about/analytics/terms/jp/" target="_blank" rel="noopener noreferrer" class="gov-text-link">Googleアナリティクスサービス利用規約</a>のページや<a href="https://policies.google.com/technologies/ads?hl=ja" target="_blank" rel="noopener noreferrer" class="gov-text-link">Googleポリシーと規約</a>ページをご覧ください。</p>
                    
                    <h3 class="gov-subsection-title">使用しているアクセス解析ツール</h3>
                    <ul class="gov-info-list">
                        <li>Googleアナリティクス</li>
                        <li>その他、サービス改善のための解析ツール</li>
                    </ul>
                </div>
            </section>
            
            <!-- Article 11 -->
            <section class="gov-policy-section" id="section-11">
                <h2 class="gov-policy-title">第11条（プライバシーポリシーの変更）</h2>
                <div class="gov-policy-content">
                    <ol class="gov-article-list">
                        <li>本ポリシーの内容は、法令その他本ポリシーに別段の定めのある事項を除いて、ユーザーに通知することなく、変更することができるものとします。</li>
                        <li>当サイトが別途定める場合を除いて、変更後のプライバシーポリシーは、本ウェブサイトに掲載したときから効力を生じるものとします。</li>
                        <li>本ポリシーの変更は、変更後のプライバシーポリシーが当サイトに掲載された時点で有効になるものとします。ユーザーは定期的に本ポリシーを確認し、最新の内容を把握するものとします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- Article 12 -->
            <section class="gov-policy-section" id="section-12">
                <h2 class="gov-policy-title">第12条（お問い合わせ窓口）</h2>
                <div class="gov-policy-content">
                    <p>本ポリシーに関するお問い合わせは、下記の窓口までお願いいたします。</p>
                    
                    <div class="gov-contact-box">
                        <h3 class="gov-contact-title">運営者情報</h3>
                        <div class="gov-contact-details">
                            <div class="gov-contact-row">
                                <span class="gov-contact-label">サイト名</span>
                                <span class="gov-contact-value">補助金図鑑</span>
                            </div>
                            <div class="gov-contact-row">
                                <span class="gov-contact-label">運営者</span>
                                <span class="gov-contact-value">中澤圭志</span>
                            </div>
                            <div class="gov-contact-row">
                                <span class="gov-contact-label">所在地</span>
                                <span class="gov-contact-value">〒136-0073<br>東京都江東区北砂3-23-8 401</span>
                            </div>
                            <div class="gov-contact-row">
                                <span class="gov-contact-label">メールアドレス</span>
                                <span class="gov-contact-value"><a href="mailto:info@hojokin-zukan.com">info@hojokin-zukan.com</a></span>
                            </div>
                            <div class="gov-contact-row">
                                <span class="gov-contact-label">お問い合わせ</span>
                                <span class="gov-contact-value"><a href="<?php echo home_url('/contact/'); ?>">お問い合わせフォーム</a></span>
                            </div>
                        </div>
                        <p class="gov-contact-note">
                            ※お問い合わせへの回答には、2〜3営業日程度お時間をいただく場合がございます。<br>
                            ※土日祝日、年末年始は休業日とさせていただきます。
                        </p>
                    </div>
                </div>
            </section>
            
            <!-- Security Measures -->
            <section class="gov-content-section">
                <div class="gov-section-header">
                    <div class="gov-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <h2 class="gov-section-title">個人情報保護の取り組み</h2>
                </div>
                
                <p class="gov-security-intro">当サイトでは、お預かりした個人情報を適切に保護するため、以下のセキュリティ対策を実施しています。</p>
                
                <div class="gov-security-grid">
                    <div class="gov-security-item">
                        <div class="gov-security-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <h3 class="gov-security-title">SSL暗号化通信</h3>
                        <p class="gov-security-description">個人情報の送信時には、SSL（Secure Socket Layer）による暗号化通信を使用し、第三者による情報の盗聴、改ざん、なりすましを防止しています。</p>
                    </div>
                    
                    <div class="gov-security-item">
                        <div class="gov-security-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <h3 class="gov-security-title">アクセス制限</h3>
                        <p class="gov-security-description">個人情報へのアクセスは、業務上必要な従業員のみに限定し、適切なアクセス管理を実施しています。</p>
                    </div>
                    
                    <div class="gov-security-item">
                        <div class="gov-security-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <h3 class="gov-security-title">定期的な監査</h3>
                        <p class="gov-security-description">個人情報の取り扱い状況について、定期的に内部監査を実施し、適切な管理体制を維持しています。</p>
                    </div>
                    
                    <div class="gov-security-item">
                        <div class="gov-security-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <h3 class="gov-security-title">従業員教育</h3>
                        <p class="gov-security-description">全従業員に対して、個人情報保護に関する教育・研修を定期的に実施し、意識向上に努めています。</p>
                    </div>
                </div>
            </section>
            
            <!-- Supplementary -->
            <section class="gov-content-section">
                <h2 class="gov-policy-title">附　則</h2>
                <div class="gov-supplementary">
                    <p>本ポリシーは、2025年10月9日から施行いたします。</p>
                </div>
            </section>
            
            <!-- Related Links -->
            <section class="gov-content-section">
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
                    <a href="<?php echo home_url('/about/'); ?>" class="gov-related-link-card">
                        <div class="gov-link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4M12 8h.01"/>
                            </svg>
                        </div>
                        <div class="gov-link-card-content">
                            <h3 class="gov-link-card-title">当サイトについて</h3>
                            <p class="gov-link-card-description">サービス概要と運営情報</p>
                        </div>
                    </a>
                    
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
            
            <!-- Page Top Button -->
            <div class="gov-page-top-wrapper">
                <a href="#" class="gov-page-top-btn" id="pageTopBtn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    <span>ページトップへ</span>
                </a>
            </div>
            
        </div>
    </div>
</article>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Page top button
    const pageTopBtn = document.getElementById('pageTopBtn');
    if (pageTopBtn) {
        pageTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    
    // TOC smooth scroll
    document.querySelectorAll('.gov-toc-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
</script>

<?php get_footer(); ?>
