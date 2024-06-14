<?php
    $suggestions = [
        'Apple',
        'Banana',
        'Cherry',
        'Date',
        'Elderberry',
        'Fig',
        'Grape',
        'Honeydew'
    ];

    $query = isset($_GET['q']) ? $_GET['q'] : '';

    $results = array_filter($suggestions, function($item) use ($query) {
        return stripos($item, $query) !== false;
    });

    header('Content-Type: application/json');
    echo json_encode(array_values($results));
?>
