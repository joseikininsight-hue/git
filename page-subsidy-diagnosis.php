<?php
/**
 * Template Name: Subsidy Diagnosis Pro (AI診断)
 * Description: RAG機能を活用した補助金・助成金AI診断 - 官公庁デザイン
 *
 * @package Grant_Insight_Perfect
 * @version 3.0.0
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

<div class="gov-wrapper" itemscope itemtype="https://schema.org/WebPage">

    <!-- ============================================
         官公庁スタイル トップバナー
         ============================================ -->
    <div class="gov-top-banner">
        <div class="gov-container">
            <div class="gov-top-banner__inner">
                <div class="gov-top-banner__left">
                    <span class="gov-top-banner__label">経済産業省・厚生労働省 補助金情報連携</span>
                </div>
                <div class="gov-top-banner__right">
                    <span class="gov-top-banner__date">最終更新：<?php echo date('Y年n月j日'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         SECTION 1: HERO - ヒーローセクション
         ============================================ -->
    <section class="gov-hero" aria-label="メインビジュアル">
        <div class="gov-container">
            <div class="gov-hero__grid">
                <!-- 左側：コンテンツ -->
                <div class="gov-hero__content">
                    <div class="gov-hero__badge-group">
                        <span class="gov-hero__badge gov-hero__badge--primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                            公式認定サービス
                        </span>
                        <span class="gov-hero__badge gov-hero__badge--secondary">無料・登録不要</span>
                    </div>
                    
                    <h1 class="gov-hero__title" itemprop="name">
                        補助金・助成金<br>
                        <span class="gov-hero__title-accent">無料診断サービス</span>
                    </h1>
                    
                    <p class="gov-hero__lead">
                        国・地方自治体が提供する補助金・助成金制度から、<br class="gov-pc-only">
                        貴社・貴方に最適な制度をAIが自動で検索・提案いたします。
                    </p>
                    
                    <div class="gov-hero__stats">
                        <div class="gov-hero__stat">
                            <span class="gov-hero__stat-number">1,000</span>
                            <span class="gov-hero__stat-unit">件以上</span>
                            <span class="gov-hero__stat-label">補助金データ収録</span>
                        </div>
                        <div class="gov-hero__stat">
                            <span class="gov-hero__stat-number">47</span>
                            <span class="gov-hero__stat-unit">都道府県</span>
                            <span class="gov-hero__stat-label">全国対応</span>
                        </div>
                        <div class="gov-hero__stat">
                            <span class="gov-hero__stat-number">24</span>
                            <span class="gov-hero__stat-unit">時間</span>
                            <span class="gov-hero__stat-label">いつでも利用可能</span>
                        </div>
                    </div>
                    
                    <div class="gov-hero__actions">
                        <button type="button" class="gov-btn gov-btn--primary gov-btn--large diag-popup-trigger" data-gip-modal-open="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <span>無料診断を開始する</span>
                        </button>
                        <a href="#about" class="gov-btn gov-btn--outline smooth-scroll">
                            <span>サービス詳細を見る</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <polyline points="19 12 12 19 5 12"/>
                            </svg>
                        </a>
                    </div>
                    
                    <div class="gov-hero__notice">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <span>本サービスは情報提供を目的としております。申請にあたっては各制度の公募要領をご確認ください。</span>
                    </div>
                </div>
                
                <!-- 右側：画像 -->
                <div class="gov-hero__visual">
                    <div class="gov-hero__image-frame">
                        <img 
                            src="<?php echo esc_url($img_base); ?>1.png" 
                            alt="補助金診断サービス - ビジネス相談イメージ" 
                            class="gov-hero__image"
                            width="600"
                            height="400"
                            loading="eager"
                        >
                        <div class="gov-hero__image-overlay">
                            <div class="gov-hero__overlay-badge">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                <span>セキュリティ認証済</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         信頼性バッジセクション
         ============================================ -->
    <section class="gov-trust-bar">
        <div class="gov-container">
            <div class="gov-trust-bar__inner">
                <div class="gov-trust-bar__item">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span>SSL暗号化通信</span>
                </div>
                <div class="gov-trust-bar__item">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <span>個人情報保護</span>
                </div>
                <div class="gov-trust-bar__item">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <span>公的機関データ連携</span>
                </div>
                <div class="gov-trust-bar__item">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span>リアルタイム更新</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 2: FLOW - 診断の流れ
         ============================================ -->
    <section class="gov-section gov-section--alt" aria-labelledby="flow-title">
        <div class="gov-container">
            <header class="gov-section__header">
                <div class="gov-section__header-line"></div>
                <div class="gov-section__header-content">
                    <span class="gov-section__number">01</span>
                    <h2 id="flow-title" class="gov-section__title">ご利用の流れ</h2>
                    <p class="gov-section__subtitle">4つのステップで最適な補助金・助成金をご提案いたします</p>
                </div>
            </header>

            <div class="gov-flow">
                <!-- Step 1 -->
                <article class="gov-flow__item">
                    <div class="gov-flow__image-wrapper">
                        <img 
                            src="<?php echo esc_url($img_base); ?>2.png" 
                            alt="ステップ1：事業内容のヒアリング" 
                            loading="lazy"
                            width="400"
                            height="300"
                        >
                    </div>
                    <div class="gov-flow__content">
                        <div class="gov-flow__step-indicator">
                            <span class="gov-flow__step-number">STEP</span>
                            <span class="gov-flow__step-num">01</span>
                        </div>
                        <h3 class="gov-flow__title">事業内容のヒアリング</h3>
                        <p class="gov-flow__description">
                            業種・従業員数・所在地など、基本的な事業情報をチャット形式でお伺いいたします。専門知識は不要です。
                        </p>
                    </div>
                </article>

                <!-- Step 2 -->
                <article class="gov-flow__item">
                    <div class="gov-flow__image-wrapper">
                        <img 
                            src="<?php echo esc_url($img_base); ?>6.png" 
                            alt="ステップ2：ニーズの深掘り" 
                            loading="lazy"
                            width="400"
                            height="300"
                        >
                    </div>
                    <div class="gov-flow__content">
                        <div class="gov-flow__step-indicator">
                            <span class="gov-flow__step-number">STEP</span>
                            <span class="gov-flow__step-num">02</span>
                        </div>
                        <h3 class="gov-flow__title">ニーズの深掘り</h3>
                        <p class="gov-flow__description">
                            設備投資・人材育成・IT導入など、具体的なご要望をお伺いし、最適な制度を特定いたします。
                        </p>
                    </div>
                </article>

                <!-- Step 3 -->
                <article class="gov-flow__item">
                    <div class="gov-flow__image-wrapper">
                        <img 
                            src="<?php echo esc_url($img_base); ?>3.png" 
                            alt="ステップ3：データベース検索" 
                            loading="lazy"
                            width="400"
                            height="300"
                        >
                    </div>
                    <div class="gov-flow__content">
                        <div class="gov-flow__step-indicator">
                            <span class="gov-flow__step-number">STEP</span>
                            <span class="gov-flow__step-num">03</span>
                        </div>
                        <h3 class="gov-flow__title">データベース検索</h3>
                        <p class="gov-flow__description">
                            1,000件以上の補助金・助成金データベースから、AIが条件に合致する制度を自動で抽出いたします。
                        </p>
                    </div>
                </article>

                <!-- Step 4 -->
                <article class="gov-flow__item">
                    <div class="gov-flow__image-wrapper">
                        <img 
                            src="<?php echo esc_url($img_base); ?>8.png" 
                            alt="ステップ4：診断結果のご提示" 
                            loading="lazy"
                            width="400"
                            height="300"
                        >
                    </div>
                    <div class="gov-flow__content">
                        <div class="gov-flow__step-indicator">
                            <span class="gov-flow__step-number">STEP</span>
                            <span class="gov-flow__step-num">04</span>
                        </div>
                        <h3 class="gov-flow__title">診断結果のご提示</h3>
                        <p class="gov-flow__description">
                            適合度の高い順に補助金・助成金をご提案。金額・申請期限・要件を分かりやすく表示いたします。
                        </p>
                    </div>
                </article>
            </div>
            
            <div class="gov-flow__cta">
                <button type="button" class="gov-btn gov-btn--primary gov-btn--large diag-popup-trigger" data-gip-modal-open="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span>無料診断を開始する</span>
                </button>
                <p class="gov-flow__cta-note">所要時間：約3分 ｜ 会員登録不要</p>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 3: DIAGNOSIS CTA
         ============================================ -->
    <section id="diagnosis-app" class="gov-section gov-diagnosis-section" aria-labelledby="diagnosis-title">
        <div class="gov-container">
            <div class="gov-diagnosis-card">
                <div class="gov-diagnosis-card__header">
                    <div class="gov-diagnosis-card__icon">
                        <img 
                            src="<?php echo esc_url($img_base); ?>7.png" 
                            alt="AI診断コンシェルジュ" 
                            width="80" 
                            height="80"
                        >
                    </div>
                    <div class="gov-diagnosis-card__title-area">
                        <span class="gov-diagnosis-card__label">AI補助金診断</span>
                        <h2 id="diagnosis-title" class="gov-diagnosis-card__title">補助金診断コンシェルジュ</h2>
                    </div>
                </div>
                
                <div class="gov-diagnosis-card__body">
                    <p class="gov-diagnosis-card__description">
                        人工知能（AI）が貴社・貴方の事業内容を分析し、<br class="gov-pc-only">
                        申請可能な補助金・助成金制度を自動で検索・ご提案いたします。
                    </p>
                    
                    <ul class="gov-diagnosis-card__features">
                        <li>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <span>完全無料でご利用いただけます</span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <span>会員登録・個人情報の入力は不要です</span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <span>24時間365日いつでもご利用可能です</span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <span>所要時間は約3分程度です</span>
                        </li>
                    </ul>
                    
                    <div class="gov-diagnosis-card__action">
                        <button type="button" class="gov-btn gov-btn--primary gov-btn--xl diag-popup-trigger" data-gip-modal-open="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <span>診断を開始する</span>
                        </button>
                    </div>
                </div>
                
                <div class="gov-diagnosis-card__footer">
                    <button type="button" class="gov-terms-btn" id="openTermsModal" aria-haspopup="dialog">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        <span>ご利用規約・免責事項</span>
                    </button>
                    <p class="gov-diagnosis-card__terms-note">本サービスのご利用により、利用規約に同意したものとみなします。</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 4: FEATURES
         ============================================ -->
    <section class="gov-section gov-section--alt" aria-labelledby="features-title">
        <div class="gov-container">
            <header class="gov-section__header">
                <div class="gov-section__header-line"></div>
                <div class="gov-section__header-content">
                    <span class="gov-section__number">02</span>
                    <h2 id="features-title" class="gov-section__title">サービスの特長</h2>
                    <p class="gov-section__subtitle">安心・安全にご利用いただける4つのポイント</p>
                </div>
            </header>

            <div class="gov-features">
                <article class="gov-feature-card">
                    <div class="gov-feature-card__icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                            <line x1="12" y1="22.08" x2="12" y2="12"/>
                        </svg>
                    </div>
                    <div class="gov-feature-card__content">
                        <h3 class="gov-feature-card__title">AI解析技術</h3>
                        <p class="gov-feature-card__description">
                            最新のRAG（検索拡張生成）技術を活用し、事業内容を理解して最適な制度を抽出いたします。
                        </p>
                    </div>
                </article>

                <article class="gov-feature-card">
                    <div class="gov-feature-card__icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <div class="gov-feature-card__content">
                        <h3 class="gov-feature-card__title">即時検索・結果表示</h3>
                        <p class="gov-feature-card__description">
                            回答内容に基づき、データベースを即座に検索。結果を数秒でご提示いたします。
                        </p>
                    </div>
                </article>

                <article class="gov-feature-card">
                    <div class="gov-feature-card__icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <div class="gov-feature-card__content">
                        <h3 class="gov-feature-card__title">登録不要・完全無料</h3>
                        <p class="gov-feature-card__description">
                            会員登録や個人情報の入力は一切不要。どなたでも無料でご利用いただけます。
                        </p>
                    </div>
                </article>

                <article class="gov-feature-card">
                    <div class="gov-feature-card__icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <div class="gov-feature-card__content">
                        <h3 class="gov-feature-card__title">セキュリティ対応</h3>
                        <p class="gov-feature-card__description">
                            SSL暗号化通信により、安全な環境でサービスをご利用いただけます。
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 5: BENEFITS
         ============================================ -->
    <section class="gov-section" aria-labelledby="benefits-title">
        <div class="gov-container">
            <header class="gov-section__header">
                <div class="gov-section__header-line"></div>
                <div class="gov-section__header-content">
                    <span class="gov-section__number">03</span>
                    <h2 id="benefits-title" class="gov-section__title">ご利用のメリット</h2>
                    <p class="gov-section__subtitle">専門家への相談前の事前調査としてもご活用いただけます</p>
                </div>
            </header>

            <div class="gov-benefits">
                <div class="gov-benefits__image">
                    <img 
                        src="<?php echo esc_url($img_base); ?>4.png" 
                        alt="補助金診断サービスを利用するビジネスパーソン" 
                        width="600" 
                        height="400"
                        loading="lazy"
                    >
                </div>
                <div class="gov-benefits__content">
                    <ul class="gov-benefits__list">
                        <li class="gov-benefits__item">
                            <div class="gov-benefits__item-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <div class="gov-benefits__item-content">
                                <h4 class="gov-benefits__item-title">検索時間の大幅削減</h4>
                                <p class="gov-benefits__item-text">条件に合う補助金をAIが自動で検索。情報収集の手間を削減できます。</p>
                            </div>
                        </li>
                        <li class="gov-benefits__item">
                            <div class="gov-benefits__item-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <div class="gov-benefits__item-content">
                                <h4 class="gov-benefits__item-title">見落としリスクの軽減</h4>
                                <p class="gov-benefits__item-text">複数の制度を横断的に検索し、適合する可能性のある制度を網羅的にご提示。</p>
                            </div>
                        </li>
                        <li class="gov-benefits__item">
                            <div class="gov-benefits__item-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <div class="gov-benefits__item-content">
                                <h4 class="gov-benefits__item-title">情報の整理・比較</h4>
                                <p class="gov-benefits__item-text">申請要件・補助金額・申請期限をまとめて確認でき、比較検討が容易です。</p>
                            </div>
                        </li>
                        <li class="gov-benefits__item">
                            <div class="gov-benefits__item-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <div class="gov-benefits__item-content">
                                <h4 class="gov-benefits__item-title">24時間利用可能</h4>
                                <p class="gov-benefits__item-text">営業時間を気にせず、お好きな時間にいつでも診断をご利用いただけます。</p>
                            </div>
                        </li>
                        <li class="gov-benefits__item">
                            <div class="gov-benefits__item-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <div class="gov-benefits__item-content">
                                <h4 class="gov-benefits__item-title">専門知識不要</h4>
                                <p class="gov-benefits__item-text">チャット形式の簡単な質問に答えるだけで、どなたでも診断可能です。</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="gov-benefits__cta">
                <button type="button" class="gov-btn gov-btn--primary gov-btn--large diag-popup-trigger" data-gip-modal-open="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span>無料で診断を始める</span>
                </button>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 6: TARGET
         ============================================ -->
    <section class="gov-section gov-section--alt" aria-labelledby="target-title">
        <div class="gov-container">
            <header class="gov-section__header">
                <div class="gov-section__header-line"></div>
                <div class="gov-section__header-content">
                    <span class="gov-section__number">04</span>
                    <h2 id="target-title" class="gov-section__title">ご利用対象者</h2>
                    <p class="gov-section__subtitle">以下のような方にご活用いただいております</p>
                </div>
            </header>

            <div class="gov-targets">
                <article class="gov-target-card">
                    <div class="gov-target-card__header">
                        <span class="gov-target-card__category">法人</span>
                        <h3 class="gov-target-card__title">中小企業の経営者様</h3>
                    </div>
                    <p class="gov-target-card__description">設備投資や事業拡大を検討されている企業様</p>
                </article>

                <article class="gov-target-card">
                    <div class="gov-target-card__header">
                        <span class="gov-target-card__category">個人</span>
                        <h3 class="gov-target-card__title">個人事業主・フリーランス様</h3>
                    </div>
                    <p class="gov-target-card__description">創業支援や運転資金をお探しの方</p>
                </article>

                <article class="gov-target-card">
                    <div class="gov-target-card__header">
                        <span class="gov-target-card__category">担当者</span>
                        <h3 class="gov-target-card__title">IT・DX推進ご担当者様</h3>
                    </div>
                    <p class="gov-target-card__description">IT導入補助金等を検討されている方</p>
                </article>

                <article class="gov-target-card">
                    <div class="gov-target-card__header">
                        <span class="gov-target-card__category">担当者</span>
                        <h3 class="gov-target-card__title">人事・採用ご担当者様</h3>
                    </div>
                    <p class="gov-target-card__description">雇用関連の助成金をお探しの方</p>
                </article>

                <article class="gov-target-card">
                    <div class="gov-target-card__header">
                        <span class="gov-target-card__category">創業</span>
                        <h3 class="gov-target-card__title">起業準備中の方</h3>
                    </div>
                    <p class="gov-target-card__description">創業・開業に使える制度をお探しの方</p>
                </article>

                <article class="gov-target-card">
                    <div class="gov-target-card__header">
                        <span class="gov-target-card__category">専門家</span>
                        <h3 class="gov-target-card__title">士業・コンサルタント様</h3>
                    </div>
                    <p class="gov-target-card__description">顧客への情報提供ツールとしてご活用の方</p>
                </article>
            </div>
            
            <div class="gov-targets__cta">
                <p class="gov-targets__cta-lead">あなたも今すぐ診断してみませんか？</p>
                <button type="button" class="gov-btn gov-btn--primary gov-btn--large diag-popup-trigger" data-gip-modal-open="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span>無料診断を開始する</span>
                </button>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 7: ABOUT
         ============================================ -->
    <section id="about" class="gov-section" aria-labelledby="about-title">
        <div class="gov-container">
            <header class="gov-section__header">
                <div class="gov-section__header-line"></div>
                <div class="gov-section__header-content">
                    <span class="gov-section__number">05</span>
                    <h2 id="about-title" class="gov-section__title">補助金診断とは</h2>
                    <p class="gov-section__subtitle">事業者様に最適な補助金・助成金を見つけるサービスです</p>
                </div>
            </header>

            <div class="gov-about">
                <div class="gov-about__content">
                    <div class="gov-about__block">
                        <h3 class="gov-about__subtitle">補助金診断サービスの概要</h3>
                        <p class="gov-about__text">
                            <strong>補助金診断</strong>とは、事業者様の業種・規模・目的などの情報をもとに、申請可能な補助金・助成金を検索するサービスです。
                            国や地方自治体は毎年多くの補助金制度を設けていますが、その数は数千種類にのぼり、自社に適した制度を見つけることは容易ではありません。
                        </p>
                        <p class="gov-about__text">
                            補助金診断サービスをご利用いただくことで、専門の<strong>補助金診断士</strong>や中小企業診断士にご相談される前に、
                            どのような制度が利用可能か事前に把握することができます。これにより、専門家へのご相談をより効率的に行うことが可能となります。
                        </p>
                    </div>

                    <div class="gov-about__block">
                        <h3 class="gov-about__subtitle">法人から個人まで幅広く対応</h3>
                        <p class="gov-about__text">
                            当サービスの補助金診断は、中小企業や法人様だけでなく、<strong>個人事業主やフリーランス</strong>の方にもご利用いただけます。
                            創業支援、設備投資、IT導入、人材育成など、様々な目的に応じた補助金・助成金情報を検索可能です。
                        </p>
                    </div>

                    <div class="gov-about__block">
                        <h3 class="gov-about__subtitle">検索技術について</h3>
                        <p class="gov-about__text">
                            本サービスでは、<strong>RAG（Retrieval-Augmented Generation）</strong>と呼ばれる検索拡張生成技術を採用しております。
                            RAGは、大規模なデータベースから関連情報を検索し、その情報をもとに回答を生成する技術です。
                        </p>
                        <p class="gov-about__text">
                            従来のキーワード検索とは異なり、文脈や意味を理解した検索が可能なため、
                            「設備を導入したい」「従業員を増やしたい」といった自然な言葉でも、適切な補助金を見つけることができます。
                        </p>
                        
                        <div class="gov-about__tech-box">
                            <h4 class="gov-about__tech-title">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="16" x2="12" y2="12"/>
                                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                RAG技術の仕組み
                            </h4>
                            <ol class="gov-about__tech-list">
                                <li><strong>質問の理解：</strong>入力された内容から、求めている情報を理解します</li>
                                <li><strong>データベース検索：</strong>1,000件以上の補助金情報から関連度の高い制度を抽出します</li>
                                <li><strong>結果の整理：</strong>検索結果を優先度順に整理してご提示します</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 8: FAQ
         ============================================ -->
    <section class="gov-section gov-section--alt" aria-labelledby="faq-title" itemscope itemtype="https://schema.org/FAQPage">
        <div class="gov-container">
            <header class="gov-section__header">
                <div class="gov-section__header-line"></div>
                <div class="gov-section__header-content">
                    <span class="gov-section__number">06</span>
                    <h2 id="faq-title" class="gov-section__title">よくあるご質問</h2>
                    <p class="gov-section__subtitle">補助金診断に関するお問い合わせの多いご質問</p>
                </div>
            </header>

            <div class="gov-faq">
                <div class="gov-faq__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <h3 class="gov-faq__question" itemprop="name">
                        <span class="gov-faq__q">Q</span>
                        補助金診断は本当に無料ですか？
                    </h3>
                    <div class="gov-faq__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <span class="gov-faq__a">A</span>
                        <p itemprop="text">はい、当サービスの補助金診断は<strong>完全無料</strong>でご利用いただけます。会員登録も不要で、何度でも診断可能です。</p>
                    </div>
                </div>

                <div class="gov-faq__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <h3 class="gov-faq__question" itemprop="name">
                        <span class="gov-faq__q">Q</span>
                        個人でも補助金診断を利用できますか？
                    </h3>
                    <div class="gov-faq__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <span class="gov-faq__a">A</span>
                        <p itemprop="text">はい、<strong>個人事業主やフリーランスの方</strong>も補助金診断をご利用いただけます。創業支援や事業拡大に関する補助金など、個人向けの制度も多数収録しております。</p>
                    </div>
                </div>

                <div class="gov-faq__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <h3 class="gov-faq__question" itemprop="name">
                        <span class="gov-faq__q">Q</span>
                        補助金診断士とは何ですか？
                    </h3>
                    <div class="gov-faq__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <span class="gov-faq__a">A</span>
                        <p itemprop="text"><strong>補助金診断士</strong>は、補助金・助成金の申請支援を専門とする民間資格です。当サービスは補助金診断士へのご相談前の事前調査としてもご活用いただけます。実際の申請手続きには、専門家へのご相談をお勧めいたします。</p>
                    </div>
                </div>

                <div class="gov-faq__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <h3 class="gov-faq__question" itemprop="name">
                        <span class="gov-faq__q">Q</span>
                        診断結果に表示された補助金は必ず申請できますか？
                    </h3>
                    <div class="gov-faq__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <span class="gov-faq__a">A</span>
                        <p itemprop="text">診断結果は参考情報としてご活用ください。実際の申請にあたっては、各補助金の公募要領で<strong>申請要件を必ずご確認</strong>ください。制度によっては申請期限や予算状況により受付が終了している場合がございます。</p>
                    </div>
                </div>

                <div class="gov-faq__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <h3 class="gov-faq__question" itemprop="name">
                        <span class="gov-faq__q">Q</span>
                        補助金と助成金の違いは何ですか？
                    </h3>
                    <div class="gov-faq__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <span class="gov-faq__a">A</span>
                        <p itemprop="text"><strong>補助金</strong>は主に経済産業省系の制度で、審査があり採択率が設定されております。<strong>助成金</strong>は主に厚生労働省系の制度で、要件を満たせば原則として受給可能です。当サービスでは両方の制度を検索可能です。</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 9: FINAL CTA
         ============================================ -->
    <section class="gov-section gov-final-cta" aria-labelledby="final-cta-title">
        <div class="gov-container">
            <div class="gov-final-cta__content">
                <div class="gov-final-cta__badge">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span>無料・登録不要</span>
                </div>
                <h2 id="final-cta-title" class="gov-final-cta__title">
                    今すぐ無料で<br class="gov-sp-only">補助金診断を開始
                </h2>
                <p class="gov-final-cta__text">
                    会員登録不要・所要時間約3分<br>
                    法人・個人問わず、申請可能な補助金・助成金を検索いたします
                </p>
                <button type="button" class="gov-btn gov-btn--white gov-btn--xl diag-popup-trigger" data-gip-modal-open="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span>無料診断を開始する</span>
                </button>
                <div class="gov-final-cta__trust">
                    <div class="gov-final-cta__trust-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <span>SSL暗号化通信</span>
                    </div>
                    <div class="gov-final-cta__trust-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <span>個人情報保護対応</span>
                    </div>
                    <div class="gov-final-cta__trust-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <span>24時間対応</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- フローティングCTA（スマホ用） -->
<div class="gov-floating-cta">
    <button type="button" class="gov-btn gov-btn--primary diag-popup-trigger" data-gip-modal-open="true">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <span>無料診断を開始</span>
    </button>
</div>

<!-- ============================================
     利用規約モーダル
     ============================================ -->
<div class="gov-modal" id="termsModal" role="dialog" aria-labelledby="termsModalTitle" aria-modal="true" aria-hidden="true">
    <div class="gov-modal__overlay" data-close-modal></div>
    <div class="gov-modal__container">
        <header class="gov-modal__header">
            <h3 id="termsModalTitle" class="gov-modal__title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                ご利用規約・免責事項
            </h3>
            <button type="button" class="gov-modal__close" id="closeTermsModal" aria-label="閉じる">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </header>
        <div class="gov-modal__body">
            <div class="gov-legal-content">
                <div class="gov-legal-block">
                    <h4 class="gov-legal-subtitle">1. サービスの性質について</h4>
                    <ul class="gov-legal-list">
                        <li>本サービスは、AI（人工知能）による自動診断システムであり、補助金・助成金に関する情報提供を目的としております。</li>
                        <li>診断結果は、ユーザー様が入力された情報に基づきAIが自動生成したものであり、専門家による個別のアドバイスや助言ではございません。</li>
                        <li>本サービスは情報提供のみを目的としており、特定の補助金・助成金の申請を推奨・勧誘するものではございません。</li>
                    </ul>
                </div>

                <div class="gov-legal-block">
                    <h4 class="gov-legal-subtitle">2. 診断結果の取り扱いについて</h4>
                    <ul class="gov-legal-list">
                        <li><strong>診断結果は参考情報としてご活用ください。</strong>実際の申請にあたっては、必ず各補助金・助成金の公募要領、申請要件等を公式サイトでご確認ください。</li>
                        <li>診断結果に表示された補助金・助成金への申請資格を保証するものではございません。</li>
                        <li>診断結果は採択を保証するものではなく、申請結果について当社は一切の責任を負いかねます。</li>
                        <li>補助金・助成金の情報は随時変更される可能性がございます。最新情報は各省庁・自治体の公式サイトでご確認ください。</li>
                    </ul>
                </div>

                <div class="gov-legal-block">
                    <h4 class="gov-legal-subtitle">3. 情報の正確性について</h4>
                    <ul class="gov-legal-list">
                        <li>当社は診断結果の正確性、完全性、最新性について保証いたしかねます。</li>
                        <li>AIによる自動生成のため、情報に誤りが含まれる可能性がございます。</li>
                        <li>表示される補助金・助成金の金額、申請期限、要件等は変更される場合がございます。</li>
                    </ul>
                </div>

                <div class="gov-legal-block">
                    <h4 class="gov-legal-subtitle">4. 免責事項</h4>
                    <ul class="gov-legal-list">
                        <li>本サービスのご利用により生じたいかなる損害（直接損害、間接損害、逸失利益、その他の損害を含む）についても、当社は一切の責任を負いかねます。</li>
                        <li>本サービスのご利用に基づく補助金・助成金の申請、不採択、その他の結果について、当社は一切の責任を負いかねます。</li>
                        <li>システムの不具合、メンテナンス、その他の理由によりサービスが一時的にご利用できない場合がございます。</li>
                    </ul>
                </div>

                <div class="gov-legal-block">
                    <h4 class="gov-legal-subtitle">5. 入力情報の取り扱いについて</h4>
                    <ul class="gov-legal-list">
                        <li>本サービスで入力された情報は、診断結果の生成およびサービス改善の目的でのみ使用いたします。</li>
                        <li>個人を特定できる情報の入力は推奨しておりません。</li>
                        <li>詳細は当社プライバシーポリシーをご確認ください。</li>
                    </ul>
                </div>

                <div class="gov-legal-block">
                    <h4 class="gov-legal-subtitle">6. 推奨事項</h4>
                    <ul class="gov-legal-list">
                        <li>補助金・助成金の申請をご検討の際は、税理士、中小企業診断士、社会保険労務士等の専門家にご相談されることをお勧めいたします。</li>
                        <li>各補助金・助成金の詳細については、実施機関の公式サイトまたは相談窓口でご確認ください。</li>
                    </ul>
                </div>
            </div>
        </div>
        <footer class="gov-modal__footer">
            <button type="button" class="gov-btn gov-btn--primary" data-close-modal>
                閉じる
            </button>
        </footer>
    </div>
</div>

<!-- ============================================
     STYLES - 官公庁デザイン
     ============================================ -->
<style>
/* ==========================================================================
   CSS Custom Properties - 官公庁カラースキーム
   ========================================================================== */
