<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing OpenRouter API ===\n\n";

$apiKey = config('services.openai.api_key');
$baseUrl = config('services.openai.base_url');

if (!$apiKey) {
    echo "❌ ERROR: API Key not configured!\n";
    echo "Please add your OpenRouter API key to .env:\n";
    echo "OPENAI_API_KEY=sk-or-v1-xxxxxxxx\n\n";
    echo "Get your FREE API key at: https://openrouter.ai/keys\n";
    exit(1);
}

echo "API Key: " . substr($apiKey, 0, 15) . "..." . substr($apiKey, -4) . "\n";
echo "Base URL: " . ($baseUrl ?: 'OpenAI Direct') . "\n";
echo "Provider: " . ($baseUrl ? 'OpenRouter' : 'OpenAI') . "\n\n";

// Test API
try {
    echo "📡 Testing API connection...\n";
    
    // Create client with OpenRouter support
    if ($baseUrl) {
        $client = OpenAI::factory()
            ->withApiKey($apiKey)
            ->withBaseUri($baseUrl)
            ->withHttpHeader('HTTP-Referer', config('app.url'))
            ->withHttpHeader('X-Title', 'DashboardGuru')
            ->make();
        
        $model = 'openai/gpt-4o-mini'; // OpenRouter format
    } else {
        $client = OpenAI::client($apiKey);
        $model = 'gpt-4o-mini'; // OpenAI format
    }
    
    echo "Model: {$model}\n";
    echo "Sending request...\n\n";
    
    $response = $client->chat()->create([
        'model' => $model,
        'messages' => [
            ['role' => 'user', 'content' => 'Say "Hello from DashboardGuru!" in Bahasa Indonesia']
        ],
        'max_tokens' => 50,
    ]);

    echo "✅ SUCCESS! API is working!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Response: " . $response->choices[0]->message->content . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    if (isset($response->model)) {
        echo "Model used: " . $response->model . "\n";
    }
    if (isset($response->usage)) {
        echo "Tokens used: " . $response->usage->totalTokens . " tokens\n";
    }
    
    echo "\n💡 Your AI Question Generator is ready to use!\n";
    echo "   Go to: Dashboard Guru → Bank Soal → Generate dengan AI\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo $e->getMessage() . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    if (str_contains(strtolower($e->getMessage()), 'rate limit')) {
        echo "🔍 RATE LIMIT ERROR\n\n";
        echo "If using OpenRouter:\n";
        echo "• You might need to wait a moment\n";
        echo "• Check your credits: https://openrouter.ai/credits\n";
        echo "• Try using free model: google/gemini-pro\n\n";
        
    } elseif (str_contains(strtolower($e->getMessage()), 'invalid') || 
              str_contains(strtolower($e->getMessage()), 'unauthorized')) {
        echo "🔍 INVALID API KEY\n\n";
        echo "Solutions:\n";
        echo "1. Get new API key from: https://openrouter.ai/keys\n";
        echo "2. Make sure key starts with: sk-or-v1-\n";
        echo "3. Update .env file with correct key\n";
        echo "4. Run: php artisan config:clear\n\n";
        
    } elseif (str_contains(strtolower($e->getMessage()), 'insufficient') || 
              str_contains(strtolower($e->getMessage()), 'credit')) {
        echo "🔍 INSUFFICIENT CREDITS\n\n";
        echo "Solutions:\n";
        echo "1. Add credits at: https://openrouter.ai/credits\n";
        echo "2. Use FREE model by editing OpenAIService.php:\n";
        echo "   Change model to: 'google/gemini-pro'\n\n";
    }
    
    exit(1);
}

echo "\n=== Test Complete ===\n";
