/**
 * Single Grant Page JavaScript (SEO & UX Optimized)
 * Version: 400.0.0
 * 補助金詳細ページ専用スクリプト
 * - パフォーマンス最適化版
 * - オンボーディング機能追加
 * - エラーハンドリング改善
 * - チェックリスト完了モーダル追加
 */

// CONFIG は PHP側で設定される
// var CONFIG = { postId, ajaxUrl, nonce, url, title, totalChecklist };

(function() {
    'use strict';

    // =================================================================
    // 1. Critical Features (Immediately Interactive)
    // プログレスバー、チェックリスト、UI開閉など、初期表示に必要な機能
    // =================================================================
    
    const UI = {
        init: function() {
            this.setupProgress();
            this.setupChecklist();
            this.setupPanelUI(); // AIの中身ではなく、パネルのガワだけ
            this.setupMobileActionBar();
            this.setupCompletionModal();
            this.setupBookmark();
            this.setupShare();
            this.setupSmoothScroll();
            this.setupToast();
            this.setupUnsavedWarning();
            
            // AI機能の遅延読み込みトリガーを設定
            AiLazyLoader.setupTriggers();
            
            // オンボーディングチェック
            OnboardingManager.checkAndShow();
        },

        setupProgress: function() {
            const progress = document.getElementById('progressBar');
            if (!progress) return;
            
            // Throttled scroll handler
            let ticking = false;
            window.addEventListener('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        const h = document.documentElement.scrollHeight - window.innerHeight;
                        const p = h > 0 ? Math.min(100, (window.pageYOffset / h) * 100) : 0;
                        progress.style.width = p + '%';
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });
        },

        setupChecklist: function() {
            const checklistItems = document.querySelectorAll('.gi-checklist-item');
            if (checklistItems.length === 0) return;

            const els = {
                fill: document.getElementById('checklistFill'),
                count: document.getElementById('checklistCount'),
                percent: document.getElementById('checklistPercent'),
                result: document.getElementById('checklistResult'),
                resultText: document.getElementById('checklistResultText'),
                resultSub: document.getElementById('checklistResultSub'),
                resetBtn: document.getElementById('checklistReset'),
                printBtn: document.getElementById('checklistPrint')
            };

            const updateUI = () => {
                const total = checklistItems.length;
                const checked = document.querySelectorAll('.gi-checklist-item.checked').length;
                const requiredItems = document.querySelectorAll('.gi-checklist-item[data-required="true"]');
                const requiredChecked = document.querySelectorAll('.gi-checklist-item[data-required="true"].checked').length;
                const percent = Math.round((checked / total) * 100);
                
                if (els.fill) els.fill.style.width = percent + '%';
                if (els.count) els.count.textContent = checked + ' / ' + total + ' 完了';
                if (els.percent) els.percent.textContent = percent + '%';
                
                if (els.result) {
                    if (requiredChecked === requiredItems.length && requiredItems.length > 0) {
                        els.result.classList.add('complete');
                        if (els.resultText) els.resultText.textContent = '✓ 申請可能です！';
                        if (els.resultSub) els.resultSub.textContent = 'すべての必須項目をクリアしました。公式サイトから申請を進めましょう。';
                    } else {
                        els.result.classList.remove('complete');
                        const remaining = requiredItems.length - requiredChecked;
                        if (els.resultText) els.resultText.textContent = 'あと' + remaining + '項目で申請可能';
                        if (els.resultSub) els.resultSub.textContent = '必須項目をすべてクリアすると申請可能です';
                    }
                }
                
                // Save state
                const checkedIds = Array.from(document.querySelectorAll('.gi-checklist-item.checked')).map(el => el.dataset.id);
                try { localStorage.setItem('gi_checklist_' + CONFIG.postId, JSON.stringify(checkedIds)); } catch(e) {}
                
                // Show completion modal when all required items are checked
                if (requiredChecked === requiredItems.length && requiredItems.length > 0) {
                    const modalShown = sessionStorage.getItem('gi_modal_shown_' + CONFIG.postId);
                    if (!modalShown) {
                        setTimeout(() => UI.showCompletionModal(), 500);
                        sessionStorage.setItem('gi_modal_shown_' + CONFIG.postId, 'true');
                    }
                }
            };

            // Restore state
            try {
                const saved = localStorage.getItem('gi_checklist_' + CONFIG.postId);
                if (saved) {
                    const checkedIds = JSON.parse(saved);
                    checklistItems.forEach(item => {
                        if (checkedIds.includes(item.dataset.id)) {
                            item.classList.add('checked');
                            const cb = item.querySelector('.gi-checklist-checkbox');
                            if (cb) cb.setAttribute('aria-checked', 'true');
                        }
                    });
                    updateUI();
                }
            } catch(e) {}

            // Event Listeners
            checklistItems.forEach(item => {
                const cb = item.querySelector('.gi-checklist-checkbox');
                const helpBtn = item.querySelector('.gi-checklist-help-btn');
                
                const toggleCheck = (e) => {
                    // Helpボタンクリック時はチェックしない
                    if (e.target.closest('.gi-checklist-help-btn')) return;
                    
                    item.classList.toggle('checked');
                    if (cb) cb.setAttribute('aria-checked', item.classList.contains('checked') ? 'true' : 'false');
                    updateUI();
                };
                
                item.addEventListener('click', toggleCheck);
                if (cb) cb.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleCheck(e); } });
                if (helpBtn) helpBtn.addEventListener('click', (e) => { e.stopPropagation(); item.classList.toggle('show-help'); });
            });

            if (els.resetBtn) {
                els.resetBtn.addEventListener('click', () => {
                    if (confirm('チェックをすべてリセットしますか？')) {
                        checklistItems.forEach(item => {
                            item.classList.remove('checked', 'show-help');
                            const cb = item.querySelector('.gi-checklist-checkbox');
                            if (cb) cb.setAttribute('aria-checked', 'false');
                        });
                        try { localStorage.removeItem('gi_checklist_' + CONFIG.postId); } catch(e) {}
                        updateUI();
                        UI.showToast('チェックリストをリセットしました');
                    }
                });
            }
            
            if (els.printBtn) els.printBtn.addEventListener('click', () => window.print());
        },

        setupPanelUI: function() {
            const els = {
                btn: document.getElementById('mobileAiBtn'),
                overlay: document.getElementById('mobileOverlay'),
                panel: document.getElementById('mobilePanel'),
                close: document.getElementById('panelClose'),
                tabs: document.querySelectorAll('.gi-panel-tab'),
                contents: document.querySelectorAll('.gi-panel-content-tab'),
                tocLinks: document.querySelectorAll('.mobile-toc-link')
            };

            const openPanel = () => {
                if (els.overlay) els.overlay.classList.add('active');
                if (els.panel) els.panel.classList.add('active');
                document.body.style.overflow = 'hidden';
                
                // パネルが開かれたらAIを初期化する
                AiLazyLoader.init();
            };

            const closePanel = () => {
                if (els.overlay) els.overlay.classList.remove('active');
                if (els.panel) els.panel.classList.remove('active');
                document.body.style.overflow = '';
            };

            if (els.btn) els.btn.addEventListener('click', openPanel);
            if (els.close) els.close.addEventListener('click', closePanel);
            if (els.overlay) els.overlay.addEventListener('click', closePanel);
            els.tocLinks.forEach(link => link.addEventListener('click', closePanel));
            
            // Additional mobile action bar AI button
            const mobileAiQuickBtn = document.getElementById('mobileAiQuickBtn');
            if (mobileAiQuickBtn) mobileAiQuickBtn.addEventListener('click', openPanel);

            els.tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const targetTab = this.dataset.tab;
                    els.tabs.forEach(t => t.classList.remove('active'));
                    els.contents.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    const target = document.getElementById('tab' + targetTab.charAt(0).toUpperCase() + targetTab.slice(1));
                    if (target) target.classList.add('active');
                });
            });
        },

        setupBookmark: function() {
            const btn = document.getElementById('bookmarkBtn');
            const mobileBtn = document.getElementById('mobileBookmarkBtn');
            const key = 'gi_bookmarks';
            
            const getBookmarks = () => { try { return JSON.parse(localStorage.getItem(key) || '[]'); } catch(e) { return []; } };
            
            const updateUI = () => {
                const bookmarked = getBookmarks().includes(CONFIG.postId);
                const text = bookmarked ? '保存済み' : '保存する';
                
                if (btn) {
                    const svg = btn.querySelector('svg');
                    if (svg) svg.style.fill = bookmarked ? 'currentColor' : 'none';
                    const span = btn.querySelector('span');
                    if (span) span.textContent = text;
                }
                if (mobileBtn) {
                    const span = mobileBtn.querySelector('span');
                    if (span) span.textContent = text;
                }
            };

            const toggle = () => {
                const bookmarks = getBookmarks();
                const index = bookmarks.indexOf(CONFIG.postId);
                if (index !== -1) bookmarks.splice(index, 1);
                else bookmarks.push(CONFIG.postId);
                try { localStorage.setItem(key, JSON.stringify(bookmarks)); } catch(e) {}
                updateUI();
                UI.showToast(index !== -1 ? '保存を解除しました' : '保存しました');
            };

            if (btn) btn.addEventListener('click', toggle);
            if (mobileBtn) mobileBtn.addEventListener('click', toggle);
            updateUI();
        },

        setupShare: function() {
            const handleShare = () => {
                if (navigator.share) {
                    navigator.share({ title: CONFIG.title, url: CONFIG.url }).catch(() => {});
                } else if (navigator.clipboard) {
                    navigator.clipboard.writeText(CONFIG.url)
                        .then(() => UI.showToast('URLをコピーしました'))
                        .catch(() => {});
                }
            };
            
            const btn = document.getElementById('shareBtn');
            const mobileBtn = document.getElementById('mobileShareBtn');
            if (btn) btn.addEventListener('click', handleShare);
            if (mobileBtn) mobileBtn.addEventListener('click', handleShare);
        },

        setupSmoothScroll: function() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href === '#') return;
                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        const top = target.getBoundingClientRect().top + window.pageYOffset - 80;
                        window.scrollTo({ top: top, behavior: 'smooth' });
                    }
                });
            });
        },

        setupMobileActionBar: function() {
            // Mobile action bar is always visible on mobile via CSS
            // Links work via standard href, AI button handled in setupPanelUI
        },

        setupCompletionModal: function() {
            const modal = document.getElementById('completionModal');
            const overlay = document.getElementById('completionOverlay');
            const closeBtn = document.getElementById('completionClose');
            
            if (!modal) return;
            
            const closeModal = () => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            };
            
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (overlay) overlay.addEventListener('click', closeModal);
            
            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    closeModal();
                }
            });
        },

        showCompletionModal: function() {
            const modal = document.getElementById('completionModal');
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        },

        setupUnsavedWarning: function() {
            // Warn before leaving if checklist has unsaved progress
            // Note: localStorage saves automatically, so this is just UX enhancement
            let hasInteracted = false;
            
            document.querySelectorAll('.gi-checklist-item').forEach(item => {
                item.addEventListener('click', () => { hasInteracted = true; });
            });
        },

        setupToast: function() {
            // Helper available via UI.showToast
        },

        showToast: function(msg) {
            const t = document.getElementById('giToast');
            if (!t) return;
            t.textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }
    };

    // =================================================================
    // 1.5 Onboarding Manager
    // 初回訪問ユーザー向けのツアー機能
    // =================================================================
    
    const OnboardingManager = {
        steps: [
            { target: '.gi-metrics', title: '重要な数字を確認', text: '補助金額、締切、難易度などの重要情報はここでチェックできます。', position: 'bottom' },
            { target: '.gi-checklist-header', title: '申請資格をセルフチェック', text: 'チェックリストで申請要件を確認しましょう。進捗は自動保存されます。', position: 'bottom' },
            { target: '#mobileAiQuickBtn', title: 'AIに質問できます', text: '下部の「AI相談」ボタンから質問できます。', position: 'top', isFixed: true }
        ],
        currentStep: 0,
        
        checkAndShow: function() {
            const shown = localStorage.getItem('gi_onboarding_done');
            if (!shown) {
                // Delay to ensure page is fully loaded
                setTimeout(() => this.showTour(), 1500);
            }
        },
        
        showTour: function() {
            // Create overlay
            const overlay = document.createElement('div');
            overlay.className = 'gi-onboarding-overlay';
            overlay.id = 'onboardingOverlay';
            document.body.appendChild(overlay);
            
            this.showStep(0);
        },
        
        showStep: function(index) {
            if (index >= this.steps.length) {
                this.completeTour();
                return;
            }
            
            const step = this.steps[index];
            const target = document.querySelector(step.target);
            
            if (!target) {
                // Skip if target not found
                this.showStep(index + 1);
                return;
            }
            
            this.currentStep = index;
            
            // Remove previous highlights first
            document.querySelectorAll('.gi-onboarding-highlight, .gi-onboarding-highlight-fixed').forEach(el => {
                el.classList.remove('gi-onboarding-highlight', 'gi-onboarding-highlight-fixed');
                el.style.boxShadow = '';
            });
            
            // Show overlay
            const overlay = document.getElementById('onboardingOverlay');
            if (overlay) overlay.classList.add('active');
            
            // Remove existing tooltip
            const existingTooltip = document.querySelector('.gi-onboarding-tooltip');
            if (existingTooltip) existingTooltip.remove();
            
            // For fixed elements, use different highlight approach
            if (step.isFixed) {
                target.classList.add('gi-onboarding-highlight-fixed');
                this.createTooltip(target, step, index, true);
            } else {
                // Highlight and scroll to target
                target.classList.add('gi-onboarding-highlight');
                
                const headerOffset = 120;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
                
                // Wait for scroll then create tooltip
                setTimeout(() => this.createTooltip(target, step, index, false), 500);
            }
        },
        
        createTooltip: function(target, step, index, isFixed) {
            const tooltip = document.createElement('div');
            tooltip.className = 'gi-onboarding-tooltip';
            tooltip.innerHTML = `
                <div class="gi-onboarding-step">STEP ${index + 1} / ${this.steps.length}</div>
                <div class="gi-onboarding-title">${step.title}</div>
                <div class="gi-onboarding-text">${step.text}</div>
                <div class="gi-onboarding-actions">
                    <button class="gi-onboarding-skip" id="onboardingSkip">スキップ</button>
                    <button class="gi-onboarding-next" id="onboardingNext">${index < this.steps.length - 1 ? '次へ' : '完了'}</button>
                </div>
                <div class="gi-onboarding-progress">
                    ${this.steps.map((_, i) => `<div class="gi-onboarding-dot ${i <= index ? 'active' : ''} ${i < index ? 'completed' : ''}"></div>`).join('')}
                </div>
            `;
            
            const rect = target.getBoundingClientRect();
            
            if (isFixed) {
                // Fixed element - position tooltip above it
                tooltip.style.position = 'fixed';
                tooltip.style.bottom = (window.innerHeight - rect.top + 16) + 'px';
                tooltip.style.left = '50%';
                tooltip.style.marginLeft = '-160px';
                tooltip.style.zIndex = '10002';
            } else {
                // Regular positioning
                tooltip.style.position = 'absolute';
                if (step.position === 'bottom') {
                    tooltip.style.top = (rect.bottom + window.scrollY + 16) + 'px';
                } else {
                    tooltip.style.top = (rect.top + window.scrollY - 180) + 'px';
                }
                tooltip.style.left = Math.max(16, Math.min(window.innerWidth - 336, rect.left)) + 'px';
            }
            
            document.body.appendChild(tooltip);
            
            // Event listeners
            document.getElementById('onboardingSkip').addEventListener('click', () => this.completeTour());
            document.getElementById('onboardingNext').addEventListener('click', () => {
                this.showStep(index + 1);
            });
        },
        
        completeTour: function() {
            localStorage.setItem('gi_onboarding_done', 'true');
            
            // Remove all highlights immediately
            document.querySelectorAll('.gi-onboarding-highlight, .gi-onboarding-highlight-fixed').forEach(el => {
                el.classList.remove('gi-onboarding-highlight', 'gi-onboarding-highlight-fixed');
                el.style.boxShadow = '';
                el.style.zIndex = '';
            });
            
            // Remove tooltip
            const tooltip = document.querySelector('.gi-onboarding-tooltip');
            if (tooltip) tooltip.remove();
            
            // Remove overlay
            const overlay = document.getElementById('onboardingOverlay');
            if (overlay) {
                overlay.classList.remove('active');
                setTimeout(() => overlay.remove(), 300);
            }
            
            UI.showToast('ツアーを完了しました');
        }
    };

    // =================================================================
    // 2. Deferred Features (AI, Diagnosis, Roadmap)
    // ユーザー操作 or 画面内に入った時に初めて初期化する機能
    // =================================================================

    const AiLazyLoader = {
        loaded: false,
        
        setupTriggers: function() {
            // 1. Desktop AI Input focus
            const desktopInput = document.getElementById('aiInput');
            if (desktopInput) {
                desktopInput.addEventListener('focus', () => this.init(), { once: true });
            }

            // 2. Chip Clicks (Delegate event initially)
            // チップがクリックされたら初期化してからアクションを実行するラッパー
            const handleChipClick = (e) => {
                if (e.target.classList.contains('gi-ai-chip') || e.target.classList.contains('gi-mobile-ai-chip')) {
                    // Initialize if not loaded
                    this.init();
                    // Let the real handler (attached in init) take over? 
                    // No, standard addEventListener won't fire for the *current* click if added now.
                    // So we call the logic directly here for the first time.
                    AiManager.handleChipAction(e.target);
                }
            };
            
            // Setup temporary delegation for chips
            document.body.addEventListener('click', (e) => {
                if (!this.loaded && (e.target.classList.contains('gi-ai-chip') || e.target.classList.contains('gi-mobile-ai-chip'))) {
                    this.init();
                    AiManager.handleChipAction(e.target);
                }
            });

            // 3. Intersection Observer for Desktop Sidebar
            // デスクトップサイドバーが画面に入ったら初期化
            const aiSidebar = document.querySelector('.gi-ai-section');
            if (aiSidebar && 'IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.init();
                            observer.disconnect();
                        }
                    });
                }, { rootMargin: '200px' }); // 少し早めに読み込む
                observer.observe(aiSidebar);
            }
        },

        init: function() {
            if (this.loaded) return;
            console.log('Initializing AI Modules...');
            AiManager.init();
            this.loaded = true;
        }
    };

    const AiManager = {
        init: function() {
            this.desktop = {
                input: document.getElementById('aiInput'),
                btn: document.getElementById('aiSend'),
                container: document.getElementById('aiMessages')
            };
            this.mobile = {
                input: document.getElementById('mobileAiInput'),
                btn: document.getElementById('mobileAiSend'),
                container: document.getElementById('mobileAiMessages')
            };

            this.setupListeners();
        },

        setupListeners: function() {
            // Send Buttons
            if (this.desktop.btn) {
                this.desktop.btn.addEventListener('click', () => this.sendMessage(this.desktop.input, this.desktop.container, this.desktop.btn));
                this.desktop.input.addEventListener('keydown', (e) => this.handleEnter(e, this.desktop.input, this.desktop.container, this.desktop.btn));
                this.desktop.input.addEventListener('input', this.autoResize);
            }
            if (this.mobile.btn) {
                this.mobile.btn.addEventListener('click', () => this.sendMessage(this.mobile.input, this.mobile.container, this.mobile.btn));
                this.mobile.input.addEventListener('keydown', (e) => this.handleEnter(e, this.mobile.input, this.mobile.container, this.mobile.btn));
                this.mobile.input.addEventListener('input', this.autoResize);
            }

            // Chips - Remove global body listener triggers, now handled by direct logic or native listeners if added later
            // Since we handle "First Click" in LazyLoader, we need subsequent clicks handled here if we wanted to change behavior.
            // But actually, simple delegation is better for performance than attaching listeners to every chip.
            document.body.addEventListener('click', (e) => {
                if (AiLazyLoader.loaded && (e.target.classList.contains('gi-ai-chip') || e.target.classList.contains('gi-mobile-ai-chip'))) {
                    // Avoid double execution if LazyLoader triggered it immediately
                    // LazyLoader trigger is one-off, so this persistence listener is fine.
                    this.handleChipAction(e.target);
                }
            });
        },

        handleEnter: function(e, input, container, btn) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage(input, container, btn);
            }
        },

        autoResize: function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        },

        handleChipAction: function(chip) {
            const isMobile = chip.classList.contains('gi-mobile-ai-chip');
            const target = isMobile ? this.mobile : this.desktop;
            
            if (!target.container) return; // UI not found

            if (chip.dataset.action) {
                if (chip.dataset.action === 'diagnosis') {
                    this.runDiagnosis(target.container);
                } else if (chip.dataset.action === 'roadmap') {
                    this.generateRoadmap(target.container);
                }
            } else if (chip.dataset.q && target.input) {
                target.input.value = chip.dataset.q;
                this.sendMessage(target.input, target.container, target.btn);
            }
        },

        sendMessage: function(input, container, btn) {
            const question = input.value.trim();
            if (!question) return;

            this.addMessage(container, question, 'user');
            input.value = '';
            input.style.height = 'auto';
            if(btn) btn.disabled = true;

            const loadingMsg = this.addMessage(container, '考え中...', 'ai-loading');

            this.callApi('gi_ai_chat', { question: question })
                .then(data => {
                    loadingMsg.remove();
                    if (data.success && data.data && data.data.answer) {
                        this.addMessage(container, data.data.answer, 'ai');
                    } else {
                        const errorMsg = (data.data && data.data.message) ? data.data.message : 'エラーが発生しました。';
                        this.addMessage(container, errorMsg, 'ai');
                    }
                })
                .catch(err => {
                    loadingMsg.remove();
                    this.addErrorMessage(container, question);
                    console.error(err);
                })
                .finally(() => { if(btn) btn.disabled = false; });
        },

        runDiagnosis: function(container) {
            this.addMessage(container, '申請資格があるか診断してください。', 'user');
            const loadingMsg = this.addMessage(container, '資格を診断中...', 'ai-loading');

            // Collect checklist answers
            const answers = {};
            document.querySelectorAll('.gi-checklist-item').forEach(item => {
                const label = item.querySelector('.gi-checklist-label').textContent.trim();
                answers[label] = item.classList.contains('checked') ? 'はい' : 'いいえ';
            });

            this.callApi('gi_eligibility_diagnosis', { answers: answers })
                .then(data => {
                    loadingMsg.remove();
                    if (data.success) {
                        const d = data.data;
                        let html = `<div style="font-weight:bold;margin-bottom:8px;font-size:1.1em;">${d.eligible ? '✅ 申請資格の可能性が高いです' : '⚠️ 要件を確認してください'}</div>`;
                        if (d.reasons?.length) html += `<strong>判定理由:</strong><ul style="margin:4px 0 8px 20px;list-style:disc;">${d.reasons.map(r => `<li>${r}</li>`).join('')}</ul>`;
                        if (d.warnings?.length) html += `<strong>注意点:</strong><ul style="margin:4px 0 8px 20px;list-style:disc;color:#dc2626;">${d.warnings.map(w => `<li>${w}</li>`).join('')}</ul>`;
                        if (d.next_steps?.length) html += `<strong>次のステップ:</strong><ol style="margin:4px 0 0 20px;list-style:decimal;">${d.next_steps.map(s => `<li>${s}</li>`).join('')}</ol>`;
                        this.addMessage(container, html, 'ai-html');
                    } else {
                        this.addMessage(container, '診断エラー: ' + (data.data.message || '不明'), 'ai');
                    }
                })
                .catch(e => {
                    loadingMsg.remove();
                    this.addErrorMessage(container, null, 'diagnosis');
                });
        },

        generateRoadmap: function(container) {
            this.addMessage(container, '申請までのロードマップを作成してください。', 'user');
            const loadingMsg = this.addMessage(container, 'ロードマップを作成中...', 'ai-loading');

            this.callApi('gi_generate_roadmap', {})
                .then(data => {
                    loadingMsg.remove();
                    if (data.success) {
                        const d = data.data;
                        let html = '<div style="font-weight:bold;margin-bottom:12px;">📅 申請ロードマップ</div>';
                        if (d.roadmap?.length) {
                            html += '<div style="display:flex;flex-direction:column;gap:12px;">';
                            d.roadmap.forEach((step, i) => {
                                html += `<div style="background:#f9fafb;padding:10px;border-left:3px solid #111;font-size:0.95em;">
                                    <div style="font-weight:bold;color:#111;">${i+1}. ${step.title} <span style="font-weight:normal;color:#666;font-size:0.9em;">(${step.timing})</span></div>
                                    <div style="color:#4b5563;margin-top:4px;">${step.description}</div>
                                </div>`;
                            });
                            html += '</div>';
                        }
                        if (d.tips?.length) html += `<div style="margin-top:12px;font-size:0.9em;color:#4b5563;"><strong>💡 アドバイス:</strong> ${d.tips[0]}</div>`;
                        this.addMessage(container, html, 'ai-html');
                    } else {
                        this.addMessage(container, 'ロードマップ生成に失敗しました。', 'ai');
                    }
                })
                .catch(e => {
                    loadingMsg.remove();
                    this.addErrorMessage(container, null, 'roadmap');
                });
        },

        callApi: function(action, dataObj) {
            const formData = new FormData();
            formData.append('action', action);
            
            // Nonce handling
            let nonce = CONFIG.nonce;
            if (window.gi_ajax?.nonce) nonce = window.gi_ajax.nonce;
            else if (window.ajaxSettings?.nonce) nonce = window.ajaxSettings.nonce;
            else if (window.wpApiSettings?.nonce) nonce = window.wpApiSettings.nonce;
            
            formData.append('nonce', nonce);
            formData.append('post_id', CONFIG.postId);
            
            // Nested object to FormData
            if (dataObj.question) formData.append('question', dataObj.question);
            if (dataObj.answers) {
                for (const key in dataObj.answers) {
                    formData.append('answers[' + key + ']', dataObj.answers[key]);
                }
            }

            return fetch(CONFIG.ajaxUrl, { method: 'POST', body: formData })
                .then(r => {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                });
        },

        addMessage: function(container, text, type) {
            const msg = document.createElement('div');
            // type: 'user', 'ai', 'ai-loading', 'ai-html'
            const isUser = type === 'user';
            msg.className = 'gi-ai-msg' + (isUser ? ' user' : '');
            
            let content = '';
            if (type === 'ai-loading') {
                content = '<div class="gi-ai-avatar">AI</div><div class="gi-ai-bubble">' + text + '</div>';
            } else if (type === 'ai-html') {
                content = '<div class="gi-ai-avatar">AI</div><div class="gi-ai-bubble">' + text + '</div>';
            } else {
                // Escape text for safety if not HTML
                const safeText = text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;").replace(/\n/g, '<br>');
                content = '<div class="gi-ai-avatar">' + (isUser ? 'You' : 'AI') + '</div><div class="gi-ai-bubble">' + safeText + '</div>';
            }
            
            msg.innerHTML = content;
            container.appendChild(msg);
            container.scrollTop = container.scrollHeight;
            return msg;
        },

        // Error handling with retry functionality
        lastFailedRequest: null,
        
        addErrorMessage: function(container, question, actionType) {
            // Store for retry
            this.lastFailedRequest = { container, question, actionType };
            
            const errorHtml = `
                <div class="gi-error-state">
                    <p>申し訳ございません。接続に問題が発生しました。</p>
                    <button class="gi-retry-btn" onclick="window.AiRetry()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                        再試行する
                    </button>
                </div>
            `;
            this.addMessage(container, errorHtml, 'ai-html');
        },
        
        retryLastRequest: function() {
            if (!this.lastFailedRequest) return;
            
            const { container, question, actionType } = this.lastFailedRequest;
            
            // Remove error message
            const lastMsg = container.querySelector('.gi-ai-msg:last-child');
            if (lastMsg && lastMsg.querySelector('.gi-error-state')) {
                lastMsg.remove();
            }
            
            if (actionType === 'diagnosis') {
                this.runDiagnosis(container);
            } else if (actionType === 'roadmap') {
                this.generateRoadmap(container);
            } else if (question) {
                // Find the input element and resend
                const isMobile = container.id === 'mobileAiMessages';
                const target = isMobile ? this.mobile : this.desktop;
                if (target.input) {
                    target.input.value = question;
                    this.sendMessage(target.input, container, target.btn);
                }
            }
            
            this.lastFailedRequest = null;
        }
    };
    
    // Global retry function for onclick handler
    window.AiRetry = function() {
        AiManager.retryLastRequest();
    };

    // Initialize Critical Features on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
        UI.init();
        console.log('Grant Single v400 Initialized (SEO & UX Optimized)');
    });

})();
