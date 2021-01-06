/**
 * Custom dark mode
 */

// Button switch
const btn = document.querySelector(".dark-mode-switcher");

// Get user preference
const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)").matches;

// Select the theme preference from localStorage
const currentTheme = localStorage.getItem("theme") ? localStorage.getItem("theme") : null;

// If the current theme in localStorage is "dark" or user prefer dark, apply it
if (currentTheme == "dark" || (currentTheme == null && prefersDarkScheme)) {
    document.body.classList.add("dark-mode");
}

function darkModeSwith() {
    // Toggle the .dark-theme class on each click
    document.body.classList.toggle("dark-mode");

    // If the body contains the .dark-theme class...
    // Then save the choice in localStorage
    if (document.body.classList.contains("dark-mode")) {
        localStorage.setItem("theme", "dark");
    } else {
        localStorage.setItem("theme", "light");
    }

    // Close mobile menu
    document.querySelector('#toggle').classList.remove('active');
    document.querySelector('#overlay').classList.remove('open');
    document.body.classList.remove('mobile-nav-open');
}
