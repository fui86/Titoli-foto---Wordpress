#!/usr/bin/env php
<?php
/**
 * Test script for Hugging Face API integration
 * 
 * This script demonstrates how the plugin communicates with Hugging Face API.
 * It's a standalone test that doesn't require WordPress.
 * 
 * Usage:
 *   1. Set your Hugging Face API token below
 *   2. Run: php test-api.php
 */

// Configuration
$API_TOKEN = 'YOUR_HUGGING_FACE_API_TOKEN_HERE'; // Replace with your token
$API_ENDPOINT = 'https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-large';

// Test image URL (you can use any publicly accessible image)
$TEST_IMAGE_URL = 'https://huggingface.co/datasets/huggingface/documentation-images/resolve/main/transformers/tasks/car.jpg';

echo "=====================================\n";
echo "Image AI Metadata - API Test Script\n";
echo "=====================================\n\n";

// Validate token
if ($API_TOKEN === 'YOUR_HUGGING_FACE_API_TOKEN_HERE') {
    echo "❌ ERROR: Please set your Hugging Face API token in the script!\n";
    echo "\n";
    echo "To get a token:\n";
    echo "1. Visit https://huggingface.co/settings/tokens\n";
    echo "2. Create a new token (Read access)\n";
    echo "3. Replace YOUR_HUGGING_FACE_API_TOKEN_HERE in this script\n";
    echo "\n";
    exit(1);
}

echo "🔧 Configuration:\n";
echo "   Endpoint: {$API_ENDPOINT}\n";
echo "   Token: " . substr($API_TOKEN, 0, 10) . "...\n\n";

echo "📥 Downloading test image...\n";
$image_data = @file_get_contents($TEST_IMAGE_URL);

if ($image_data === false) {
    echo "❌ ERROR: Failed to download test image from {$TEST_IMAGE_URL}\n";
    exit(1);
}

$image_size = strlen($image_data);
echo "✓ Image downloaded: " . number_format($image_size) . " bytes\n\n";

echo "🤖 Calling Hugging Face API...\n";

// Initialize cURL
$ch = curl_init($API_ENDPOINT);

// Set cURL options
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $API_TOKEN,
        'Content-Type: application/octet-stream'
    ],
    CURLOPT_POSTFIELDS => $image_data,
    CURLOPT_TIMEOUT => 30
]);

// Execute request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Check for cURL errors
if ($response === false) {
    echo "❌ ERROR: cURL error: {$curl_error}\n";
    exit(1);
}

echo "✓ API Response received (HTTP {$http_code})\n\n";

// Check HTTP status
if ($http_code !== 200) {
    echo "❌ ERROR: API returned HTTP {$http_code}\n";
    echo "Response: {$response}\n\n";
    
    if ($http_code === 401) {
        echo "💡 This usually means your API token is invalid or expired.\n";
        echo "   Please check your token at https://huggingface.co/settings/tokens\n";
    } elseif ($http_code === 503) {
        echo "💡 Model is loading. This is normal on first use.\n";
        echo "   Wait 20 seconds and try again.\n";
    }
    
    exit(1);
}

// Parse JSON response
$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "❌ ERROR: Failed to parse JSON response\n";
    echo "Response: {$response}\n";
    exit(1);
}

// Extract description
if (!isset($data[0]['generated_text'])) {
    echo "❌ ERROR: Unexpected response format\n";
    echo "Response: " . print_r($data, true) . "\n";
    exit(1);
}

$description = $data[0]['generated_text'];

echo "=====================================\n";
echo "✅ SUCCESS!\n";
echo "=====================================\n\n";

echo "AI Generated Description:\n";
echo "   \"{$description}\"\n\n";

echo "This description would be used as:\n";
echo "   • Alt Text: {$description}\n";
echo "   • Title: " . ucfirst($description) . "\n";
echo "   • Caption: {$description}\n";
echo "   • Description: {$description}\n\n";

echo "=====================================\n";
echo "Test completed successfully! ✓\n";
echo "=====================================\n\n";

echo "Next steps:\n";
echo "1. Install the plugin in WordPress\n";
echo "2. Go to Settings → Image AI Metadata\n";
echo "3. Enter your API token\n";
echo "4. Upload images to test\n\n";
