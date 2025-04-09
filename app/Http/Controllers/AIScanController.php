<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\OpenAIService;

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

        $file = $request->file('file');
        $fileName = time() . '_' . Str::slug($file->getClientOriginalName()) . '.pdf';
        $path = $file->storeAs('uploads', $fileName, 'local');
        $localPath = storage_path('app/' . $path);

        try {
            // Upload to OpenAI
            $openaiFileId = $openai->uploadFile($localPath, $fileName);

            // Dummy excerpt (you can later replace with PDF parser)
            $excerpt = "Cracking the Code of Change by Michael Beer and Nitin Nohria...";

            // Metadata
            $meta = $openai->extractMetadata($excerpt);
            $citation = $this->extractCitation($meta['metadata'] ?? '');
            $summary = $this->extractSummary($meta['metadata'] ?? '');

            // Recommendations
            $recs = $openai->getRecommendations($summary ?? $excerpt);
            $recommendations = $recs['recommendations'] ?? [];

            return view('aiscan.result', [
                'fileName' => $fileName,
                'citation' => $citation ?? 'Not found',
                'summary' => $summary ?? 'Not found',
                'recommendations' => is_array($recommendations) ? $recommendations : [],
            ]);

        } catch (\Throwable $e) {
            logger('❌ AI Scan Error', ['message' => $e->getMessage()]);
            return back()->withErrors(['file' => 'Error during AI scan.']);
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