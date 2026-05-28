<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== OpenAI Account Checker ===\n\n";

$apiKey = config('services.openai.api_key');
if (!$apiKey) {
    echo "❌ ERROR: API Key not configured!\n";
    exit(1);
}

echo "API Key: " . substr($apiKey, 0, 20) . "..." . substr($apiKey, -4) . "\n";
echo "Length: " . strlen($apiKey) . " characters\n\n";

// Test dengan request sangat kecil
try {
    echo "📡 Testing API connection with minimal request...\n";
    
    $client = OpenAI::client($apiKey);
    
    // Try to get models list (cheaper than chat)
    echo "   • Fetching models list...\n";
    try {
        $models = $client->models()->list();
        echo "   ✅ Models accessible: " . count($models->data) . " models found\n\n";
    } catch (Exception $e) {
        echo "   ⚠️ Models endpoint: " . $e->getMessage() . "\n\n";
    }
    
    // Try chat with absolute minimum tokens
    echo "   • Testing chat completion (1 token)...\n";
    $response = $client->chat()->create([
        'model' => 'gpt-4o-mini',
        'messages' => [
            ['role' => 'user', 'content' => 'Hi']
        ],
        'max_tokens' => 1,
        'temperature' => 0,
    ]);

    echo "\n✅ SUCCESS! Your API key is working!\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Model used: " . $response->model . "\n";
    echo "Response: " . $response->choices[0]->message->content . "\n";
    echo "Tokens used: " . $response->usage->totalTokens . " tokens\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "💡 Your account is working properly!\n";
    echo "   You can now use the AI Question Generator.\n";
    
} catch (Exception $e) {
    $errorMsg = $e->getMessage();
    
    echo "\n❌ ERROR OCCURRED\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo $errorMsg . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Detailed diagnostics
    if (str_contains(strtolower($errorMsg), 'rate limit')) {
        echo "🔍 DIAGNOSIS: RATE LIMIT\n\n";
        echo "Possible causes:\n";
        echo "1. ⏰ Recent API usage (within last 60 seconds)\n";
        echo "2. 📊 Account on Free Tier with very strict limits\n";
        echo "3. 🆕 New account with restricted access\n";
        echo "4. 📱 Phone verification might be required\n\n";
        
        echo "🎯 SOLUTIONS:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Option 1: VERIFY YOUR ACCOUNT\n";
        echo "   → Go to: https://platform.openai.com/settings/organization\n";
        echo "   → Add phone number for verification\n";
        echo "   → This often increases free tier limits\n\n";
        
        echo "Option 2: ADD PAYMENT METHOD (Even $1 helps)\n";
        echo "   → Go to: https://platform.openai.com/settings/organization/billing\n";
        echo "   → Add payment method\n";
        echo "   → Add $5 credit (Tier 1: 500 RPM vs 3 RPM)\n";
        echo "   → Worth it: ~Rp 75k for 250-500 generates!\n\n";
        
        echo "Option 3: WAIT STRATEGY\n";
        echo "   → Wait 2-5 minutes for complete cooldown\n";
        echo "   → Try during off-peak hours (6-8 AM or 10-12 PM)\n";
        echo "   → Generate only 1-2 questions at a time\n\n";
        
    } elseif (str_contains(strtolower($errorMsg), 'invalid')) {
        echo "🔍 DIAGNOSIS: INVALID API KEY\n\n";
        echo "The API key might be:\n";
        echo "• Deleted or revoked\n";
        echo "• Expired\n";
        echo "• Copied incorrectly\n\n";
        echo "SOLUTION: Create a new API key\n";
        echo "→ https://platform.openai.com/api-keys\n\n";
        
    } elseif (str_contains(strtolower($errorMsg), 'quota') || str_contains(strtolower($errorMsg), 'insufficient')) {
        echo "🔍 DIAGNOSIS: INSUFFICIENT QUOTA\n\n";
        echo "Your account has no credits or quota.\n\n";
        echo "SOLUTION: Add credits\n";
        echo "→ https://platform.openai.com/settings/organization/billing\n";
        echo "→ Minimum: $5 USD\n\n";
        
    } else {
        echo "🔍 DIAGNOSIS: UNKNOWN ERROR\n\n";
        echo "Please check:\n";
        echo "1. OpenAI service status: https://status.openai.com\n";
        echo "2. Your account status: https://platform.openai.com/account\n";
        echo "3. API key permissions: https://platform.openai.com/api-keys\n\n";
    }
    
    exit(1);
}

echo "\n=== Check Complete ===\n";
