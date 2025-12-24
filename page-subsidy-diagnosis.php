<?php
/**
 * Template Name: Subsidy Diagnosis Pro (AI診断)
 * Description: RAG機能を活用した補助金・助成金AI診断 - 補助金図鑑×官公庁デザイン
 *
 * @package Grant_Insight_Perfect
 * @version 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// チャットシステムの利用可否確認
$has_chat_system = shortcode_exists('gip_chat');

// SEO用メタデータ
$page_title = '【無料】補助金診断サービス | 法人・個人対応の補助金診断ツール';
$page_description = '補助金診断サービスを無料で提供。中小企業から個人事業主まで対応した補助金診断ツールで、申請可能な補助金・助成金を検索。';

// 画像パス
$img_base = 'https://joseikin-insight.com/wp-content/uploads/2025/12/';
?>

<div class="sd-wrapper" itemscope itemtype="https://schema.org/WebPage">

    <!-- ============================================
         官公庁スタイル トップバナー
         ============================================ -->
    <div class="sd-top-banner">
        <div class="sd-container">
            <div class="sd-top-banner__inner">
                <div class="sd-top-banner__left">
                    <span class="sd-top-banner__label">経済産業省・厚生労働省 補助金情報連携</span>
                </div>
                <div class="sd-top-banner__right">
                    <svg class="sd-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span class="sd-top-banner__date">最終更新：<?php echo date('Y年n月j日'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         HERO SECTION - ヒーローセクション
         ============================================ -->
    <section class="sd-hero" aria-label="メインビジュアル">
        <div class="sd-hero__grid-bg"></div>
        <div class="sd-container">
            <div class="sd-hero__grid">
                <!-- 左側：コンテンツ -->
                <div class="sd-hero__content">
                    <div class="sd-hero__badge-group">
                        <span class="sd-hero__badge sd-hero__badge--primary">経済産業省・厚生労働省 補助金情報連携</span>
                        <span class="sd-hero__badge sd-hero__badge--date">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            最終更新：<?php echo date('Y年n月j日'); ?>
                        </span>
                    </div>

                    <div class="sd-hero__official-badge">
                        公式認定サービス無料・登録不要
                    </div>

                    <h1 class="sd-hero__title" itemprop="name">
                        補助金・助成金<br>
                        <span class="sd-hero__title-accent">無料診断サービス</span>
                    </h1>

                    <p class="sd-hero__lead">
                        国・地方自治体が提供する補助金・助成金制度から、<br class="sd-pc-only">
                        貴社・貴方に最適な制度をAIが自動で検索・提案いたします。
                    </p>

                    <!-- Stats -->
                    <div class="sd-hero__stats">
                        <div class="sd-hero__stat">
                            <span class="sd-hero__stat-number">1,000</span>
                            <span class="sd-hero__stat-label">件以上<br>補助金データ収録</span>
                        </div>
                        <div class="sd-hero__stat">
                            <span class="sd-hero__stat-number">47</span>
                            <span class="sd-hero__stat-label">都道府県<br>全国対応</span>
                        </div>
                        <div class="sd-hero__stat">
                            <span class="sd-hero__stat-number">24</span>
                            <span class="sd-hero__stat-label">時間<br>いつでも利用可能</span>
                        </div>
                    </div>

                    <div class="sd-hero__actions">
                        <button type="button" class="sd-btn sd-btn--gold sd-btn--lg diag-popup-trigger" data-gip-modal-open="true">
                            無料診断を開始する
                        </button>
                        <a href="#features" class="sd-btn sd-btn--outline sd-btn--lg smooth-scroll">
                            サービス詳細を見る
                        </a>
                    </div>
                </div>

                <!-- 右側：診断プレビューカード -->
                <div class="sd-hero__visual">
                    <div class="sd-hero__card">
                        <div class="sd-hero__card-inner">
                            <div class="sd-hero__card-icon">
                                <span>診</span>
                            </div>
                            <p class="sd-hero__card-title">AI SUBSIDY DIAGNOSIS</p>
                            <div class="sd-hero__card-progress">
                                <div class="sd-hero__card-progress-bar"></div>
                            </div>
                            <div class="sd-hero__card-lines">
                                <div class="sd-hero__card-line sd-hero__card-line--75"></div>
                                <div class="sd-hero__card-line sd-hero__card-line--100"></div>
                                <div class="sd-hero__card-line sd-hero__card-line--85"></div>
                            </div>
                        </div>
                    </div>
                    <div class="sd-hero__card-glow"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         Trust Bar - 信頼性バー
         ============================================ -->
    <section class="sd-trust-bar">
        <div class="sd-container">
            <p class="sd-trust-bar__notice">本サービスは情報提供を目的としております。申請にあたっては各制度の公募要領をご確認ください。</p>
            <div class="sd-trust-bar__items">
                <div class="sd-trust-bar__item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>セキュリティ認証済</span>
                </div>
                <div class="sd-trust-bar__item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>SSL暗号化通信</span>
                </div>
                <div class="sd-trust-bar__item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.23-2.841.676-4.134"/>
                    </svg>
                    <span>個人情報保護</span>
                </div>
                <div class="sd-trust-bar__item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>公的機関データ連携</span>
                </div>
                <div class="sd-trust-bar__item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>リアルタイム更新</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 01: ご利用の流れ
         ============================================ -->
    <section class="sd-section" aria-labelledby="flow-title">
        <div class="sd-container sd-container--narrow">
            <header class="sd-section__header">
                <span class="sd-section__number">01</span>
                <div class="sd-section__header-content">
                    <h2 id="flow-title" class="sd-section__title">ご利用の流れ</h2>
                    <p class="sd-section__subtitle">4つのステップで最適な補助金・助成金をご提案いたします</p>
                </div>
            </header>

            <div class="sd-flow">
                <!-- Step 1 -->
                <article class="sd-flow__card">
                    <div class="sd-flow__card-image">
                        <img src="<?php echo esc_url($img_base); ?>2.png" alt="事業内容ヒアリング" loading="lazy" width="600" height="450">
                        <div class="sd-flow__card-overlay"></div>
                        <span class="sd-flow__card-step">STEP 01</span>
                    </div>
                    <div class="sd-flow__card-content">
                        <h3 class="sd-flow__card-en">Interview</h3>
                        <h4 class="sd-flow__card-title">事業内容のヒアリング</h4>
                        <p class="sd-flow__card-desc">業種・従業員数・所在地など、基本的な事業情報をチャット形式でお伺いいたします。専門知識は不要です。</p>
                    </div>
                </article>

                <!-- Step 2 -->
                <article class="sd-flow__card">
                    <div class="sd-flow__card-image">
                        <img src="<?php echo esc_url($img_base); ?>6.png" alt="ニーズの深掘り" loading="lazy" width="600" height="450">
                        <div class="sd-flow__card-overlay"></div>
                        <span class="sd-flow__card-step">STEP 02</span>
                    </div>
                    <div class="sd-flow__card-content">
                        <h3 class="sd-flow__card-en">Analysis</h3>
                        <h4 class="sd-flow__card-title">ニーズの深掘り</h4>
                        <p class="sd-flow__card-desc">設備投資・人材育成・IT導入など、具体的なご要望をお伺いし、最適な制度を特定いたします。</p>
                    </div>
                </article>

                <!-- Step 3 -->
                <article class="sd-flow__card">
                    <div class="sd-flow__card-image">
                        <img src="<?php echo esc_url($img_base); ?>3.png" alt="データベース検索" loading="lazy" width="600" height="450">
                        <div class="sd-flow__card-overlay"></div>
                        <span class="sd-flow__card-step">STEP 03</span>
                    </div>
                    <div class="sd-flow__card-content">
                        <h3 class="sd-flow__card-en">Database</h3>
                        <h4 class="sd-flow__card-title">データベース検索</h4>
                        <p class="sd-flow__card-desc">1,000件以上の補助金・助成金データベースから、AIが条件に合致する制度を自動で抽出いたします。</p>
                    </div>
                </article>

                <!-- Step 4 -->
                <article class="sd-flow__card">
                    <div class="sd-flow__card-image">
                        <img src="<?php echo esc_url($img_base); ?>8.png" alt="診断結果のご提示" loading="lazy" width="600" height="450">
                        <div class="sd-flow__card-overlay"></div>
                        <span class="sd-flow__card-step">STEP 04</span>
                    </div>
                    <div class="sd-flow__card-content">
                        <h3 class="sd-flow__card-en">Proposal</h3>
                        <h4 class="sd-flow__card-title">診断結果のご提示</h4>
                        <p class="sd-flow__card-desc">適合度の高い順に補助金・助成金をご提案。金額・申請期限・要件を分かりやすく表示いたします。</p>
                    </div>
                </article>
            </div>

            <div class="sd-flow__cta">
                <button type="button" class="sd-btn sd-btn--gold sd-btn--lg diag-popup-trigger" data-gip-modal-open="true">
                    無料診断を開始する
                </button>
                <p class="sd-flow__cta-note">所要時間：約3分 ｜ 会員登録不要</p>
            </div>
        </div>
    </section>

    <!-- ============================================
         AI診断コンシェルジュセクション（画像付き2カラム）
         ============================================ -->
    <section id="diagnosis-app" class="sd-diagnosis-section" aria-labelledby="diagnosis-title">
        <div class="sd-diagnosis-section__bg"></div>
        <div class="sd-container">
            <div class="sd-diagnosis-card sd-diagnosis-card--with-image">
                <div class="sd-diagnosis-card__grid">
                    <!-- 左側：コンシェルジュ画像 -->
                    <div class="sd-diagnosis-card__image">
                        <img src="https://joseikin-insight.com/wp-content/uploads/2025/12/Gemini_Generated_Image_t5iu5xt5iu5xt5iu-scaled.png" alt="補助金診断コンシェルジュ - あなたの事業成長をサポートします" loading="lazy" width="2560" height="1396">
                    </div>
                    
                    <!-- 右側：コンテンツ -->
                    <div class="sd-diagnosis-card__content">
                        <div class="sd-diagnosis-card__header">
                            <span class="sd-diagnosis-card__label">AI補助金診断</span>
                            <h2 id="diagnosis-title" class="sd-diagnosis-card__title">補助金診断コンシェルジュ</h2>
                            <p class="sd-diagnosis-card__desc">
                                人工知能（AI）が貴社・貴方の事業内容を分析し、<br class="sd-pc-only">
                                申請可能な補助金・助成金制度を自動で検索・ご提案いたします。
                            </p>
                        </div>

                        <div class="sd-diagnosis-card__features">
                            <div class="sd-diagnosis-card__feature">
                                <span class="sd-diagnosis-card__feature-check">✓</span>
                                <span>完全無料でご利用いただけます</span>
                            </div>
                            <div class="sd-diagnosis-card__feature">
                                <span class="sd-diagnosis-card__feature-check">✓</span>
                                <span>会員登録・個人情報の入力は不要です</span>
                            </div>
                            <div class="sd-diagnosis-card__feature">
                                <span class="sd-diagnosis-card__feature-check">✓</span>
                                <span>24時間365日いつでもご利用可能です</span>
                            </div>
                            <div class="sd-diagnosis-card__feature">
                                <span class="sd-diagnosis-card__feature-check">✓</span>
                                <span>所要時間は約3分程度です</span>
                            </div>
                        </div>

                        <div class="sd-diagnosis-card__action">
                            <button type="button" class="sd-btn sd-btn--gold sd-btn--xl diag-popup-trigger" data-gip-modal-open="true">
                                診断を開始する
                            </button>
                        </div>

                        <div class="sd-diagnosis-card__footer">
                            <button type="button" class="sd-terms-btn" id="openTermsModal" aria-haspopup="dialog">
                                <span class="sd-terms-btn__label">ご利用規約・免責事項</span>
                            </button>
                            <p class="sd-diagnosis-card__terms-note">本サービスのご利用により、利用規約に同意したものとみなします。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 02: サービスの特長
         ============================================ -->
    <section id="features" class="sd-section sd-section--alt" aria-labelledby="features-title">
        <div class="sd-container sd-container--narrow">
            <header class="sd-section__header">
                <span class="sd-section__number">02</span>
                <div class="sd-section__header-content">
                    <h2 id="features-title" class="sd-section__title">サービスの特長</h2>
                    <p class="sd-section__subtitle">安心・安全にご利用いただける4つのポイント</p>
                </div>
            </header>

            <div class="sd-features">
                <article class="sd-feature">
                    <div class="sd-feature__number">01</div>
                    <div class="sd-feature__content">
                        <h3 class="sd-feature__title">AI解析技術</h3>
                        <p class="sd-feature__desc">最新のRAG（検索拡張生成）技術を活用し、事業内容を理解して最適な制度を抽出いたします。</p>
                    </div>
                </article>

                <article class="sd-feature">
                    <div class="sd-feature__number">02</div>
                    <div class="sd-feature__content">
                        <h3 class="sd-feature__title">即時検索・結果表示</h3>
                        <p class="sd-feature__desc">回答内容に基づき、データベースを即座に検索。結果を数秒でご提示いたします。</p>
                    </div>
                </article>

                <article class="sd-feature">
                    <div class="sd-feature__number">03</div>
                    <div class="sd-feature__content">
                        <h3 class="sd-feature__title">登録不要・完全無料</h3>
                        <p class="sd-feature__desc">会員登録や個人情報の入力は一切不要。どなたでも無料でご利用いただけます。</p>
                    </div>
                </article>

                <article class="sd-feature">
                    <div class="sd-feature__number">04</div>
                    <div class="sd-feature__content">
                        <h3 class="sd-feature__title">セキュリティ対応</h3>
                        <p class="sd-feature__desc">SSL暗号化通信により、安全な環境でサービスをご利用いただけます。</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 03: ご利用のメリット
         ============================================ -->
    <section class="sd-section" aria-labelledby="merits-title">
        <div class="sd-container sd-container--narrow">
            <header class="sd-section__header">
                <span class="sd-section__number">03</span>
                <div class="sd-section__header-content">
                    <h2 id="merits-title" class="sd-section__title">ご利用のメリット</h2>
                    <p class="sd-section__subtitle">専門家への相談前の事前調査としてもご活用いただけます</p>
                </div>
            </header>

            <div class="sd-merits">
                <ul class="sd-merits__list">
                    <li class="sd-merits__item">
                        <span class="sd-merits__check">✓</span>
                        <div class="sd-merits__content">
                            <h4 class="sd-merits__title">検索時間の大幅削減</h4>
                            <p class="sd-merits__desc">条件に合う補助金をAIが自動で検索。情報収集の手間を削減できます。</p>
                        </div>
                    </li>
                    <li class="sd-merits__item">
                        <span class="sd-merits__check">✓</span>
                        <div class="sd-merits__content">
                            <h4 class="sd-merits__title">見落としリスクの軽減</h4>
                            <p class="sd-merits__desc">複数の制度を横断的に検索し、適合する可能性のある制度を網羅的にご提示。</p>
                        </div>
                    </li>
                    <li class="sd-merits__item">
                        <span class="sd-merits__check">✓</span>
                        <div class="sd-merits__content">
                            <h4 class="sd-merits__title">情報の整理・比較</h4>
                            <p class="sd-merits__desc">申請要件・補助金額・申請期限をまとめて確認でき、比較検討が容易です。</p>
                        </div>
                    </li>
                    <li class="sd-merits__item">
                        <span class="sd-merits__check">✓</span>
                        <div class="sd-merits__content">
                            <h4 class="sd-merits__title">24時間利用可能</h4>
                            <p class="sd-merits__desc">営業時間を気にせず、お好きな時間にいつでも診断をご利用いただけます。</p>
                        </div>
                    </li>
                    <li class="sd-merits__item">
                        <span class="sd-merits__check">✓</span>
                        <div class="sd-merits__content">
                            <h4 class="sd-merits__title">専門知識不要</h4>
                            <p class="sd-merits__desc">チャット形式の簡単な質問に答えるだけで、どなたでも診断可能です。</p>
                        </div>
                    </li>
                </ul>
                <div class="sd-merits__cta">
                    <a href="#diagnosis-app" class="sd-link smooth-scroll">無料で診断を始める</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 04: ご利用対象者
         ============================================ -->
    <section class="sd-section sd-section--alt" aria-labelledby="target-title">
        <div class="sd-container sd-container--narrow">
            <header class="sd-section__header">
                <span class="sd-section__number">04</span>
                <div class="sd-section__header-content">
                    <h2 id="target-title" class="sd-section__title">ご利用対象者</h2>
                    <p class="sd-section__subtitle">以下のような方にご活用いただいております</p>
                </div>
            </header>

            <div class="sd-targets">
                <article class="sd-target">
                    <span class="sd-target__badge">法人</span>
                    <h3 class="sd-target__title">中小企業の経営者様</h3>
                    <p class="sd-target__desc">設備投資や事業拡大を検討されている企業様</p>
                </article>

                <article class="sd-target">
                    <span class="sd-target__badge">個人</span>
                    <h3 class="sd-target__title">個人事業主・フリーランス様</h3>
                    <p class="sd-target__desc">創業支援や運転資金をお探しの方</p>
                </article>

                <article class="sd-target">
                    <span class="sd-target__badge">担当者</span>
                    <h3 class="sd-target__title">IT・DX推進ご担当者様</h3>
                    <p class="sd-target__desc">IT導入補助金等を検討されている方</p>
                </article>

                <article class="sd-target">
                    <span class="sd-target__badge">担当者</span>
                    <h3 class="sd-target__title">人事・採用ご担当者様</h3>
                    <p class="sd-target__desc">雇用関連の助成金をお探しの方</p>
                </article>

                <article class="sd-target">
                    <span class="sd-target__badge">創業</span>
                    <h3 class="sd-target__title">起業準備中の方</h3>
                    <p class="sd-target__desc">創業・開業に使える制度をお探しの方</p>
                </article>

                <article class="sd-target">
                    <span class="sd-target__badge">専門家</span>
                    <h3 class="sd-target__title">士業・コンサルタント様</h3>
                    <p class="sd-target__desc">顧客への情報提供ツールとしてご活用の方</p>
                </article>
            </div>

            <div class="sd-targets__cta">
                <p class="sd-targets__cta-lead">あなたも今すぐ診断してみませんか？</p>
                <button type="button" class="sd-btn sd-btn--navy sd-btn--lg diag-popup-trigger" data-gip-modal-open="true">
                    無料診断を開始する
                </button>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 05: 補助金診断とは
         ============================================ -->
    <section id="about" class="sd-section" aria-labelledby="about-title">
        <div class="sd-container sd-container--narrow">
            <header class="sd-section__header">
                <span class="sd-section__number">05</span>
                <div class="sd-section__header-content">
                    <h2 id="about-title" class="sd-section__title">補助金診断とは</h2>
                    <p class="sd-section__subtitle">事業者様に最適な補助金・助成金を見つけるサービスです</p>
                </div>
            </header>

            <div class="sd-about">
                <div class="sd-about__block">
                    <h3 class="sd-about__subtitle">補助金診断サービスの概要</h3>
                    <p class="sd-about__text">
                        <strong>補助金診断</strong>とは、事業者様の業種・規模・目的などの情報をもとに、申請可能な補助金・助成金を検索するサービスです。
                        国や地方自治体は毎年多くの補助金制度を設けていますが、その数は数千種類にのぼり、自社に適した制度を見つけることは容易ではありません。
                    </p>
                    <p class="sd-about__text">
                        補助金診断サービスをご利用いただくことで、専門の<strong>補助金診断士</strong>や中小企業診断士にご相談される前に、
                        どのような制度が利用可能か事前に把握することができます。これにより、専門家へのご相談をより効率的に行うことが可能となります。
                    </p>
                </div>

                <div class="sd-about__block">
                    <h3 class="sd-about__subtitle">法人から個人まで幅広く対応</h3>
                    <p class="sd-about__text">
                        当サービスの補助金診断は、中小企業や法人様だけでなく、<strong>個人事業主やフリーランス</strong>の方にもご利用いただけます。
                        創業支援、設備投資、IT導入、人材育成など、様々な目的に応じた補助金・助成金情報を検索可能です。
                    </p>
                </div>

                <div class="sd-about__block">
                    <h3 class="sd-about__subtitle">検索技術について</h3>
                    <p class="sd-about__text">
                        本サービスでは、<strong>RAG（Retrieval-Augmented Generation）</strong>と呼ばれる検索拡張生成技術を採用しております。
                        RAGは、大規模なデータベースから関連情報を検索し、その情報をもとに回答を生成する技術です。
                    </p>
                    <p class="sd-about__text">
                        従来のキーワード検索とは異なり、文脈や意味を理解した検索が可能なため、
                        「設備を導入したい」「従業員を増やしたい」といった自然な言葉でも、適切な補助金を見つけることができます。
                    </p>

                    <div class="sd-about__tech-box">
                        <h4 class="sd-about__tech-title">RAG技術の仕組み</h4>
                        <ul class="sd-about__tech-list">
                            <li>
                                <span class="sd-about__tech-num">1</span>
                                <span>質問の理解：入力された内容から、求めている情報を理解します</span>
                            </li>
                            <li>
                                <span class="sd-about__tech-num">2</span>
                                <span>データベース検索：1,000件以上の補助金情報から関連度の高い制度を抽出します</span>
                            </li>
                            <li>
                                <span class="sd-about__tech-num">3</span>
                                <span>結果の整理：検索結果を優先度順に整理してご提示します</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 06: よくあるご質問
         ============================================ -->
    <section class="sd-section sd-section--alt" aria-labelledby="faq-title" itemscope itemtype="https://schema.org/FAQPage">
        <div class="sd-container sd-container--narrow">
            <header class="sd-section__header">
                <span class="sd-section__number">06</span>
                <div class="sd-section__header-content">
                    <h2 id="faq-title" class="sd-section__title">よくあるご質問</h2>
                    <p class="sd-section__subtitle">補助金診断に関するお問い合わせの多いご質問</p>
                </div>
            </header>

            <div class="sd-faq">
                <details class="sd-faq__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <summary class="sd-faq__question">
                        <span class="sd-faq__q">Q</span>
                        <span itemprop="name">補助金診断は本当に無料ですか？</span>
                        <span class="sd-faq__toggle"></span>
                    </summary>
                    <div class="sd-faq__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <span class="sd-faq__a">A</span>
                        <p itemprop="text">はい、当サービスの補助金診断は<strong>完全無料</strong>でご利用いただけます。会員登録も不要で、何度でも診断可能です。</p>
                    </div>
                </details>

                <details class="sd-faq__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <summary class="sd-faq__question">
                        <span class="sd-faq__q">Q</span>
                        <span itemprop="name">個人でも補助金診断を利用できますか？</span>
                        <span class="sd-faq__toggle"></span>
                    </summary>
                    <div class="sd-faq__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <span class="sd-faq__a">A</span>
                        <p itemprop="text">はい、<strong>個人事業主やフリーランスの方</strong>も補助金診断をご利用いただけます。創業支援や事業拡大に関する補助金など、個人向けの制度も多数収録しております。</p>
                    </div>
                </details>

                <details class="sd-faq__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <summary class="sd-faq__question">
                        <span class="sd-faq__q">Q</span>
                        <span itemprop="name">補助金診断士とは何ですか？</span>
                        <span class="sd-faq__toggle"></span>
                    </summary>
                    <div class="sd-faq__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <span class="sd-faq__a">A</span>
                        <p itemprop="text"><strong>補助金診断士</strong>は、補助金・助成金の申請支援を専門とする民間資格です。当サービスは補助金診断士へのご相談前の事前調査としてもご活用いただけます。実際の申請手続きには、専門家へのご相談をお勧めいたします。</p>
                    </div>
                </details>

                <details class="sd-faq__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <summary class="sd-faq__question">
                        <span class="sd-faq__q">Q</span>
                        <span itemprop="name">診断結果に表示された補助金は必ず申請できますか？</span>
                        <span class="sd-faq__toggle"></span>
                    </summary>
                    <div class="sd-faq__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <span class="sd-faq__a">A</span>
                        <p itemprop="text">診断結果は参考情報としてご活用ください。実際の申請にあたっては、各補助金の公募要領で<strong>申請要件を必ずご確認</strong>ください。制度によっては申請期限や予算状況により受付が終了している場合がございます。</p>
                    </div>
                </details>

                <details class="sd-faq__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <summary class="sd-faq__question">
                        <span class="sd-faq__q">Q</span>
                        <span itemprop="name">補助金と助成金の違いは何ですか？</span>
                        <span class="sd-faq__toggle"></span>
                    </summary>
                    <div class="sd-faq__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <span class="sd-faq__a">A</span>
                        <p itemprop="text"><strong>補助金</strong>は主に経済産業省系の制度で、審査があり採択率が設定されております。<strong>助成金</strong>は主に厚生労働省系の制度で、要件を満たせば原則として受給可能です。当サービスでは両方の制度を検索可能です。</p>
                    </div>
                </details>
            </div>
        </div>
    </section>

    <!-- ============================================
         BOTTOM CTA
         ============================================ -->
    <section class="sd-bottom-cta">
        <div class="sd-container">
            <span class="sd-bottom-cta__badge">無料・登録不要</span>
            <h2 class="sd-bottom-cta__title">今すぐ無料で補助金診断を開始</h2>
            <p class="sd-bottom-cta__text">
                会員登録不要・所要時間約3分<br>
                法人・個人問わず、申請可能な補助金・助成金を検索いたします
            </p>
            <button type="button" class="sd-btn sd-btn--gold sd-btn--xl diag-popup-trigger" data-gip-modal-open="true">
                無料診断を開始する
            </button>
            <div class="sd-bottom-cta__trust">
                <span class="sd-bottom-cta__trust-item">
                    <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    SSL暗号化通信
                </span>
                <span class="sd-bottom-cta__trust-item">
                    <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    個人情報保護対応
                </span>
                <span class="sd-bottom-cta__trust-item">
                    <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    24時間対応
                </span>
            </div>
        </div>
    </section>

</div>

<!-- ============================================
     利用規約モーダル
     ============================================ -->
<div class="sd-modal" id="termsModal" role="dialog" aria-labelledby="termsModalTitle" aria-modal="true" aria-hidden="true">
    <div class="sd-modal__overlay" data-close-modal></div>
    <div class="sd-modal__container">
        <header class="sd-modal__header">
            <h3 id="termsModalTitle" class="sd-modal__title">ご利用規約・免責事項</h3>
            <button type="button" class="sd-modal__close" id="closeTermsModal" aria-label="閉じる">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </header>
        <div class="sd-modal__body">
            <div class="sd-legal">
                <div class="sd-legal__block">
                    <h4 class="sd-legal__subtitle">1. サービスの性質について</h4>
                    <ul>
                        <li>本サービスは、AI（人工知能）による自動診断システムであり、補助金・助成金に関する情報提供を目的としております。</li>
                        <li>診断結果は、ユーザー様が入力された情報に基づきAIが自動生成したものであり、専門家による個別のアドバイスや助言ではございません。</li>
                        <li>本サービスは情報提供のみを目的としており、特定の補助金・助成金の申請を推奨・勧誘するものではございません。</li>
                    </ul>
                </div>
                <div class="sd-legal__block">
                    <h4 class="sd-legal__subtitle">2. 診断結果の取り扱いについて</h4>
                    <ul>
                        <li><strong>診断結果は参考情報としてご活用ください。</strong>実際の申請にあたっては、必ず各補助金・助成金の公募要領、申請要件等を公式サイトでご確認ください。</li>
                        <li>診断結果に表示された補助金・助成金への申請資格を保証するものではございません。</li>
                        <li>診断結果は採択を保証するものではなく、申請結果について当社は一切の責任を負いかねます。</li>
                    </ul>
                </div>
                <div class="sd-legal__block">
                    <h4 class="sd-legal__subtitle">3. 免責事項</h4>
                    <ul>
                        <li>本サービスのご利用により生じたいかなる損害についても、当社は一切の責任を負いかねます。</li>
                        <li>本サービスのご利用に基づく補助金・助成金の申請、不採択、その他の結果について、当社は一切の責任を負いかねます。</li>
                    </ul>
                </div>
            </div>
        </div>
        <footer class="sd-modal__footer">
            <button type="button" class="sd-btn sd-btn--navy" data-close-modal>閉じる</button>
        </footer>
    </div>
</div>

<!-- ============================================
     STYLES - 補助金図鑑×官公庁デザイン
     ============================================ -->
<style>
/* ==========================================================================
   CSS Custom Properties
   ========================================================================== */
