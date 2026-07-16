class CustomInput {
    constructor(container) {
        this.container = container;
        this.input = container.querySelector('input[type="text"]');
        this.init();
    }
    
    init() {
        // Динамическая валидация
        this.input.addEventListener('input', () => this.validate());
        this.input.addEventListener('blur', () => this.validate());
        
        // Очистка ошибки при фокусе
        this.input.addEventListener('focus', () => {
            this.container.classList.remove('error');
        });
    }
    
    validate() {
        const value = this.input.value.trim();
        
        // Сброс состояний
        this.container.classList.remove('error', 'success');
        
        if (this.input.hasAttribute('required') && !value) {
            this.container.classList.add('error');
            return false;
        }
        
        if (value && this.isValid(value)) {
            this.container.classList.add('success');
        }
        
        return true;
    }
    
    isValid(value) {
        // Кастомная валидация в зависимости от типа
        if (this.input.type === 'email') {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        }
        
        return true;
    }
    
    // Публичные методы
    setError(message) {
        this.container.classList.add('error');
        // Можно добавить tooltip с message
    }
    
    clearError() {
        this.container.classList.remove('error');
    }
    
    setSuccess() {
        this.container.classList.add('success');
    }
    
    getValue() {
        return this.input.value;
    }
    
    setValue(value) {
        this.input.value = value;
        this.validate();
    }
}


// Инициализация всех кастомных input'ов
document.addEventListener('DOMContentLoaded', function() {
    const customInputs = document.querySelectorAll('.custom-input');
    customInputs.forEach(container => {
        new CustomInput(container);
    });

    

});