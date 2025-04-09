<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;
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
        $originalName = $uploadedFile->getClientOriginalName();

        try {
            // ✅ Get the real temporary path instead of storing the file
            $tempPath = $uploadedFile->getRealPath();

            // ✅ Parse the PDF directly from the temp file
            $parser = new Parser();
            $pdf = $parser->parseFile($tempPath);
            $excerpt = substr($pdf->getText(), 0, 2000);

            // ✅ Upload file to OpenAI (for embeddings / metadata)
            $openaiFileId = $openai->uploadFile($tempPath, $originalName);

            // ✅ Extract metadata (summary & citation)
            $meta = $openai->extractMetadata($excerpt);
            $citation = $this->extractCitation($meta['metadata'] ?? '');
            $summary = $this->extractSummary($meta['metadata'] ?? '');

            // ✅ Get recommended readings
            $recs = $openai->getRecommendations($summary ?? $excerpt);
            $recommendations = $recs['recommendations'] ?? [];

            return view('aiscan.result', [
                'fileName' => $originalName,
                'citation' => $citation,
                'summary' => $summary,
                'recommendations' => $recommendations,
            ]);
        } catch (\Throwable $e) {
            \Log::error('❌ AI Scan Error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while scanning the file.');
        }
    }

    private function extractCitation(string $text): ?string
    {
        if (preg_match('/(?<citation>.+?\(\d{4}\).+?\.)/', $text, $match)) {
            return $match['citation'];
        }

        return null;
    }

    private function extractSummary(string $text): ?string
    {
        $lines = explode("\n", trim($text));
        $filtered = array_filter($lines, fn($line) => !str_contains($line, 'Citation'));
        return implode(" ", array_slice($filtered, 1));
    }
}