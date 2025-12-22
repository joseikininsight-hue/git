<?php
/**
 * Content Ad Injector
 * 記事本文中に広告を自動挿入する機能
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 * 
 * 機能:
 * - 最初のH2見出しの直前に広告を自動挿入
 * - 投稿タイプに応じた広告枠の切り替え
 * - カテゴリ連動による広告の出し分け
 * - H2がない記事への対応（オプション）
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 記事本文中のH2見出し手前に広告を自動挿入する
 * 
 * ✅ 収益最適化: 最初のH2だけでなく、記事中盤・後半にも広告を配置
 * 
 * @param string $content 記事本文
 * @return string 広告挿入後の記事本文
 */
function ji_inject_ad_content_middle($content) {
    // 個別記事ページのみ対象（フィード、一覧、管理画面は除外）
    if (!is_singular() || is_feed() || is_admin()) {
        return $content;
    }
    
    // REST APIリクエストは除外
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return $content;
    }
    
    // ji_display_ad 関数が存在するか確認
    if (!function_exists('ji_display_ad')) {
        return $content;
    }
    
    // 投稿タイプによって呼び出す広告枠を切り替え
    $post_type = get_post_type();
    $position_id = '';
    
    switch ($post_type) {
        case 'grant':
            $position_id = 'single_grant_content_middle';
            break;
        case 'column':
            $position_id = 'single_column_content_middle';
            break;
        case 'post':
            $position_id = 'content_middle';
            break;
        default:
            // その他の投稿タイプは処理しない
            return $content;
    }
    
    // 広告HTMLを取得（出力バッファリングを使用）
    ob_start();
    ji_display_ad($position_id);
    $ad_html = ob_get_clean();
    
    // 広告が設定されていなければ何もしない
    if (empty(trim($ad_html))) {
        return $content;
    }
    
    // 広告をラッパーで囲む
    $ad_wrapper = sprintf(
        '<aside class="ji-content-middle-ad" aria-label="広告" style="margin: 2.5em 0; clear: both;">
            <div class="ji-content-ad-inner">
                %s
            </div>
        </aside>',
        $ad_html
    );
    
    // H2見出しの数をカウント
    $h2_pattern = '/<h2[^>]*>/i';
    preg_match_all($h2_pattern, $content, $matches);
    $h2_count = count($matches[0]);
    
    if ($h2_count > 0) {
        // 最初のH2の直前に挿入
        $content = preg_replace(
            $h2_pattern,
            $ad_wrapper . '$0',
            $content,
            1 // 1回だけ置換（＝最初のH2だけ）
        );
    } else {
        // H2がない場合は記事末尾に追加
        $content .= $ad_wrapper;
    }
    
    return $content;
}
// AdSense自動広告に任せるため無効化
// add_filter('the_content', 'ji_inject_ad_content_middle', 20);


/**
 * 【収益最適化】記事の中盤～後半にも広告を追加
 * 
 * 最初のH2だけでなく、3番目のH2の前にも広告を配置することで、
 * 記事が長い場合の収益機会を増やします。
 * 
 * @param string $content 記事本文
 * @return string 広告挿入後の記事本文
 */
function ji_inject_additional_ads($content) {
    // 対象投稿タイプのチェック
    if (!is_singular(array('grant', 'column'))) {
        return $content;
    }
    
    // REST APIリクエストは除外
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return $content;
    }
    
    // ji_inject_ad_at_multiple_h2 関数が存在するか確認
    if (!function_exists('ji_inject_ad_at_multiple_h2')) {
        return $content;
    }
    
    // 投稿タイプに応じた広告位置ID
    $post_type = get_post_type();
    $position_id = ($post_type === 'grant') ? 'single_grant_content_middle' : 'single_column_content_middle';
    
    // H2の数をカウント
    $h2_pattern = '/<h2[^>]*>/i';
    preg_match_all($h2_pattern, $content, $matches);
    $h2_count = count($matches[0]);
    
    // H2の数に応じて広告を追加
    // H2が2つ以上: 2番目のH2の前に広告
    // H2が4つ以上: 2番目と4番目のH2の前に広告
    if ($h2_count >= 4) {
        $content = ji_inject_ad_at_multiple_h2($content, array(2, 4), $position_id);
    } elseif ($h2_count >= 2) {
        $content = ji_inject_ad_at_multiple_h2($content, array(2), $position_id);
    }
    
    return $content;
}
// AdSense自動広告に任せるため無効化
// add_filter('the_content', 'ji_inject_additional_ads', 21);


