/**
 * Header JavaScript - Zukan Style
 * 補助金図鑑 - Perfect Header
 * 
 * @package Joseikin_Insight_Header
 * @version 15.1.0 (Zukan Edition)
 */

(function() {
    'use strict';
    
    const header = document.getElementById('ji-header');
    const searchToggle = document.getElementById('ji-search-toggle');
    const searchPanel = document.getElementById('ji-search-panel');
    const searchInput = document.getElementById('ji-search-input');
    const mobileToggle = document.getElementById('ji-mobile-toggle');
    const mobileMenu = document.getElementById('ji-mobile-menu');
    const mobileClose = document.getElementById('ji-mobile-close');
    const mobileSearchInput = document.getElementById('ji-mobile-search-input');
    const navItems = document.querySelectorAll('.ji-nav-item[data-menu]');
    
    let lastScrollY = 0;
    let isSearchOpen = false;
    let isMobileMenuOpen = false;
    let ticking = false;
    
    /**
     * Initialize Mega Menus
     */
    function initMegaMenus() {
        navItems.forEach(item => {
            const link = item.querySelector('.ji-nav-link');
            const menu = item.querySelector('.ji-mega-menu');
            
            if (!menu || !link) return;
            
            link.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const isExpanded = item.classList.contains('menu-active');
                
                // Close other menus
                navItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('menu-active');
                        const otherLink = otherItem.querySelector('.ji-nav-link');
                        if (otherLink) otherLink.setAttribute('aria-expanded', 'false');
                    }
                });
                
                // Toggle current menu
                if (isExpanded) {
                    item.classList.remove('menu-active');
                    link.setAttribute('aria-expanded', 'false');
                } else {
                    item.classList.add('menu-active');
                    link.setAttribute('aria-expanded', 'true');
                }
            });
            
            // Keyboard navigation
            link.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    link.click();
                }
                if (e.key === 'Escape') {
                    item.classList.remove('menu-active');
                    link.setAttribute('aria-expanded', 'false');
                    link.focus();
                }
            });
        });
        
        // Close menus when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.ji-nav-item')) {
                navItems.forEach(item => {
                    item.classList.remove('menu-active');
                    const link = item.querySelector('.ji-nav-link');
                    if (link) link.setAttribute('aria-expanded', 'false');
                });
            }
        });
    }
    
    /**
     * Handle Scroll Events
     */
    function handleScroll() {
        const scrollY = window.scrollY;
        
        // Add scrolled class
        if (scrollY > 50) {
            header?.classList.add('scrolled');
        } else {
            header?.classList.remove('scrolled');
        }
        
        // Hide/show header on scroll
        if (scrollY > 100) {
            if (scrollY > lastScrollY + 3) {
                header?.classList.add('hidden');
                // Close mega menus when hiding header
                navItems.forEach(item => {
                    item.classList.remove('menu-active');
                    const link = item.querySelector('.ji-nav-link');
                    if (link) link.setAttribute('aria-expanded', 'false');
                });
            } 
            else if (scrollY < lastScrollY - 3) {
                header?.classList.remove('hidden');
            }
        } else {
            header?.classList.remove('hidden');
        }
        
        lastScrollY = scrollY;
        ticking = false;
    }
    
    /**
     * Request Animation Frame for Scroll
     */
    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(handleScroll);
            ticking = true;
        }
    }
    
    /**
     * Toggle Search Panel
     */
    function toggleSearch() {
        isSearchOpen = !isSearchOpen;
        searchPanel?.classList.toggle('open', isSearchOpen);
        
        // Close mega menus
        navItems.forEach(item => {
            item.classList.remove('menu-active');
            const link = item.querySelector('.ji-nav-link');
            if (link) link.setAttribute('aria-expanded', 'false');
        });
        
        // Update toggle button
        if (searchToggle) {
            searchToggle.setAttribute('aria-expanded', isSearchOpen);
            searchToggle.innerHTML = isSearchOpen 
                ? '<i class="fas fa-times" aria-hidden="true"></i>'
                : '<i class="fas fa-search" aria-hidden="true"></i>';
        }
        
        // Focus search input when opening
        if (isSearchOpen && searchInput) {
            setTimeout(() => searchInput.focus(), 150);
        }
    }
    
    /**
     * Close Search Panel
     */
    function closeSearch() {
        if (!isSearchOpen) return;
        isSearchOpen = false;
        searchPanel?.classList.remove('open');
        if (searchToggle) {
            searchToggle.setAttribute('aria-expanded', 'false');
            searchToggle.innerHTML = '<i class="fas fa-search" aria-hidden="true"></i>';
        }
    }
    
    /**
     * Open Mobile Menu
     */
    function openMobileMenu() {
        isMobileMenuOpen = true;
        mobileMenu?.classList.add('open');
        mobileToggle?.setAttribute('aria-expanded', 'true');
        document.body.classList.add('menu-open');
        setTimeout(() => mobileClose?.focus(), 100);
    }
    
    /**
     * Close Mobile Menu
     */
    function closeMobileMenu() {
        if (!isMobileMenuOpen) return;
        isMobileMenuOpen = false;
        mobileMenu?.classList.remove('open');
        mobileToggle?.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('menu-open');
        mobileToggle?.focus();
    }
    
    /**
     * Initialize Mobile Accordions
     */
    function initAccordions() {
        document.querySelectorAll('.ji-mobile-accordion-trigger').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
                const contentId = trigger.getAttribute('aria-controls');
                const content = document.getElementById(contentId);
                
                trigger.setAttribute('aria-expanded', !isExpanded);
                content?.classList.toggle('open', !isExpanded);
            });
        });
    }
    
    /**
     * Initialize Search Suggestions
     */
    function initSearchSuggestions() {
        document.querySelectorAll('.ji-search-suggestion').forEach(btn => {
            btn.addEventListener('click', (e) => {
                // If it's a link (a tag), let it navigate naturally
                if (btn.tagName === 'A') return;
                
                // If it's a button or span acting as a suggestion filler
                if (searchInput) {
                    searchInput.value = btn.innerText;
                    searchInput.focus();
                }
            });
        });
    }
    
    /**
     * Initialize Mobile Search
     */
    function initMobileSearch() {
        if (mobileSearchInput) {
            mobileSearchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = mobileSearchInput.value.trim();
                    if (query) {
                        // Get grants URL from data attribute or use default
                        const grantsUrl = document.body.dataset.grantsUrl || '/grants/';
                        window.location.href = grantsUrl + '?s=' + encodeURIComponent(query) + '&post_type=grant';
                    }
                }
            });
        }
    }
    
    /**
     * Trap Focus within Element (Accessibility)
     */
    function trapFocus(element) {
        const focusableElements = element.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        element.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                if (e.shiftKey && document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                } else if (!e.shiftKey && document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        });
    }
    
    // Event Listeners
    window.addEventListener('scroll', requestTick, { passive: true });
    
    searchToggle?.addEventListener('click', toggleSearch);
    mobileToggle?.addEventListener('click', openMobileMenu);
    mobileClose?.addEventListener('click', closeMobileMenu);
    
    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (isMobileMenuOpen) closeMobileMenu();
            else if (isSearchOpen) closeSearch();
            else {
                navItems.forEach(item => {
                    item.classList.remove('menu-active');
                    const link = item.querySelector('.ji-nav-link');
                    if (link) link.setAttribute('aria-expanded', 'false');
                });
            }
        }
        
        // Ctrl+K to open search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            toggleSearch();
        }
    });
    
    // Close search panel when clicking outside
    document.addEventListener('click', (e) => {
        if (isSearchOpen && !e.target.closest('.ji-search-panel') && !e.target.closest('#ji-search-toggle')) {
            closeSearch();
        }
    });
    
    // Close mobile menu on resize to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024 && isMobileMenuOpen) {
            closeMobileMenu();
        }
    });
    
    // Initialize all features
    initMegaMenus();
    initAccordions();
    initSearchSuggestions();
    initMobileSearch();
    
    // Trap focus in mobile menu
    if (mobileMenu) {
        trapFocus(mobileMenu);
    }
    
    // Initial scroll check
    handleScroll();
    
    console.log('[OK] Header JS v15.1.0 - Zukan Style initialized');
})();