:root {
    /* Primary - Navy */
    --sd-navy-900: #0f2350;
    --sd-navy-800: #1a3a70;
    --sd-navy-700: #2c3e50;
    --sd-navy-600: #34495e;
    --sd-navy-100: #e8ecf1;
    --sd-navy-50: #f4f6f8;
    
    /* Gold */
    --sd-gold: #c5a059;
    --sd-gold-light: #e6c885;
    --sd-gold-pale: #f0e6c8;
    
    /* Neutral */
    --sd-white: #fdfdfd;
    --sd-gray-900: #2b2b2b;
    --sd-gray-700: #495057;
    --sd-gray-600: #6c757d;
    --sd-gray-500: #adb5bd;
    --sd-gray-400: #ced4da;
    --sd-gray-300: #dce3ea;
    --sd-gray-200: #e9ecef;
    --sd-gray-100: #f8f9fa;
    
    /* Typography */
    --sd-font-serif: "Shippori Mincho", "Yu Mincho", serif;
    --sd-font-sans: "Noto Sans JP", sans-serif;
    --sd-font-heading: "Zen Old Mincho", serif;
    
    /* Spacing */
    --sd-space-xs: 4px;
    --sd-space-sm: 8px;
    --sd-space-md: 16px;
    --sd-space-lg: 24px;
    --sd-space-xl: 32px;
    --sd-space-2xl: 48px;
    --sd-space-3xl: 64px;
    --sd-space-4xl: 80px;
    
    /* Container */
    --sd-container-max: 1200px;
    --sd-container-narrow: 900px;
    
    /* Shadow */
    --sd-shadow-sm: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.1);
    --sd-shadow-md: 0 4px 12px rgba(0,0,0,0.1);
    --sd-shadow-lg: 0 20px 40px -5px rgba(15, 35, 80, 0.15);
    
    /* Border */
    --sd-radius: 4px;
    --sd-radius-lg: 8px;
    
    /* Transition */
    --sd-trans: 0.25s ease;
}