:root {
    /* Primary Colors - ネイビー系 */
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
    
    /* Accent Colors */
    --gov-gold: #c9a227;
    --gov-gold-light: #f0e6c8;
    --gov-green: #2e7d32;
    --gov-green-light: #e8f5e9;
    --gov-red: #c62828;
    --gov-red-light: #ffebee;
    
    /* Neutral Colors */
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
    
    /* Typography */
    --gov-font-serif: "Shippori Mincho", "Yu Mincho", "YuMincho", "Hiragino Mincho ProN", serif;
    --gov-font-sans: "Noto Sans JP", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    --gov-font-mono: "SF Mono", "Monaco", "Inconsolata", "Roboto Mono", monospace;
    
    /* Spacing */
    --gov-space-xs: 4px;
    --gov-space-sm: 8px;
    --gov-space-md: 16px;
    --gov-space-lg: 24px;
    --gov-space-xl: 32px;
    --gov-space-2xl: 48px;
    --gov-space-3xl: 64px;
    --gov-space-4xl: 96px;
    
    /* Layout */
    --gov-container-max: 1100px;
    --gov-container-padding: 24px;
    
    /* Effects */
    --gov-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
    --gov-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
    --gov-shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
    --gov-shadow-xl: 0 20px 50px rgba(0, 0, 0, 0.15);
    
    --gov-transition-fast: 150ms ease;
    --gov-transition-base: 250ms ease;
    --gov-transition-slow: 400ms ease;
    
    /* Border */
    --gov-border-radius: 4px;
    --gov-border-radius-lg: 8px;
}

