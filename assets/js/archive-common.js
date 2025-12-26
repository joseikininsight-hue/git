/**
 * Archive Common JavaScript - v20.0 (Integrated)
 * archive-grant.php, taxonomy-*.php 共通使用
 * 
 * 使用方法:
 * このファイルをインクルードした後、以下を実行:
 * ArchiveCommon.init({
 *     ajaxUrl: '<?php echo admin_url("admin-ajax.php"); ?>',
 *     nonce: '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>',
 *     postType: 'grant', // or 'column'
 *     fixedCategory: '', // カテゴリ固定の場合（taxonomy-*用）
 *     fixedPrefecture: '', // 都道府県固定の場合
 *     fixedMunicipality: '', // 市町村固定の場合
 *     fixedPurpose: '', // 用途固定の場合
 *     fixedTag: '' // タグ固定の場合
 * });
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

(function(window) {
    'use strict';

    const ArchiveCommon = {
        config: {
            ajaxUrl: '',
            nonce: '',
            postType: 'grant',
            fixedCategory: '',
            fixedPrefecture: '',
            fixedMunicipality: '',
            fixedPurpose: '',
            fixedTag: ''
        },
        
        state: {
            currentPage: 1,
            perPage: 12,
            view: 'single',
            filters: {
                search: '',
                category: [],
                prefecture: [],
                municipality: '',
                region: '',
                amount: '',
                status: '',
                difficulty: '',
                sort: 'date_desc',
                tag: '',
                purpose: ''
            },
            isLoading: false,
            tempCategories: [],
            tempPrefectures: [],
            currentMunicipalities: []
        },
        
        elements: {},

        /**
         * 初期化
         */
        init: function(options) {
            // 設定をマージ
            Object.assign(this.config, options);
            
            // Production: removed
            // Production: removed
            
            // 固定フィルターを設定
            if (this.config.fixedCategory) {
                this.state.filters.category = [this.config.fixedCategory];
            }
            if (this.config.fixedPrefecture) {
                this.state.filters.prefecture = [this.config.fixedPrefecture];
            }
            if (this.config.fixedMunicipality) {
                this.state.filters.municipality = this.config.fixedMunicipality;
            }
            if (this.config.fixedPurpose) {
                this.state.filters.purpose = this.config.fixedPurpose;
            }
            if (this.config.fixedTag) {
                this.state.filters.tag = this.config.fixedTag;
            }
            
            this.initializeElements();
            this.initializeFromUrlParams();
            this.setupCustomSelects();
            this.setupEventListeners();
            this.loadGrants();
        },

        /**
         * DOM要素の取得
         */
        initializeElements: function() {
            const el = this.elements;
            
            el.grantsContainer = document.getElementById('grants-container');
            el.loadingOverlay = document.getElementById('loading-overlay');
            el.noResults = document.getElementById('no-results');
            el.resultsCount = document.getElementById('current-count');
            el.showingFrom = document.getElementById('showing-from');
            el.showingTo = document.getElementById('showing-to');
            el.paginationWrapper = document.getElementById('pagination-wrapper');
            el.activeFilters = document.getElementById('active-filters');
            el.activeFilterTags = document.getElementById('active-filter-tags');
            
            el.keywordSearch = document.getElementById('keyword-search');
            el.searchBtn = document.getElementById('search-btn');
            el.searchClearBtn = document.getElementById('search-clear-btn');
            el.searchSuggestions = document.getElementById('search-suggestions');
            el.suggestionsList = document.getElementById('suggestions-list');
            
            el.categorySelect = document.getElementById('category-select');
            el.categorySearch = document.getElementById('category-search');
            el.categoryOptions = document.getElementById('category-options');
            el.clearCategoryBtn = document.getElementById('clear-category-btn');
            el.applyCategoryBtn = document.getElementById('apply-category-btn');
            el.categoryCountBadge = document.getElementById('category-count-badge');
            
            el.regionSelect = document.getElementById('region-select');
            
            el.prefectureSelect = document.getElementById('prefecture-select');
            el.prefectureSearch = document.getElementById('prefecture-search');
            el.prefectureOptions = document.getElementById('prefecture-options');
            el.clearPrefectureBtn = document.getElementById('clear-prefecture-btn');
            el.applyPrefectureBtn = document.getElementById('apply-prefecture-btn');
            el.prefectureCountBadge = document.getElementById('prefecture-count-badge');
            
            el.municipalitySelect = document.getElementById('municipality-select');
            el.municipalityWrapper = document.getElementById('municipality-wrapper');
            el.municipalitySearch = document.getElementById('municipality-search');
            el.municipalityOptions = document.getElementById('municipality-options');
            el.selectedPrefectureName = document.getElementById('selected-prefecture-name');
            
            el.amountSelect = document.getElementById('amount-select');
            el.statusSelect = document.getElementById('status-select');
            el.sortSelect = document.getElementById('sort-select');
            
            el.viewBtns = document.querySelectorAll('.view-btn');
            el.resetAllFiltersBtn = document.getElementById('reset-all-filters-btn');
            
            el.mobileFilterToggle = document.getElementById('mobile-filter-toggle');
            el.mobileFilterClose = document.getElementById('mobile-filter-close');
            // Support both old 'filter-panel' and new 'mobile-filter-panel' IDs
            el.filterPanel = document.getElementById('mobile-filter-panel') || document.getElementById('filter-panel');
            el.mobileFilterCount = document.getElementById('mobile-filter-count');
            el.filterPanelOverlay = document.getElementById('mobile-filter-overlay') || document.getElementById('filter-panel-overlay');
        },

        /**
         * URLパラメータから初期化
         */
        initializeFromUrlParams: function() {
            const urlParams = new URLSearchParams(window.location.search);
            const state = this.state;
            const el = this.elements;
            
            const searchParam = urlParams.get('search');
            if (searchParam) {
                state.filters.search = searchParam;
                if (el.keywordSearch) {
                    el.keywordSearch.value = searchParam;
                    if (el.searchClearBtn) el.searchClearBtn.style.display = 'flex';
                }
            }
            
            const categoryParam = urlParams.get('category');
            if (categoryParam && !this.config.fixedCategory) {
                state.filters.category = [categoryParam];
            }
            
            const prefectureParam = urlParams.get('prefecture');
            if (prefectureParam && !this.config.fixedPrefecture) {
                state.filters.prefecture = [prefectureParam];
            }
            
            const municipalityParam = urlParams.get('municipality');
            if (municipalityParam && !this.config.fixedMunicipality) {
                state.filters.municipality = municipalityParam;
            }
            
            const tagParam = urlParams.get('grant_tag');
            if (tagParam && !this.config.fixedTag) {
                state.filters.tag = tagParam;
            }
            
            // 募集状況フィルター
            const statusParam = urlParams.get('application_status');
            if (statusParam) {
                const statusMapping = {
                    'open': 'active',
                    'recruiting': 'active',
                    '募集中': 'active',
                    'upcoming': 'upcoming',
                    '募集予定': 'upcoming',
                    'closed': 'closed',
                    '終了': 'closed'
                };
                const mappedStatus = statusMapping[statusParam] || statusParam;
                state.filters.status = mappedStatus;
                
                if (el.statusSelect) {
                    this.updateSelectUI(el.statusSelect, mappedStatus);
                }
            }
            
            // ソート順フィルター
            const orderbyParam = urlParams.get('orderby');
            if (orderbyParam) {
                let sortValue = 'date_desc';
                switch (orderbyParam) {
                    case 'deadline': sortValue = 'deadline_asc'; break;
                    case 'new': sortValue = 'date_desc'; break;
                    case 'popular': sortValue = 'popular_desc'; break;
                    case 'amount': sortValue = 'amount_desc'; break;
                }
                state.filters.sort = sortValue;
                if (el.sortSelect) {
                    this.updateSelectUI(el.sortSelect, sortValue);
                }
            }
            
            this.updateActiveFiltersDisplay();
        },

        /**
         * セレクトUIの更新
         */
        updateSelectUI: function(selectElement, value) {
            const valueSpan = selectElement.querySelector('.select-value');
            const options = selectElement.querySelectorAll('.select-option');
            options.forEach(opt => {
                opt.classList.remove('active');
                opt.setAttribute('aria-selected', 'false');
                if (opt.dataset.value === value) {
                    opt.classList.add('active');
                    opt.setAttribute('aria-selected', 'true');
                    if (valueSpan) {
                        valueSpan.textContent = opt.textContent.trim();
                    }
                }
            });
        },

        /**
         * カスタムセレクトのセットアップ
         */
        setupCustomSelects: function() {
            const self = this;
            
            // カテゴリ（固定でない場合のみ）
            if (!this.config.fixedCategory) {
                this.setupMultiSelectCategory();
            }
            
            // 地域
            this.setupSingleSelect(this.elements.regionSelect, function(value) {
                self.state.filters.region = value;
                self.filterPrefecturesByRegion(value);
                self.state.currentPage = 1;
                self.loadGrants();
            });
            
            // 都道府県（固定でない場合のみ）
            if (!this.config.fixedPrefecture) {
                this.setupMultiSelectPrefecture();
            }
            
            // 市町村
            this.setupMunicipalitySelect();
            
            // 金額
            this.setupSingleSelect(this.elements.amountSelect, function(value) {
                self.state.filters.amount = value;
                self.state.currentPage = 1;
                self.loadGrants();
            });
            
            // 募集状況
            this.setupSingleSelect(this.elements.statusSelect, function(value) {
                self.state.filters.status = value;
                self.state.currentPage = 1;
                self.loadGrants();
            });
            
            // ソート
            this.setupSingleSelect(this.elements.sortSelect, function(value) {
                self.state.filters.sort = value;
                self.state.currentPage = 1;
                self.loadGrants();
            });
        },

        /**
         * シングルセレクトのセットアップ
         */
        setupSingleSelect: function(selectElement, onChange) {
            if (!selectElement) return;
            
            const self = this;
            const trigger = selectElement.querySelector('.select-trigger');
            const dropdown = selectElement.querySelector('.select-dropdown');
            const options = selectElement.querySelectorAll('.select-option');
            const valueSpan = selectElement.querySelector('.select-value');
            
            trigger.addEventListener('click', function() {
                const isActive = selectElement.classList.contains('active');
                self.closeAllSelects();
                if (!isActive) {
                    selectElement.classList.add('active');
                    selectElement.setAttribute('aria-expanded', 'true');
                    dropdown.style.display = 'block';
                }
            });
            
            options.forEach(function(option) {
                option.addEventListener('click', function() {
                    const value = option.dataset.value;
                    const text = option.textContent.trim();
                    
                    options.forEach(function(opt) {
                        opt.classList.remove('active');
                        opt.setAttribute('aria-selected', 'false');
                    });
                    option.classList.add('active');
                    option.setAttribute('aria-selected', 'true');
                    
                    valueSpan.textContent = text;
                    
                    selectElement.classList.remove('active');
                    selectElement.setAttribute('aria-expanded', 'false');
                    dropdown.style.display = 'none';
                    
                    if (window.innerWidth > 768) {
                        onChange(value);
                    } else {
                        // モバイルでは値だけ更新
                        const filterName = selectElement.id.replace('-select', '');
                        if (filterName === 'region') {
                            self.state.filters.region = value;
                            self.filterPrefecturesByRegion(value);
                        } else if (filterName === 'amount') {
                            self.state.filters.amount = value;
                        } else if (filterName === 'status') {
                            self.state.filters.status = value;
                        } else if (filterName === 'sort') {
                            self.state.filters.sort = value;
                        }
                    }
                });
            });
        },

        /**
         * カテゴリマルチセレクトのセットアップ
         */
        setupMultiSelectCategory: function() {
            const el = this.elements;
            if (!el.categorySelect) return;
            
            const self = this;
            const trigger = el.categorySelect.querySelector('.select-trigger');
            const dropdown = el.categorySelect.querySelector('.select-dropdown');
            
            // el.categoryOptions が null の場合は空配列を使用
            const checkboxes = el.categoryOptions ? el.categoryOptions.querySelectorAll('.option-checkbox') : [];
            const allCheckbox = document.getElementById('cat-all');
            
            // trigger または dropdown が null の場合は終了
            if (!trigger || !dropdown) return;
            
            trigger.addEventListener('click', function() {
                const isActive = el.categorySelect.classList.contains('active');
                self.closeAllSelects();
                if (!isActive) {
                    el.categorySelect.classList.add('active');
                    el.categorySelect.setAttribute('aria-expanded', 'true');
                    dropdown.style.display = 'block';
                    self.state.tempCategories = [...self.state.filters.category];
                    self.updateCategoryCheckboxes();
                }
            });
            
            if (el.categorySearch && el.categoryOptions) {
                el.categorySearch.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase();
                    const options = el.categoryOptions.querySelectorAll('.select-option:not(.all-option)');
                    options.forEach(function(option) {
                        const name = (option.dataset.name || '').toLowerCase();
                        option.style.display = name.includes(query) ? 'flex' : 'none';
                    });
                });
            }
            
            if (allCheckbox) {
                allCheckbox.addEventListener('change', function(e) {
                    if (e.target.checked) {
                        self.state.tempCategories = [];
                        checkboxes.forEach(function(cb) {
                            if (cb !== allCheckbox) cb.checked = false;
                        });
                    }
                });
            }
            
            checkboxes.forEach(function(checkbox) {
                if (checkbox !== allCheckbox) {
                    checkbox.addEventListener('change', function(e) {
                        const value = e.target.value;
                        if (e.target.checked) {
                            if (!self.state.tempCategories.includes(value)) {
                                self.state.tempCategories.push(value);
                            }
                            if (allCheckbox) allCheckbox.checked = false;
                        } else {
                            const index = self.state.tempCategories.indexOf(value);
                            if (index > -1) self.state.tempCategories.splice(index, 1);
                            if (self.state.tempCategories.length === 0 && allCheckbox) {
                                allCheckbox.checked = true;
                            }
                        }
                    });
                }
            });
            
            if (el.clearCategoryBtn) {
                el.clearCategoryBtn.addEventListener('click', function() {
                    self.state.tempCategories = [];
                    self.updateCategoryCheckboxes();
                    if (allCheckbox) allCheckbox.checked = true;
                });
            }
            
            if (el.applyCategoryBtn) {
                el.applyCategoryBtn.addEventListener('click', function() {
                    self.state.filters.category = [...self.state.tempCategories];
                    self.updateCategoryDisplay();
                    el.categorySelect.classList.remove('active');
                    el.categorySelect.setAttribute('aria-expanded', 'false');
                    dropdown.style.display = 'none';
                    
                    if (window.innerWidth > 768) {
                        self.state.currentPage = 1;
                        self.loadGrants();
                    }
                });
            }
        },

        /**
         * 都道府県マルチセレクトのセットアップ
         */
        setupMultiSelectPrefecture: function() {
            const el = this.elements;
            if (!el.prefectureSelect) return;
            
            const self = this;
            const trigger = el.prefectureSelect.querySelector('.select-trigger');
            const dropdown = el.prefectureSelect.querySelector('.select-dropdown');
            
            // el.prefectureOptions が null の場合は空配列を使用
            const checkboxes = el.prefectureOptions ? el.prefectureOptions.querySelectorAll('.option-checkbox') : [];
            const allCheckbox = document.getElementById('pref-all');
            
            // trigger または dropdown が null の場合は終了
            if (!trigger || !dropdown) return;
            
            trigger.addEventListener('click', function() {
                const isActive = el.prefectureSelect.classList.contains('active');
                self.closeAllSelects();
                if (!isActive) {
                    el.prefectureSelect.classList.add('active');
                    el.prefectureSelect.setAttribute('aria-expanded', 'true');
                    dropdown.style.display = 'block';
                    self.state.tempPrefectures = [...self.state.filters.prefecture];
                    self.updatePrefectureCheckboxes();
                }
            });
            
            if (el.prefectureSearch && el.prefectureOptions) {
                el.prefectureSearch.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase();
                    const options = el.prefectureOptions.querySelectorAll('.select-option:not(.all-option)');
                    options.forEach(function(option) {
                        const name = (option.dataset.name || '').toLowerCase();
                        option.style.display = name.includes(query) ? 'flex' : 'none';
                    });
                });
            }
            
            if (allCheckbox) {
                allCheckbox.addEventListener('change', function(e) {
                    if (e.target.checked) {
                        self.state.tempPrefectures = [];
                        checkboxes.forEach(function(cb) {
                            if (cb !== allCheckbox) cb.checked = false;
                        });
                    }
                });
            }
            
            checkboxes.forEach(function(checkbox) {
                if (checkbox !== allCheckbox) {
                    checkbox.addEventListener('change', function(e) {
                        const value = e.target.value;
                        if (e.target.checked) {
                            if (!self.state.tempPrefectures.includes(value)) {
                                self.state.tempPrefectures.push(value);
                            }
                            if (allCheckbox) allCheckbox.checked = false;
                        } else {
                            const index = self.state.tempPrefectures.indexOf(value);
                            if (index > -1) self.state.tempPrefectures.splice(index, 1);
                            if (self.state.tempPrefectures.length === 0 && allCheckbox) {
                                allCheckbox.checked = true;
                            }
                        }
                    });
                }
            });
            
            if (el.clearPrefectureBtn) {
                el.clearPrefectureBtn.addEventListener('click', function() {
                    self.state.tempPrefectures = [];
                    self.updatePrefectureCheckboxes();
                    if (allCheckbox) allCheckbox.checked = true;
                });
            }
            
            if (el.applyPrefectureBtn) {
                el.applyPrefectureBtn.addEventListener('click', function() {
                    self.state.filters.prefecture = [...self.state.tempPrefectures];
                    self.updatePrefectureDisplay();
                    el.prefectureSelect.classList.remove('active');
                    el.prefectureSelect.setAttribute('aria-expanded', 'false');
                    dropdown.style.display = 'none';
                    
                    if (self.state.filters.prefecture.length === 1) {
                        const prefectureSlug = self.state.filters.prefecture[0];
                        const prefectureOption = document.querySelector('.select-option[data-value="' + prefectureSlug + '"]');
                        const prefectureName = prefectureOption ? prefectureOption.dataset.name : '';
                        self.loadMunicipalities(prefectureSlug, prefectureName);
                    } else {
                        self.hideMunicipalityFilter();
                        self.state.filters.municipality = '';
                    }
                    
                    if (window.innerWidth > 768) {
                        self.state.currentPage = 1;
                        self.loadGrants();
                    }
                });
            }
        },

        /**
         * 市町村セレクトのセットアップ
         */
        setupMunicipalitySelect: function() {
            const el = this.elements;
            if (!el.municipalitySelect) return;
            
            const self = this;
            const trigger = el.municipalitySelect.querySelector('.select-trigger');
            const dropdown = el.municipalitySelect.querySelector('.select-dropdown');
            
            // trigger または dropdown が null の場合は終了
            if (!trigger || !dropdown) return;
            
            trigger.addEventListener('click', function() {
                const isActive = el.municipalitySelect.classList.contains('active');
                self.closeAllSelects();
                if (!isActive) {
                    el.municipalitySelect.classList.add('active');
                    el.municipalitySelect.setAttribute('aria-expanded', 'true');
                    dropdown.style.display = 'block';
                }
            });
            
            if (el.municipalitySearch && el.municipalityOptions) {
                el.municipalitySearch.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase();
                    const options = el.municipalityOptions.querySelectorAll('.select-option');
                    options.forEach(function(option) {
                        const name = option.textContent.toLowerCase();
                        option.style.display = name.includes(query) ? 'flex' : 'none';
                    });
                });
            }
        },

        /**
         * カテゴリチェックボックスの更新
         */
        updateCategoryCheckboxes: function() {
            const el = this.elements;
            if (!el.categoryOptions) return;
            
            const checkboxes = el.categoryOptions.querySelectorAll('.option-checkbox');
            const allCheckbox = document.getElementById('cat-all');
            const self = this;
            
            checkboxes.forEach(function(checkbox) {
                if (checkbox !== allCheckbox) {
                    checkbox.checked = self.state.tempCategories.includes(checkbox.value);
                }
            });
            
            if (allCheckbox) {
                allCheckbox.checked = self.state.tempCategories.length === 0;
            }
        },

        /**
         * カテゴリ表示の更新
         */
        updateCategoryDisplay: function() {
            const el = this.elements;
            if (!el.categorySelect) return;
            
            const valueSpan = el.categorySelect.querySelector('.select-value');
            const count = this.state.filters.category.length;
            
            if (count === 0) {
                if (valueSpan) valueSpan.textContent = '選択';
                if (el.categoryCountBadge) el.categoryCountBadge.style.display = 'none';
            } else {
                if (valueSpan) valueSpan.textContent = count + '件選択';
                if (el.categoryCountBadge) {
                    el.categoryCountBadge.textContent = count;
                    el.categoryCountBadge.style.display = 'inline-flex';
                }
            }
        },

        /**
         * 都道府県チェックボックスの更新
         */
        updatePrefectureCheckboxes: function() {
            const el = this.elements;
            if (!el.prefectureOptions) return;
            
            const checkboxes = el.prefectureOptions.querySelectorAll('.option-checkbox');
            const allCheckbox = document.getElementById('pref-all');
            const self = this;
            
            checkboxes.forEach(function(checkbox) {
                if (checkbox !== allCheckbox) {
                    checkbox.checked = self.state.tempPrefectures.includes(checkbox.value);
                }
            });
            
            if (allCheckbox) {
                allCheckbox.checked = self.state.tempPrefectures.length === 0;
            }
        },

        /**
         * 都道府県表示の更新
         */
        updatePrefectureDisplay: function() {
            const el = this.elements;
            if (!el.prefectureSelect) return;
            
            const valueSpan = el.prefectureSelect.querySelector('.select-value');
            const count = this.state.filters.prefecture.length;
            
            if (count === 0) {
                if (valueSpan) valueSpan.textContent = '選択';
                if (el.prefectureCountBadge) el.prefectureCountBadge.style.display = 'none';
            } else {
                if (valueSpan) valueSpan.textContent = count + '件選択';
                if (el.prefectureCountBadge) {
                    el.prefectureCountBadge.textContent = count;
                    el.prefectureCountBadge.style.display = 'inline-flex';
                }
            }
        },

        /**
         * 地域で都道府県をフィルタリング
         */
        filterPrefecturesByRegion: function(region) {
            const el = this.elements;
            if (!el.prefectureOptions) return;
            
            const options = el.prefectureOptions.querySelectorAll('.select-option:not(.all-option)');
            options.forEach(function(option) {
                const optionRegion = option.dataset.region;
                option.style.display = (!region || optionRegion === region) ? 'flex' : 'none';
            });
        },

        /**
         * 全セレクトを閉じる
         */
        closeAllSelects: function() {
            document.querySelectorAll('.custom-select').forEach(function(select) {
                select.classList.remove('active');
                select.setAttribute('aria-expanded', 'false');
                const dropdown = select.querySelector('.select-dropdown');
                if (dropdown) dropdown.style.display = 'none';
            });
        },

        /**
         * 市町村を読み込む
         */
        loadMunicipalities: function(prefectureSlug, prefectureName) {
            const el = this.elements;
            const self = this;
            
            if (!prefectureSlug) {
                this.hideMunicipalityFilter();
                return;
            }
            
            if (el.municipalityWrapper) {
                el.municipalityWrapper.style.display = 'block';
            }
            
            if (el.selectedPrefectureName) {
                el.selectedPrefectureName.textContent = '（' + prefectureName + '）';
            }
            
            if (el.municipalityOptions) {
                el.municipalityOptions.innerHTML = '<div class="select-option loading-option" role="option">読み込み中...</div>';
            }
            
            const formData = new FormData();
            formData.append('action', 'gi_get_municipalities_for_prefecture');
            formData.append('prefecture_slug', prefectureSlug);
            formData.append('nonce', this.config.nonce);
            
            const timeoutId = setTimeout(function() {
                console.warn('⏱️ Municipality AJAX timeout');
                self.renderMunicipalityOptions([]);
            }, 10000);
            
            fetch(this.config.ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                clearTimeout(timeoutId);
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(function(data) {
                let municipalities = [];
                if (data.success) {
                    if (data.data && data.data.data && Array.isArray(data.data.data.municipalities)) {
                        municipalities = data.data.data.municipalities;
                    } else if (data.data && Array.isArray(data.data.municipalities)) {
                        municipalities = data.data.municipalities;
                    } else if (Array.isArray(data.municipalities)) {
                        municipalities = data.municipalities;
                    } else if (Array.isArray(data.data)) {
                        municipalities = data.data;
                    }
                }
                
                if (municipalities.length > 0) {
                    self.state.currentMunicipalities = municipalities;
                }
                self.renderMunicipalityOptions(municipalities);
            })
            .catch(function(error) {
                clearTimeout(timeoutId);
                console.error('❌ Municipality fetch error:', error);
                self.renderMunicipalityOptions([]);
            });
        },

        /**
         * 市町村オプションをレンダリング
         */
        renderMunicipalityOptions: function(municipalities) {
            const el = this.elements;
            if (!el.municipalityOptions || !el.municipalitySelect) return;
            
            const self = this;
            let html = '<div class="select-option active" data-value="" role="option">すべて</div>';
            
            municipalities.forEach(function(municipality) {
                html += '<div class="select-option" data-value="' + municipality.slug + '" role="option">' + municipality.name + '</div>';
            });
            
            el.municipalityOptions.innerHTML = html;
            
            const options = el.municipalityOptions.querySelectorAll('.select-option');
            const valueSpan = el.municipalitySelect.querySelector('.select-value');
            const dropdown = el.municipalitySelect.querySelector('.select-dropdown');
            
            if (!valueSpan || !dropdown) return;
            
            options.forEach(function(option) {
                option.addEventListener('click', function() {
                    const value = option.dataset.value;
                    const text = option.textContent.trim();
                    
                    options.forEach(function(opt) {
                        opt.classList.remove('active');
                        opt.setAttribute('aria-selected', 'false');
                    });
                    option.classList.add('active');
                    option.setAttribute('aria-selected', 'true');
                    
                    valueSpan.textContent = text;
                    
                    el.municipalitySelect.classList.remove('active');
                    el.municipalitySelect.setAttribute('aria-expanded', 'false');
                    dropdown.style.display = 'none';
                    
                    self.state.filters.municipality = value;
                    
                    if (window.innerWidth > 768) {
                        self.state.currentPage = 1;
                        self.loadGrants();
                    }
                });
            });
        },

        /**
         * 市町村フィルターを非表示
         */
        hideMunicipalityFilter: function() {
            const el = this.elements;
            if (el.municipalityWrapper) {
                el.municipalityWrapper.style.display = 'none';
            }
            
            this.state.filters.municipality = '';
            if (el.municipalitySelect) {
                const valueSpan = el.municipalitySelect.querySelector('.select-value');
                if (valueSpan) valueSpan.textContent = 'すべて';
            }
        },

        /**
         * イベントリスナーのセットアップ
         */
        setupEventListeners: function() {
            const self = this;
            const el = this.elements;
            
            // 検索
            if (el.keywordSearch) {
                el.keywordSearch.addEventListener('input', this.debounce(function() {
                    self.handleSearchInput();
                }, 300));
                el.keywordSearch.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        self.handleSearch();
                    } else if (e.key === 'Escape') {
                        self.hideSearchSuggestions();
                    } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                        e.preventDefault();
                        self.navigateSuggestions(e.key === 'ArrowDown' ? 1 : -1);
                    }
                });
                el.keywordSearch.addEventListener('focus', function() {
                    if (this.value.trim().length >= 2) {
                        self.fetchSearchSuggestions(this.value.trim());
                    }
                });
            }
            
            if (el.searchBtn) {
                el.searchBtn.addEventListener('click', function() {
                    self.handleSearch();
                });
            }
            
            if (el.searchClearBtn) {
                el.searchClearBtn.addEventListener('click', function() {
                    self.clearSearch();
                });
            }
            
            // 検索候補の外側クリックで閉じる
            document.addEventListener('click', function(e) {
                if (el.searchSuggestions && !el.searchSuggestions.contains(e.target) && 
                    el.keywordSearch && !el.keywordSearch.contains(e.target)) {
                    self.hideSearchSuggestions();
                }
            });
            
            // 表示切替
            el.viewBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    el.viewBtns.forEach(function(b) { b.classList.remove('active'); });
                    this.classList.add('active');
                    self.state.view = this.dataset.view;
                    el.grantsContainer.setAttribute('data-view', self.state.view);
                });
            });
            
            // リセット
            if (el.resetAllFiltersBtn) {
                el.resetAllFiltersBtn.addEventListener('click', function() {
                    self.resetAllFilters();
                });
            }
            
            // モバイルフィルター適用
            const mobileApplyFiltersBtn = document.getElementById('mobile-apply-filters-btn');
            if (mobileApplyFiltersBtn) {
                mobileApplyFiltersBtn.addEventListener('click', function() {
                    self.state.currentPage = 1;
                    self.loadGrants();
                    self.closeMobileFilter();
                });
            }
            
            // モバイルフィルター開閉
            if (el.mobileFilterToggle) {
                el.mobileFilterToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (el.filterPanel && el.filterPanel.classList.contains('active')) {
                        self.closeMobileFilter();
                    } else {
                        self.openMobileFilter();
                    }
                }, false);
            }
            
            if (el.mobileFilterClose) {
                el.mobileFilterClose.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.closeMobileFilter();
                }, false);
            }
            
            if (el.filterPanelOverlay) {
                el.filterPanelOverlay.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.closeMobileFilter();
                }, false);
            }
            
            if (el.filterPanel) {
                el.filterPanel.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
            
            // ESCキー
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && window.innerWidth <= 768) {
                    if (el.filterPanel && el.filterPanel.classList.contains('active')) {
                        self.closeMobileFilter();
                    }
                }
            });
            
            // セレクト外クリックで閉じる
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.custom-select')) {
                    self.closeAllSelects();
                }
            });
            
            // お気に入りボタン（イベント委譲でオプティミスティックUI対応）
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.favorite-btn');
                if (btn) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.toggleFavorite(btn);
                }
            });
        },
        
        /**
         * お気に入りをトグル（オプティミスティックUI）
         */
        toggleFavorite: function(btn) {
            const self = this;
            const postId = btn.dataset.postId;
            if (!postId) return;
            
            const icon = btn.querySelector('.favorite-icon');
            const isCurrentlyFavorite = icon && icon.classList.contains('active');
            
            // オプティミスティック更新（即座にUI更新）
            if (icon) {
                icon.classList.toggle('active');
                btn.classList.toggle('is-favorite');
            }
            btn.title = isCurrentlyFavorite ? 'お気に入りに追加' : 'お気に入りから削除';
            
            // アニメーション効果
            btn.style.transform = 'scale(1.2)';
            setTimeout(function() { btn.style.transform = ''; }, 200);
            
            // AJAX送信
            const formData = new FormData();
            formData.append('action', 'gi_toggle_favorite');
            formData.append('post_id', postId);
            formData.append('nonce', this.config.nonce);
            
            fetch(this.config.ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    // 成功時のフィードバック
                    self.showToast(data.data.message, 'success', null, 2000);
                } else {
                    // 失敗時はUIを元に戻す
                    if (icon) {
                        icon.classList.toggle('active');
                        btn.classList.toggle('is-favorite');
                    }
                    btn.title = isCurrentlyFavorite ? 'お気に入りから削除' : 'お気に入りに追加';
                    
                    // エラーメッセージ
                    var errorMsg = data.data && data.data.message ? data.data.message : 'お気に入りの更新に失敗しました';
                    self.showToast(errorMsg, 'error');
                }
            })
            .catch(function(err) {
                console.error('Favorite toggle error:', err);
                // 失敗時はUIを元に戻す
                if (icon) {
                    icon.classList.toggle('active');
                    btn.classList.toggle('is-favorite');
                }
                btn.title = isCurrentlyFavorite ? 'お気に入りから削除' : 'お気に入りに追加';
                self.showToast('通信エラーが発生しました', 'error');
            });
        },

        /**
         * モバイルフィルターを開く
         */
        openMobileFilter: function() {
            const el = this.elements;
            if (el.filterPanel) {
                el.filterPanel.classList.add('active');
                document.body.style.overflow = 'hidden';
                if (el.filterPanelOverlay) el.filterPanelOverlay.classList.add('active');
                if (el.mobileFilterToggle) el.mobileFilterToggle.setAttribute('aria-expanded', 'true');
            }
        },

        /**
         * モバイルフィルターを閉じる
         */
        closeMobileFilter: function() {
            const el = this.elements;
            if (el.filterPanel) {
                el.filterPanel.classList.remove('active');
                document.body.style.overflow = '';
                if (el.filterPanelOverlay) el.filterPanelOverlay.classList.remove('active');
                if (el.mobileFilterToggle) el.mobileFilterToggle.setAttribute('aria-expanded', 'false');
            }
        },

        /**
         * 検索入力ハンドラ（検索候補表示対応）
         */
        handleSearchInput: function() {
            const self = this;
            const el = this.elements;
            const query = el.keywordSearch.value.trim();
            
            if (el.searchClearBtn) {
                el.searchClearBtn.style.display = query.length > 0 ? 'flex' : 'none';
            }
            
            // 2文字以上で検索候補を表示
            if (query.length >= 2 && el.searchSuggestions) {
                this.fetchSearchSuggestions(query);
            } else {
                this.hideSearchSuggestions();
            }
        },
        
        /**
         * 検索候補を取得
         */
        fetchSearchSuggestions: function(query) {
            const self = this;
            const el = this.elements;
            
            const formData = new FormData();
            formData.append('action', 'gi_search_suggestions');
            formData.append('nonce', this.config.nonce);
            formData.append('query', query);
            formData.append('post_type', this.config.postType);
            
            fetch(this.config.ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.json();
            })
            .then(function(data) {
                if (data.success && data.data.suggestions && data.data.suggestions.length > 0) {
                    self.showSearchSuggestions(data.data.suggestions, query);
                } else {
                    self.hideSearchSuggestions();
                }
            })
            .catch(function() {
                self.hideSearchSuggestions();
            });
        },
        
        /**
         * 検索候補を表示
         */
        showSearchSuggestions: function(suggestions, query) {
            const self = this;
            const el = this.elements;
            if (!el.suggestionsList || !el.searchSuggestions) return;
            
            // 有効な候補のみフィルタリング
            const validSuggestions = suggestions.filter(function(item) {
                return item && item.title && item.title.trim() !== '' && item.title !== 'undefined';
            });
            
            if (validSuggestions.length === 0) {
                self.hideSearchSuggestions();
                return;
            }
            
            el.suggestionsList.innerHTML = validSuggestions.map(function(item, index) {
                const title = item.title || '';
                const highlightedText = self.highlightQuery(title, query);
                const icon = self.getSuggestionIcon(item.type);
                const typeLabel = self.getSuggestionTypeLabel(item.type);
                
                return '<li class="suggestion-item" data-index="' + index + '" data-value="' + self.escapeHtml(title) + '" data-type="' + (item.type || 'keyword') + '">' +
                    icon +
                    '<span class="suggestion-text">' + highlightedText + '</span>' +
                    (typeLabel ? '<span class="suggestion-type">' + typeLabel + '</span>' : '') +
                    (item.count ? '<span class="suggestion-count">' + item.count + '件</span>' : '') +
                    '</li>';
            }).join('');
            
            el.searchSuggestions.style.display = 'block';
            
            // クリックイベントを設定
            el.suggestionsList.querySelectorAll('.suggestion-item').forEach(function(item) {
                item.addEventListener('click', function() {
                    const value = this.dataset.value;
                    if (value && value !== 'undefined') {
                        el.keywordSearch.value = value;
                        self.hideSearchSuggestions();
                        self.handleSearch();
                    }
                });
            });
        },
        
        /**
         * 候補タイプ別アイコンを取得
         */
        getSuggestionIcon: function(type) {
            const icons = {
                'keyword': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>',
                'category': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>',
                'tag': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>',
                'prefecture': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>',
                'municipality': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9v.01"/><path d="M9 12v.01"/><path d="M9 15v.01"/><path d="M9 18v.01"/></svg>',
                'related': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>'
            };
            return icons[type] || icons['keyword'];
        },
        
        /**
         * 候補タイプ別ラベルを取得
         */
        getSuggestionTypeLabel: function(type) {
            const labels = {
                'category': 'カテゴリ',
                'tag': 'タグ',
                'prefecture': '都道府県',
                'municipality': '市区町村',
                'related': '関連'
            };
            return labels[type] || '';
        },
        
        /**
         * 検索候補を非表示
         */
        hideSearchSuggestions: function() {
            const el = this.elements;
            if (el.searchSuggestions) {
                el.searchSuggestions.style.display = 'none';
            }
            this.state.suggestionIndex = -1;
        },
        
        /**
         * 検索候補のキーボードナビゲーション
         */
        navigateSuggestions: function(direction) {
            const el = this.elements;
            if (!el.suggestionsList || el.searchSuggestions.style.display === 'none') return;
            
            const items = el.suggestionsList.querySelectorAll('.suggestion-item');
            if (items.length === 0) return;
            
            // 現在のインデックスを初期化
            if (typeof this.state.suggestionIndex === 'undefined') {
                this.state.suggestionIndex = -1;
            }
            
            // 前のアクティブ項目を解除
            items.forEach(function(item) { item.classList.remove('active'); });
            
            // 新しいインデックスを計算
            this.state.suggestionIndex += direction;
            if (this.state.suggestionIndex < 0) this.state.suggestionIndex = items.length - 1;
            if (this.state.suggestionIndex >= items.length) this.state.suggestionIndex = 0;
            
            // アクティブ項目を設定
            items[this.state.suggestionIndex].classList.add('active');
            el.keywordSearch.value = items[this.state.suggestionIndex].dataset.value;
        },
        
        /**
         * クエリをハイライト
         */
        highlightQuery: function(text, query) {
            if (!query) return this.escapeHtml(text);
            const escaped = this.escapeHtml(text);
            const keywords = query.split(/[\s　]+/).filter(function(k) { return k.length > 0; });
            let result = escaped;
            keywords.forEach(function(keyword) {
                const regex = new RegExp('(' + keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
                result = result.replace(regex, '<mark>$1</mark>');
            });
            return result;
        },

        /**
         * 検索実行
         */
        handleSearch: function() {
            const el = this.elements;
            this.state.filters.search = el.keywordSearch.value.trim();
            this.state.currentPage = 1;
            this.hideSearchSuggestions();
            this.loadGrants();
        },

        /**
         * 検索クリア
         */
        clearSearch: function() {
            const el = this.elements;
            el.keywordSearch.value = '';
            this.state.filters.search = '';
            if (el.searchClearBtn) el.searchClearBtn.style.display = 'none';
            this.hideSearchSuggestions();
            this.state.currentPage = 1;
            this.loadGrants();
        },

        /**
         * 全フィルターをリセット
         */
        resetAllFilters: function() {
            const el = this.elements;
            
            // 固定フィルター以外をリセット
            this.state.filters = {
                search: '',
                category: this.config.fixedCategory ? [this.config.fixedCategory] : [],
                prefecture: this.config.fixedPrefecture ? [this.config.fixedPrefecture] : [],
                municipality: this.config.fixedMunicipality || '',
                region: '',
                amount: '',
                status: '',
                difficulty: '',
                sort: 'date_desc',
                tag: this.config.fixedTag || '',
                purpose: this.config.fixedPurpose || ''
            };
            this.state.tempCategories = [];
            this.state.tempPrefectures = [];
            this.state.currentPage = 1;
            
            if (el.keywordSearch) el.keywordSearch.value = '';
            if (el.searchClearBtn) el.searchClearBtn.style.display = 'none';
            
            this.resetCustomSelect(el.regionSelect, '全国');
            this.resetCustomSelect(el.amountSelect, '指定なし');
            this.resetCustomSelect(el.statusSelect, 'すべて');
            this.resetCustomSelect(el.sortSelect, '新着順');
            
            if (!this.config.fixedCategory) {
                this.updateCategoryDisplay();
                this.updateCategoryCheckboxes();
            }
            if (!this.config.fixedPrefecture) {
                this.updatePrefectureDisplay();
                this.updatePrefectureCheckboxes();
            }
            
            this.filterPrefecturesByRegion('');
            if (!this.config.fixedMunicipality) {
                this.hideMunicipalityFilter();
            }
            
            this.loadGrants();
        },

        /**
         * カスタムセレクトをリセット
         */
        resetCustomSelect: function(selectElement, defaultText) {
            if (!selectElement) return;
            
            const valueSpan = selectElement.querySelector('.select-value');
            const options = selectElement.querySelectorAll('.select-option');
            
            valueSpan.textContent = defaultText;
            options.forEach(function(opt) {
                opt.classList.remove('active');
                opt.setAttribute('aria-selected', 'false');
            });
            if (options[0]) {
                options[0].classList.add('active');
                options[0].setAttribute('aria-selected', 'true');
            }
        },

        /**
         * 助成金/コラムを読み込む
         */
        loadGrants: function() {
            if (this.state.isLoading) return;
            
            const self = this;
            this.state.isLoading = true;
            this.showLoading(true);
            
            const formData = new FormData();
            formData.append('action', 'gi_ajax_load_grants');
            formData.append('nonce', this.config.nonce);
            formData.append('page', this.state.currentPage);
            formData.append('posts_per_page', this.state.perPage);
            formData.append('view', this.state.view);
            formData.append('post_type', this.config.postType);
            
            const filters = this.state.filters;
            
            if (filters.search) formData.append('search', filters.search);
            if (filters.category.length > 0) formData.append('categories', JSON.stringify(filters.category));
            if (filters.prefecture.length > 0) formData.append('prefectures', JSON.stringify(filters.prefecture));
            if (filters.municipality) formData.append('municipalities', JSON.stringify([filters.municipality]));
            if (filters.region) formData.append('region', filters.region);
            if (filters.amount) formData.append('amount', filters.amount);
            if (filters.status) formData.append('status', JSON.stringify([filters.status]));
            if (filters.tag) formData.append('tag', filters.tag);
            if (filters.purpose) formData.append('purpose', filters.purpose);
            formData.append('sort', filters.sort);
            
            fetch(this.config.ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                // HTTPステータスコードをチェック
                if (!response.ok) {
                    console.error('HTTP Error:', response.status, response.statusText);
                    throw new Error('サーバーエラー (HTTP ' + response.status + ')');
                }
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    self.displayGrants(data.data.grants);
                    self.updateStats(data.data.stats);
                    self.updatePagination(data.data.pagination);
                    self.updateActiveFiltersDisplay();
                } else {
                    console.error('API Error:', data.data || 'Unknown error');
                    self.showError('データの読み込みに失敗しました。');
                }
            })
            .catch(function(error) {
                console.error('Fetch Error:', error);
                self.showError('通信エラーが発生しました。ページを再読み込みしてお試しください。');
            })
            .finally(function() {
                self.state.isLoading = false;
                self.showLoading(false);
            });
        },

        /**
         * 助成金/コラムを表示
         */
        displayGrants: function(grants) {
            const el = this.elements;
            if (!el.grantsContainer) return;
            
            if (!grants || grants.length === 0) {
                el.grantsContainer.innerHTML = '';
                el.grantsContainer.style.display = 'none';
                if (el.noResults) el.noResults.style.display = 'block';
                return;
            }
            
            el.grantsContainer.style.display = this.state.view === 'single' ? 'flex' : 'grid';
            if (el.noResults) el.noResults.style.display = 'none';
            
            const fragment = document.createDocumentFragment();
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = grants.map(function(grant) { return grant.html; }).join('');
            
            while (tempDiv.firstChild) {
                fragment.appendChild(tempDiv.firstChild);
            }
            
            el.grantsContainer.innerHTML = '';
            el.grantsContainer.appendChild(fragment);
        },

        /**
         * 統計を更新
         * FIX: Calculate showing_from and showing_to client-side to ensure updates on page change
         */
        updateStats: function(stats) {
            const el = this.elements;
            const totalFound = stats.total_found || 0;
            const currentPage = this.state.currentPage || 1;
            const perPage = this.state.perPage || 12;
            
            // Calculate showing range client-side (fallback if server doesn't provide)
            let showingFrom = stats.showing_from;
            let showingTo = stats.showing_to;
            
            if (showingFrom === undefined || showingFrom === null) {
                showingFrom = totalFound > 0 ? ((currentPage - 1) * perPage) + 1 : 0;
            }
            if (showingTo === undefined || showingTo === null) {
                showingTo = Math.min(currentPage * perPage, totalFound);
            }
            
            // Update DOM elements
            if (el.resultsCount) el.resultsCount.textContent = totalFound.toLocaleString();
            if (el.showingFrom) el.showingFrom.textContent = showingFrom.toLocaleString();
            if (el.showingTo) el.showingTo.textContent = showingTo.toLocaleString();
            
            // Production: removed
            
            // GA event tracking for results display (「1〜12件を表示」)
            if (typeof gtag === 'function' && totalFound > 0) {
                gtag('event', 'results_display', {
                    'event_category': 'archive_view',
                    'event_label': showingFrom + '〜' + showingTo + '件を表示',
                    'value': totalFound,
                    'page_number': currentPage
                });
            }
        },

        /**
         * ページネーションを更新
         */
        updatePagination: function(pagination) {
            const el = this.elements;
            const self = this;
            if (!el.paginationWrapper) return;
            
            if (!pagination || pagination.total_pages <= 1) {
                el.paginationWrapper.innerHTML = '';
                return;
            }
            
            const currentPage = pagination.current_page || 1;
            const totalPages = pagination.total_pages || 1;
            
            let html = '<div class="page-numbers">';
            
            if (currentPage > 1) {
                html += '<a href="#" class="page-numbers prev" data-page="' + (currentPage - 1) + '">前へ</a>';
            }
            
            const range = 2;
            let startPage = Math.max(1, currentPage - range);
            let endPage = Math.min(totalPages, currentPage + range);
            
            if (startPage > 1) {
                html += '<a href="#" class="page-numbers" data-page="1">1</a>';
                if (startPage > 2) html += '<span class="page-numbers dots">…</span>';
            }
            
            for (let i = startPage; i <= endPage; i++) {
                if (i === currentPage) {
                    html += '<span class="page-numbers current">' + i + '</span>';
                } else {
                    html += '<a href="#" class="page-numbers" data-page="' + i + '">' + i + '</a>';
                }
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) html += '<span class="page-numbers dots">…</span>';
                html += '<a href="#" class="page-numbers" data-page="' + totalPages + '">' + totalPages + '</a>';
            }
            
            if (currentPage < totalPages) {
                html += '<a href="#" class="page-numbers next" data-page="' + (currentPage + 1) + '">次へ</a>';
            }
            
            html += '</div>';
            el.paginationWrapper.innerHTML = html;
            
            // ページネーションクリックハンドラ
            el.paginationWrapper.querySelectorAll('a.page-numbers').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = parseInt(this.dataset.page);
                    if (page && page !== currentPage) {
                        self.state.currentPage = page;
                        self.loadGrants();
                        
                        // スクロール
                        const resultsHeader = document.querySelector('.results-header');
                        if (resultsHeader) {
                            const headerHeight = 80;
                            const elementPosition = resultsHeader.getBoundingClientRect().top;
                            const offsetPosition = elementPosition + window.pageYOffset - headerHeight;
                            window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
                        } else if (el.grantsContainer) {
                            el.grantsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }
                });
            });
        },

        /**
         * アクティブフィルター表示を更新
         */
        updateActiveFiltersDisplay: function() {
            const el = this.elements;
            const state = this.state;
            const self = this;
            
            if (!el.activeFilters || !el.activeFilterTags) return;
            
            const tags = [];
            
            if (state.filters.search) {
                tags.push({ type: 'search', label: '検索: "' + state.filters.search + '"', value: state.filters.search });
            }
            
            // 固定でないカテゴリのみ表示
            if (!this.config.fixedCategory && state.filters.category.length > 0) {
                state.filters.category.forEach(function(catSlug) {
                    const option = document.querySelector('.select-option[data-value="' + catSlug + '"]');
                    if (option) {
                        tags.push({ type: 'category', label: option.dataset.name || option.textContent.trim(), value: catSlug });
                    }
                });
            }
            
            // 固定でない都道府県のみ表示
            if (!this.config.fixedPrefecture && state.filters.prefecture.length > 0) {
                state.filters.prefecture.forEach(function(prefSlug) {
                    const option = document.querySelector('.select-option[data-value="' + prefSlug + '"]');
                    if (option) {
                        tags.push({ type: 'prefecture', label: option.dataset.name || option.textContent.trim(), value: prefSlug });
                    }
                });
            }
            
            if (!this.config.fixedMunicipality && state.filters.municipality && el.municipalityOptions) {
                const municipalityOption = Array.from(el.municipalityOptions.querySelectorAll('.select-option')).find(function(opt) {
                    return opt.dataset.value === state.filters.municipality;
                });
                if (municipalityOption) {
                    tags.push({ type: 'municipality', label: '市町村: ' + municipalityOption.textContent.trim(), value: state.filters.municipality });
                }
            }
            
            if (state.filters.amount) {
                const labels = {
                    '0-100': '〜100万円',
                    '100-500': '100万円〜500万円',
                    '500-1000': '500万円〜1000万円',
                    '1000-3000': '1000万円〜3000万円',
                    '3000+': '3000万円以上'
                };
                tags.push({ type: 'amount', label: '金額: ' + labels[state.filters.amount], value: state.filters.amount });
            }
            
            if (state.filters.status) {
                const labels = {
                    'open': '募集中',
                    'active': '募集中',
                    'recruiting': '募集中',
                    'upcoming': '募集予定',
                    'closed': '募集終了'
                };
                tags.push({ type: 'status', label: '状況: ' + (labels[state.filters.status] || state.filters.status), value: state.filters.status });
            }
            
            if (state.filters.sort && state.filters.sort !== 'date_desc') {
                const sortLabels = {
                    'deadline_asc': '締切間近順',
                    'popular_desc': '人気順',
                    'amount_desc': '金額順',
                    'featured_first': '注目順',
                    'date_asc': '古い順'
                };
                if (sortLabels[state.filters.sort]) {
                    tags.push({ type: 'sort', label: '並び順: ' + sortLabels[state.filters.sort], value: state.filters.sort });
                }
            }
            
            if (!this.config.fixedTag && state.filters.tag) {
                tags.push({ type: 'tag', label: '#' + state.filters.tag, value: state.filters.tag });
            }
            
            if (tags.length === 0) {
                el.activeFilters.style.display = 'none';
                if (el.resetAllFiltersBtn) el.resetAllFiltersBtn.style.display = 'none';
                if (el.mobileFilterCount) el.mobileFilterCount.style.display = 'none';
                return;
            }
            
            el.activeFilters.style.display = 'flex';
            if (el.resetAllFiltersBtn) el.resetAllFiltersBtn.style.display = 'flex';
            if (el.mobileFilterCount) {
                el.mobileFilterCount.textContent = tags.length;
                el.mobileFilterCount.style.display = 'flex';
            }
            
            el.activeFilterTags.innerHTML = tags.map(function(tag) {
                return '<div class="filter-tag"><span>' + self.escapeHtml(tag.label) + '</span><button class="filter-tag-remove" data-type="' + tag.type + '" data-value="' + self.escapeHtml(tag.value) + '" type="button">×</button></div>';
            }).join('');
            
            el.activeFilterTags.querySelectorAll('.filter-tag-remove').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    self.removeFilter(this.dataset.type, this.dataset.value);
                });
            });
        },

        /**
         * フィルターを削除
         */
        removeFilter: function(type, value) {
            const state = this.state;
            const el = this.elements;
            
            switch(type) {
                case 'search':
                    this.clearSearch();
                    return;
                case 'category':
                    const catIndex = state.filters.category.indexOf(value);
                    if (catIndex > -1) state.filters.category.splice(catIndex, 1);
                    state.tempCategories = [...state.filters.category];
                    this.updateCategoryDisplay();
                    this.updateCategoryCheckboxes();
                    break;
                case 'prefecture':
                    const prefIndex = state.filters.prefecture.indexOf(value);
                    if (prefIndex > -1) state.filters.prefecture.splice(prefIndex, 1);
                    state.tempPrefectures = [...state.filters.prefecture];
                    this.updatePrefectureDisplay();
                    this.updatePrefectureCheckboxes();
                    if (state.filters.prefecture.length !== 1) {
                        this.hideMunicipalityFilter();
                    }
                    break;
                case 'municipality':
                    state.filters.municipality = '';
                    if (el.municipalitySelect) {
                        const valueSpan = el.municipalitySelect.querySelector('.select-value');
                        if (valueSpan) valueSpan.textContent = 'すべて';
                    }
                    break;
                case 'amount':
                    state.filters.amount = '';
                    this.resetCustomSelect(el.amountSelect, '指定なし');
                    break;
                case 'status':
                    state.filters.status = '';
                    this.resetCustomSelect(el.statusSelect, 'すべて');
                    break;
                case 'tag':
                    state.filters.tag = '';
                    break;
            }
            
            state.currentPage = 1;
            this.loadGrants();
        },

        /**
         * ローディング表示（スケルトンスクリーン対応）
         * 全画面オーバーレイは廃止し、コンテナ内にスケルトンを表示
         */
        showLoading: function(show) {
            const el = this.elements;
            const self = this;
            
            // 全画面オーバーレイは常に非表示
            if (el.loadingOverlay) el.loadingOverlay.style.display = 'none';
            
            if (show) {
                // noResults を非表示
                if (el.noResults) el.noResults.style.display = 'none';
                
                // スケルトンスクリーンを表示
                if (el.grantsContainer) {
                    const skeletonCount = 6;
                    // view: 'single' (リスト) / 'grid' (グリッド) / 'compact' (コンパクト)
                    const view = this.state.view || 'single';
                    let skeletonHtml = '<div class="skeleton-container" data-view="' + view + '">';
                    
                    for (let i = 0; i < skeletonCount; i++) {
                        skeletonHtml += self.createSkeletonCard();
                    }
                    skeletonHtml += '</div>';
                    
                    // コンテナをスケルトンで置き換え
                    el.grantsContainer.innerHTML = skeletonHtml;
                    el.grantsContainer.style.display = view === 'single' ? 'flex' : 'grid';
                }
            }
            // show=false の場合は displayGrants() がコンテナを上書きするため処理不要
        },
        
        /**
         * スケルトンカードを生成
         */
        createSkeletonCard: function() {
            return '<div class="skeleton-card">' +
                '<div class="skeleton-element skeleton-badge"></div>' +
                '<div class="skeleton-element skeleton-title"></div>' +
                '<div class="skeleton-element skeleton-text"></div>' +
                '<div class="skeleton-element skeleton-text-short"></div>' +
                '<div class="skeleton-meta">' +
                    '<div class="skeleton-element skeleton-meta-item"></div>' +
                    '<div class="skeleton-element skeleton-meta-item"></div>' +
                    '<div class="skeleton-element skeleton-meta-item"></div>' +
                '</div>' +
                '<div class="skeleton-tags">' +
                    '<div class="skeleton-element skeleton-tag"></div>' +
                    '<div class="skeleton-element skeleton-tag"></div>' +
                '</div>' +
            '</div>';
        },

        /**
         * エラー表示 - トースト通知に変更 (FIX: alert() replaced)
         */
        showError: function(message) {
            console.error('Error:', message);
            this.showToast(message, 'error', 'エラーが発生しました');
        },

        /**
         * トースト通知表示 (FIX: alert() replacement)
         * @param {string} message - 表示メッセージ
         * @param {string} type - 'error', 'success', 'warning', 'info'
         * @param {string} title - オプションのタイトル
         * @param {number} duration - 表示時間(ms) デフォルト5000
         */
        showToast: function(message, type, title, duration) {
            type = type || 'info';
            duration = duration || 5000;
            
            // コンテナを取得または作成
            var container = document.querySelector('.toast-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'toast-container';
                document.body.appendChild(container);
            }
            
            // アイコンSVG
            var icons = {
                error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
                success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
            };
            
            // トースト要素を作成
            var toast = document.createElement('div');
            toast.className = 'toast toast-' + type;
            
            var titleHtml = title ? '<p class="toast-title">' + this.escapeHtml(title) + '</p>' : '';
            
            toast.innerHTML = 
                '<span class="toast-icon">' + icons[type] + '</span>' +
                '<div class="toast-content">' +
                    titleHtml +
                    '<p class="toast-message">' + this.escapeHtml(message) + '</p>' +
                '</div>' +
                '<button class="toast-close" aria-label="閉じる">' +
                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
                '</button>';
            
            container.appendChild(toast);
            
            // 閉じるボタンのイベント
            var closeBtn = toast.querySelector('.toast-close');
            var self = this;
            closeBtn.addEventListener('click', function() {
                self.hideToast(toast);
            });
            
            // アニメーションで表示
            requestAnimationFrame(function() {
                toast.classList.add('show');
            });
            
            // 自動で非表示
            setTimeout(function() {
                self.hideToast(toast);
            }, duration);
        },

        /**
         * トースト非表示
         */
        hideToast: function(toast) {
            if (!toast) return;
            toast.classList.remove('show');
            setTimeout(function() {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        },

        /**
         * デバウンス
         */
        debounce: function(func, wait) {
            let timeout;
            return function() {
                const args = arguments;
                const later = function() {
                    clearTimeout(timeout);
                    func.apply(null, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * HTMLエスケープ
         */
        escapeHtml: function(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    };

    // グローバルに公開
    window.ArchiveCommon = ArchiveCommon;

    // Production: removed

    /**
     * ========================================================================
     * Mobile Filter Module - モバイル用絞り込みパネル機能
     * ========================================================================
     */
    
    var mobileFilterInitialized = false;
    
    ArchiveCommon.initMobileFilter = function() {
        // 重複初期化を防止
        if (mobileFilterInitialized) return;
        mobileFilterInitialized = true;
        
        var self = this;
        var toggleBtn = document.getElementById('mobile-filter-toggle');
        var closeBtn = document.getElementById('mobile-filter-close');
        var overlay = document.getElementById('mobile-filter-overlay');
        var panel = document.getElementById('mobile-filter-panel');
        var applyBtn = document.getElementById('mobile-filter-apply');
        var resetBtn = document.getElementById('mobile-filter-reset');
        var countBadge = document.getElementById('mobile-filter-count');
        
        if (!toggleBtn || !panel) return;
        
        // パネルを開く
        function openPanel() {
            panel.classList.add('active');
            if (overlay) overlay.classList.add('active');
            document.body.classList.add('mobile-filter-open');
            toggleBtn.setAttribute('aria-expanded', 'true');
        }
        
        // パネルを閉じる
        function closePanel() {
            panel.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            document.body.classList.remove('mobile-filter-open');
            toggleBtn.setAttribute('aria-expanded', 'false');
        }
        
        // アコーディオンのセットアップ
        var accordions = panel.querySelectorAll('.mobile-filter-accordion');
        accordions.forEach(function(accordion) {
            accordion.addEventListener('click', function() {
                var isExpanded = this.getAttribute('aria-expanded') === 'true';
                var options = this.nextElementSibling;
                
                this.setAttribute('aria-expanded', !isExpanded);
                if (options) {
                    options.style.display = isExpanded ? 'none' : 'block';
                }
            });
        });
        
        // チェックボックス変更時のカウント更新
        var checkboxes = panel.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                self.updateMobileFilterCounts();
            });
        });
        
        // イベントリスナー
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (panel.classList.contains('active')) {
                closePanel();
            } else {
                openPanel();
            }
        });
        
        if (closeBtn) {
            closeBtn.addEventListener('click', closePanel);
        }
        
        if (overlay) {
            overlay.addEventListener('click', closePanel);
        }
        
        // ESCキーで閉じる
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && panel.classList.contains('active')) {
                closePanel();
            }
        });
        
        // 適用ボタン
        if (applyBtn) {
            applyBtn.addEventListener('click', function() {
                self.applyMobileFilters();
                closePanel();
            });
        }
        
        // リセットボタン
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                self.resetMobileFilters();
            });
        }
    };
    
    /**
     * モバイルフィルターカウントを更新
     */
    ArchiveCommon.updateMobileFilterCounts = function() {
        var panel = document.getElementById('mobile-filter-panel');
        if (!panel) return;
        
        var totalCount = 0;
        
        // 各フィルタータイプのカウント
        var filterTypes = ['category', 'prefecture', 'region', 'amount', 'status'];
        filterTypes.forEach(function(type) {
            var checkboxes = panel.querySelectorAll('input[name="mobile_' + type + '[]"]:checked');
            var badge = document.getElementById('mobile-' + type + '-badge');
            var count = checkboxes.length;
            
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline-flex';
                } else {
                    badge.style.display = 'none';
                }
            }
            totalCount += count;
        });
        
        // キーワード検索もカウント
        var searchInput = document.getElementById('mobile-keyword-search');
        if (searchInput && searchInput.value.trim()) {
            totalCount++;
        }
        
        // トグルボタンのカウント更新
        var toggleCount = document.getElementById('mobile-filter-count');
        if (toggleCount) {
            if (totalCount > 0) {
                toggleCount.textContent = totalCount;
                toggleCount.style.display = 'inline-flex';
            } else {
                toggleCount.style.display = 'none';
            }
        }
    };
    
    /**
     * モバイルフィルターを適用
     */
    ArchiveCommon.applyMobileFilters = function() {
        if (!this.state) return;
        
        var panel = document.getElementById('mobile-filter-panel');
        if (!panel) return;
        
        var state = this.state;
        
        // キーワード検索
        var searchInput = document.getElementById('mobile-keyword-search');
        if (searchInput) {
            state.filters.search = searchInput.value.trim();
            // サイドバーの検索にも同期
            var sidebarSearch = document.getElementById('sidebar-keyword-search');
            if (sidebarSearch) sidebarSearch.value = state.filters.search;
            var mainSearch = document.getElementById('keyword-search');
            if (mainSearch) mainSearch.value = state.filters.search;
        }
        
        // カテゴリ
        var categoryCheckboxes = panel.querySelectorAll('input[name="mobile_category[]"]:checked');
        state.filters.category = Array.from(categoryCheckboxes).map(function(cb) { return cb.value; });
        
        // 都道府県
        var prefectureCheckboxes = panel.querySelectorAll('input[name="mobile_prefecture[]"]:checked');
        state.filters.prefecture = Array.from(prefectureCheckboxes).map(function(cb) { return cb.value; });
        
        // 地域
        var regionCheckboxes = panel.querySelectorAll('input[name="mobile_region[]"]:checked');
        state.filters.region = regionCheckboxes.length > 0 ? regionCheckboxes[0].value : '';
        
        // 助成金額
        var amountCheckboxes = panel.querySelectorAll('input[name="mobile_amount[]"]:checked');
        state.filters.amount = amountCheckboxes.length > 0 ? amountCheckboxes[0].value : '';
        
        // 募集状況
        var statusCheckboxes = panel.querySelectorAll('input[name="mobile_status[]"]:checked');
        state.filters.status = statusCheckboxes.length > 0 ? statusCheckboxes[0].value : '';
        
        // 検索実行
        state.currentPage = 1;
        this.loadGrants();
        this.updateActiveFiltersDisplay();
        
        // サイドバーのチェックボックスにも同期
        this.syncSidebarFilters();
        
        // 結果エリアへスクロール
        var resultsHeader = document.querySelector('.results-header, .zukan-results-header');
        if (resultsHeader) {
            setTimeout(function() {
                resultsHeader.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    };
    
    /**
     * モバイルフィルターをリセット
     */
    ArchiveCommon.resetMobileFilters = function() {
        var panel = document.getElementById('mobile-filter-panel');
        if (!panel) return;
        
        // チェックボックスをリセット
        var checkboxes = panel.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(function(cb) { cb.checked = false; });
        
        // 検索をクリア
        var searchInput = document.getElementById('mobile-keyword-search');
        if (searchInput) searchInput.value = '';
        
        // カウントを更新
        this.updateMobileFilterCounts();
    };
    
    /**
     * サイドバーフィルターと同期
     */
    ArchiveCommon.syncSidebarFilters = function() {
        var state = this.state;
        if (!state) return;
        
        // カテゴリの同期
        state.filters.category.forEach(function(slug) {
            var sidebarCb = document.querySelector('input[name="sidebar_category[]"][value="' + slug + '"]');
            if (sidebarCb) sidebarCb.checked = true;
        });
        
        // 都道府県の同期
        state.filters.prefecture.forEach(function(slug) {
            var sidebarCb = document.querySelector('input[name="sidebar_prefecture[]"][value="' + slug + '"]');
            if (sidebarCb) sidebarCb.checked = true;
        });
        
        // カウント更新
        if (typeof this.updateSidebarFilterCounts === 'function') {
            this.updateSidebarFilterCounts();
        }
    };

    /**
     * ========================================================================
     * Sidebar Filters Module - 統合されたサイドバーフィルター機能
     * ========================================================================
     */
    
    /**
     * 統合ソートセレクトの初期化
     */
    ArchiveCommon.initUnifiedSortSelect = function() {
        var sortSelect = document.getElementById('unified-sort-select');
        if (!sortSelect) {
            // Production: removed
            return;
        }
        
        var self = this;
        // Production: removed
        
        sortSelect.addEventListener('change', function() {
            var sortValue = this.value;
            // Production: removed
            
            if (self.state) {
                self.state.filters.sort = sortValue;
                self.state.currentPage = 1;
                self.loadGrants();
                // Production: removed
            } else {
                console.error('❌ ArchiveCommon state not available');
            }
        });
        
        // Production: removed
    };
    
    /**
     * サイドバーフィルターの初期化（重複防止版）
     */
    var archiveCommonSidebarInitialized = false;
    
    ArchiveCommon.initSidebarFilters = function() {
        // 重複初期化を防止
        if (archiveCommonSidebarInitialized) {
            // Production: removed
            return;
        }
        archiveCommonSidebarInitialized = true;
        
        var self = this;
        // Production: removed
        
        // フィルターグループのトグル
        var filterToggles = document.querySelectorAll('.sidebar-filter-toggle');
        // Production: removed
        
        filterToggles.forEach(function(toggle, index) {
            // 既にイベントリスナーが登録されていればスキップ
            if (toggle.dataset.acInitialized === 'true') {
                return;
            }
            toggle.dataset.acInitialized = 'true';
            
            var parentGroup = toggle.closest('.sidebar-filter-group');
            var options = parentGroup ? parentGroup.querySelector('.sidebar-filter-options') : null;
            
            if (index === 0 && options) {
                toggle.setAttribute('aria-expanded', 'true');
                options.style.display = 'block';
            }
            
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var isExpanded = this.getAttribute('aria-expanded') === 'true';
                var group = this.closest('.sidebar-filter-group');
                var opts = group ? group.querySelector('.sidebar-filter-options') : null;
                
                this.setAttribute('aria-expanded', !isExpanded);
                if (opts) {
                    opts.style.display = isExpanded ? 'none' : 'block';
                }
            });
        });
        
        // チェックボックス変更時のカウント更新
        var checkboxes = document.querySelectorAll('.sidebar-filter-option input[type="checkbox"]');
        // Production: removed
        
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                self.updateSidebarFilterCounts();
            });
        });
        
        // サイドバー検索ボタン
        var sidebarSearchBtn = document.getElementById('sidebar-search-btn');
        var sidebarSearchInput = document.getElementById('sidebar-keyword-search');
        
        if (sidebarSearchBtn && sidebarSearchInput) {
            // Production: removed
            sidebarSearchBtn.addEventListener('click', function() {
                self.applySidebarFilters();
            });
            
            sidebarSearchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    self.applySidebarFilters();
                }
            });
        }
        
        // フィルター適用ボタン
        var applyBtn = document.getElementById('sidebar-apply-filter');
        if (applyBtn) {
            // Production: removed
            applyBtn.addEventListener('click', function() {
                self.applySidebarFilters();
            });
        }
        
        // リセットボタン
        var resetBtn = document.getElementById('sidebar-reset-filter');
        if (resetBtn) {
            // Production: removed
            resetBtn.addEventListener('click', function() {
                self.resetSidebarFilters();
            });
        }
        
        // 「さらに表示」ボタン - カテゴリ追加読み込み
        var moreButtons = document.querySelectorAll('.sidebar-filter-more');
        moreButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var target = this.getAttribute('data-target');
                var button = this;
                // Production: removed
                
                if (target === 'category') {
                    button.textContent = '読み込み中...';
                    button.disabled = true;
                    self.loadMoreCategories(button);
                }
            });
        });
        
        // Production: removed
    };
    
    /**
     * カテゴリの追加読み込み
     */
    ArchiveCommon.loadMoreCategories = function(button) {
        var self = this;
        var formData = new FormData();
        formData.append('action', 'gi_get_all_categories');
        formData.append('nonce', this.config.nonce);
        formData.append('offset', 8);
        
        fetch(this.config.ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(function(data) {
            if (data.success && data.data.categories) {
                var optionsContainer = document.querySelector('#sidebar-category-filter .sidebar-filter-options');
                var categories = data.data.categories;
                
                categories.forEach(function(category) {
                    var label = document.createElement('label');
                    label.className = 'sidebar-filter-option';
                    label.innerHTML = 
                        '<input type="checkbox" name="sidebar_category[]" value="' + category.slug + '">' +
                        '<span class="checkbox-custom"></span>' +
                        '<span class="option-label">' + category.name + '</span>' +
                        '<span class="option-count">' + category.count + '</span>';
                    
                    label.querySelector('input').addEventListener('change', function() {
                        self.updateSidebarFilterCounts();
                    });
                    
                    optionsContainer.insertBefore(label, button);
                });
                
                button.remove();
                // Production: removed
            } else {
                button.textContent = 'カテゴリの取得に失敗しました';
                setTimeout(function() {
                    button.textContent = 'さらに表示';
                    button.disabled = false;
                }, 2000);
            }
        })
        .catch(function(error) {
            console.error('カテゴリ取得エラー:', error);
            button.textContent = 'エラーが発生しました';
            setTimeout(function() {
                button.textContent = 'さらに表示';
                button.disabled = false;
            }, 2000);
        });
    };
    
    /**
     * サイドバーフィルターカウントを更新
     */
    ArchiveCommon.updateSidebarFilterCounts = function() {
        var filterGroups = document.querySelectorAll('.sidebar-filter-group');
        filterGroups.forEach(function(group) {
            var checkedCount = group.querySelectorAll('input[type="checkbox"]:checked').length;
            var countBadge = group.querySelector('.filter-selected-count');
            if (countBadge) {
                if (checkedCount > 0) {
                    countBadge.textContent = checkedCount;
                    countBadge.style.display = 'inline-flex';
                } else {
                    countBadge.style.display = 'none';
                }
            }
        });
    };
    
    /**
     * サイドバーフィルターを適用
     */
    ArchiveCommon.applySidebarFilters = function() {
        var self = this;
        
        if (!this.state) {
            console.error('ArchiveCommon state not available');
            return;
        }
        
        // Production: removed
        
        var state = this.state;
        
        // キーワード検索
        var searchInput = document.getElementById('sidebar-keyword-search');
        if (searchInput) {
            var searchValue = searchInput.value.trim();
            state.filters.search = searchValue;
            var mainSearch = document.getElementById('keyword-search');
            if (mainSearch) mainSearch.value = searchValue;
            // Production: removed
        }
        
        // カテゴリ
        var categoryCheckboxes = document.querySelectorAll('input[name="sidebar_category[]"]:checked');
        state.filters.category = Array.from(categoryCheckboxes).map(function(cb) { return cb.value; });
        
        // 都道府県
        var prefectureCheckboxes = document.querySelectorAll('input[name="sidebar_prefecture[]"]:checked');
        state.filters.prefecture = Array.from(prefectureCheckboxes).map(function(cb) { return cb.value; });
        
        // 地域
        var regionCheckboxes = document.querySelectorAll('input[name="sidebar_region[]"]:checked');
        state.filters.region = regionCheckboxes.length > 0 ? regionCheckboxes[0].value : '';
        
        // 助成金額
        var amountCheckboxes = document.querySelectorAll('input[name="sidebar_amount[]"]:checked');
        state.filters.amount = amountCheckboxes.length > 0 ? amountCheckboxes[0].value : '';
        // Production: removed
        
        // 募集状況
        var statusCheckboxes = document.querySelectorAll('input[name="sidebar_status[]"]:checked');
        state.filters.status = statusCheckboxes.length > 0 ? statusCheckboxes[0].value : '';
        // Production: removed
        
        // 検索実行
        state.currentPage = 1;
        // Production: removed
        this.loadGrants();
        this.updateActiveFiltersDisplay();
        
        // 結果エリアへスクロール
        var resultsHeader = document.querySelector('.zukan-results-header, .unified-results-header, .results-header');
        if (resultsHeader) {
            setTimeout(function() {
                resultsHeader.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    };
    
    /**
     * サイドバーフィルターをリセット
     */
    ArchiveCommon.resetSidebarFilters = function() {
        // 全チェックボックスをリセット
        var checkboxes = document.querySelectorAll('.sidebar-filter-option input[type="checkbox"]');
        checkboxes.forEach(function(cb) { cb.checked = false; });
        
        // 検索欄をクリア
        var searchInput = document.getElementById('sidebar-keyword-search');
        if (searchInput) searchInput.value = '';
        
        // カウントバッジをリセット
        this.updateSidebarFilterCounts();
        
        // メインフィルターをリセット
        this.resetAllFilters();
    };
    
    /**
     * 初期化後のフック - サイドバー機能を自動初期化
     */
    var originalInit = ArchiveCommon.init;
    ArchiveCommon.init = function(options) {
        // 元の初期化を実行
        originalInit.call(this, options);
        
        // サイドバーフィルターと統合ソートを初期化
        var self = this;
        setTimeout(function() {
            self.initSidebarFilters();
            self.initUnifiedSortSelect();
            self.initMobileFilter(); // モバイルフィルターを初期化
        }, 0);
    };

})(window);

/**
 * ランキングタブ切り替え機能
 */
(function() {
    'use strict';
    
    function initRankingTabs() {
        var tabs = document.querySelectorAll('.ranking-tab');
        var contents = document.querySelectorAll('.ranking-content');
        
        if (tabs.length === 0) return;
        
        tabs.forEach(function(tab) {
            if (tab.dataset.initialized === 'true') return;
            tab.dataset.initialized = 'true';
            
            tab.addEventListener('click', function() {
                var period = this.getAttribute('data-period');
                var targetId = this.getAttribute('data-target');
                
                tabs.forEach(function(t) { t.classList.remove('active'); });
                this.classList.add('active');
                
                contents.forEach(function(c) { c.classList.remove('active'); });
                var targetContent = document.querySelector(targetId);
                
                if (targetContent) {
                    targetContent.classList.add('active');
                    
                    var hasLoadingDiv = targetContent.querySelector('.ranking-loading');
                    if (hasLoadingDiv && window.ArchiveCommon && window.ArchiveCommon.config.ajaxUrl) {
                        loadRankingData(period, targetContent);
                    }
                }
            });
        });
    }
    
    function loadRankingData(period, container) {
        container.innerHTML = '<div class="ranking-loading">読み込み中...</div>';
        
        var formData = new FormData();
        formData.append('action', 'get_ranking_data');
        formData.append('period', period);
        formData.append('post_type', window.ArchiveCommon ? window.ArchiveCommon.config.postType : 'grant');
        
        fetch(window.ArchiveCommon.config.ajaxUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(function(response) {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(function(data) {
            if (data.success && data.data) {
                container.innerHTML = data.data;
            } else {
                container.innerHTML = '<div class="ranking-empty" style="text-align: center; padding: 30px 20px; color: #666;"><p style="margin: 0; font-size: 14px;">データがありません</p></div>';
            }
        })
        .catch(function() {
            container.innerHTML = '<div class="ranking-error" style="text-align: center; padding: 30px 20px; color: #999;"><p style="margin: 0; font-size: 14px;">エラーが発生しました</p></div>';
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRankingTabs);
    } else {
        initRankingTabs();
    }
})();

/**
 * ハッシュスクロール処理 & グローバル関数公開
 * ページネーション後に #list へ自動スクロール
 */
(function() {
    'use strict';
    
    /**
     * フィルターカウントを更新
     */
    function updateFilterCounts() {
        var filterGroups = document.querySelectorAll('.sidebar-filter-group');
        filterGroups.forEach(function(group) {
            var checkedCount = group.querySelectorAll('input[type="checkbox"]:checked').length;
            var countBadge = group.querySelector('.filter-selected-count');
            if (countBadge) {
                if (checkedCount > 0) {
                    countBadge.textContent = checkedCount;
                    countBadge.style.display = 'inline-flex';
                } else {
                    countBadge.style.display = 'none';
                }
            }
        });
    }

    /**
     * サイドバーフィルターを適用
     */
    function applySidebarFilters() {
        if (typeof ArchiveCommon === 'undefined' || !ArchiveCommon.state) {
            return;
        }
        
        var state = ArchiveCommon.state;
        
        var searchInput = document.getElementById('sidebar-keyword-search');
        if (searchInput) {
            var searchValue = searchInput.value.trim();
            state.filters.search = searchValue;
            var mainSearch = document.getElementById('keyword-search');
            if (mainSearch) mainSearch.value = searchValue;
        }
        
        var categoryCheckboxes = document.querySelectorAll('input[name="sidebar_category[]"]:checked');
        state.filters.category = Array.from(categoryCheckboxes).map(function(cb) { return cb.value; });
        
        var prefectureCheckboxes = document.querySelectorAll('input[name="sidebar_prefecture[]"]:checked');
        state.filters.prefecture = Array.from(prefectureCheckboxes).map(function(cb) { return cb.value; });
        
        var regionCheckboxes = document.querySelectorAll('input[name="sidebar_region[]"]:checked');
        state.filters.region = regionCheckboxes.length > 0 ? regionCheckboxes[0].value : '';
        
        var amountCheckboxes = document.querySelectorAll('input[name="sidebar_amount[]"]:checked');
        state.filters.amount = amountCheckboxes.length > 0 ? amountCheckboxes[0].value : '';
        
        var statusCheckboxes = document.querySelectorAll('input[name="sidebar_status[]"]:checked');
        state.filters.status = statusCheckboxes.length > 0 ? statusCheckboxes[0].value : '';
        
        if (typeof gtag === 'function') {
            gtag('event', 'filter_apply', {
                'event_category': 'archive_filter',
                'event_label': 'sidebar_filter',
                'value': state.filters.category.length + state.filters.prefecture.length + (state.filters.search ? 1 : 0)
            });
        }
        
        state.currentPage = 1;
        ArchiveCommon.loadGrants();
        ArchiveCommon.updateActiveFiltersDisplay();
        
        var listSection = document.getElementById('list');
        if (listSection) {
            setTimeout(function() {
                listSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    }

    /**
     * サイドバーフィルターをリセット
     */
    function resetSidebarFilters() {
        var checkboxes = document.querySelectorAll('.sidebar-filter-option input[type="checkbox"]');
        checkboxes.forEach(function(cb) { cb.checked = false; });
        
        var searchInput = document.getElementById('sidebar-keyword-search');
        if (searchInput) searchInput.value = '';
        
        updateFilterCounts();
        
        if (typeof gtag === 'function') {
            gtag('event', 'filter_reset', {
                'event_category': 'archive_filter',
                'event_label': 'sidebar_filter'
            });
        }
        
        if (typeof ArchiveCommon !== 'undefined') {
            ArchiveCommon.resetAllFilters();
        }
    }

    /**
     * GA tracking
     */
    function trackResultsDisplay() {
        var showingFrom = document.getElementById('showing-from');
        var showingTo = document.getElementById('showing-to');
        var currentCount = document.getElementById('current-count');
        
        if (showingFrom && showingTo && currentCount) {
            var from = parseInt(showingFrom.textContent.replace(/,/g, '')) || 0;
            var to = parseInt(showingTo.textContent.replace(/,/g, '')) || 0;
            var total = parseInt(currentCount.textContent.replace(/,/g, '')) || 0;
            
            if (typeof gtag === 'function' && total > 0) {
                gtag('event', 'results_display', {
                    'event_category': 'archive_view',
                    'event_label': from + '〜' + to + '件を表示',
                    'value': total
                });
            }
        }
    }

    /**
     * ハッシュスクロール処理
     */
    function handleHashScroll() {
        if (window.location.hash === '#list') {
            var listSection = document.getElementById('list');
            if (listSection) {
                setTimeout(function() {
                    var headerOffset = 80;
                    var elementPosition = listSection.getBoundingClientRect().top;
                    var offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
                }, 200);
            }
        }
    }
    
    // グローバルに公開
    window.applySidebarFilters = applySidebarFilters;
    window.resetSidebarFilters = resetSidebarFilters;
    window.updateFilterCounts = updateFilterCounts;
    window.trackResultsDisplay = trackResultsDisplay;
    
    // DOM ready時
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            handleHashScroll();
            setTimeout(trackResultsDisplay, 2000);
        });
    } else {
        handleHashScroll();
        setTimeout(trackResultsDisplay, 2000);
    }
})();