/* ==========================================================================
   Base
   ========================================================================== */
.sd-wrapper {
    background-color: var(--sd-white);
    color: var(--sd-gray-900);
    font-family: var(--sd-font-serif);
    font-size: 16px;
    line-height: 1.8;
    -webkit-font-smoothing: antialiased;
}

.sd-wrapper *, .sd-wrapper *::before, .sd-wrapper *::after {
    box-sizing: border-box;
}

.sd-container {
    max-width: var(--sd-container-max);
    margin: 0 auto;
    padding: 0 var(--sd-space-lg);
}

.sd-container--narrow {
    max-width: var(--sd-container-narrow);
}

.sd-pc-only { display: inline; }
.sd-sp-only { display: none; }

@media (max-width: 768px) {
    .sd-pc-only { display: none; }
    .sd-sp-only { display: inline; }
}

/* ==========================================================================
   Top Banner
   ========================================================================== */
.sd-top-banner {
    background: var(--sd-navy-900);
    color: var(--sd-white);
    padding: var(--sd-space-sm) 0;
    border-bottom: 3px solid var(--sd-gold);
}

.sd-top-banner__inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: var(--sd-space-sm);
}

.sd-top-banner__label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.05em;
    font-family: var(--sd-font-sans);
}

.sd-top-banner__right {
    display: flex;
    align-items: center;
    gap: var(--sd-space-xs);
    font-size: 11px;
    color: var(--sd-gray-500);
    font-family: var(--sd-font-sans);
}