/* ==========================================================================
   Base Styles
   ========================================================================== */
.gov-wrapper {
    background-color: var(--gov-white);
    color: var(--gov-gray-900);
    font-family: var(--gov-font-sans);
    font-size: 16px;
    line-height: 1.8;
    overflow-x: hidden;
    max-width: 100vw;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.gov-wrapper *,
.gov-wrapper *::before,
.gov-wrapper *::after {
    box-sizing: border-box;
}

.gov-container {
    max-width: var(--gov-container-max);
    margin: 0 auto;
    padding: 0 var(--gov-container-padding);
}

.gov-pc-only {
    display: inline;
}

.gov-sp-only {
    display: none;
}

@media (max-width: 768px) {
    .gov-pc-only {
        display: none;
    }
    .gov-sp-only {
        display: inline;
    }
}

/* ==========================================================================
   Top Banner - 官公庁スタイル
   ========================================================================== */
.gov-top-banner {
    background-color: var(--gov-navy-800);
    color: var(--gov-white);
    padding: var(--gov-space-sm) 0;
    border-bottom: 3px solid var(--gov-gold);
}

.gov-top-banner__inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
}

.gov-top-banner__label {
    display: flex;
    align-items: center;
    gap: var(--gov-space-sm);
    font-weight: 500;
}

