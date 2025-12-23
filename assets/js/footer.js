/**
 * Footer JavaScript - Zukan Style
 * 補助金図鑑 - Footer Scripts
 * 
 * @package Joseikin_Insight_Footer
 * @version 10.0.0 (Zukan Edition)
 */

(function() {
    'use strict';
    
    /**
     * GIP Chat Container Error Suppression
     * Purpose: チャットボットコンテナが存在しないページでのJSエラー抑制とCPU負荷削減
     * Impact: モバイルPageSpeedスコア改善（JS実行時間短縮）
     */
    function initGIPChatControl() {
        // チャットコンテナの存在チェック
        var chatSelectors = [
            '.gip-chat',
            '#gip-chat-container',
            '[data-gip-chat]',
            '.chat-widget',
            '#chat-widget-container'
        ];
        
        var chatExists = false;
        for (var i = 0; i < chatSelectors.length; i++) {
            if (document.querySelector(chatSelectors[i])) {
                chatExists = true;
                break;
            }
        }
        
        // チャットコンテナが存在しない場合、GIP Chat初期化を抑制
        if (!chatExists) {
            // GIPChatオブジェクトが存在する場合、init関数を無効化
            if (typeof window.GIPChat !== 'undefined') {
                // 既存のinit関数をバックアップして無害な関数に置き換え
                if (typeof window.GIPChat.init === 'function') {
                    window.GIPChat._originalInit = window.GIPChat.init;
                    window.GIPChat.init = function() {
                        console.info('GIP Chat: Initialization skipped (container not found)');
                        return false;
                    };
                }
            }
            
            // コンソールエラーのキャッチ（グローバルエラーハンドラー）
            var originalConsoleError = console.error;
            console.error = function() {
                var message = arguments[0] || '';
                // GIP Chat関連のエラーメッセージを抑制
                if (typeof message === 'string' && 
                    (message.indexOf('GIP Chat') !== -1 || 
                     message.indexOf('Container not found') !== -1)) {
                    // エラーを表示しない（開発環境では警告として表示可能）
                    if (window.location.hostname === 'localhost' || window.location.hostname.indexOf('dev') !== -1) {
                        console.warn('[Suppressed Error]', message);
                    }
                    return;
                }
                // その他のエラーは通常通り表示
                originalConsoleError.apply(console, arguments);
            };
        }
    }
    
    // DOM読み込み完了後に実行
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGIPChatControl);
    } else {
        initGIPChatControl();
    }
    
    console.log('[OK] Footer JS v10.0.0 - Zukan Style initialized');
})();
