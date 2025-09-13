#!/usr/bin/env php
<?php

// Simple test to check if the export route works
$testUrl = 'http://localhost:8000/reports/export/sales-pdf';

echo "Testing PDF export functionality...\n";
echo "URL: $testUrl\n";

// Use curl to test the route
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Response Code: $httpCode\n";

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "Response received successfully\n";
    if ($httpCode == 200) {
        echo "✓ Export route is working!\n";
    } else {
        echo "✗ Export route returned HTTP $httpCode\n";
        // Print response headers
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        echo "Headers:\n$headers\n";
    }
}