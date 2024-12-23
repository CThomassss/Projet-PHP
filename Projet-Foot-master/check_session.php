<?php
session_start();
header('Content-Type: application/json');

$response = [
    'logged_in' => isset($_SESSION['utilisateur_id']),
    'session_id' => session_id(),
    'session_status' => session_status(),
    'utilisateur_id' => $_SESSION['utilisateur_id'] ?? null,
    'timestamp' => time()
];

echo json_encode($response);