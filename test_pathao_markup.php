<?php
/**
 * Test script to verify Pathao price markup functionality
 * Run this to test the getPathaoPrice method with different districts
 */

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Front\CheckoutController;
use App\Repositories\Front\CartRepository;
use Illuminate\Http\Request;

echo "=== Pathao Price Markup Test ===\n\n";

// Create controller instance with required dependencies
$cartRepository = app(App\Repositories\Front\CartRepository::class);
$controller = new CheckoutController($cartRepository);

// Test data
$testCases = [
    [
        'division_id' => 1,
        'district_id' => 1, // Dhaka
        'thana_id' => 1,
        'expected_markup' => 20,
        'description' => 'Dhaka District (should have +20 markup)'
    ],
    [
        'division_id' => 1,
        'district_id' => 2, // Dhaka Metro (if exists)
        'thana_id' => 1,
        'expected_markup' => 20,
        'description' => 'Dhaka Metro District (should have +20 markup)'
    ],
    [
        'division_id' => 2,
        'district_id' => 3, // Any other district
        'thana_id' => 1,
        'expected_markup' => 40,
        'description' => 'Other District (should have +40 markup)'
    ]
];

foreach ($testCases as $i => $testCase) {
    echo "Test Case " . ($i + 1) . ": " . $testCase['description'] . "\n";
    echo "Input: division_id={$testCase['division_id']}, district_id={$testCase['district_id']}, thana_id={$testCase['thana_id']}\n";
    
    try {
        // Create request with test data
        $request = new Request();
        $request->merge([
            'division_id' => $testCase['division_id'],
            'district_id' => $testCase['district_id'],
            'thana_id' => $testCase['thana_id']
        ]);
        
        // Call the method
        $response = $controller->getPathaoPrice($request);
        $responseData = $response->getData(true);
        
        echo "Response: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
        
        // Check if markup is correct
        if (isset($responseData['breakdown']['markup'])) {
            $actualMarkup = $responseData['breakdown']['markup'];
            if ($actualMarkup == $testCase['expected_markup']) {
                echo "✅ PASS: Markup is correct ($actualMarkup)\n";
            } else {
                echo "❌ FAIL: Expected markup {$testCase['expected_markup']}, got $actualMarkup\n";
            }
        } else {
            echo "⚠️  WARNING: No markup breakdown in response\n";
        }
        
    } catch (Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "=== Test Complete ===\n";
?>
