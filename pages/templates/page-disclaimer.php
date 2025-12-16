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

<article class="disclaimer-page" itemscope itemtype="https://schema.org/WebPage">
    
    <!-- ページヘッダー -->
    <header class="page-header">
        <div class="container">
            <h1 class="page-title" itemprop="headline">免責事項</h1>
            <div class="page-meta">
                <p class="meta-item">制定日：<time datetime="2025-10-09">2025年10月9日</time></p>
                <p class="meta-item">最終改定日：<time datetime="2025-12-16">2025年12月16日</time></p>
            </div>
        </div>
    </header>
    
    <!-- メインコンテンツ -->
    <div class="page-content">
        <div class="container">
            
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
:root {
    --color-white: #ffffff;
    --color-black: #000000;
    --color-yellow: #ffeb3b;
    --color-yellow-dark: #ffc107;
    --color-gray-50: #fafafa;
    --color-gray-100: #f5f5f5;
    --color-gray-200: #eeeeee;
    --color-gray-300: #e0e0e0;
    --color-gray-400: #bdbdbd;
    --color-gray-500: #9e9e9e;
    --color-gray-600: #757575;
    --color-gray-700: #616161;
    --color-gray-800: #424242;
    --color-gray-900: #212121;
    --color-primary: var(--color-yellow);
    --text-primary: var(--color-gray-900);
    --text-secondary: var(--color-gray-600);
    --bg-primary: var(--color-white);
    --bg-secondary: var(--color-gray-50);
    --bg-tertiary: var(--color-gray-100);
    --border-light: var(--color-gray-200);
    --border-medium: var(--color-gray-300);
    --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --spacing-xs: 0.25rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --spacing-xl: 2rem;
    --spacing-2xl: 2.5rem;
    --spacing-3xl: 3rem;
    --spacing-4xl: 4rem;
    --radius-sm: 0.25rem;
    --radius-md: 0.375rem;
    --radius-lg: 0.5rem;
    --radius-xl: 0.75rem;
    --font-size-xs: 0.75rem;
    --font-size-sm: 0.875rem;
    --font-size-base: 1rem;
    --font-size-lg: 1.125rem;
    --font-size-xl: 1.25rem;
    --font-size-2xl: 1.5rem;
    --font-size-3xl: 1.875rem;
    --font-size-4xl: 2.25rem;
    --font-weight-normal: 400;
    --font-weight-medium: 500;
    --font-weight-semibold: 600;
    --font-weight-bold: 700;
    --line-height-tight: 1.25;
    --line-height-normal: 1.5;
    --line-height-relaxed: 1.75;
}

.disclaimer-page {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    color: var(--text-primary);
    background: var(--bg-primary);
}

.container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
}

.page-header {
    background: var(--bg-secondary);
    padding: var(--spacing-4xl) 0 var(--spacing-3xl);
    text-align: center;
    border-bottom: 1px solid var(--border-light);
}

.page-title {
    font-size: var(--font-size-4xl);
    font-weight: var(--font-weight-bold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-lg);
}

.page-meta {
    display: flex;
    justify-content: center;
    gap: var(--spacing-xl);
    flex-wrap: wrap;
}

.meta-item {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    margin: 0;
}

.page-content {
    padding: var(--spacing-4xl) 0;
}

.content-section {
    margin-bottom: var(--spacing-4xl);
}

.preamble-content p {
    font-size: var(--font-size-base);
    line-height: var(--line-height-relaxed);
    color: var(--text-secondary);
}

.section-title {
    font-size: var(--font-size-2xl);
    font-weight: var(--font-weight-bold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-xl);
    padding-bottom: var(--spacing-md);
    border-bottom: 2px solid var(--color-primary);
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.title-icon {
    display: inline-flex;
    width: 32px;
    height: 32px;
}

/* 目次 */
.toc-nav {
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
}

.toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: var(--spacing-sm);
}

.toc-link {
    display: block;
    padding: var(--spacing-sm) var(--spacing-md);
    color: var(--text-primary);
    text-decoration: none;
    border-radius: var(--radius-md);
    transition: all 0.2s ease;
    font-size: var(--font-size-sm);
}

.toc-link:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
    transform: translateX(4px);
}

/* 免責セクション */
.disclaimer-section {
    scroll-margin-top: 80px;
}

