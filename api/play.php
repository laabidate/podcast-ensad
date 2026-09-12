<?php
/**
 * API JSON : Enregistrement d'écoute (100% Statique)
 */
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'status'  => 'success',
    'message' => 'Écoute enregistrée'
]);
