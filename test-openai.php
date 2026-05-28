<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing OpenAI API Connection ===\n\n";

// Check API Key
$apiKey = config('services.openai.api_key');
if (!$apiKey) {
    echo "❌ ERROR: API Key not configured!\n";
    exit(1);
}

echo "✅ API Key found: " . substr($apiKey, 0, 20) . "..." . substr($apiKey, -4) . "\n";
echo "   Length: " . strlen($apiKey) . " characters\n\n";

// Test API Connection
try {
    echo "📡 Sending test request to OpenAI...\n";
    
    $client = OpenAI::client($apiKey);
    
    $response = $client->chat()->create([
        'model' => 'gpt-4o-mini',
        'messages' => [
            ['role' => 'user', 'content' => 'Say "Hello from DashboardGuru!"']
        ],
        'max_tokens' => 20,
    ]);

    echo "✅ SUCCESS! API is working!\n\n";
    echo "Response: " . $response->choices[0]->message->content . "\n";
    echo "\nModel: " . $response->model . "\n";
    echo "Usage: " . $response->usage->totalTokens . " tokens\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    
    if (str_contains($e->getMessage(), 'rate limit')) {
        echo "💡 This is a RATE LIMIT error.\n";
        echo "   Solutions:\n";
        echo "   1. Wait 1-2 minutes and try again\n";
        echo "   2. Your account is on Free Tier (3 requests/minute)\n";
        echo "   3. Consider upgrading to Tier 1 ($5 credit)\n";
    } elseif (str_contains($e->getMessage(), 'invalid')) {
        echo "💡 API Key might be invalid or expired.\n";
        echo "   Check: https://platform.openai.com/api-keys\n";
    } elseif (str_contains($e->getMessage(), 'insufficient')) {
        echo "💡 No credit/quota remaining.\n";
        echo "   Add credit at: https://platform.openai.com/billing\n";
    }
    
    exit(1);
}

echo "\n=== Test Complete ===\n";
