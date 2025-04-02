<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scan;
use Illuminate\Support\Facades\Storage;
use App\Services\OpenAIService;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    public function index()
    {
        // Show the form with the latest scan result (optional)
        $scan = Scan::latest()->first();
        return view('scan', compact('scan'));
    }

    public function store(Request $request, OpenAIService $openai)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120', // Max 5MB
        ]);

        // Store uploaded file locally
        $uploadedFile = $request->file('file');
        $fileName = time() . '_' . Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
        $filePath = $uploadedFile->storeAs('scans', $fileName, 'spaces');
        $fileUrl = Storage::disk('spaces')->url($filePath);

        // Upload to OpenAI
        $openaiFileId = $openai->uploadFile(storage_path("app/public/{$filePath}"), $fileName);

        // Parse PDF (optional - simplified: just use dummy excerpt)
        $text = "Cracking the Code of Change by Michael Beer and Nitin Nohria (2000)..."; // Replace with actual PDF parsing if needed

        // Get metadata
        $meta = $openai->extractMetadata($text);
        $citation = null;
        $summary = null;

        if (isset($meta['metadata'])) {
            $citation = $this->extractCitation($meta['metadata']);
            $summary = $this->extractSummary($meta['metadata']);
        }

        // Get recommendations
        $recs = $openai->getRecommendations($summary ?? $text);
        $recommendations = $recs['recommendations'] ?? null;

        // Save scan to database
        $scan = Scan::create([
            'user_id' => auth()->id(),
            'file_name' => $fileName,
            'file_path' => $filePath,
            'openai_file_id' => $openaiFileId,
            'citation' => $citation,
            'summary' => $summary,
            'recommendations' => $recommendations,
        ]);

        return redirect()->route('scan.index');
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