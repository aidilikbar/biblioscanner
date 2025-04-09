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

        $tempPath = $request->file('file')->getRealPath();

        // Extract text using PDF parser
        $parser = new Parser();
        $pdf = $parser->parseFile($tempPath);
        $excerpt = substr($pdf->getText(), 0, 2000); // Optional: limit for prompt size

        // Continue with OpenAI logic...
        $metadata = $openai->analyze($excerpt);

        return view('aiscan.result', [
            'fileName' => $request->file('file')->getClientOriginalName(),
            'citation' => $metadata['citation'] ?? null,
            'summary' => $metadata['summary'] ?? null,
            'recommendations' => $metadata['recommendations'] ?? [],
        ]);
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