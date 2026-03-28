import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('darkMode', {
    on: localStorage.getItem('darkMode') === 'true',

    toggle() {
        this.on = !this.on;
        localStorage.setItem('darkMode', this.on);
        document.documentElement.classList.toggle('dark', this.on);
    },

    init() {
        document.documentElement.classList.toggle('dark', this.on);
    }
});

Alpine.start();
