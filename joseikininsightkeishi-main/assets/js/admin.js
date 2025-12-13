/**
 * Google Sheets Integration - Admin JavaScript
 * 管理画面用スクリプト（メモリ最適化版 v4.0対応）
 * 
 * @package Grant_Insight_Perfect
 * @version 4.0.0
 */

(function($) {
    'use strict';

    /**
     * Google Sheets Admin Controller
     */
    const GISheetsAdmin = {
        
        // 設定
        config: {
            ajaxTimeout: 300000, // 5分
            progressInterval: 2000, // 2秒
            noticeDisplayTime: 5000
        },
        
        // 状態
        state: {
            isSyncing: false,
            progressTimer: null,
            currentOperation: null
        },

        /**
         * 初期化
         */
        init: function() {
            console.log('[GI Sheets Admin] Initializing v4.0...');
            
            if (typeof giSheetsAdmin === 'undefined') {
                console.error('[GI Sheets Admin] giSheetsAdmin object not found');
                return;
            }
            
            this.bindEvents();
            this.initializeUI();
            
            console.log('[GI Sheets Admin] Initialized successfully');
        },

        /**
         * UI初期化
         */
        initializeUI: function() {
            // プログレスバーを非表示
            $('#gi-progress-container').hide();
            $('#sync-result').hide();
            $('#duplicate-check-result').hide();
        },

        /**
         * イベントバインディング
         */
        bindEvents: function() {
            var self = this;
            
            // 接続テスト
            $('#test-connection').on('click', function(e) {
                e.preventDefault();
                self.testConnection();
            });

            // 同期ボタン（共通ハンドラー）
            $('.gi-sync-btn').on('click', function(e) {
                e.preventDefault();
                var direction = $(this).data('direction');
                self.executeSync(direction);
            });

            // シート初期化
            $('#initialize-sheet').on('click', function(e) {
                e.preventDefault();
                self.initializeSheet();
            });

            // データクリア
            $('#clear-sheet').on('click', function(e) {
                e.preventDefault();
                self.clearSheet();
            });

            // 重複チェック
            $('#check-duplicates').on('click', function(e) {
                e.preventDefault();
                self.checkDuplicates();
            });

            // 重複エクスポート
            $('#export-duplicates').on('click', function(e) {
                e.preventDefault();
                self.exportDuplicates();
            });

            // 都道府県検証
            $('#export-invalid-prefectures').on('click', function(e) {
                e.preventDefault();
                self.exportInvalidPrefectures();
            });

            // タクソノミーエクスポート
            $('#export-taxonomies').on('click', function(e) {
                e.preventDefault();
                self.exportTaxonomies();
            });

            // タクソノミーインポート
            $('#import-taxonomies').on('click', function(e) {
                e.preventDefault();
                self.importTaxonomies();
            });

            // ログ更新
            $('#refresh-log').on('click', function(e) {
                e.preventDefault();
                self.refreshLog();
            });

            // ログクリア
            $('#clear-log').on('click', function(e) {
                e.preventDefault();
                self.clearLog();
            });

            // ID範囲エクスポート
            $('#export-by-id-range').on('click', function(e) {
                e.preventDefault();
                self.exportByIdRange();
            });

            // 同期キャンセル
            $('#cancel-sync').on('click', function(e) {
                e.preventDefault();
                self.cancelSync();
            });
        },

        /**
         * 接続テスト
         */
        testConnection: function() {
            var self = this;
            var $btn = $('#test-connection');
            var $status = $('#connection-status');
            
            this.setButtonLoading($btn, true, 'テスト中...');
            this.updateConnectionStatus('loading', 'テスト中...');
            
            this.ajax('gi_test_sheets_connection', {})
                .done(function(response) {
                    if (response.success) {
                        self.updateConnectionStatus('success', response.data);
                        self.showNotice('success', response.data);
                    } else {
                        self.updateConnectionStatus('error', response.data || 'エラー');
                        self.showNotice('error', response.data || 'エラー');
                    }
                })
                .fail(function(xhr, status, error) {
                    var message = self.getErrorMessage(xhr, error);
                    self.updateConnectionStatus('error', message);
                    self.showNotice('error', message);
                })
                .always(function() {
                    self.setButtonLoading($btn, false, '<span class="dashicons dashicons-yes"></span> 接続テスト');
                });
        },

        /**
         * 接続ステータス更新
         */
        updateConnectionStatus: function(status, message) {
            var $status = $('#connection-status');
            
            $status.removeClass('success error idle loading');
            $status.addClass(status);
            
            var icon = 'dashicons-minus';
            if (status === 'success') icon = 'dashicons-yes-alt';
            else if (status === 'error') icon = 'dashicons-warning';
            else if (status === 'loading') icon = 'dashicons-update spin';
            
            $status.html(
                '<span class="dashicons ' + icon + '"></span>' +
                '<span class="gi-status-text">' + this.escapeHtml(message) + '</span>'
            );
        },

        /**
         * 同期実行
         */
        executeSync: function(direction) {
            var self = this;
            var directionText = direction === 'sheets_to_wp' ? 'Sheets → WordPress' : 'WordPress → Sheets';
            
            if (this.state.isSyncing) {
                this.showNotice('warning', '別の同期処理が実行中です');
                return;
            }
            
            if (!confirm(directionText + ' の同期を実行しますか？\n\n大量のデータがある場合、数分かかることがあります。\nブラウザを閉じないでください。')) {
                return;
            }
            
            this.state.isSyncing = true;
            this.state.currentOperation = direction;
            
            var $btns = $('.gi-sync-btn');
            var $currentBtn = $btns.filter('[data-direction="' + direction + '"]');
            var originalHtml = $currentBtn.html();
            
            $btns.prop('disabled', true);
            $currentBtn.html('<span class="dashicons dashicons-update spin"></span> 同期中...');
            
            // プログレス表示開始
            this.showProgress();
            this.startProgressMonitor();
            
            // 結果エリアをクリア
            $('#sync-result').hide();
            
            this.ajax('gi_manual_sheets_sync', { direction: direction }, { timeout: this.config.ajaxTimeout })
                .done(function(response) {
                    self.stopProgressMonitor();
                    
                    if (response.success) {
                        self.updateProgress(100, '完了');
                        self.showSyncResult('success', response.data);
                        self.showNotice('success', response.data);
                    } else {
                        self.showSyncResult('error', response.data || '同期に失敗しました');
                        self.showNotice('error', response.data || '同期に失敗しました');
                    }
                })
                .fail(function(xhr, status, error) {
                    self.stopProgressMonitor();
                    var message = self.getErrorMessage(xhr, error);
                    self.showSyncResult('error', message);
                    self.showNotice('error', message);
                })
                .always(function() {
                    self.state.isSyncing = false;
                    self.state.currentOperation = null;
                    
                    $btns.prop('disabled', false);
                    $currentBtn.html(originalHtml);
                    
                    // 3秒後にプログレスを非表示
                    setTimeout(function() {
                        self.hideProgress();
                    }, 3000);
                    
                    // 2秒後にログを更新
                    setTimeout(function() {
                        self.refreshLog();
                    }, 2000);
                });
        },

        /**
         * プログレス表示
         */
        showProgress: function() {
            var $container = $('#gi-progress-container');
            if ($container.length === 0) {
                // プログレスコンテナがなければ作成
                var html = '<div id="gi-progress-container" style="margin: 15px 0;">' +
                           '<div class="gi-progress">' +
                           '<div id="gi-progress-bar" class="gi-progress-bar" style="width: 0%;">0%</div>' +
                           '</div>' +
                           '<p id="gi-progress-text" style="text-align: center; color: #666; margin: 10px 0;">処理中...</p>' +
                           '</div>';
                $('.gi-sync-btn').first().closest('.gi-btn-group').after(html);
            }
            $('#gi-progress-container').show();
            this.updateProgress(0, '開始中...');
        },

        /**
         * プログレス非表示
         */
        hideProgress: function() {
            $('#gi-progress-container').fadeOut();
        },

        /**
         * プログレス更新
         */
        updateProgress: function(percentage, text) {
            $('#gi-progress-bar').css('width', percentage + '%').text(percentage + '%');
            if (text) {
                $('#gi-progress-text').text(text);
            }
        },

        /**
         * プログレス監視開始
         */
        startProgressMonitor: function() {
            var self = this;
            
            this.state.progressTimer = setInterval(function() {
                self.ajax('gi_get_sync_progress', {}, { timeout: 10000 })
                    .done(function(response) {
                        if (response.success && response.data) {
                            var progress = response.data;
                            var percentage = progress.percentage || 0;
                            var text = progress.processed + ' / ' + progress.total + ' 処理中';
                            
                            if (progress.stats) {
                                text += ' (作成: ' + (progress.stats.created || 0) + 
                                        ', 更新: ' + (progress.stats.updated || 0) + ')';
                            }
                            
                            self.updateProgress(percentage, text);
                            
                            if (progress.status === 'completed' || progress.status === 'error') {
                                self.stopProgressMonitor();
                            }
                        }
                    });
            }, this.config.progressInterval);
        },

        /**
         * プログレス監視停止
         */
        stopProgressMonitor: function() {
            if (this.state.progressTimer) {
                clearInterval(this.state.progressTimer);
                this.state.progressTimer = null;
            }
        },

        /**
         * 同期キャンセル
         */
        cancelSync: function() {
            var self = this;
            
            if (!this.state.isSyncing) {
                this.showNotice('info', '実行中の同期はありません');
                return;
            }
            
            if (!confirm('同期をキャンセルしますか？')) {
                return;
            }
            
            this.ajax('gi_cancel_sync', {})
                .done(function(response) {
                    if (response.success) {
                        self.showNotice('success', response.data);
                    }
                });
        },

        /**
         * 同期結果表示
         */
        showSyncResult: function(type, message) {
            var $result = $('#sync-result');
            var $message = $('#sync-message');
            
            $result.removeClass('notice-success notice-error notice-warning');
            $result.addClass(type === 'success' ? 'notice-success' : 'notice-error');
            
            $message.text(message);
            $result.show();
        },

        /**
         * シート初期化
         */
        initializeSheet: function() {
            var self = this;
            
            if (!confirm('スプレッドシートを初期化します。\n\n⚠️ 警告: 既存のデータは全て削除されます。\n\n続行しますか？')) {
                return;
            }
            
            var $btn = $('#initialize-sheet');
            this.setButtonLoading($btn, true, '初期化中...');
            
            this.ajax('gi_initialize_sheet', {})
                .done(function(response) {
                    if (response.success) {
                        self.showNotice('success', response.data);
                    } else {
                        self.showNotice('error', response.data || '初期化に失敗しました');
                    }
                })
                .fail(function(xhr, status, error) {
                    self.showNotice('error', self.getErrorMessage(xhr, error));
                })
                .always(function() {
                    self.setButtonLoading($btn, false, '<span class="dashicons dashicons-welcome-add-page"></span> シート初期化');
                });
        },

        /**
         * シートクリア
         */
        clearSheet: function() {
            var self = this;
            
            if (!confirm('⚠️ 警告: スプレッドシートの全データ（ヘッダー以外）が削除されます。\n\nこの操作は取り消せません。本当に実行しますか？')) {
                return;
            }
            
            var $btn = $('#clear-sheet');
            this.setButtonLoading($btn, true, 'クリア中...');
            
            this.ajax('gi_clear_sheet', {})
                .done(function(response) {
                    if (response.success) {
                        self.showNotice('success', response.data);
                    } else {
                        self.showNotice('error', response.data || 'クリアに失敗しました');
                    }
                })
                .fail(function(xhr, status, error) {
                    self.showNotice('error', self.getErrorMessage(xhr, error));
                })
                .always(function() {
                    self.setButtonLoading($btn, false, '<span class="dashicons dashicons-trash"></span> データクリア');
                });
        },

        /**
         * 重複チェック
         */
        checkDuplicates: function() {
            var self = this;
            var $btn = $('#check-duplicates');
            var $result = $('#duplicate-check-result');
            var $content = $('#duplicate-check-content');
            
            this.setButtonLoading($btn, true, 'チェック中...');
            $result.hide();
            
            this.ajax('gi_check_duplicate_titles', {})
                .done(function(response) {
                    if (response.success) {
                        var data = response.data;
                        var html = self.buildDuplicateResultHtml(data);
                        
                        $content.html(html);
                        $result.removeClass('notice-success notice-warning notice-error');
                        $result.addClass(data.has_duplicates ? 'notice-warning' : 'notice-success');
                        $result.show();
                    } else {
                        self.showNotice('error', 'チェックに失敗しました');
                    }
                })
                .fail(function(xhr, status, error) {
                    self.showNotice('error', self.getErrorMessage(xhr, error));
                })
                .always(function() {
                    self.setButtonLoading($btn, false, '<span class="dashicons dashicons-search"></span> 重複チェック');
                });
        },

        /**
         * 重複結果HTMLの構築
         */
        buildDuplicateResultHtml: function(data) {
            var html = '';
            
            if (data.has_duplicates) {
                html += '<strong>⚠️ 重複タイトル: ' + data.count + ' グループ見つかりました</strong><br><br>';
                html += '<table style="width: 100%; border-collapse: collapse; font-size: 13px;">';
                html += '<thead><tr style="background: #f9f9f9;">';
                html += '<th style="padding: 8px; border: 1px solid #ddd; text-align: left;">タイトル</th>';
                html += '<th style="padding: 8px; border: 1px solid #ddd; text-align: center;">重複数</th>';
                html += '<th style="padding: 8px; border: 1px solid #ddd; text-align: left;">投稿ID / ステータス</th>';
                html += '</tr></thead><tbody>';
                
                var self = this;
                var displayCount = Math.min(data.duplicates.length, 20);
                
                for (var i = 0; i < displayCount; i++) {
                    var dup = data.duplicates[i];
                    var title = dup.title.length > 50 ? dup.title.substring(0, 50) + '...' : dup.title;
                    
                    html += '<tr>';
                    html += '<td style="padding: 8px; border: 1px solid #ddd;">' + self.escapeHtml(title) + '</td>';
                    html += '<td style="padding: 8px; border: 1px solid #ddd; text-align: center; font-weight: bold; color: #d63638;">' + dup.count + '</td>';
                    html += '<td style="padding: 8px; border: 1px solid #ddd;">';
                    
                    for (var j = 0; j < dup.posts.length; j++) {
                        if (j > 0) html += ', ';
                        html += 'ID ' + dup.posts[j].id + ' (' + dup.posts[j].status + ')';
                    }
                    
                    html += '</td></tr>';
                }
                
                html += '</tbody></table>';
                
                if (data.count > 20) {
                    html += '<p style="margin-top: 10px; color: #666;">※ 最初の20グループのみ表示。全件は「重複エクスポート」でシートに出力できます。</p>';
                }
                
                html += '<p style="margin-top: 10px;"><strong>💡 ヒント:</strong> 「重複エクスポート」ボタンでスプレッドシートに出力し、削除する投稿のステータスを「deleted」に変更後、同期を実行してください。</p>';
                
            } else {
                html = '<strong>✅ 重複タイトルはありません</strong><p>すべての投稿タイトルはユニークです。</p>';
            }
            
            return html;
        },

        /**
         * 重複エクスポート
         */
        exportDuplicates: function() {
            var self = this;
            
            if (!confirm('重複しているタイトルの投稿を「重複タイトル」シートにエクスポートします。\n\n続行しますか？')) {
                return;
            }
            
            var $btn = $('#export-duplicates');
            this.setButtonLoading($btn, true, 'エクスポート中...');
            
            this.ajax('gi_export_duplicate_titles', {}, { timeout: 120000 })
                .done(function(response) {
                    if (response.success) {
                        var data = response.data;
                        var message = data.message;
                        
                        if (data.spreadsheet_url) {
                            if (confirm(message + '\n\nスプレッドシートを開きますか？')) {
                                window.open(data.spreadsheet_url, '_blank');
                            }
                        } else {
                            self.showNotice('success', message);
                        }
                    } else {
                        self.showNotice('error', response.data || 'エクスポートに失敗しました');
                    }
                })
                .fail(function(xhr, status, error) {
                    self.showNotice('error', self.getErrorMessage(xhr, error));
                })
                .always(function() {
                    self.setButtonLoading($btn, false, '<span class="dashicons dashicons-media-spreadsheet"></span> 重複エクスポート');
                });
        },

        /**
         * 都道府県検証エクスポート
         */
        exportInvalidPrefectures: function() {
            var self = this;
            
            if (!confirm('都道府県データを検証し、問題のある投稿を「都道府県」シートにエクスポートします。\n\n続行しますか？')) {
                return;
            }
            
            var $btn = $('#export-invalid-prefectures');
            this.setButtonLoading($btn, true, '検証中...');
            
            this.ajax('gi_export_invalid_prefectures', {}, { timeout: 120000 })
                .done(function(response) {
                    if (response.success) {
                        self.showNotice('success', response.data.message);
                    } else {
                        self.showNotice('error', response.data || 'エクスポートに失敗しました');
                    }
                })
                .fail(function(xhr, status, error) {
                    self.showNotice('error', self.getErrorMessage(xhr, error));
                })
                .always(function() {
                    self.setButtonLoading($btn, false, '<span class="dashicons dashicons-location"></span> 都道府県検証');
                });
        },

        /**
         * タクソノミーエクスポート
         */
        exportTaxonomies: function() {
            var self = this;
            
            if (!confirm('カテゴリ、都道府県、市町村、タグをスプレッドシートにエクスポートします。\n\n続行しますか？')) {
                return;
            }
            
            var $btn = $('#export-taxonomies');
            this.setButtonLoading($btn, true, 'エクスポート中...');
            
            this.ajax('gi_export_taxonomies', {}, { timeout: 120000 })
                .done(function(response) {
                    if (response.success) {
                        var msg = response.data.message + '\n\n';
                        response.data.results.forEach(function(r) {
                            var status = r.success ? '✅' : '❌';
                            msg += status + ' ' + r.taxonomy + ': ' + r.count + '件\n';
                        });
                        alert(msg);
                    } else {
                        self.showNotice('error', 'エクスポートに失敗しました');
                    }
                })
                .fail(function(xhr, status, error) {
                    self.showNotice('error', self.getErrorMessage(xhr, error));
                })
                .always(function() {
                    self.setButtonLoading($btn, false, '<span class="dashicons dashicons-upload"></span> エクスポート');
                });
        },

        /**
         * タクソノミーインポート
         */
        importTaxonomies: function() {
            var self = this;
            
            if (!confirm('スプレッドシートからタクソノミーをインポートします。\n\n⚠️ 注意:\n- 既存のタクソノミーが更新される可能性があります\n- 削除する場合は名前列に「DELETE」または「削除」と入力してください\n\n続行しますか？')) {
                return;
            }
            
            var $btn = $('#import-taxonomies');
            this.setButtonLoading($btn, true, 'インポート中...');
            
            this.ajax('gi_import_taxonomies', {}, { timeout: 120000 })
                .done(function(response) {
                    if (response.success) {
                        var msg = response.data.message + '\n\n';
                        response.data.results.forEach(function(r) {
                            msg += r.taxonomy + ':\n';
                            msg += '  作成: ' + r.created + ', 更新: ' + r.updated + ', 削除: ' + r.deleted + ', スキップ: ' + r.skipped + '\n';
                            if (r.errors && r.errors.length > 0) {
                                msg += '  エラー: ' + r.errors.length + '件\n';
                            }
                        });
                        alert(msg);
                    } else {
                        self.showNotice('error', 'インポートに失敗しました');
                    }
                })
                .fail(function(xhr, status, error) {
                    self.showNotice('error', self.getErrorMessage(xhr, error));
                })
                .always(function() {
                    self.setButtonLoading($btn, false, '<span class="dashicons dashicons-download"></span> インポート');
                });
        },

        /**
         * ID範囲エクスポート
         */
        exportByIdRange: function() {
            var self = this;
            var $startId = $('#export-id-start');
            var $endId = $('#export-id-end');
            var $btn = $('#export-by-id-range');
            var $result = $('#id-range-export-result');
            var $message = $('#id-range-export-message');
            
            var startId = parseInt($startId.val());
            var endId = parseInt($endId.val());
            
            // バリデーション
            if (!startId || !endId || startId <= 0 || endId <= 0) {
                this.showNotice('error', '開始IDと終了IDを入力してください');
                return;
            }
            
            if (startId > endId) {
                this.showNotice('error', '開始IDは終了ID以下にしてください');
                return;
            }
            
            if (!confirm('ID ' + startId + ' 〜 ' + endId + ' の範囲の投稿をスプレッドシートにエクスポートしますか？')) {
                return;
            }
            
            this.setButtonLoading($btn, true, 'エクスポート中...');
            $result.hide();
            
            this.ajax('gi_export_posts_by_id_range', {
                start_id: startId,
                end_id: endId
            }, { timeout: 120000 })
                .done(function(response) {
                    if (response.success) {
                        var data = response.data;
                        var message = data.message || (data.count + ' 件の投稿をエクスポートしました');
                        
                        $message.text(message);
                        $result.removeClass('notice-error').addClass('notice-success').show();
                        
                        self.showNotice('success', message);
                        
                        // 入力フィールドをクリア
                        $startId.val('');
                        $endId.val('');
                    } else {
                        $message.text(response.data || 'エクスポートに失敗しました');
                        $result.removeClass('notice-success').addClass('notice-error').show();
                        self.showNotice('error', response.data || 'エクスポートに失敗しました');
                    }
                })
                .fail(function(xhr, status, error) {
                    var errorMsg = self.getErrorMessage(xhr, error);
                    $message.text(errorMsg);
                    $result.removeClass('notice-success').addClass('notice-error').show();
                    self.showNotice('error', errorMsg);
                })
                .always(function() {
                    self.setButtonLoading($btn, false, '<span class="dashicons dashicons-upload"></span> ID範囲エクスポート');
                });
        },

        /**
         * ログ更新
         */
        refreshLog: function() {
            location.reload();
        },

        /**
         * ログクリア
         */
        clearLog: function() {
            var self = this;
            
            if (!confirm('ログをクリアしますか？\n\nこの操作は取り消せません。')) {
                return;
            }
            
            var $btn = $('#clear-log');
            this.setButtonLoading($btn, true, 'クリア中...');
            
            this.ajax('gi_clear_sheets_log', {})
                .done(function(response) {
                    if (response.success) {
                        $('#sync-log').html('<div class="gi-log-entry">ログはまだありません</div>');
                        self.showNotice('success', response.data);
                    } else {
                        self.showNotice('error', response.data || 'クリアに失敗しました');
                    }
                })
                .fail(function(xhr, status, error) {
                    self.showNotice('error', self.getErrorMessage(xhr, error));
                })
                .always(function() {
                    self.setButtonLoading($btn, false, '<span class="dashicons dashicons-trash"></span> クリア');
                });
        },

        /**
         * AJAX ヘルパー
         */
        ajax: function(action, data, options) {
            var defaults = {
                timeout: 60000
            };
            
            options = $.extend({}, defaults, options);
            
            var requestData = $.extend({}, data, {
                action: action,
                nonce: giSheetsAdmin.nonce
            });
            
            return $.ajax({
                url: giSheetsAdmin.ajaxurl,
                type: 'POST',
                data: requestData,
                timeout: options.timeout
            });
        },

        /**
         * ボタンのローディング状態を設定
         */
        setButtonLoading: function($btn, isLoading, text) {
            if (isLoading) {
                $btn.data('original-html', $btn.html());
                $btn.prop('disabled', true);
                $btn.html('<span class="dashicons dashicons-update spin"></span> ' + text);
            } else {
                $btn.prop('disabled', false);
                $btn.html(text || $btn.data('original-html'));
            }
        },

        /**
         * 通知表示
         */
        showNotice: function(type, message) {
            var self = this;
            
            // 既存の通知を削除
            $('.gi-admin-notice').remove();
            
            var noticeClass = 'notice-' + (type === 'success' ? 'success' : (type === 'warning' ? 'warning' : 'error'));
            
            var $notice = $('<div class="notice ' + noticeClass + ' is-dismissible gi-admin-notice">' +
                '<p>' + this.escapeHtml(message) + '</p>' +
                '<button type="button" class="notice-dismiss"><span class="screen-reader-text">この通知を閉じる</span></button>' +
                '</div>');
            
            // 通知を挿入
            var $header = $('.gi-header');
            if ($header.length) {
                $header.after($notice);
            } else {
                $('.wrap h1').first().after($notice);
            }
            
            // 自動削除
            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, this.config.noticeDisplayTime);
            
            // 閉じるボタン
            $notice.find('.notice-dismiss').on('click', function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            });
        },

        /**
         * エラーメッセージ取得
         */
        getErrorMessage: function(xhr, error) {
            var message = '通信エラー';
            
            if (xhr.status === 0) {
                message = 'ネットワーク接続を確認してください';
            } else if (xhr.status === 500) {
                message = 'サーバーエラー (500): PHPのエラーログを確認してください。メモリ不足の可能性があります。';
            } else if (xhr.status === 504) {
                message = 'タイムアウト (504): 処理に時間がかかりすぎています。バッチサイズを小さくしてください。';
            } else if (error === 'timeout') {
                message = 'リクエストがタイムアウトしました。処理は継続している可能性があります。';
            } else if (error) {
                message = 'エラー: ' + error;
            }
            
            console.error('[GI Sheets Admin] Error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                error: error,
                responseText: xhr.responseText ? xhr.responseText.substring(0, 500) : ''
            });
            
            return message;
        },

        /**
         * HTMLエスケープ
         */
        escapeHtml: function(text) {
            if (!text) return '';
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    };

    // ドキュメント読み込み完了時に初期化
    $(document).ready(function() {
        GISheetsAdmin.init();
    });

    // グローバルアクセス用
    window.GISheetsAdmin = GISheetsAdmin;

})(jQuery);