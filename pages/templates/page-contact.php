<?php
/**
 * 補助金・助成金情報サイト - お問い合わせページ（表示テンプレート部分）
 * Grant & Subsidy Information Site - Contact Page (Display Template Part)
 * @package Grant_Insight_Contact
 * @version 7.0-fixed-success-page
 * 
 * 注意: このファイルは表示のみを担当します。POST処理は functions.php の admin_post フックで行われます。
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// このファイルは page-contact.php から include されるため、get_header() は不要
// 親ファイルから変数を取得
global $form_submitted, $form_success, $form_errors;

// 変数が設定されていない場合のフォールバック
if (!isset($form_success)) {
    $form_success = isset($_GET['contact_sent']) && $_GET['contact_sent'] == '1';
}
if (!isset($form_errors)) {
    $form_errors = array();
    if (isset($_GET['contact_error']) && $_GET['contact_error'] == '1' && isset($_GET['error_msg'])) {
        $form_errors = explode('|', urldecode($_GET['error_msg']));
    }
}

// 構造化データ
$contact_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'ContactPage',
    'name' => 'お問い合わせ - 補助金インサイト',
    'description' => '補助金インサイトへのお問い合わせフォーム。サービスに関するご質問やご相談を承ります。',
    'url' => 'https://joseikin-insight.com/contact/'
);
?>

<!-- 構造化データ -->
<script type="application/ld+json">
<?php echo wp_json_encode($contact_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<style>
/* ==========================================================================
   Official Government Design - Contact Page CSS
   官公庁デザイン - お問い合わせページ
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

.contact-page {
    font-family: var(--gov-font-sans);
    color: var(--gov-gray-900);
    background: linear-gradient(180deg, var(--gov-navy-50) 0%, var(--gov-white) 100%);
    line-height: 1.7;
    min-height: 100vh;
}

/* 上部アクセントライン */
.contact-page::before {
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
   Page Header - 官公庁風ヘッダー
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
    margin: 0 0 20px;
    font-weight: 500;
}

