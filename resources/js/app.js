import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Dark mode functionality
(function() {
    // Function to get theme preference
    function getThemePreference() {
        return localStorage.getItem('theme') || 
               (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    }

    // Function to set theme
    function setTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('theme', theme);
    }

    // Function to toggle theme
    function toggleTheme() {
        const currentTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        setTheme(currentTheme === 'dark' ? 'light' : 'dark');
    }

    // Initialize theme on page load
    setTheme(getThemePreference());

    // Expose toggle function globally
    window.toggleDarkMode = toggleTheme;
    window.getCurrentTheme = () => document.documentElement.classList.contains('dark') ? 'dark' : 'light';
})();
