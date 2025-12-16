<?php
/**
 * Template Name: Contact Page (お問い合わせ)
 * 
 * 補助金・助成金情報サイト - お問い合わせページ
 * Grant & Subsidy Information Site - Contact Page
 * 
 * @package Grant_Insight_Perfect
 * @version 6.0-fixed-variable-scope
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

get_header();

// メール送信処理用の変数をグローバル宣言
global $form_submitted, $form_success, $form_errors;

$form_submitted = false;
$form_success = false;
$form_errors = array();

// POSTデータ処理は admin_post フックで処理されるため、ここでは処理しない
// GET パラメータのみ処理

// GET パラメータで成功メッセージを表示
if (isset($_GET['contact_sent']) && $_GET['contact_sent'] == '1') {
    $form_success = true;
}

// GET パラメータでエラーメッセージを表示
if (isset($_GET['contact_error']) && $_GET['contact_error'] == '1') {
    if (isset($_GET['error_msg'])) {
        $form_errors = explode('|', urldecode($_GET['error_msg']));
    } else {
        $form_errors = array('フォームの送信中にエラーが発生しました。');
    }
}

// pages/templates内の実際のテンプレートファイルを読み込み
$template_path = get_template_directory() . '/pages/templates/page-contact.php';

if (file_exists($template_path)) {
    include $template_path;
} else {
    // フォールバック処理
    ?>
    <div class="container" style="padding: 60px 20px; text-align: center;">
        <h1>お問い合わせ</h1>
        <p>お問い合わせページのテンプレートが見つかりませんでした。</p>
        <p>管理者に連絡してください。</p>
    </div>
    <?php
    get_footer();
}
?>