.gov-page-description {
    font-size: 0.9375rem;
    color: var(--gov-navy-200);
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.8;
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

.gov-section-intro {
    color: var(--gov-gray-700);
    margin-bottom: 24px;
    font-size: 0.9375rem;
}

/* ==========================================================================
   Success Message - 送信完了画面
   ========================================================================== */
.gov-success-section {
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.gov-success-box {
    background: linear-gradient(135deg, var(--gov-green-light) 0%, #dcfce7 100%);
    border: 2px solid #86efac;
    border-radius: var(--gov-radius-lg);
    padding: 60px 48px;
    text-align: center;
    animation: successFadeIn 0.6s ease;
    max-width: 720px;
    margin: 0 auto;
    box-shadow: 0 10px 40px rgba(46, 125, 50, 0.15);
}

@keyframes successFadeIn {
    from { 
        opacity: 0; 
        transform: translateY(-30px) scale(0.98); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
    }
}

.gov-success-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100px;
    height: 100px;
    background: var(--gov-white);
    border: 4px solid var(--gov-green);
    border-radius: 50%;
    margin-bottom: 28px;
    color: var(--gov-green);
    animation: successPulse 2s infinite;
}

@keyframes successPulse {
    0%, 100% { 
        box-shadow: 0 0 0 0 rgba(46, 125, 50, 0.4); 
    }
    50% { 
        box-shadow: 0 0 0 15px rgba(46, 125, 50, 0); 
    }
}

.gov-success-title {
    font-family: var(--gov-font-serif);
    font-size: 2rem;
    font-weight: 600;
    color: #166534;
    margin: 0 0 8px;
}

.gov-success-subtitle {
    font-size: 0.9375rem;
    color: #22c55e;
    font-weight: 500;
    margin: 0 0 24px;
    letter-spacing: 0.05em;
}

.gov-success-text {
    font-size: 1rem;
    color: #15803d;
    line-height: 1.9;
    margin-bottom: 32px;
}

.gov-success-text p {
    margin: 8px 0;
}

.gov-success-text strong {
    color: #166534;
}

.gov-success-info {
    display: grid;
    gap: 16px;
    margin-bottom: 36px;
    text-align: left;
}

.gov-success-info-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    background: rgba(255, 255, 255, 0.7);
    padding: 16px 20px;
    border-radius: var(--gov-radius);
    border-left: 3px solid var(--gov-green);
}

.gov-success-info-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    background: var(--gov-white);
    border-radius: 50%;
    color: var(--gov-green);
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.gov-success-info-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.gov-success-info-content strong {
    font-size: 0.9375rem;
    color: #166534;
}

.gov-success-info-content span {
    font-size: 0.875rem;
    color: #15803d;
    line-height: 1.6;
}

.gov-success-actions {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
}

.gov-success-actions .gov-btn-primary,
.gov-success-actions .gov-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.gov-success-actions .gov-btn-primary {
    background: linear-gradient(135deg, var(--gov-green) 0%, #16a34a 100%);
    border-color: var(--gov-green);
}

.gov-success-actions .gov-btn-primary:hover {
    background: linear-gradient(135deg, #166534 0%, var(--gov-green) 100%);
}

/* ==========================================================================
   Error Message
   ========================================================================== */
.gov-error-box {
    background: var(--gov-red-light);
    border: 1px solid #fca5a5;
    border-left: 4px solid var(--gov-red);
    border-radius: var(--gov-radius);
    padding: 20px 24px;
    margin-bottom: 32px;
    animation: slideDown 0.5s ease;
}

.gov-error-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.gov-error-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    color: var(--gov-red);
}

.gov-error-title {
    font-size: 1rem;
    font-weight: 600;
    color: #991b1b;
    margin: 0;
}

.gov-error-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gov-error-list li {
    position: relative;
    padding-left: 20px;
    margin-bottom: 8px;
    color: #991b1b;
    font-size: 0.875rem;
}

.gov-error-list li::before {
    content: '•';
    position: absolute;
    left: 4px;
    color: var(--gov-red);
    font-weight: bold;
}

/* ==========================================================================
   FAQ Section
   ========================================================================== */
.gov-faq-grid {
    display: grid;
    gap: 16px;
}

.gov-faq-item {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-left: 4px solid var(--gov-navy-800);
    border-radius: var(--gov-radius);
    padding: 24px;
    transition: all var(--gov-transition);
}

.gov-faq-item:hover {
    border-left-color: var(--gov-gold);
    box-shadow: var(--gov-shadow);
    transform: translateX(4px);
}

.gov-faq-question {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 12px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.gov-faq-question::before {
    content: 'Q';
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    background: var(--gov-navy-800);
    color: var(--gov-gold);
    font-weight: 700;
    border-radius: var(--gov-radius);
    font-size: 0.875rem;
}

.gov-faq-answer {
    font-size: 0.9375rem;
    color: var(--gov-gray-700);
    line-height: 1.8;
    margin: 0;
    padding-left: 40px;
}

.gov-faq-answer::before {
    content: 'A. ';
    font-weight: 600;
    color: var(--gov-navy-600);
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

/* ==========================================================================
   Form Section
   ========================================================================== */
.gov-form-container {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    padding: 40px;
    box-shadow: var(--gov-shadow-sm);
}

.gov-contact-form {
    max-width: 800px;
    margin: 0 auto;
}

.gov-form-group {
    margin-bottom: 28px;
}

.gov-form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
}

.gov-form-col {
    margin-bottom: 0;
}

.gov-form-label {
    display: block;
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin-bottom: 8px;
}

.gov-form-label.required::after {
    content: '必須';
    display: inline-block;
    margin-left: 8px;
    padding: 2px 8px;
    background: var(--gov-red);
    color: var(--gov-white);
    font-size: 0.6875rem;
    font-weight: 700;
    border-radius: 2px;
}

.gov-optional-label {
    font-size: 0.8125rem;
    font-weight: 400;
    color: var(--gov-gray-600);
}

.gov-form-control {
    width: 100%;
    padding: 14px 16px;
    font-size: 1rem;
    color: var(--gov-gray-900);
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-300);
    border-radius: var(--gov-radius);
    transition: all var(--gov-transition);
    font-family: inherit;
}

.gov-form-control:focus {
    outline: none;
    border-color: var(--gov-navy-500);
    box-shadow: 0 0 0 3px rgba(65, 90, 119, 0.15);
}

.gov-form-control:hover {
    border-color: var(--gov-gray-400);
}

textarea.gov-form-control {
    resize: vertical;
    min-height: 160px;
}

.gov-form-help {
    margin-top: 6px;
    font-size: 0.8125rem;
    color: var(--gov-gray-600);
}

#char-count {
    font-weight: 600;
    color: var(--gov-navy-700);
}

/* Radio & Checkbox */
.gov-radio-group,
.gov-checkbox-group-inline {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.gov-radio-label,
.gov-checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    padding: 8px 16px;
    background: var(--gov-gray-100);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius);
    transition: all var(--gov-transition);
}