.sd-top-banner__right svg {
    color: var(--sd-gold);
}

/* ==========================================================================
   Hero Section
   ========================================================================== */
.sd-hero {
    position: relative;
    padding: var(--sd-space-4xl) 0;
    background: linear-gradient(180deg, var(--sd-gray-100) 0%, var(--sd-white) 100%);
    border-bottom: 1px solid var(--sd-gray-300);
    overflow: hidden;
}

.sd-hero__grid-bg {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(rgba(15, 35, 80, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(15, 35, 80, 0.03) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
}

.sd-hero__grid {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: var(--sd-space-3xl);
    align-items: center;
    position: relative;
    z-index: 1;
}

.sd-hero__badge-group {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: var(--sd-space-md);
    margin-bottom: var(--sd-space-lg);
}

.sd-hero__badge {
    display: inline-flex;
    align-items: center;
    gap: var(--sd-space-xs);
    font-size: 10px;
    font-weight: 700;
    font-family: var(--sd-font-sans);
    letter-spacing: 0.05em;
    padding: var(--sd-space-xs) var(--sd-space-md);
    border-radius: var(--sd-radius);
}

.sd-hero__badge--primary {
    background: var(--sd-navy-900);
    color: var(--sd-white);
}

.sd-hero__badge--date {
    color: var(--sd-gray-600);
    background: transparent;
    padding: 0;
}

.sd-hero__badge--date svg {
    color: var(--sd-gray-500);
}

.sd-hero__official-badge {
    display: inline-block;
    border: 1px solid var(--sd-gold);
    color: var(--sd-gold);
    font-size: 12px;
    font-weight: 700;
    font-family: var(--sd-font-sans);
    letter-spacing: 0.1em;
    padding: var(--sd-space-xs) var(--sd-space-md);
    background: var(--sd-white);
    box-shadow: var(--sd-shadow-sm);
    margin-bottom: var(--sd-space-md);
}

.sd-hero__title {
    font-family: var(--sd-font-heading);
    font-size: clamp(32px, 5vw, 48px);
    font-weight: 700;
    line-height: 1.2;
    color: var(--sd-navy-900);
    margin: 0 0 var(--sd-space-lg) 0;
}

.sd-hero__title-accent {
    position: relative;
    display: inline-block;
}

.sd-hero__title-accent::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 12px;
    background: var(--sd-gold-pale);
    z-index: -1;
}

.sd-hero__lead {
    font-size: 16px;
    line-height: 2;
    color: var(--sd-gray-700);
    margin: 0 0 var(--sd-space-xl) 0;
}

.sd-hero__stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--sd-space-md);
    margin-bottom: var(--sd-space-xl);
}

