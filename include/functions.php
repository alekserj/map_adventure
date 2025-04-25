<?php
if (!function_exists('old')) {
    function old($field) {
        return $_SESSION['old'][$field] ?? '';
    }
}

if (!function_exists('validationErrorAttr')) {
    function validationErrorAttr($field) {
        if (hasValidationError($field)) {
            echo 'class="is-invalid"';
        }
    }
}

if (!function_exists('hasValidationError')) {
    function hasValidationError($field) {
        return isset($_SESSION['validation'][$field]);
    }
}

if (!function_exists('validationErrorMessage')) {
    function validationErrorMessage($field) {
        echo $_SESSION['validation'][$field] ?? '';
    }
}

if (!function_exists('hasMessage')) {
    function hasMessage($key) {
        return isset($_SESSION[$key]);
    }
}

if (!function_exists('getMessage')) {
    function getMessage($key) {
        $message = $_SESSION[$key] ?? '';
        unset($_SESSION[$key]);
        return $message;
    }
}

if (!function_exists('clearValidation')) {
    function clearValidation() {
        unset($_SESSION['validation'], $_SESSION['old']);
    }
}
?>