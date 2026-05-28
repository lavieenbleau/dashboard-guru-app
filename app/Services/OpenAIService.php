<?php

namespace App\Services;

use OpenAI;
use Exception;

class OpenAIService
{
    protected $client;
    protected $maxRetries = 3;
    protected $retryDelay = 2; // seconds

    public function __construct()
    {
        $apiKey = config('services.openai.api_key');
        
        if (!$apiKey) {
            throw new Exception('AI API key belum dikonfigurasi. Silakan tambahkan OpenRouter API key ke file .env Anda. Dapatkan gratis di: https://openrouter.ai/keys');
        }

        // Support for custom base URL (e.g., OpenRouter)
        $baseUrl = config('services.openai.base_url');
        
        if ($baseUrl) {
            $this->client = OpenAI::factory()
                ->withApiKey($apiKey)
                ->withBaseUri($baseUrl)
                ->withHttpHeader('HTTP-Referer', config('app.url'))
                ->withHttpHeader('X-Title', config('app.name'))
                ->make();
        } else {
            $this->client = OpenAI::client($apiKey);
        }
    }

    /**
     * Generate questions based on material illustration
     *
     * @param string $illustration Material description from teacher
     * @param string $questionType Type of question: 'pilihan_ganda' or 'essai'
     * @param string $difficulty Difficulty level: 'mudah', 'sedang', or 'sulit'
     * @param int $count Number of questions to generate
     * @return array Array of generated questions
     * @throws Exception
     */
    public function generateQuestions(string $illustration, string $questionType, string $difficulty, int $count): array
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->maxRetries) {
            try {
                $prompt = $this->buildPrompt($illustration, $questionType, $difficulty, $count);

                // Use best available model based on provider
                $model = config('services.openai.base_url') 
                    ? 'openai/gpt-4o-mini'  // OpenRouter format
                    : 'gpt-4o-mini';         // OpenAI direct format

                $response = $this->client->chat()->create([
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Anda adalah seorang guru berpengalaman yang ahli dalam membuat soal-soal berkualitas untuk siswa. Anda harus menghasilkan soal dalam format JSON yang valid.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'response_format' => ['type' => 'json_object'],
                ]);

                $content = $response->choices[0]->message->content;
                $data = json_decode($content, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Failed to parse OpenAI response: ' . json_last_error_msg());
                }

                return $this->formatQuestions($data, $questionType);

            } catch (Exception $e) {
                $lastException = $e;
                $attempt++;

                // Check if it's a rate limit error
                if ($this->isRateLimitError($e)) {
                    if ($attempt < $this->maxRetries) {
                        // Exponential backoff: wait longer with each retry
                        $delay = $this->retryDelay * pow(2, $attempt - 1);
                        sleep($delay);
                        continue;
                    }
                }

                // If not rate limit error or max retries reached, throw immediately
                throw new Exception('AI API Error: ' . $e->getMessage());
            }
        }

        // If all retries failed
        throw new Exception('AI API Error setelah ' . $this->maxRetries . ' percobaan: ' . ($lastException ? $lastException->getMessage() : 'Unknown error'));
    }

    /**
     * Check if the error is a rate limit error
     */
    private function isRateLimitError(Exception $e): bool
    {
        $message = strtolower($e->getMessage());
        return str_contains($message, 'rate limit') || 
               str_contains($message, 'too many requests') ||
               str_contains($message, '429');
    }

    /**
     * Build prompt for OpenAI based on parameters
     */
    private function buildPrompt(string $illustration, string $questionType, string $difficulty, int $count): string
    {
        $difficultyMap = [
            'mudah' => 'mudah (cocok untuk pemula)',
            'sedang' => 'sedang (tingkat menengah)',
            'sulit' => 'sulit (tingkat lanjut dengan analisis mendalam)'
        ];

        $difficultyText = $difficultyMap[$difficulty] ?? 'sedang';

        if ($questionType === 'pilihan_ganda') {
            return <<<PROMPT
Buatlah {$count} soal pilihan ganda berkualitas dengan tingkat kesulitan {$difficultyText} berdasarkan materi berikut:

{$illustration}

Format output harus dalam JSON dengan struktur:
{
  "questions": [
    {
      "title": "Judul singkat soal",
      "question": "Teks soal yang lengkap dan jelas",
      "options": {
        "A": "Pilihan A",
        "B": "Pilihan B",
        "C": "Pilihan C",
        "D": "Pilihan D"
      },
      "correct_answer": "A",
      "explanation": "Penjelasan mengapa jawaban tersebut benar"
    }
  ]
}

Pastikan:
- Setiap soal memiliki 4 pilihan jawaban (A, B, C, D)
- Hanya satu jawaban yang benar
- Question dan options menggunakan bahasa Indonesia yang baik dan benar
- Explanation memberikan penjelasan yang jelas dan edukatif
PROMPT;
        } else {
            return <<<PROMPT
Buatlah {$count} soal essay berkualitas dengan tingkat kesulitan {$difficultyText} berdasarkan materi berikut:

{$illustration}

Format output harus dalam JSON dengan struktur:
{
  "questions": [
    {
      "title": "Judul singkat soal",
      "question": "Teks soal yang mendorong analisis dan pemikiran kritis",
      "correct_answer": "Poin-poin kunci yang harus ada dalam jawaban",
      "explanation": "Penjelasan lengkap tentang jawaban yang diharapkan"
    }
  ]
}

Pastikan:
- Soal mendorong siswa untuk berpikir kritis dan analitis
- Question menggunakan bahasa Indonesia yang baik dan benar
- Correct_answer berisi poin-poin kunci yang harus ada dalam jawaban siswa
- Explanation memberikan panduan untuk penilaian
PROMPT;
        }
    }

    /**
     * Format questions from OpenAI response to match our application structure
     */
    private function formatQuestions(array $data, string $questionType): array
    {
        if (!isset($data['questions']) || !is_array($data['questions'])) {
            throw new Exception('Format response dari AI tidak valid. Silakan coba lagi.');
        }

        $formatted = [];

        foreach ($data['questions'] as $question) {
            $formattedQuestion = [
                'title' => $question['title'] ?? 'Soal',
                'question' => $question['question'] ?? '',
                'correct_answer' => $question['correct_answer'] ?? '',
                'explanation' => $question['explanation'] ?? '',
            ];

            if ($questionType === 'pilihan_ganda' && isset($question['options'])) {
                $formattedQuestion['options'] = [
                    $question['options']['A'] ?? '',
                    $question['options']['B'] ?? '',
                    $question['options']['C'] ?? '',
                    $question['options']['D'] ?? '',
                ];
            }

            $formatted[] = $formattedQuestion;
        }

        return $formatted;
    }
}
