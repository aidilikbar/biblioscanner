<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\OpenAIService;
use App\Models\Scan;

class AIScanController extends Controller
{
    public function index()
    {
        return view('aiscan.index');
    }

    public function scan(Request $request, OpenAIService $openai)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120',
        ]);

        $uploadedFile = $request->file('file');
        $fileName = time() . '_' . Str::slug($uploadedFile->getClientOriginalName()) . '.pdf';
        $path = $uploadedFile->storeAs('aiscan_uploads', $fileName);

        $localPath = storage_path("app/{$path}");

        // Upload to OpenAI and get metadata
        $openaiFileId = $openai->uploadFile($localPath, $fileName);

        $excerpt = "Cracking the Code of Change by Michael Beer and Nitin Nohria...";

        $meta = $openai->extractMetadata($excerpt);
        $citation = static::extractCitation($meta['metadata'] ?? '');
        $summary = static::extractSummary($meta['metadata'] ?? '');
        $recs = $openai->getRecommendations($summary ?? $excerpt);
        $recommendationsRaw = $recs['recommendations'] ?? '';
        $recommendations = preg_split('/\r\n|\r|\n/', trim($recommendationsRaw));

        return view('aiscan.result', compact('fileName', 'citation', 'summary', 'recommendations'));
    }

    private static function extractCitation(string $text): ?string
    {
        if (preg_match('/(?<citation>.+?\(\d{4}\).+?\.)/', $text, $match)) {
            return $match['citation'];
        }

        return null;
    }

    private static function extractSummary(string $text): ?string
    {
        $lines = explode("\n", trim($text));
        $filtered = array_filter($lines, fn($line) => !str_contains($line, 'Citation'));
        return implode(" ", array_slice($filtered, 1));
    }
}