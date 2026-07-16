class CustomSelect {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.selected = this.container.querySelector('.select-selected');
        this.optionsContainer = this.container.querySelector('.select-options');
        this.searchInput = this.container.querySelector('.select-search');
        this.options = Array.from(this.container.querySelectorAll('.select-option'));
        this.isOpen = false;
        
        this.init();
    }
    
    init() {
        this.selected.addEventListener('click', () => this.toggle());
        document.addEventListener('click', (e) => this.handleClickOutside(e));
        
        this.searchInput?.addEventListener('input', () => this.filterOptions());
        
        this.options.forEach(option => {
            option.addEventListener('click', (e) => {
                if (option.tagName === 'A') {
                    e.preventDefault();
                }
                this.selectOption(option);
            });
        });
        
        this.selected.addEventListener('keydown', (e) => this.handleKeydown(e));
        this.searchInput?.addEventListener('keydown', (e) => this.handleSearchKeydown(e));
    }
    
    toggle() {
        if (this.container.classList.contains('disabled')) return;
        
        this.isOpen = !this.isOpen;
        this.container.classList.toggle('active', this.isOpen);
        
        if (this.isOpen) {
            if (this.searchInput) {
                this.searchInput.value = '';
                this.filterOptions();
                this.searchInput.focus();
            }
        }
    }
    
    handleClickOutside(e) {
        if (!this.container.contains(e.target)) {
            this.close();
        }
    }
    
    close() {
        this.isOpen = false;
        this.container.classList.remove('active');
    }
    
    filterOptions() {
        const searchTerm = (this.searchInput?.value || '').toLowerCase();
        
        this.options.forEach(option => {
            const text = option.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm);
            option.style.display = isVisible ? 'block' : 'none';
        });
    }
    
    selectOption(option) {
        if (option.classList.contains('disabled')) return;
        
        this.options.forEach(opt => opt.classList.remove('selected'));
        
        option.classList.add('selected');
        
        const selectedText = this.container.querySelector('.selected-text');
        selectedText.textContent = option.textContent;

        const selectedInput = this.container.querySelector(`input[name="selected-value"]`);
        selectedInput.value = option.dataset.value;

        const inputEvent = new Event('change', { bubbles: true });
            selectedInput.dispatchEvent(inputEvent);
        
        this.updateNativeSelect(option.dataset.value);
        
        this.close();
        
        this.triggerChangeEvent(option.dataset.value, option.textContent);
    }
    
    updateNativeSelect(value) {
        const nativeSelect = document.getElementById('standardSelect');
        if (nativeSelect) {
            nativeSelect.value = value;
        }
    }
    
    triggerChangeEvent(value, text) {
        const event = new CustomEvent('selectChange', {
            detail: { value, text }
        });
        this.container.dispatchEvent(event);
    }
    
    handleKeydown(e) {
        switch(e.key) {
            case 'Enter':
            case ' ':
                e.preventDefault();
                this.toggle();
                break;
            case 'ArrowDown':
                e.preventDefault();
                this.openAndFocusFirst();
                break;
            case 'Escape':
                this.close();
                break;
        }
    }
    
    handleSearchKeydown(e) {
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.focusNextOption();
                break;
            case 'ArrowUp':
                e.preventDefault();
                this.focusPreviousOption();
                break;
            case 'Escape':
                this.close();
                break;
            case 'Enter':
                e.preventDefault();
                const focused = this.optionsContainer.querySelector('.select-option:focus');
                if (focused) {
                    this.selectOption(focused);
                }
                break;
        }
    }
    
    openAndFocusFirst() {
        if (!this.isOpen) {
            this.toggle();
        }
        setTimeout(() => {
            const firstVisible = this.getVisibleOptions()[0];
            if (firstVisible) firstVisible.focus();
        }, 100);
    }
    
    focusNextOption() {
        const visibleOptions = this.getVisibleOptions();
        const currentIndex = visibleOptions.findIndex(opt => opt === document.activeElement);
        const nextIndex = (currentIndex + 1) % visibleOptions.length;
        visibleOptions[nextIndex]?.focus();
    }
    
    focusPreviousOption() {
        const visibleOptions = this.getVisibleOptions();
        const currentIndex = visibleOptions.findIndex(opt => opt === document.activeElement);
        const prevIndex = (currentIndex - 1 + visibleOptions.length) % visibleOptions.length;
        visibleOptions[prevIndex]?.focus();
    }
    
    getVisibleOptions() {
        return this.options.filter(opt => opt.style.display !== 'none');
    }
    
    setValue(value) {
        const option = this.options.find(opt => opt.dataset.value === value);
        if (option) {
            this.selectOption(option);
        }
    }
    
    getValue() {
        const nativeSelect = document.getElementById('standardSelect');
        return nativeSelect ? nativeSelect.value : '';
    }
    
    disable() {
        this.container.classList.add('disabled');
        this.close();
    }
    
    enable() {
        this.container.classList.remove('disabled');
    }
    
    addOption(value, text, disabled = false) {
        const option = document.createElement('div');
        option.className = `select-option ${disabled ? 'disabled' : ''}`;
        option.dataset.value = value;
        option.textContent = text;
        option.tabIndex = -1;
        
        option.addEventListener('click', () => this.selectOption(option));
        this.optionsContainer.querySelector('.select-options').appendChild(option);
        this.options.push(option);
    }
    
    destroy() {
        this.selected.removeEventListener('click', this.toggle);
        document.removeEventListener('click', this.handleClickOutside);
        this.searchInput.removeEventListener('input', this.filterOptions);
    }
}

    // Инициализация
    // const customSelect = new CustomSelect('customSelect');
    
    // Пример использования событий
    // customSelect.container.addEventListener('selectChange', function(e) {
    //     console.log('Выбрано:', e.detail.value, e.detail.text);
    // });
    
    // Пример публичных методов
    // customSelect.setValue('3');
    // customSelect.disable();
    // customSelect.addOption('6', 'Новая опция');
