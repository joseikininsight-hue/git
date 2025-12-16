<?php
/**
 * 補助金・助成金情報サイト - 免責事項ページ（統一デザイン版）
 * Grant & Subsidy Information Site - Disclaimer Page (Unified Design)
 * @package Grant_Insight_Disclaimer
 * @version 2.0-unified-design
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

get_header();

// 構造化データ
$disclaimer_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => '免責事項 - 補助金インサイト',
    'description' => '補助金インサイトの免責事項。情報の正確性、利用上の注意事項、責任の限界、技術的制約について定めています。',
    'url' => 'https://joseikin-insight.com/disclaimer/',
    'datePublished' => '2025-10-09',
    'dateModified' => '2025-12-16'
);
?>

<!-- 構造化データ -->
<script type="application/ld+json">
<?php echo wp_json_encode($disclaimer_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<article class="gov-disclaimer-page" itemscope itemtype="https://schema.org/WebPage">
    
    <!-- Breadcrumb -->
    <nav class="gov-breadcrumb" aria-label="パンくずリスト">
        <div class="gov-container">
            <ol class="gov-breadcrumb-list">
                <li class="gov-breadcrumb-item"><a href="<?php echo home_url('/'); ?>">ホーム</a></li>
                <li class="gov-breadcrumb-item current" aria-current="page">免責事項</li>
            </ol>
        </div>
    </nav>
    
    <!-- ページヘッダー -->
    <header class="gov-page-header">
        <div class="gov-container">
            <div class="gov-header-content">
                <h1 class="gov-page-title" itemprop="headline">免責事項</h1>
                <div class="gov-page-meta">
                    <p class="gov-meta-item">制定日：<time datetime="2025-10-09">2025年10月9日</time></p>
                    <p class="gov-meta-item">最終改定日：<time datetime="2025-12-16">2025年12月16日</time></p>
                </div>
            </div>
        </div>
    </header>
    
    <!-- メインコンテンツ -->
    <div class="gov-page-content">
        <div class="gov-container">
            
            <!-- 前文 -->
            <section class="content-section preamble-section">
                <div class="preamble-content">
                    <p>
                        補助金インサイト（以下「当サイト」といいます）をご利用いただく前に、以下の免責事項を必ずお読みください。当サイトのご利用をもって、本免責事項に同意いただいたものとみなします。
                    </p>
                </div>
            </section>
            
            <!-- 目次 -->
            <section class="content-section toc-section">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="8" y1="6" x2="21" y2="6"/>
                            <line x1="8" y1="12" x2="21" y2="12"/>
                            <line x1="8" y1="18" x2="21" y2="18"/>
                            <line x1="3" y1="6" x2="3.01" y2="6"/>
                            <line x1="3" y1="12" x2="3.01" y2="12"/>
                            <line x1="3" y1="18" x2="3.01" y2="18"/>
                        </svg>
                    </span>
                    目次
                </h2>
                <nav class="toc-nav">
                    <ul class="toc-list">
                        <li><a href="#section-1" class="toc-link">1. 情報の正確性について</a></li>
                        <li><a href="#section-2" class="toc-link">2. 利用上の注意事項</a></li>
                        <li><a href="#section-3" class="toc-link">3. 責任の限界</a></li>
                        <li><a href="#section-4" class="toc-link">4. 第三者サービスについて</a></li>
                        <li><a href="#section-5" class="toc-link">5. 技術的制約</a></li>
                        <li><a href="#section-6" class="toc-link">6. 法的事項</a></li>
                        <li><a href="#section-7" class="toc-link">7. 免責事項の変更</a></li>
                        <li><a href="#section-8" class="toc-link">8. お問い合わせ</a></li>
                    </ul>
                </nav>
            </section>
            
            <!-- 1. 情報の正確性について -->
            <section class="content-section disclaimer-section" id="section-1">
                <h2 class="disclaimer-title">1. 情報の正確性について</h2>
                <div class="disclaimer-content">
                    
                    <h3 class="subsection-title">情報収集と更新について</h3>
                    <p>当サイトは、各省庁・都道府県・市区町村が公式に発表する補助金・助成金情報を基に、データベースの構築・更新を行っております。情報の収集は以下の方法により実施しております：</p>
                    <ul class="info-list">
                        <li>各行政機関の公式ウェブサイトからの情報取得</li>
                        <li>公示・公告文書の定期的な確認</li>
                        <li>制度所管部署からの直接情報提供</li>
                        <li>専門スタッフによる情報の精査・検証</li>
                    </ul>
                    
                    <h3 class="subsection-title">情報の制約</h3>
                    <p>本サイトで提供する情報について、以下の制約があることをご理解ください：</p>
                    <ul class="info-list">
                        <li><strong>完全性の制約</strong>：全ての補助金・助成金制度を網羅することを目指しておりますが、一部の制度が掲載されていない可能性があります</li>
                        <li><strong>正確性の制約</strong>：情報は定期的に更新しておりますが、制度変更の反映に時間を要する場合があります</li>
                        <li><strong>最新性の制約</strong>：制度内容は予告なく変更される場合があり、本サイトの情報と実際の制度内容に差異が生じる可能性があります</li>
                    </ul>
                </div>
            </section>
            
            <!-- 2. 利用上の注意事項 -->
            <section class="content-section disclaimer-section" id="section-2">
                <h2 class="disclaimer-title">2. 利用上の注意事項</h2>
                <div class="disclaimer-content">
                    
                    <h3 class="subsection-title">申請前の確認義務</h3>
                    <p>補助金・助成金の申請を行う前には、以下の確認を必ず行ってください：</p>
                    <ul class="info-list">
                        <li>制度所管機関の公式ウェブサイトでの最新情報確認</li>
                        <li>募集要領・申請の手引きの詳細な読み込み</li>
                        <li>申請条件・対象要件の詳細な確認</li>
                        <li>提出書類・添付資料の内容確認</li>
                        <li>申請期限・事業期間の正確な把握</li>
                    </ul>
                    
                    <h3 class="subsection-title">申請結果について</h3>
                    <p>本サイトの利用は、補助金・助成金の申請結果に影響を与えるものではありません：</p>
                    <ul class="info-list">
                        <li>採択・不採択の結果については、制度所管機関の審査により決定されます</li>
                        <li>本サイトの情報を参考に申請を行った場合でも、採択を保証するものではありません</li>
                        <li>申請結果についてのご質問・ご要望は、直接制度所管機関にお問い合わせください</li>
                    </ul>
                </div>
            </section>
            
            <!-- 3. 責任の限界 -->
            <section class="content-section disclaimer-section highlight-section" id="section-3">
                <h2 class="disclaimer-title">3. 責任の限界</h2>
                <div class="disclaimer-content">
                    
                    <h3 class="subsection-title">損害賠償の免責</h3>
                    <p>本サイトの利用に関連して生じた以下の損害について、運営者は一切の責任を負いません：</p>
                    <ul class="info-list">
                        <li>情報の誤りや遅延により生じた機会損失</li>
                        <li>申請の不採択により生じた損失</li>
                        <li>制度変更により生じた準備費用の無駄</li>
                        <li>本サイトの一時的な利用不能により生じた損失</li>
                        <li>第三者による情報の不正利用により生じた損害</li>
                    </ul>
                    
                    <h3 class="subsection-title">間接損害の免責</h3>
                    <p>以下の間接的な損害についても、運営者は責任を負いません：</p>
                    <ul class="info-list">
                        <li>事業機会の逸失による損失</li>
                        <li>第三者との契約不履行による損害</li>
                        <li>信用失墜による損害</li>
                        <li>その他一切の特別損害・間接損害・逸失利益</li>
                    </ul>
                    
                    <div class="important-notice">
                        <h4 class="notice-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            重要なお知らせ
                        </h4>
                        <p>補助金・助成金の申請は、事業者の責任において行ってください。本サイトは情報提供を目的としており、申請の代行や成果の保証を行うものではありません。申請前には必ず制度所管機関の公式情報を確認し、不明な点は直接お問い合わせいただくことを強く推奨いたします。</p>
                    </div>
                </div>
            </section>
            
            <!-- 4. 第三者サービスについて -->
            <section class="content-section disclaimer-section" id="section-4">
                <h2 class="disclaimer-title">4. 第三者サービスについて</h2>
                <div class="disclaimer-content">
                    
                    <h3 class="subsection-title">外部リンク</h3>
                    <p>本サイトには、制度所管機関や関連団体への外部リンクが含まれております：</p>
                    <ul class="info-list">
                        <li>外部サイトの内容については、当該サイト運営者が責任を負います</li>
                        <li>外部サイトの利用により生じた損害について、当サイトは責任を負いません</li>
                        <li>外部サイトのプライバシーポリシーや利用規約をご確認の上、ご利用ください</li>
                    </ul>
                    
                    <h3 class="subsection-title">広告について</h3>
                    <p>本サイトには第三者の広告が含まれる場合があります：</p>
                    <ul class="info-list">
                        <li>広告内容については、広告主が責任を負います</li>
                        <li>広告を通じた取引により生じた損害について、当サイトは責任を負いません</li>
                    </ul>
                </div>
            </section>
            
            <!-- 5. 技術的制約 -->
            <section class="content-section disclaimer-section" id="section-5">
                <h2 class="disclaimer-title">5. 技術的制約</h2>
                <div class="disclaimer-content">
                    
                    <h3 class="subsection-title">システム障害</h3>
                    <p>以下の事由によりサービスが利用できない場合があります：</p>
                    <ul class="info-list">
                        <li>サーバーメンテナンスや障害</li>
                        <li>インターネット回線の不具合</li>
                        <li>天災や停電等の不可抗力</li>
                        <li>サイバー攻撃や不正アクセス</li>
                    </ul>
                    <p>これらの事由による利用不能について、運営者は責任を負いません。</p>
                    
                    <h3 class="subsection-title">推奨ブラウザ環境</h3>
                    <p>本サイトは以下の環境での利用を推奨しております：</p>
                    <ul class="info-list">
                        <li>Google Chrome 最新版</li>
                        <li>Mozilla Firefox 最新版</li>
                        <li>Apple Safari 最新版</li>
                        <li>Microsoft Edge 最新版</li>
                    </ul>
                    <p>推奨環境以外での利用により生じた問題について、運営者は責任を負いません。</p>
                </div>
            </section>
            
            <!-- 6. 法的事項 -->
            <section class="content-section disclaimer-section" id="section-6">
                <h2 class="disclaimer-title">6. 法的事項</h2>
                <div class="disclaimer-content">
                    
                    <h3 class="subsection-title">準拠法</h3>
                    <p>本免責事項の解釈・適用については、日本国法を準拠法とします。</p>
                    
                    <h3 class="subsection-title">管轄裁判所</h3>
                    <p>本サイトの利用に関して紛争が生じた場合は、東京地方裁判所を第一審の専属的合意管轄裁判所とします。</p>
                </div>
            </section>
            
            <!-- 7. 免責事項の変更 -->
            <section class="content-section disclaimer-section" id="section-7">
                <h2 class="disclaimer-title">7. 免責事項の変更</h2>
                <div class="disclaimer-content">
                    <p>本免責事項は、法令の変更や事業内容の変更に応じて、予告なく変更される場合があります。変更後の免責事項は、本ページへの掲載により効力を生じるものとします。</p>
                    <p>ユーザーは定期的に本免責事項を確認し、最新の内容を把握するものとします。</p>
                </div>
            </section>
            
            <!-- 8. お問い合わせ -->
            <section class="content-section disclaimer-section" id="section-8">
                <h2 class="disclaimer-title">8. お問い合わせ</h2>
                <div class="disclaimer-content">
                    <p>本免責事項に関するお問い合わせは、下記の窓口までお願いいたします。</p>
                    
                    <div class="contact-info-box">
                        <h3 class="contact-title">運営者情報</h3>
                        <dl class="contact-details">
                            <dt>サイト名</dt>
                            <dd>補助金インサイト</dd>
                            
                            <dt>運営者</dt>
                            <dd>中澤圭志</dd>
                            
                            <dt>所在地</dt>
                            <dd>〒136-0073<br>東京都江東区北砂3-23-8　401</dd>
                            
                            <dt>メールアドレス</dt>
                            <dd><a href="mailto:info@joseikin-insight.com" class="text-link">info@joseikin-insight.com</a></dd>
                            
                            <dt>お問い合わせフォーム</dt>
                            <dd><a href="https://joseikin-insight.com/contact/" class="text-link">https://joseikin-insight.com/contact/</a></dd>
                        </dl>
                    </div>
                </div>
            </section>
            
            <!-- 附則 -->
            <section class="content-section supplementary-section">
                <h2 class="disclaimer-title">附　則</h2>
                <div class="disclaimer-content">
                    <p>本免責事項は、2025年10月9日から施行いたします。</p>
                </div>
            </section>
            
            <!-- 関連ページへのリンク -->
            <section class="content-section" id="related-links">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                    </span>
                    関連ページ
                </h2>
                <div class="related-links-grid">
                    <a href="https://joseikin-insight.com/about/" class="related-link-card">
                        <div class="link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4M12 8h.01"/>
                            </svg>
                        </div>
                        <div class="link-card-content">
                            <h3 class="link-card-title">当サイトについて</h3>
                            <p class="link-card-description">サービス概要と運営情報</p>
                        </div>
                    </a>
                    
                    <a href="https://joseikin-insight.com/privacy/" class="related-link-card">
                        <div class="link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <div class="link-card-content">
                            <h3 class="link-card-title">プライバシーポリシー</h3>
                            <p class="link-card-description">個人情報の取り扱いについて</p>
                        </div>
                    </a>
                    
                    <a href="https://joseikin-insight.com/terms/" class="related-link-card">
                        <div class="link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div class="link-card-content">
                            <h3 class="link-card-title">利用規約</h3>
                            <p class="link-card-description">サービス利用の規約について</p>
                        </div>
                    </a>
                </div>
            </section>
            
            <!-- ページトップへ戻るボタン -->
            <div class="page-top-btn-wrapper">
                <a href="#" class="page-top-btn" aria-label="ページトップへ戻る">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    <span>ページトップへ</span>
                </a>
            </div>
            
        </div>
    </div>
</article>

<style>
/* ==========================================================================
   Official Government Design - Disclaimer Page CSS
   官公庁デザイン - 免責事項ページ
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
    --gov-red: #c62828;
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
    --gov-radius: 4px;
    --gov-radius-lg: 8px;
}

* { box-sizing: border-box; }

.gov-disclaimer-page {
    font-family: var(--gov-font-sans);
    color: var(--gov-gray-900);
    background: linear-gradient(180deg, var(--gov-navy-50) 0%, var(--gov-white) 100%);
    line-height: 1.7;
    min-height: 100vh;
}

.gov-disclaimer-page::before {
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

.gov-page-content {
    padding: 60px 0 80px;
}

.content-section {
    margin-bottom: 48px;
}

/* Preamble */
.preamble-content {
    background: var(--gov-navy-50);
    border-left: 4px solid var(--gov-gold);
    padding: 24px 28px;
    border-radius: var(--gov-radius);
}

