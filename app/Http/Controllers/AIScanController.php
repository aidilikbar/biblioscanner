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

        try {
            // ✅ Use temp file path (safe for PaaS like App Platform)
            $tempPath = $uploadedFile->getRealPath();

            $parser = new Parser();
            $pdf = $parser->parseFile($tempPath);
            $excerpt = substr($pdf->getText(), 0, 2000);

            $openaiFileId = $openai->uploadFile($tempPath, $uploadedFile->getClientOriginalName());
            $meta = $openai->extractMetadata($excerpt);
            $citation = $this->extractCitation($meta['metadata'] ?? '');
            $summary = $this->extractSummary($meta['metadata'] ?? '');
            $recommendations = $openai->getRecommendations($summary)['recommendations'] ?? [];

            return view('aiscan.result', [
                'fileName' => $uploadedFile->getClientOriginalName(),
                'citation' => $citation,
                'summary' => $summary,
                'recommendations' => $recommendations
            ]);

        } catch (\Throwable $e) {
            \Log::error('❌ AI Scan Error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while scanning the file.');
        }
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