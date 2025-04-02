<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Scan;
use App\Services\OpenAIService;

class ScanForm extends Component
{
    use WithFileUploads;
    public $file;
    public $scan;

    public function render()
    {
        return view('livewire.scan-form');
    }

    public function upload(OpenAIService $openai)
    {
        $this->validate([
            'file' => 'required|mimes:pdf|max:5120',
        ]);

        // 1. Upload to DigitalOcean Spaces
        $fileName = time() . '_' . Str::slug($this->file->getClientOriginalName()) . '.pdf';
        $filePath = $this->file->storeAs('scans', $fileName, 'spaces');
        $fileUrl = Storage::disk('spaces')->url($filePath);

        // 2. Download from Spaces and store locally (for OpenAI)
        $fileContent = Storage::disk('spaces')->get($filePath);
        $tmpPath = storage_path("app/tmp/{$fileName}");
        Storage::disk('local')->put("tmp/{$fileName}", $fileContent);

        // 3. Upload to OpenAI from local temp file
        $openaiFileId = $openai->uploadFile($tmpPath, $fileName);

        // 4. (Optional) Remove local temp file
        Storage::disk('local')->delete("tmp/{$fileName}");

        // 5. Dummy text for now — replace with actual PDF parsing later
        $excerpt = "Cracking the Code of Change by Michael Beer and Nitin Nohria...";

        // 6. Get citation + summary
        $meta = $openai->extractMetadata($excerpt);
        $citation = $this->extractCitation($meta['metadata'] ?? '');
        $summary = $this->extractSummary($meta['metadata'] ?? '');

        // 7. Get recommendations
        $recs = $openai->getRecommendations($summary ?? $excerpt);
        $recommendations = $recs['recommendations'] ?? null;

        // 8. Save to database
        $this->scan = Scan::create([
            'user_id' => auth()->id(),
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_url' => $fileUrl,
            'openai_file_id' => $openaiFileId,
            'citation' => $citation,
            'summary' => $summary,
            'recommendations' => $recommendations,
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