.preamble-content p {
    margin: 0;
    color: var(--gov-gray-800);
    line-height: 1.9;
    font-size: 1rem;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-family: var(--gov-font-serif);
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--gov-navy-800);
}

.title-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--gov-navy-800);
    border-radius: var(--gov-radius);
    color: var(--gov-gold);
}

/* TOC */
.toc-nav {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    padding: 24px;
}

.toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 8px;
}

.toc-link {
    display: block;
    padding: 10px 16px;
    color: var(--gov-navy-800);
    text-decoration: none;
    border-radius: var(--gov-radius);
    transition: all var(--gov-transition);
    font-size: 0.9375rem;
    border-left: 3px solid transparent;
}

.toc-link:hover {
    background: var(--gov-navy-50);
    border-left-color: var(--gov-gold);
    transform: translateX(4px);
}

/* Disclaimer Sections */
.disclaimer-section {
    scroll-margin-top: 80px;
}

.disclaimer-title {
    font-family: var(--gov-font-serif);
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gov-white);
    margin: 0 0 24px;
    padding: 16px 20px;
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-700) 100%);
    border-radius: var(--gov-radius);
}

.disclaimer-content {
    padding: 0 8px;
}

.disclaimer-content p {
    color: var(--gov-gray-700);
    line-height: 1.8;
    margin-bottom: 20px;
}

