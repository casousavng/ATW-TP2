<?php
session_start();

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === 1;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function checkAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        header("Location: ../login.php");
        exit;
    }
}