.disclaimer-title {
    font-size: var(--font-size-xl);
    font-weight: var(--font-weight-bold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-lg);
    padding: var(--spacing-md);
    background: var(--bg-secondary);
    border-left: 4px solid var(--color-primary);
    border-radius: var(--radius-md);
}

.disclaimer-content {
    padding-left: var(--spacing-lg);
}

.disclaimer-content p {
    color: var(--text-secondary);
    line-height: var(--line-height-relaxed);
    margin-bottom: var(--spacing-lg);
}

.subsection-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin: var(--spacing-xl) 0 var(--spacing-md);
}

.info-list {
    list-style: none;
    padding: 0;
    margin: var(--spacing-md) 0;
}

.info-list li {
    position: relative;
    padding-left: var(--spacing-xl);
    margin-bottom: var(--spacing-sm);
    color: var(--text-secondary);
    line-height: var(--line-height-normal);
}

.info-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.6em;
    width: 6px;
    height: 6px;
    background: var(--color-primary);
    border-radius: 50%;
}

.info-list li strong {
    color: var(--text-primary);
}

/* ハイライトセクション */
.highlight-section {
    background: var(--bg-secondary);
    border: 2px solid var(--border-medium);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
}

/* 重要なお知らせ */
.important-notice {
    background: #fffdf5;
    border: 2px solid var(--color-primary);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    margin-top: var(--spacing-xl);
}

.notice-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-md);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.notice-title svg {
    color: var(--color-yellow-dark);
}

.important-notice p {
    margin-bottom: 0;
    color: var(--text-primary);
    font-weight: var(--font-weight-medium);
}

/* 連絡先情報ボックス */
.contact-info-box {
    background: var(--bg-tertiary);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    margin-top: var(--spacing-lg);
}

.contact-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-lg);
    padding-bottom: var(--spacing-sm);
    border-bottom: 2px solid var(--color-primary);
}

.contact-details {
    display: grid;
    gap: var(--spacing-md);
}

.contact-details dt {
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
}

.contact-details dd {
    color: var(--text-secondary);
    margin: 0 0 var(--spacing-md);
    padding-left: var(--spacing-lg);
}

.text-link {
    color: var(--text-primary);
    text-decoration: underline;
    font-weight: var(--font-weight-medium);
}

.text-link:hover {
    color: var(--color-gray-700);
}

/* 附則 */
.supplementary-section .disclaimer-content p {
    color: var(--text-secondary);
}

/* 関連リンク */
.related-links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--spacing-lg);
}

.related-link-card {
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-md);
    padding: var(--spacing-lg);
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    text-decoration: none;
    transition: all 0.2s ease;
}

.related-link-card:hover {
    background: var(--bg-tertiary);
    border-color: var(--border-medium);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.link-card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    background: var(--color-white);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-md);
    color: var(--text-primary);
    flex-shrink: 0;
}

.link-card-content {
    flex: 1;
}

.link-card-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-xs);
}

.link-card-description {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    margin: 0;
}

/* ページトップボタン */
.page-top-btn-wrapper {
    text-align: center;
    margin-top: var(--spacing-4xl);
}

.page-top-btn {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-md) var(--spacing-xl);
    background: var(--color-primary);
    color: var(--color-black);
    text-decoration: none;
    border-radius: var(--radius-xl);
    font-weight: var(--font-weight-semibold);
    transition: all 0.2s ease;
    box-shadow: var(--shadow-md);
}

.page-top-btn:hover {
    background: var(--color-yellow-dark);
    transform: translateY(-2px);
}

/* レスポンシブ */
@media (max-width: 768px) {
    .page-header {
        padding: var(--spacing-3xl) 0 var(--spacing-2xl);
    }
    
    .page-title {
        font-size: var(--font-size-3xl);
    }
    
    .page-meta {
        flex-direction: column;
        gap: var(--spacing-sm);
    }
    
    .toc-list {
        grid-template-columns: 1fr;
    }
    
    .disclaimer-content {
        padding-left: 0;
    }
    
    .related-links-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .container {
        padding: 0 var(--spacing-md);
    }
    
    .page-title {
        font-size: var(--font-size-2xl);
    }
    
    .section-title {
        font-size: var(--font-size-xl);
        flex-direction: column;
        align-items: flex-start;
    }
    
    .disclaimer-title {
        font-size: var(--font-size-lg);
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