/**
 * 複数のH2見出しに対して広告を挿入する（オプション機能）
 * 
 * 例: 2番目と4番目のH2の前に広告を挿入
 * 
 * @param string $content 記事本文
 * @param array $positions 挿入位置（1始まり）の配列 例: [1, 3]
 * @param string $position_id 広告位置ID
 * @return string 広告挿入後の記事本文
 */
function ji_inject_ad_at_multiple_h2($content, $positions = array(1), $position_id = 'content_middle') {
    if (!function_exists('ji_display_ad')) {
        return $content;
    }
    
    // 広告HTMLを取得
    ob_start();
    ji_display_ad($position_id);
    $ad_html = ob_get_clean();
    
    if (empty(trim($ad_html))) {
        return $content;
    }
    
    $ad_wrapper = sprintf(
        '<aside class="ji-content-middle-ad" aria-label="広告" style="margin: 2.5em 0; clear: both;">%s</aside>',
        $ad_html
    );
    
    // H2タグの位置を全て取得
    $h2_pattern = '/<h2[^>]*>/i';
    preg_match_all($h2_pattern, $content, $matches, PREG_OFFSET_CAPTURE);
    
    if (empty($matches[0])) {
        return $content;
    }
    
    // 後ろから挿入（位置ズレ防止）
    $positions = array_reverse(array_unique($positions));
    
    foreach ($positions as $pos) {
        $index = $pos - 1; // 0始まりに変換
        if (isset($matches[0][$index])) {
            $offset = $matches[0][$index][1];
            $content = substr_replace($content, $ad_wrapper, $offset, 0);
        }
    }
    
    return $content;
}


/**
 * 段落数に基づいて広告を挿入する（オプション機能）
 * 
 * @param string $content 記事本文
 * @param int $after_paragraph 何段落目の後に挿入するか
 * @param string $position_id 広告位置ID
 * @return string 広告挿入後の記事本文
 */
function ji_inject_ad_after_paragraph($content, $after_paragraph = 3, $position_id = 'content_middle') {
    if (!function_exists('ji_display_ad')) {
        return $content;
    }
    
    // 広告HTMLを取得
    ob_start();
    ji_display_ad($position_id);
    $ad_html = ob_get_clean();
    
    if (empty(trim($ad_html))) {
        return $content;
    }
    
    $ad_wrapper = sprintf(
        '<aside class="ji-content-middle-ad" aria-label="広告" style="margin: 2.5em 0; clear: both;">%s</aside>',
        $ad_html
    );
    
    // </p>タグで分割
    $paragraphs = explode('</p>', $content);
    $paragraph_count = count($paragraphs);
    
    // 指定段落数より少ない場合は挿入しない
    if ($paragraph_count <= $after_paragraph) {
        return $content;
    }
    
    // 指定段落の後に広告を挿入
    $new_content = '';
    foreach ($paragraphs as $index => $paragraph) {
        $new_content .= $paragraph;
        
        // 最後の要素でなければ</p>を付加
        if ($index < $paragraph_count - 1) {
            $new_content .= '</p>';
        }
        
        // 指定段落の後に広告挿入
        if ($index + 1 === $after_paragraph) {
            $new_content .= $ad_wrapper;
        }
    }
    
    return $new_content;
}


/**
 * コンテンツ内広告用のCSSを出力
 */
function ji_content_ad_styles() {
    if (!is_singular()) {
        return;
    }
    ?>
    <style>
    /* Content Middle Ad Styles */
    .ji-content-middle-ad {
        margin: 2.5em 0;
        padding: 0;
        clear: both;
    }
    
    .ji-content-middle-ad .ji-content-ad-inner {
        position: relative;
    }
    
    /* 記事本文内のアフィリエイト広告スタイル調整 */
    .ji-content-middle-ad .ji-affiliate-ad {
        margin: 0;
    }
    
    .ji-content-middle-ad .ji-article-ad {
        max-width: 100%;
    }
    
    /* AdSense用の中央寄せ */
    .ji-content-middle-ad ins.adsbygoogle {
        display: block;
        margin: 0 auto;
    }
    
    /* モバイル対応 */
    @media (max-width: 768px) {
        .ji-content-middle-ad {
            margin: 2em 0;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'ji_content_ad_styles', 100);
