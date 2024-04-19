<?php

function encode_filename($filename) {
    $encoded_filename = base64_encode($filename);
    return $encoded_filename;
}

function decode_filename($encoded_filename) {
    $decoded_filename = base64_decode($encoded_filename);
    return $decoded_filename;
}

// 範例使用
$original_filename = "example_file.txt";
$encoded_filename = encode_filename($original_filename);
echo "Encoded filename: " . $encoded_filename . "<br>";

$decoded_filename = decode_filename($encoded_filename);
echo "Decoded filename: " . $decoded_filename . "<br>";
?>