.gov-radio-label:hover,
.gov-checkbox-label:hover {
    background: var(--gov-navy-50);
    border-color: var(--gov-navy-300);
}

.gov-radio-label input,
.gov-checkbox-label input {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--gov-navy-700);
}

.gov-radio-text,
.gov-checkbox-text {
    font-size: 0.9375rem;
    color: var(--gov-gray-800);
}

/* Privacy Agreement */
.gov-checkbox-label-large {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    padding: 16px 20px;
    background: var(--gov-navy-50);
    border: 2px solid var(--gov-navy-200);
    border-radius: var(--gov-radius);
    transition: all var(--gov-transition);
}

.gov-checkbox-label-large:hover {
    border-color: var(--gov-navy-400);
    background: var(--gov-gold-pale);
}

.gov-checkbox-label-large input {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: var(--gov-navy-700);
}

/* Privacy Notice */
.gov-privacy-notice {
    background: var(--gov-gray-100);
    border-left: 4px solid var(--gov-navy-800);
    padding: 20px 24px;
    border-radius: var(--gov-radius);
    margin-bottom: 28px;
}

.gov-privacy-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.gov-privacy-notice p {
    color: var(--gov-gray-700);
    line-height: 1.7;
    margin: 8px 0;
    font-size: 0.875rem;
}

.gov-privacy-list {
    list-style: none;
    padding: 0;
    margin: 12px 0;
}

.gov-privacy-list li {
    position: relative;
    padding-left: 20px;
    margin-bottom: 8px;
    color: var(--gov-gray-700);
    font-size: 0.875rem;
}

.gov-privacy-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.6em;
    width: 6px;
    height: 6px;
    background: var(--gov-gold);
    border-radius: 50%;
}

/* Submit Button */
.gov-form-actions {
    text-align: center;
    margin-top: 40px;
}

.gov-btn-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 18px 60px;
    font-size: 1.0625rem;
    font-weight: 700;
    color: var(--gov-white);
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-700) 100%);
    border: none;
    border-radius: var(--gov-radius);
    cursor: pointer;
    transition: all var(--gov-transition);
    box-shadow: var(--gov-shadow);
}

.gov-btn-submit:hover:not(:disabled) {
    background: linear-gradient(135deg, var(--gov-navy-900) 0%, var(--gov-navy-800) 100%);
    transform: translateY(-2px);
    box-shadow: var(--gov-shadow-lg);
}

.gov-btn-submit:active:not(:disabled) {
    transform: translateY(0);
}

.gov-btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Form Note */
.gov-form-note {
    margin-top: 32px;
    padding: 20px 24px;
    background: var(--gov-gray-100);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius);
}

.gov-form-note .note-title {
    margin: 0 0 12px;
    color: var(--gov-navy-900);
    font-size: 0.9375rem;
}

.gov-form-note ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gov-form-note li {
    position: relative;
    padding-left: 16px;
    margin-bottom: 6px;
    color: var(--gov-gray-700);
    font-size: 0.8125rem;
}

