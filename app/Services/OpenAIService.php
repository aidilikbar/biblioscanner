<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected string $apiKey;
    protected string $apiBase;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
        $this->apiBase = 'https://api.openai.com/v1';
    }

    public function uploadFile(string $filePath, string $fileName): ?string
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->attach('file', file_get_contents($filePath), $fileName)
                ->post($this->apiBase . '/files', [
                    'purpose' => 'assistants',
                ]);

            if ($response->successful()) {
                return $response->json('id');
            }

            Log::error('OpenAI file upload failed', ['response' => $response->json()]);
        } catch (\Throwable $e) {
            Log::error('OpenAI file upload error', ['exception' => $e->getMessage()]);
        }

        return null;
    }

    public function extractMetadata(string $text): array
    {
        $payload = [
            'model' => 'gpt-4-1106-preview',
            'temperature' => 0.5,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are an academic assistant. Based on the user-provided article text, extract the following:\n\n1. APA Citation (Title, Author(s), Year)\n2. Short summary of the document in 3-5 sentences."
                ],
                [
                    'role' => 'user',
                    'content' => "Here is the content of the PDF:\n\n" . $text
                ],
            ],
        ];

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post($this->apiBase . '/chat/completions', $payload);

            if ($response->successful()) {
                return [
                    'metadata' => $response->json('choices.0.message.content'),
                    'error' => null,
                ];
            }

            Log::error('OpenAI metadata extraction failed', ['response' => $response->json()]);
            return ['metadata' => null, 'error' => $response->json()];
        } catch (\Throwable $e) {
            Log::error('OpenAI metadata exception', ['exception' => $e->getMessage()]);
            return ['metadata' => null, 'error' => $e->getMessage()];
        }
    }

    public function getRecommendations(string $summary): array
    {
        $prompt = "The document I read was: \n\n" . $summary . "\n\nBased on this, suggest up to 2 academic journal articles and 2 books that cover similar themes. Format each suggestion with:\n\n- Title\n- Author(s), Year (APA format)\n- Description\n- Link (if available)\n\nGroup results under two headings: JOURNAL ARTICLES and BOOKS.";

        $payload = [
            'model' => 'gpt-4-1106-preview',
            'temperature' => 0.7,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are an expert in academic research and change management. Based on user input, suggest high-quality journal articles and books related to organizational change, especially around Theory E and Theory O. Limit your suggestions to 2 per category. Use clear formatting with these fields:\n\n- Title\n- Author(s), Year (APA format)\n- Description\n- Link (if available)\n\nUse the headings: JOURNAL ARTICLES and BOOKS."
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ];

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post($this->apiBase . '/chat/completions', $payload);

            if ($response->successful()) {
                return [
                    'recommendations' => $response->json('choices.0.message.content'),
                    'error' => null,
                ];
            }

            Log::error('OpenAI recommendation generation failed', ['response' => $response->json()]);
            return ['recommendations' => null, 'error' => $response->json()];
        } catch (\Throwable $e) {
            Log::error('OpenAI recommendation exception', ['exception' => $e->getMessage()]);
            return ['recommendations' => null, 'error' => $e->getMessage()];
        }
    }
}