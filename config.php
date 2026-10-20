<?php
// includes/config.php - un seul endroit pour le chemin de base du projet
if (!defined('BASE_URL')) {
    define('BASE_URL', '/TAFAWOQ');
}

// Redirection toujours vers un chemin absolu (marche depuis n'importe quel dossier)
if (!function_exists('redirect')) {
    function redirect(string $path): void {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }
}