.gov-top-banner__date {
    color: var(--gov-navy-300);
}

/* ==========================================================================
   Hero Section
   ========================================================================== */
.gov-hero {
    padding: var(--gov-space-3xl) 0;
    background: linear-gradient(180deg, var(--gov-navy-50) 0%, var(--gov-white) 100%);
    border-bottom: 1px solid var(--gov-gray-200);
}

.gov-hero__grid {
    display: grid;
    grid-template-columns: 1.1fr 0.9fr;
    gap: var(--gov-space-3xl);
    align-items: center;
}

.gov-hero__content {
    order: 1;
}

.gov-hero__visual {
    order: 2;
}

.gov-hero__badge-group {
    display: flex;
    flex-wrap: wrap;
    gap: var(--gov-space-sm);
    margin-bottom: var(--gov-space-lg);
}

.gov-hero__badge {
    display: inline-flex;
    align-items: center;
    gap: var(--gov-space-xs);
    padding: var(--gov-space-xs) var(--gov-space-md);
    font-size: 12px;
    font-weight: 600;
    border-radius: var(--gov-border-radius);
}

.gov-hero__badge--primary {
    background-color: var(--gov-navy-800);
    color: var(--gov-white);
}

.gov-hero__badge--secondary {
    background-color: var(--gov-gold-light);
    color: var(--gov-navy-800);
    border: 1px solid var(--gov-gold);
}

.gov-hero__badge svg {
    width: 14px;
    height: 14px;
}

.gov-hero__title {
    font-family: var(--gov-font-serif);
    font-size: clamp(28px, 4vw, 40px);
    font-weight: 700;
    line-height: 1.3;
    color: var(--gov-navy-900);
    margin: 0 0 var(--gov-space-lg) 0;
    letter-spacing: 0.02em;
}

.gov-hero__title-accent {
    display: block;
    color: var(--gov-navy-700);
    font-size: 0.85em;
}

.gov-hero__lead {
    font-size: 15px;
    line-height: 1.9;
    color: var(--gov-gray-700);
    margin: 0 0 var(--gov-space-xl) 0;
}

.gov-hero__stats {
    display: flex;
    gap: var(--gov-space-xl);
    margin-bottom: var(--gov-space-xl);
    padding: var(--gov-space-lg);
    background-color: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-border-radius-lg);
}

.gov-hero__stat {
    text-align: center;
    flex: 1;
}

.gov-hero__stat-number {
    font-family: var(--gov-font-mono);
    font-size: 32px;
    font-weight: 700;
    color: var(--gov-navy-800);
    line-height: 1;
}

.gov-hero__stat-unit {
    font-size: 14px;
    color: var(--gov-navy-600);
    margin-left: 2px;
}

.gov-hero__stat-label {
    display: block;
    font-size: 11px;
    color: var(--gov-gray-600);
    margin-top: var(--gov-space-xs);
}

.gov-hero__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--gov-space-md);
    margin-bottom: var(--gov-space-lg);
}

.gov-hero__notice {
    display: flex;
    align-items: flex-start;
    gap: var(--gov-space-sm);
    font-size: 12px;
    color: var(--gov-gray-600);
    padding: var(--gov-space-md);
    background-color: var(--gov-gray-100);
    border-radius: var(--gov-border-radius);
    border-left: 3px solid var(--gov-gray-400);
}

.gov-hero__notice svg {
    flex-shrink: 0;
    margin-top: 2px;
}

/* Hero Image */
.gov-hero__image-frame {
    position: relative;
    border-radius: var(--gov-border-radius-lg);
    overflow: hidden;
    box-shadow: var(--gov-shadow-lg);
}

.gov-hero__image {
    display: block;
    width: 100%;
    height: auto;
    transition: transform var(--gov-transition-slow);
}

.gov-hero__image-frame:hover .gov-hero__image {
    transform: scale(1.02);
}

.gov-hero__image-overlay {
    position: absolute;
    bottom: var(--gov-space-md);
    right: var(--gov-space-md);
}

.gov-hero__overlay-badge {
    display: flex;
    align-items: center;
    gap: var(--gov-space-sm);
    background-color: rgba(255, 255, 255, 0.95);
    padding: var(--gov-space-sm) var(--gov-space-md);
    border-radius: var(--gov-border-radius);
    font-size: 12px;
    font-weight: 600;
    color: var(--gov-green);
    box-shadow: var(--gov-shadow-md);
}

.gov-hero__overlay-badge svg {
    width: 18px;
    height: 18px;
}

/* ==========================================================================
   Button Styles
   ========================================================================== */
.gov-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--gov-space-sm);
    padding: var(--gov-space-md) var(--gov-space-xl);
    min-height: 48px;
    font-family: var(--gov-font-sans);
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    border: 2px solid transparent;
    border-radius: var(--gov-border-radius);
    cursor: pointer;
    transition: all var(--gov-transition-base);
}

.gov-btn--primary {
    background-color: var(--gov-navy-800);
    color: var(--gov-white);
    border-color: var(--gov-navy-800);
}

.gov-btn--primary:hover {
    background-color: var(--gov-navy-900);
    border-color: var(--gov-navy-900);
}

.gov-btn--outline {
    background-color: transparent;
    color: var(--gov-navy-700);
    border-color: var(--gov-navy-400);
}

.gov-btn--outline:hover {
    background-color: var(--gov-navy-50);
    border-color: var(--gov-navy-700);
}

.gov-btn--white {
    background-color: var(--gov-white);
    color: var(--gov-navy-800);
    border-color: var(--gov-white);
}

.gov-btn--white:hover {
    background-color: var(--gov-navy-50);
}

.gov-btn--large {
    padding: var(--gov-space-md) var(--gov-space-2xl);
    font-size: 16px;
}

.gov-btn--xl {
    padding: var(--gov-space-lg) var(--gov-space-3xl);
    font-size: 17px;
    min-height: 56px;
}

.gov-btn svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

/* ==========================================================================
   Trust Bar
   ========================================================================== */
.gov-trust-bar {
    background-color: var(--gov-navy-800);
    padding: var(--gov-space-md) 0;
    border-top: 1px solid var(--gov-navy-600);
}

.gov-trust-bar__inner {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: var(--gov-space-xl);
}

.gov-trust-bar__item {
    display: flex;
    align-items: center;
    gap: var(--gov-space-sm);
    color: var(--gov-navy-200);
    font-size: 13px;
    font-weight: 500;
}

.gov-trust-bar__item svg {
    width: 20px;
    height: 20px;
    color: var(--gov-gold);
}

/* ==========================================================================
   Section Base
   ========================================================================== */
.gov-section {
    padding: var(--gov-space-4xl) 0;
}

.gov-section--alt {
    background-color: var(--gov-navy-50);
}

.gov-section__header {
    display: flex;
    align-items: flex-start;
    gap: var(--gov-space-lg);
    margin-bottom: var(--gov-space-3xl);
}

.gov-section__header-line {
    width: 4px;
    min-height: 80px;
    background: linear-gradient(180deg, var(--gov-navy-800) 0%, var(--gov-navy-400) 100%);
    border-radius: 2px;
    flex-shrink: 0;
}

.gov-section__header-content {
    flex: 1;
}

.gov-section__number {
    display: inline-block;
    font-family: var(--gov-font-mono);
    font-size: 12px;
    font-weight: 700;
    color: var(--gov-navy-500);
    letter-spacing: 0.1em;
    margin-bottom: var(--gov-space-sm);
}

.gov-section__title {
    font-family: var(--gov-font-serif);
    font-size: clamp(24px, 3.5vw, 32px);
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0 0 var(--gov-space-sm) 0;
    line-height: 1.3;
}

.gov-section__subtitle {
    font-size: 14px;
    color: var(--gov-gray-600);
    margin: 0;
}

/* ==========================================================================
   Flow Section
   ========================================================================== */
.gov-flow {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--gov-space-xl);
}

.gov-flow__item {
    display: flex;
    gap: var(--gov-space-lg);
    background-color: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-border-radius-lg);
    overflow: hidden;
    transition: all var(--gov-transition-base);
}

.gov-flow__item:hover {
    border-color: var(--gov-navy-400);
    box-shadow: var(--gov-shadow-md);
}

.gov-flow__image-wrapper {
    flex: 0 0 40%;
    overflow: hidden;
}

.gov-flow__image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--gov-transition-slow);
}

.gov-flow__item:hover .gov-flow__image-wrapper img {
    transform: scale(1.05);
}

