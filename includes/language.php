<?php
/**
 * Language Handler
 *
 * This file handles language selection and loading for the Citizen Complaints System.
 */

// Available languages
$available_languages = [
    'en' => 'English',
    'fr' => 'Français',
    'ar' => 'العربية'
];

// Default language
$default_language = 'en';

// Get language from cookie, session, or browser preference
function get_current_language() {
    global $available_languages, $default_language;

    // Check if language is set in session
    if (isset($_SESSION['language']) && array_key_exists($_SESSION['language'], $available_languages)) {
        return $_SESSION['language'];
    }

    // Check if language is set in cookie
    if (isset($_COOKIE['language']) && array_key_exists($_COOKIE['language'], $available_languages)) {
        return $_COOKIE['language'];
    }

    // Check browser language preference
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        foreach ($browser_languages as $browser_language) {
            $browser_language = substr($browser_language, 0, 2);
            if (array_key_exists($browser_language, $available_languages)) {
                return $browser_language;
            }
        }
    }

    // Return default language
    return $default_language;
}

// Set language
function set_language($language) {
    global $available_languages, $default_language;

    // Validate language
    if (!array_key_exists($language, $available_languages)) {
        $language = $default_language;
    }

    // Set language in session
    $_SESSION['language'] = $language;

    // Set language in cookie (expires in 30 days)
    setcookie('language', $language, time() + (86400 * 30), '/');

    return $language;
}

// Get language direction (LTR or RTL)
function get_language_direction($language) {
    // RTL languages
    $rtl_languages = ['ar'];

    return in_array($language, $rtl_languages) ? 'rtl' : 'ltr';
}

// Load language file
function load_language_file($language) {
    global $available_languages, $default_language;

    // Validate language
    if (!array_key_exists($language, $available_languages)) {
        $language = $default_language;
    }

    // Language file path
    $language_file = __DIR__ . '/../languages/' . $language . '.php';

    // Check if language file exists
    if (file_exists($language_file)) {
        require_once $language_file;
    } else {
        // Load default language file
        require_once __DIR__ . '/../languages/' . $default_language . '.php';
    }

    return $translations;
}

// Initialize language
session_start();
$current_language = get_current_language();

// Handle language change
if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $available_languages)) {
    $current_language = set_language($_GET['lang']);
}

// Get language direction
$language_direction = get_language_direction($current_language);

// Load translations
$translations = load_language_file($current_language);

// Translation function
function __($key) {
    global $translations;

    return isset($translations[$key]) ? $translations[$key] : $key;
}
?>