.subsection-title {
    font-size: 1.0625rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 28px 0 16px;
    padding-left: 12px;
    border-left: 3px solid var(--gov-gold);
}

.info-list {
    list-style: none;
    padding: 0;
    margin: 16px 0;
}

.info-list li {
    position: relative;
    padding-left: 24px;
    margin-bottom: 10px;
    color: var(--gov-gray-700);
    line-height: 1.7;
}

.info-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.65em;
    width: 6px;
    height: 6px;
    background: var(--gov-gold);
    border-radius: 50%;
}

.info-list li strong {
    color: var(--gov-navy-900);
}

/* Highlight Section */
.highlight-section {
    background: var(--gov-navy-50);
    border: 2px solid var(--gov-navy-200);
    border-radius: var(--gov-radius-lg);
    padding: 28px;
}

/* Important Notice */
.important-notice {
    background: var(--gov-gold-pale);
    border: 2px solid var(--gov-gold);
    border-radius: var(--gov-radius-lg);
    padding: 28px;
    margin-top: 28px;
}

.notice-title {
    font-size: 1.0625rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.notice-title svg {
    color: var(--gov-gold);
}

.important-notice p {
    margin-bottom: 0;
    color: var(--gov-navy-800);
    font-weight: 500;
}

/* Contact Info Box */
.contact-info-box {
    background: var(--gov-white);
    border: 2px solid var(--gov-navy-200);
    border-radius: var(--gov-radius-lg);
    padding: 28px;
    margin-top: 24px;
}

.contact-title {
    font-family: var(--gov-font-serif);
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--gov-gold);
}