.gov-flow__content {
    flex: 1;
    padding: var(--gov-space-lg);
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.gov-flow__step-indicator {
    display: flex;
    align-items: baseline;
    gap: var(--gov-space-xs);
    margin-bottom: var(--gov-space-md);
}

.gov-flow__step-number {
    font-family: var(--gov-font-mono);
    font-size: 10px;
    font-weight: 700;
    color: var(--gov-gray-500);
    letter-spacing: 0.1em;
}

.gov-flow__step-num {
    font-family: var(--gov-font-mono);
    font-size: 24px;
    font-weight: 700;
    color: var(--gov-navy-800);
}

.gov-flow__title {
    font-family: var(--gov-font-serif);
    font-size: 17px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0 0 var(--gov-space-sm) 0;
}

.gov-flow__description {
    font-size: 13px;
    color: var(--gov-gray-600);
    line-height: 1.7;
    margin: 0;
}

.gov-flow__cta {
    margin-top: var(--gov-space-3xl);
    text-align: center;
}

.gov-flow__cta-note {
    margin-top: var(--gov-space-md);
    font-size: 13px;
    color: var(--gov-gray-600);
}

/* ==========================================================================
   Diagnosis Card
   ========================================================================== */
.gov-diagnosis-section {
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-900) 100%);
    padding: var(--gov-space-4xl) 0;
}

.gov-diagnosis-card {
    background-color: var(--gov-white);
    border-radius: var(--gov-border-radius-lg);
    box-shadow: var(--gov-shadow-xl);
    overflow: hidden;
    max-width: 700px;
    margin: 0 auto;
}

.gov-diagnosis-card__header {
    display: flex;
    align-items: center;
    gap: var(--gov-space-lg);
    padding: var(--gov-space-xl);
    background: linear-gradient(135deg, var(--gov-navy-50) 0%, var(--gov-white) 100%);
    border-bottom: 1px solid var(--gov-gray-200);
}

.gov-diagnosis-card__icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid var(--gov-navy-300);
    flex-shrink: 0;
}

.gov-diagnosis-card__icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gov-diagnosis-card__title-area {
    flex: 1;
}

.gov-diagnosis-card__label {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    color: var(--gov-navy-600);
    background-color: var(--gov-navy-100);
    padding: 2px 10px;
    border-radius: 12px;
    margin-bottom: var(--gov-space-xs);
}

.gov-diagnosis-card__title {
    font-family: var(--gov-font-serif);
    font-size: 22px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0;
}

.gov-diagnosis-card__body {
    padding: var(--gov-space-xl);
}

.gov-diagnosis-card__description {
    font-size: 15px;
    line-height: 1.8;
    color: var(--gov-gray-700);
    margin: 0 0 var(--gov-space-xl) 0;
    text-align: center;
}

.gov-diagnosis-card__features {
    list-style: none;
    padding: 0;
    margin: 0 0 var(--gov-space-xl) 0;
}

.gov-diagnosis-card__features li {
    display: flex;
    align-items: center;
    gap: var(--gov-space-md);
    padding: var(--gov-space-md) 0;
    border-bottom: 1px solid var(--gov-gray-100);
    font-size: 14px;
    color: var(--gov-gray-700);
}

.gov-diagnosis-card__features li:last-child {
    border-bottom: none;
}

.gov-diagnosis-card__features svg {
    flex-shrink: 0;
    color: var(--gov-green);
}

.gov-diagnosis-card__action {
    text-align: center;
}

.gov-diagnosis-card__footer {
    padding: var(--gov-space-lg) var(--gov-space-xl);
    background-color: var(--gov-gray-50);
    border-top: 1px solid var(--gov-gray-200);
    text-align: center;
}

.gov-terms-btn {
    display: inline-flex;
    align-items: center;
    gap: var(--gov-space-sm);
    background: transparent;
    border: 1px solid var(--gov-gray-300);
    padding: var(--gov-space-sm) var(--gov-space-md);
    font-size: 13px;
    font-family: var(--gov-font-sans);
    color: var(--gov-gray-600);
    border-radius: var(--gov-border-radius);
    cursor: pointer;
    transition: all var(--gov-transition-fast);
}

.gov-terms-btn:hover {
    border-color: var(--gov-navy-400);
    color: var(--gov-navy-700);
}

.gov-diagnosis-card__terms-note {
    font-size: 11px;
    color: var(--gov-gray-500);
    margin: var(--gov-space-sm) 0 0 0;
}

/* ==========================================================================
   Features Section
   ========================================================================== */
.gov-features {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--gov-space-lg);
}

.gov-feature-card {
    display: flex;
    gap: var(--gov-space-lg);
    padding: var(--gov-space-xl);
    background-color: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-border-radius-lg);
    transition: all var(--gov-transition-base);
}

.gov-feature-card:hover {
    border-color: var(--gov-navy-400);
    box-shadow: var(--gov-shadow-md);
}

.gov-feature-card__icon {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--gov-navy-50);
    border: 1px solid var(--gov-navy-200);
    border-radius: var(--gov-border-radius);
    flex-shrink: 0;
}

.gov-feature-card__icon svg {
    color: var(--gov-navy-700);
}

.gov-feature-card__content {
    flex: 1;
}

.gov-feature-card__title {
    font-family: var(--gov-font-serif);
    font-size: 16px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0 0 var(--gov-space-sm) 0;
}

.gov-feature-card__description {
    font-size: 13px;
    color: var(--gov-gray-600);
    line-height: 1.7;
    margin: 0;
}

/* ==========================================================================
   Benefits Section
   ========================================================================== */
.gov-benefits {
    display: grid;
    grid-template-columns: 0.9fr 1.1fr;
    gap: var(--gov-space-3xl);
    align-items: start;
}

.gov-benefits__image {
    border-radius: var(--gov-border-radius-lg);
    overflow: hidden;
    box-shadow: var(--gov-shadow-lg);
}

.gov-benefits__image img {
    display: block;
    width: 100%;
    height: auto;
}

.gov-benefits__list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gov-benefits__item {
    display: flex;
    gap: var(--gov-space-md);
    padding: var(--gov-space-lg) 0;
    border-bottom: 1px solid var(--gov-gray-200);
}

.gov-benefits__item:first-child {
    padding-top: 0;
}

.gov-benefits__item:last-child {
    border-bottom: none;
}

.gov-benefits__item-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--gov-green-light);
    border-radius: 50%;
    flex-shrink: 0;
}

.gov-benefits__item-icon svg {
    width: 18px;
    height: 18px;
    color: var(--gov-green);
}

.gov-benefits__item-content {
    flex: 1;
}

.gov-benefits__item-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0 0 var(--gov-space-xs) 0;
}

.gov-benefits__item-text {
    font-size: 13px;
    color: var(--gov-gray-600);
    line-height: 1.6;
    margin: 0;
}

.gov-benefits__cta {
    margin-top: var(--gov-space-3xl);
    text-align: center;
}

/* ==========================================================================
   Target Section
   ========================================================================== */
.gov-targets {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--gov-space-lg);
}

.gov-target-card {
    background-color: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-border-radius-lg);
    padding: var(--gov-space-xl);
    transition: all var(--gov-transition-base);
}

.gov-target-card:hover {
    border-color: var(--gov-navy-400);
    box-shadow: var(--gov-shadow-md);
}

.gov-target-card__header {
    display: flex;
    align-items: center;
    gap: var(--gov-space-md);
    margin-bottom: var(--gov-space-md);
}

.gov-target-card__category {
    font-size: 10px;
    font-weight: 700;
    color: var(--gov-white);
    background-color: var(--gov-navy-600);
    padding: 3px 10px;
    border-radius: 12px;
    flex-shrink: 0;
}

.gov-target-card__title {
    font-size: 15px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0;
    flex: 1;
}

.gov-target-card__description {
    font-size: 13px;
    color: var(--gov-gray-600);
    line-height: 1.6;
    margin: 0;
}

.gov-targets__cta {
    margin-top: var(--gov-space-3xl);
    text-align: center;
    background-color: var(--gov-white);
    padding: var(--gov-space-2xl);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-border-radius-lg);
}

.gov-targets__cta-lead {
    font-family: var(--gov-font-serif);
    font-size: 18px;
    font-weight: 600;
    color: var(--gov-navy-800);
    margin: 0 0 var(--gov-space-lg) 0;
}

/* ==========================================================================
   About Section
   ========================================================================== */
.gov-about__content {
    max-width: 800px;
    margin: 0 auto;
}

.gov-about__block {
    margin-bottom: var(--gov-space-2xl);
}

.gov-about__block:last-child {
    margin-bottom: 0;
}

.gov-about__subtitle {
    font-family: var(--gov-font-serif);
    font-size: 18px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0 0 var(--gov-space-md) 0;
    padding-bottom: var(--gov-space-sm);
    border-bottom: 2px solid var(--gov-navy-200);
}

.gov-about__text {
    font-size: 15px;
    line-height: 1.9;
    color: var(--gov-gray-700);
    margin: 0 0 var(--gov-space-md) 0;
}

.gov-about__text:last-child {
    margin-bottom: 0;
}

.gov-about__tech-box {
    background-color: var(--gov-navy-50);
    border: 1px solid var(--gov-navy-200);
    border-radius: var(--gov-border-radius-lg);
    padding: var(--gov-space-xl);
    margin-top: var(--gov-space-xl);
}

.gov-about__tech-title {
    display: flex;
    align-items: center;
    gap: var(--gov-space-sm);
    font-size: 15px;
    font-weight: 700;
    color: var(--gov-navy-800);
    margin: 0 0 var(--gov-space-md) 0;
}

.gov-about__tech-title svg {
    color: var(--gov-navy-600);
}

.gov-about__tech-list {
    margin: 0;
    padding-left: var(--gov-space-lg);
}

.gov-about__tech-list li {
    font-size: 14px;
    line-height: 1.8;
    color: var(--gov-gray-700);
    margin-bottom: var(--gov-space-sm);
}

.gov-about__tech-list li:last-child {
    margin-bottom: 0;
}

/* ==========================================================================
   FAQ Section
   ========================================================================== */
.gov-faq {
    max-width: 800px;
    margin: 0 auto;
}

.gov-faq__item {
    background-color: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-border-radius-lg);
    margin-bottom: var(--gov-space-md);
    overflow: hidden;
}

.gov-faq__item:last-child {
    margin-bottom: 0;
}

.gov-faq__question {
    display: flex;
    align-items: flex-start;
    gap: var(--gov-space-md);
    font-size: 15px;
    font-weight: 700;
    color: var(--gov-navy-900);
    padding: var(--gov-space-lg);
    margin: 0;
    background-color: var(--gov-gray-50);
    border-bottom: 1px solid var(--gov-gray-200);
}

.gov-faq__q {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background-color: var(--gov-navy-800);
    color: var(--gov-white);
    font-family: var(--gov-font-mono);
    font-size: 14px;
    font-weight: 700;
    border-radius: var(--gov-border-radius);
    flex-shrink: 0;
}

