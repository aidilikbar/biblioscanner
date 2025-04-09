<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\OpenAIService;
use App\Models\Scan;
use Smalot\PdfParser\Parser;

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

        // Extract actual text from the uploaded PDF
        $parser = new Parser();
        $pdf = $parser->parseFile($localPath);
        $excerpt = substr($pdf->getText(), 0, 2000); // Limit to first 2000 characters to keep it concise

        // Upload to OpenAI and get metadata
        $openaiFileId = $openai->uploadFile($localPath, $fileName);
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
        return implode(" ", array_slice($filtered, 0, 5)); // Limit summary to 5 lines
    }
}