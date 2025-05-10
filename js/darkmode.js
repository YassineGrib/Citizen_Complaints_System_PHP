/**
 * Dark Mode Functionality
 * 
 * This file handles the dark mode toggle functionality.
 */

document.addEventListener('DOMContentLoaded', function() {
    const darkModeSwitch = document.getElementById('darkModeSwitch');
    const darkModeLabel = darkModeSwitch.nextElementSibling;
    
    // Function to set dark mode
    function setDarkMode(isDarkMode) {
        // Set cookie
        document.cookie = `dark_mode=${isDarkMode}; path=/; max-age=${60 * 60 * 24 * 365}`; // 1 year
        
        // Update body class
        if (isDarkMode) {
            document.body.classList.add('dark-mode');
            document.querySelector('.navbar').classList.remove('navbar-light', 'bg-light');
            document.querySelector('.navbar').classList.add('navbar-dark', 'bg-dark');
            darkModeLabel.textContent = darkModeLabel.getAttribute('data-light-text');
        } else {
            document.body.classList.remove('dark-mode');
            document.querySelector('.navbar').classList.remove('navbar-dark', 'bg-dark');
            document.querySelector('.navbar').classList.add('navbar-light', 'bg-light');
            darkModeLabel.textContent = darkModeLabel.getAttribute('data-dark-text');
        }
        
        // Update all cards
        document.querySelectorAll('.card').forEach(card => {
            if (isDarkMode) {
                card.classList.add('bg-dark', 'text-light', 'border-secondary');
            } else {
                card.classList.remove('bg-dark', 'text-light', 'border-secondary');
            }
        });
        
        // Update all tables
        document.querySelectorAll('table').forEach(table => {
            if (isDarkMode) {
                table.classList.add('table-dark');
            } else {
                table.classList.remove('table-dark');
            }
        });
        
        // Update form controls
        document.querySelectorAll('.form-control, .form-select').forEach(element => {
            if (isDarkMode) {
                element.classList.add('bg-dark', 'text-light', 'border-secondary');
            } else {
                element.classList.remove('bg-dark', 'text-light', 'border-secondary');
            }
        });
    }
    
    // Check system preference for dark mode
    function getSystemPreference() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    
    // Check if dark mode cookie exists
    function getDarkModeCookie() {
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'dark_mode') {
                return value === 'true';
            }
        }
        return null; // Cookie not found
    }
    
    // Initialize dark mode
    function initDarkMode() {
        const cookieValue = getDarkModeCookie();
        
        // If cookie exists, use its value
        if (cookieValue !== null) {
            darkModeSwitch.checked = cookieValue;
            setDarkMode(cookieValue);
        } 
        // Otherwise, use system preference
        else {
            const systemPreference = getSystemPreference();
            darkModeSwitch.checked = systemPreference;
            setDarkMode(systemPreference);
        }
    }
    
    // Initialize
    initDarkMode();
    
    // Listen for changes to the dark mode switch
    darkModeSwitch.addEventListener('change', function() {
        setDarkMode(this.checked);
    });
    
    // Listen for system preference changes
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            // Only update if cookie doesn't exist
            if (getDarkModeCookie() === null) {
                darkModeSwitch.checked = e.matches;
                setDarkMode(e.matches);
            }
        });
    }
});