.gov-faq__answer {
    display: flex;
    align-items: flex-start;
    gap: var(--gov-space-md);
    padding: var(--gov-space-lg);
}

.gov-faq__a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background-color: var(--gov-gold);
    color: var(--gov-white);
    font-family: var(--gov-font-mono);
    font-size: 14px;
    font-weight: 700;
    border-radius: var(--gov-border-radius);
    flex-shrink: 0;
}

.gov-faq__answer p {
    flex: 1;
    font-size: 14px;
    line-height: 1.8;
    color: var(--gov-gray-700);
    margin: 0;
}

/* ==========================================================================
   Final CTA Section
   ========================================================================== */
.gov-final-cta {
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-900) 100%);
    text-align: center;
}

.gov-final-cta__content {
    max-width: 600px;
    margin: 0 auto;
}

.gov-final-cta__badge {
    display: inline-flex;
    align-items: center;
    gap: var(--gov-space-sm);
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: var(--gov-space-sm) var(--gov-space-md);
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: var(--gov-white);
    margin-bottom: var(--gov-space-lg);
}

.gov-final-cta__badge svg {
    color: var(--gov-gold);
}

.gov-final-cta__title {
    font-family: var(--gov-font-serif);
    font-size: clamp(28px, 5vw, 40px);
    font-weight: 700;
    color: var(--gov-white);
    margin: 0 0 var(--gov-space-lg) 0;
    line-height: 1.3;
}

.gov-final-cta__text {
    font-size: 15px;
    color: var(--gov-navy-200);
    margin: 0 0 var(--gov-space-xl) 0;
    line-height: 1.8;
}

.gov-final-cta__trust {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: var(--gov-space-lg);
    margin-top: var(--gov-space-xl);
}

.gov-final-cta__trust-item {
    display: flex;
    align-items: center;
    gap: var(--gov-space-sm);
    font-size: 12px;
    color: var(--gov-navy-300);
}

.gov-final-cta__trust-item svg {
    color: var(--gov-gold);
}

/* ==========================================================================
   Floating CTA
   ========================================================================== */
.gov-floating-cta {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    display: none;
}

.gov-floating-cta .gov-btn {
    box-shadow: var(--gov-shadow-xl);
}

@media (max-width: 768px) {
    .gov-floating-cta {
        display: block;
        bottom: 16px;
        left: 16px;
        right: 16px;
        transform: none;
    }
    
    .gov-floating-cta .gov-btn {
        width: 100%;
    }
}

/* ==========================================================================
   Modal Styles
   ========================================================================== */
.gov-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all var(--gov-transition-base);
}

.gov-modal[aria-hidden="false"] {
    opacity: 1;
    visibility: visible;
}

.gov-modal__overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(13, 27, 42, 0.7);
    cursor: pointer;
}

.gov-modal__container {
    position: relative;
    width: 90%;
    max-width: 700px;
    max-height: 85vh;
    background-color: var(--gov-white);
    border-radius: var(--gov-border-radius-lg);
    display: flex;
    flex-direction: column;
    box-shadow: var(--gov-shadow-xl);
}

.gov-modal__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--gov-space-lg) var(--gov-space-xl);
    border-bottom: 1px solid var(--gov-gray-200);
    background-color: var(--gov-gray-50);
    border-radius: var(--gov-border-radius-lg) var(--gov-border-radius-lg) 0 0;
    flex-shrink: 0;
}

.gov-modal__title {
    display: flex;
    align-items: center;
    gap: var(--gov-space-sm);
    font-family: var(--gov-font-serif);
    font-size: 18px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0;
}

.gov-modal__title svg {
    color: var(--gov-navy-600);
}

.gov-modal__close {
    width: 40px;
    height: 40px;
    background-color: transparent;
    border: 1px solid var(--gov-gray-300);
    border-radius: var(--gov-border-radius);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gov-gray-600);
    transition: all var(--gov-transition-fast);
}

.gov-modal__close:hover {
    border-color: var(--gov-navy-400);
    color: var(--gov-navy-700);
}

.gov-modal__body {
    flex: 1;
    overflow-y: auto;
    padding: var(--gov-space-xl);
}

.gov-modal__footer {
    padding: var(--gov-space-lg) var(--gov-space-xl);
    border-top: 1px solid var(--gov-gray-200);
    text-align: center;
    flex-shrink: 0;
}

/* Legal Content */
.gov-legal-content {
    font-size: 13px;
    line-height: 1.7;
}

.gov-legal-block {
    margin-bottom: var(--gov-space-xl);
}

.gov-legal-block:last-child {
    margin-bottom: 0;
}

.gov-legal-subtitle {
    font-size: 14px;
    font-weight: 700;
    color: var(--gov-navy-800);
    margin: 0 0 var(--gov-space-md) 0;
    padding-bottom: var(--gov-space-sm);
    border-bottom: 1px solid var(--gov-gray-200);
}

.gov-legal-list {
    margin: 0;
    padding-left: var(--gov-space-lg);
}

.gov-legal-list li {
    color: var(--gov-gray-700);
    margin-bottom: var(--gov-space-sm);
}

.gov-legal-list li:last-child {
    margin-bottom: 0;
}

/* ==========================================================================
   Responsive Styles
   ========================================================================== */
@media (max-width: 1024px) {
    .gov-hero__grid {
        grid-template-columns: 1fr;
        gap: var(--gov-space-2xl);
    }
    
    .gov-hero__content {
        order: 2;
    }
    
    .gov-hero__visual {
        order: 1;
    }
    
    .gov-flow {
        grid-template-columns: 1fr;
    }
    
    .gov-features {
        grid-template-columns: 1fr;
    }
    
    .gov-benefits {
        grid-template-columns: 1fr;
    }
    
    .gov-benefits__image {
        max-width: 500px;
        margin: 0 auto;
    }
    
    .gov-targets {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    :root {
        --gov-container-padding: 16px;
    }
    
    .gov-section {
        padding: var(--gov-space-2xl) 0;
    }
    
    /* Top Banner */
    .gov-top-banner__inner {
        flex-direction: column;
        gap: var(--gov-space-xs);
        text-align: center;
    }
    
    /* Hero */
    .gov-hero {
        padding: var(--gov-space-2xl) 0;
    }
    
    .gov-hero__title {
        text-align: center;
    }
    
    .gov-hero__lead {
        text-align: center;
    }
    
    .gov-hero__stats {
        flex-direction: column;
        gap: var(--gov-space-md);
    }
    
    .gov-hero__stat {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--gov-space-md);
    }
    
    .gov-hero__stat-label {
        display: inline;
        margin-top: 0;
        margin-left: var(--gov-space-sm);
    }
    
    .gov-hero__actions {
        flex-direction: column;
    }
    
    .gov-hero__actions .gov-btn {
        width: 100%;
    }
    
    /* Trust Bar */
    .gov-trust-bar__inner {
        gap: var(--gov-space-md);
    }
    
    .gov-trust-bar__item {
        font-size: 11px;
    }
    
    /* Section Header */
    .gov-section__header {
        flex-direction: column;
        gap: var(--gov-space-md);
    }
    
    .gov-section__header-line {
        width: 100%;
        min-height: auto;
        height: 4px;
    }
    
    /* Flow */
    .gov-flow__item {
        flex-direction: column;
    }
    
    .gov-flow__image-wrapper {
        flex: none;
        height: 180px;
    }
    
    .gov-flow__content {
        padding: var(--gov-space-lg);
    }
    
    /* Features */
    .gov-feature-card {
        flex-direction: column;
        text-align: center;
    }
    
    .gov-feature-card__icon {
        margin: 0 auto;
    }
    
    /* Targets */
    .gov-targets {
        grid-template-columns: 1fr;
    }
    
    .gov-target-card__header {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--gov-space-sm);
    }
    
    /* Diagnosis Card */
    .gov-diagnosis-card__header {
        flex-direction: column;
        text-align: center;
    }
    
    .gov-diagnosis-card__icon {
        margin: 0 auto;
    }
    
    /* FAQ */
    .gov-faq__question,
    .gov-faq__answer {
        padding: var(--gov-space-md);
    }
    
    /* Modal */
    .gov-modal__container {
        width: 95%;
        max-height: 90vh;
    }
    
    .gov-modal__header {
        padding: var(--gov-space-md) var(--gov-space-lg);
    }
    
    .gov-modal__body {
        padding: var(--gov-space-lg);
    }
}

@media (max-width: 480px) {
    .gov-hero__stat-number {
        font-size: 24px;
    }
    
    .gov-btn--xl {
        padding: var(--gov-space-md) var(--gov-space-xl);
        font-size: 15px;
    }
    
    .gov-faq__q,
    .gov-faq__a {
        width: 24px;
        height: 24px;
        font-size: 12px;
    }
}

/* ==========================================================================
   Chat Popup Styles (チャットポップアップ)
   ========================================================================== */
.diag-chat-popup {
    position: fixed;
    inset: 0;
    z-index: 10001;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all var(--gov-transition-base);
}

.diag-chat-popup[aria-hidden="false"] {
    opacity: 1;
    visibility: visible;
}

.diag-chat-popup__overlay {
    position: absolute;
    inset: 0;
    background: rgba(13, 27, 42, 0.7);
    cursor: pointer;
}

.diag-chat-popup__container {
    position: relative;
    width: 95%;
    max-width: 800px;
    max-height: 90vh;
    background: var(--gov-white);
    display: flex;
    flex-direction: column;
    border-radius: var(--gov-border-radius-lg);
    box-shadow: var(--gov-shadow-xl);
    overflow: hidden;
}

.diag-chat-popup__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--gov-space-md) var(--gov-space-lg);
    border-bottom: 1px solid var(--gov-gray-200);
    background: var(--gov-gray-50);
    flex-shrink: 0;
}

.diag-chat-popup__profile {
    display: flex;
    align-items: center;
    gap: var(--gov-space-md);
}

.diag-chat-popup__avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid var(--gov-navy-300);
    flex-shrink: 0;
}

.diag-chat-popup__avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.diag-chat-popup__info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.diag-chat-popup__name {
    font-size: 15px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0;
}

.diag-chat-popup__status {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--gov-gray-600);
    margin: 0;
}

.diag-status-dot {
    width: 6px;
    height: 6px;
    background-color: #22c55e;
    border-radius: 50%;
    animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.diag-chat-popup__close {
    width: 40px;
    height: 40px;
    background: transparent;
    border: 1px solid var(--gov-gray-300);
    border-radius: var(--gov-border-radius);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gov-gray-600);
    transition: all var(--gov-transition-fast);
}

