<?php
/**
 * Template Part: Front Page - Final CTA Section
 * フロントページ最終CTA（Call To Action）セクション - 官公庁デザイン
 *
 * @package Grant_Insight_Perfect
 * @version 12.0.0 - Government Official Design
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="gov-cta" aria-labelledby="gov-cta-title">
    <div class="gov-cta__container">
        
        <!-- 装飾ライン（上部） -->
        <div class="gov-cta__accent-line" aria-hidden="true"></div>
        
        <div class="gov-cta__content">
            
            <!-- アイコン -->
            <div class="gov-cta__icon" aria-hidden="true">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
            </div>
            
            <!-- サブタイトル -->
            <p class="gov-cta__subtitle">SUBSIDY DIAGNOSIS</p>
            
            <!-- メインタイトル -->
            <h2 id="gov-cta-title" class="gov-cta__title" itemprop="name">
                あなたに最適な補助金を<br class="gov-cta__br-sp">無料診断
            </h2>
            
            <!-- 説明文 -->
            <p class="gov-cta__description" itemprop="description">
                簡単な質問に答えるだけで、あなたの事業に最適な補助金・助成金を診断します。<br class="gov-cta__br-pc" aria-hidden="true">
                診断は完全無料、所要時間はわずか3分です。
            </p>
            
            <!-- 特徴リスト -->
            <ul class="gov-cta__features" role="list">
                <li class="gov-cta__feature">
                    <span class="gov-cta__feature-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </span>
                    <span>会員登録不要</span>
                </li>
                <li class="gov-cta__feature">
                    <span class="gov-cta__feature-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </span>
                    <span>メールアドレス不要</span>
                </li>
                <li class="gov-cta__feature">
                    <span class="gov-cta__feature-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </span>
                    <span>所要時間わずか3分</span>
                </li>
            </ul>
            
            <!-- ボタングループ -->
            <div class="gov-cta__buttons">
                <!-- プライマリボタン: 無料診断 -->
                <a href="https://joseikin-insight.com/subsidy-diagnosis/" 
                   class="gov-cta__btn gov-cta__btn--primary"
                   itemprop="url"
                   aria-label="今すぐ無料診断を始める">
                    <span class="gov-cta__btn-text">今すぐ無料診断を始める</span>
                    <span class="gov-cta__btn-icon" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </span>
                </a>

                <!-- セカンダリボタン: 補助金検索 -->
                <a href="<?php echo esc_url(home_url('/grants/')); ?>" 
                   class="gov-cta__btn gov-cta__btn--secondary"
                   aria-label="補助金一覧から探す">
                    <span class="gov-cta__btn-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </span>
                    <span class="gov-cta__btn-text">補助金を探す</span>
                </a>
            </div>

            <!-- 補足情報 -->
            <p class="gov-cta__note">
                <span class="gov-cta__note-icon" aria-hidden="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v6h2v-6h-2zm0-4v2h2V7h-2z"/>
                    </svg>
                </span>
                <span>本診断は公的機関の情報をもとに作成されています</span>
            </p>
            
        </div>
        
    </div>
</section>
