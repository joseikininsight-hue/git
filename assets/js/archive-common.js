/**
 * Archive Common JavaScript - v19.0
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
            
            console.log('🚀 Archive Common JS v19.0 Initialized');
            console.log('📋 Post Type:', this.config.postType);
            
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
            el.filterPanel = document.getElementById('filter-panel');
            el.mobileFilterCount = document.getElementById('mobile-filter-count');
            el.filterPanelOverlay = document.getElementById('filter-panel-overlay');
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
                el.keywordSearch.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        self.handleSearch();
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
         * 検索入力ハンドラ
         */
        handleSearchInput: function() {
            const el = this.elements;
            const query = el.keywordSearch.value.trim();
            if (el.searchClearBtn) {
                el.searchClearBtn.style.display = query.length > 0 ? 'flex' : 'none';
            }
        },

        /**
         * 検索実行
         */
        handleSearch: function() {
            const el = this.elements;
            this.state.filters.search = el.keywordSearch.value.trim();
            this.state.currentPage = 1;
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
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    self.displayGrants(data.data.grants);
                    self.updateStats(data.data.stats);
                    self.updatePagination(data.data.pagination);
                    self.updateActiveFiltersDisplay();
                } else {
                    self.showError('データの読み込みに失敗しました。');
                }
            })
            .catch(function(error) {
                console.error('Fetch Error:', error);
                self.showError('通信エラーが発生しました。');
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
         */
        updateStats: function(stats) {
            const el = this.elements;
            if (el.resultsCount) el.resultsCount.textContent = (stats.total_found || 0).toLocaleString();
            if (el.showingFrom) el.showingFrom.textContent = (stats.showing_from || 0).toLocaleString();
            if (el.showingTo) el.showingTo.textContent = (stats.showing_to || 0).toLocaleString();
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
         * ローディング表示
         */
        showLoading: function(show) {
            const el = this.elements;
            if (el.loadingOverlay) el.loadingOverlay.style.display = show ? 'flex' : 'none';
            if (el.grantsContainer) el.grantsContainer.style.opacity = show ? '0.5' : '1';
        },

        /**
         * エラー表示
         */
        showError: function(message) {
            console.error('Error:', message);
            alert(message);
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

    console.log('✅ Archive Common JS - Fully Loaded');

})(window);

/**
 * ランキングタブ切り替え機能
 */
(function() {
    'use strict';
    
    // DOM読み込み後に初期化
    function initRankingTabs() {
        const tabs = document.querySelectorAll('.ranking-tab');
        const contents = document.querySelectorAll('.ranking-content');
        
        if (tabs.length === 0) return;
        
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                const period = this.getAttribute('data-period');
                const targetId = this.getAttribute('data-target');
                
                console.log('📊 Tab clicked - Period:', period, 'Target:', targetId);
                
                tabs.forEach(function(t) { t.classList.remove('active'); });
                this.classList.add('active');
                
                contents.forEach(function(c) { c.classList.remove('active'); });
                const targetContent = document.querySelector(targetId);
                
                if (targetContent) {
                    targetContent.classList.add('active');
                    
                    const hasLoadingDiv = targetContent.querySelector('.ranking-loading');
                    if (hasLoadingDiv && window.ArchiveCommon && window.ArchiveCommon.config.ajaxUrl) {
                        loadRankingData(period, targetContent);
                    }
                }
            });
        });
        
        console.log('✅ Ranking tabs initialized');
    }
    
    function loadRankingData(period, container) {
        console.log('🔄 Loading ranking data for period:', period);
        
        container.innerHTML = '<div class="ranking-loading">読み込み中...</div>';
        
        const formData = new FormData();
        formData.append('action', 'get_ranking_data');
        formData.append('period', period);
        formData.append('post_type', window.ArchiveCommon ? window.ArchiveCommon.config.postType : 'grant');
        
        fetch(window.ArchiveCommon.config.ajaxUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success && data.data) {
                container.innerHTML = data.data;
            } else {
                container.innerHTML = '<div class="ranking-empty" style="text-align: center; padding: 30px 20px; color: #666;"><p style="margin: 0; font-size: 14px;">データがありません</p></div>';
            }
        })
        .catch(function(error) {
            console.error('❌ Fetch Error:', error);
            container.innerHTML = '<div class="ranking-error" style="text-align: center; padding: 30px 20px; color: #999;"><p style="margin: 0; font-size: 14px;">エラーが発生しました</p></div>';
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRankingTabs);
    } else {
        initRankingTabs();
    }
})();
