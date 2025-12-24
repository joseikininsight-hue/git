(function() {
    'use strict';
    
    const init = () => {
        setupBackgroundImageLoading();
        setupCTATracking();
        setupKeyboardNavigation();
    };
    
    /**
     * 背景画像の読み込み処理
     */
    const setupBackgroundImageLoading = () => {
        const bgImage = document.querySelector('.hero__bg-image');
        if (!bgImage) return;
        
        const handleLoad = () => {
            bgImage.classList.add('is-loaded');
            bgImage.style.opacity = '1';
        };
        
        if (bgImage.complete) {
            handleLoad();
        } else {
            bgImage.style.opacity = '0';
            bgImage.style.transition = 'opacity 0.5s ease';
            bgImage.addEventListener('load', handleLoad, { once: true });
        }
    };
    
    /**
     * CTAクリックトラッキング
     */
    const setupCTATracking = () => {
        const primaryCTA = document.querySelector('.hero__btn--primary');
        const secondaryCTA = document.querySelector('.hero__btn--secondary');
        
        const trackClick = (label, destination) => {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'cta_click', {
                    'event_category': 'hero_section',
                    'event_label': label,
                    'destination': destination,
                    'transport_type': 'beacon'
                });
            }
            
            if (typeof dataLayer !== 'undefined') {
                dataLayer.push({
                    'event': 'hero_cta_click',
                    'cta_label': label,
                    'cta_destination': destination
                });
            }
        };
        
        if (primaryCTA) {
            primaryCTA.addEventListener('click', () => {
                trackClick('primary_search', primaryCTA.href);
            });
        }
        
        if (secondaryCTA) {
            secondaryCTA.addEventListener('click', () => {
                trackClick('secondary_diagnosis', secondaryCTA.href);
            });
        }
    };
    
    /**
     * キーボードナビゲーション
     */
    const setupKeyboardNavigation = () => {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });
        
        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-navigation');
        });
    };
    
    // DOMContentLoaded時に初期化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
