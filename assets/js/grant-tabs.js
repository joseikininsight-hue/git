/**
 * Grant & Column Information Hub - Tab Controller v38.0
 * シンプルなタブ切り替えロジック
 * @version 38.0.0 - Clean Government Style
 */
(function() {
    'use strict';

    // ==========================================================================
    // Configuration
    // ==========================================================================
    const CONFIG = {
        selectors: {
            tabButtons: '.grant-tabs__btn',
            tabPanels: '.grant-tabs__panel'
        },
        classes: {
            active: 'active'
        }
    };

    // ==========================================================================
    // Tab Switching
    // ==========================================================================
    function initTabs() {
        const buttons = document.querySelectorAll(CONFIG.selectors.tabButtons);
        const panels = document.querySelectorAll(CONFIG.selectors.tabPanels);

        if (buttons.length === 0 || panels.length === 0) {
            return;
        }

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                switchTab(tabName, buttons, panels);
            });

            // Keyboard navigation
            button.addEventListener('keydown', function(e) {
                const buttonArray = Array.from(buttons);
                const currentIndex = buttonArray.indexOf(this);
                let newIndex;

                if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    newIndex = (currentIndex + 1) % buttonArray.length;
                } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    newIndex = (currentIndex - 1 + buttonArray.length) % buttonArray.length;
                } else if (e.key === 'Home') {
                    e.preventDefault();
                    newIndex = 0;
                } else if (e.key === 'End') {
                    e.preventDefault();
                    newIndex = buttonArray.length - 1;
                }

                if (newIndex !== undefined) {
                    buttonArray[newIndex].click();
                    buttonArray[newIndex].focus();
                }
            });
        });
    }

    function switchTab(tabName, buttons, panels) {
        // Reset all buttons
        buttons.forEach(btn => {
            btn.classList.remove(CONFIG.classes.active);
            btn.setAttribute('aria-selected', 'false');
        });

        // Hide all panels
        panels.forEach(panel => {
            panel.hidden = true;
            panel.classList.remove(CONFIG.classes.active);
        });

        // Activate selected button
        const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
        if (activeButton) {
            activeButton.classList.add(CONFIG.classes.active);
            activeButton.setAttribute('aria-selected', 'true');
        }

        // Show selected panel
        const activePanel = document.getElementById(`panel-${tabName}`);
        if (activePanel) {
            activePanel.hidden = false;
            activePanel.classList.add(CONFIG.classes.active);
        }

        // Track event
        trackEvent('tab_switch', 'engagement', tabName);
    }

    // ==========================================================================
    // Analytics
    // ==========================================================================
    function trackEvent(action, category, label) {
        if (typeof gtag !== 'undefined') {
            gtag('event', action, {
                'event_category': category,
                'event_label': label
            });
        }

        if (typeof dataLayer !== 'undefined') {
            dataLayer.push({
                'event': action,
                'eventCategory': category,
                'eventLabel': label
            });
        }
    }

    // ==========================================================================
    // Initialize
    // ==========================================================================
    function init() {
        initTabs();
        console.log('✅ Grant & Column Hub v38.0 Initialized');
    }

    // Run on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