.sd-hero__stat {
    background: var(--sd-white);
    padding: var(--sd-space-md);
    text-align: center;
    border-top: 2px solid var(--sd-navy-900);
    box-shadow: var(--sd-shadow-sm);
}

.sd-hero__stat-number {
    display: block;
    font-family: var(--sd-font-heading);
    font-size: 28px;
    font-weight: 700;
    color: var(--sd-navy-900);
    line-height: 1;
}

.sd-hero__stat-label {
    display: block;
    font-size: 10px;
    color: var(--sd-gray-600);
    font-family: var(--sd-font-sans);
    margin-top: var(--sd-space-xs);
    line-height: 1.4;
}

.sd-hero__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sd-space-md);
}

/* Hero Visual - 診断プレビューカード */
.sd-hero__visual {
    position: relative;
}

.sd-hero__card {
    background: var(--sd-white);
    padding: var(--sd-space-sm);
    border-radius: var(--sd-radius-lg);
    box-shadow: var(--sd-shadow-lg);
    transform: rotate(2deg);
    border: 1px solid var(--sd-gray-300);
}

.sd-hero__card-inner {
    aspect-ratio: 4/5;
    background: linear-gradient(180deg, var(--sd-navy-50) 0%, var(--sd-white) 100%);
    border-radius: var(--sd-radius);
    border: 1px solid var(--sd-gray-200);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--sd-space-xl);
}

.sd-hero__card-icon {
    width: 80px;
    height: 80px;
    background: var(--sd-navy-900);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--sd-space-md);
    box-shadow: var(--sd-shadow-md);
    border: 4px solid var(--sd-white);
}

.sd-hero__card-icon span {
    font-family: var(--sd-font-heading);
    font-size: 32px;
    font-weight: 700;
    color: var(--sd-white);
}

.sd-hero__card-title {
    font-family: var(--sd-font-serif);
    font-size: 14px;
    font-weight: 700;
    color: var(--sd-navy-900);
    margin: 0 0 var(--sd-space-md) 0;
    letter-spacing: 0.05em;
}

.sd-hero__card-progress {
    width: 100%;
    height: 8px;
    background: var(--sd-gold-pale);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: var(--sd-space-lg);
}

.sd-hero__card-progress-bar {
    width: 66%;
    height: 100%;
    background: var(--sd-gold);
    animation: sd-pulse 2s ease-in-out infinite;
}

@keyframes sd-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

.sd-hero__card-lines {
    width: 100%;
    opacity: 0.5;
}

.sd-hero__card-line {
    height: 12px;
    background: var(--sd-gray-400);
    border-radius: 2px;
    margin-bottom: var(--sd-space-sm);
}

.sd-hero__card-line--75 { width: 75%; }
.sd-hero__card-line--100 { width: 100%; }
.sd-hero__card-line--85 { width: 85%; margin-bottom: 0; }

.sd-hero__card-glow {
    position: absolute;
    bottom: -40px;
    left: -40px;
    width: 160px;
    height: 160px;
    background: var(--sd-gold-pale);
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.5;
    z-index: -1;
}

/* ==========================================================================
   Buttons
   ========================================================================== */
.sd-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--sd-space-sm);
    padding: var(--sd-space-md) var(--sd-space-xl);
    font-family: var(--sd-font-sans);
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-decoration: none;
    border: none;
    border-radius: var(--sd-radius);
    cursor: pointer;
    transition: all var(--sd-trans);
}

.sd-btn--gold {
    background: var(--sd-gold);
    color: var(--sd-white);
    border-bottom: 4px solid rgba(0,0,0,0.1);
}

.sd-btn--gold:hover {
    background: var(--sd-navy-900);
}

.sd-btn--gold:active {
    border-bottom-width: 0;
    transform: translateY(4px);
}

.sd-btn--navy {
    background: var(--sd-navy-900);
    color: var(--sd-white);
}

.sd-btn--navy:hover {
    background: var(--sd-gold);
}

.sd-btn--outline {
    background: var(--sd-white);
    color: var(--sd-navy-900);
    border: 1px solid var(--sd-gray-300);
    box-shadow: var(--sd-shadow-sm);
}

.sd-btn--outline:hover {
    background: var(--sd-gray-100);
    border-color: var(--sd-navy-900);
}

.sd-btn--lg {
    padding: var(--sd-space-md) var(--sd-space-2xl);
    font-size: 15px;
}

.sd-btn--xl {
    padding: var(--sd-space-lg) var(--sd-space-3xl);
    font-size: 16px;
    width: 100%;
    max-width: 400px;
}

/* ==========================================================================
   Trust Bar
   ========================================================================== */
.sd-trust-bar {
    background: var(--sd-navy-900);
    color: var(--sd-white);
    padding: var(--sd-space-lg) 0;
    border-top: 4px solid var(--sd-gold);
}

.sd-trust-bar__notice {
    font-size: 12px;
    color: var(--sd-gray-500);
    text-align: center;
    margin: 0 0 var(--sd-space-md) 0;
    font-family: var(--sd-font-sans);
}

.sd-trust-bar__items {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: var(--sd-space-lg) var(--sd-space-2xl);
}

.sd-trust-bar__item {
    display: flex;
    align-items: center;
    gap: var(--sd-space-sm);
    font-size: 12px;
    font-weight: 700;
    font-family: var(--sd-font-sans);
    letter-spacing: 0.05em;
}

.sd-trust-bar__item svg {
    color: var(--sd-gold);
}

/* ==========================================================================
   Section Base
   ========================================================================== */
.sd-section {
    padding: var(--sd-space-4xl) 0;
}

.sd-section--alt {
    background: var(--sd-gray-100);
}

.sd-section__header {
    display: flex;
    align-items: flex-end;
    gap: var(--sd-space-md);
    border-bottom: 1px solid var(--sd-navy-900);
    padding-bottom: var(--sd-space-md);
    margin-bottom: var(--sd-space-2xl);
}

.sd-section__number {
    font-family: var(--sd-font-heading);
    font-size: 56px;
    font-weight: 900;
    color: var(--sd-gold);
    opacity: 0.3;
    line-height: 0.8;
}