.diag-chat-popup__close:hover {
    border-color: var(--gov-navy-400);
    color: var(--gov-navy-700);
}

.diag-chat-popup__body {
    flex: 1;
    overflow-y: auto;
    padding: var(--gov-space-xl);
    min-height: 300px;
    max-height: 60vh;
    background: var(--gov-white);
    -webkit-overflow-scrolling: touch;
}

.diag-chat-popup__footer {
    padding: var(--gov-space-md) var(--gov-space-lg);
    border-top: 1px solid var(--gov-gray-200);
    background: var(--gov-white);
    flex-shrink: 0;
}

.diag-chat-popup__input-wrap {
    display: flex;
    gap: var(--gov-space-sm);
    align-items: center;
    background: var(--gov-gray-100);
    border: 1px solid var(--gov-gray-300);
    border-radius: var(--gov-border-radius-lg);
    padding: 6px 8px 6px 16px;
}

.diag-chat-popup__input {
    flex: 1;
    padding: 10px 0;
    border: none;
    font-size: 16px;
    font-family: var(--gov-font-sans);
    background: transparent;
    min-width: 0;
}

.diag-chat-popup__input:focus {
    outline: none;
}

.diag-chat-popup__input::placeholder {
    color: var(--gov-gray-500);
}

.diag-chat-popup__send {
    width: 40px;
    height: 40px;
    min-width: 40px;
    background: var(--gov-navy-800);
    color: var(--gov-white);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all var(--gov-transition-fast);
}

.diag-chat-popup__send:hover {
    background: var(--gov-navy-900);
}

.diag-chat-popup__send:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* ポップアップ内メッセージ */
.diag-popup-message {
    margin-bottom: var(--gov-space-md);
}

.diag-popup-message--bot {
    background: var(--gov-navy-50);
    padding: var(--gov-space-lg);
    border-radius: var(--gov-border-radius-lg);
    border-left: 3px solid var(--gov-navy-400);
}

.diag-popup-message--user {
    display: flex;
    justify-content: flex-end;
}

.diag-popup-message--user .diag-popup-bubble {
    background: var(--gov-navy-100);
    padding: var(--gov-space-md) var(--gov-space-lg);
    border-radius: var(--gov-border-radius-lg);
    max-width: 80%;
}

.diag-popup-bubble {
    font-size: 14px;
    line-height: 1.8;
    white-space: pre-wrap;
    word-break: break-word;
    color: var(--gov-gray-800);
}

/* ポップアップ内選択肢 */
.diag-popup-options {
    display: flex;
    flex-direction: column;
    gap: var(--gov-space-sm);
    margin-top: var(--gov-space-lg);
}

.diag-popup-option {
    width: 100%;
    padding: var(--gov-space-md) var(--gov-space-lg);
    min-height: 48px;
    font-size: 14px;
    font-weight: 500;
    font-family: var(--gov-font-sans);
    border: 1px solid var(--gov-gray-300);
    background: var(--gov-white);
    color: var(--gov-navy-800);
    cursor: pointer;
    text-align: left;
    display: flex;
    align-items: center;
    border-radius: var(--gov-border-radius);
    transition: all var(--gov-transition-fast);
}

.diag-popup-option:hover {
    background: var(--gov-navy-50);
    border-color: var(--gov-navy-400);
}

.diag-popup-option:active,
.diag-popup-option.selected {
    background: var(--gov-navy-800);
    color: var(--gov-white);
    border-color: var(--gov-navy-800);
}

.diag-popup-option:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
}

/* 都道府県セレクト */
.diag-popup-select {
    width: 100%;
    padding: var(--gov-space-md) 40px var(--gov-space-md) var(--gov-space-lg);
    font-size: 14px;
    font-family: var(--gov-font-sans);
    border: 1px solid var(--gov-gray-300);
    background: var(--gov-white);
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23495057' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    margin-top: var(--gov-space-md);
    border-radius: var(--gov-border-radius);
}

.diag-popup-select:focus {
    outline: none;
    border-color: var(--gov-navy-500);
}

.diag-popup-select:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background-color: var(--gov-gray-100);
}

/* ヒント */
.diag-popup-hint {
    margin-top: var(--gov-space-md);
    font-size: 12px;
    color: var(--gov-gray-500);
}

.diag-popup-input-hint {
    margin-top: var(--gov-space-sm);
    font-size: 11px;
    color: var(--gov-gray-400);
    text-align: center;
}

/* タイピングインジケーター */
.diag-popup-typing {
    display: flex;
    gap: 6px;
    padding: var(--gov-space-md) var(--gov-space-lg);
    background: var(--gov-navy-50);
    border-radius: var(--gov-border-radius-lg);
    margin-bottom: var(--gov-space-md);
}

.diag-popup-typing-dot {
    width: 8px;
    height: 8px;
    background: var(--gov-gray-400);
    border-radius: 50%;
    animation: diagTyping 1.2s infinite;
}

.diag-popup-typing-dot:nth-child(2) { animation-delay: 0.15s; }
.diag-popup-typing-dot:nth-child(3) { animation-delay: 0.3s; }

@keyframes diagTyping {
    0%, 60%, 100% { transform: scale(1); opacity: 0.4; }
    30% { transform: scale(1.2); opacity: 1; }
}

/* 結果カード */
.diag-popup-results {
    margin-top: var(--gov-space-lg);
}

.diag-results-summary {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #86efac;
    border-radius: var(--gov-border-radius-lg);
    padding: var(--gov-space-lg);
    margin-bottom: var(--gov-space-lg);
}

.diag-results-summary-header {
    display: flex;
    align-items: center;
    gap: var(--gov-space-md);
    margin-bottom: var(--gov-space-md);
}

.diag-results-summary-icon {
    width: 40px;
    height: 40px;
    background: #22c55e;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.diag-results-summary-icon svg {
    width: 20px;
    height: 20px;
    color: white;
}

.diag-results-summary-title {
    font-size: 16px;
    font-weight: 700;
    color: #166534;
    margin: 0;
}

.diag-results-summary-count {
    font-size: 13px;
    color: #15803d;
    margin-top: 2px;
}

.diag-results-section-title {
    font-family: var(--gov-font-serif);
    font-size: 14px;
    font-weight: 700;
    color: var(--gov-navy-800);
    margin-bottom: var(--gov-space-md);
    padding-bottom: var(--gov-space-sm);
    border-bottom: 2px solid var(--gov-navy-800);
}

/* 結果カード続き */
.diag-popup-result-card {
    border: 1px solid var(--gov-gray-200);
    padding: var(--gov-space-lg);
    margin-bottom: var(--gov-space-md);
    background: var(--gov-white);
    border-radius: var(--gov-border-radius-lg);
    transition: all var(--gov-transition-base);
}

.diag-popup-result-card:hover {
    border-color: var(--gov-navy-400);
    box-shadow: var(--gov-shadow-md);
}

.diag-popup-result-card--highlight {
    border-left: 3px solid var(--gov-navy-800);
}

.diag-popup-result-header {
    display: flex;
    align-items: flex-start;
    gap: var(--gov-space-md);
    margin-bottom: var(--gov-space-md);
}

.diag-popup-result-header-info {
    flex: 1;
}

.diag-popup-result-rank {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gov-gray-200);
    color: var(--gov-gray-700);
    font-size: 12px;
    font-weight: 700;
    flex-shrink: 0;
    border-radius: 50%;
}

.diag-popup-result-rank--1 {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
}

.diag-popup-result-rank--2 {
    background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
    color: white;
}

.diag-popup-result-rank--3 {
    background: linear-gradient(135deg, #cd7c2c 0%, #b45309 100%);
    color: white;
}

.diag-popup-result-title {
    font-family: var(--gov-font-serif);
    font-size: 15px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0;
    line-height: 1.4;
}

.diag-popup-result-score {
    display: inline-block;
    padding: 3px 10px;
    background: var(--gov-navy-800);
    color: white;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    margin-top: var(--gov-space-xs);
}

.diag-popup-result-meta {
    display: flex;
    flex-wrap: wrap;
    gap: var(--gov-space-md);
    font-size: 12px;
    color: var(--gov-gray-600);
    margin-bottom: var(--gov-space-md);
}

.diag-popup-result-amount {
    color: #2563eb;
    font-weight: 600;
}

.diag-popup-result-deadline {
    color: #dc2626;
}

.diag-popup-result-reason {
    font-size: 13px;
    color: var(--gov-navy-700);
    padding: var(--gov-space-md);
    background: var(--gov-navy-50);
    border-left: 3px solid var(--gov-navy-400);
    margin-bottom: var(--gov-space-md);
    border-radius: 0 var(--gov-border-radius) var(--gov-border-radius) 0;
}

.diag-popup-result-actions {
    display: flex;
    flex-direction: column;
    gap: var(--gov-space-sm);
}

.diag-popup-result-btn {
    width: 100%;
    padding: var(--gov-space-md) var(--gov-space-lg);
    font-size: 13px;
    font-weight: 600;
    font-family: var(--gov-font-sans);
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    display: block;
    border-radius: var(--gov-border-radius);
    transition: all var(--gov-transition-fast);
    border: none;
}

.diag-popup-result-btn--primary {
    background: var(--gov-navy-800);
    color: var(--gov-white);
}

.diag-popup-result-btn--primary:hover {
    background: var(--gov-navy-900);
}

.diag-popup-result-btn--secondary {
    background: var(--gov-white);
    color: var(--gov-gray-700);
    border: 1px solid var(--gov-gray-300);
}

.diag-popup-result-btn--secondary:hover {
    background: var(--gov-gray-100);
    border-color: var(--gov-navy-400);
}

/* 個別フィードバック */
.diag-result-feedback {
    display: flex;
    align-items: center;
    gap: var(--gov-space-sm);
    margin-top: var(--gov-space-md);
    padding-top: var(--gov-space-md);
    border-top: 1px solid var(--gov-gray-100);
}

.diag-result-feedback-label {
    font-size: 12px;
    color: var(--gov-gray-500);
}

.diag-feedback-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: var(--gov-gray-100);
    border: 1px solid transparent;
    border-radius: var(--gov-border-radius);
    color: var(--gov-gray-500);
    cursor: pointer;
    transition: all var(--gov-transition-fast);
}

