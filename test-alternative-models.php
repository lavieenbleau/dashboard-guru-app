<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Alternative OpenAI Models ===\n\n";

$apiKey = config('services.openai.api_key');
$client = OpenAI::client($apiKey);

// Models to test (from cheapest to most expensive)
$modelsToTest = [
    'gpt-3.5-turbo',      // Cheapest, might work for free tier
    'gpt-4o-mini',        // Current model we use
];

foreach ($modelsToTest as $model) {
    echo "Testing: {$model}\n";
    echo str_repeat("─", 50) . "\n";
    
    try {
        $response = $client->chat()->create([
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => 'Hi']
            ],
            'max_tokens' => 1,
        ]);
        
        echo "✅ SUCCESS!\n";
        echo "   Response: " . $response->choices[0]->message->content . "\n";
        echo "   Tokens: " . $response->usage->totalTokens . "\n";
        echo "   → This model works for your account!\n\n";
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        
        if (str_contains(strtolower($error), 'rate limit')) {
            echo "❌ Rate limit\n\n";
        } elseif (str_contains(strtolower($error), 'model')) {
            echo "❌ Model not available\n\n";
        } else {
            echo "❌ Error: " . substr($error, 0, 80) . "...\n\n";
        }
    }
    
    sleep(2); // Wait between tests
}

echo "=== Recommendation ===\n";
echo "If ALL models show rate limit, you need to:\n";
echo "→ Add payment method to OpenAI account\n";
echo "→ Even $1 is enough to unlock API access\n\n";