.sd-section__header-content {
    flex: 1;
}

.sd-section__title {
    font-family: var(--sd-font-serif);
    font-size: 24px;
    font-weight: 700;
    color: var(--sd-navy-900);
    margin: 0 0 var(--sd-space-xs) 0;
}

.sd-section__subtitle {
    font-size: 14px;
    color: var(--sd-gray-600);
    font-family: var(--sd-font-sans);
    margin: 0;
}

/* ==========================================================================
   Flow Section - 4カラムグリッド
   ========================================================================== */
.sd-flow {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--sd-space-lg);
}

.sd-flow__card {
    background: var(--sd-white);
    border: 1px solid var(--sd-gray-300);
    border-radius: var(--sd-radius);
    overflow: hidden;
    transition: all var(--sd-trans);
}

.sd-flow__card:hover {
    border-color: var(--sd-gold);
    box-shadow: var(--sd-shadow-md);
}

.sd-flow__card-image {
    position: relative;
    aspect-ratio: 4/3;
    overflow: hidden;
}

.sd-flow__card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.9;
    transition: transform 0.5s ease;
}

.sd-flow__card:hover .sd-flow__card-image img {
    transform: scale(1.05);
}

.sd-flow__card-overlay {
    position: absolute;
    inset: 0;
    background: var(--sd-navy-900);
    opacity: 0.1;
    mix-blend-mode: multiply;
    pointer-events: none;
}

.sd-flow__card-step {
    position: absolute;
    top: 0;
    left: 0;
    background: var(--sd-navy-900);
    color: var(--sd-white);
    font-size: 11px;
    font-weight: 700;
    font-family: var(--sd-font-sans);
    letter-spacing: 0.1em;
    padding: var(--sd-space-xs) var(--sd-space-md);
}

.sd-flow__card-content {
    padding: var(--sd-space-lg);
}

.sd-flow__card-en {
    font-family: var(--sd-font-heading);
    font-size: 24px;
    font-weight: 700;
    color: var(--sd-navy-900);
    margin: 0 0 var(--sd-space-sm) 0;
    text-align: center;
}

.sd-flow__card-title {
    font-family: var(--sd-font-serif);
    font-size: 14px;
    font-weight: 700;
    color: var(--sd-navy-900);
    margin: 0 0 var(--sd-space-sm) 0;
    text-align: center;
    padding-bottom: var(--sd-space-sm);
    border-bottom: 1px solid var(--sd-gray-300);
}

.sd-flow__card-desc {
    font-size: 12px;
    color: var(--sd-gray-700);
    line-height: 1.7;
    margin: 0;
    text-align: justify;
}

.sd-flow__cta {
    margin-top: var(--sd-space-2xl);
    text-align: center;
}

.sd-flow__cta-note {
    font-size: 13px;
    color: var(--sd-gray-600);
    font-family: var(--sd-font-sans);
    margin-top: var(--sd-space-md);
}

/* ==========================================================================
   Diagnosis Section
   ========================================================================== */
.sd-diagnosis-section {
    position: relative;
    padding: var(--sd-space-4xl) 0;
    background: var(--sd-navy-900);
    overflow: hidden;
}

.sd-diagnosis-section__bg {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
    background-size: 40px 40px;
}

.sd-diagnosis-card {
    position: relative;
    background: var(--sd-white);
    max-width: 700px;
    margin: 0 auto;
    padding: var(--sd-space-2xl);
    border-radius: var(--sd-radius-lg);
    box-shadow: var(--sd-shadow-lg);
    border: 4px solid var(--sd-gold-pale);
}

/* 画像付きバージョン（横長画像対応・上下配置） */
.sd-diagnosis-card--with-image {
    max-width: 900px;
    padding: 0;
    overflow: hidden;
}

.sd-diagnosis-card__grid {
    display: flex;
    flex-direction: column;
}

.sd-diagnosis-card__image {
    position: relative;
    background: linear-gradient(135deg, var(--sd-navy-100) 0%, var(--sd-gray-100) 100%);
    overflow: hidden;
    aspect-ratio: 16 / 9;
    max-height: 320px;
}

.sd-diagnosis-card__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center top;
    transition: transform 0.5s ease;
}

.sd-diagnosis-card__image:hover img {
    transform: scale(1.02);
}

.sd-diagnosis-card__content {
    padding: var(--sd-space-2xl);
}

.sd-diagnosis-card--with-image .sd-diagnosis-card__header {
    text-align: center;
}

.sd-diagnosis-card--with-image .sd-diagnosis-card__features {
    grid-template-columns: repeat(2, 1fr);
}

.sd-diagnosis-card--with-image .sd-diagnosis-card__action {
    text-align: center;
}

.sd-diagnosis-card--with-image .sd-diagnosis-card__action .sd-btn--xl {
    width: 100%;
    max-width: 400px;
}

.sd-diagnosis-card--with-image .sd-diagnosis-card__footer {
    text-align: center;
}

.sd-diagnosis-card__header {
    text-align: center;
    margin-bottom: var(--sd-space-xl);
}

.sd-diagnosis-card__label {
    display: inline-block;
    background: var(--sd-navy-900);
    color: var(--sd-gold);
    font-size: 11px;
    font-weight: 700;
    font-family: var(--sd-font-sans);
    letter-spacing: 0.1em;
    padding: var(--sd-space-xs) var(--sd-space-md);
    border-radius: 20px;
    margin-bottom: var(--sd-space-md);
}

.sd-diagnosis-card__title {
    font-family: var(--sd-font-heading);
    font-size: 28px;
    font-weight: 700;
    color: var(--sd-navy-900);
    margin: 0 0 var(--sd-space-md) 0;
}

.sd-diagnosis-card__desc {
    font-size: 15px;
    color: var(--sd-gray-700);
    line-height: 1.9;
    margin: 0;
}

.sd-diagnosis-card__features {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--sd-space-md);
    margin-bottom: var(--sd-space-xl);
}

.sd-diagnosis-card__feature {
    display: flex;
    align-items: center;
    gap: var(--sd-space-md);
    padding: var(--sd-space-md);
    background: var(--sd-gray-100);
    border: 1px solid var(--sd-gray-300);
    border-radius: var(--sd-radius);
    font-size: 13px;
    font-weight: 700;
    font-family: var(--sd-font-sans);
}

.sd-diagnosis-card__feature-check {
    color: var(--sd-gold);
    font-size: 18px;
}

.sd-diagnosis-card__action {
    text-align: center;
    margin-bottom: var(--sd-space-xl);
}

.sd-diagnosis-card__footer {
    text-align: center;
    padding-top: var(--sd-space-lg);
    border-top: 1px dashed var(--sd-gray-300);
}

.sd-terms-btn {
    display: inline-flex;
    align-items: center;
    gap: var(--sd-space-sm);
    background: transparent;
    border: 1px solid var(--sd-gray-400);
    padding: var(--sd-space-sm) var(--sd-space-md);
    font-size: 12px;
    font-family: var(--sd-font-sans);
    color: var(--sd-gray-600);
    border-radius: var(--sd-radius);
    cursor: pointer;
    transition: all var(--sd-trans);
}

.sd-terms-btn:hover {
    border-color: var(--sd-navy-900);
    color: var(--sd-navy-900);
}

.sd-diagnosis-card__terms-note {
    font-size: 10px;
    color: var(--sd-gray-600);
    margin: var(--sd-space-sm) 0 0 0;
}

/* ==========================================================================
   Features Section
   ========================================================================== */
.sd-features {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--sd-space-lg);
}

.sd-feature {
    display: flex;
    gap: var(--sd-space-md);
    padding: var(--sd-space-lg);
    background: var(--sd-white);
    border: 1px solid var(--sd-gray-300);
    border-radius: var(--sd-radius);
    transition: all var(--sd-trans);
}

.sd-feature:hover {
    border-color: var(--sd-gold);
    box-shadow: var(--sd-shadow-sm);
}

.sd-feature__number {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    background: var(--sd-navy-50);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--sd-font-heading);
    font-size: 20px;
    font-weight: 700;
    color: var(--sd-navy-900);
    border-radius: var(--sd-radius);
}