.diag-feedback-btn:hover {
    background: var(--gov-gray-200);
    color: var(--gov-gray-700);
}

.diag-feedback-btn.selected[data-feedback="positive"] {
    background: #dcfce7;
    border-color: #22c55e;
    color: #166534;
}

.diag-feedback-btn.selected[data-feedback="negative"] {
    background: #fee2e2;
    border-color: #ef4444;
    color: #dc2626;
}

/* 再調整パネル */
.diag-readjust-panel {
    margin-top: var(--gov-space-lg);
    padding: var(--gov-space-lg);
    background: var(--gov-gray-50);
    border-radius: var(--gov-border-radius-lg);
}

.diag-readjust-header {
    display: flex;
    align-items: center;
    gap: var(--gov-space-sm);
    font-size: 13px;
    font-weight: 600;
    color: var(--gov-gray-700);
    margin-bottom: var(--gov-space-md);
}

.diag-readjust-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--gov-space-sm);
}

.diag-readjust-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: var(--gov-space-md);
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-border-radius);
    font-size: 12px;
    color: var(--gov-gray-700);
    cursor: pointer;
    transition: all var(--gov-transition-fast);
}

.diag-readjust-btn:hover {
    border-color: var(--gov-navy-400);
    background: var(--gov-navy-50);
}

/* フィードバックバー */
.diag-results-feedback-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--gov-space-md);
    padding: var(--gov-space-md) var(--gov-space-lg);
    background: var(--gov-gray-50);
    border-radius: var(--gov-border-radius-lg);
    margin-bottom: var(--gov-space-lg);
}

.diag-results-feedback-text {
    font-size: 13px;
    color: var(--gov-gray-600);
}

.diag-results-feedback-btns {
    display: flex;
    gap: var(--gov-space-sm);
}

.diag-results-fb-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: var(--gov-space-sm) var(--gov-space-md);
    background: white;
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-border-radius);
    font-size: 12px;
    color: var(--gov-gray-600);
    cursor: pointer;
    transition: all var(--gov-transition-fast);
}

.diag-results-fb-btn:hover {
    background: var(--gov-gray-100);
}

.diag-results-fb-btn.selected.positive {
    background: #dcfce7;
    border-color: #22c55e;
    color: #166534;
}

.diag-results-fb-btn.selected.negative {
    background: #fee2e2;
    border-color: #ef4444;
    color: #dc2626;
}

/* サマリー情報 */
.diag-results-summary-info {
    display: flex;
    justify-content: space-around;
    gap: var(--gov-space-sm);
    padding-top: var(--gov-space-md);
    border-top: 1px solid #86efac;
}

.diag-summary-stat {
    text-align: center;
}

.diag-summary-stat-value {
    font-size: 18px;
    font-weight: 700;
    color: #166534;
}

.diag-summary-stat-label {
    font-size: 11px;
    color: #15803d;
}

/* チャットポップアップ モバイル対応 */
@media (max-width: 768px) {
    .diag-chat-popup__container {
        width: 100%;
        max-width: none;
        max-height: 100vh;
        height: 100%;
        border-radius: 0;
    }
    
    .diag-chat-popup__body {
        max-height: none;
        flex: 1;
        padding: var(--gov-space-md);
    }
    
    .diag-chat-popup__footer {
        padding: var(--gov-space-md);
        padding-bottom: calc(var(--gov-space-md) + env(safe-area-inset-bottom, 0px));
    }
    
    .diag-popup-result-card {
        padding: var(--gov-space-md);
    }
    
    .diag-readjust-options {
        grid-template-columns: 1fr;
    }
    
    .diag-results-feedback-bar {
        flex-direction: column;
        text-align: center;
        gap: var(--gov-space-sm);
    }
}

/* ==========================================================================
   GIP Chat Plugin Overrides (官公庁デザイン統合)
   ========================================================================== */
.gov-wrapper .gip-chat {
    border: 1px solid var(--gov-gray-200) !important;
    border-radius: var(--gov-border-radius-lg) !important;
    box-shadow: var(--gov-shadow-md) !important;
    background: var(--gov-white) !important;
}

.gov-wrapper .gip-chat-header {
    background: var(--gov-navy-800) !important;
    border-radius: var(--gov-border-radius-lg) var(--gov-border-radius-lg) 0 0 !important;
}

.gov-wrapper .gip-message-bot .gip-message-bubble {
    background: var(--gov-navy-50) !important;
    border: none !important;
    border-left: 3px solid var(--gov-navy-400) !important;
    border-radius: 0 var(--gov-border-radius-lg) var(--gov-border-radius-lg) 0 !important;
    color: var(--gov-gray-800) !important;
}

.gov-wrapper .gip-message-user .gip-message-bubble {
    background: var(--gov-navy-100) !important;
    color: var(--gov-navy-900) !important;
    border-radius: var(--gov-border-radius-lg) !important;
}

.gov-wrapper .gip-option-btn {
    border: 1px solid var(--gov-gray-300) !important;
    border-radius: var(--gov-border-radius) !important;
    background: var(--gov-white) !important;
    color: var(--gov-navy-800) !important;
    font-family: var(--gov-font-sans) !important;
    min-height: 48px !important;
}

.gov-wrapper .gip-option-btn:hover {
    background: var(--gov-navy-50) !important;
    border-color: var(--gov-navy-400) !important;
}

.gov-wrapper .gip-option-btn.selected {
    background: var(--gov-navy-800) !important;
    color: var(--gov-white) !important;
    border-color: var(--gov-navy-800) !important;
}

.gov-wrapper .gip-results {
    border: 1px solid var(--gov-gray-200) !important;
    border-radius: var(--gov-border-radius-lg) !important;
    overflow: hidden !important;
}

.gov-wrapper .gip-results-header {
    background: var(--gov-navy-50) !important;
    border-bottom: 1px solid var(--gov-gray-200) !important;
}

.gov-wrapper .gip-result-card {
    border: 1px solid var(--gov-gray-200) !important;
    border-radius: var(--gov-border-radius-lg) !important;
}

.gov-wrapper .gip-result-card:hover {
    border-color: var(--gov-navy-400) !important;
    box-shadow: var(--gov-shadow-md) !important;
}

.gov-wrapper .gip-result-rank {
    background: var(--gov-navy-800) !important;
    color: var(--gov-white) !important;
    border-radius: 50% !important;
}

.gov-wrapper .gip-result-btn-primary {
    background: var(--gov-navy-800) !important;
    color: var(--gov-white) !important;
    border-radius: var(--gov-border-radius) !important;
}

.gov-wrapper .gip-result-btn-primary:hover {
    background: var(--gov-navy-900) !important;
}

.gov-wrapper .gip-typing-indicator {
    background: var(--gov-navy-50) !important;
    border: none !important;
    border-radius: var(--gov-border-radius-lg) !important;
}

.gov-wrapper .gip-typing-dot {
    background: var(--gov-gray-400) !important;
}

/* ==========================================================================
   Print Styles
   ========================================================================== */
@media print {
    .gov-hero__actions,
    .gov-flow__cta,
    .gov-diagnosis-card,
    .gov-benefits__cta,
    .gov-targets__cta,
    .gov-final-cta,
    .gov-floating-cta,
    .gov-modal,
    .gov-trust-bar,
    .gov-top-banner {
        display: none !important;
    }
    
    .gov-wrapper {
        color: #000;
    }
    
    .gov-section {
        padding: 20px 0;
    }
    
    .gov-section--alt {
        background: #f5f5f5;
    }
}
</style>

<!-- ============================================
     SCRIPTS
     ============================================ -->
<script>
(function() {
    'use strict';
    
    // ===========================================
    // 利用規約モーダル
    // ===========================================
    
    const termsModal = document.getElementById('termsModal');
    const openTermsBtn = document.getElementById('openTermsModal');
    const closeTermsBtn = document.getElementById('closeTermsModal');
    
    function openTermsModal() {
        if (termsModal) {
            termsModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeTermsModalFn() {
        if (termsModal) {
            termsModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
    }
    
    if (openTermsBtn) {
        openTermsBtn.addEventListener('click', openTermsModal);
    }
    
    if (closeTermsBtn) {
        closeTermsBtn.addEventListener('click', closeTermsModalFn);
    }
    
    // オーバーレイクリックで閉じる
    if (termsModal) {
        termsModal.querySelectorAll('[data-close-modal]').forEach(el => {
            el.addEventListener('click', closeTermsModalFn);
        });
    }
    
    // ESCキーで利用規約モーダルを閉じる
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && termsModal && termsModal.getAttribute('aria-hidden') === 'false') {
            closeTermsModalFn();
        }
    });
    
    // ===========================================
    // スムーススクロール
    // ===========================================
    
    document.querySelectorAll('.smooth-scroll').forEach(function(link) {
        link.addEventListener('click', function(e) {
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
    
    // ===========================================
    // フローティングCTAの表示制御
    // ===========================================
    
    const floatingCta = document.querySelector('.gov-floating-cta');
    
    if (floatingCta) {
        let lastScrollY = window.scrollY;
        let ticking = false;
        
        function updateFloatingCta() {
            const scrollY = window.scrollY;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            
            // ページ下部（フッター付近）では非表示
            if (scrollY + windowHeight > documentHeight - 200) {
                floatingCta.style.opacity = '0';
                floatingCta.style.pointerEvents = 'none';
            } else if (scrollY > 300) {
                floatingCta.style.opacity = '1';
                floatingCta.style.pointerEvents = 'auto';
            } else {
                floatingCta.style.opacity = '0';
                floatingCta.style.pointerEvents = 'none';
            }
            
            lastScrollY = scrollY;
            ticking = false;
        }
        
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(updateFloatingCta);
                ticking = true;
            }
        });
        
        // 初期状態
        updateFloatingCta();
    }
    
    // ===========================================
    // アニメーション（Intersection Observer）
    // ===========================================
    
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
    
    // アニメーション対象要素
    const animateElements = document.querySelectorAll('.gov-flow__item, .gov-feature-card, .gov-target-card, .gov-benefits__item, .gov-faq__item');
    
    animateElements.forEach(function(el) {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });
    
    // visible状態のスタイル
    const style = document.createElement('style');
    style.textContent = `
        .gov-flow__item.is-visible,
        .gov-feature-card.is-visible,
        .gov-target-card.is-visible,
        .gov-benefits__item.is-visible,
        .gov-faq__item.is-visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
    `;
    document.head.appendChild(style);
    
})();
</script>

<?php get_footer(); ?>
