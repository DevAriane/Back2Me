<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Les routes concernées

    'allowed_methods' => ['*'], // Toutes les méthodes HTTP

    'allowed_origins' => [
        'http://localhost:8080',        // Si vous testez en Flutter Web
        'http://10.0.2.2:8000',         // Pour l'émulateur Android (10.0.2.2 = localhost)
        'http://127.0.0.1:8000',        // Pour les tests
        // Ajoutez l'URL de votre application en production (ex: https://app.example.com)
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Tous les en-têtes

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Important pour Sanctum (cookies)
];