.sd-feature__content {
    flex: 1;
}

.sd-feature__title {
    font-family: var(--sd-font-serif);
    font-size: 16px;
    font-weight: 700;
    color: var(--sd-navy-900);
    margin: 0 0 var(--sd-space-sm) 0;
}

.sd-feature__desc {
    font-size: 13px;
    color: var(--sd-gray-700);
    line-height: 1.7;
    margin: 0;
}

/* ==========================================================================
   Merits Section
   ========================================================================== */
.sd-merits {
    background: var(--sd-white);
    border: 2px solid var(--sd-gray-300);
    padding: var(--sd-space-xl);
    border-radius: var(--sd-radius-lg);
    box-shadow: var(--sd-shadow-lg);
    position: relative;
    overflow: hidden;
}

.sd-merits::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 120px;
    height: 120px;
    background: var(--sd-gold-pale);
    border-radius: 0 0 0 100%;
}

.sd-merits__list {
    list-style: none;
    padding: 0;
    margin: 0;
    position: relative;
    z-index: 1;
}

.sd-merits__item {
    display: flex;
    gap: var(--sd-space-md);
    padding: var(--sd-space-md) 0;
    border-bottom: 1px solid var(--sd-gray-200);
}

.sd-merits__item:last-child {
    border-bottom: none;
}

.sd-merits__check {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    background: var(--sd-gold);
    color: var(--sd-white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    margin-top: 2px;
}

.sd-merits__content {
    flex: 1;
}

.sd-merits__title {
    font-family: var(--sd-font-serif);
    font-size: 15px;
    font-weight: 700;
    color: var(--sd-navy-900);
    margin: 0 0 var(--sd-space-xs) 0;
}

.sd-merits__desc {
    font-size: 13px;
    color: var(--sd-gray-700);
    line-height: 1.6;
    margin: 0;
}

.sd-merits__cta {
    margin-top: var(--sd-space-lg);
    text-align: center;
}

.sd-link {
    color: var(--sd-navy-900);
    font-weight: 700;
    font-size: 14px;
    text-decoration: none;
    border-bottom: 1px solid var(--sd-navy-900);
    transition: all var(--sd-trans);
}

.sd-link:hover {
    color: var(--sd-gold);
    border-color: var(--sd-gold);
}

/* ==========================================================================
   Target Section
   ========================================================================== */
.sd-targets {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--sd-space-lg);
}

.sd-target {
    background: var(--sd-white);
    border: 1px solid var(--sd-gray-300);
    border-radius: var(--sd-radius);
    padding: var(--sd-space-lg);
    text-align: center;
    transition: all var(--sd-trans);
}

.sd-target:hover {
    border-color: var(--sd-gold);
    box-shadow: var(--sd-shadow-sm);
}

.sd-target__badge {
    display: inline-block;
    background: var(--sd-navy-900);
    color: var(--sd-white);
    font-size: 10px;
    font-weight: 700;
    font-family: var(--sd-font-sans);
    padding: 2px 10px;
    border-radius: 12px;
    margin-bottom: var(--sd-space-sm);
}

.sd-target__title {
    font-family: var(--sd-font-serif);
    font-size: 15px;
    font-weight: 700;
    color: var(--sd-navy-900);
    margin: 0 0 var(--sd-space-sm) 0;
}

.sd-target__desc {
    font-size: 12px;
    color: var(--sd-gray-700);
    line-height: 1.6;
    margin: 0;
}

.sd-targets__cta {
    margin-top: var(--sd-space-xl);
    background: var(--sd-navy-900);
    color: var(--sd-white);
    padding: var(--sd-space-xl);
    border-radius: var(--sd-radius-lg);
    text-align: center;
}

.sd-targets__cta-lead {
    font-family: var(--sd-font-serif);
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 var(--sd-space-lg) 0;
}

/* ==========================================================================
   About Section
   ========================================================================== */
.sd-about {
    max-width: 800px;
    margin: 0 auto;
}

.sd-about__block {
    margin-bottom: var(--sd-space-2xl);
}

.sd-about__block:last-child {
    margin-bottom: 0;
}

.sd-about__subtitle {
    font-family: var(--sd-font-sans);
    font-size: 16px;
    font-weight: 700;
    color: var(--sd-navy-900);
    margin: 0 0 var(--sd-space-md) 0;
    padding-left: var(--sd-space-md);
    border-left: 4px solid var(--sd-gold);
}

.sd-about__text {
    font-size: 14px;
    line-height: 1.9;
    color: var(--sd-gray-700);
    margin: 0 0 var(--sd-space-md) 0;
}

.sd-about__text:last-child {
    margin-bottom: 0;
}

.sd-about__tech-box {
    background: var(--sd-white);
    border: 1px solid var(--sd-gray-300);
    border-radius: var(--sd-radius-lg);
    padding: var(--sd-space-lg);
    margin-top: var(--sd-space-lg);
}

.sd-about__tech-title {
    font-family: var(--sd-font-sans);
    font-size: 14px;
    font-weight: 700;
    color: var(--sd-navy-900);
    margin: 0 0 var(--sd-space-md) 0;
    text-align: center;
}

.sd-about__tech-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sd-about__tech-list li {
    display: flex;
    align-items: center;
    gap: var(--sd-space-md);
    font-size: 13px;
    color: var(--sd-gray-700);
    padding: var(--sd-space-sm) 0;
}

.sd-about__tech-num {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    background: var(--sd-gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--sd-font-sans);
    font-size: 12px;
    font-weight: 700;
    color: var(--sd-navy-900);
    border-radius: 50%;
}

/* ==========================================================================
   FAQ Section
   ========================================================================== */
.sd-faq {
    max-width: 700px;
    margin: 0 auto;
}

.sd-faq__item {
    background: var(--sd-white);
    border: 1px solid var(--sd-gray-300);
    border-radius: var(--sd-radius);
    margin-bottom: var(--sd-space-md);
}

.sd-faq__question {
    display: flex;
    align-items: center;
    gap: var(--sd-space-md);
    padding: var(--sd-space-lg);
    cursor: pointer;
    list-style: none;
    font-size: 14px;
    font-weight: 700;
    color: var(--sd-navy-900);
}

.sd-faq__question::-webkit-details-marker {
    display: none;
}

.sd-faq__q {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
    background: var(--sd-navy-900);
    color: var(--sd-white);
    font-family: var(--sd-font-heading);
    font-size: 14px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--sd-radius);
}

.sd-faq__toggle {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    margin-left: auto;
    position: relative;
}

.sd-faq__toggle::before,
.sd-faq__toggle::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    background: var(--sd-gray-600);
}

.sd-faq__toggle::before {
    width: 12px;
    height: 2px;
    transform: translate(-50%, -50%);
}

.sd-faq__toggle::after {
    width: 2px;
    height: 12px;
    transform: translate(-50%, -50%);
    transition: transform 0.2s ease;
}

.sd-faq__item[open] .sd-faq__toggle::after {
    transform: translate(-50%, -50%) rotate(90deg);
}

.sd-faq__answer {
    display: flex;
    gap: var(--sd-space-md);
    padding: 0 var(--sd-space-lg) var(--sd-space-lg) var(--sd-space-lg);
    border-top: 1px dashed var(--sd-gray-300);
    padding-top: var(--sd-space-lg);
}

.sd-faq__a {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
    background: var(--sd-gold);
    color: var(--sd-white);
    font-family: var(--sd-font-heading);
    font-size: 14px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--sd-radius);
}

.sd-faq__answer p {
    flex: 1;
    font-size: 14px;
    line-height: 1.8;
    color: var(--sd-gray-700);
    margin: 0;
}

/* ==========================================================================
   Bottom CTA
   ========================================================================== */
