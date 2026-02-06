<?php

// Gestion des sessions

require_once 'config.php';

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifie si un utilisateur est connecté
function isLoggedIn()
{
    return isset($_SESSION['user']);
}

// Redirection si non connecté
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Connexion simple : stocke l'utilisateur en session
function login($username)
{
    $_SESSION['user'] = $username;
}

// Déconnexion
function logout()
{
    session_unset();    // vide les variables de session
    session_destroy();  // détruit la session
    header('Location: index.php');
    exit;
}

// Récupérer l'utilisateur connecté
function getCurrentUser()
{
    return $_SESSION['user'] ?? null;
}