.gov-form-note li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.6em;
    width: 4px;
    height: 4px;
    background: var(--gov-gray-500);
    border-radius: 50%;
}

/* Honeypot (hidden) */
.gov-honeypot {
    position: absolute;
    left: -9999px;
    opacity: 0;
    height: 0;
    width: 0;
    overflow: hidden;
}

/* ==========================================================================
   Contact Methods
   ========================================================================== */
.gov-contact-methods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.gov-contact-method-card {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    padding: 32px;
    text-align: center;
    transition: all var(--gov-transition);
}

.gov-contact-method-card:hover {
    border-color: var(--gov-navy-300);
    box-shadow: var(--gov-shadow);
    transform: translateY(-4px);
}

.gov-method-icon {
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
    transition: all var(--gov-transition);
}

.gov-contact-method-card:hover .gov-method-icon {
    background: var(--gov-gold-pale);
    border-color: var(--gov-gold);
    color: var(--gov-navy-900);
}

.gov-method-title {
    font-size: 1.0625rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 12px;
}

.gov-method-detail {
    font-size: 1rem;
    font-weight: 500;
    color: var(--gov-navy-800);
    margin: 8px 0;
}

.gov-method-time {
    font-size: 0.8125rem;
    color: var(--gov-gray-600);
    margin: 0;
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
   Buttons
   ========================================================================== */
.gov-btn-primary,
.gov-btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 14px 32px;
    font-size: 0.9375rem;
    font-weight: 600;
    border-radius: var(--gov-radius);
    text-decoration: none;
    transition: all var(--gov-transition);
    box-shadow: var(--gov-shadow-sm);
}

.gov-btn-primary {
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-700) 100%);
    color: var(--gov-white);
    border: 2px solid var(--gov-navy-800);
}

.gov-btn-primary:hover {
    background: linear-gradient(135deg, var(--gov-navy-900) 0%, var(--gov-navy-800) 100%);
    transform: translateY(-2px);
    box-shadow: var(--gov-shadow);
}

.gov-btn-secondary {
    background: var(--gov-white);
    color: var(--gov-navy-800);
    border: 2px solid var(--gov-gray-300);
}

.gov-btn-secondary:hover {
    background: var(--gov-gray-100);
    border-color: var(--gov-navy-400);
    transform: translateY(-2px);
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
    
    .gov-page-subtitle {
        font-size: 1rem;
    }
    
    .gov-form-container {
        padding: 28px 20px;
    }
    
    .gov-form-row {
        grid-template-columns: 1fr;
    }
    
    .gov-radio-group,
    .gov-checkbox-group-inline {
        flex-direction: column;
        gap: 12px;
    }
    
    .gov-btn-submit {
        width: 100%;
        padding: 16px 24px;
    }
}