.sd-bottom-cta {
    background: linear-gradient(135deg, var(--sd-navy-900) 0%, #1a3a70 100%);
    padding: var(--sd-space-4xl) 0;
    text-align: center;
    border-top: 4px solid var(--sd-gold);
}

.sd-bottom-cta__badge {
    display: inline-block;
    background: var(--sd-gold);
    color: var(--sd-white);
    font-size: 12px;
    font-weight: 700;
    font-family: var(--sd-font-sans);
    padding: var(--sd-space-xs) var(--sd-space-md);
    border-radius: 20px;
    margin-bottom: var(--sd-space-lg);
}

.sd-bottom-cta__title {
    font-family: var(--sd-font-heading);
    font-size: clamp(24px, 4vw, 36px);
    font-weight: 700;
    color: var(--sd-white);
    margin: 0 0 var(--sd-space-md) 0;
}

.sd-bottom-cta__text {
    font-size: 14px;
    color: var(--sd-gray-500);
    font-family: var(--sd-font-sans);
    margin: 0 0 var(--sd-space-xl) 0;
    line-height: 1.8;
}

.sd-bottom-cta__trust {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: var(--sd-space-lg);
    margin-top: var(--sd-space-xl);
}

.sd-bottom-cta__trust-item {
    display: flex;
    align-items: center;
    gap: var(--sd-space-xs);
    font-size: 10px;
    color: var(--sd-gray-500);
    font-family: var(--sd-font-sans);
    letter-spacing: 0.05em;
}

.sd-bottom-cta__trust-item svg {
    color: var(--sd-gold);
}

/* ==========================================================================
   Modal
   ========================================================================== */
.sd-modal {
    position: fixed;
    inset: 0;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all var(--sd-trans);
}

.sd-modal[aria-hidden="false"] {
    opacity: 1;
    visibility: visible;
}

.sd-modal__overlay {
    position: absolute;
    inset: 0;
    background: rgba(15, 35, 80, 0.7);
    cursor: pointer;
}

.sd-modal__container {
    position: relative;
    width: 90%;
    max-width: 600px;
    max-height: 85vh;
    background: var(--sd-white);
    border-radius: var(--sd-radius-lg);
    display: flex;
    flex-direction: column;
}

.sd-modal__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--sd-space-lg);
    border-bottom: 1px solid var(--sd-gray-300);
    background: var(--sd-gray-100);
}

.sd-modal__title {
    font-family: var(--sd-font-serif);
    font-size: 16px;
    font-weight: 700;
    color: var(--sd-navy-900);
    margin: 0;
}

.sd-modal__close {
    width: 32px;
    height: 32px;
    background: transparent;
    border: 1px solid var(--sd-gray-400);
    border-radius: var(--sd-radius);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--sd-gray-600);
    transition: all var(--sd-trans);
}

.sd-modal__close:hover {
    border-color: var(--sd-navy-900);
    color: var(--sd-navy-900);
}

.sd-modal__body {
    flex: 1;
    overflow-y: auto;
    padding: var(--sd-space-lg);
}

.sd-modal__footer {
    padding: var(--sd-space-md) var(--sd-space-lg);
    border-top: 1px solid var(--sd-gray-300);
    text-align: center;
}

.sd-legal__block {
    margin-bottom: var(--sd-space-lg);
}

.sd-legal__subtitle {
    font-size: 14px;
    font-weight: 700;
    color: var(--sd-navy-900);
    margin: 0 0 var(--sd-space-sm) 0;
    padding-bottom: var(--sd-space-xs);
    border-bottom: 1px solid var(--sd-gray-200);
}

.sd-legal ul {
    margin: 0;
    padding-left: var(--sd-space-lg);
}

.sd-legal li {
    font-size: 13px;
    color: var(--sd-gray-700);
    line-height: 1.7;
    margin-bottom: var(--sd-space-sm);
}

/* ==========================================================================
   Responsive
   ========================================================================== */
@media (max-width: 1024px) {
    .sd-hero__grid {
        grid-template-columns: 1fr;
        gap: var(--sd-space-xl);
    }
    
    .sd-hero__visual {
        order: -1;
        max-width: 400px;
        margin: 0 auto;
    }
    
    .sd-hero__card {
        transform: rotate(0);
    }
    
    .sd-flow {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .sd-features {
        grid-template-columns: 1fr;
    }
    
    .sd-targets {
        grid-template-columns: repeat(2, 1fr);
    }
    
    /* 診断カード - タブレット対応 */
    .sd-diagnosis-card__image {
        max-height: 280px;
    }
    
    .sd-diagnosis-card__content {
        padding: var(--sd-space-xl);
    }
    
    .sd-diagnosis-card__title {
        font-size: 24px;
    }
    
    .sd-diagnosis-card--with-image .sd-diagnosis-card__features {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .sd-section {
        padding: var(--sd-space-2xl) 0;
    }
    
    .sd-top-banner__inner {
        justify-content: center;
        text-align: center;
    }
    
    .sd-hero {
        padding: var(--sd-space-xl) 0;
    }
    
    .sd-hero__title,
    .sd-hero__lead {
        text-align: center;
    }
    
    .sd-hero__stats {
        grid-template-columns: 1fr;
    }
    
    .sd-hero__stat {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--sd-space-md);
        padding: var(--sd-space-sm) var(--sd-space-md);
    }
    
    .sd-hero__stat-label {
        text-align: left;
        margin-top: 0;
    }
    
    .sd-hero__stat-label br {
        display: none;
    }
    
    .sd-hero__actions {
        flex-direction: column;
    }
    
    .sd-hero__actions .sd-btn {
        width: 100%;
    }
    
    .sd-section__header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .sd-section__number {
        font-size: 40px;
    }
    
    .sd-flow {
        grid-template-columns: 1fr;
    }
    
    .sd-flow__card-en {
        font-size: 20px;
    }
    
    .sd-diagnosis-card {
        padding: var(--sd-space-lg);
    }
    
    .sd-diagnosis-card--with-image {
        padding: 0;
    }
    
    .sd-diagnosis-card__image {
        aspect-ratio: 16/9;
        max-height: 220px;
    }
    
    .sd-diagnosis-card__content {
        padding: var(--sd-space-lg);
    }
    
    .sd-diagnosis-card--with-image .sd-diagnosis-card__header {
        text-align: center;
    }
    
    .sd-diagnosis-card--with-image .sd-diagnosis-card__action {
        text-align: center;
    }
    
    .sd-diagnosis-card--with-image .sd-diagnosis-card__action .sd-btn--xl {
        width: 100%;
        max-width: 400px;
    }
    
    .sd-diagnosis-card--with-image .sd-diagnosis-card__footer {
        text-align: center;
    }
    
    .sd-diagnosis-card__features {
        grid-template-columns: 1fr;
    }
    
    .sd-diagnosis-card__title {
        font-size: 22px;
    }
    
    .sd-targets {
        grid-template-columns: 1fr;
    }
    
    .sd-trust-bar__items {
        gap: var(--sd-space-md);
    }
    
    .sd-trust-bar__item {
        font-size: 10px;
    }
    
    .sd-faq__question,
    .sd-faq__answer {
        padding: var(--sd-space-md);
    }
}
</style>

<!-- ============================================
     JavaScript
     ============================================ -->
<script>
(function() {
    'use strict';
    
    // ==============================
    // 利用規約モーダル
    // ==============================
    const termsModal = document.getElementById('termsModal');
    const openTermsBtn = document.getElementById('openTermsModal');
    const closeTermsBtn = document.getElementById('closeTermsModal');
    
    if (termsModal && openTermsBtn && closeTermsBtn) {
        openTermsBtn.addEventListener('click', function() {
            termsModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        });
        
        closeTermsBtn.addEventListener('click', function() {
            termsModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        });
        
        termsModal.querySelectorAll('[data-close-modal]').forEach(function(el) {
            el.addEventListener('click', function() {
                termsModal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            });
        });
    }
    
    // ==============================
    // スムーズスクロール
    // ==============================
    document.querySelectorAll('.smooth-scroll').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const headerOffset = 80;
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    
    // ==============================
    // アニメーション
    // ==============================
    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -50px 0px',
        threshold: 0.1
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    const animateElements = document.querySelectorAll('.sd-flow__card, .sd-feature, .sd-target, .sd-merits__item, .sd-faq__item');
    
    animateElements.forEach(function(el) {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });
    
    const style = document.createElement('style');
    style.textContent = `
        .sd-flow__card.is-visible,
        .sd-feature.is-visible,
        .sd-target.is-visible,
        .sd-merits__item.is-visible,
        .sd-faq__item.is-visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
    `;
    document.head.appendChild(style);
    
})();
</script>

<?php get_footer(); ?>