.contact-details {
    display: grid;
    gap: 16px;
}

.contact-details dt {
    font-weight: 600;
    color: var(--gov-navy-800);
    font-size: 0.9375rem;
}

.contact-details dd {
    color: var(--gov-gray-700);
    margin: 0 0 16px;
    padding-left: 16px;
    font-size: 0.9375rem;
}

.text-link {
    color: var(--gov-navy-700);
    text-decoration: underline;
    font-weight: 500;
    transition: color var(--gov-transition);
}

.text-link:hover {
    color: var(--gov-gold);
}

/* Supplementary */
.supplementary-section .disclaimer-content {
    background: var(--gov-gray-100);
    border-left: 4px solid var(--gov-navy-600);
    padding: 20px 24px;
    border-radius: var(--gov-radius);
}

.supplementary-section .disclaimer-content p {
    margin: 0;
    color: var(--gov-gray-700);
}

/* Related Links */
.related-links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.related-link-card {
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

.related-link-card:hover {
    background: var(--gov-navy-50);
    border-color: var(--gov-navy-300);
    transform: translateX(4px);
}

.link-card-icon {
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

.related-link-card:hover .link-card-icon {
    background: var(--gov-gold-pale);
    color: var(--gov-navy-900);
}

.link-card-content { flex: 1; }

.link-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 4px;
}

.link-card-description {
    font-size: 0.8125rem;
    color: var(--gov-gray-600);
    margin: 0;
}

/* Page Top Button */
.page-top-btn-wrapper {
    text-align: center;
    margin-top: 60px;
}

.page-top-btn {
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

.page-top-btn:hover {
    background: linear-gradient(135deg, var(--gov-navy-900) 0%, var(--gov-navy-800) 100%);
    transform: translateY(-2px);
    box-shadow: var(--gov-shadow);
}

/* Responsive */
@media (max-width: 768px) {
    .gov-page-header {
        padding: 48px 0 40px;
    }
    
    .gov-page-title {
        font-size: 1.875rem;
    }
    
    .gov-page-meta {
        flex-direction: column;
        gap: 8px;
    }
    
    .toc-list {
        grid-template-columns: 1fr;
    }
    
    .disclaimer-content {
        padding: 0;
    }
    
    .related-links-grid {
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
    
    .section-title {
        font-size: 1.25rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .disclaimer-title {
        font-size: 1.125rem;
        padding: 14px 16px;
    }
}

/* スムーススクロール */
html {
    scroll-behavior: smooth;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ページトップボタン
    const pageTopBtn = document.querySelector('.page-top-btn');
    if (pageTopBtn) {
        pageTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // 目次リンクのスムーススクロール
    const tocLinks = document.querySelectorAll('.toc-link');
    tocLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php get_footer(); ?>