@media (max-width: 640px) {
    .gov-container {
        padding: 0 16px;
    }
    
    .gov-page-title {
        font-size: 1.5rem;
    }
    
    .gov-section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .gov-section-title {
        font-size: 1.25rem;
    }
    
    .gov-form-container {
        padding: 20px 16px;
    }
    
    .gov-success-section {
        min-height: auto;
        padding: 40px 0;
    }
    
    .gov-success-box {
        padding: 32px 20px;
    }
    
    .gov-success-icon {
        width: 80px;
        height: 80px;
    }
    
    .gov-success-icon svg {
        width: 40px;
        height: 40px;
    }
    
    .gov-success-title {
        font-size: 1.5rem;
    }
    
    .gov-success-subtitle {
        font-size: 0.8125rem;
    }
    
    .gov-success-info-item {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .gov-success-info-icon {
        margin: 0 auto;
    }
    
    .gov-success-info-content {
        text-align: center;
    }
    
    .gov-success-actions {
        flex-direction: column;
    }
    
    .gov-success-actions .gov-btn-primary,
    .gov-success-actions .gov-btn-secondary {
        width: 100%;
        justify-content: center;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}

.gov-form-control:focus,
.gov-btn-submit:focus {
    outline: 2px solid var(--gov-navy-500);
    outline-offset: 2px;
}
</style>

<article class="contact-page" itemscope itemtype="https://schema.org/ContactPage">
    
    <!-- Breadcrumb -->
    <nav class="gov-breadcrumb" aria-label="パンくずリスト">
        <div class="gov-container">
            <ol class="gov-breadcrumb-list">
                <li class="gov-breadcrumb-item"><a href="<?php echo home_url('/'); ?>">ホーム</a></li>
                <li class="gov-breadcrumb-item current" aria-current="page">お問い合わせ</li>
            </ol>
        </div>
    </nav>
    
    <!-- Page Header -->
    <header class="gov-page-header">
        <div class="gov-container">
            <div class="gov-header-content">
                <h1 class="gov-page-title" itemprop="headline">お問い合わせ</h1>
                <p class="gov-page-subtitle">Contact Us</p>
                <p class="gov-page-description">
                    補助金・助成金に関するご質問やサイトの使い方など、どのようなことでもお気軽にお問い合わせください。<br>
                    専門スタッフが丁寧にご対応いたします。
                </p>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <div class="gov-page-content">
        <div class="gov-container">
            
            <?php if ($form_success): ?>
            <!-- Success Message - 送信完了画面 -->
            <section class="gov-content-section gov-success-section" id="success-message">
                <div class="gov-success-box">
                    <div class="gov-success-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <h2 class="gov-success-title">送信が完了しました</h2>
                    <p class="gov-success-subtitle">Submission Completed</p>
                    <div class="gov-success-text">
                        <p>お問い合わせいただき、誠にありがとうございます。</p>
                        <p>送信が正常に完了いたしました。</p>
                        <p>ご入力いただいたメールアドレス宛に、自動送信メールをお送りしております。</p>
                        <p><strong>内容を確認の上、2営業日以内に担当者よりご連絡させていただきます。</strong></p>
                    </div>
                    
                    <div class="gov-success-info">
                        <div class="gov-success-info-item">
                            <div class="gov-success-info-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </div>
                            <div class="gov-success-info-content">
                                <strong>自動返信メール</strong>
                                <span>受付確認メールを送信しました。届かない場合は迷惑メールフォルダをご確認ください。</span>
                            </div>
                        </div>
                        <div class="gov-success-info-item">
                            <div class="gov-success-info-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div class="gov-success-info-content">
                                <strong>回答予定</strong>
                                <span>通常2営業日以内にご返信いたします。お急ぎの場合はお電話でご連絡ください。</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="gov-success-actions">
                        <a href="<?php echo home_url('/'); ?>" class="gov-btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                            トップページへ戻る
                        </a>
                        <a href="<?php echo home_url('/grant/'); ?>" class="gov-btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            補助金を検索
                        </a>
                    </div>
                </div>
            </section>
            <?php else: ?>
            
            <?php if (!empty($form_errors)): ?>
            <!-- Error Message -->
            <section class="gov-content-section">
                <div class="gov-error-box">
                    <div class="gov-error-header">
                        <div class="gov-error-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </div>
                        <h3 class="gov-error-title">入力内容にエラーがあります</h3>
                    </div>
                    <ul class="gov-error-list">
                        <?php foreach ($form_errors as $error): ?>
                        <li><?php echo esc_html($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- FAQ Section -->
            <section class="gov-content-section">
                <div class="gov-section-header">
                    <div class="gov-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </div>
                    <h2 class="gov-section-title">よくあるご質問</h2>
                </div>
                <p class="gov-section-intro">お問い合わせの前に、よくあるご質問をご確認ください。</p>
                
                <div class="gov-faq-grid">
                    <div class="gov-faq-item">
                        <h3 class="gov-faq-question">掲載されている補助金情報は最新ですか？</h3>
                        <p class="gov-faq-answer">当サイトでは、各省庁・自治体の公式情報を毎日収集し、週2回の専門スタッフによる確認を行っています。ただし、制度内容は予告なく変更される場合がありますので、申請前には必ず公式情報をご確認ください。</p>
                    </div>
                    
                    <div class="gov-faq-item">
                        <h3 class="gov-faq-question">補助金の採択を保証してもらえますか？</h3>
                        <p class="gov-faq-answer">補助金・助成金の採択は、各制度の管轄機関が審査・決定するため、当サイトでは採択を保証することはできません。申請サポートサービスでは、書類作成のアドバイスや相談対応を行っています。</p>
                    </div>
                    
                    <div class="gov-faq-item">
                        <h3 class="gov-faq-question">サイトの利用に料金はかかりますか？</h3>
                        <p class="gov-faq-answer">補助金・助成金の検索機能は完全無料でご利用いただけます。申請サポートサービスなど一部の有料サービスについては、事前に料金を明示し、ご同意いただいた上で提供いたします。</p>
                    </div>
                    
                    <div class="gov-faq-item">
                        <h3 class="gov-faq-question">個人情報はどのように管理されていますか？</h3>
                        <p class="gov-faq-answer">お預かりした個人情報は、個人情報保護法に基づき適切に管理しています。SSL暗号化通信の採用、定期的なセキュリティ監査など、万全の体制で保護しています。詳しくは<a href="<?php echo home_url('/privacy/'); ?>" class="gov-text-link">プライバシーポリシー</a>をご覧ください。</p>
                    </div>
                </div>
            </section>
            
            <!-- Contact Form -->
            <section class="gov-content-section">
                <div class="gov-section-header">
                    <div class="gov-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <h2 class="gov-section-title">お問い合わせフォーム</h2>
                </div>
                
                <div class="gov-form-container">
                    <form class="gov-contact-form" id="contactForm" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('contact_form_submit', 'contact_form_nonce'); ?>
                        <input type="hidden" name="action" value="contact_form">
                        
                        <!-- Honeypot field (hidden from users, filled by bots) -->
                        <div class="gov-honeypot" aria-hidden="true">
                            <label for="website_url">ウェブサイト（入力しないでください）</label>
                            <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
                        </div>
                        
                        <!-- Inquiry Type -->
                        <div class="gov-form-group">
                            <label for="inquiry-type" class="gov-form-label required">お問い合わせ種別</label>
                            <select id="inquiry-type" name="inquiry_type" class="gov-form-control" required>
                                <option value="">選択してください</option>
                                <option value="usage">サイトの使い方について</option>
                                <option value="grant-info">補助金・助成金の制度について</option>
                                <option value="update">掲載情報の修正・更新</option>
                                <option value="media">媒体掲載・取材依頼</option>
                                <option value="technical">技術的な問題・不具合</option>
                                <option value="other">その他</option>
                            </select>
                        </div>
                        
                        <!-- Name -->
                        <div class="gov-form-group">
                            <label for="name" class="gov-form-label required">お名前</label>
                            <input type="text" id="name" name="name" class="gov-form-control" placeholder="山田 太郎" required>
                        </div>
                        
                        <!-- Email -->
                        <div class="gov-form-group">
                            <label for="email" class="gov-form-label required">メールアドレス</label>
                            <input type="email" id="email" name="email" class="gov-form-control" placeholder="example@example.com" required>
                            <p class="gov-form-help">回答のご連絡に使用いたします</p>
                        </div>
                        
                        <!-- Phone -->
                        <div class="gov-form-group">
                            <label for="phone" class="gov-form-label">
                                電話番号
                                <span class="gov-optional-label">（任意）</span>
                            </label>
                            <input type="tel" id="phone" name="phone" class="gov-form-control" placeholder="03-1234-5678">
                            <p class="gov-form-help">緊急時のご連絡用</p>
                        </div>
                        
                        <!-- Company -->
                        <div class="gov-form-group">
                            <label for="company" class="gov-form-label">
                                会社名・団体名
                                <span class="gov-optional-label">（任意）</span>
                            </label>
                            <input type="text" id="company" name="company" class="gov-form-control" placeholder="株式会社〇〇">
                        </div>
                        
                        <div class="gov-form-row">
                            <!-- Industry -->
                            <div class="gov-form-group gov-form-col">
                                <label for="industry" class="gov-form-label">
                                    業種
                                    <span class="gov-optional-label">（任意）</span>
                                </label>
                                <select id="industry" name="industry" class="gov-form-control">
                                    <option value="">選択してください</option>
                                    <option value="manufacturing">製造業</option>
                                    <option value="retail">小売業</option>
                                    <option value="service">サービス業</option>
                                    <option value="it">IT・通信業</option>
                                    <option value="construction">建設業</option>
                                    <option value="transport">運輸業</option>
                                    <option value="healthcare">医療・福祉</option>
                                    <option value="education">教育・学習支援</option>
                                    <option value="agriculture">農林水産業</option>
                                    <option value="other">その他</option>
                                </select>
                            </div>
                            
                            <!-- Employees -->
                            <div class="gov-form-group gov-form-col">
                                <label for="employees" class="gov-form-label">
                                    従業員数
                                    <span class="gov-optional-label">（任意）</span>
                                </label>
                                <select id="employees" name="employees" class="gov-form-control">
                                    <option value="">選択してください</option>
                                    <option value="1">1人（個人事業主）</option>
                                    <option value="2-5">2-5人</option>
                                    <option value="6-20">6-20人</option>
                                    <option value="21-50">21-50人</option>
                                    <option value="51-100">51-100人</option>
                                    <option value="101-300">101-300人</option>
                                    <option value="301+">301人以上</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Subject -->
                        <div class="gov-form-group">
                            <label for="subject" class="gov-form-label required">件名</label>
                            <input type="text" id="subject" name="subject" class="gov-form-control" placeholder="お問い合わせ件名" required>
                        </div>
                        
                        <!-- Message -->
                        <div class="gov-form-group">
                            <label for="message" class="gov-form-label required">お問い合わせ内容</label>
                            <textarea id="message" name="message" class="gov-form-control" rows="8" placeholder="具体的にご記入ください（500文字以内）" maxlength="500" required></textarea>
                            <p class="gov-form-help">残り<span id="char-count">500</span>文字</p>
                        </div>
                        
                        <!-- Contact Method -->
                        <div class="gov-form-group">
                            <label class="gov-form-label">ご希望の連絡方法</label>
                            <div class="gov-radio-group">
                                <label class="gov-radio-label">
                                    <input type="radio" name="contact_method" value="email" checked>
                                    <span class="gov-radio-text">メール</span>
                                </label>
                                <label class="gov-radio-label">
                                    <input type="radio" name="contact_method" value="phone">
                                    <span class="gov-radio-text">電話</span>
                                </label>
                                <label class="gov-radio-label">
                                    <input type="radio" name="contact_method" value="either">
                                    <span class="gov-radio-text">どちらでも可</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Contact Time -->
                        <div class="gov-form-group">
                            <label class="gov-form-label">ご希望の連絡時間帯</label>
                            <div class="gov-checkbox-group-inline">
                                <label class="gov-checkbox-label">
                                    <input type="checkbox" name="contact_time[]" value="morning">
                                    <span class="gov-checkbox-text">9:00-12:00</span>
                                </label>
                                <label class="gov-checkbox-label">
                                    <input type="checkbox" name="contact_time[]" value="afternoon">
                                    <span class="gov-checkbox-text">13:00-17:00</span>
                                </label>
                                <label class="gov-checkbox-label">
                                    <input type="checkbox" name="contact_time[]" value="evening">
                                    <span class="gov-checkbox-text">17:00-19:00</span>
                                </label>
                                <label class="gov-checkbox-label">
                                    <input type="checkbox" name="contact_time[]" value="anytime" checked>
                                    <span class="gov-checkbox-text">時間指定なし</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Privacy Notice -->
                        <div class="gov-privacy-notice">
                            <h3 class="gov-privacy-title">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                個人情報の取り扱いについて
                            </h3>
                            <p>お問い合わせでお預かりした個人情報は、以下の目的でのみ使用いたします：</p>
                            <ul class="gov-privacy-list">
                                <li>お問い合わせへの回答・対応</li>
                                <li>サービス改善のための統計分析</li>
                                <li>重要なお知らせの配信（ご希望の場合のみ）</li>
                            </ul>
                            <p>詳細は<a href="<?php echo home_url('/privacy/'); ?>" class="gov-text-link">プライバシーポリシー</a>をご確認ください。</p>
                        </div>
                        
                        <!-- Privacy Agreement -->
                        <div class="gov-form-group">
                            <label class="gov-checkbox-label-large">
                                <input type="checkbox" id="privacy-agree" name="privacy_agree" required>
                                <span class="gov-checkbox-text">個人情報の取り扱いに同意する</span>
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="gov-form-actions">
                            <button type="submit" class="gov-btn-submit">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                                <span class="btn-text">送信する</span>
                            </button>
                        </div>
                        
                        <!-- Form Note -->
                        <div class="gov-form-note">
                            <p class="note-title"><strong>送信前にご確認ください</strong></p>
                            <ul>
                                <li>入力内容に誤りがないかご確認ください</li>
                                <li>メールアドレスが正しくないと返信できません</li>
                                <li>通常2営業日以内にご返信いたします</li>
                            </ul>
                        </div>
                    </form>
                </div>
            </section>
            
            <?php endif; ?>
            
            <!-- Other Contact Methods -->
            <section class="gov-content-section">
                <div class="gov-section-header">
                    <div class="gov-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <h2 class="gov-section-title">その他のお問い合わせ方法</h2>
                </div>
                
                <div class="gov-contact-methods-grid">
                    <div class="gov-contact-method-card">
                        <div class="gov-method-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <h3 class="gov-method-title">お電話でのお問い合わせ</h3>
                        <p class="gov-method-detail">TEL: 準備中</p>
                        <p class="gov-method-time">受付時間: 平日 9:00-18:00（土日祝日除く）</p>
                    </div>
                    
                    <div class="gov-contact-method-card">
                        <div class="gov-method-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        <h3 class="gov-method-title">メールでのお問い合わせ</h3>
                        <p class="gov-method-detail">Email: info@joseikin-insight.com</p>
                        <p class="gov-method-time">回答まで2-3営業日いただく場合があります</p>
                    </div>
                    
                    <div class="gov-contact-method-card">
                        <div class="gov-method-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <h3 class="gov-method-title">郵送でのお問い合わせ</h3>
                        <p class="gov-method-detail">〒136-0073<br>東京都江東区北砂3-23-8 401</p>
                        <p class="gov-method-time">補助金インサイト運営事務局 宛</p>
                    </div>
                </div>
            </section>
            
            <!-- Related Links -->
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character count
    const messageField = document.getElementById('message');
    const charCount = document.getElementById('char-count');
    
    if (messageField && charCount) {
        messageField.addEventListener('input', function() {
            const remaining = 500 - this.value.length;
            charCount.textContent = remaining;
            
            if (remaining < 50) {
                charCount.style.color = 'var(--gov-red)';
                charCount.style.fontWeight = '700';
            } else if (remaining < 100) {
                charCount.style.color = 'var(--gov-gold)';
                charCount.style.fontWeight = '600';
            } else {
                charCount.style.color = '';
                charCount.style.fontWeight = '';
            }
        });
    }
    
    // Form submission
    const form = document.getElementById('contactForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('.gov-btn-submit');
            const buttonText = submitButton.querySelector('.btn-text');
            
            if (submitButton && buttonText) {
                buttonText.textContent = '送信中...';
                submitButton.disabled = true;
            }
        });
    }
    
    // Success message handling - scroll to top and show
    const successSection = document.querySelector('.gov-success-section');
    if (successSection) {
        // Scroll to top smoothly
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // Add confetti effect (optional visual feedback)
        setTimeout(() => {
            successSection.classList.add('is-visible');
        }, 100);
    }
    
    // Scroll to error message
    const errorBox = document.querySelector('.gov-error-box');
    if (errorBox) {
        setTimeout(() => {
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }
    
    // Smooth scroll for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
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
